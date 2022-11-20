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

namespace Magedelight\Customerprice\Block\Adminhtml\Customer\Price\Items;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\ObjectManagerInterface;
use Magedelight\Customerprice\Model\DiscountFactory;

/**
 * Adminhtml sales order create items grid block.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Flag to check can items be move to customer storage.
     *
     * @var bool
     */
    protected $_moveToCustomerStorage = true;

    /**
     * Tax data.
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData;

    /**
     * Wishlist factory.
     *
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * Gift message save.
     *
     * @var \Magento\GiftMessage\Model\Save
     */
    protected $_giftMessageSave;

    /**
     * Tax config.
     *
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * Message helper.
     *
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $_messageHelper;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var StockStateInterface
     */
    protected $stockState;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var array
     */
    protected $websites;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\GiftMessage\Model\Save $giftMessageSave
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param StockRegistryInterface $stockRegistry
     * @param StockStateInterface $stockState
     * @param ObjectManagerInterface $objectManager
     * @param \Magedelight\Customerprice\Model\Customerprice $customerprice
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Escaper
     * @param Discount $discount
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\GiftMessage\Model\Save $giftMessageSave,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        StockRegistryInterface $stockRegistry,
        StockStateInterface $stockState,
        ObjectManagerInterface $objectManager,
        \Magedelight\Customerprice\Model\CustomerpriceFactory $customerprice,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\Pricing\Helper\Data $helperdata,
        \Magento\Framework\Escaper $escaper,
        DiscountFactory $discount,
        \Magento\Directory\Helper\Data $_directoryHelper,
        array $data = []
    ) {
        $this->_messageHelper = $messageHelper;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_giftMessageSave = $giftMessageSave;
        $this->_taxConfig = $taxConfig;
        $this->_taxData = $taxData;
        $this->stockRegistry = $stockRegistry;
        $this->stockState = $stockState;
        $this->_objectManager = $objectManager;
        $this->customerprice = $customerprice;
        $this->product = $product;
        $this->helperdata = $helperdata;
        $this->discount = $discount;
        $this->escaper = $escaper;
        $this->_directoryHelper = $_directoryHelper;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_product_grid');
    }

    public function getOptionValues()
    {
        $customerId = $this->getRequest()->getParam('id');

        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $optionCollection = $this->customerprice->create()
                            ->getCollection()
                            ->addFieldToSelect('*')->addFieldToFilter('customer_id', ['eq' => $customerId])
                            ->setOrder('product_id');

        $finaldata = [];
        $k = 0;
        $productObj = [];
        foreach ($optionCollection as $key => $option) {
            if (!isset($productObj[$option['product_id']])) {
                $productObj[$option['product_id']] = $this->product->create()->load(trim($option['product_id']))->getTypeId();
            }
            $finaldata[$key]['id'] = $key;
            $finaldata[$key]['pname'] = $this->escaper->escapeHtml($option['product_name']);
            $finaldata[$key]['pid'] = $option['product_id'];
            $finaldata[$key]['price'] = $option['price'];
            $finaldata[$key]['newprice'] = $option['log_price'];
            $finaldata[$key]['logprice'] = $option['log_price'];
            $finaldata[$key]['qty'] = $option['qty'];
            $finaldata[$key]['website'] = $option['website_id'];
            $finaldata[$key]['css_class'] = '';
            $finaldata[$key]['sign'] = '';
            if ($productObj[$option['product_id']] == 'bundle') {
                $finaldata[$key]['css_class'] = 'validate-greater-than-zero validate-percents';
                $finaldata[$key]['sign'] = '%';
            }
            ++$k;
        }

        return json_encode($finaldata);
    }

    public function getDiscountByCustomerId($customerId)
    {
        $discount = $this->discount->create()
                ->getCollection()
                ->addFieldToFilter('customer_id', ['eq' => $customerId])
                ->getFirstItem();
        if ($discount->getId()) {
            return $discount;
        } else {
            return;
        }
    }
    
    public function currency($price, $format = true, $includeContainer = true)
    {
        $this->helperdata->currency($price, $format, $includeContainer);
    }

    public function getWebsites()
    {
        if ($this->websites !== null) {
            return $this->websites;
        }

        $this->websites = [
            0 => ['name' => __('All Websites'), 'currency' => $this->_directoryHelper->getBaseCurrencyCode()]
        ];

        /** @var $website \Magento\Store\Model\Website */
        $allWebsites = $this->_storeManager->getWebsites();
        foreach ($allWebsites as $website) {
            $this->websites[$website->getId()] = [
                'name' => $website->getName(),
                'currency' => $website->getBaseCurrencyCode()
            ];
        }
        return $this->websites;
    }

    public function getWebsiteHtml()
    {
        $html = '';
        $allWebsites = $this->getWebsites();
        foreach ($allWebsites as $key => $value) {
            $html .= '<option value="'.$key.'">'.$value['name'].' '.$value['currency'].'</option>';
        }
        return $html;
    }
}
