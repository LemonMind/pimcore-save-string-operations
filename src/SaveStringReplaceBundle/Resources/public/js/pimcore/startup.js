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
    const gridStore = e.detail.object.store

    removeMenuItemsIfPresent(menu)

    const className = selectedRows[0].data.classname

    const columns = e.detail.object.columns
    const columnsConfig = getColumnsConfig(columns)
    const fieldsData = getFieldsData(columnsConfig)
    const allFieldsData = getFieldsData(columnsConfig, false)

    addMenuItemsReplace(gridStore, menu, fieldsData, className, selectedRows.map(e => e.id))
    addMenuItemsConcat(gridStore, menu, fieldsData, className, selectedRows.map(e => e.id), allFieldsData)

}

const headerMenuHandler = async (e) => {
    const menu = e.detail.menu
    const selectedRows = e.detail.selectedRows
    const gridStore = e.detail.object.store

    removeMenuItemsIfPresent(menu)

    const classId = e.detail.classId
    const classes = e.detail.classes
    const className = classes.find(c => c.id === classId).name;

    const columns = e.detail.object.columns
    const columnsConfig = getColumnsConfig(columns)
    const fieldsData = getFieldsData(columnsConfig)
    const allFieldsData = getFieldsData(columnsConfig, false)

    const activeHeader = menu.activeHeader.dataIndex

    const notEditableError = fieldsData.find(f => f.value === activeHeader) ? false : true

    addMenuItemsReplace(gridStore, menu, fieldsData, className, selectedRows.map(e => e.id), activeHeader, notEditableError, false)
    addMenuItemsConcat(gridStore, menu, fieldsData, className, selectedRows.map(e => e.id), allFieldsData, activeHeader)
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

const getFieldsData = (columnsConfig, onlyEditable = true) => {
    return columnsConfig.reduce((arr, c) => {
        if (allowedTypes.includes(c.type) && (onlyEditable ? !c.noteditable : true)) {
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

const addMenuItemsReplace = (gridStore, menu, fieldsData, className, idList = [], activeHeader = '', notEditableError = false, showSelect = true) => {
    menu.add({
        itemId: keys.replaceSelected,
        text: "String replace selected",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            if (showEditableError(notEditableError)) return
            makeWindow('Replace selected', '/admin/string_replace/selected', gridStore, fieldsData, className, activeHeader, showSelect, idList)
        }
    });

    menu.add({
        itemId: keys.replaceAll,
        text: "String replace all",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            if (showEditableError(notEditableError)) return
            makeWindow('Replace all', '/admin/string_replace/all', gridStore, fieldsData, className, activeHeader, showSelect)
        }
    });

    pimcore.layout.refresh();
}

const addMenuItemsConcat = (gridStore, menu, fieldsData, className, idList = [], allFieldsData = [], activeHeader = '') => {
    menu.add({
        itemId: keys.concatSelected,
        text: "String concatenate selected",
        iconCls: "pimcore_icon_operator_concatenator",
        handler: () => {
            concatWindow('Concatenate selected', '/admin/string_concat/selected', gridStore, fieldsData, allFieldsData, className, activeHeader, idList)
        }
    });

    menu.add({
        itemId: keys.concatAll,
        text: "String concatenate all",
        iconCls: "pimcore_icon_operator_concatenator",
        handler: () => {
            concatWindow('Concatenate all', '/admin/string_concat/all', gridStore, fieldsData, allFieldsData, className, activeHeader)
        }
    });

    pimcore.layout.refresh();
}

document.addEventListener(pimcore.events.prepareOnRowContextmenu, rowContextMenuHandler);
document.addEventListener('beforeGridHeaderContextMenuShow', headerMenuHandler)
