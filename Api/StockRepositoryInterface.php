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

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\Vendiro\Api\Data\StockInterface;

//@codingStandardsIgnoreFile
interface StockRepositoryInterface
{
    /**
     * Save a Vendiro stock to the queue
     *
     * @api
     * @param StockInterface $stock
     * @return StockInterface
     *
     * @throws CouldNotSaveException
     */
    public function save(StockInterface $stock);

    /**
     * Save multiple Vendiro stocks to the queue simultaneous
     *
     * @api
     * @param array $data
     * @return int
     *
     * @throws LocalizedException
     */
    public function insertMultiple(array $data);

    /**
     * Update multiple Vendiro stocks to the queue simultaneous
     *
     * @api
     * @param array  $data
     * @param string $condition
     * @return int
     *
     * @throws LocalizedException
     */
    public function updateMultiple($data, $condition = '');

    /**
     * @param array $skus
     *
     * @return int
     * @throws LocalizedException
     */
    public function deleteMultipleBySku($skus);

    /**
     * Return a specific Vendiro stock.
     *
     * @api
     * @param int $entityId
     * @return StockInterface
     *
     * @throws NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * Return a specific Vendiro stock.
     *
     * @api
     * @param string $sku
     * @return StockInterface
     *
     * @throws NoSuchEntityException
     */
    public function getBySku($sku);

    /**
     * @return array
     */
    public function fetchExistingSkus();

    /**
     * Get all Vendiro stock where the status is a forced update
     *
     * @return StockInterface[]|array|null
     */
    public function getForcedStock();

    /**
     * Get all Vendiro stock where the status is new
     *
     * @return StockInterface[]|array|null
     */
    public function getNewStock();

    /**
     * Delete a specific Vendiro stock.
     *
     * @api
     * @param StockInterface $stock
     * @return bool
     *
     * @throws CouldNotDeleteException
     */
    public function delete(StockInterface $stock);

    /**
     * Delete a Vendiro stock by Id.
     *
     * @api
     * @param int $entityId
     * @return bool
     *
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById($entityId);

    /**
     * Create a Vendiro stock.
     *
     * @api
     * @param array $data
     * @return StockInterface
     */
    public function create(array $data = []);
}
