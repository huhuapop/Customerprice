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

class Customerprice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('customerprice', 'customerprice_id');
    }

    public function uploadAndImport(\Magento\Framework\DataObject $object)
    {
        try {
            $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'customerpriceimport']);
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
            throw new \Magento\Framework\Exception\LocalizedException(__('Please correct Price Per Customer File Format.'));
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

                $row = $this->_getImportRow($csvLine, $rowNumber, $headers);
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

                $priceperCustomer = $objectManager->create('Magedelight\Customerprice\Model\Customerprice');
                $ppc = $priceperCustomer->getCollection()
                    ->addFieldToFilter('customer_id', $row['customer_id'])
                    ->addFieldToFilter('product_id', $row['product_id'])
                    ->addFieldToFilter('qty', $row['qty'])
                    ->getFirstItem();

                if ($ppc->hasData('customerprice_id')) {
                    $row['customerprice_id'] = $ppc->getData('customerprice_id');
                }
                if ($row !== false && $row['product_id'] > 0 && $row['customer_id'] > 0) {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

                    $priceCustomer = $objectManager->create('Magedelight\Customerprice\Model\Customerprice');
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

    protected function _getImportRow($row, $rowNumber = 0, $headers)
    {
        if (count($row) < 4) {
            $this->_importErrors[] = __('Please correct Table Rates format in the Row #%1.', $rowNumber);

            return false;
        }
        $emailKey = array_search('email', $headers);
        $skuKey = array_search('sku', $headers);
        $qtyKey = array_search('qty', $headers);
        $priceKey = array_search('price', $headers);
        $websiteKey = array_search('website', $headers);
        // strip whitespace from the beginning and end of each row
        foreach ($row as $k => $v) {
            $row[$k] = trim($v);
        }

        $email = $row[$emailKey];
        $sku = $row[$skuKey];
        $qty = $row[$qtyKey];
        if ($websiteKey) {
            $website_id = $row[$websiteKey];
        }
        $newprice = $row[$priceKey];
        $logprice = $row[$priceKey];
        if (!is_numeric($qty)) {
            $this->_importErrors[] = __('Invalid Qty Price "%1" in the Row #%2.', $row[$qtyKey], $rowNumber);

            return false;
        } else {
            if ($qty <= 0) {
                $this->_importErrors[] = __('Qty should be greater than 0 in the Row #%1.', $rowNumber);

                return false;
            }
        }
        $matches = [];
        if (!is_numeric($newprice)) {
            preg_match('/(.*)%/', $newprice, $matches);
            if ((is_array($matches) && count($matches) <= 0) || !is_numeric($matches[1])) {
                $this->_importErrors[] = __('Invalid New Price "%1" in the Row #%2.', $row[$priceKey], $rowNumber);

                return false;
            } elseif (is_numeric($matches[1]) && ($matches[1] <= 0 || $matches[1] > 100)) {
                $this->_importErrors[] = __('Invalid New Price "%1" in the Row #%2.Percentage should be greater than 0 and less or equals than 100.', $row[$priceKey], $rowNumber);

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
        $product = $objectManager->get('Magento\Catalog\Model\Product')->loadByAttribute('sku', $sku);
        //var_dump($product); exit;
        if (!$product) {
            $this->_importErrors[] = __('%1 Products are not allowed in the row #%2.', ucfirst($sku), $rowNumber);
            return false;
        }
        if ($product->getTypeId() == 'grouped' || $product->getTypeId() == 'bundle' || $product->getTypeId() == 'configurable') {
            $this->_importErrors[] = __('%1 Products are not allowed in the row #%2.', ucfirst($product->getTypeId()), $rowNumber);

            return false;
        }
        

        $productName = $product->getName();
        $productId = $product->getId();
        $price = $product->getPrice();
        if (is_array($matches) && count($matches) > 0) {
            if ($product->getTypeId() != 'bundle') {
                $newprice = $product->getPrice() - ($product->getPrice() * ($matches[1] / 100));
            } else {
                if ($matches[1] < 0 || $matches[1] > 100) {
                    $this->_importErrors[] = __('Invalid New Price "%1" in the row #%2.Percentage should be greater than 0 and less or equals than 100.', $newprice, $rowNumber);

                    return false;
                } else {
                    $newprice = $matches[1];
                }
            }
        } else {
            if ($product->getTypeId() == 'bundle') {
                if ($newprice < 0 || $newprice > 100) {
                    $this->_importErrors[] = __('Invalid New Price "%1" in the row #%2.Percentage should be greater than 0 and less or equals than 100.', $newprice, $rowNumber);

                    return false;
                }
            }
        }

        return [
            'customer_id' => $customerId, // Customer Id
            'customer_name' => $customer->getName(), // Customer Name
            'customer_email' => $email, // customer email
            'product_id' => $productId, // Product Id
            'product_name' => $productName, // Product Name
            'price' => $price, // Price
            'log_price' => ($product->getTypeId() != 'bundle') ? $logprice : str_replace('%', '', $logprice),
            'new_price' => ($product->getTypeId() != 'bundle') ? $newprice : str_replace('%', '', $newprice), // New price for customer
            'qty' => $qty,           // Qty
        ];
    }
}
