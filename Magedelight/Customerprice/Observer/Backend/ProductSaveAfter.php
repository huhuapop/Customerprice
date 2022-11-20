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

class ProductSaveAfter implements ObserverInterface
{
    protected $logger;
    protected $customerSession;
    protected $helper;
    protected $request;
    protected $_priceModel;
    protected $storeManager;

    /**
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magedelight\Customerprice\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Catalog\Model\Product\Type\Price $priceModel
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magedelight\Customerprice\Model\Customerprice $customerprice
     * @param \Magento\Catalog\Model\Product $product
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Psr\Log\LoggerInterface $logger,
        \Magedelight\Customerprice\Helper\Data $helper,
        \Magento\Checkout\Model\Session $session,
        \Magento\Catalog\Model\Product\Type\Price $priceModel,
        \Magento\Customer\Model\Session $customerSession,
        \Magedelight\Customerprice\Model\CustomerpriceFactory $customerprice,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->session = $session;
        $this->_priceModel = $priceModel;
        $this->customerSession = $customerSession;
        $this->customerprice = $customerprice;
        $this->product = $product;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     * @codingStandardsIgnoreStart
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($this->helper->isEnabled()) {
            $options = $this->request->getPostValue();

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
                            }

                            /* ---Insert---- */
                            $priceCustomer = $this->customerprice->create();

                            if (is_int($k)) {
                                $priceCustomer->setId($k);
                            }
                            $newPrice = $value['newprice'];

                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            //$product= $objectManager->get('Magento\Catalog\Model\Product')->load(trim($value['pid']));
                            $product = $this->product->create()->load(trim($value['pid']));
                            preg_match('/(.*)%/', $newPrice, $matches);

                            if (is_array($matches) && count($matches) > 0) {
                                $newPrice = $product->getPrice() - ($product->getPrice() * ($matches[1] / 100));
                            }

                            $priceCustomer->setCustomerId(trim($value['cid']))
                                    ->setCustomerName(trim($value['cname']))
                                    ->setCustomerEmail(trim($value['email']))
                                    ->setProductId($product->getId())
                                    ->setProductName($product->getName())
                                    ->setNewPrice($newPrice)
                                    ->setLogPrice($value['newprice'])
                                    ->setPrice($product->getPrice())
                                    ->setQty($value['qty'])
                                    ->setWebsiteId($value['website']);

                            if ($value['del'] == 1 && !is_int($k)) {
                                unset($_options[$k]);
                            } else {
                                $priceCustomer->save();
                            }
                        }
                    }
                }
            }else{
                $priceCustomer = $this->customerprice->create()->getCollection()->addFieldToFilter('product_id', $options['product']['current_product_id']);
                if ($priceCustomer->getSize()) {
                    $price = $options['product']['price'];
                    foreach ($priceCustomer as $customerdata) {
                        preg_match('/(.*)%/', $customerdata['log_price'], $matches);
                        if (is_array($matches) && count($matches) > 0) {
                            $newPrice =  $price - ($price * ($matches[1] / 100));
                            $customerdata['new_price'] = $newPrice;
                        }
                        $customerdata['price'] = $price;
                        $customerdata->save();
                    }
                }
            }
        }
    }
}
