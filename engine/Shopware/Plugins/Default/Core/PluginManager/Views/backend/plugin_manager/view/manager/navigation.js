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

//{namespace name=backend/plugin_manager/main}
//{block name="backend/plugin_manager/view/manager/navigation"}
Ext.define('Shopware.apps.PluginManager.view.manager.Navigation', {
    extend: 'Ext.container.Container',
    alias: 'widget.plugin-manager-manager-navigation',
    border: 0,
    padding: 10,
    cls: Ext.baseCSSPrefix + 'plugin-manager-navigation',

    /**
     * Snippets for the components bundled in a object.
     * @object
     */
    snippets: {
        my_extensions: '{s name=navigation/headline/my_extensions}My extensions{/s}',
        my_account: '{s name=navigation/headline/my_account}My account{/s}'
    },

    /**
     * Initializes the component
     *
     * @public
     * @constructor
     * @return void
     */
    initComponent: function() {
        var me = this;

        me.registerAdditionalEvents();
        /** {if $storeApiAvailable} */
        me.searchField = me.createSearchfield();
        me.accountView = me.createAccountView();
        /** {/if} */
        me.categoryView = me.createCategoryView();

        me.items = [ /** {if $storeApiAvailable} */ me.searchField, me.accountView, /** {/if} */ me.categoryView ];
        me.callParent(arguments);
    },

    /**
     * Registers additional events for the component.
     *
     * @public
     * @return void
     */
    registerAdditionalEvents: function() {
        var me = this;

        me.addEvents(
            'searchCommunityStore',
            'changeCategory',
            'openAccount',
            'openLicense',
            'openUpdates'
        );
    },

    /**
     * Creates a search field which will be used
     * to search into the community store.
     *
     * @return [array]
     */
    createSearchfield: function() {
        var me = this;

        return Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'communitySearch',
            cls: 'searchfield',
            width: 200,
            emptyText: '{s name=navigation/search/empty}Search in the community store...{/s}',
            listeners: {
                scope: me,
                buffer: 500,
                change: function(field, newValue, oldValue, eOpts) {
                    me.fireEvent('searchCommunityStore', field, newValue, oldValue, eOpts);
                }
            }
        });
    },

    /**
     * Creates the store and the view which are necessary for the category
     * listing.
     *
     * @public
     * @return [object] Ext.view.View
     */
    createCategoryView: function() {
        var me = this;

        me.extensionCategoryStore = Ext.create('Ext.data.Store', {
            fields: [ 'name', 'badge', 'selected', 'requestParam' ],
            data: [
                { name: '{s name=navigation/all_extensions}All extensions{/s}', badge: 0, selected: false, requestParam: null },
                { name: '{s name=navigation/community}Community extensions{/s}', badge: 0, selected: true, requestParam: 'Community' },
                { name: '{s name=navigation/shopware}Shopware extensions{/s}', badge: 0, selected: false, requestParam: 'Default' },
                { name: '{s name=navigation/local}Local extensions{/s}', badge: 0, selected: false, requestParam: 'Local' }
            ]
        });

        return Ext.create('Ext.view.View', {
            store: me.extensionCategoryStore,
            cls: Ext.baseCSSPrefix + 'category-navigation',
            tpl: me.createCategoryViewTemplate(),
            itemSelector: '.clickable',
            listeners: {
                scope: me,
                itemclick: function(view, record, dom, index) {
                    me.fireEvent('changeCategory', view, record, dom, index);
                }
            }
        });
    },

    /**
     * Creates the XTemplate which is used for the category listing.
     *
     * @public
     * @return [object] Ext.XTemplate
     */
    createCategoryViewTemplate: function() {
        var me = this;

        return new Ext.XTemplate(
           '{literal}<div class="outer-container">',
                '<h2 class="headline">' + me.snippets.my_extensions + '</h2>',
                '<ul class="categories">',
                    '<tpl for=".">',
                        '<li>',
                            '<tpl if="selected == true">',
                                '<span data-action="{requestParam}" class="active clickable">{name}</span>',
                            '</tpl>',

                            '<tpl if="selected != true">',
                                '<span class="clickable" data-action="{requestParam}">{name}</span>',
                            '</tpl>',
                        '</li>',
                    '</tpl>',
                '</ul>',
            '</div>{/literal}'
        );
    },

    /**
     * Creates the account view and it's associated store.
     *
     * @public
     * @return [object] Ext.view.View
     */
    createAccountView: function() {
        var me = this, updateCount = 0;

        me.accountCategoryStore = Ext.create('Ext.data.Store', {
            fields: [ 'name', 'badge', 'selected', 'requestParam' ],
            data: [
                { name: '{s name=navigation/open_account}Open account{/s}', badge: 0, selected: false, requestParam: 'openAccount' },
                { name: '{s name=navigation/purchases_licenses}My purchases / licenses{/s}', badge: 0, selected: false, requestParam: 'openLicense' },
                { name: '{s name=navigation/updates}Updates{/s}', badge: updateCount, selected: false, requestParam: 'openUpdates' }
            ]
        });

        me.accountNavigation = Ext.create('Ext.view.View', {
            store: me.accountCategoryStore,
            cls: Ext.baseCSSPrefix + 'account-navigation',
            tpl: me.createAccountViewTemplate(),
            itemSelector: '.clickable',
            listeners: {
                scope: me,
                itemclick: function(view, record, element, index, eOpts) {
                    var event, i, attr;

                    for(i in element.attributes) {
                        attr = element.attributes[i];

                        if(attr.name === 'data-action') {
                            event = attr.value;
                            break;
                        }
                    }

                    if(!event || !event.length) {
                        return false;
                    }

                    me.fireEvent(event, view, record);
                }
            }
        });
        return me.accountNavigation;
    },

    /**
     * Creates the XTemplate which is used for the account listing.
     *
     * @public
     * @return [object] Ext.XTemplate
     */
    createAccountViewTemplate: function() {
        var me = this;

        return new Ext.XTemplate(
           '{literal}<div class="outer-container">',
                '<h2 class="headline">' + me.snippets.my_account + '</h2>',
                '<ul class="categories">',
                    '<tpl for=".">',
                        '<li>',
                            '<tpl if="selected == true">',
                                '<span data-action="{requestParam}" class="active clickable">{name}</span>',
                            '</tpl>',

                            '<tpl if="selected != true">',
                                '<span data-action="{requestParam}" class="clickable">{name}</span>',
                            '</tpl>',

                            '<tpl if="badge &gt; 0">',
                                '<span class="badge">{badge}</span>',
                            '</tpl>',
                        '</li>',
                    '</tpl>',
                '</ul>',
            '</div>{/literal}'
        );
    }
});
//{/block}
