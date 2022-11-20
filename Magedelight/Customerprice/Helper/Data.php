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

namespace Magedelight\Customerprice\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_GENERAL_ENABLED = 'customerprice/general/enable';
    const XML_PATH_ADVANCED_ENABLED = 'customerprice/general/advanceprice';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    protected $_customerSession;

    /**
     * Customer Group factory.
     *
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $_customerGroupFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData;
    
    protected $_optionHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\GroupFactory $customerGroupFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\GroupFactory $customerGroupFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Helper\Product\Configuration $optionHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Framework\App\State $state,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->_customerGroupFactory = $customerGroupFactory;
        $this->priceCurrency = $priceCurrency;
        $this->_storeManager = $storeManager;
        $this->_catalogData = $catalogData;
        $this->_optionHelper = $optionHelper;
        $this->_customerSession = $customerSession;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->state = $state;
        $this->orderCreate = $orderCreate;
        parent::__construct($context);
    }

    public function isEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLED, $storeScope);
    }

    public function isAdvanced()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::XML_PATH_ADVANCED_ENABLED, $storeScope);
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function isCustomerPriceAllow()
    {
        $isEnabled = $this->isEnabled();
        if ($this->state->getAreaCode() == 'adminhtml') {
            $isLoggedIn = $this->orderCreate->getQuote()->getCustomerIsGuest() ? false : true;
        } else {
            $isLoggedIn = $this->customerSessionFactory->create()->isLoggedIn();
        }
        if ($isEnabled && $isLoggedIn) {
            return true;
        }
        return false;
    }

    /**
     * @param float $price
     * @param bool  $format
     *
     * @return float
     */
    public function convertPrice($price, $format = true)
    {
        return $format ? $this->priceCurrency->convertAndFormat($price) : $this->priceCurrency->convert($price);
    }

    /**
     * @param float $price
     *
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->priceCurrency->format(
            $price,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->_storeManager->getStore()
        );
    }
}
