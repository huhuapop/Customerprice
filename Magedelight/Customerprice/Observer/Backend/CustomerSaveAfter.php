<?php

/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@Magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Customerprice
 * @copyright Copyright (c) 2017 Mage Delight (http://www.Magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@Magedelight.com>
 */

namespace Magedelight\Customerprice\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class CustomerSaveAfter implements ObserverInterface
{

    protected $logger;
    protected $customerSession;
    protected $helper;
    protected $request;
    protected $_priceModel;
    protected $_customerFactory;
    protected $categoryPriceFactory;

    /**
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magedelight\Customerprice\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Catalog\Model\Product\Type\Price $priceModel
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magedelight\Customerprice\Model\Discount $discount
     * @param \Magedelight\Customerprice\Model\Customerprice $customerprice
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magedelight\Customerprice\Model\Categoryprice $categoryprice
     */
    
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Psr\Log\LoggerInterface $logger,
        \Magedelight\Customerprice\Helper\Data $helper,
        \Magento\Checkout\Model\Session $session,
        \Magento\Catalog\Model\Product\Type\Price $priceModel,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magedelight\Customerprice\Model\DiscountFactory $discount,
        \Magedelight\Customerprice\Model\CustomerpriceFactory $customerprice,
        \Magento\Catalog\Model\ProductFactory $productModel,
        \Magedelight\Customerprice\Model\CategorypriceFactory $categoryprice,
        \Magedelight\Customerprice\Model\ResourceModel\Categoryprice\CollectionFactory $categoryPriceFactory
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->session = $session;
        $this->_priceModel = $priceModel;
        $this->_customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->discount = $discount;
        $this->customerprice = $customerprice;
        $this->productModel = $productModel;
        $this->categoryprice = $categoryprice;
        $this->categoryPriceFactory = $categoryPriceFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return bool
     * @codingStandardsIgnoreStart
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($this->helper->isEnabled()) {
            $options = $this->request->getPostValue();
            if (isset($options['product'])) {
                $discountValue = $options['product'];

                $customer_id = $options['customer']['customer_id'];

                $discount = $this->getDiscountByCustomerId($customer_id);
                if (!($discount === null) && $discount->getId()) {
                    if (is_numeric($discountValue['discount'])) {
                        $discount->setValue($discountValue['discount'])->setId($discount->getId())->save();
                    } else {
                        $discount->setId($discount->getId())->delete();
                    }
                } else {
                    if (is_numeric($discountValue['discount'])) {
                        //$discountModel = $this->_objectManager->create('Magedelight\Customerprice\Model\Discount')
                        $discountModel = $this->discount->create();
                        $discountModel->setData(['customer_id' => $customer_id, 'value' => $discountValue['discount']]);
                        try {
                            $discountModel->save();
                        } catch (\Exception $e) {
                            $this->messageManager->addError($e->getMessage());
                        }
                    }
                }
            }

            if (isset($options['option'])) {
                foreach ($options['option'] as $key => $_options) {
                    $checkArray = array();
                    foreach($_options as $optionKey => $optionValue) {
                        $checkArray[] = array(
                            'customer' => $optionValue['cid'],
                            'website' => $optionValue['website'],
                            'quantity' => $optionValue['qty'],
                            'product' => $optionValue['pid'],
                        );
                    }
                    //var_dump($checkArray);
                    $testArray = array_map("unserialize", array_unique(array_map("serialize", $checkArray)));
                    if(count($testArray) != count($checkArray)) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('Duplicate Entry Found In Customer Price'));
                    }
                    foreach ($_options as $k => $value) {
                        if ($key == 'value') {
                            /* ---Delete--- */
                            if ($value['del'] == 1 && is_int($k)) {
                                $priceCustomerDel = $this->customerprice->create()
                                        ->load($k)
                                        ->delete();

                                unset($_options[$k]);
                                continue;
                            }

                            /* ---Insert---- */
                            $priceCustomer = $this->customerprice->create();

                            if (is_int($k)) {
                                $priceCustomer->setId($k);
                            }
                            $newPrice = $value['newprice'];

                            $product = $this->productModel->create()->load(trim($value['pid']));

                            $customer = $this->_customerFactory->create()->load($value['cid']);

                            preg_match('/(.*)%/', $newPrice, $matches);
                            if (is_array($matches) && count($matches) > 0) {
                                $newPrice = $product->getPrice() - ($product->getPrice() * ($matches[1] / 100));
                            }
                            $priceCustomer->setCustomerId($customer->getId())
                                    ->setCustomerName(trim($customer->getName()))
                                    ->setCustomerEmail(trim($customer->getEmail()))
                                    ->setProductId($value['pid'])
                                    ->setProductName(trim($value['pname']))
                                    ->setNewPrice($newPrice)
                                    ->setLogPrice($value['newprice'])
                                    ->setPrice($product->getPrice())
                                    ->setQty($value['qty'])
                                    ->setWebsiteId($value['website']);
                                    
                            if ($value['del'] == 1 && !is_int($k)) {
                                unset($_options[$k]);
                            } else {
                                try {
                                    $priceCustomer->save();
                                } catch (LocalizedException $e) {
                                    $messageManager = $this->_objectManager->
                                    create(\Magento\Framework\Message\ManagerInterface::class);
                                    $messageManager->addErrorMessage("We found a duplicate product and quantity.");
                                }
                            }
                        }
                    }
                }
            }

            if (isset($options['categoryoption'])) {
                foreach ($options['categoryoption'] as $key => $_options) {
                    
                    $customerId = $this->request->getPostValue('customer_id');
                    
                    $priceCustomerCategory = $this->categoryPriceFactory->create()
                            ->addFieldToSelect('*')
                            ->addFieldToFilter('customer_id', $customerId);

//                    if(!empty($priceCustomerCategory->getData()))
//                    {
//                        array_splice($_options, 0, 1);
//                    }
                    foreach ($_options as $k => $value) {
                       
                        if ($key == 'value') {
                            $priceCustomerCategory = $this->categoryprice->create();
                            $customer = $this->_customerFactory->create()->load($value['cid']);
                            if (is_int($k)) {
                                $priceCustomerCategory->setCategorypriceId($k);
                            }
                            $priceCustomerCategory->setCustomerId($customer->getId())
                                    ->setCustomerName(trim($customer->getName()))
                                    ->setCustomerEmail(trim($customer->getEmail()))
                                    ->setCategoryId($value['pid'])
                                    ->setCategoryName(trim($value['pname']))
                                    ->setDiscount($value['discount']);
                            $priceCustomerCategory->save();
                        }
                    } 
                }
            }

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $cacheManager = $objectManager->create('Magento\Framework\App\Cache\Manager');
            $cacheManager->flush($cacheManager->getAvailableTypes());
        }
    }

    public function getDiscountByCustomerId($customerId)
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $discount = $this->discount->create()->getCollection()
                ->addFieldToFilter('customer_id', ['eq' => $customerId])
                ->getFirstItem();
        if ($discount->getId()) {
            return $discount;
        } else {
            return;
        }
    }
}
