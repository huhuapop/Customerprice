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

    /**
     * @param \Magento\Framework\App\Request\Http       $request
     * @param \Psr\Log\LoggerInterface                  $logger
     * @param \Magedelight\Customerprice\Helper\Data             $helper
     * @param \Magento\Checkout\Model\Session           $session
     * @param \Magento\Catalog\Model\Product\Type\Price $priceModel
     * @param \Magento\Customer\Model\Session           $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Psr\Log\LoggerInterface $logger,
        \Magedelight\Customerprice\Helper\Data $helper,
        \Magento\Checkout\Model\Session $session,
        \Magento\Catalog\Model\Product\Type\Price $priceModel,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->session = $session;
        $this->_priceModel = $priceModel;
        $this->_customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return bool
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //$product = $observer->getEvent()->getProduct();
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($this->helper->isEnabled()) {
            $options = $this->request->getPostValue();
            //exit();
            if (isset($options['product'])) {
                $discountValue = $options['product'];

                $customer_id = $options['customer']['customer_id'];

                $discount = $this->getDiscountByCustomerId($customer_id);
                if (!is_null($discount) && $discount->getId()) {
                    if (is_numeric($discountValue['discount'])) {
                        $discount->setValue($discountValue['discount'])->setId($discount->getId())->save();
                    } else {
                        $discount->setId($discount->getId())->delete();
                    }
                } else {
                    if (is_numeric($discountValue['discount'])) {
                        $discountModel = $this->_objectManager->create('Magedelight\Customerprice\Model\Discount')
                                ->setData(['customer_id' => $customer_id, 'value' => $discountValue['discount']]);
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
                    foreach ($_options as $k => $value) {
                        if ($key == 'value') {
                            /* ---Delete--- */
                            if ($value['del'] == 1 && is_int($k)) {
                                $priceCustomerDel = $this->_objectManager->create('Magedelight\Customerprice\Model\Customerprice')
                                        ->load($k)
                                        ->delete();

                                unset($_options[$k]);
                                continue;
                            }

                            /* ---Insert---- */
                            $priceCustomer = $this->_objectManager->create('Magedelight\Customerprice\Model\Customerprice');

                            if (is_int($k)) {
                                $priceCustomer->setId($k);
                            }
                            //die($value['cid']);
                            $newPrice = $value['newprice'];

                            $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load(trim($value['pid']));

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
                                    ->setQty($value['qty']);

                            if ($value['del'] == 1 && !is_int($k)) {
                                unset($_options[$k]);
                            } else {
                                try {
                                    $priceCustomer->save();
                                } catch (LocalizedException $e) {
                                    $messageManager = $this->_objectManager->create("Magento\Framework\Message\ManagerInterface");
                                    $messageManager->addErrorMessage("We found a duplicate product and quantity.");
                                }
                            }
                        }
                    }
                }
            }

            if (isset($options['categoryoption'])) {
                //echo "<pre>"; print_r($options['categoryoption']); exit();
                foreach ($options['categoryoption'] as $key => $_options) {
                    foreach ($_options as $k => $value) {
                        if ($key == 'value') {
                            $priceCustomerCategory = $this->_objectManager->create('Magedelight\Customerprice\Model\Categoryprice');
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

            //    use Magento\Framework\App\Bootstrap;
//        require __DIR__ . '/app/bootstrap.php';
//
//                        $params = $_SERVER;
//                        $bootstrap = Bootstrap::create(BP, $params);
//                        $objectManager = $bootstrap->getObjectManager();
//                        $_cacheTypeList = $objectManager->create('Magento\Framework\App\Cache\TypeListInterface');
//                        $_cacheFrontendPool = $objectManager->create('Magento\Framework\App\Cache\Frontend\Pool');
//                        $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
//                        foreach ($types as $type) {
//                            $_cacheTypeList->cleanType($type);
//                        }
//                        foreach ($_cacheFrontendPool as $cacheFrontend) {
//                            $cacheFrontend->getBackend()->clean();
//                        }
//        print_r(system('php bin/magento setup:clean'));
//        print_r(system('php bin/magento setup:flush'));

	  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	  $cacheManager = $objectManager->create('Magento\Framework\App\Cache\Manager');
	  $cacheManager->flush($cacheManager->getAvailableTypes());

//            $command = 'cd /var/www/html/magento && php bin/magento cache:clean && php bin/magento cache:flush';
//            echo '<pre>' . shell_exec($command) . '</pre>';
//        $append = 'Succesfully Recache';
//        $result = ['error' => true, 'message' => $append];
//        $resultJson = $this->resultJsonFactory->create();
//        $resultJson->setData($result);
//
//        return $resultJson;
//            throw new LocalizedException(__('test'));

        }
    }

    public function getDiscountByCustomerId($customerId)
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $discount = $this->_objectManager->create('Magedelight\Customerprice\Model\Discount')->getCollection()
                ->addFieldToFilter('customer_id', ['eq' => $customerId])
                ->getFirstItem();
        if ($discount->getId()) {
            return $discount;
        } else {
            return;
        }
    }
}
