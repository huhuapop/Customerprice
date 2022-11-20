<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Magedelight\Customerprice\Model\Resolver;

use Magedelight\Customerprice\Api\Data\CategorypriceInterface;
use Magedelight\Customerprice\Api\Data\CategorypriceInterfaceFactory;
use Magedelight\Customerprice\Api\CategorypriceRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class CreateCategoryPriceService
{
    
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CategorypriceRepositoryInterface
     */
    private $categorypriceRepository;

    /**
     * @var categorypriceInterfaceFactory
     */
    private $categorypriceInterfaceFactory;

    /**
     *
     * @param DataObjectHelper $dataObjectHelper
     * @param CustomerpriceRepositoryInterface $customerpriceRepository
     * @param CustomerpriceInterfaceFactory $customerpriceInterfaceFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        CategorypriceRepositoryInterface $categorypriceRepository,
        CategorypriceInterfaceFactory $categorypriceInterfaceFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->categorypriceRepository = $categorypriceRepository;
        $this->categorypriceInterfaceFactory = $categorypriceInterfaceFactory;
    }

    /**
     * Creates new store
     * @param array $data
     * @return CustomerpriceInterface
     * @throws GraphQlInputException
     */
    public function execute(array $data): CategorypriceInterface
    {
        try {
            $categoryprice = $this->createCategoryPrice($data);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $categoryprice;
    }

    /**
     * Creates store
     *
     * @param array $data
     * @return CategorypriceInterface
     * @throws LocalizedException
     */
    private function createCategoryPrice(array $data): CategorypriceInterface
    {
        /** @var CategorypriceInterface $categorypriceDataObject */
        $categorypriceDataObject = $this->categorypriceInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $categorypriceDataObject,
            $data,
            CategorypriceInterface::class
        );
        
        $this->categorypriceRepository->save($categorypriceDataObject);

        return $categorypriceDataObject;
    }
}
