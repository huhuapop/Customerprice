<?php

namespace Magedelight\Customerprice\Observer;

use \Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magedelight\Customerprice\Model\Customerprice;

/**
 * Class SalesQuoteMergeAfter
 * @package Magedelight\Customerprice\Observer
 *
 */
class SalesQuoteMergeAfter implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    /**
     * @var customerPrice
     */
    protected $customerPrice;

    public function __construct (
        CheckoutSession $checkoutSession,
        Customerprice $customerPrice
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->customerPrice = $customerPrice;
    }

    public function execute(\Magento\Framework\Event\Observer $observer){

        $data = $observer->getEvent()->getData('info');

        $convert_data = (array)$data;

        foreach ($convert_data as $itemsdata=>$datainfo) {
            foreach ($datainfo as $itemId => $itemInfo) {
                $item = $this->_checkoutSession->getQuote()->getItemById($itemId);
                $customerId = $this->_checkoutSession->getQuote()->getCustomer()->getId();
                $productId = $item->getProduct()->getId();
                if ($item->getProduct()->getTypeId() == 'configurable') {
                    foreach ($item->getProduct()->getTypeInstance()->getUsedProducts($item->getProduct()) as $child) {
                        if ($child->getSku() == $item->getSku()) {
                            $productId = $child->getId();
                            $qty = $itemInfo['qty'];
                            $priceCollection = $this->customerPrice->getCollection()->addFieldToSelect('new_price')->addFieldToFilter('product_id', ['eq' => $productId])->addFieldToFilter('customer_id', ['eq' => $customerId])->addFieldToFilter('qty', ['eq' => $qty]);

                            if ($priceCollection->getSize()) {
                                $newTierPrice = $priceCollection->fetchItem()->getData();
                                $item->setCustomPrice($newTierPrice['new_price']);
                                $item->setOriginalCustomPrice($newTierPrice['new_price']);
                                $item->getProduct()->setIsSuperMode(true);
                            }else{
                                $priceCollection = $this->customerPrice->getCollection()->addFieldToSelect('new_price')->addFieldToFilter('product_id', ['eq' => $productId])->addFieldToFilter('customer_id', ['eq' => $customerId])->addFieldToFilter('qty', ['lt' => $qty]);
                                if ($priceCollection->getSize()) {
                                    $newTierPrice = $priceCollection->getLastItem()->getData();
                                    $item->setCustomPrice($newTierPrice['new_price']);
                                    $item->setOriginalCustomPrice($newTierPrice['new_price']);
                                    $item->getProduct()->setIsSuperMode(true);
                                }else{
                                    $item->setCustomPrice($item->getProduct()->getFinalPrice());
                                    $item->setOriginalCustomPrice($item->getProduct()->getFinalPrice());
                                    $item->getProduct()->setIsSuperMode(true);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}