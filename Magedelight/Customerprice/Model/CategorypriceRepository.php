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

use Magedelight\Customerprice\Api\CategorypriceRepositoryInterface;
use Magedelight\Customerprice\Api\Data\CategorypriceSearchResultsInterfaceFactory;
use Magedelight\Customerprice\Api\Data\CategorypriceInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magedelight\Customerprice\Model\ResourceModel\Categoryprice as ResourceCategoryprice;
use Magedelight\Customerprice\Model\ResourceModel\Categoryprice\Collection;
use Magedelight\Customerprice\Model\ResourceModel\Categoryprice\CollectionFactory as CategorypriceCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Customer\Model\Customer;
use Magento\Catalog\Model\Category;

/**
 * Class CategorypriceRepository
 *
 * @package Magedelight\Customerprice\Model
 */
class CategorypriceRepository implements CategorypriceRepositoryInterface
{
    
    protected $resource;
    
    protected $categorypriceFactory;
    
    protected $categorypriceCollectionFactory;
    
    protected $searchResultsFactory;
    
    protected $dataObjectHelper;
    
    protected $dataObjectProcessor;
    
    protected $dataCategorypriceFactory;
    
    protected $extensionAttributesJoinProcessor;
    
    private $storeManager;
    
    private $collectionProcessor;
    
    protected $extensibleDataObjectConverter;
    
    protected $customers;
    
    protected $category;

    /**
     *
     * @param ResourceCategoryprice $resource
     * @param \Magedelight\Customerprice\Model\CategorypriceFactory $categorypriceFactory
     * @param CategorypriceInterfaceFactory $dataCategorypriceFactory
     * @param CategorypriceCollectionFactory $categorypriceCollectionFactory
     * @param CategorypriceSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Magento\Framework\App\Request\Http $request
     * @param Customer $customers
     * @param Category $category
     */
    public function __construct(
        ResourceCategoryprice $resource,
        CategorypriceFactory $categorypriceFactory,
        CategorypriceInterfaceFactory $dataCategorypriceFactory,
        CategorypriceCollectionFactory $categorypriceCollectionFactory,
        CategorypriceSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Framework\App\Request\Http $request,
        Customer $customers,
        Category $category
    ) {
        $this->resource = $resource;
        $this->categorypriceFactory = $categorypriceFactory;
        $this->categorypriceCollectionFactory = $categorypriceCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCategorypriceFactory = $dataCategorypriceFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->request = $request;
        $this->customers = $customers;
        $this->category = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Magedelight\Customerprice\Api\Data\CategorypriceInterface $categoryprice
    ) {

        $categorypriceData = $this->request->getParams();
        
        if (empty($categorypriceData)) {
            $categorypriceData = $this->extensibleDataObjectConverter->toNestedArray(
                $categoryprice,
                [],
                \Magedelight\Customerprice\Api\Data\CategorypriceInterface::class
            );
        }
        
        $customer = $this->customers->load($categorypriceData['customer_id']);
        
        if (empty($customer->getEmail())) {
            throw new NoSuchEntityException(__('Customer with id "%1" does not exist.', $categorypriceData['customer_id']));
        }
        
        if (!is_numeric($categorypriceData['discount'])) {
            throw new NoSuchEntityException(__('Please enter valid discount number.'));
        }
        $categorypriceData['customer_name'] = $customer->getFirstname().' '.$customer->getLastname();
        $categorypriceData['customer_email'] = $customer->getEmail();
        
        
        $category_data = $this->category->load($categorypriceData['category_id']);
        $categorypriceData['category_name'] = $category_data->getName();
        $categorypriceModel = $this->categorypriceFactory->create();
        if (empty($category_data->getName())) {
            throw new NoSuchEntityException(__('Category with id "%1" does not exist.', $categorypriceData['category_id']));
        }
        
        if (!empty($categorypriceData['categoryprice_id'])) {
            $this->resource->load($categorypriceModel, $categorypriceData['categoryprice_id']);
            if (!$categorypriceModel->getCategorypriceId()) {
                throw new NoSuchEntityException(__('Categoryprice with id "%1" does not exist.', $categorypriceData['categoryprice_id']));
            }
        }
            $categorypricemodel = $this->categorypriceCollectionFactory->create()
                    ->addFieldToFilter('customer_id', $categorypriceData['customer_id'])
                    ->addFieldToFilter('category_id', $categorypriceData['category_id'])
                    ->toArray();
           
        if (empty($categorypriceData['categoryprice_id'])) {
            if ($categorypricemodel['totalRecords'] > 0) {
                throw new NoSuchEntityException(__('Customer with same Category already exist.'));
            }
        } else {
            if ($categorypricemodel['totalRecords'] > 1) {
                throw new NoSuchEntityException(__('Customer with same Category already exist.'));
            }
        }
        
        if ($categorypricemodel['totalRecords'] == 1) {
             unset($categorypriceData['category_id']);
             unset($categorypriceData['category_name']);
        }
        
        $categorypricedata = $categorypriceModel->setData($categorypriceData);

        try {
            $this->resource->save($categorypricedata);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the categoryprice: %1',
                $exception->getMessage()
            ));
        }
        return true;
        //return $categorypricedata['category_name'];
    }

    /**
     * {@inheritdoc}
     */
    public function get($categorypriceId)
    {
        $categoryprice = $this->categorypriceFactory->create();
        $this->resource->load($categoryprice, $categorypriceId);
        if (!$categoryprice->getId()) {
            throw new NoSuchEntityException(__('Categoryprice with id "%1" does not exist.', $categorypriceId));
        }
        return $categoryprice->getDataModel();
    }

    /**
     * @param $categorypriceId
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceInterface int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($categorypriceId)
    {
        $categoryprice = $this->categorypriceFactory->create();
        $this->resource->load($categoryprice, $categorypriceId);
        if (!$categoryprice->getId()) {
            throw new CouldNotSaveException(__('Categoryprice with id "%1" does not exist.', $categorypriceId));
        }
        return $categorypriceId;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magedelight\Customerprice\Api\Data\CategorypriceSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magedelight\Customerprice\Api\Data\CategorypriceSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Magedelight\Customerprice\Model\ResourceModel\Categoryprice\Collection $collection */
        $collection = $this->categorypriceCollectionFactory->create();

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
            $field = 'categoryprice_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($collection->getItems());
    }

    public function delete($categorypriceId)
    {
        $categorypriceModel = $this->categorypriceFactory->create();
        $categorypriceModel->setId($categorypriceId);
        if ($this->resource->delete($categorypriceModel)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($categorypriceId)
    {
        return $this->delete($this->getById($categorypriceId));
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
