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

namespace Magedelight\Customerprice\Api;

interface CustomerdiscountRepositoryInterface
{
    

    /**
     * Save Customerdiscount
     * @param \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface $customerdiscount
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface $customerdiscount
    );
    
    
    /**
     * Retrieve Customerdiscount
     * @param string $discountId
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($discountId);
    
    /**
     * @param \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface $discountId
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($discountId);

    /**
     * Retrieve Customerdiscount matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Customerdiscount
     * @param \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface $customerdiscount
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($discountId);

    /**
     * Delete Customerdiscount by ID
     * @param string $discountId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($discountId);
}
