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

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface CategorypriceRepositoryInterface
 *
 * @package Magedelight\Customerprice\Api
 */
interface CategorypriceRepositoryInterface
{
    
    /**
     * Save Categoryprice
     * @param \Magedelight\Customerprice\Api\Data\CustomerpriceInterface $categoryprice
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Magedelight\Customerprice\Api\Data\CategorypriceInterface $categoryprice
    );
    
    
    /**
     * Retrieve Categoryprice
     * @param string $categorypriceId
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($categorypriceId);
    
    /**
     * @param \Magedelight\Customerprice\Api\Data\CategorypriceInterface $categorypriceId
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($categorypriceId);

    /**
     * Retrieve Categoryprice matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Categoryprice by ID
     * @param  $categorypriceId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($categorypriceId);

    /**
     * Delete Categoryprice by ID
     * @param string $categorypriceId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($categorypriceId);
}
