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

namespace Magedelight\Customerprice\Model\Data;

use Magedelight\Customerprice\Api\Data\CategorypriceInterface;

/**
 * Class Customerprice
 *
 * @package Magedelight\Customerprice\Model\Data
 */
class Categoryprice extends \Magento\Framework\Api\AbstractExtensibleObject implements CategorypriceInterface
{

    /**
     * Get categoryprice_id
     * @return string|null
     */
    public function getCategorypriceId()
    {
        return $this->_get(self::CATEGORYPRICE_ID);
    }

    /**
     * Set categoryprice_id
     * @param string $categorypriceId
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     */
    public function setCategorypriceId($categorypriceId)
    {
        return $this->setData(self::CATEGORYPRICE_ID, $categorypriceId);
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
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
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
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
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
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    
    /**
     * Get category_id
     * @return string|null
     */
    public function getCategoryId()
    {
        return $this->_get(self::CATEGORY_ID);
    }

    /**
     * Set category_id
     * @param int $categoryId
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * Get category_name
     * @return string|null
     */
    public function getCategoryName()
    {
        return $this->_get(self::CATEGORY_NAME);
    }

    /**
     * Set category_name
     * @param string $categoryName
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     */
    public function setCategoryName($categoryName)
    {
        return $this->setData(self::CATEGORY_NAME, $categoryName);
    }

    /**
     * Get new_price
     * @return string|null
     */
    public function getDiscount()
    {
        return $this->_get(self::DISCOUNT);
    }

    /**
     * Set new_price
     * @param string $discount
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     */
    public function setDiscount($discount)
    {
        return $this->setData(self::DISCOUNT, $discount);
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
