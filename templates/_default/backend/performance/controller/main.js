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
 * @package    Shopware_Config
 * @subpackage Config
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */

/**
 * Shopware Controller - Performance backend module
 *
 * todo@all: Documentation
 */
//{block name="backend/performance/controller/main"}
Ext.define('Shopware.apps.Performance.controller.Main', {
    extend: 'Enlight.app.Controller',

    refs: [
        { ref: 'info', selector: 'performance-tabs-cache-info' },
        { ref: 'settings', selector: 'performance-tabs-settings-main' },
        { ref: 'navigation', selector: 'performance-tabs-settings-navigation' }
    ],

    /**
     * The main window instance
     * @object
     */
    mainWindow: null,

    /**
     * Called by the SubApplication to create and show the window
     * 
     */
    run: function() {
        var me = this;

        me.mainWindow = me.subApplication.getView('main.Window').create({
				treeStore: me.getStore('Tree')
		}).show();

        me.infoStore = me.getStore('Info').load(function() {
            me.getInfo().bindStore(me.infoStore);
        });

		// Loads the main settings store and injects it into the settings form
        me.getController('Settings').loadConfigStore();
    }


});
//{/block}
