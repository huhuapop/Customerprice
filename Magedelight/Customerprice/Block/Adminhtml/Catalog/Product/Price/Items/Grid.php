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

namespace Magedelight\Customerprice\Block\Adminhtml\Catalog\Product\Price\Items;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\ObjectManagerInterface;

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
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_customer_grid');
    }

    public function getOptionValues()
    {
        $productId = $this->getRequest()->getParam('id');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $optionCollection = $objectManager->create('\Magedelight\Customerprice\Model\Customerprice')
                ->getCollection()
                ->addFieldToSelect('*')->addFieldToFilter('product_id', ['eq' => $productId])
                ->setOrder('customer_id');

        $finaldata = [];
        $k = 0;
        foreach ($optionCollection as $key => $option) {
            $finaldata[$key]['id'] = $key;
            $finaldata[$key]['cname'] = htmlspecialchars($option['customer_name']);
            $finaldata[$key]['cid'] = $option['customer_id'];
            $finaldata[$key]['email'] = $option['customer_email'];
            $finaldata[$key]['newprice'] = $option['log_price'];
            $finaldata[$key]['logprice'] = $option['log_price'];
            $finaldata[$key]['qty'] = $option['qty'];

            ++$k;
        }

        return json_encode($finaldata);
    }
}
