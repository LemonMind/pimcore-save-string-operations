document.addEventListener(pimcore.events.prepareOnRowContextmenu, async (e) => {
    const menu = e.detail.menu
    const selectedRows = e.detail.selectedRows
    const className = selectedRows[0].data.classname
    const keys = Object.keys(selectedRows[0].data.inheritedFields)

    if (keys.length === 0) {
        return
    }

    const columns = e.detail.object.columns
    const allowedTypes = ['input', 'textarea', 'wysiwyg']

    const columnsConfig = columns.reduce((arr, curr) => {
        if (curr.config.layout) {
            arr.push({
                dataIndex: curr.config.dataIndex,
                text: curr.config.text,
                type: curr.config.layout.type
            })
        }

        return arr;
    }, []);

    const selectedFieldsData = keys.reduce((arr, key) => {
        let config = columnsConfig.find(c => c.dataIndex === key && allowedTypes.includes(c.type))
        if (config) {
            arr.push({
                value: key,
                optionName: config.text
            })
        }

        return arr;
    }, []);

    menu.add({
        text: "String replace selected",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            makeWindow('Replace selected', '/admin/string_replace/selected', selectedFieldsData, className, selectedRows.map(e => e.id))
        }
    });

    menu.add({
        text: "String replace all",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            makeWindow('Replace all', '/admin/string_replace/all', selectedFieldsData, className)
        }
    });

    pimcore.layout.refresh();
});
