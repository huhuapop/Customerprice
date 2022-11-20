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

class Customerprice
{
    protected $customerprice;

    public function __construct(
        \Magedelight\Customerprice\Model\CustomerpriceFactory $customerprice
    ) {
        $this->customerprice  = $customerprice;
    }
    /**
     * @params int $customerpriceid
     * this function return all the word of the day by id
     **/
    public function getcustomerPrice()
    {
        try {
            $collection = $this->customerprice->create()->getCollection();
            $collection->setOrder('customerprice_id', 'ASC');
            $customerData = $collection->getData();

        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $customerData;
    }
}
