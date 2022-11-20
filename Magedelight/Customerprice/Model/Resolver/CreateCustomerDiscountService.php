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

namespace Magedelight\Customerprice\Model\Resolver;

use Magedelight\Customerprice\Api\Data\CustomerdiscountInterface;
use Magedelight\Customerprice\Api\Data\CustomerdiscountInterfaceFactory;
use Magedelight\Customerprice\Api\CustomerdiscountRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class CreateCustomerDiscountService
{
    
     /**
      * @var DataObjectHelper
      */
    private $dataObjectHelper;

    /**
     * @var CustomerdiscountRepositoryInterface
     */
    private $customerdiscountRepository;

    /**
     * @var customerdiscountInterfaceFactory
     */
    private $customerdiscountInterfaceFactory;

    /**
     *
     * @param DataObjectHelper $dataObjectHelper
     * @param CustomerdiscountRepositoryInterface $customerdiscountRepository
     * @param CustomerdiscountInterfaceFactory $customerdiscountInterfaceFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        CustomerdiscountRepositoryInterface $customerdiscountRepository,
        CustomerdiscountInterfaceFactory $customerdiscountInterfaceFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerdiscountRepository = $customerdiscountRepository;
        $this->customerdiscountInterfaceFactory = $customerdiscountInterfaceFactory;
    }

    /**
     * Creates new store
     * @param array $data
     * @return CustomerpriceInterface
     * @throws GraphQlInputException
     */
    public function execute(array $data): CustomerdiscountInterface
    {
        try {
            $customerdiscount = $this->createCustomerDiscount($data);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $customerdiscount;
    }

    /**
     * Creates  customer discount
     *
     * @param array $data
     * @return CustomerdiscountInterface
     * @throws LocalizedException
     */
    private function createCustomerDiscount(array $data): CustomerdiscountInterface
    {
        /** @var StorelocatorInterface $storeDataObject */
        $customerdiscountDataObject = $this->customerdiscountInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $customerdiscountDataObject,
            $data,
            CustomerpriceInterface::class
        );

        $this->customerdiscountRepository->save($customerdiscountDataObject);

        return $customerdiscountDataObject;
    }
}
