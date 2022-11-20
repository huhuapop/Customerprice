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

namespace Magedelight\Customerprice\Controller\Index;

class Offers extends \Magento\Framework\App\Action\Action
{
    const MD_LAYER = 'mdlayer';
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     * @param \Magento\Framework\DataObject                      $requestObject
     * @param \Magento\Framework\App\Action\Context              $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\DataObject $requestObject,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magedelight\Customerprice\Helper\Data $helper,
        \Magento\Framework\UrlInterface $url
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_requestObject = $requestObject;
        $this->scopeConfig = $scopeConfig;
        $this->layerResolver = $layerResolver;
        $this->helper = $helper;
        $this->url = $url;
        $this->response = $context->getResponse();
        $this->redirect = $context->getRedirect();
    }

    public function execute()
    {
        if ($this->helper->isEnabled()) {
            $this->layerResolver->create(self::MD_LAYER);
            $resultPage = $this->resultPageFactory->create();

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $layout = $this->scopeConfig->getValue('customerprice/general/layout', $storeScope);

            if ($layout != 'empty') {
                $resultPage->getConfig()->addBodyClass('page-products');
            }
            if ($layout == '1column' || $layout == '3columns') {
                $resultPage->getConfig()->addBodyClass('page-with-filter');
            }

            $resultPage->getConfig()->setPageLayout($layout);

            $ObjectManager= \Magento\Framework\App\ObjectManager::getInstance();
            $context = $ObjectManager->get('Magento\Framework\App\Http\Context');
            $isLoggedIn = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);

            if ($isLoggedIn) {
                return $resultPage;
            } else {
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('customer/account/login');
            }
        } else {
            $homeUrl = $this->url->getUrl();
            $this->getResponse()->setRedirect($homeUrl);
            return;
        }
    }

    /**
     * Retrieve response object
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
