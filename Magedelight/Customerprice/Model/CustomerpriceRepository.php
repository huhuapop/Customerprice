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

use Magedelight\Customerprice\Api\CustomerpriceRepositoryInterface;
use Magedelight\Customerprice\Api\Data\CustomerpriceSearchResultsInterfaceFactory;
use Magedelight\Customerprice\Api\Data\CustomerpriceInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magedelight\Customerprice\Model\ResourceModel\Customerprice as ResourceCustomerprice;
use Magedelight\Customerprice\Model\ResourceModel\Customerprice\Collection;
use Magedelight\Customerprice\Model\ResourceModel\Customerprice\CollectionFactory as CustomerpriceCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Customer\Model\Customer;
use Magento\Catalog\Model\Product;

/**
 * Class CustomerpriceRepository
 *
 * @package Magedelight\Customerprice\Model
 */
class CustomerpriceRepository implements CustomerpriceRepositoryInterface
{

    protected $resource;

    protected $customerpriceFactory;

    protected $customerpriceCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataCustomerpriceFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    
    protected $customers;
    
    protected $product;

    /**
     *
     * @param ResourceCustomerprice $resource
     * @param \Magedelight\Customerprice\Model\CustomerpriceFactory $customerpriceFactory
     * @param CustomerpriceInterfaceFactory $dataCustomerpriceFactory
     * @param CustomerpriceCollectionFactory $customerpriceCollectionFactory
     * @param CustomerpriceSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Magento\Framework\App\Request\Http $request
     * @param Customer $customers
     * @param Product $product
     */
    public function __construct(
        ResourceCustomerprice $resource,
        CustomerpriceFactory $customerpriceFactory,
        CustomerpriceInterfaceFactory $dataCustomerpriceFactory,
        CustomerpriceCollectionFactory $customerpriceCollectionFactory,
        CustomerpriceSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Framework\App\Request\Http $request,
        Customer $customers,
        Product $product
    ) {
        $this->resource = $resource;
        $this->customerpriceFactory = $customerpriceFactory;
        $this->customerpriceCollectionFactory = $customerpriceCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCustomerpriceFactory = $dataCustomerpriceFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->request = $request;
        $this->customers = $customers;
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Magedelight\Customerprice\Api\Data\CustomerpriceInterface $customerprice
    ) {
        /* if (empty($customerprice->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $customerprice->setStoreId($storeId);
        } */
        
        $customerpriceData = $this->request->getParams();
        
        if (empty($customerpriceData)) {
            $customerpriceData = $this->extensibleDataObjectConverter->toNestedArray(
                $customerprice,
                [],
                \Magedelight\Customerprice\Api\Data\CustomerpriceInterface::class
            );
        }
        //$product = $this->product->create();
        $customer = $this->customers->load($customerpriceData['customer_id']);
        $product = $this->product->load($customerpriceData['product_id']);
        
        
        if (!empty($customerpriceData['customerprice_id'])) {
            $customerpricei = $this->customerpriceFactory->create();
            $this->resource->load($customerpricei, $customerpriceData['customerprice_id']);
            if (!$customerpricei->getId()) {
                throw new NoSuchEntityException(__('Customerprice with id "%1" does not exist.', $customerpriceData['customerprice_id']));
            }
        }
        if (empty($product->getName())) {
            throw new NoSuchEntityException(__('Product with id "%1" does not exist.', $customerpriceData['product_id']));
        }
        
        if (empty($customer->getFirstname())) {
            throw new NoSuchEntityException(__('Customer with id "%1" does not exist.', $customerpriceData['customer_id']));
        }
        
        if (!is_numeric($customerpriceData['log_price'])) {
            throw new NoSuchEntityException(__('Please enter valid log price.'));
        }
        if (!is_numeric($customerpriceData['new_price'])) {
            throw new NoSuchEntityException(__('Please enter valid new price.'));
        }
        
        $customerpricemodel = $this->customerpriceCollectionFactory->create()->addFieldToFilter('customer_id', $customerpriceData['customer_id'])
                                        ->addFieldToFilter('product_id', $customerpriceData['product_id']);
        
//        if($customerpricemodel->getCustomerpriceId){
//
//        }
        $customerpriceData['customer_name'] = $customer->getFirstname().' '.$customer->getLastname();
        $customerpriceData['customer_email'] = $customer->getEmail();
        $customerpriceData['price'] = $product->getPrice();
        $customerpriceData['product_name'] = $product->getName();
        
//        $checkTokenIsExist = $this->subscriptionsFactory->create()
//            ->getCollection()
//            ->addFieldToFilter('token', $subscriptionsData['token']);
//        if ($checkTokenIsExist->getSize() > 0) {
//            $subscriptionsData['subscriptions_id'] = $checkTokenIsExist->getFirstItem()->getSubscriptionsId();
//        }
        
        $customerpriceModel = $this->customerpriceFactory->create()->setData($customerpriceData);
        
        try {
            $this->resource->save($customerpriceModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the customerprice: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($customerpriceId)
    {
        $customerprice = $this->customerpriceFactory->create();
        $this->resource->load($customerprice, $customerpriceId);
        if (!$customerprice->getId()) {
            throw new NoSuchEntityException(__('Customerprice with id "%1" does not exist.', $customerpriceId));
        }
        return $customerprice->getDataModel();
    }
    
    /**
     * @param $customerpriceId
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceInterface int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($customerpriceId)
    {
        $customerprice = $this->customerpriceFactory->create();
        $this->resource->load($customerprice, $customerpriceId);
        if (!$customerprice->getId()) {
            throw new NoSuchEntityException(__('Customerprice with id "%1" does not exist.', $customerpriceId));
        }
        return $customerpriceId;
    }
    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magedelight\Customerprice\Api\Data\CustomerpriceSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magedelight\Customerprice\Api\Data\CustomerpriceSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Magedelight\Customerprice\Model\ResourceModel\Customerprice\Collection $collection */
        $collection = $this->customerpriceCollectionFactory->create();

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
            $field = 'customerprice_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($collection->getItems());
    }

    /**
     * {@inheritdoc}
     */
//    public function delete(
//        \Magedelight\Customerprice\Api\Data\CustomerpriceInterface $customerprice
//    ) {
//        try {
//            $customerpriceModel = $this->customerpriceFactory->create();
//            $this->resource->load($customerpriceModel, $customerprice->getCustomerpriceId());
//            $this->resource->delete($customerpriceModel);
//        } catch (\Exception $exception) {
//            throw new CouldNotDeleteException(__(
//                'Could not delete the Customerprice: %1',
//                $exception->getMessage()
//            ));
//        }
//        return true;
//    }
    
    
    public function delete($customerpriceId)
    {
        $customerpriceModel = $this->customerpriceFactory->create();
        $customerpriceModel->setId($customerpriceId);
        if ($this->resource->delete($customerpriceModel)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function deleteById($customerpriceId)
    {
        return $this->delete($this->getById($customerpriceId));
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
