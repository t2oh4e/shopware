<?php

namespace Shopware\Gateway\DBAL\Hydrator;

use Shopware\Struct as Struct;

class Manufacturer extends Hydrator
{
    /**
     * @var Attribute
     */
    private $attributeHydrator;

    private $translationMapping = array(
        'metaTitle' => 'meta_title',
        'metaDescription' => 'meta_description',
        'metaKeywords' => 'meta_keywords',
    );

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
        $translation = $this->getTranslation($data);
        $data = array_merge($data, $translation);

        if (isset($data['__manufacturer_id'])) {
            $manufacturer->setId(intval($data['__manufacturer_id']));
        }

        if (isset($data['__manufacturer_name'])) {
            $manufacturer->setName($data['__manufacturer_name']);
        }

        if (isset($data['__manufacturer_description'])) {
            $manufacturer->setDescription($data['__manufacturer_description']);
        }

        if (isset($data['__manufacturer_meta_title'])) {
            $manufacturer->setMetaTitle($data['__manufacturer_meta_title']);
        }

        if (isset($data['__manufacturer_meta_description'])) {
            $manufacturer->setMetaDescription($data['__manufacturer_meta_description']);
        }

        if (isset($data['__manufacturer_meta_keywords'])) {
            $manufacturer->setMetaKeywords($data['__manufacturer_meta_keywords']);
        }

        if (isset($data['__manufacturer_link'])) {
            $manufacturer->setLink($data['__manufacturer_link']);
        }

        if (isset($data['__manufacturer_img'])) {
            $manufacturer->setCoverFile($data['__manufacturer_img']);
        }
    }

    private function assignAttribute(Struct\Product\Manufacturer $manufacturer, array $data)
    {
        $attribute = $this->attributeHydrator->hydrate(
            $this->extractFields('__manufacturerAttribute_', $data)
        );

        $manufacturer->addAttribute('core', $attribute);
    }

    private function getTranslation($data)
    {
        $translation = array();

        if (!isset($data['__manufacturer_translation'])) {
            return $translation;
        }

        $translation = unserialize($data['__manufacturer_translation']);

        if (empty($translation)) {
            return array();
        }

        return $this->convertArrayKeys(
            $translation,
            $this->translationMapping
        );
    }
}