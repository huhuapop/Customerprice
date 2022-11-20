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

namespace Magedelight\Customerprice\Model\Source;

class Layouts
{
    protected $objectManager;
    protected $pageLayoutBuilder;

    /**
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     */
    public function __construct(
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
    ) {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
    }

    public function toOptionArray()
    {
        $result = [
            [
                'value' => '',
                'label' => '-- Please Select --'
            ],
            [
                'value' => 'empty',
                'label' => 'Empty'
            ],
            [
                'value' => '1column',
                'label' => '1 column'
            ],
            [
                'value' => '2columns-left',
                'label' => '2 columns with left bar'
            ],
            [
                'value' => '2columns-right',
                'label' => '2 columns with right bar'
            ],
            [
                'value' => '3columns',
                'label' => '3 columns'
            ]
        ];

        //$result = $this->pageLayoutBuilder->getPageLayoutsConfig()->toOptionArray(true);

        return $result;
    }
}
