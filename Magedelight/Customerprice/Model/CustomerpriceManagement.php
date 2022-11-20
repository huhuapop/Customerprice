<?php

namespace Magedelight\Customerprice\Model;

use Magedelight\Customerprice\Api\CustomerpriceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class CustomerpriceManagement
 *
 * @package Magedelight\Customerprice\Model
 */
class CustomerpriceManagement implements \Magedelight\Customerprice\Api\CustomerpriceManagementInterface
{
    protected $customerpriceRepository;
    
    protected $searchCriteriaBuilder;


    public function __cunstruct(
        CustomerpriceRepositoryInterface $customerpriceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->customerpriceRepository = $customerpriceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerprice()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResult = $this->customerpriceRepository->getList($searchCriteria);
        return $searchResult->getItems();

        //return 'hello api GET return the $param ';
    }
}
