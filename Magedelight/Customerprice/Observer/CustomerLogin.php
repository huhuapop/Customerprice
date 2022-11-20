<?php

namespace Magedelight\Customerprice\Observer;

use \Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\SessionFactory;
use Magedelight\Customerprice\Model\Customerprice;

/**
 * Class CustomerLogin
 * @package Magedelight\Customerprice\Observer
 *
 */
class CustomerLogin implements ObserverInterface
{

    /**
     * @var customerSession
     */
    protected $customerSession;

    /**
     * @var customerPrice
     */
    protected $customerPrice;


    public function __construct (
        SessionFactory $customerSession,
        Customerprice $customerPrice
    ) {
        $this->customerSession = $customerSession;
        $this->customerPrice = $customerPrice;
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
    	$customerId = $this->customerSession->create()->getCustomer()->getId();
    	if ($customerId) {
    		$item = $observer->getEvent()->getItem();
			if (!is_null($item->getId())) {
				$productId = $item->getProduct()->getId();

				if ($item->getProductType() == 'configurable') {
					foreach ($item->getProduct()->getTypeInstance()->getUsedProducts($item->getProduct()) as $child) {
						if ($child->getSku() == $item->getSku()) {
							$productId = $child->getId();
							$qty = $item->getQty();
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