<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magedelight\Customerprice\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Adjustment\Calculator;
use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Framework\Pricing\Price\BasePriceProviderInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManager;
use Magedelight\Customerprice\Model\Calculation\Calculator\CalculatorInterface;
use Magedelight\Customerprice\Helper\Data;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class CatalogRulePrice
 */
class PpcRulePrice extends AbstractPrice implements BasePriceProviderInterface
{
    /**
     * Price type identifier string
     */
    const PRICE_CODE = 'ppc_rule_price';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $dateTime;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param Calculator $calculator
     * @param TimezoneInterface $dateTime
     * @param StoreManager $storeManager
     * @param Session $customerSession
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        Calculator $calculator,
        PriceCurrencyInterface $priceCurrency,
        TimezoneInterface $dateTime,
        StoreManager $storeManager,
        Session $customerSession,
        CalculatorInterface $calculatorInterface,
        Data $helper
    ) {
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency);
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->calculatorInterface = $calculatorInterface;
        $this->helper = $helper;
    }

    /**
     * Returns catalog rule value
     *
     * @return float|boolean
     */
    public function getValue()
    {
        if ($this->helper->isAdvanced()) {
            $this->value = false;
        } else {
            if (null === $this->value) {
                if ($this->product->hasData(self::PRICE_CODE)) {
                    $this->value = (float)$this->product->getData(self::PRICE_CODE) ?: false;
                } else {
                    $this->value = $this->calculatorInterface->
                    calculate($this->product->getData('price'), $this->product);
                    $this->value = $this->value ? (float)$this->value : false;
                }
                if ($this->value) {
                    $this->value = $this->priceCurrency->convertAndRound($this->value);
                }
            }
        }
        return $this->value;
    }
}
