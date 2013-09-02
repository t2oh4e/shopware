
//{block name="backend/application/model/container"}

Ext.define('Shopware.model.Container', {

    extend: 'Ext.container.Container',
    autoScroll: true,

    /**
     * Internal property which contains all created association components.
     * This array is used to reload the association data in the component when
     * the data is reloaded.
     * @type { Array }
     */
    associationComponents: [],

    /**
     * List of classes to mix into this class.
     * @type { Object }
     */
    mixins: {
        helper: 'Shopware.model.Helper'
    },

    /**
     * Get the reference to the class from which this object was instantiated. Note that unlike self, this.statics()
     * is scope-independent and it always returns the class from which it was called, regardless of what
     * this points to during run-time.
     *
     * The statics object contains the shopware default configuration for
     * this component. The different shopware configurations are stored
     * within the displayConfig object.
     *
     * @type { object }
     */
    statics: {
        /**
         * The statics displayConfig contains the default shopware configuration for
         * this component.
         * To set the shopware configuration, you can set the displayConfig directly
         * as property of the component:
         *
         * @example
         *      Ext.define('Shopware.apps.Product.view.detail.Product', {
         *          extend: 'Shopware.model.Container',
         *          displayConfig: {
         *              controller: 'product',
         *              fields: {
         *                  name: { fieldLabel: 'Product name' }
         *              },
         *              ...
         *          }
         *      });
         */
        displayConfig: {
            /**
             * The event alias is used to customize the component events for each
             * backend application.
             * The event alias is an optional parameter. If the property is set to
             * undefined, the grid component use the model name as alias.
             * For example:
             *  - A store with Shopware.apps.Product.model.Product is passed to this component
             *  - The model alias will be set to "product"
             *  - All component events have now the prefix "product-..."
             *   - Example "product-add-item".
             *
             * @type { string }
             */
            eventAlias: undefined,

            /**
             * The controller property is used for manyToOne associations.
             * This controller will be requested to load the associated data.
             * In the default case, this controller is the backend php application controller
             * name like 'Article', 'Banner', etc.
             *
             * @type { String }
             * @required
             */
            controller: undefined,

            /**
             * The searchUrl property is used to request the associated data
             * of the base model.
             * Shopware requests the association data as default from the
             * application php backend controller.
             * The searchUrl requires an configured { @link #controller }.
             *
             * @type { String }
             */
            searchUrl: '{url controller="base" action="searchAssociation"}',

            /**
             * The fields property can contains custom form field configurations.
             * It allows to customize the different form fields without overriding the
             * createFormField function.
             * The field configuration will be applied at least to the form field, so it
             * allows to override the each field configuration like listeners, validation or something
             * else.
             *
             * @example
             *  fields: {
             *      name: { fieldLabel: 'OwnLabel' },
             *  }
             */
            fields: { },

            /**
             * The association property can contains the association which has to be displayed
             * within this container.
             * To add an association to this component, the association key has to be added to this array.
             *
             * @example:
             *  Model of this container:
             *  Ext.define('Shopware.apps.Product.model.Product', {
             *      extend: 'Shopware.data.Model',
             *      fields: [ 'id', ... ]
             *      associations: [
             *          {
             *              relation: 'OneToOne',
             *              type: 'hasMany',
             *              model: 'Shopware.apps.Product.model.Attribute',
             *              name: 'getAttribute',
             *              associationKey: 'attribute'
             *          },
             *      ]
             *  });
             *
             *  To display the 'Shopware.apps.Product.model.Attribute' model within
             *  this component, add the associationKey property 'attribute' to the
             *  { @link #associations } property:
             *
             *  Ext.define('Shopware.apps.Product.view.detail.Product', {
             *      extend: 'Shopware.model.Container',
             *      displayConfig: {
             *          associations: [ 'attribute' ]
             *      }
             *  });
             *  
             *  The attribute association component will be created in the { @link #createAssociationComponent }
             *
             * @type { Array }
             */
            associations: [  ],


            /**
             * The fieldAlias property is used to prefix the form fields with the
             * associationKey of the associated model.
             * To display different models in the same Ext.form.Panel, the association
             * model fields uses the associationKey as field name prefix.
             * Additionally the association model field names are surrounded with
             * square brackets:
             *
             * @example
             *  Base model of this container:
             *  Ext.define('Shopware.apps.Product.model.Product', {
             *      extend: 'Shopware.data.Model',
             *      fields: [ 'id', 'name', ... ]
             *      associations: [
             *          {
             *              relation: 'OneToOne',
             *              type: 'hasMany',
             *              model: 'Shopware.apps.Product.model.Attribute',
             *              name: 'getAttribute',
             *              associationKey: 'attribute'
             *          },
             *      ]
             *  });
             *
             *  The fields of this model are created normally:
             *      -   field.name = name;
             *
             *  To display the 'Shopware.apps.Product.model.Attribute' model within
             *  this component, add the associationKey property 'attribute' to the
             *  { @link #associations } property:
             *
             *  Ext.define('Shopware.apps.Product.view.detail.Product', {
             *      extend: 'Shopware.model.Container',
             *      displayConfig: {
             *          associations: [ 'attribute' ]
             *      }
             *  });
             *
             *  The fields of this model are prefixed with the association key
             *  and surrounded with square brackets:
             *      -   field.name = attribute['name']
             *
             *
             * @optional - For the base model
             * @required - For associated models
             * @type { String }
             */
            fieldAlias: undefined
        },

        /**
         * Static function to merge the different configuration values
         * which passed in the class constructor.
         *
         * @param { Object } userOpts
         * @param { Object } displayConfig
         * @returns { Object }
         */
        getDisplayConfig: function (userOpts, displayConfig) {
            var config;

            config = Ext.apply({ }, userOpts.displayConfig, displayConfig);
            config = Ext.apply({ }, config, this.displayConfig);

            if (config.controller) {
                config.searchUrl = config.searchUrl.replace(
                    '/backend/base/', '/backend/' + config.controller + '/'
                );
            }
            return config;
        },

        /**
         * Static function which sets the property value of
         * the passed property and value in the display configuration.
         *
         * @param { String } prop - Property which should be in the { @link #displayConfig }
         * @param { String } val - The value of the property (optional)
         * @returns { Boolean }
         */
        setDisplayConfig: function (prop, val) {
            var me = this;

            val = val || '';

            if (!me.displayConfig.hasOwnProperty(prop)) {
                return false;
            }
            me.displayConfig[prop] = val;
            return true;
        }
    },

    /**
     * Helper function to get config access.
     *
     * @param prop string
     * @returns mixed
     */
    getConfig: function (prop) {
        var me = this;
        return me._opts[prop];
    },

    /**
     * Class constructor which merges the different configurations.
     *
     * @param { Object } opts - Passed configuration
     */
    constructor: function (opts) {
        var me = this;

        me._opts = me.statics().getDisplayConfig(opts, this.displayConfig);
        me.callParent(arguments);
    },

    /**
     * Initialisation of this component.
     * Creates all required elements which has to be displayed within this component.
     */
    initComponent: function() {
        var me = this;

        me.eventAlias = me.getConfig('eventAlias');
        if (!me.eventAlias) me.eventAlias = me.getEventAlias(me.record.$className);
        me.registerEvents();

        me.fireEvent(me.eventAlias + '-before-init-component', me);

        me.fieldAssociations = me.getAssociations(me.record.$className, [
            { relation: 'ManyToOne' }
        ]);

        me.associationComponents = [];
        me.items = me.createItems();
        me.title = me.getModelName(me.record.$className);

        me.fireEvent(me.eventAlias + '-after-init-component', me);

        me.callParent(arguments);
    },

    /**
     * Registers the additional custom events of this component.
     */
    registerEvents: function() {
        var me = this;

        this.addEvents(
            /**
             * Event fired before shopware creates the default elements for this component.
             * @param { Shopware.model.Container } container - Instance of this component
             */
            me.eventAlias + '-before-init-component',

            /**
             * Event fired after all shopware default elements created, but before the me.callParent(arguments)
             * call done.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             */
            me.eventAlias + '-after-init-component',

            /**
             * Event fired before the shopware default elements will be created.
             * Return false in the event listener to cancel the default process.
             * Event can also be used to add some elements at the beginning of the items array.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Array } items - Empty array which will be used as items definition of this component.
             */
            me.eventAlias + '-before-create-items',

            /**
             * Event fired after all shopware default elements created.
             * This event can be used to modify the created items or to push additional items at the end of the items array.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Array } items - Contains all created items like the model field set and all association components.
             */
            me.eventAlias + '-after-create-items',

            /**
             * Event fired before an association component will be created. Association components created if the { @link #associations }
             * property is filled with association keys.
             * The association components are defined in the { @link Shopware.data.Model:shopware } config object.
             * If the event listener function returns false, the function process will be canceled and the component parameter
             * will be set as return value.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Object } component - An empty object which will be set as return value if the event listener returns false
             * @param { String } type - Association type which defined in the association.relation property. (listing, detail, related, field)
             * @param { Shopware.data.Model } model - Instance of the associated model. This model was used to get the component type.
             * @param { Ext.data.Store } store - Store of the association, this store contains all associated data for the created component.
             */
            me.eventAlias + '-before-association-component-created',

            /**
             * Event fired after an association component created. Association components created if the { @link #associations }
             * property is filled with association keys.
             * The association components are defined in the { @link Shopware.data.Model:shopware } config object.
             * The event can be used to modify the component or to replace the created association component with another.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { mixed } component - The created association component. This could be an Shopware.model.Container, Shopware.grid.Association, Shopware.grid.Panel or a custom component.
             * @param { String } type - Association type which defined in the association.relation property. (listing, detail, related, field)
             * @param { Shopware.data.Model } model - Instance of the associated model. This model was used to get the component type.
             * @param { Ext.data.Store } store - Store of the association, this store contains all associated data for the created component.
             */
            me.eventAlias + '-after-association-component-created',

            /**
             * Event fired after all form fields created for the current model.
             * This function can be used to push additional form field to the fields array.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Array } fields - Contains the created model fields
             * @param { Shopware.data.Model } model - Instance of the model which used to create the field set
             * @param { String } alias - Field alias for one to one associations.
             */
            me.eventAlias + '-model-fields-created',

            /**
             * Event fired before a model field set will be created.
             * This event can be used to cancel the field set creation.
             * If the event listener returns false, the fieldSet parameter will be used as return value.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { null } fieldSet - This value will be used as return value, if the listener returns false.
             * @param { Array } items - Array which used as item definition for the form field set.
             * @param { Shopware.data.Model } model - Instance of the model which used to create the form field set.
             * @param { String } alias - Field alias for one to one association which displayed in the same form panel.
             */
            me.eventAlias + '-before-model-field-set-created',

            /**
             * Event fired after the column containers created and pushed into the items array which used for as item
             * definition of the form field set.
             * This event can be used to modify the items array or to push additional items.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Array } fields - Contains the created model fields
             * @param { Array } items - Contains the created column containers which used for the items definition of the field set.
             * @param { Shopware.data.Model } model - Instance of the model which used to create the field set
             * @param { String } alias - Field alias for one to one associations.
             */
            me.eventAlias + '-column-containers-created',

            /**
             * Event fired after the form field set created and before the field set will be set as return value of the
             * function. The fieldSet parameter contains the created form field set.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Ext.form.FieldSet } fieldSet - The created field set which will be set as function return value.
             * @param { Shopware.data.Model } model - Instance of the model which used to create the field set
             * @param { String } alias - Field alias for one to one associations.
             */
            me.eventAlias + '-after-model-field-set-created',

            /**
             * Event fired before a single model form field will be created. If the event listener function
             * returns false, the formField parameter will be used as return value and the default process will
             * be canceled.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Shopware.data.Model } model - Instance of the field model which used to create the form field.
             * @param { Ext.data.Field } field - The model field which used to generate the form field.
             * @param { String } alias - Field alias for one to one associations.
             */
            me.eventAlias + '-before-create-model-field',

            /**
             * Event fired after an association field was created for a single model field.
             * An association field are created if the corresponding model contains a many to one association
             * for a model field.
             * For example: Many articles are belongs to one supplier, so the form field supplierId should be displayed
             * as a search combo box to select an article supplier.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Shopware.data.Model } model - Instance of the field model which used to create the form field.
             * @param { Ext.form.field.Field } formField - The generated form field, this parameter will be set as function return value.
             * @param { Ext.data.Field } field - The model field which used to generate the form field.
             * @param { Ext.data.association.HasMany } fieldAssociation - Association of the model field. Used for many to one associations.
             */
            me.eventAlias + '-association-field-created',

            /**
             * Event fired after one model form field was created by the { @link #createModelField } function.
             * This event is even fired for association fields.
             * The formField parameter contains the created form field and will be used as function return value.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Ext.form.field.Field } formField - The generated form field, this parameter will be set as function return value.
             * @param { Ext.data.Field } field - The model field which used to generate the form field.
             * @param { Ext.data.association.HasMany } fieldAssociation - Association of the model field. Used for many to one associations.
             */
            me.eventAlias + '-model-field-created',

            /**
             * Event fired before the reloaded data will be set in the association components.
             * If the event listener function returns false, the process will be canceled and the function returns false.
             * To cancel the reload for a single association component within this container, use the "eventAlias-before-reload-association-data"
             * event.
             * The store parameter contains the association store which will be null for one to one associations.
             * The record parameter contains the first record of the association store and will be used for additional association
             * stores.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Ext.data.Store } store - Instance of the association store.
             * @param { Shopware.data.Model } record - Instance of the record which will be loaded into this container.
             */
            me.eventAlias + '-before-reload-data',

            /**
             * Event fired before the reloaded data will be set in a single association component.
             * If the event listener function returns false, the reloadData function of this component won't be called
             * and the function continue with the next association.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Ext.data.Store } store - Instance of the association store.
             * @param { Shopware.data.Model } record - Instance of the record which will be loaded into this container.
             * @param { mixed } component - Component of the association. This component has to implement a reloadData function which will be called to reload the data.
             * @param { Ext.data.association.HasMany } association - Definition of the model association which will be reloaded.
             * @param { Ext.data.Store } associationStore - Store of the association which passed to the reloadData function.
             */
            me.eventAlias + '-before-reload-association-data',

            /**
             * Event fired after the reloaded data was set in a single association component.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Ext.data.Store } store - Instance of the association store.
             * @param { Shopware.data.Model } record - Instance of the record which will be loaded into this container.
             * @param { mixed } component - Component of the association. This component has to implement a reloadData function which will be called to reload the data.
             * @param { Ext.data.association.HasMany } association - Definition of the model association which will be reloaded.
             * @param { Ext.data.Store } associationStore - Store of the association which passed to the reloadData function.
             */
            me.eventAlias + '-after-reload-association-data',

            /**
             * Event fired after the reloaded data was set in each association component.
             * To get all association components at this point, you can get them over the container parameter in
             * the associationComponents property.
             *
             * @param { Shopware.model.Container } container - Instance of this component
             * @param { Ext.data.Store } store - Instance of the association store.
             * @param { Shopware.data.Model } record - Instance of the record which will be loaded into this container.
             */
            me.eventAlias + '-after-reload-data'
        );
    },


    /**
     * Creates all components for this container.
     * Shopware creates as default only a field set with the model
     * fields.
     * To display additional association in this component
     * you can add the associationKey to the { @link #associations } property within the displayConfig.
     *
     * Each additional association component is created over the { @link #createAssociationComponent } function.
     *
     * @returns { Array }
     */
    createItems: function() {
        var me = this, items = [], item,
            associations;

        if (!me.fireEvent(me.eventAlias + '-before-create-items', me, items)) {
            return false;
        }

        items.push(
            me.createModelFieldSet(
                me.record.$className,
                me.getConfig('fieldAlias')
            )
        );

        //get all record associations, which defined in the display config.
        associations = me.getAssociations(
            me.record.$className,
            { associationKey: me.getConfig('associations') }
        );

        //the associations will be displayed within this component.
        Ext.each(associations, function(association) {

            //Important row! This call creates the each association component which can be defined in the association array.
            item = me.createAssociationComponent(
                me.getComponentTypeOfAssociation(association),
                Ext.create(association.associatedName),
                me.getAssociationStore(me.record, association),
                association.associationKey
            );

            //check if the component creation was canceled, or throws an exception
            if(item) {
                items.push(item);
                me.associationComponents[association.associationKey] = item;
            }
        });

        me.fireEvent(me.eventAlias + '-after-create-items', me, items);

        return items;
    },

    /**
     * Helper function which creates a single association components.
     *
     * @param type { String } - Possible values: field, detail, listing, related
     * @param model { Shopware.data.Model } - Contains the model instance of the association
     * @param store { Ext.data.Store } - Ext.data.Store of the association
     * @returns { Object }
     */
    createAssociationComponent: function(type, model, store, associationKey) {
        var me = this, component = { }, componentType = model.getConfig(type);

        if (!me.fireEvent(me.eventAlias + '-before-association-component-created', me, component, type, model, store)) {
            return component;
        }

        component = Ext.create(componentType, {
            record: model,
            store: store,
            flex: 1,
            displayConfig: {
                associationKey: associationKey
            }
        });

        me.fireEvent(me.eventAlias + '-after-association-component-created', me, component, type, model, store);

        return component;
    },


    /**
     * Creates an Ext.form.FieldSet for the passed model.
     * The fields are created in the { @link #createModelFields } function.
     * The fields array will be split in two arrays to display them in two
     * column layout containers.
     * If the model is an associated model of the main record, the function requires the alias parameter
     * to prefix the field name with the association key and surround the original field name with square
     * brackets.
     *
     * @param modelName { String } - Full class name of the model. Used to create a model instance.
     * @param alias { String } - Additional alias for the field names (example: 'attribute' => 'attribute[name]')
     *
     * @return Ext.form.FieldSet
     */
    createModelFieldSet: function (modelName, alias) {
        var me = this, fieldSet = null, model = Ext.create(modelName), items = [], container, fields;

        if (!me.fireEvent(me.eventAlias + '-before-model-field-set-created', me, fieldSet, items, model, alias)) {
            return fieldSet;
        }

        //convert all model fields to form fields.
        fields = me.createModelFields(model, alias);

        //create a column container to display the columns in a two column layout
        container = Ext.create('Ext.container.Container', {
            columnWidth: 0.5,
            padding: '0 20 0 0',
            layout: 'anchor',
            items: fields.slice(0, Math.round(fields.length / 2))
        });
        items.push(container);

        container = Ext.create('Ext.container.Container', {
            columnWidth: 0.5,
            layout: 'anchor',
            items: fields.slice(Math.round(fields.length / 2))
        });
        items.push(container);

        me.fireEvent(me.eventAlias + '-column-containers-created', me, fields, items, model, alias);

        fieldSet = Ext.create('Ext.form.FieldSet', {
            flex: 1,
            padding: '10 20',
            layout: 'column',
            items: items,
            title: me.getModelName(modelName)
        });

        me.fireEvent(me.eventAlias + '-after-model-field-set-created', me, fieldSet, model, alias);

        return fieldSet;
    },

    /**
     * Creates all Ext.form.Fields for the passed model.
     * The alias can be used to prefix the field names.
     * For example: 'attribute[name]'.
     *
     * @return Array
     */
    createModelFields: function (model, alias) {
        var me = this, fields = [], field;

        Ext.each(model.fields.items, function (item) {
            field = me.createModelField(model, item, alias);
            if (field) fields.push(field);
        });

        me.fireEvent(me.eventAlias + '-model-fields-created', me, fields, model, alias);

        return fields;
    },

    /**
     * This function creates the form field element
     * for a single model field.
     * This functions use different helper function like { @link Shopware.model.Helper:applyBooleanFieldConfig }
     * to set different shopware default configurations for a form field.
     * The id property of the model won't be displayed.
     *
     * @param model { Ext.data.Model } - Instance of the model which fields should be displayed
     * @param field { Ext.data.Field } - The model field which will be used for the form field creation.
     * @param alias { string } - Field alias for associations. See { @link #fieldAlias }
     *
     * @return { Ext.form.field.Field }
     */
    createModelField: function (model, field, alias) {
        var me = this, formField = {},
            config, customConfig, name,
            fieldModel, fieldComponent, xtype;

        if (!me.fireEvent(me.eventAlias + '-before-create-model-field', me, formField, model, field, alias)) {
            return formField;
        }

        //don't display the id property
        if (model.idProperty === field.name) {
            return null;
        }

        //add default configuration for a form field.
        formField.xtype = 'displayfield';
        formField.anchor = '100%';
        formField.margin = '0 3 7 0';
        formField.labelWidth = 130;
        formField.name = field.name;

        //if an alias was passed, the form field name will be surround with square bracket
        if (alias !== undefined && Ext.isString(alias) && alias.length > 0) {
            formField.name = alias + '[' + field.name + ']';
        }

        //convert the model field name to a human readable word
        formField.fieldLabel = me.camelCaseToWord(field.name);

        //check if the field is configured as association field.
        var fieldAssociation = me.getFieldAssociation(field.name);

        if (fieldAssociation === undefined) {
            switch (field.type.type) {
                case 'int':
                    formField = me.applyIntegerFieldConfig(formField);
                    break;
                case 'string':
                    formField = me.applyStringFieldConfig(formField);
                    break;
                case 'bool':
                    formField = me.applyBooleanFieldConfig(formField);
                    break;
                case 'date':
                    formField = me.applyDateFieldConfig(formField);
                    break;
                case 'float':
                    formField = me.applyFloatFieldConfig(formField);
                    break;
            }

        //association fields are used for manyToOne association like article > supplier.
        //this fields will be displayed as default with an { @link Shopware.form.field.Search }
        } else {
            //first create a model instance to get the merged display config of the model.
            fieldModel = Ext.create(fieldAssociation.associatedName);

            //after the display config merged, we can get the field component.
            fieldComponent = fieldModel.getConfig('field');

            //the field component are defined with the full class name, but we need the xtype for this component
            xtype = Ext.ClassManager.getAliasesByName(fieldComponent);
            formField.xtype = xtype[0].replace('widget.', '');
            formField.subApp = me.subApp;

            //if no custom field configured, we have to configure the display config of the component
            if (fieldComponent === 'Shopware.form.field.Search') {
                formField.store = me.createAssociationSearchStore(
                    fieldAssociation.associatedName,
                    fieldAssociation.associationKey,
                    me.getConfig('searchUrl')
                ).load();
            }
            me.fireEvent(me.eventAlias + '-association-field-created', model, formField, field, fieldAssociation);
        }

        //get the component field configuration. This configuration contains custom field configuration.
        config = me.getConfig('fields');

        if (config) {
            //check if the current field is defined in the fields configuration. Otherwise use an empty object which will be applied.
            customConfig = config[field.name] || {};
            formField = Ext.apply(formField, customConfig);
        }

        me.fireEvent(me.eventAlias + '-model-field-created', model, formField, field, fieldAssociation);
        
        return formField;
    },

    /**
     * Helper function which checks if an many to one association is configured for
     * the passed field.
     *
     * @param fieldName { String }
     * @returns { undefined|Ext.data.association.Association }
     */
    getFieldAssociation: function(fieldName) {
        var me = this, fieldAssociation = undefined;

        Ext.each(me.fieldAssociations, function(association) {
            if (association.field === fieldName) {
                fieldAssociation = association;
                return false;
            }
        });
        return fieldAssociation;
    },

    /**
     * Interface to reload the component data.
     * Used from the { @link Shopware.detail.Controller }.
     *
     * @param store { Ext.data.Store }
     * @param record { Shopware.data.Model }
     */
    reloadData: function(store, record) {
        var me = this, association, component, associationStore;

        //event to cancel the reload process.
        if (!me.fireEvent(me.eventAlias + '-before-reload-data', me, store, record)) {
            return false;
        }

        //iterate the object keys, to get access of the association component, which indexed in the associationComponents array with the association key.
        Object.keys(me.associationComponents).forEach(function(key) {
            component = me.associationComponents[key];

            //if the component hasn't implement the reloadData function, the component will be skipped.
            if (component && typeof component.reloadData === 'function') {

                association = me.getAssociations(
                    record.$className,
                    [ { associationKey: [ key ] } ]
                );
                associationStore = me.getAssociationStore(
                    record,
                    association[0]
                );

                //if the event listener returns false, continue with the next association.
                if (!me.fireEvent(me.eventAlias + '-before-reload-association-data', me, store, record, component, association, associationStore)) {
                    return true;
                }

                //all association components has to implement a reload data function for a uniform interface.
                component.reloadData(
                    associationStore,
                    record
                );

                me.fireEvent(me.eventAlias + '-after-reload-association-data', me, store, record, component, association, associationStore);
            }
        });

        me.fireEvent(me.eventAlias + '-after-reload-data', me, store, record);
    }
});
//{/block}
