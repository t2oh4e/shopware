<?php
/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Bundle\StoreFrontBundle\Service;

use Shopware\Bundle\StoreFrontBundle\Struct;

/**
 * @category  Shopware
 * @package   Shopware\Bundle\StoreFrontBundle\Service
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
interface ContextServiceInterface
{
    /**
     * The \Shopware\Bundle\StoreFrontBundle\Struct\Context class contains
     * all information about the current state.
     *
     * Requires the following data:
     * - Current shop
     * - Current customer group
     * - Fallback customer group of the current shop
     * - The currency of the shop
     * - Tax rules of the current customer group
     * - Price group discounts of the current customer group
     * - Location data of the current state.
     *
     * Required conditions for the selection:
     * - Use the `shop` service of the di container for the language and current category
     * - Use the `session` service of the di container for the current user data.
     *
     * @return Struct\Context
     */
    public function getContext();

    /**
     * Requires the following data:
     * - Current shop
     * - Current customer group
     * - Fallback customer group of the current shop
     * - The currency of the shop
     *
     * @return Struct\ShopContext
     */
    public function getShopContext();

    /**
     * Requires the following data:
     * - Current shop
     * - Current customer group
     * - Fallback customer group of the current shop
     * - The currency of the shop
     * - Tax rules of the current customer group
     * - Price group discounts of the current customer group
     *
     * @return Struct\ProductContext
     */
    public function getProductContext();

    /**
     * Requires the following data:
     * - Location data of the current state. (area, country, state)
     *
     * @return Struct\LocationContext
     */
    public function getLocationContext();

    public function initializeContext();

    public function initializeShopContext();

    public function initializeLocationContext();

    public function initializeProductContext();

}
