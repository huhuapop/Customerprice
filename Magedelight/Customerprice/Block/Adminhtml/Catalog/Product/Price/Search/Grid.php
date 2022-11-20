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

namespace Magedelight\Customerprice\Block\Adminhtml\Catalog\Product\Price\Search;

/**
 * Adminhtml sales order create search products block.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Sales config.
     *
     * @var \Magento\Sales\Model\Config
     */
    protected $_salesConfig;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * Session quote.
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * Catalog config.
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * Customer factory.
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Customer Group factory.
     *
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $_customerGroupFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data            $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory   $productFactory
     * @param \Magento\Catalog\Model\Config           $catalogConfig
     * @param \Magento\Backend\Model\Session\Quote    $sessionQuote
     * @param \Magento\Sales\Model\Config             $salesConfig
     * @param \Magento\Framework\Module\Manager       $moduleManager
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\GroupFactory $customerGroupFactory,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_customerGroupFactory = $customerGroupFactory;
        $this->_sessionQuote = $sessionQuote;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customerprice_customer_search_grid');

        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Retrieve quote store object.
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_sessionQuote->getStore();
    }

    /**
     * Retrieve quote object.
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->_sessionQuote->getQuote();
    }

    /**
     * Add column filter to collection.
     *
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     *
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedCustomers();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Prepare collection to be displayed in the grid.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_customerFactory->create()->getCollection()
                ->addNameToSelect()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('email')
                ->addAttributeToSelect('group_id');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns.
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_customers',
            [
            'type' => 'checkbox',
            'name' => 'in_customers',
            'values' => $this->_getSelectedCustomers(),
            'align' => 'center',
            'index' => 'entity_id',
            'header_css_class' => 'col-select',
            'column_css_class' => 'col-select',
                ]
        );
        $this->addColumn(
            'entity_id',
            [
            'header' => __('ID'),
            'sortable' => true,
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
            'index' => 'entity_id',
                ]
        );
        $this->addColumn(
            'name',
            [
            'header' => __('Name'),
            'index' => 'name',
                ]
        );

        $this->addColumn(
            'email',
            [
            'header' => __('Email'),
            'index' => 'email',
                ]
        );

        $groups = $this->_customerGroupFactory->create()->getCollection()
                ->addFieldToFilter('customer_group_id', ['gt' => 0])
                ->load()
                ->toOptionHash();

        $this->addColumn('group', [
            'header' => __('Group'),
            'index' => 'group_id',
            'type' => 'options',
            'options' => $groups,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Get grid url.
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'md_customerprice/product/loadblock',
            ['block' => 'product_customer_grid', '_current' => true, 'collapse' => null]
        );
    }

    /**
     * Get selected products.
     *
     * @return mixed
     */
    protected function _getSelectedCustomers()
    {
        $products = $this->getRequest()->getPost('customers', []);
        return $products;
    }

    /**
     * Add custom options to product collection.
     *
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection();
        return parent::_afterLoadCollection();
    }
}
