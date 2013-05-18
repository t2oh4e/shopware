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
 * @package    Order
 * @subpackage View
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */

//{namespace name=backend/performance/main}

//{block name="backend/performance/view/tabs/settings/main"}
Ext.define('Shopware.apps.Performance.view.tabs.settings.Main', {

    /**
     * Define that the additional information is an Ext.panel.Panel extension
     * @string
     */
    extend:'Ext.panel.Panel',

    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
     */
    alias:'widget.performance-tabs-settings-main',

	// Title of the panel shown in the tab
    title: '{s name=tabs/settings/title}Settings{/s}',
    
	// Define the layout of the panel to be a border layut
	layout: 'border',
	
    /**
	 * The initComponent template method is an important initialization step for a Component.
     * It is intended to be implemented by each subclass of Ext.Component to provide any needed constructor logic.
     * The initComponent method of the class being created is called first,
     * with each initComponent method up the hierarchy to Ext.Component being called thereafter.
     * This makes it easy to implement and, if needed, override the constructor logic of the Component at any step in the hierarchy.
     * The initComponent method must contain a call to callParent in order to ensure that the parent class' initComponent method is also called.
	 *
	 * @return void
	 */
    initComponent:function () {
        var me = this;

		me.items = me.createItems();
    	
        me.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'shopware-ui',
            cls: 'shopware-toolbar',
            items: me.getButtons()
        }];

        me.callParent(arguments);
    },

	/*
	 * Helper method which creates the items of the panel
	 * @return Array
	 */
	createItems: function() {
		var me = this,
            link = '"http://wiki.shopware.de/Performance-Tipps-Shopware-4_detail_1258.html"',
            warning = '{s name=fieldset/main/warning}Zu jedem Menüpunkt erhalten Sie korrespondierende Informationen in unserem Wiki. Bevor Sie Einstellungen modifizieren, sollten Sie also die Hinweise in unserer Dokumentation beachten!{/s}',
            info = '{s name=fieldset/main/information}<h2>Performance Einstellungen</h2><br>In diesem Bereich können Sie verschiedene Einstellungen vornehmen, die die Performance Ihrer Shopware-Installation betreffen.<br><br>Bitte beachten Sie auch unseren allgemeinen Performance-Guide unter <a target=[0] href=[1]>Performance Tipps Shopware 4</a>{/s}';

        info = Ext.String.format(info, '"_blank"', link);

        me.panel = Ext.create('Ext.form.Panel', {
			region: 'center',
			trackResetOnLoad: true,
		    autoScroll: true,
			items: [
                {
                    xtype: 'panel',
                    border: false,
                    bodyPadding: 20,
                    style: 'font-size: 18px; font-weight: 700; line-height: 20px;',
                    html: '<span style="color: #4d4d4d;">' +  info + '</span><br><br>' + '<p style="color: #ba2323">' + warning + '</p>' },
                {
                	xtype: 'performance-tabs-settings-seo'
           	 	},{
                	xtype: 'performance-tabs-settings-http-cache'
            	},{
                	xtype: 'performance-tabs-settings-search'
            	},{
                	xtype: 'performance-tabs-settings-topseller'
                },{
                	xtype: 'performance-tabs-settings-various'
                },{
                    xtype: 'performance-tabs-settings-customers'
                },{
                	xtype: 'performance-tabs-settings-categories'
        	}]
		});

        me.navigation = Ext.create('Shopware.apps.Performance.view.tabs.settings.Navigation', {
            region: 'west',
            bodyStyle: 'background: #ffffff;'
        });

        return [
            me.navigation,
            me.panel
        ];
	},

    /**
     * @return Array
     */
    getButtons: function() {
        var me = this;

        return ['->', {
            text: '{s name=settings/buttons/save}Save{/s}',
            action: 'save-settings',
            cls: 'primary'
        }];
    }

});
//{/block}
