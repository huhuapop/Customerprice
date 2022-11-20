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
        \Magedelight\Customerprice\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
        $this->_coreRegistry = $context->getRegistry();
        $this->_mdlayer = $layerResolver->get();
        $this->request = $request;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->request = $request;
        $this->scopeConfig = $context->getScopeConfig();
        $this->helper = $helper;
    }
    
    public function _prepareLayout()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $title = $this->scopeConfig->getValue('customerprice/general/page_title', $storeScope);
        $this->pageConfig->getTitle()->set(__($title));
        if ($this->_getProductCollection()) {
            $toolbar = $this->getLayout()
                       ->createBlock(
                    'Magento\Catalog\Block\Product\ProductList\Toolbar',
                    'custom_product_list_toolbar'
                    )
                    ->setTemplate('Magedelight_Customerprice::product/list/toolbar.phtml')
                    ->setCollection($this->_getProductCollection());
    
                $pager = $this->getLayout()->createBlock(
                    'Magento\Theme\Block\Html\Pager',
                    'AllProduct.product.pager'
                )->setAvailableLimit(array(12=>12,24=>24,36=>36))->setShowPerPage(true)->setCollection(
                    $this->_getProductCollection()
                );
                $this->setChild('pager', $pager);
                $this->setChild('toolbar', $toolbar);
                $this->_getProductCollection()->load();
            }
        return $this;
    }

    protected function _getProductCollection()
    {
        
        if ($this->_productCollection === null) {   
            $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
            //get values of current limit
            $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 12;
            $this->_productCollection = $this->getLayer()->getProductCollection()
                                        ->setPageSize($pageSize)
                                        ->setCurPage($page);
        }
        return $this->_productCollection;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    }

    public function getLayer()
    {
        $this->setLayer($this->_mdlayer);
        return $this->_mdlayer;
    }

    public function getmoduleStatus()
    {
        if ($this->helper->isEnabled()) {
            return true;
        }
        return false;
    }
}
