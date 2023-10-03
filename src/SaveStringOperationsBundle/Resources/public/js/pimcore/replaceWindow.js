function replaceWindow(title, url, gridStore, data, className, value, showSelect, idList) {
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
        }, {
            xtype: 'textfield',
            fieldLabel: 'Search',
            name: 'search',
            allowBlank: true,
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