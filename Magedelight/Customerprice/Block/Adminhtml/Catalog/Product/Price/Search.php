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

namespace Magedelight\Customerprice\Block\Adminhtml\Catalog\Product\Price;

/**
 * Adminhtml sales order create search block.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Search extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_customer_search');
    }

    /**
     * Get header text.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Please Select Customers to Add');
    }

    /**
     * Get buttons html.
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'label' => __('Add Selected Customer(s) to Product'),
            'onclick' => 'addSelectedProduct()',
            'class' => 'action-default scalable add action-default primary',
            'id' => 'add_selected_customers',
        ];

        return $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            $addButtonData
        )->toHtml();
    }

    /**
     * Get header css class.
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }
}
