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
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Model\Config\Provider\QueueStatus;

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

    /** @var Log */
    private $logger;

    /**
     * @param ApiConfiguration         $apiConfiguration
     * @param ApiStatusManager         $apiStatusManager
     * @param OrderRepositoryInterface $orderRepository
     * @param Create                   $createOrder
     * @param Log                  $logger
     */
    public function __construct(
        ApiConfiguration $apiConfiguration,
        ApiStatusManager $apiStatusManager,
        OrderRepositoryInterface $orderRepository,
        Create $createOrder,
        Log $logger
    ) {
        $this->apiConfiguration = $apiConfiguration;
        $this->apiStatusManager = $apiStatusManager;
        $this->orderRepository = $orderRepository;
        $this->createOrder = $createOrder;
        $this->logger = $logger;
    }

    public function importToMagento()
    {
        if (!$this->apiConfiguration->canImportOrders()) {
            return;
        }

        $orders = $this->apiStatusManager->getOrders();

        if (isset($orders['message'])) {
            return;
        }

        $orderIds = array_column($orders, 'id');
        $valuesToSkip = $this->orderRepository->getAlreadyInsertedOrders($orderIds);

        if (isset($orders) && !is_array($orders)) {
            $this->createOrder($orders, $valuesToSkip);
            return;
        }

        foreach ($orders as $order) {
            $this->createOrder($order, $valuesToSkip);
        }
    }

    /**
     * @param $order
     */
    private function saveVendiroOrder($order)
    {
        try {
            $this->orderRepository->save($order);
        } catch (\Exception $exception) {
            $errorMessage = 'Import on Vendiro order #' . $order['id'] . ' went wrong: ' . $exception->getMessage();
            $this->logger->critical($errorMessage);
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @param                $valuesToSkip
     *
     * @return void
     */
    private function createOrder($order, $valuesToSkip)
    {
        $vendiroId = $order['id'];
        $newOrderId = null;

        if (in_array($order['id'], array_keys($valuesToSkip))) {
            $this->apiStatusManager->acceptOrder($valuesToSkip[$order['id']]['order_id'], $newOrderId);
            return;
        }

        try {
            $newOrderId = $this->createOrder->execute($order);
            $this->apiStatusManager->acceptOrder($vendiroId, $newOrderId);
        } catch (VendiroException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
            $this->apiStatusManager->rejectOrder($vendiroId, $exception->getMessage());
        }

        if ($newOrderId) {
            $this->saveOrder($newOrderId, $order);
        }
    }

    public function saveOrder($newOrderId, $order)
    {
        $vendiroOrder = $this->orderRepository->create();
        $vendiroOrder->setOrderId($newOrderId);
        $vendiroOrder->setVendiroId($order['id']);
        $vendiroOrder->setMarketplaceReference($order['marketplace']['reference']);
        $vendiroOrder->setStatus(QueueStatus::QUEUE_STATUS_IMPORTED);
        $this->saveVendiroOrder($vendiroOrder);
    }
}
