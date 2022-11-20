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

use Magento\Framework\App\Http\Context as CustomerContext;
use Magento\Framework\View\Element\Template\Context as CurrentContext;
use Magedelight\Customerprice\Helper\Data;
use Magento\Framework\View\Element\Html\Link;

class Toplink extends Link
{
    /**
     * @var CustomerContext
     */
    protected $customer;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Toplink constructor.
     * @param CurrentContext $context
     * @param CustomerContext $customer
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        CurrentContext $context,
        CustomerContext $customer,
        Data $helper,
        array $data = []
    ) {
    
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->customer = $customer;
        $this->helper = $helper;
    }

    public function getHref()
    {
        $isLoggedIn = $this->customer->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if ($isLoggedIn) {
            $url = trim($this->helper->getConfig('customerprice/general/identifier'));
            return $this->getUrl($url);
        }
        return $this->getUrl('customer/account/login/');
    }

    public function getLabel()
    {
        $label = $this->helper->getConfig('customerprice/general/title');
        return __($label);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if(!$this->helper->getConfig('customerprice/general/enable')) {
            return '';
        }
        if(!$this->helper->getConfig('customerprice/general/top_enable')) {
            return '';
        }
        return parent::_toHtml();
    }
}
