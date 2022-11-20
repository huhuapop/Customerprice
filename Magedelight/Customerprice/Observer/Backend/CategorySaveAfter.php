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

class CategorySaveAfter implements ObserverInterface
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
        \Magento\Customer\Model\Session $customerSession,
        \Magedelight\Customerprice\Model\CategorypriceFactory $categoryprice
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->session = $session;
        $this->_customerFactory = $customerFactory;
        $this->_priceModel = $priceModel;
        $this->customerSession = $customerSession;
        $this->categoryprice = $categoryprice;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return bool
     * @codingStandardsIgnoreStart
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getCategory();
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($this->helper->isEnabled()) {
            $options = $this->request->getPostValue();
            if (isset($options['customeroption'])) {
                //echo "<pre>"; print_r($options['categoryoption']); exit();
                foreach ($options['customeroption'] as $key => $_options) {
                    foreach ($_options as $k => $value) {
                        if ($key == 'value') {
                            //$priceCustomerCategory = $this->_objectManager->create('Magedelight\Customerprice\Model\Categoryprice');
                            $priceCustomerCategory = $this->categoryprice->create();
                            $customer = $this->_customerFactory->create()->load($value['pid']);
                            if (is_int($k)) {
                                $priceCustomerCategory->setCategorypriceId($k);
                            }
                            $priceCustomerCategory->setCustomerId($value['pid'])
                                    ->setCustomerName(trim($customer->getName()))
                                    ->setCustomerEmail(trim($customer->getEmail()))
                                    ->setCategoryId($options['category_id'])
                                    ->setCategoryName(trim($options['name']))
                                    ->setDiscount($value['discount']);
                            $priceCustomerCategory->save();
                        }
                    }
                }
            }
        } else {
            $options = $this->request->getPostValue();
            if (isset($options['customeroption'])) {
                unset($options['customeroption']);
                throw new LocalizedException(__('Magedelight Price Per Customer extension is disabled'));
            }
        }
    }
}
