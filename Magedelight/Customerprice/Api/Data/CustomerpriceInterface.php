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

namespace Magedelight\Customerprice\Api\Data;

/**
 * Interface CustomerpriceInterface
 *
 * @package Magedelight\Customerprice\Api\Data
 */
interface CustomerpriceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const CUSTOMERPRICE_ID = 'customerprice_id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_NAME = 'product_name';
    const PRICE = 'price';
    const LOG_PRICE = 'log_price';
    const NEW_PRICE = 'new_price';
    const QTY = 'qty';

    /**
     * Get customerprice_id
     * @return string|null
     */
    public function getCustomerpriceId();

    /**
     * Set customerprice_id
     * @param string $customerpriceId
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setCustomerpriceId($customerpriceId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get customer_name
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set customer_name
     * @param string $customerName
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setCustomerName($customerName);

    /**
     * Get customer_email
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer_email
     * @param string $customerEmail
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setProductId($productId);

    /**
     * Get product_name
     * @return string|null
     */
    public function getProductName();

    /**
     * Set product_name
     * @param string $productName
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setProductName($productName);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setPrice($price);

    /**
     * Get log_price
     * @return string|null
     */
    public function getLogPrice();

    /**
     * Set log_price
     * @param string $logPrice
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setLogPrice($logPrice);

    /**
     * Get new_price
     * @return string|null
     */
    public function getNewPrice();

    /**
     * Set new_price
     * @param string $newPrice
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setNewPrice($newPrice);

    /**
     * Get qty
     * @return string|null
     */
    public function getQty();

    /**
     * Set qty
     * @param string $qty
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setQty($qty);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Magedelight\Customerprice\Api\Data\CustomerpriceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magedelight\Customerprice\Api\Data\CustomerpriceExtensionInterface $extensionAttributes
    );
}
