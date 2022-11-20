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

namespace Magedelight\Customerprice\Model\Calculation\Calculator;

use Magedelight\Customerprice\Helper\Data as Helper;
use Magedelight\Customerprice\Model\Calculation\Calculator\CalculatorInterface;

abstract class AbstractCalculator implements CalculatorInterface
{
    /**
     * @var GstHelper
     */
    protected $helper;

    /**
     * AbstractCalculation constructor.
     *
     * @param GstHelper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }
}
