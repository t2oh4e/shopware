/**
 * Shopware 5
 * Copyright (c) shopware AG
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

//{namespace name="backend/customer_stream/translation"}
Ext.define('Shopware.apps.CustomerStream.view.conditions.OrderedWithDeliveryCondition', {

    getLabel: function() {
        return '{s name="ordered_with_delivery_condition"}{/s}';
    },

    supports: function(conditionClass) {
        return (conditionClass == 'Shopware\\Bundle\\CustomerSearchBundle\\Condition\\OrderedWithDeliveryCondition');
    },

    create: function(callback) {
        callback(this._create());
    },

    load: function(conditionClass, items, callback) {
        callback(this._create());
    },

    _create: function() {
        var factory = Ext.create('Shopware.attribute.SelectionFactory');

        return {
            title: '{s name="ordered_with_delivery_condition_selection"}{/s}',
            conditionClass: 'Shopware\\Bundle\\CustomerSearchBundle\\Condition\\OrderedWithDeliveryCondition',
            items: [{
                xtype: 'shopware-form-field-dispatch-grid',
                name: 'dispatchIds',
                flex: 1,
                allowSorting: false,
                useSeparator: false,
                allowBlank: false,
                store: factory.createEntitySearchStore("Shopware\\Models\\Dispatch\\Dispatch"),
                searchStore: factory.createEntitySearchStore("Shopware\\Models\\Dispatch\\Dispatch")
            }]
        };
    }
});