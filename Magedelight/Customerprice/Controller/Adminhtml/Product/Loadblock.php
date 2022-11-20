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

namespace Magedelight\Customerprice\Controller\Adminhtml\Product;

class Loadblock extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context           $context
     * @param \Magento\Framework\Registry                     $registry
     * @param \Magento\Framework\View\Result\PageFactory      $resultPageFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $request = $this->getRequest();
        $block = $request->getParam('block');

        $resultPage = $this->_resultPageFactory->create();

        $resultPage->addHandle('md_customerprice_product_load_block_json');
        $resultPage->addHandle('md_customerprice_product_load_block_'.$block);

        $result = $resultPage->getLayout()->renderElement('content');

        return $this->resultRawFactory->create()->setContents($result);
    }
}
