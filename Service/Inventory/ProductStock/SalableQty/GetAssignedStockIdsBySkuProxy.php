<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Vendiro\Service\Inventory\ProductStock\SalableQty;

use Magento\Framework\ObjectManagerInterface;
use Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySku;
use Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySkuFactory;

class GetAssignedStockIdsBySkuProxy
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var GetAssignedStockIdsBySku */
    private $subject;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * GetAssignedStockIdsBySku only exists in Magento 2.3.
     * In order to make the extension compatible in Magento 2.2, the ObjectManager is needed in this proxy in order to
     * load the Class properly, as Magento will fail in certain cases otherwise, such as on di:compile.
     * Classes using this proxy will check whether the Class can be successfully created or not.
     *
     * @return mixed
     */
    private function getSubject()
    {
        if (!$this->subject && class_exists(GetAssignedStockIdsBySku::class)) {
            // @codingStandardsIgnoreLine
            $this->subject = $this->objectManager->get(GetAssignedStockIdsBySkuFactory::class);
        }

        return $this->subject;
    }

    /**
     * @param array $data
     *
     * @return GetAssignedStockIdsBySku|null
     */
    public function create(array $data = [])
    {
        $subject = $this->getSubject();

        if ($subject === null) {
            return null;
        }

        return $subject->create($data);
    }
}
