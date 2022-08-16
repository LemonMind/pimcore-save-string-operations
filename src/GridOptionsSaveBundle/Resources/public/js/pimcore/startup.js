document.addEventListener(pimcore.events.prepareOnRowContextmenu, async (e) => {
    let menu = e.detail.menu
    let selectedRows = e.detail.selectedRows

    console.log(menu)
    console.log(selectedRows)

    if (selectedRows.length === 0) {
        return
    }

    const keys = Object.keys(selectedRows[0].data.inheritedFields)
    const className = selectedRows[0].data.className

    let fieldsToSelect = []
    keys.forEach(key => {
        if (typeof (selectedRows[0].data[key]) === "string") {
            fieldsToSelect.push(key)
        }
    });

    console.log(fieldsToSelect)

    fieldsToSelectData = fieldsToSelect.map(e => ({ value: e, optionName: e }))

    let idList = selectedRows.map(e => e.id)
    console.log(idList)

    menu.add({
        text: "String replace selected",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: function () {
            let modal = new Ext.Window({
                title: 'Replace selected',
                modal: true,
                layout: 'fit',
                width: 500,
                height: 250,
                items: new Ext.form.Panel({
                    layout: 'anchor',
                    url: '/admin/string_replace',
                    defaults: {
                        anchor: '100%'
                    },
                    items: [{
                        xtype: 'combo',
                        name: 'Field',
                        fieldLabel: 'Select Field:',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['optionName', 'value'],
                            data: fieldsToSelectData
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
                        name: 'reaplace',
                        allowBlank: false,
                        margin: '5'
                    },
                    {
                        xtype: 'checkboxfield',
                        boxLabel: 'Insensitive',
                        name: 'insensitive',
                        inputValue: '1',
                        margin: '5'
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

            modal.show(this);

        }.bind(this)
    });

    pimcore.layout.refresh();
});
