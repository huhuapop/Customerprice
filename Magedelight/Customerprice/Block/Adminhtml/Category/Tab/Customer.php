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

/**
 * Product in category grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magedelight\Customerprice\Block\Adminhtml\Category\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Customer extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $logger;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_faqFactory;

    public $status;
    
    public $questiontype;
    
    protected $_categorypriceFactory;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magedelight\Customerprice\Model\ResourceModel\Categoryprice\CollectionFactory $categorypriceFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_categorypriceFactory = $categorypriceFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('catalog_category_customer');
        $this->setDefaultSort('customer_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * @return array|null
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('category');
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedCustomer();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(['in_category' => 1]);
        }
        $collection = $this->_customerFactory->create()->getCollection();
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId > 0) {
            $collection->addAttributeToFilter('store_id', $storeId);
        }
        $this->setCollection($collection);

        if ($this->getCategory()->getProductsReadonly()) {
            $productIds = $this->_getSelectedCustomer();
            if (empty($productIds)) {
                $productIds = 0;
            }
            $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
        }

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        if (!$this->getCategory()->getProductsReadonly()) {
            $this->addColumn(
                'in_category',
                [
                    'type' => 'checkbox',
                    'name' => 'in_category',
                    'values' => $this->_getSelectedCustomer(),
                    'index' => 'entity_id',
                    'renderer'=> '\Magedelight\Customerprice\Block\Adminhtml\Category\Tab\Renderer\Renderer',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction'
                ]
            );
        }
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('firstname', ['header' => __('Name'), 'index' => 'firstname']);
        $this->addColumn('email', ['header' => __('Email'), 'index' => 'email']);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('md_customerprice/category/CustomerGrid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedCustomer()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if ($products === null) {
            $vProducts = $this->_categorypriceFactory->create()
                                ->addFieldToFilter('category_id', $this->getCategory()->getId())
                                ->addFieldToSelect('customer_id');
            $products = [];
            foreach ($vProducts as $pdct) {
                $products[]  = $pdct->getCustomerId();
            }
        }
        return $products;
    }
}
