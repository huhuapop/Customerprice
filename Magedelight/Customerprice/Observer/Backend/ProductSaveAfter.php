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
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->session = $session;
        $this->_priceModel = $priceModel;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return bool
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($this->helper->isEnabled()) {
            $options = $this->request->getPostValue();

            if (isset($options['option'])) {
                foreach ($options['option'] as $key => $_options) {
                    foreach ($_options as $k => $value) {
                        if ($key == 'value') {
                            /* ---Delete--- */
                            if ($value['del'] == 1 && is_int($k)) {
                                $priceCustomerDel = $this->_objectManager->create('Magedelight\Customerprice\Model\Customerprice')
                                        ->load($k)
                                        ->delete();
                            }

                            /* ---Insert---- */
                            $priceCustomer = $this->_objectManager->create('Magedelight\Customerprice\Model\Customerprice');

                            if (is_int($k)) {
                                $priceCustomer->setId($k);
                            }
                            //die($value['cid']);
                            $newPrice = $value['newprice'];

                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $product = $objectManager->get('Magento\Catalog\Model\Product')->load(trim($value['pid']));

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
                                    ->setQty($value['qty']);

                            if ($value['del'] == 1 && !is_int($k)) {
                                unset($_options[$k]);
                            } else {
                                $priceCustomer->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
