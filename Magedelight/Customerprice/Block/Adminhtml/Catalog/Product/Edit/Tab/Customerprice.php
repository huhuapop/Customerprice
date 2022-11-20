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

namespace Magedelight\Customerprice\Block\Adminhtml\Catalog\Product\Edit\Tab;

/**
 * Products mass update inventory tab.
 */
class Customerprice extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Retrieve field suffix.
     *
     * @return string
     */
    public function getFieldSuffix()
    {
        return 'customerprice';
    }

    /**
     * Retrieve current store id.
     *
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store');

        return intval($storeId);
    }

    /**
     * Tab settings.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Price Per Customer');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Price Per Customer');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        $id = $this->getRequest()->getParam('id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Catalog\Model\Product')->load($id);
        if ($product->getTypeId() == 'simple' || $product->getTypeId() == 'downloadable' || $product->getTypeId() == 'virtual') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('md_customerprice/product/index', ['_current' => true]);
    }
}
