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

class Export extends \Magento\Config\Controller\Adminhtml\System\AbstractConfig
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
     * @var \Magedelight\Customerprice\Model\Customerprice
     */
    protected $customerPrice;
    
    /**
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $product;

   /**
    *
    * @param \Magento\Backend\App\Action\Context $context
    * @param \Magento\Config\Model\Config\Structure $configStructure
    * @param ConfigSectionChecker $sectionChecker
    * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    * @param \Magento\Customer\Model\CustomerFactory $customerFactory
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \Magedelight\Customerprice\Model\Customerprice $customerprice
    * @param \Magento\Catalog\Model\ProductFactory $product
    */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Config\Model\Config\Structure $configStructure,
        ConfigSectionChecker $sectionChecker,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magedelight\Customerprice\Model\Customerprice $customerprice,
        \Magento\Catalog\Model\ProductFactory $product
    ) {
        $this->storeManager = $storeManager;
        $this->fileFactory = $fileFactory;
        $this->customerFactory = $customerFactory;
        $this->customerPrice = $customerprice;
        $this->product = $product;
        parent::__construct($context, $configStructure, $sectionChecker);
    }

    /**
     * Export shipping table rates in csv format.
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'pricepercustomer.csv';
        $content = '';
        $_columns = [
            'email', 'sku', 'qty', 'price', 'website',
        ];
        $data = [];
        foreach ($_columns as $column) {
            $data[] = '"'.$column.'"';
        }
        $content .= implode(',', $data)."\n";
        
        $pricePerCustomer = $this->customerPrice->getCollection();
        foreach ($pricePerCustomer as $_pricePerCustomer) {
            $product = $this->product->create()->load(trim($_pricePerCustomer['product_id']));
            $customer = $this->customerFactory->create()->load(trim($_pricePerCustomer['customer_id']));

            $data = [];
            $data[] = trim($_pricePerCustomer->getCustomerEmail());
            $data[] = trim($product->getSku());
            $data[] = trim($_pricePerCustomer->getQty());
            $data[] = trim($_pricePerCustomer->getLogPrice());
            $data[] = trim($customer->getWebsiteId());

            $content .= implode(',', $data)."\n";
        }

        return $this->fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
