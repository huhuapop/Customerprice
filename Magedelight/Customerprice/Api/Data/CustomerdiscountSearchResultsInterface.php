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
 * Interface CustomerdiscountSearchResultsInterface
 *
 * @package Magedelight\Customerprice\Api\Data
 */
interface CustomerdiscountSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Customerprice list.
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface[]
     */
    public function getItems();

    /**
     * Set customerprice_id list.
     * @param \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
