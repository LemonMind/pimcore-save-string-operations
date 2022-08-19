function makeWindow(title, url, data, className, value, idList = []) {
    const store = Ext.create('Ext.data.Store', {
        fields: ['optionName', 'value'],
        data: data
    })

    const replacePanel = new Ext.form.Panel({
        layout: 'anchor',
        url: url,
        defaults: {
            anchor: '100%'
        },
        items: [{
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
        }, {
            xtype: 'textfield',
            fieldLabel: 'Search',
            name: 'search',
            allowBlank: false,
            margin: '10'
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Replace',
            name: 'replace',
            allowBlank: false,
            margin: '10'
        },
        {
            xtype: 'checkboxfield',
            boxLabel: 'Insensitive',
            name: 'insensitive',
            inputValue: '1',
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
                        mockEvent = { stopEvent: function () { } };
                        pimcore.helpers.handleF5(116, mockEvent);
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
        target: replacePanel
    });

    let modal = new Ext.Window({
        title: title,
        modal: true,
        layout: 'fit',
        width: 500,
        height: 300,
        items: replacePanel
    })

    modal.show();
}