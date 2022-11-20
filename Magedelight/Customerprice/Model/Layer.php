<?php
/**
 *
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

use Magedelight\Customerprice\Model\ResourceModel\Discount\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer\ContextInterface;
use Magento\Catalog\Model\Layer\StateFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Store\Model\StoreManagerInterface;

class Layer extends \Magento\Catalog\Model\Layer
{
    /**
     * @var array
     */
    protected $_productCollection = [];

    /**
     * @var Config
     */
    protected $catalogConfig;

    /**
     * @var Visibility
     */
    protected $productVisibility;

    /**
     * @var SessionFactory
     */
    protected $customerSession;

    /**
     * @var ResourceModel\Discount\CollectionFactory
     */
    protected $discountFactory;

    /**
     * @var Customerprice
     */
    protected $customerprice;

    /**
     * @var Categoryprice
     */
    protected $categoryprice;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var ProductFactory
     */
    protected $mdproductcollection;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var Grouped
     */
    protected $grouped;

    /**
     * @var Configurable
     */
    protected $configurable;

    /**
     * Layer constructor.
     * @param ContextInterface $context
     * @param StateFactory $layerStateFactory
     * @param ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param Product $catalogProduct
     * @param StoreManagerInterface $storeManager
     * @param SessionFactory $customerSession
     * @param Categoryprice $categoryprice
     * @param Customerprice $customerprice
     * @param Configurable $configurable
     * @param Grouped $grouped
     * @param Type $type
     * @param CategoryFactory $categoryFactory
     * @param Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Http $request
     * @param Config $catalogConfig
     * @param Visibility $productVisibility
     * @param ProductFactory $productcollectionFactory
     * @param ResourceModel\Discount\CollectionFactory $discountFactory
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StateFactory $layerStateFactory,
        AttributeCollectionFactory $attributeCollectionFactory,
        Product $catalogProduct,
        StoreManagerInterface $storeManager,
        SessionFactory $customerSession,
        Categoryprice $categoryprice,
        Customerprice $customerprice,
        Configurable $configurable,
        Grouped $grouped,
        Type $type,
        CategoryFactory $categoryFactory,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        Http $request,
        Config $catalogConfig,
        Visibility $productVisibility,
        ProductFactory $productcollectionFactory,
        CollectionFactory $discountFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );
        $this->request = $request;
        $this->catalogConfig = $catalogConfig;
        $this->productVisibility = $productVisibility;
        $this->customerSession = $customerSession;
        $this->configurable = $configurable;
        $this->grouped = $grouped;
        $this->categoryFactory = $categoryFactory;
        $this->type = $type;
        $this->customerprice = $customerprice;
        $this->categoryprice = $categoryprice;
        $this->mdproductcollection = $productcollectionFactory;
        $this->discountFactory = $discountFactory;
        $this->collectionProvider = $context->getCollectionProvider();
    }

    /**
     * @return Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductCollection()
    {
        $customerId = $this->customerSession->create()->getCustomerId();
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->collectionProvider->getCollection($this->getCurrentCategory());
            $this->prepareProductCollection($collection);
            $discountLoad = $this->discountFactory->create()
                ->addFieldToFilter('customer_id', ['eq' => $customerId])
                ->getFirstItem();
            if(!$discountLoad->hasData()) {
                $this->FilterByCustomerDiscount($collection,$customerId);
            }
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
        return $collection->distinct(true);
    }

    /**
     * @param Collection $collection
     * @return $this|\Magento\Catalog\Model\Layer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareProductCollection($collection)
    {
        $collection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->setStore($this->_storeManager->getStore())
            ->addMinimalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        return $this;
    }

    /**
     * @param $collection
     * @param $customerId
     * @return Collection
     */
    public function FilterByCustomerDiscount($collection,$customerId) {
        $ids = array();
        $result = array();
        $allIds = array();
        $configurableIds = array();
        $groupedIds = array();
        $bundleIds = array();
        $productIds = array();
        /* @var $collection Collection */
        $categoryCollection = $this->categoryprice->getCollection()
            ->addFieldToFilter('customer_id', ['eq' => $customerId])
            ->getColumnValues('category_id');

        $productCollection = $this->customerprice->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', ['eq' => $customerId])
            ->getColumnValues('product_id');

        if(!empty($categoryCollection)) {
            foreach ($categoryCollection as $key => $product) {
                $category = $this->categoryFactory->create()->load($product);
                $ids[] = $category->getProductCollection()->getAllIds();
            }
            foreach ($ids as $id) {
                $result = array_unique(array_merge($result,$id));
            }
            if(!empty($productCollection)) {
                $allIds = array_unique(array_merge($result,$productCollection));
            } else {
                $allIds = $result;
            }
        } else {
            if(!empty($productCollection)) {
                $allIds = $productCollection;
            }
        }
        if(empty($allIds)) {
            return $collection->addIdFilter(array('in' => 0));
        } else {
            foreach ($allIds as $productId) {
                $item = $this->mdproductcollection->create()->load($productId);
                if ($item->getVisibility() == 1) {
                    $configurableIds[] = $this->configurable->getParentIdsByChild($productId);
                }
                $groupedIds[] = $this->grouped->getParentIdsByChild($productId);
                $bundleIds[] = $this->type->getParentIdsByChild($productId);
            }
            $parentProductIds = array_merge($configurableIds,$groupedIds,$bundleIds);
            foreach ($parentProductIds as $ids) {
                if (is_array($ids) && !empty($ids)) {
                    $productIds[] = $ids[0];
                }
            }
            $productIds = array_unique($productIds);
            if(!empty($productIds)) {
                return $collection->addIdFilter(array('in' => array_unique(array_merge($productIds,$allIds))));
            }
            return $collection->addIdFilter(array('in' => $allIds));
        }
    }
}
