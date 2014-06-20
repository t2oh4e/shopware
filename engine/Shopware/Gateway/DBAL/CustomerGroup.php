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

namespace Shopware\Gateway\DBAL;

use Shopware\Components\Model\ModelManager;
use Shopware\Gateway\DBAL\Hydrator as Hydrator;

/**
 * @package Shopware\Gateway\DBAL
 */
class CustomerGroup implements \Shopware\Gateway\CustomerGroup
{
    /**
     * @var \Shopware\Gateway\DBAL\Hydrator\CustomerGroup
     */
    private $customerGroupHydrator;

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
     * @param Hydrator\CustomerGroup $customerGroupHydrator
     */
    function __construct(
        ModelManager $entityManager,
        FieldHelper $fieldHelper,
        Hydrator\CustomerGroup $customerGroupHydrator
    ) {
        $this->customerGroupHydrator = $customerGroupHydrator;
        $this->entityManager = $entityManager;
        $this->fieldHelper = $fieldHelper;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        $groups = $this->getList(array($key));

        return array_shift($groups);
    }

    /**
     * @inheritdoc
     */
    public function getList(array $keys)
    {
        $query = $this->entityManager->getDBALQueryBuilder();
        $query->select($this->fieldHelper->getCustomerGroupFields());

        $query->from('s_core_customergroups', 'customerGroup')
            ->leftJoin(
                'customerGroup',
                's_core_customergroups_attributes',
                'customerGroupAttribute',
                'customerGroupAttribute.customerGroupID = customerGroup.id'
            );

        $query->where('customerGroup.groupkey IN (:keys)')
            ->setParameter(':keys', implode(',', $keys));

        /**@var $statement \Doctrine\DBAL\Driver\ResultStatement */
        $statement = $query->execute();

        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $customerGroups = array();
        foreach ($data as $group) {
            $key = $group['groupkey'];

            $customerGroups[$key] = $this->customerGroupHydrator->hydrate($group);
        }

        return $customerGroups;
    }

}
