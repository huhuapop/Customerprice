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
use Magedelight\Customerprice\Api\CategorypriceRepositoryInterface;
use Magedelight\Customerprice\Model\Categoryprice;

class DeleteCategoryprice implements ResolverInterface
{
    
    /**
     * @var CategorypriceRepositoryInterface
     */
    private $categorypriceRepository;
    
    /**
     * @var Categoryprice
     */

    private $categoryprice;

    /**
     *
     * @param CategorypriceRepositoryInterface $categorypriceRepository
     */
    public function __construct(
        CategorypriceRepositoryInterface $categorypriceRepository,
        Categoryprice $categoryprice
    ) {
        $this->categorypriceRepository = $categorypriceRepository;
        $this->categoryprice = $categoryprice;
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
        if (!isset($args['categoryprice_id'])) {
            throw new GraphQlInputException(__('Specify the "categoryprice_id" value.'));
        }

        //$id = $this->categorypriceRepository->getById($args['categoryprice_id']);
        $id = $this->categoryprice->load($args['categoryprice_id']);
        ;
        if (empty($id->getCustomerId())) {
            throw new GraphQlNoSuchEntityException(
                __('Could not find a category price with id : %1', $args['categoryprice_id'])
            );
        }
        return ['result' => $this->categorypriceRepository->deleteById($args['categoryprice_id'])];
    }
}
