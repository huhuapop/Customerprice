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

declare(strict_types = 1);

namespace Magedelight\Customerprice\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magedelight\Customerprice\Model\Resolver\CreateCategoryPriceService;

class CreateCategoryprice implements ResolverInterface
{
    
    /**
     * @var creatCategoryPriceService
     */
    protected $creatCategoryPriceService;

    /**
     *
     * @param CreateCategoryPriceService $creatCategoryPriceService
     */
    public function __construct(
        CreateCategoryPriceService $creatCategoryPriceService
    ) {
        $this->creatCategoryPriceService = $creatCategoryPriceService;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['input']) || !is_array($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }

        $customerpriceData = $this->creatCategoryPriceService->execute($args['input']);

        if (!is_numeric($customerpriceData->getDiscount())) {
            throw new GraphQlInputException(__('Please enter value of status from 1 or 2. 1 is for Enabled and 2 is for Disabled.'));
        }


        return ['category_price' => [
            'categoryprice_id' => $customerpriceData->getCategorypriceId(),
            'customer_id' => $customerpriceData->getCustomerId(),
            'customer_name' => $customerpriceData->getCustomerName(),
            'customer_email' => $customerpriceData->getCustomerEmail(),
            'category_id' => $customerpriceData->getCategoryId(),
            'category_name' => $customerpriceData->getCategoryName(),
            'discount' => $customerpriceData->getDiscount()]
        ];
    }
}
