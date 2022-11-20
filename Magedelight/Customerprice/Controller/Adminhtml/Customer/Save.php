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

namespace Magedelight\Customerprice\Controller\Adminhtml\Customer;

class Save extends \Magento\Framework\App\Action\Action
{
    protected $_customerSession;
    protected $resultPageFactory;
    protected $_customerFactory;
    protected $resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context            $context
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Framework\View\Result\PageFactory       $resultPageFactory
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_customerFactory = $customerFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        parse_str($params['data'], $options);

        $append = '';
        $success = false;

        $discountValue = $options['product'];

        $customer_id = $options['customer']['customer_id'];

        $discount = $this->getDiscountByCustomerId($customer_id);
        if (!is_null($discount) && $discount->getId()) {
            if (is_numeric($discountValue['discount'])) {
                $discount->setValue($discountValue['discount'])->setId($discount->getId())->save();
            } else {
                $discount->setId($discount->getId())->delete();
            }
        } else {
            if (is_numeric($discountValue['discount'])) {
                $discountModel = $this->_objectManager->create('Magedelight\Customerprice\Model\Discount')
                        ->setData(['customer_id' => $customer_id, 'value' => $discountValue['discount']]);
                try {
                    $discountModel->save();
                } catch (\Exception $e) {
                    //Mage::log($e->getMessage(), false, 'discount-exception.log');
                }
            }
        }

        foreach ($options['option'] as $key => $_options) {
            foreach ($_options as $k => $value) {
                if ($key == 'value') {
                    /* ---Delete--- */
                    if ($value['del'] == 1 && is_int($k)) {
                        $priceCustomerDel = $this->_objectManager->create('Magedelight\Customerprice\Model\Customerprice')
                                ->load($k)
                                ->delete();
                        unset($_options[$k]);
                        $success = true;
                        continue;
                    }

                    /* ---Insert---- */
                    $priceCustomer = $this->_objectManager->create('Magedelight\Customerprice\Model\Customerprice');

                    if (is_int($k)) {
                        $priceCustomer->setId($k);
                    }
                    //die($value['cid']);
                    $newPrice = $value['newprice'];

                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $product = $objectManager->get('Magento\Catalog\Model\Product')->load(trim($value['pid']));

                    $customer = $this->_customerFactory->create()->load($value['cid']);

                    preg_match('/(.*)%/', $newPrice, $matches);
                    if (is_array($matches) && count($matches) > 0) {
                        $newPrice = $product->getPrice() - ($product->getPrice() * ($matches[1] / 100));
                    }
                    try {
                        $priceCustomer->setCustomerId($customer->getId())
                                ->setCustomerName(trim($customer->getName()))
                                ->setCustomerEmail(trim($customer->getEmail()))
                                ->setProductId($value['pid'])
                                ->setProductName(trim($value['pname']))
                                ->setNewPrice($newPrice)
                                ->setLogPrice($value['newprice'])
                                ->setPrice($product->getPrice())
                                ->setQty($value['qty']);

                        if ($value['del'] == 1 && !is_int($k)) {
                            unset($_options[$k]);
                        } else {
                            $priceCustomer->save();
                        }

                        $success = true;
                    } catch (\Exception $e) {
                        $append .= '<div id="messages"><div class="messages"><div class="message message-error error"><div data-ui-id="messages-message-error">'.$e->getMessage().'</div></div></div></div>';
                        $result = ['error' => true, 'message' => $append];
                        $resultJson = $this->resultJsonFactory->create();
                        $resultJson->setData($result);

                        return $resultJson;
                    }
                }
            }
        }

        $items = $this->getOptionValues($options['customer_id']);
        if ($success == true) {
            $append .= '<div id="messages"><div class="messages"><div class="message message-success success"><div data-ui-id="messages-message-success">'.__('Prices saved successfully.').'</div></div></div></div>';
            $result = ['error' => false, 'message' => $append, 'items' => $items];
        }
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($result);

        return $resultJson;
    }

    public function getDiscountByCustomerId($customerId)
    {
        $discount = $this->_objectManager->create('Magedelight\Customerprice\Model\Discount')->getCollection()
                ->addFieldToFilter('customer_id', ['eq' => $customerId])
                ->getFirstItem();
        if ($discount->getId()) {
            return $discount;
        } else {
            return;
        }
    }

    /**
     * @param type $customerId
     *
     * @return string
     */
    public function getOptionValues($customerId)
    {
        $finaldata = [];
        if ($customerId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $optionCollection = $objectManager->create('\Magedelight\Customerprice\Model\Customerprice')
                    ->getCollection()
                    ->addFieldToSelect('*')->addFieldToFilter('customer_id', ['eq' => $customerId])
                    ->setOrder('product_id');

            $finaldata = [];
            $k = 0;
            //$collection = new Varien_Data_Collection();
            $productObj = [];
            foreach ($optionCollection as $key => $option) {
                if (!isset($productObj[$option['product_id']])) {
                    $productObj[$option['product_id']] = $objectManager->get('Magento\Catalog\Model\Product')->load(trim($option['product_id']))->getTypeId();
                }
                //$rowObj = new Varien_Object();
                $finaldata[$key]['id'] = $key;
                $finaldata[$key]['pname'] = htmlspecialchars($option['product_name']);
                $finaldata[$key]['pid'] = $option['product_id'];
                $finaldata[$key]['price'] = $option['price'];
                $finaldata[$key]['newprice'] = $option['log_price'];
                $finaldata[$key]['logprice'] = $option['log_price'];
                $finaldata[$key]['qty'] = $option['qty'];
                $finaldata[$key]['cid'] = $option['customer_id'];
                $finaldata[$key]['css_class'] = '';
                $finaldata[$key]['sign'] = '';
                if ($productObj[$option['product_id']] == 'bundle') {
                    $finaldata[$key]['css_class'] = 'validate-greater-than-zero validate-percents';
                    $finaldata[$key]['sign'] = '%';
                }
                /* $rowObj->setData($finaldata);
                  $collection->addItem($rowObj);
                 */ ++$k;
            }
        }

        return json_encode($finaldata);
    }
}
