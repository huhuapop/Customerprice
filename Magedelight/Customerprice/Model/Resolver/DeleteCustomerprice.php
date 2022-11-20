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
use Magedelight\Customerprice\Api\CustomerpriceRepositoryInterface;
use Magedelight\Customerprice\Model\Customerprice;

class DeleteCustomerprice implements ResolverInterface
{
    /**
     * @var CustomerpriceRepositoryInterface
     */
    private $customerpriceRepository;
    
    /**
     * @var CustomerpriceRepositoryInterface
     */
    private $customerprice;

    

    /**
     *
     * @param CustomerpriceRepositoryInterface $customerpriceRepository
     */
    public function __construct(
        CustomerpriceRepositoryInterface $customerpriceRepository,
        Customerprice $customerprice
    ) {
        $this->customerpriceRepository = $customerpriceRepository;
        $this->customerprice = $customerprice;
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
        if (!isset($args['customerprice_id'])) {
            throw new GraphQlInputException(__('Specify the "customerprice_id" value.'));
        }

        //$id = $this->customerpriceRepository->getById($args['customerprice_id']);
        $id = $this->customerprice->load($args['customerprice_id']);
        ;
        if (empty($id->getCustomerId())) {
            throw new GraphQlNoSuchEntityException(
                __('Could not find a customer price with id : %1', $args['customerprice_id'])
            );
        }

        return ['result' => $this->customerpriceRepository->deleteById($args['customerprice_id'])];
    }
}
