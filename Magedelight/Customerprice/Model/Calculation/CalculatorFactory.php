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

namespace Magedelight\Customerprice\Model\Calculation;

use Magedelight\Customerprice\Helper\Data as Helper;
use Magedelight\Customerprice\Model\Calculation\Calculator\GlobalDiscountCalculator;

//use Magedelight\Customerprice\Model\Calculation\Calculator\CustomerTierPriceCalculator;
//use Magedelight\Customerprice\Model\Calculation\Calculator\CategoryDiscountCalculator;

class CalculatorFactory
{
    /**
     * @var GstHelper
     */
    protected $helper;
    
    /**
     *
     * @var GlobalDiscountCalculator
     */
    protected $globalDiscountCalculator;

    /**
     * CalculationFactory constructor.
     *
     * @param GstHelper $helper
     */
    public function __construct(
        Helper $helper,
        GlobalDiscountCalculator $globalDiscountCalculator
    ) {
        $this->helper = $helper;
        $this->globalDiscountCalculator = $globalDiscountCalculator;
    }

    /**
     * @return Calculator\CalculatorInterface
     */
    public function get()
    {
        return $this->globalDiscountCalculator;
    }
}
