<?php
/*
* Magedelight
* Copyright (C) 2017 Magedelight <info@Magedelight.com>
*
* @category Magedelight
* @package Magedelight_Customerprice
* @copyright Copyright (c) 2017 Mage Delight (http://www.Magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@Magedelight.com>
*/
namespace Magedelight\Customerprice\Controller\Adminhtml\Category;
 
use Magento\Backend\App\Action;
 
class Delete extends Action
{
    public $categoryPrice;
    
    public function __construct(
        Action\Context $context,
        \Magedelight\Customerprice\Model\Categoryprice $categoryPrice,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->categoryPrice = $categoryPrice;
        $this->resultJsonFactory = $resultJsonFactory;
    }
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultJsonFactory = $this->resultJsonFactory->create();
        if ($id) {
            try {
                $categoryPrice = $this->categoryPrice;
                $categoryPrice->load($id);
                $categoryPrice->delete();
                return $resultJsonFactory->setData(true);
            } catch (\Exception $e) {
                return $resultJsonFactory->setData(false);
            }
        }
        return $resultJsonFactory->setData(true);
    }
}
