/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
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
 *
 * @category   Shopware
 * @package    Shopware_Cache
 * @subpackage Cache
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */

/**
 * Shopware Application - Performance module
 *
 * todo@all: Documentation
 */
//{block name="backend/performance/application"}
Ext.define('Shopware.apps.Performance', {

    extend: 'Enlight.app.SubApplication',

    /**
     * The name of the module. Used for internal purpose
     * @string
     */
    name:'Shopware.apps.Performance',

    /**
     * Requires controllers for sub-application
     * @array
     */
    controllers: [
        'Main',
        'Settings',
        'Cache',
        'MultiRequest'
    ],


    /**
     * The detail controller knows all form field sets and the detail window component
     * @array
     */
    views:[
        'main.Window',
        'main.Categories',
        'main.MultiRequestDialog',

        'tabs.cache.Main',
        'tabs.cache.Form',
        'tabs.cache.Info',

        'tabs.settings.Main',
        'tabs.settings.Navigation',
        'tabs.settings.fields.Base',
        'tabs.settings.fields.Seo',
        'tabs.settings.fields.Topseller',
        'tabs.settings.fields.Search',
        'tabs.settings.fields.HttpCache',
        'tabs.settings.fields.Categories',

        'tabs.settings.elements.BaseGrid',
        'tabs.settings.elements.CacheTime',
        'tabs.settings.elements.NoCache'
    ],

    /**
     * All required stores are defined here. The detail store contains all data around the customer.
     * The other shops are global stores which used for combo boxes.
     * @array
     */
    stores:[ 'Info', 'Config', 'Tree' ],

    /**
     * All store's required models. The detail store handles the base, billing, shipping and debit model.
     * @array
     */
    models: [
        'Config',
        'KeyValue',
        'HttpCache',
        'TopSeller',
        'Seo',
        'Search',
        'Categories',
        'Tree'
    ],

    bulkLoad: true,
    loadPath: '{url action=load}',

    /**
     * This method will be called when all dependencies are solved and
     * all member controllers, models, views and stores are initialized.
     */
    launch: function() {
        var me = this,
            mainController;

        // In order to support clearing the cache directly, we might
        // have to load the 'direct' controller
        if (me.action) {
            me.getController('Direct');
        } else {
            mainController = me.getController('Main');
            if (!mainController.mainWindow) {
                mainController.run();
            }
            return mainController.mainWindow;
        }

    }
});
//{/block}

