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

interface CustomerdiscountInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const DISCOUNT_ID = 'discount_id';
    const CUSTOMER_ID = 'customer_id';
    const VALUE = 'value';
    
    /**
     * Get discount_id
     * @return int|null
     */
    public function getDiscountId();

    /**
     * Set discount_id
     * @param int $discountId
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface
     */
    public function setDiscountId($discountId);
    
    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface
     */
    public function setCustomerId($customerId);
    
    /**
     * Get customer_id
     * @return string|null
     */
    public function getValue();

    /**
     * Set customer_id
     * @param string $value
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface
     */
    public function setValue($value);
}
