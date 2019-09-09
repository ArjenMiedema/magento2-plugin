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
namespace TIG\Vendiro\Api;

use TIG\Vendiro\Api\Data\StockInterface;

interface StockRepositoryInterface
{
    /**
     * Save a Vendiro stock to the queue
     *
     * @api
     * @param StockInterface $stock
     * @return StockInterface
     */
    public function save(StockInterface $stock);

    /**
     * Return a specific Vendiro stock.
     *
     * @api
     * @param int $entityId
     * @return StockInterface
     */
    public function getById($entityId);

    /**
     * Delete a specific Vendiro stock.
     *
     * @api
     * @param StockInterface $stock
     * @return bool
     */
    public function delete(StockInterface $stock);

    /**
     * Delete a Vendiro stock by Id.
     *
     * @api
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * Create a Vendiro stock.
     *
     * @api
     *
     * @param array $data
     * @return StockInterface
     */
    public function create(array $data = []);
}
