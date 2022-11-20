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

namespace Magedelight\Customerprice\Block;

class Toplink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $_template = 'Magedelight_Customerprice::link.phtml';

    /**
     * @param \Magento\Framework\View\Element\Template\Context   $context
     * @param array                                              $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magedelight\Customerprice\Helper\Data $helper,
        array $data = []
    ) {
    
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->httpContext = $httpContext;
        $this->helper = $helper;
    }

    public function getHref()
    {
        $context = $this->httpContext;
        $isLoggedIn = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);

        if ($isLoggedIn) {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $url = trim($this->scopeConfig->getValue('customerprice/general/identifier', $storeScope));

            return $this->getUrl($url);
        } else {
            return $this->getUrl('customer/account/login/');
        }
    }

    public function getLabel()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $label = $this->scopeConfig->getValue('customerprice/general/title', $storeScope);

        return __($label);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $mode_enable = $this->scopeConfig->getValue('customerprice/general/enable', $storeScope);
        $top_enable = $this->scopeConfig->getValue('customerprice/general/top_enable', $storeScope);
        
        if (!$mode_enable) {
            return '';
        }
        
        if (!$top_enable) {
            return '';
        }

        return parent::_toHtml();
    }

    public function getmoduleStatus()
    {
        if ($this->helper->isEnabled()) {
            return true;
        }
        return false;
    }
}
