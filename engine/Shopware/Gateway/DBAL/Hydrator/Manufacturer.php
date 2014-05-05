<?php

namespace Shopware\Gateway\DBAL\Hydrator;

use Shopware\Struct as Struct;

class Manufacturer extends Hydrator
{
    /**
     * @var Attribute
     */
    private $attributeHydrator;

    function __construct(Attribute $attributeHydrator)
    {
        $this->attributeHydrator = $attributeHydrator;
    }

    /**
     * @param array $data
     * @return Struct\Product\Manufacturer
     */
    public function hydrate(array $data)
    {
        $manufacturer = new Struct\Product\Manufacturer();

        $this->assignData($manufacturer, $data);

        if (isset($data['__manufacturerAttribute_id'])) {
            $this->assignAttribute($manufacturer, $data);
        }

        return $manufacturer;
    }

    public function assignData(Struct\Product\Manufacturer $manufacturer, array $data)
    {
        if (isset($data['id'])) {
            $manufacturer->setId(intval($data['id']));
        }

        if (isset($data['name'])) {
            $manufacturer->setName($data['name']);
        }

        if (isset($data['description'])) {
            $manufacturer->setDescription($data['description']);
        }

        if (isset($data['meta_title'])) {
            $manufacturer->setMetaTitle($data['meta_title']);
        }

        if (isset($data['meta_description'])) {
            $manufacturer->setMetaDescription($data['meta_description']);
        }

        if (isset($data['meta_keywords'])) {
            $manufacturer->setMetaKeywords($data['meta_keywords']);
        }

        if (isset($data['link'])) {
            $manufacturer->setLink($data['link']);
        }

        if (isset($data['img'])) {
            $manufacturer->setCoverFile($data['img']);
        }
    }

    private function assignAttribute(Struct\Product\Manufacturer $manufacturer, array $data)
    {
        $attribute = $this->attributeHydrator->hydrate(
            $this->extractFields('__manufacturerAttribute_', $data)
        );

        $manufacturer->addAttribute('core', $attribute);
    }
}