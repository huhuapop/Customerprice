<?php

namespace Magedelight\Customerprice\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface CustomerpriceRepositoryInterface
 *
 * @package Magedelight\Customerprice\Api
 */
interface CustomerpriceRepositoryInterface
{

    /**
     * Save Customerprice
     * @param \Magedelight\Customerprice\Api\Data\CustomerpriceInterface $customerprice
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Magedelight\Customerprice\Api\Data\CustomerpriceInterface $customerprice
    );
    
    
    /**
     * Retrieve Customerprice
     * @param string $customerpriceId
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($customerpriceId);
    
    /**
     * @param \Magedelight\Customerprice\Api\Data\CustomerpriceInterface $customerpriceId
     * @return \Magedelight\Customerprice\Api\Data\CustomInterface int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($customerpriceId);

    /**
     * Retrieve Customerprice matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Customerprice
     * @param \Magedelight\Customerprice\Api\Data\CustomerpriceInterface $customerprice
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($customerpriceId);

    /**
     * Delete Customerprice by ID
     * @param string $customerpriceId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($customerpriceId);
}
