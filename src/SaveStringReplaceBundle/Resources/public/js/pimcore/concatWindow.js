function concatWindow(title, url, gridStore, data, allData, className, value, idList = []) {
    const store = Ext.create('Ext.data.Store', {
        fields: ['optionName', 'value'],
        data: data
    })

    const storeWithInput = Ext.create('Ext.data.Store', {
        fields: ['optionName', 'value'],
        data: [...allData, { optionName: 'Input', value: 'input' }]
    })

    const handleInput = (name, e) => {
        const selectId = concatPanel.items.items.findIndex(item => item.id === e.id)
        const inputField = concatPanel.items.items.find(item => item.name === name)

        if (inputField) {
            concatPanel.remove(inputField.id)
        }

        if (e.value === 'input') {
            concatPanel.insert(selectId + 1, Ext.create("Ext.form.field.Text", {
                xtype: 'textfield',
                fieldLabel: 'Input',
                name: name,
                allowBlank: false,
                margin: '10'
            }));
        }
    }

    let concatPanel = new Ext.form.Panel({
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
            allowBlank: false,
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
                if (!form.isValid()) {
                    pimcore.helpers.showNotification(t("error"), t("Your form is invalid!"), "error");
                    return
                }

                waitMask.show();

                form.submit({
                    success: function (form, action) {
                        waitMask.hide();
                        modal.hide();
                        gridStore.loadPage(gridStore.currentPage)
                        pimcore.helpers.showNotification(t("success"), t("Changes Saved"), "success");
                    },
                    failure: function (form, action) {
                        waitMask.hide();
                        pimcore.helpers.showNotification(t("error"), t("Error when saving"), "error");
                    },
                });
            }
        }],
    })



    const waitMask = new Ext.LoadMask({
        msg: 'Please wait...',
        target: concatPanel
    });

    let modal = new Ext.Window({
        title: title,
        modal: true,
        layout: 'fit',
        width: 600,
        height: 370,
        items: concatPanel
    })

    modal.show();
}