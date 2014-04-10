<?php

namespace Shopware\Struct;

/**
 * @package Shopware\Struct
 */
class Unit
{
    /**
     * Unique identifier of the struct.
     *
     * @var int
     */
    private $id;

    /**
     * Contains a name of the unit.
     * This value will be translated over the translation service.
     *
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $unit;

    /**
     * Contains the numeric value of the purchase unit.
     * Used to calculate the unit price of the product.
     *
     * Example:
     *  reference unit equals 1.0 liter
     *  purchase unit  equals 0.7 liter
     *
     *  product price       7,- €
     *  reference price    10,- €
     *
     * @var float
     */
    private $purchaseUnit;

    /**
     * Contains the numeric value of the reference unit.
     * Used to calculate the unit price of the product.
     *
     * Example:
     *  reference unit equals 1.0 liter
     *  purchase unit  equals 0.7 liter
     *  product price       7,- €
     *  reference price    10,- €
     *
     * @var float
     */
    private $referenceUnit;

    /**
     * Alphanumeric description how the product
     * units are delivered.
     *
     * Example: bottle, box, pair
     *
     * @var string
     */
    private $packUnit;

    /**
     * Minimal purchase value for the product.
     * Used as minimum value to add a product to the basket.
     *
     * @var float
     */
    private $minPurchase;

    /**
     * Maximal purchase value for the product.
     * Used as maximum value to add a product to the basket.
     *
     * @var float
     */
    private $maxPurchase;

    /**
     * Numeric step value for the purchase.
     * This value is used to generate the quantity combo box
     * on the product detail page and in the basket.
     *
     * @var float
     */
    private $purchaseStep;


    /**
     * @param int $id
     *
     */
    public function setId($id)
    {
        $this->id = $id;

    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $unit
     *
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }


    /**
     * @return string
     */
    public function getPackUnit()
    {
        return $this->packUnit;
    }

    /**
     * @param string $packUnit
     */
    public function setPackUnit($packUnit)
    {
        $this->packUnit = $packUnit;

    }

    /**
     * @return float
     */
    public function getPurchaseUnit()
    {
        return $this->purchaseUnit;
    }

    /**
     * @param float $purchaseUnit
     */
    public function setPurchaseUnit($purchaseUnit)
    {
        $this->purchaseUnit = $purchaseUnit;
    }

    /**
     * @return float
     */
    public function getReferenceUnit()
    {
        return $this->referenceUnit;
    }

    /**
     * @param float $referenceUnit
     */
    public function setReferenceUnit($referenceUnit)
    {
        $this->referenceUnit = $referenceUnit;
    }

    /**
     * @param float $maxPurchase
     */
    public function setMaxPurchase($maxPurchase)
    {
        $this->maxPurchase = $maxPurchase;
    }

    /**
     * @return float
     */
    public function getMaxPurchase()
    {
        return $this->maxPurchase;
    }

    /**
     * @param float $minPurchase
     */
    public function setMinPurchase($minPurchase)
    {
        $this->minPurchase = $minPurchase;
    }

    /**
     * @return float
     */
    public function getMinPurchase()
    {
        return $this->minPurchase;
    }


    /**
     * @param float $purchaseStep
     */
    public function setPurchaseStep($purchaseStep)
    {
        $this->purchaseStep = $purchaseStep;
    }

    /**
     * @return float
     */
    public function getPurchaseStep()
    {
        return $this->purchaseStep;
    }


}