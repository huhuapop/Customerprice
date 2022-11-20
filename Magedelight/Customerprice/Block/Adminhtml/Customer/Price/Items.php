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

namespace Magedelight\Customerprice\Block\Adminhtml\Customer\Price;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item;

/**
 * Adminhtml sales order create items block.
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Items extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Contains button descriptions to be shown at the top of accordion.
     *
     * @var array
     */
    protected $_buttons = [];

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
     * @var array
     */
    protected $websites;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote    $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create  $orderCreate
     * @param PriceCurrencyInterface                  $priceCurrency
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\GiftMessage\Model\Save         $giftMessageSave
     * @param \Magento\Tax\Model\Config               $taxConfig
     * @param \Magento\Tax\Helper\Data                $taxData
     * @param \Magento\GiftMessage\Helper\Message     $messageHelper
     * @param StockRegistryInterface                  $stockRegistry
     * @param StockStateInterface                     $stockState
     * @param array                                   $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
        \Magento\Directory\Helper\Data $_directoryHelper,
        \Magento\Framework\Registry $registry,
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
        $this->_directoryHelper = $_directoryHelper;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Define block ID.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_product_items_items');
    }

    /**
     * Accordion header text.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Price per customer');
    }

    /**
     * Returns all visible items.
     *
     * @return Item[]
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Add button to the items header.
     *
     * @param array $args
     */
    public function addButton($args)
    {
        $this->_buttons[] = $args;
    }

    public function getReadOnly()
    {
        return false;
    }

    /**
     * Return HTML code of the block.
     *
     * @return string
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
     */
    protected function _toHtml()
    {
        return parent::_toHtml();
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
