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

namespace Magedelight\Customerprice\Controller\Adminhtml\Index;

use Magento\Framework\App\ResponseInterface;
use Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker;
use Magento\Framework\App\Filesystem\DirectoryList;

class Exportcategory extends \Magento\Config\Controller\Adminhtml\System\AbstractConfig
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    
    /**
     *
     * @var \Magedelight\Customerprice\Model\Categoryprice
     */
    protected $categoryPrice;
    
    /**
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $category;

    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param ConfigSectionChecker $sectionChecker
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magedelight\Customerprice\Model\Categoryprice $categoryPrice
     * @param \Magento\Catalog\Model\CategoryFactory $category
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Config\Model\Config\Structure $configStructure,
        ConfigSectionChecker $sectionChecker,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magedelight\Customerprice\Model\Categoryprice $categoryPrice,
        \Magento\Catalog\Model\CategoryFactory $category
    ) {
        $this->storeManager = $storeManager;
        $this->fileFactory = $fileFactory;
        $this->customerFactory = $customerFactory;
        $this->categoryPrice = $categoryPrice;
        $this->category = $category;
        parent::__construct($context, $configStructure, $sectionChecker);
    }

    /**
     * Export shipping table rates in csv format.
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'pricepercategory.csv';
        $content = '';
        $_columns = [
            'customer_email', 'category_id', 'discount', 'website',
        ];
        $data = [];
        foreach ($_columns as $column) {
            $data[] = '"'.$column.'"';
        }
        $content .= implode(',', $data)."\n";
        
        $pricePerCustomer = $this->categoryPrice->getCollection();
        
        foreach ($pricePerCustomer as $_pricePerCustomer) {
            $category = $this->category->create()->load(trim($_pricePerCustomer['category_id']));

            $customer = $this->customerFactory->create()->load(trim($_pricePerCustomer['customer_id']));

            $data = [];
            $data[] = trim($_pricePerCustomer->getCustomerEmail());
            $data[] = trim($_pricePerCustomer->getCategoryId());
            $data[] = trim($_pricePerCustomer->getDiscount());
            $data[] = trim($customer->getWebsiteId());
            $content .= implode(',', $data)."\n";
        }

        return $this->fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
