<?php

namespace Magedelight\Customerprice\Model\Data;

use Magedelight\Customerprice\Api\Data\CustomerpriceInterface;

/**
 * Class Customerprice
 *
 * @package Magedelight\Customerprice\Model\Data
 */
class Customerprice extends \Magento\Framework\Api\AbstractExtensibleObject implements CustomerpriceInterface
{

    /**
     * Get customerprice_id
     * @return string|null
     */
    public function getCustomerpriceId()
    {
        return $this->_get(self::CUSTOMERPRICE_ID);
    }

    /**
     * Set customerprice_id
     * @param string $customerpriceId
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setCustomerpriceId($customerpriceId)
    {
        return $this->setData(self::CUSTOMERPRICE_ID, $customerpriceId);
    }

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get customer_name
     * @return string|null
     */
    public function getCustomerName()
    {
        return $this->_get(self::CUSTOMER_NAME);
    }

    /**
     * Set customer_name
     * @param string $customerName
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * Get customer_email
     * @return string|null
     */
    public function getCustomerEmail()
    {
        return $this->_get(self::CUSTOMER_EMAIL);
    }

    /**
     * Set customer_email
     * @param string $customerEmail
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * Set product_id
     * @param string $productId
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get product_name
     * @return string|null
     */
    public function getProductName()
    {
        return $this->_get(self::PRODUCT_NAME);
    }

    /**
     * Set product_name
     * @param string $productName
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setProductName($productName)
    {
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    /**
     * Get price
     * @return string|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Set price
     * @param string $price
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Get log_price
     * @return string|null
     */
    public function getLogPrice()
    {
        return $this->_get(self::LOG_PRICE);
    }

    /**
     * Set log_price
     * @param string $logPrice
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setLogPrice($logPrice)
    {
        return $this->setData(self::LOG_PRICE, $logPrice);
    }

    /**
     * Get new_price
     * @return string|null
     */
    public function getNewPrice()
    {
        return $this->_get(self::NEW_PRICE);
    }

    /**
     * Set new_price
     * @param string $newPrice
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setNewPrice($newPrice)
    {
        return $this->setData(self::NEW_PRICE, $newPrice);
    }

    /**
     * Get qty
     * @return string|null
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * Set qty
     * @param string $qty
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Magedelight\Customerprice\Api\Data\CustomerpriceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magedelight\Customerprice\Api\Data\CustomerpriceExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
