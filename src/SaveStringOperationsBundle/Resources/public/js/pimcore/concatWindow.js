function concatWindow(title, url, gridStore, data, allData, className, value, idList) {
    const store = Ext.create('Ext.data.Store', {
        fields: ['optionName', 'value'],
        data: data
    })

    const storeWithInput = Ext.create('Ext.data.Store', {
        fields: ['optionName', 'value'],
        data: [...allData, { optionName: 'Input', value: 'input' }]
    })

    const handleInput = (name, e) => {
        const selectId = panel.items.items.findIndex(item => item.id === e.id)
        const inputField = panel.items.items.find(item => item.name === name)

        if (inputField) {
            panel.remove(inputField.id)
        }

        if (e.value === 'input') {
            panel.insert(selectId + 1, Ext.create("Ext.form.field.Text", {
                xtype: 'textfield',
                fieldLabel: 'Input',
                name: name,
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
        items: [{
            xtype: 'combo',
            name: 'field_one',
            fieldLabel: 'Select Field:',
            store: storeWithInput,
            emptyText: 'Select one...',
            displayField: 'optionName',
            valueField: 'value',
            value: storeWithInput.findRecord('value', value),
            allowBlank: false,
            margin: '10',
            listeners: {
                'select': (e) => handleInput('input_one', e)
            }
        },

        {
            xtype: 'textfield',
            fieldLabel: 'Separator',
            name: 'separator',
            allowBlank: true,
            margin: '10'
        },
        {
            xtype: 'combo',
            name: 'field_two',
            fieldLabel: 'Select Field:',
            store: storeWithInput,
            emptyText: 'Select one...',
            displayField: 'optionName',
            valueField: 'value',
            allowBlank: false,
            margin: '10',
            listeners: {
                'select': (e) => handleInput('input_two', e)
            }
        },
        {
            xtype: 'combo',
            name: 'field_save',
            fieldLabel: 'Save to:',
            store: store,
            emptyText: 'Select one...',
            displayField: 'optionName',
            valueField: 'value',
            value: store.findRecord('value', value),
            allowBlank: false,
            margin: '10'
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
        width: 600,
        height: 370,
        items: panel
    })

    modal.show();
}