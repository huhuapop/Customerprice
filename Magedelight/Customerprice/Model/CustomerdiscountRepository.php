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

namespace Magedelight\Customerprice\Model;

use Magedelight\Customerprice\Api\CustomerdiscountRepositoryInterface;
use Magedelight\Customerprice\Api\Data\CustomerdiscountSearchResultsInterfaceFactory;
use Magedelight\Customerprice\Api\Data\CustomerdiscountInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magedelight\Customerprice\Model\ResourceModel\Discount as ResourceCustomerdiscount;
use Magedelight\Customerprice\Model\ResourceModel\Discount\Collection;
use Magedelight\Customerprice\Model\ResourceModel\Discount\CollectionFactory as CustomerdiscountCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Customer\Model\Customer;

class CustomerdiscountRepository implements CustomerdiscountRepositoryInterface
{
    
    protected $resource;

    protected $customerdiscountFactory;

    protected $customerdiscountCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataCustomerdiscountFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    
    protected $customers;
    

    /**
     *
     * @param \Magedelight\Customerprice\Model\ResourceCustomerdiscount $resource
     * @param \Magedelight\Customerprice\Model\CustomerdiscountFactory $customerdiscountFactory
     * @param CustomerdiscountInterfaceFactory $dataCustomerdiscountFactory
     * @param CustomerdiscountCollectionFactory $customerdiscountCollectionFactory
     * @param CustomerdiscountSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Magento\Framework\App\Request\Http $request
     * @param Customer $customers
     */
    public function __construct(
        ResourceCustomerdiscount $resource,
        DiscountFactory $customerdiscountFactory,
        CustomerdiscountInterfaceFactory $dataCustomerdiscountFactory,
        CustomerdiscountCollectionFactory $customerdiscountCollectionFactory,
        CustomerdiscountSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Framework\App\Request\Http $request,
        Customer $customers
    ) {
        $this->resource = $resource;
        $this->customerdiscountFactory = $customerdiscountFactory;
        $this->customerdiscountCollectionFactory = $customerdiscountCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCustomerdiscountFactory = $dataCustomerdiscountFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->request = $request;
        $this->customers = $customers;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface $customerdiscount
    ) {
        
        $customerdiscountData = $this->request->getParams();
        
        if (empty($customerdiscountData)) {
            $customerdiscountData = $this->extensibleDataObjectConverter->toNestedArray(
                $customerdiscount,
                [],
                \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface::class
            );
        }
        
        if (!empty($customerdiscountData['discount_id'])) {
            $customerdiscounti = $this->customerdiscountFactory->create();
            $this->resource->load($customerdiscounti, $customerdiscountData['discount_id']);
            if (!$customerdiscounti->getDiscountId()) {
                throw new NoSuchEntityException(__('Customer discount with id "%1" does not exist.', $customerdiscountData['discount_id']));
            }
            if ($customerdiscounti->getCustomerId()!=$customerdiscountData['customer_id']) {
                 throw new NoSuchEntityException(__('Customer not exist with discount id "%1"', $customerdiscountData['discount_id']));
            }
        }
       
        
        $customer = $this->customers->load($customerdiscountData['customer_id']);
        
        if (empty($customer->getFirstname())) {
            throw new NoSuchEntityException(__('Customer with id "%1" does not exist.', $customerdiscountData['customer_id']));
        }
        
        if (!is_numeric($customerdiscountData['value'])) {
            throw new NoSuchEntityException(__('Please enter valid discount value.'));
        }
        
        $categorydiscountmodel = $this->customerdiscountCollectionFactory->create()
                    ->addFieldToFilter('customer_id', $customerdiscountData['customer_id'])
                    ->toArray();
           
        if (empty($customerdiscountData['discount_id'])) {
            if ($categorydiscountmodel['totalRecords'] > 0) {
                throw new NoSuchEntityException(__('Customer with same discount already exist.'));
            }
        } else {
            if ($categorydiscountmodel['totalRecords'] > 1) {
                throw new NoSuchEntityException(__('No change in the input.'));
            }
        }
        
        if (!empty($customerdiscountData['discount_id'])) {
            unset($customerdiscountData['customer_id']);
        }
        
        
        
        $customerdiscountModel = $this->customerdiscountFactory->create()->setData($customerdiscountData);
        
        try {
            $this->resource->save($customerdiscountModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the customerdiscount: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($discountId)
    {
        $customerdiscount = $this->customerdiscountFactory->create();
        $this->resource->load($customerdiscount, $discountId);
        if (!$customerdiscount->getDiscountId()) {
            throw new NoSuchEntityException(__('Customer discount with id "%1" does not exist.', $discountId));
        }
        return $customerdiscount->getDataModel();
    }
    
    /**
     * @param $discountId
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountInterface int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($discountId)
    {
        $customerdiscount = $this->customerdiscountFactory->create();
        $this->resource->load($customerdiscount, $discountId);
        if (!$customerdiscount->getDiscountId()) {
            throw new NoSuchEntityException(__('Customer discount with id "%1" does not exist.', $discountId));
        }
        return $discountId;
    }
    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magedelight\Customerprice\Api\Data\CustomerdiscountSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magedelight\Customerprice\Api\Data\CustomerdiscountSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Magedelight\Customerprice\Model\ResourceModel\Discount\Collection $collection */
        $collection = $this->customerdiscountCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            $field = 'discount_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($collection->getItems());
    }

    
    public function delete($discountId)
    {
        $customerdiscountModel = $this->customerdiscountFactory->create();
        $customerdiscountModel->setId($discountId);
        if ($this->resource->delete($customerdiscountModel)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function deleteById($discountId)
    {
        return $this->delete($this->getById($discountId));
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }
}
