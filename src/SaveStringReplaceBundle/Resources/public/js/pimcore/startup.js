const keys = {
    replaceAll: 'srtingReplaceAll',
    replaceSelected: 'srtingReplaceSelected',
    concatAll: 'srtingConcatenateAll',
    concatSelected: 'srtingConcatenateSelected'
}
const allowedTypes = ['input', 'textarea', 'wysiwyg']

const rowContextMenuHandler = async (e) => {
    const menu = e.detail.menu
    const selectedRows = e.detail.selectedRows

    removeMenuItemsIfPresent(menu)

    const className = selectedRows[0].data.classname

    const columns = e.detail.object.columns
    const columnsConfig = getColumnsConfig(columns)
    const fieldsData = getFieldsData(columnsConfig)

    addMenuItems(menu, fieldsData, className, selectedRows.map(e => e.id))

}

const headerMenuHandler = async (e) => {
    const menu = e.detail.menu
    const selectedRows = e.detail.selectedRows

    removeMenuItemsIfPresent(menu)

    const classId = e.detail.classId
    const classes = e.detail.classes
    const className = classes.find(c => c.id === classId).name;

    const columns = e.detail.object.columns
    const columnsConfig = getColumnsConfig(columns)
    const fieldsData = getFieldsData(columnsConfig)

    const activeHeader = menu.activeHeader.dataIndex

    const notEditableError = fieldsData.find(f => f.value === activeHeader) ? false : true

    addMenuItems(menu, fieldsData, className, selectedRows.map(e => e.id), activeHeader, notEditableError, false)
}


const removeMenuItemsIfPresent = (menu) => {
    const menuKeys = menu.items.keys

    Object.values(keys).forEach(item => {
        if (menuKeys.includes(item)) {
            menu.remove(item);
        }
    })
}

const getColumnsConfig = (columns) => {
    return columns.reduce((arr, curr) => {
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
}

const getFieldsData = (columnsConfig) => {
    return columnsConfig.reduce((arr, c) => {
        if (allowedTypes.includes(c.type) && !c.noteditable) {
            arr.push({
                value: c.dataIndex,
                optionName: c.text
            })
        }

        return arr
    }, [])
}

const showEditableError = (notEditableError) => {
    if (notEditableError) {
        Ext.MessageBox.alert(t('error'), t('this_element_cannot_be_edited'));
        return true
    }
    return false
}

const addMenuItems = (menu, fieldsData, className, idList = [], activeHeader = '', notEditableError = false, showSelect = true) => {
    menu.add({
        itemId: keys.replaceSelected,
        text: "String replace selected",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            if (showEditableError(notEditableError)) return
            makeWindow('Replace selected', '/admin/string_replace/selected', fieldsData, className, activeHeader, showSelect, idList)
        }
    });

    menu.add({
        itemId: keys.replaceAll,
        text: "String replace all",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            if (showEditableError(notEditableError)) return
            makeWindow('Replace all', '/admin/string_replace/all', fieldsData, className, activeHeader, showSelect)
        }
    });

    menu.add({
        itemId: keys.concatSelected,
        text: "String concatenate selected",
        iconCls: "pimcore_icon_operator_concatenator",
        handler: () => {
            if (showEditableError(notEditableError)) return
            concatWindow('Concatenate selected', '/admin/string_concat/selected', fieldsData, className, activeHeader, idList)
        }
    });

    menu.add({
        itemId: keys.concatAll,
        text: "String concatenate all",
        iconCls: "pimcore_icon_operator_concatenator",
        handler: () => {
            if (showEditableError(notEditableError)) return
            concatWindow('Concatenate all', '/admin/string_concat/all', fieldsData, className, activeHeader)
        }
    });

    pimcore.layout.refresh();
}

document.addEventListener(pimcore.events.prepareOnRowContextmenu, rowContextMenuHandler);
document.addEventListener('beforeGridHeaderContextMenuShow', headerMenuHandler)
