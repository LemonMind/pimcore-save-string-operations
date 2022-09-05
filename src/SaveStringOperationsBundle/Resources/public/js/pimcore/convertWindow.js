function convertWindow(title, url, gridStore, data, className, value, idList) {
    const store = Ext.create('Ext.data.Store', {
        fields: ['optionName', 'value'],
        data: data
    })

    let panel = new Ext.form.Panel({
        layout: 'anchor',
        url: url,
        defaults: {
            anchor: '100%'
        },
        items: [
            {
                xtype: 'combo',
                name: 'field',
                fieldLabel: 'Select field:',
                store: store,
                emptyText: 'Select one...',
                displayField: 'optionName',
                valueField: 'value',
                value: store.findRecord('value', value),
                allowBlank: false,
                margin: '10'
            },
            {
                xtype: 'fieldcontainer',
                fieldLabel: 'Capitalization',
                defaultType: 'radiofield',
                defaults: {
                    flex: 1
                },
                layout: 'vbox',
                allowBlank: false,
                margin: '10',
                items: [
                    {
                        boxLabel: 'Upper',
                        name: 'type',
                        inputValue: 'upper'
                    }, {
                        boxLabel: 'Lower',
                        name: 'type',
                        inputValue: 'lower'
                    }
                ]
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
            },
            {
                xtype: 'hiddenfield',
                name: 'language',
                value: gridStore.proxy.extraParams.language,
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