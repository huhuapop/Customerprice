<?php
/**
 * Magedelight
 * Copyright (C) 2018 Magedelight <info@Magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Customerprice
 * @copyright Copyright (c) 2018 Mage Delight (http://www.Magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@Magedelight.com>
 */

namespace Magedelight\Customerprice\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magedelight\Customerprice\Helper\Data;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\Request\Http as Request;

class RefreshCache implements ObserverInterface
{
    /**
     * @var AbstractRestrictHelper
     */
    private $restrictHelper;
    
    /**
     * @var Manager
     */
    private $cacheManager;
    private $helper;
    
    /**
     * @param AbstractRestrictHelper $restrictHelper
     * @param Manager $cacheManager
     */
    public function __construct(
        Data $restrictHelper,
        Manager $cacheManager,
        Request $request,
        Data $helper
    ) {
        $this->restrictHelper = $restrictHelper;
        $this->cacheManager = $cacheManager;
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */

    public function execute(Observer $observer)
    {
        if ($this->helper->isEnabled()) 
        {
            $types = $this->cacheManager->getAvailableTypes();
            $cac = array();
            foreach($types as $t)
            {
                if($t == 'block_html')
                {
                    $cac[] = $t;
                }
            }
            $this->cacheManager->clean($cac);
        }
    }
}
