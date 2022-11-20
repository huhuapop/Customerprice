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

namespace Magedelight\Customerprice\Model\Calculation\Calculator;

use Magedelight\Customerprice\Model\Calculation\Calculator\AbstractCalculator;
use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magedelight\Customerprice\Helper\Data as Helper;
use Magento\Framework\App\Http\Context;
use Magento\Customer\Model\SessionFactory;
use Magedelight\Customerprice\Model\Discount as DiscountModel;
use Magedelight\Customerprice\Model\Categoryprice as CategoryDiscountModel;
use Magento\Sales\Model\AdminOrder\Create  as OrderCreate;
use Magedelight\Customerprice\Model\Customer\Context as CustomerContex;

class GlobalDiscountCalculator extends AbstractCalculator
{

    /**
     * @var GstHelper
     */
    protected $helper;

    /**
     * AbstractCalculation constructor.
     *
     * @param GstHelper $helper
     */
    public function __construct(
        Context $customer,
        SessionFactory $customerSession,
        DiscountModel $discountModel,
        Helper $helper,
        CategoryDiscountModel $categoryDiscountModel,
        OrderCreate $orderCreate,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->customer = $customer;
        $this->customerSession = $customerSession;
        $this->discountModel = $discountModel;
        $this->categoryDiscountModel = $categoryDiscountModel;
        $this->orderCreate = $orderCreate;
        $this->state = $state;
        $this->httpContext = $httpContext;
        parent::__construct($helper);
    }
    /**
     * {@inheritdoc}
     */
    public function calculate($price, $product)
    {
        if ($this->helper->isEnabled()) {
            $discount = 0.00;
            $productId = $product->getId();
            if ($this->state->getAreaCode() == 'adminhtml') {
                $customerId = $this->orderCreate->getQuote()->getCustomer()->getId();
            } else {
                $customerId = $this->getCustomerId();
            }
            if ($customerId) {
                $discount = $this->calculateMaximumDiscount($customerId, $product->getCategoryIds());
                if ($discount > 0.00) {
                    $resultPrice = $price - (($price * $discount)/100);
                    return $resultPrice;
                }
            }
        }
        return null;
    }

    private function getDiscountByCustomerId($customerId)
    {
        $discount = $this->discountModel->getCollection()
                ->addFieldToFilter('customer_id', ['eq' => $customerId])
                ->getFirstItem();
        if (!empty($discount)) {
            return $discount->getValue();
        } else {
            return (int)0;
        }
    }
    
    private function getCategoryDiscount($customerId, $categoryIds)
    {
        $categoryDiscounts = $this->categoryDiscountModel
                ->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('category_id', ['in' => $categoryIds])
                ->addFieldToFilter('customer_id', ['eq' => $customerId]);
        foreach ($categoryDiscounts as $categoryDiscount) {
            $discountArray[] = $categoryDiscount->getDiscount();
        }
        if (!empty($discountArray)) {
            $maxCategoryDiscount = max($discountArray);
            return $maxCategoryDiscount;
        } else {
            return (int)0;
        }
    }
    
    private function calculateMaximumDiscount($customerId, $categoryIds)
    {
        $categoryDiscount = $this->getCategoryDiscount($customerId, $categoryIds);
        $globalDiscount = $this->getDiscountByCustomerId($customerId);
        return max($categoryDiscount, $globalDiscount);
    }
    
    /**
     * @return mixed|null
     */
    private function getCustomerId()
    {
        return $this->customerSession->create()->getCustomerId();
    }
}
