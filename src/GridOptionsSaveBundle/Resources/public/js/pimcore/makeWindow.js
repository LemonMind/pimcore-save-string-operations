function makeWindow(title, url, data, className, idList = []) {
    let modal = new Ext.Window({
        title: title,
        modal: true,
        layout: 'fit',
        width: 500,
        height: 250,
        items: new Ext.form.Panel({
            layout: 'anchor',
            url: url,
            defaults: {
                anchor: '100%'
            },
            items: [{
                xtype: 'combo',
                name: 'field',
                fieldLabel: 'Select Field:',
                store: Ext.create('Ext.data.Store', {
                    fields: ['optionName', 'value'],
                    data: data
                }),
                emptyText: 'Select one...',
                displayField: 'optionName',
                valueField: 'value',
                allowBlank: false,
                margin: '5'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Search',
                name: 'search',
                allowBlank: false,
                margin: '5'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Replace',
                name: 'replace',
                allowBlank: false,
                margin: '5'
            },
            {
                xtype: 'checkboxfield',
                boxLabel: 'Insensitive',
                name: 'insensitive',
                inputValue: '1',
                margin: '7'
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
                text: 'Send',
                formBind: true,
                disabled: true,
                handler: function () {
                    let form = this.up('form').getForm();
                    if (!form.isValid()) {
                        pimcore.helpers.showNotification(t("error"), t("Your form is invalid!"), "error");
                        return
                    }

                    form.submit({
                        success: function (form, action) {
                            modal.hide();
                            pimcore.helpers.showNotification(t("success"), t("Message sent"), "success");
                        },
                        failure: function (form, action) {
                            modal.hide();
                            pimcore.helpers.showNotification(t("error"), t("Error when sending message"), "error");
                        },
                    });
                }
            }],
        })
    })

    modal.show();
}