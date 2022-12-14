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

namespace Magedelight\Customerprice\Block\Adminhtml\Customer\Price;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Order create data.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Data extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Currency factory.
     *
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @param \Magento\Backend\Block\Template\Context     $context
     * @param \Magento\Backend\Model\Session\Quote        $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create      $orderCreate
     * @param PriceCurrencyInterface                      $priceCurrency
     * @param \Magento\Directory\Model\CurrencyFactory    $currencyFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        $this->_currencyFactory = $currencyFactory;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Retrieve avilable currency codes.
     *
     * @return string[]
     */
    public function getAvailableCurrencies()
    {
        $dirtyCodes = $this->getStore()->getAvailableCurrencyCodes();
        $codes = [];
        if (is_array($dirtyCodes) && count($dirtyCodes)) {
            $rates = $this->_currencyFactory->create()->getCurrencyRates(
                $this->_storeManager->getStore()->getBaseCurrency(),
                $dirtyCodes
            );
            foreach ($dirtyCodes as $code) {
                if (isset($rates[$code]) || $code == $this->_storeManager->getStore()->getBaseCurrencyCode()) {
                    $codes[] = $code;
                }
            }
        }

        return $codes;
    }

    /**
     * Retrieve curency name by code.
     *
     * @param string $code
     *
     * @return string
     */
    public function getCurrencyName($code)
    {
        return $this->_localeCurrency->getCurrency($code)->getName();
    }

    /**
     * Retrieve curency name by code.
     *
     * @param string $code
     *
     * @return string
     */
    public function getCurrencySymbol($code)
    {
        $currency = $this->_localeCurrency->getCurrency($code);

        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }

    /**
     * Retrieve current order currency code.
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->getStore()->getCurrentCurrencyCode();
    }
}
