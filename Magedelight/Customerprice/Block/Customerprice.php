<?php

/**
 * Magedelight
 * Copyright (C) 2016 Magedelight <info@Magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Customerprice
 * @copyright Copyright (c) 2016 Mage Delight (http://www.Magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@Magedelight.com>
 */

namespace Magedelight\Customerprice\Block;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as productCollectionFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Customerprice extends \Magento\Catalog\Block\Product\ListProduct
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
     /**
      * @var \Magento\Framework\App\Config\ScopeConfigInterface
      */
    protected $scopeConfig;
    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magedelight\Customerprice\Model\Categoryprice $categoryprice,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        productCollectionFactory $productCollectionFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
        $this->_coreRegistry = $context->getRegistry();
        $this->_mdlayer = $layerResolver->get();
        $this->request = $request;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->request = $request;
        $this->scopeConfig = $context->getScopeConfig();
    }
    
    public function _prepareLayout()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $title = $this->scopeConfig->getValue('customerprice/general/page_title', $storeScope);
        $this->pageConfig->getTitle()->set(__($title));
        return $this;
    }

    protected function _getProductCollection()
    {
        
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->getLayer()->getProductCollection();
        }
        return $this->_productCollection;
    }

    public function getLayer()
    {
        $this->setLayer($this->_mdlayer);
        return $this->_mdlayer;
    }
}
