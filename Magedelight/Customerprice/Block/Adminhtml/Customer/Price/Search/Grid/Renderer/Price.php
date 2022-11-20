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

namespace Magedelight\Customerprice\Block\Adminhtml\Customer\Price\Search\Grid\Renderer;

/**
 * Adminhtml sales create order product search grid price column renderer.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Price extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Price
{
    /**
     * Render minimal price for downloadable products.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getTypeId() == 'downloadable') {
            $row->setPrice($row->getPrice());
        }

        return parent::render($row);
    }
}
