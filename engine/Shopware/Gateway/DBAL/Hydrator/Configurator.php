<?php

namespace Shopware\Gateway\DBAL\Hydrator;

use Shopware\Struct;

class Configurator extends Hydrator
{
    /**
     * @var Attribute
     */
    private $attributeHydrator;

    /**
     * @param Attribute $attributeHydrator
     */
    function __construct(Attribute $attributeHydrator)
    {
        $this->attributeHydrator = $attributeHydrator;
    }


    public function hydrate(array $data, array $selection = array())
    {
        $set = $this->createSet($data[0]);

        $set->setGroups(
            $this->hydrateGroups($data, $selection)
        );

        return $set;

    }

    /**
     * @param array $data
     * @param array $selection
     * @return Struct\Configurator\Group[]
     */
    public function hydrateGroups(array $data, array $selection = array())
    {
        $groups = array();

        foreach ($data as $row) {
            $groupId = $row['__configuratorGroup_id'];

            if ($groups[$groupId]) {
                $group = $groups[$groupId];
            } else {
                $group = $this->createGroup($row);
                $group->setSelected(isset($selection[$groupId]));
                $groups[$groupId] = $group;
            }

            $option = $this->createOption($row);

            $option->setSelected(in_array($option->getId(), $selection));

            $group->addOption($option);
        }
        return array_values($groups);
    }


    private function createSet($data)
    {
        $set = new Struct\Configurator\Set();
        $set->setId((int) $data['__configuratorSet_id']);
        $set->setName($data['__configuratorSet_name']);
        $set->setType($data['__configuratorSet_type']);
        return $set;
    }

    private function createGroup($data)
    {
        $group = new Struct\Configurator\Group();
        $translation = $this->getTranslation(
            $data,
            '__configuratorGroup_translation',
            array('name' => '__configuratorGroup_name', 'description' => '__configuratorGroup_description')
        );
        $data = array_merge($data, $translation);

        $group->setId((int) $data['__configuratorGroup_id']);
        $group->setName($data['__configuratorGroup_name']);
        $group->setDescription($data['__configuratorGroup_description']);
        return $group;
    }

    private function createOption($data)
    {
        $option = new Struct\Configurator\Option();
        $translation = $this->getTranslation(
            $data,
            '__configuratorOption_translation',
            array('name' => '__configuratorOption_name')
        );
        $data = array_merge($data, $translation);

        $option->setId((int) $data['__configuratorOption_id']);
        $option->setName($data['__configuratorOption_name']);
        return $option;
    }

    private function getTranslation($data, $arrayKey, $mapping)
    {
        if (!isset($data[$arrayKey])
            || empty($data[$arrayKey])
        ) {

            return array();
        }

        $translation = unserialize($data[$arrayKey]);

        if (empty($translation)) {
            return array();
        }

        return $this->convertArrayKeys($translation, $mapping);
    }
}
