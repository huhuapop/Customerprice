<?php

namespace Magedelight\Customerprice\Controller\Adminhtml\Category;

class CustomerGrid extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Cms\Model\Wysiwyg\Config $config
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Cms\Model\Wysiwyg\Config $config
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->registry = $registry;
        $this->customer = $customer;
        $this->config = $config;
    }

    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id', false);
        $myModel = $this->customer;
        if ($id) {
            $myModel->load($id);
        }
        $this->registry->register('category', $myModel);
        //$this->_objectManager->get('Magento\Cms\Model\Wysiwyg\Config');
        $this->config;
        
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                \Magedelight\Customerprice\Block\Adminhtml\Category\Tab\Customer::class,
                'category.product.grid'
            )->toHtml()
        );
    }
}
