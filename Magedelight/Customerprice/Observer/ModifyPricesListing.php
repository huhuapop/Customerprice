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

namespace Magedelight\Customerprice\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\AdminOrder\Create as OrderCreate;

class ModifyPricesListing implements ObserverInterface
{

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magedelight\Customerprice\Helper\Data $helper,
        \Magedelight\Customerprice\Model\Customerprice $customerPrice,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        OrderCreate $orderCreate,
        \Magento\Framework\App\State $state
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->customerPrice = $customerPrice;
        $this->customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->orderCreate = $orderCreate;
        $this->state = $state;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $Allproducts = $observer->getCollection();
        foreach ($Allproducts as $product) {
            if ($this->helper->isCustomerPriceAllow()) {
                if ($product->getTypeId() == 'configurable') {
                    foreach ($product->getTypeInstance()->getUsedProducts($product) as $child) {
                        $this->setNewTierPrice($child);
                    }
                } else {
                    $this->setNewTierPrice($product);
                }
            }
        }
    }
    
    private function getCurrentWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    private function setNewTierPrice($product)
    {
        $res = [];
        $newArray = [];
        if ($this->state->getAreaCode() == 'adminhtml') {
            $customerId = $this->orderCreate->getQuote()->getCustomer()->getId();
        } else {
            $customerId = $this->customerSession->create()->getCustomer()->getId();
        }
        if ($customerId) {
            $tierPriceCollection = $this->customerPrice->getCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('product_id', ['eq' => $product->getId()])
                    ->addFieldToFilter('customer_id', ['eq' => $customerId]);
            if ($tierPriceCollection->getSize()) {
                $newTierPrice = $tierPriceCollection->getData();
                if ($this->state->getAreaCode() == 'adminhtml') {
                    $groupId = $this->orderCreate->getQuote()->getCustomer()->getGroupId();
                } else {
                    $groupId = $this->customerSession->create()->getCustomerGroupId();
                }
                $websiteId = $this->getCurrentWebsiteId();
                foreach ($newTierPrice as $price) {
                    $res['website_id'] = $websiteId;
                    $res['all_groups'] = 0;
                    $res['cust_group'] = $groupId;
                    $res['price'] = (float)$price['new_price'];
                    $res['price_qty'] = (float)$price['qty'] * 1;
                    $res['website_price'] = (float)$price['new_price'];
                    $res['value'] = (float)$price['new_price'];
                    $newArray[] = $res;
                }
                if (!$product->getData('is_tier_price_changed')) {
                    $newTier = array_merge($newArray, $product->getTierPrice());
                    $price = array_column($newTier, 'price');
                    $priceQty = array_column($newTier, 'price_qty');
                    array_multisort($price, SORT_ASC, $newTier);
                    //array_multisort($priceQty, SORT_ASC, $newTier);

                    $finalTierPrice = $this->uniqueArray($newTier, 'price_qty');
                    if (!empty($finalTierPrice)) {
                        $product->setdata('is_tier_price_changed', 1);
                    }
                    $product->setData('tier_price', $finalTierPrice);
                }
            }
        }
    }

    private function uniqueArray($array, $key)
    {
        $temp_array = [];
        $i = 0;
        $key_array = [];

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }
}
