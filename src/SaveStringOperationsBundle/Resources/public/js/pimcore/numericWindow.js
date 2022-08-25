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

    const handleInput = (e) => {
        const names = ['set_to_value', 'set_to_percentage']
        const selectId = panel.items.items.findIndex(item => item.id === e.id)

        const fields = panel.items.items.filter(item => names.some(name => name === item.name))

        fields.forEach(field => {
            panel.remove(field.id)
        });

        if (e.value === 'value') {
            panel.insert(selectId + 1, Ext.create("Ext.form.field.Number", {
                xtype: 'numberfield',
                name: 'set_to_value',
                fieldLabel: 'Value',
                allowBlank: false,
                margin: '10'
            }));
        }

        if (e.value === 'percentage') {
            panel.insert(selectId + 1, Ext.create("Ext.form.field.Number", {
                xtype: 'numberfield',
                name: 'set_to_percentage',
                fieldLabel: 'Percentage',
                minValue: 0,
                allowBlank: false,
                margin: '10'
            }));
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
            name: 'field',
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
        width: 420,
        height: 260,
        items: panel
    })

    modal.show();
}