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

declare(strict_types = 1);

namespace Magedelight\Customerprice\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magedelight\Customerprice\Model\Resolver\CreateCustomerDiscountService;

class CreateCustomerdiscount implements ResolverInterface
{
     
    /**
     * @var createCustomerDiscount
     */
    protected $createCustomerDiscountService;

    /**
     *
     * @param createCustomerDiscount $createCustomerDiscount
     */
    public function __construct(
        CreateCustomerDiscountService $createCustomerDiscountService
    ) {
        $this->createCustomerDiscountService = $createCustomerDiscountService;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['input']) || !is_array($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }

        $customerdiscountData = $this->createCustomerDiscountService->execute($args['input']);

        return ['customer_discount' => [
            'discount_id' => $customerdiscountData->getDiscountId(),
            'customer_id' => $customerdiscountData->getCustomerId(),
            'value' => $customerdiscountData->getValue()]
        ];
    }
}
