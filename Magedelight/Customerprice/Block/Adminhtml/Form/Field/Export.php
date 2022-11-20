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
 * Export CSV button for shipping table rates.
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Export extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory           $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper                             $escaper
     * @param \Magento\Backend\Helper\Data                           $helper
     * @param array                                                  $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->_backendUrl = $backendUrl;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
        $buttonBlock = $this->getForm()->getParent()
            ->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class);

        $params = ['website' => $buttonBlock->getRequest()->getParam('website')];

        $urlsPass = $this->_backendUrl->getUrl('md_customerprice/index/export', $params);
        $data = [
            'label' => __('Export CSV'),
            'onclick' => "setLocation('".$urlsPass."' )",
            'class' => '',
        ];

        $htmlToPass = $buttonBlock->setData($data)->toHtml();

        return $htmlToPass;
    }
}
