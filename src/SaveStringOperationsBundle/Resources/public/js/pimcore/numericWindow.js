function numericWindow(title, url, gridStore, data, className, value, showSelect, idList) {
    const store = Ext.create('Ext.data.Store', {
        fields: ['optionName', 'value'],
        data: data
    })

    const storeOptions = Ext.create('Ext.data.Store', {
        fields: ['optionName', 'value'],
        data: [
            { 'optionName': 'Value', 'value': 'value' },
            { 'optionName': 'Percentage', 'value': 'percentage' }
        ]

    })

    const storePercentage = Ext.create('Ext.data.Store', {
        fields: ['optionName', 'value'],
        data: [
            { 'optionName': 'Increase', 'value': 'increase' },
            { 'optionName': 'Decrease', 'value': 'decrease' }
        ]
    })

    const handleInput = (e) => {
        const names = ['value', 'change_type']
        const selectId = panel.items.items.findIndex(item => item.id === e.id)

        const fields = panel.items.items.filter(item => names.some(name => name === item.name))

        fields.forEach(field => {
            panel.remove(field.id)
        });

        if (e.value === 'value') {
            panel.insert(selectId + 1, Ext.create("Ext.form.field.Number", {
                xtype: 'numberfield',
                name: names[0],
                fieldLabel: 'Value',
                allowBlank: false,
                margin: '10'
            }));
        }

        if (e.value === 'percentage') {
            panel.insert(selectId + 1, Ext.create("Ext.form.field.ComboBox", {
                xtype: 'combo',
                name: names[1],
                fieldLabel: 'Select Type',
                store: storePercentage,
                emptyText: 'Select one...',
                displayField: 'optionName',
                valueField: 'value',
                allowBlank: false,
                margin: '10',
                listeners: {
                    'select': (selectEvent) => {
                        const field = panel.items.items.find(item => item.name === names[0])

                        if (field) {
                            panel.remove(field.id)
                        }

                        panel.insert(selectId + 2, Ext.create("Ext.form.field.Number", {
                            xtype: 'numberfield',
                            name: names[0],
                            fieldLabel: 'Percentage',
                            minValue: 0,
                            maxValue: selectEvent.value === 'decrease' ? 100 : null,
                            allowBlank: false,
                            margin: '10'
                        }));
                    }
                }
            }))
        }
    }

    let panel = new Ext.form.Panel({
        layout: 'anchor',
        url: url,
        defaults: {
            anchor: '100%'
        },
        items: [showSelect ? {
            xtype: 'combo',
            name: 'field',
            fieldLabel: 'Select Field:',
            store: store,
            emptyText: 'Select one...',
            displayField: 'optionName',
            valueField: 'value',
            value: store.findRecord('value', value),
            allowBlank: false,
            margin: '10'
        } : {
            xtype: 'hiddenfield',
            name: 'field',
            value: value,
            allowBlank: false,
        },
        {
            xtype: 'combo',
            name: 'set_to',
            fieldLabel: 'Set to',
            store: storeOptions,
            emptyText: 'Select one...',
            displayField: 'optionName',
            valueField: 'value',
            allowBlank: false,
            margin: '10',
            listeners: {
                'select': handleInput
            }
        },
        {
            xtype: 'hiddenfield',
            name: 'className',
            value: className,
        },
        {
            xtype: 'hiddenfield',
            name: 'idList',
            value: idList,
        }
        ],
        buttons: [{
            text: 'Close',
            handler: () => modal.hide(),
        }, {
            text: 'Apply',
            formBind: true,
            disabled: true,
            iconCls: 'x-btn-icon-el x-btn-icon-el-default-small pimcore_icon_apply',
            handler: function () {
                let form = this.up('form').getForm();
                formHandler(form, waitMask, modal, gridStore)
            }
        }],
    })

    const waitMask = new Ext.LoadMask({
        msg: 'Please wait...',
        target: panel
    });

    let modal = new Ext.Window({
        title: title,
        modal: true,
        layout: 'fit',
        width: 500,
        height: 300,
        items: panel
    })

    modal.show();
}