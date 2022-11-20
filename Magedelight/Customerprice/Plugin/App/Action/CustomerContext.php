<?php
namespace Magedelight\Customerprice\Plugin\App\Action;

/**
 * Plugin on \Magento\Framework\App\Http\Context
 */
class CustomerContext
{
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }
    /**
     * \Magento\Framework\App\Http\Context::getVaryString is used by Magento to retrieve unique identifier for selected context,
     * so this is a best place to declare custom context variables
     */
    function beforeGetVaryString(\Magento\Framework\App\Http\Context $subject)
    {
        $customerId = 0;
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerData()->getId();
        }

        $defaultContext = 0;
        $subject->setValue('CONTEXT_CUSTOMER_ID', $customerId, false);
    }
}
