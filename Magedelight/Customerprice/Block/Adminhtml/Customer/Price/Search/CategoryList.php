<?php
/*
* Magedelight
* Copyright (C) 2017 Magedelight <info@Magedelight.com>
*
* @category Magedelight
* @package Magedelight_Customerprice
* @copyright Copyright (c) 2017 Mage Delight (http://www.Magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@Magedelight.com>
*/

namespace Magedelight\Customerprice\Block\Adminhtml\Customer\Price\Search;

class CategoryList extends \Magento\Catalog\Block\Adminhtml\Category\Tree
{
    protected $_buttonList;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList,
        array $data = []
    ) {
    
        $this->_buttonList = $buttonList;
        parent::__construct(
            $context,
            $categoryTree,
            $registry,
            $categoryFactory,
            $jsonEncoder,
            $resourceHelper,
            $backendSession,
            $data
        );
    }

    public function getFrontUrl($storeId = null)
    {
        return $this->_storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\Url::URL_TYPE_WEB);
    }

    /**
     * @param bool|null $expanded
     * @return string
     */
    public function getLoadTreeUrl($expanded = null)
    {
        $params = ['_current' => true, 'id' => null, 'store' => null];
        if ($expanded === null && $this->_backendSession->getIsTreeWasExpanded() || $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('catalog/category/categoriesJson', $params);
    }
}
