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

namespace Magedelight\Customerprice\Block;

class Categoryprice extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Catalog\Block\Product\Context             $context
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param \Magento\Catalog\Model\ProductFactory              $productFactory
     * @param \Magento\Framework\Url\Helper\Data                 $urlHelper
     * @param array                                              $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magedelight\Customerprice\Model\CategorypriceFactory $categoryprice,
        \Magedelight\Customerprice\Helper\Data $helper,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->categoryFactory = $categoryFactory;
        $this->categoryprice = $categoryprice;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getCategories()
    {
        $customerId = $this->customerSession->getId();
        $collections = $this->categoryprice->create()->getCollection()
                ->addFieldToSelect('*')->addFieldToFilter('customer_id', ['eq' => $customerId]);
        return $collections;
    }
    public function getCategory($catId)
    {
        $customerId = $this->customerSession->getId();
        $category = $this->categoryFactory->create()->load($catId);
        return $category;
    }

    public function getmoduleStatus()
    {
        if ($this->helper->isEnabled()) {
            return true;
        }
        return false;
    }
}
