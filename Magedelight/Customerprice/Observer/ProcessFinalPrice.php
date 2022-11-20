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
use Magedelight\Customerprice\Model\Calculation\Calculator\CalculatorInterface;

class ProcessFinalPrice implements ObserverInterface
{
    protected $logger;
    
    protected $helper;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magedelight\Customerprice\Helper\Data $helper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magedelight\Customerprice\Helper\Data $helper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CalculatorInterface $catalogPriceCalculator
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->catalogPriceCalculator = $catalogPriceCalculator;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return \Magedelight\Customerprice\Observer\ProcessFinalPrice
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $ppcFinalPrice = null;
        if ($this->helper->isCustomerPriceAllow()) {
            $oldFinalPrice = $product->getData('final_price');
            foreach ($product->getPriceInfo()->getPrices() as $price) {
                if ($price->getPriceCode() == 'ppc_rule_price') {
                    $ppcFinalPrice = $price->getValue();
                }
            }
            if ($ppcFinalPrice) {
                $newFinalPrice = min(
                    $oldFinalPrice,
                    $this->convertCurrentToBase($ppcFinalPrice)
                );
                if ($newFinalPrice !== $oldFinalPrice) {
                    $product->setPpcPrice(1);
                }
                $product->setData('final_price', $newFinalPrice);
            } else {
                if ($this->helper->isAdvanced()) {
                    $discount = $this->catalogPriceCalculator->calculate($oldFinalPrice, $product);
                    if ($discount) {
                        $product->setData('final_price', $discount);
                    }
                }
            }
        }
    }
    
    private function convertCurrentToBase($amount = 0, $store = null, $currency = null)
    {
        if ($store == null) {
            $store = $this->storeManager->getStore()->getStoreId();
        }
        $rate = $this->priceCurrency->convert($amount, $store, $currency);
        $rate = $this->priceCurrency->convert($amount, $store) / $amount;
        $amount = $amount / $rate;
        return $this->priceCurrency->round($amount);
    }
}
