<?php

namespace Shopware\Service\Core;

use Shopware\Struct;
use Shopware\Service;
use Shopware\Gateway;

class SimilarProducts implements Service\SimilarProducts
{
    /**
     * @var Gateway\SimilarProducts
     */
    private $gateway;

    /**
     * @var Service\ListProduct
     */
    private $listProductService;

    /**
     * @param Gateway\SimilarProducts $gateway
     * @param Service\ListProduct $listProductService
     */
    function __construct(
        Gateway\SimilarProducts $gateway,
        Service\ListProduct $listProductService
    ) {
        $this->gateway = $gateway;
        $this->listProductService = $listProductService;
    }

    /**
     * Selects all similar products for the provided product.
     *
     * The relation between the products are selected over the \Shopware\Gateway\SimilarProducts class.
     * After the relation is selected, the \Shopware\Service\ListProduct is used to load
     * the whole product data for the relations.
     *
     * If the product has no manually assigned similar products, the function selects the fallback similar products
     * over the same category.
     *
     * To get detailed information about the selection conditions, structure and content of the returned object,
     * please refer to the linked classes.
     *
     * @see \Shopware\Service\ListProduct::get()
     *
     * @param Struct\ListProduct $product
     * @param Struct\Context $context
     * @return Struct\ListProduct[] Indexed by the product order number.
     */
    public function get(Struct\ListProduct $product, Struct\Context $context)
    {
        $similar = $this->getList(array($product), $context);
        return array_shift($similar);
    }

    /**
     * @see Shopware\Service\SimilarProducts::get()
     *
     * @param Struct\ListProduct[] $products
     * @param Struct\Context $context
     * @return array Indexed with the product number, the values are a list of ListProduct structs.
     */
    public function getList(array $products, Struct\Context $context)
    {
        /**
         * returns an array which is associated with the different product numbers.
         * Each array contains a list of product numbers which are related to the reference product.
         */
        $numbers = $this->gateway->getList($products);

        //loads the list product data for the selected numbers.
        //all numbers are joined in the extractNumbers function to prevent that a product will be
        //loaded multiple times
        $listProducts = $this->listProductService->getList(
            $this->extractNumbers($numbers),
            $context
        );

        $result = array();
        $fallback = array();

        foreach ($products as $product) {
            if (!isset($numbers[$product->getId()])) {
                $fallback[$product->getNumber()] = $product;
                continue;
            }

            $result[$product->getNumber()] = $this->getProductsByNumbers(
                $listProducts,
                $numbers[$product->getId()]
            );
        }

        if (empty($fallback)) {
            return $result;
        }

        $fallback = $this->gateway->getByListCategory($fallback, $context);

        //loads the list product data for the selected numbers.
        //all numbers are joined in the extractNumbers function to prevent that a product will be
        //loaded multiple times
        $listProducts = $this->listProductService->getList(
            $this->extractNumbers($fallback),
            $context
        );

        $fallbackResult = array();
        foreach ($products as $product) {
            $fallbackResult[$product->getNumber()] = $this->getProductsByNumbers(
                $listProducts,
                $fallback[$product->getId()]
            );
        }

        return array_merge($result, $fallbackResult);
    }

    /**
     * @param Struct\ListProduct[] $products
     * @param array $numbers
     * @return Struct\ListProduct[]
     */
    private function getProductsByNumbers(array $products, array $numbers)
    {
        $result = array();

        foreach ($products as $product) {
            if (in_array($product->getNumber(), $numbers)) {
                $result[$product->getNumber()] = $product;
            }
        }
        return $result;
    }

    /**
     * @param $numbers
     * @return array
     */
    private function extractNumbers($numbers)
    {
        //collect all numbers to send a single list product request.
        $related = array();
        foreach ($numbers as $value) {
            $related = array_merge($related, $value);
        }

        //filter duplicate numbers to prevent duplicate data requests and iterations.
        $unique = array_unique($related);

        return array_values($unique);
    }
}
