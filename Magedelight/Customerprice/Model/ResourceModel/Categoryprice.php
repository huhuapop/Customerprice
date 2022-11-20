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

namespace Magedelight\Customerprice\Model\ResourceModel;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;

class Categoryprice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Errors in import process.
     *
     * @var array
     */
    protected $_importErrors = [];

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_coreConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Filesystem instance.
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * Customer factory.
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context  $context
     * @param \Psr\Log\LoggerInterface                           $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\Filesystem                      $filesystem
     * @param string                                             $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\Category $category,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_coreConfig = $coreConfig;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->_customerFactory = $customerFactory;
    }

    public function _construct()
    {
        $this->_init('md_categoryprice', 'categoryprice_id');
    }

    public function uploadAndImport(\Magento\Framework\DataObject $object)
    {
        try {
            $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $uploader = $this->_objectManager->
            create(
                \Magento\MediaStorage\Model\File\Uploader::class,
                ['fileId' => 'categorypriceimport']
            );
        } catch (\Exception $e) {
            if ($e->getCode() == '666') {
                return $this;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
        }
        $csvFile = $uploader->validateFile()['tmp_name'];
        
        $website = $this->_storeManager->getWebsite($object->getScopeId());
        
        $this->_importWebsiteId = (int) $website->getId();
        $this->_importUniqueHash = [];
        $this->_importErrors = [];
        $this->_importedRows = 0;

        $tmpDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $path = $tmpDirectory->getRelativePath($csvFile);
        $stream = $tmpDirectory->openFile($path);

        // check and skip headers
        $headers = $stream->readCsv();
        if ($headers === false || count($headers) < 1) {
            $stream->close();
            throw new
            \Magento\Framework\Exception\LocalizedException(__('Please correct Price Per Customer File Format.'));
        }

        $connection = $this->getConnection();
        $connection->beginTransaction();

        try {
            $rowNumber = 1;
            $importData = [];

            while (false !== ($csvLine = $stream->readCsv())) {
                ++$rowNumber;
                if (empty($csvLine)) {
                    continue;
                }
                $row = $this->_getImportRow($csvLine, $headers, $rowNumber);
                //echo "<pre>"; print_r($row);
                if ($row !== false && $row['category_id'] > 0 && $row['customer_id'] > 0) {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $priceCollection = $objectManager->
                    create(\Magedelight\Customerprice\Model\ResourceModel\Categoryprice\CollectionFactory::class)
                    ->create();
                    $priceCollection->addFieldToFilter('category_id', $row['category_id']);
                    if (count($priceCollection) > 0) {
                        continue;
                    }
                    $priceCustomer = $objectManager->create(\Magedelight\Customerprice\Model\Categoryprice::class);
                    
                    $priceCustomer->setData($row)->save();
                }
            }
            $stream->close();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $connection->rollback();
            $stream->close();
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        } catch (\Exception $e) {
            $connection->rollback();
            $stream->close();
            $this->_logger->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while importing prices.')
            );
        }

        $connection->commit();

        if ($this->_importErrors) {
            $error = __(
                'We couldn\'t import this file because of these errors: %1',
                implode(" \n", $this->_importErrors)
            );
            throw new \Magento\Framework\Exception\LocalizedException($error);
        }

        return $this;
    }

    protected function _getImportRow($row, $headers, $rowNumber = 0)
    {
        if (count($row) < 3) {
            //echo "hiii";
            
            $this->_importErrors[] = __('Please correct Table Rates format in the Row #%1.', $rowNumber);
            
            return false;
        }
        $emailKey = array_search('customer_email', $headers);
        $catKey = array_search('category_id', $headers);
        $discountKey = array_search('discount', $headers);
        $websiteKey = array_search('website', $headers);
        // strip whitespace from the beginning and end of each row
        foreach ($row as $k => $v) {
            $row[$k] = trim($v);
        }

        $email = $row[$emailKey];
        $cat = $row[$catKey];
        if ($websiteKey) {
            $website_id = $row[$websiteKey];
        }
        $discount = $row[$discountKey];
        $matches = [];
        if (!is_numeric($discount)) {
            preg_match('/(.*)%/', $newprice, $matches);
            if ((is_array($matches) && count($matches) <= 0) || !is_numeric($matches[1])) {
                $this->_importErrors[] = __('Invalid discount "%1" in the Row #%2.', $row[$discountKey], $rowNumber);

                return false;
            } elseif (is_numeric($matches[1]) && ($matches[1] <= 0 || $matches[1] > 100)) {
                $this->_importErrors[] = __(
                    'Invalid New Price "%1" in the Row #%2.
                Percentage should be greater than 0 and less or equals than 100.',
                    $row[$discount],
                    $rowNumber
                );

                return false;
            }
        }

        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
            $this->_importErrors[] = __('Invalid email "%1" in the Row #%2.', $row[$emailKey], $rowNumber);

            return false;
        }

        if ($websiteKey) {
            $customer = $this->_customerFactory->create()->getCollection()
                    ->addNameToSelect()
                    ->addAttributeToSelect('entity_id')
                    ->addAttributeToSelect('email')
                    ->addAttributeToSelect('group_id')
                    ->addFieldToFilter('email', $email)
                    ->addFieldToFilter('website_id', $website_id)
                    ->getFirstItem();
        } else {
            $customer = $this->_customerFactory->create()->getCollection()
                    ->addNameToSelect()
                    ->addAttributeToSelect('entity_id')
                    ->addAttributeToSelect('email')
                    ->addAttributeToSelect('group_id')
                    ->addFieldToFilter('email', $email)
                    ->getFirstItem();
        }
        
        $customerId = $customer->getId();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category = $objectManager->get(\Magento\Catalog\Model\Category::class)->load($cat);
        $categoryName = $category->getName();
       
        return [
            'customer_id' => $customerId, // Customer Id
            'customer_name' => $customer->getName(), // Customer Name
            'customer_email' => $email, // customer email
            'category_id' => $category->getId(), // Category Id
            'category_name' => $categoryName, // Category Name
            'discount' => $discount, //discont
        ];
    }
    //@codingStandardsIgnoreStart
    public function getCategoryChild($cat)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $explodeCats = explode('/', $cat);
        foreach ($explodeCats as $explodeCat) {
            $category = $objectManager->get(\Magento\Catalog\Model\Category::class)
                ->loadByAttribute('name', $cat);
        }
        return ;
    }
}
