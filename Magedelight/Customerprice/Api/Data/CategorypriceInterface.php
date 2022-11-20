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
 * Interface CategorypriceInterface
 *
 * @package Magedelight\Customerprice\Api\Data
 */
interface CategorypriceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    
    const CATEGORYPRICE_ID = 'categoryprice_id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const CATEGORY_ID = 'category_id';
    const CATEGORY_NAME = 'category_name';
    const DISCOUNT = 'discount';
    
    
    /**
     * Get categoryprice_id
     * @return string|null
     */
    public function getCategorypriceId();

    /**
     * Set categoryprice_id
     * @param string $categorypriceId
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     */
    public function setCategorypriceId($categorypriceId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
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
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
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
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     */
    public function setCustomerEmail($customerEmail);
    
    /**
     * Get category_id
     * @return string|null
     */
    public function getCategoryId();

    /**
     * Set customer_email
     * @param string $categoryId
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     */
    public function setCategoryId($categoryId);
    
    /**
     * Get category_name
     * @return string|null
     */
    public function getCategoryName();

    /**
     * Set customer_email
     * @param string $categoryName
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     */
    public function setCategoryName($categoryName);
    
    /**
     * Get discount
     * @return string|null
     */
    public function getDiscount();

    /**
     * Set discount
     * @param string $discount
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     */
    public function setDiscount($discount);
}
