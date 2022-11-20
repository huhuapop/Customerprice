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

namespace Magedelight\Customerprice\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magedelight\Customerprice\Api\CustomerdiscountRepositoryInterface;
use Magedelight\Customerprice\Model\Discount;

class DeleteCustomerdiscount implements ResolverInterface
{
    /**
     * @var CustomerdiscountRepositoryInterface
     */
    private $customerdiscountRepository;
    
    /**
     * @var CustomerdiscountRepositoryInterface
     */
    private $customerdiscount;

    
     /**
      *
      * @param CustomerpriceRepositoryInterface $customerpriceRepository
      */
    public function __construct(
        CustomerdiscountRepositoryInterface $customerdiscountRepository,
        Discount $customerdiscount
    ) {
        $this->customerdiscountRepository = $customerdiscountRepository;
        $this->customerdiscount = $customerdiscount;
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
        if (!isset($args['discount_id'])) {
            throw new GraphQlInputException(__('Specify the "discount_id" value.'));
        }

        $id = $this->customerdiscount->load($args['discount_id']);
        ;
        if (empty($id->getDiscountId())) {
            throw new GraphQlNoSuchEntityException(
                __('Could not find a customer discount with id : %1', $args['discount_id'])
            );
        }

        return ['result' => $this->customerdiscountRepository->delete($args['discount_id'])];
    }
}
