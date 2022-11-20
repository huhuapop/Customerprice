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

use Magedelight\Customerprice\Api\Data\CustomerdiscountInterface;

class Customerdiscount extends \Magento\Framework\Api\AbstractExtensibleObject implements CustomerdiscountInterface
{
    
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
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
    
    /**
     * Get value
     * @return string|null
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }

    /**
     * Set value
     * @param string $value
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }
    
    /**
     * Get discount_id
     * @return int|null
     */
    public function getDiscountId()
    {
        return $this->_get(self::DISCOUNT_ID);
    }

    /**
     * Set discount_id
     * @param int $discountId
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface
     */
    public function setDiscountId($discountId)
    {
        return $this->setData(self::DISCOUNT_ID, $discountId);
    }
}
