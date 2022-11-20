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

declare(strict_types=1);

namespace Magedelight\Customerprice\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Categoryprice implements ResolverInterface
{
    
    private $categorypriceDataProvider;
   
    /**
     *
     * @param \Magedelight\Customerprice\Model\Resolver\DataProvider\Categoryprice $categorypriceDataProvider
     */
    public function __construct(
        \Magedelight\Customerprice\Model\Resolver\DataProvider\Categoryprice $categorypriceDataProvider
    ) {
        $this->categorypriceDataProvider = $categorypriceDataProvider;
    }
 
    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $categorypriceData = $this->categorypriceDataProvider->getcategoryPrice();
        return $categorypriceData;
    }
}
