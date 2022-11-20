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

namespace Magedelight\Customerprice\Block\Adminhtml\Form\Field;

/**
 * Custom import CSV file field for shipping table rates.
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Importcategory extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setType('file');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';

        $html .= '<input id="time_condition" type="hidden" name="'.$this->getName().'" value="'.time().'" />';

        //$html .= parent::getElementHtml();
        $html .= '<input id="customer_category_price_sample_import" name="categorypriceimport" data-ui-id="file-groups-sample-fields-import-value" value="" class="" type="file" />';

        return $html;
    }
}
