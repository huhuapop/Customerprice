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

namespace Magedelight\Customerprice\Block\Adminhtml\Category;

class AssignCustomer extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'customer/category/assign_customer.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;
    protected $blockFormdetails;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magedelight\Customerprice\Model\ResourceModel\Categoryprice\CollectionFactory $categorypriceFactory
     * @param \Magento\Customer\Model\Customer $customer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magedelight\Customerprice\Model\ResourceModel\Categoryprice\CollectionFactory $categorypriceFactory,
        \Magento\Customer\Model\CustomerFactory $customer,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->_categorypriceFactory = $categorypriceFactory;
        $this->customer = $customer;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Magedelight\Customerprice\Block\Adminhtml\Category\Tab\Customer::class,
                'category.customer.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $vProducts = $this->_categorypriceFactory->create()
                            ->addFieldToFilter('category_id', $this->getCategory()->getId())
                            ->addFieldToSelect('customer_id');
        $products = [];
        foreach ($vProducts as $pdct) {
            $products[$pdct->getCustomerId()]  = '';
        }

        if (!empty($products)) {
            return $this->jsonEncoder->encode($products);
        }
        return '{}';
    }
    /**
     * Retrieve current category instance
     *
     * @return array|null
     */
    public function getCategory()
    {
        return $this->registry->registry('category');
    }
    
    public function getHeaderText()
    {
        return __('Price per customer');
    }
    /**
     * Get buttons html.
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'label' => __('Add Selected Customer(s) to Category'),
            'class' => 'action-default scalable add action-default primary',
            'id' => 'add_selected_customers',
        ];

        return $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            $addButtonData
        )->toHtml();
    }
    
    public function getBlockForms()
    {
        if (null === $this->blockFormdetails) {
            $this->blockFormdetails = $this->getLayout()->createBlock(
                \Magedelight\Customerprice\Block\Adminhtml\Category\Tab\GridCustomer::class,
                'category.customer.gridcategory'
            )->setTemplate('customer/category/gridcategory.phtml');
        }
        return $this->blockFormdetails->toHtml();
    }
    public function getCustomerName($custId)
    {
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $this->customer->create()->load($custId);
        return $customer->getName();
    }
    public function getexitsCustomer()
    {
        $vProducts = $this->_categorypriceFactory->create()
                            ->addFieldToFilter('category_id', $this->getCategory()->getId())
                            ->addFieldToSelect('customer_id');
        $exists= [];
        foreach ($vProducts as $option) {
            $exists[] = $option['customer_id'];
        }
        return json_encode($exists);
    }
}
