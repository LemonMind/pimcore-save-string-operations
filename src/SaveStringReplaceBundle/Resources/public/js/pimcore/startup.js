const eventsHandler = async (e) => {
    const menu = e.detail.menu
    const selectedRows = e.detail.selectedRows

    const allKey = 'srtingReplaceAll'
    const selectedKey = 'srtingReplaceSelected'

    const menuKeys = menu.items.keys

    if (menuKeys.includes(allKey)) {
        menu.remove(allKey);
    }

    if (menuKeys.includes(selectedKey)) {
        menu.remove(selectedKey);
    }

    if (selectedRows.length === 0) {
        return
    }

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
                type: curr.config.layout.type,
                noteditable: curr.config.layout.layout.noteditable
            })
        }

        return arr;
    }, []);

    const selectedFieldsData = keys.reduce((arr, key) => {
        let config = columnsConfig.find(c => c.dataIndex === key && allowedTypes.includes(c.type) && !c.noteditable)
        if (config) {
            arr.push({
                value: key,
                optionName: config.text
            })
        }

        return arr;
    }, []);

    if (selectedFieldsData.length === 0) {
        return
    }

    let activeHeader = ''

    if (menu.activeHeader) {
        activeHeader = menu.activeHeader.dataIndex

        if (!selectedFieldsData.some(field => field.value === activeHeader)) {
            return
        }
    }

    menu.add({
        itemId: selectedKey,
        text: "String replace selected",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            makeWindow('Replace selected', '/admin/string_replace/selected', selectedFieldsData, className, activeHeader, selectedRows.map(e => e.id))
        }
    });

    menu.add({
        itemId: allKey,
        text: "String replace all",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            makeWindow('Replace all', '/admin/string_replace/all', selectedFieldsData, className, activeHeader)
        }
    });

    pimcore.layout.refresh();
}

document.addEventListener(pimcore.events.prepareOnRowContextmenu, eventsHandler);
document.addEventListener('beforeGridHeaderContextMenuShow', eventsHandler)
