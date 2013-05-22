/**
 * Shopware 4.0
 * Copyright © 2013 shopware AG
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

//{namespace name=backend/performance/main}

//{block name="backend/performance/view/tabs/cache/form"}
Ext.define('Shopware.apps.Performance.view.tabs.cache.Form', {

    extend: 'Ext.form.Panel',
    alias: 'widget.performance-tabs-cache-form',

    title: '{s name=form/title}What areas are supposed to be cleared?{/s}',

    autoScroll: true,
    bodyPadding: 10,

    url: '{url controller=Cache action=clearCache}',
    waitMsg: '{s name=form/wait_message}Cache is cleared ...{/s}',
    waitMsgTarget: true,
    submitEmptyText: false,

    layout: 'column',

    /**
     * Init the component, load items
     */
    initComponent:function () {
        var me = this;

        Ext.applyIf(me, {
            items: me.getItems()
        });

        me.callParent(arguments);
    },

    /**
     * Apply url und wait message on submit
     * @param options
     */
    submit: function(options) {
        var me = this;
            options = options || {};
        Ext.applyIf(options, {
            url: me.url,
            waitMsg: me.waitMsg
        });
        this.form.submit(options);
    },

    /**
     * @return Array
     */
    getItems: function() {
        var me = this;
        return [
            { xtype: 'container',
                columnWidth: '0.5',
                defaults: {
                    labelWidth: 155,
                    anchor: '100%',
                    xtype: 'checkbox',
                    margin: '10 0',
                    hideLabel: true
                },
                padding: '0 20 0 0',
                layout: 'anchor',
                items: [
                    {
                        name: 'cache[config]',
                        boxLabel: '{s name=form/items/config}Templates, settings, snippets, etc.{/s}',
                        supportText: '{s name=form/items/config/support}Lorem ipsum dolor sit{/s}'
                    },
                    {
                        name: 'cache[frontend]',
                        boxLabel: '{s name=form/items/frontend}HttpProxy + Query-Cache (products, categories){/s}',
                        supportText: '{s name=form/items/frontend/support}Lorem ipsum dolor sit{/s}'
                    },
                    {
                        name: 'cache[backend]',
                        boxLabel: '{s name=form/items/backend}Backend cache{/s}',
                        supportText: '{s name=form/items/backend/support}Lorem ipsum dolor sit{/s}'
                    }
                ] },
            { xtype: 'container',
                columnWidth: '0.5',
                defaults: {
                    labelWidth: 155,
                    anchor: '100%',
                    xtype: 'checkbox',
                    margin: '10 0',
                    hideLabel: true
                },
                padding: '0 20 0 0',
                layout: 'anchor',
                items: [
                    {
                        name: 'cache[router]',
                        boxLabel: '{s name=form/items/router}SEO URL cache{/s}',
                        supportText: '{s name=form/items/router/support}Lorem ipsum dolor sit{/s}'
                    },
                    {
                        name: 'cache[search]',
                        boxLabel: '{s name=form/items/search}Intelligent search (index / keywords){/s}',
                        supportText: '{s name=form/items/search/support}Lorem ipsum dolor sit{/s}'
                    },
                    {
                        name: 'cache[proxy]',
                        boxLabel: '{s name=form/items/proxy}Proxy cache (For development purposes){/s}',
                        supportText: '{s name=form/items/proxy/support}Lorem ipsum dolor sit{/s}'
                    }
                ]}
        ];
    }
});
//{/block}
