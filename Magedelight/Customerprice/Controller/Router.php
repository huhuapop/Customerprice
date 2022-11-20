<?php

/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Customerprice
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Customerprice\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response.
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\ActionFactory     $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_response = $response;
    }

    /**
     * Validate and Match.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if ($request->getModuleName() == 'md_customerprice') {
            return;
        }
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $route = trim($this->scopeConfig->getValue('customerprice/general/identifier', $storeScope));

        $identifier = trim($request->getPathInfo(), '/');
        if ($identifier == $route) {
            $request->setModuleName('md_customerprice')->setControllerName('index')->setActionName('offers');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        } else {
            return false;
        }

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
