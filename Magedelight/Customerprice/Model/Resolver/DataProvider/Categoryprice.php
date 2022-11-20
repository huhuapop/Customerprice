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

namespace Magedelight\Customerprice\Model\Resolver\DataProvider;

class Categoryprice
{
    
    protected $categoryprice;

    public function __construct(
        \Magedelight\Customerprice\Model\CategorypriceFactory $categoryprice
    ) {
        $this->categoryprice  = $categoryprice;
    }
    /**
     * @params int $categorypriceId
     * this function return all the word of the day by id
     **/
    public function getcategoryPrice()
    {
        try {
            $collection = $this->categoryprice->create()->getCollection();
            $collection->setOrder('categoryprice_id', 'ASC');
            $categoryData = $collection->getData();

        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $categoryData;
    }
}
