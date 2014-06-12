<?php

namespace Shopware\Gateway\DBAL;

use Doctrine\DBAL\Connection;
use Shopware\Components\Model\ModelManager;
use Shopware\Struct\Context;
use Shopware\Gateway\DBAL\Hydrator;

class Category
{
    /**
     * @var Hydrator\Category
     */
    private $categoryHydrator;

    /**
     * The FieldHelper class is used for the
     * different table column definitions.
     *
     * This class helps to select each time all required
     * table data for the store front.
     *
     * Additionally the field helper reduce the work, to
     * select in a second step the different required
     * attribute tables for a parent table.
     *
     * @var FieldHelper
     */
    private $fieldHelper;

    /**
     * @param ModelManager $entityManager
     * @param FieldHelper $fieldHelper
     * @param Hydrator\Category $categoryHydrator
     */
    function __construct(
        ModelManager $entityManager,
        FieldHelper $fieldHelper,
        Hydrator\Category $categoryHydrator
    ) {
        $this->entityManager = $entityManager;
        $this->categoryHydrator = $categoryHydrator;
        $this->fieldHelper = $fieldHelper;
    }

    /**
     * @param array $ids
     * @param Context $context
     * @return array
     */
    public function getList(array $ids, Context $context)
    {
        $query = $this->entityManager->getDBALQueryBuilder();

        $query->select($this->fieldHelper->getCategoryFields())
            ->addSelect($this->fieldHelper->getMediaFields())
            ->addSelect($this->fieldHelper->getMediaSettingFields())
        ;

        $query->from('s_categories', 'category');

        $query->leftJoin('category', 's_categories_attributes', 'categoryAttribute', 'categoryAttribute.categoryID = category.id')
            ->leftJoin('category', 's_media', 'media', 'media.id = category.mediaID')
            ->leftJoin('media', 's_media_album_settings', 'mediaSettings', 'mediaSettings.albumID = media.albumID')
            ->leftJoin('media', 's_media_attributes', 'mediaAttribute', 'mediaAttribute.mediaID = media.id');

        $query->addOrderBy('category.position');

        $query->where('category.id IN (:categories)');

        $query->setParameter(':categories', $ids, Connection::PARAM_INT_ARRAY);

        /**@var $statement \Doctrine\DBAL\Driver\ResultStatement */
        $statement = $query->execute();

        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $categories = array();
        foreach ($data as $row) {
            $id = $row['__category_id'];

            $categories[$id] = $this->categoryHydrator->hydrate($row);
        }

        return $categories;
    }

}
