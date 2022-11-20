<?php

namespace Magedelight\Customerprice\Plugin;

use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Sales\Model\AdminOrder\Create  as OrderCreate;

/**
 * Class BeforeFinalPrice
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ChangeTierPriceInfo
{

    /**
     * ChangeTierPrice constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magedelight\Customerprice\Helper\Data $helper
     * @param \Magedelight\Customerprice\Model\Customerprice $customerPrice
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magedelight\Customerprice\Helper\Data $helper,
        \Magedelight\Customerprice\Model\Customerprice $customerPrice,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        OrderCreate $orderCreate,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->customerPrice = $customerPrice;
        $this->customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->orderCreate = $orderCreate;
        $this->state = $state;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * @param $subject
     * @param null $qty
     */
    public function beforeGetValue($subject)
    {
        $product = $subject->getProduct();
        if ($this->helper->isCustomerPriceAllow()) {
            $types = array('collections');
            foreach ($types as $type) {
                $this->_cacheTypeList->cleanType($type);
            }
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            if ($product->getTypeId() == 'configurable') {
                foreach ($product->getTypeInstance()->getUsedProducts($product) as $child) {
                    $this->setNewTierPrice($child);
                }
            } else {
                $this->setNewTierPrice($product);
            }
        }
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCurrentWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * @param $product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
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
            $websiteId = $this->getCurrentWebsiteId();
            // Tier Price Collection For Customer Price
            $tierPriceCollection = $this->customerPrice->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('product_id', ['eq' => $product->getId()])
                ->addFieldToFilter('customer_id', ['eq' => $customerId])
                ->addFieldToFilter('website_id', ['eq' => $websiteId]);

            $logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
            $logger->info(print_r($tierPriceCollection->getData(),1));

            // Create an array of customer tier price
            if ($tierPriceCollection->getSize()) {
                $newTierPrice = $tierPriceCollection->getData();
                if ($this->state->getAreaCode() == 'adminhtml') {
                    $groupId = $this->orderCreate->getQuote()->getCustomer()->getGroupId();
                } else {
                    $groupId = $this->customerSession->create()->getCustomerGroupId();
                }
                //$websiteId = $this->getCurrentWebsiteId();
                foreach ($newTierPrice as $price) {
                    //$res['website_id'] = $websiteId;
                    $res['website_id'] = $price['website_id'];
                    $res['all_groups'] = 0;
                    $res['cust_group'] = $groupId;
                    $res['price'] = (float)$price['new_price'];
                    $res['price_qty'] = (float)$price['qty'] * 1;
                    $res['website_price'] = (float)$price['new_price'];
                    $res['value'] = (float)$price['new_price'];
                    $newArray[] = $res;
                }

                // Merge Group Price Or Customer Price
                $newTier = array_merge($newArray, $product->getTierPrice());
                $price = array_column($newTier, 'price');
                $priceQty = array_column($newTier, 'price_qty');
                array_multisort($price, SORT_ASC, $newTier);
                //array_multisort($priceQty, SORT_ASC, $newTier);

                // Create unique array for group and customer price
                $finalTierPrice = $this->uniqueArray($newTier, 'price_qty');
                // Set New Tier Price
                $product->setData('tier_price', $finalTierPrice);
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
