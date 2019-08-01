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
namespace TIG\Vendiro\Service\Order;

use TIG\Vendiro\Api\Data\OrderInterface;
use TIG\Vendiro\Api\OrderRepositoryInterface;
use TIG\Vendiro\Exception as VendiroException;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;

class Import
{
    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var ApiStatusManager */
    private $apiStatusManager;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var Create */
    private $createOrder;

    /**
     * @param ApiConfiguration         $apiConfiguration
     * @param ApiStatusManager         $apiStatusManager
     * @param OrderRepositoryInterface $orderRepository
     * @param Create                   $createOrder
     */
    public function __construct(
        ApiConfiguration $apiConfiguration,
        ApiStatusManager $apiStatusManager,
        OrderRepositoryInterface $orderRepository,
        Create $createOrder
    ) {
        $this->apiConfiguration = $apiConfiguration;
        $this->apiStatusManager = $apiStatusManager;
        $this->orderRepository = $orderRepository;
        $this->createOrder = $createOrder;
    }

    public function importToMagento()
    {
        if (!$this->apiConfiguration->canImportOrders()) {
            return;
        }

        $orders = $this->orderRepository->getNewOrders();

        foreach ($orders as $order) {
            $this->createOrder($order);
        }
    }

    /**
     * @param OrderInterface $order
     */
    private function createOrder($order)
    {
        $vendiroId = $order->getVendiroId();
        $vendiroOrder = $this->apiStatusManager->getOrders($vendiroId);
        $newOrderId = null;

        try {
            $newOrderId = $this->createOrder->execute($vendiroOrder);

            $this->apiStatusManager->acceptOrder($vendiroId, $newOrderId);
        } catch (VendiroException $exception) {
            $this->apiStatusManager->rejectOrder($vendiroId, $exception->getMessage());
        }

        if ($newOrderId) {
            $order->setOrderId($newOrderId);
            $this->orderRepository->save($order);
        }
    }
}
