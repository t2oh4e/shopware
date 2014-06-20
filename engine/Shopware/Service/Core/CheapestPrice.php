<?php

namespace Shopware\Service\Core;

use Shopware\Struct;
use Shopware\Service;
use Shopware\Gateway;

class CheapestPrice implements Service\CheapestPrice
{
    /**
     * @var Gateway\CheapestPrice
     */
    private $cheapestPriceGateway;

    /**
     * @param Gateway\CheapestPrice $cheapestPriceGateway
     */
    function __construct(Gateway\CheapestPrice $cheapestPriceGateway)
    {
        $this->cheapestPriceGateway = $cheapestPriceGateway;
    }

    /**
     * Returns the cheapest product price for the provided context and product.
     *
     * If the current customer group has no specified prices, the function returns
     * the cheapest product price for the fallback customer group.
     *
     * To get detailed information about the selection conditions, structure and content of the returned object,
     * please refer to the linked classes.
     *
     * @see \Shopware\Gateway\CheapestPrice::get()
     *
     * @param Struct\ListProduct $product
     * @param Struct\Context $context
     * @return Struct\Product\PriceRule
     */
    public function get(Struct\ListProduct $product, Struct\Context $context)
    {
        $cheapestPrices = $this->getList(array($product), $context);

        return array_shift($cheapestPrices);
    }

    /**
     * @see \Shopware\Service\Core\CheapestPrice::get()
     *
     * @param Struct\ListProduct[] $products
     * @param Struct\Context $context
     * @return Struct\Product\PriceRule[] Indexed by product number
     */
    public function getList(array $products, Struct\Context $context)
    {
        $group = $context->getCurrentCustomerGroup();

        $rules = $this->cheapestPriceGateway->getList($products, $context, $group);

        $prices = $this->buildPrices($products, $rules, $group);

        //check if one of the products have no assigned price within the prices variable.
        $fallbackProducts = array_filter(
            $products,
            function (Struct\ListProduct $product) use ($prices) {
                return !array_key_exists($product->getNumber(), $prices);
            }
        );

        if (empty($fallbackProducts)) {
            return $prices;
        }

        //if some product has no price, we have to load the fallback customer group prices for the fallbackProducts.
        $fallbackPrices = $this->cheapestPriceGateway->getList(
            $fallbackProducts,
            $context,
            $context->getFallbackCustomerGroup()
        );

        $fallbackPrices = $this->buildPrices(
            $fallbackProducts,
            $fallbackPrices,
            $context->getFallbackCustomerGroup()
        );

        return array_merge($prices, $fallbackPrices);
    }

    /**
     * Helper function which iterates the products and builds a price array which indexed
     * with the product order number.
     *
     * @param Struct\ListProduct[] $products
     * @param Struct\Product\PriceRule[] $priceRules
     * @param Struct\Customer\Group $group
     * @return array
     */
    private function buildPrices(array $products, array $priceRules, Struct\Customer\Group $group)
    {
        $prices = array();

        foreach ($products as $product) {
            $key = $product->getId();

            if (!array_key_exists($key, $priceRules) || empty($priceRules[$key])) {
                continue;
            }

            /**@var $cheapestPrice Struct\Product\PriceRule */
            $cheapestPrice = $priceRules[$key];

            $cheapestPrice->setCustomerGroup($group);

            $prices[$product->getNumber()] = $cheapestPrice;
        }

        return $prices;
    }
}
