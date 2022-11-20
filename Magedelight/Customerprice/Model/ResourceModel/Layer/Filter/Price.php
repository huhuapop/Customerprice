<?php
namespace Magedelight\Customerprice\Model\ResourceModel\Layer\Filter;

use Magento\Framework\App\Http\Context;

class Price extends \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magedelight\Customerprice\Model\Layer\Resolver $layerResolver,
        \Magento\Customer\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $eventManager, $layerResolver, $session, $storeManager, $connectionName);
    }
}
