<?php
/**
 *
 */
namespace Magedelight\Customerprice\Pricing;

use Magento\Framework\Pricing\Adjustment\AdjustmentInterface;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Pricing\Price\CustomOptionPriceInterface;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magedelight\Customerprice\Model\Calculation\Calculator\CalculatorInterface;
use Magento\Catalog\Model\Product\Type;
use Magedelight\Customerprice\Helper\Data as helper;

/**
 * Tax pricing adjustment model
 */
class Adjustment implements AdjustmentInterface
{
    /**
     * Adjustment code tax
     */
    const ADJUSTMENT_CODE = 'pricepercustomer';

    /**
     * \Magento\Catalog\Helper\Data
     *
     * @var CatalogHelper
     */
    protected $catalogHelper;

    /**
     * @var int|null
     */
    protected $sortOrder;

    /**
     * @param TaxHelper $taxHelper
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param int|null $sortOrder
     */
    public function __construct(
        \Magento\Catalog\Helper\Data $catalogHelper,
        PriceCurrencyInterface $priceCurrency,
        CalculatorInterface $catalogPriceCalculator,
        helper $helper,
        $sortOrder = null
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->priceCurrency = $priceCurrency;
        $this->catalogPriceCalculator = $catalogPriceCalculator;
        $this->helper = $helper;
        $this->sortOrder = $sortOrder;
    }

    /**
     * Get adjustment code
     *
     * @return string
     */
    public function getAdjustmentCode()
    {
        return self::ADJUSTMENT_CODE;
    }

    /**
     * Define if adjustment is included in base price
     *
     * @return bool
     */
    public function isIncludedInBasePrice()
    {
        return true;
    }

    /**
     * Define if adjustment is included in display price
     *
     * @return bool
     */
    public function isIncludedInDisplayPrice()
    {
        return true;
    }

    /**
     * Extract adjustment amount from the given amount value
     *
     * @param float $amount
     * @param SaleableInterface $saleableItem
     * @param null|array $context
     * @return float
     */
    public function extractAdjustment($amount, SaleableInterface $saleableItem, $context = [])
    {
        return 0;
    }

    /**
     * Apply adjustment amount and return result value
     *
     * @param float $amount
     * @param SaleableInterface $saleableItem
     * @param null|array $context
     * @return float
     */
    public function applyAdjustment($amount, SaleableInterface $saleableItem, $context = [])
    {
        if (isset($context[CustomOptionPriceInterface::CONFIGURATION_OPTION_FLAG])) {
            return $amount;
        }

//        if (isset($context[BundleSelectionPrice::CONFIGURATION_OPTION_FLAG])) {
//            return $amount;
//        }

        if (!$this->helper->isAdvanced()) {
            return $amount;
        }
        $value = $this->catalogPriceCalculator->calculate($amount, $saleableItem);
        if ($value) {
            return $value;
        }
        return $amount;
    }

    /**
     * Check if adjustment should be excluded from calculations along with the given adjustment
     *
     * @param string $adjustmentCode
     * @return bool
     */
    public function isExcludedWith($adjustmentCode)
    {
        return $this->getAdjustmentCode() === $adjustmentCode;
    }

    /**
     * Return sort order position
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
