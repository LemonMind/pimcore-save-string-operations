const keys = {
    stringSubMenu: 'stringSubMenu',
    replaceAll: 'srtingReplaceAll',
    replaceSelected: 'srtingReplaceSelected',
    concatAll: 'srtingConcatenateAll',
    concatSelected: 'srtingConcatenateSelected',
    caseAll: 'stringCaseConvertAll',
    caseSelected: 'stringCaseConvertSelected',
    numericSubMenu: 'numericSubMenu',
    changeAll: 'numberChangeAll',
    changeSelected: 'numberChangeSelected',
}

const allowedStringTypes = ['input', 'textarea', 'wysiwyg']
const allowedNumberTypes = ['numeric']

const rowContextMenuHandler = async (e) => {
    const menu = e.detail.menu
    removeMenuItemsIfPresent(menu)
    const selectedRows = e.detail.selectedRows
    const gridStore = e.detail.object.store

    const className = selectedRows[0].data.classname

    const columns = e.detail.object.columns
    const columnsConfig = getColumnsConfig(columns);

    (() => {
        const fieldsData = getFieldsData(columnsConfig, allowedStringTypes)
        const allFieldsData = getFieldsData(columnsConfig, allowedStringTypes, false)

        const subMenu = addSubMenu(menu, keys.stringSubMenu, 'String Operators')

        addMenuItemsString({
            gridStore,
            menu: subMenu,
            fieldsData,
            className,
            idList: selectedRows.map(e => e.id),
            allFieldsData,
        });
    })();

    (() => {
        const fieldsData = getFieldsData(columnsConfig, allowedNumberTypes)
        const allFieldsData = getFieldsData(columnsConfig, allowedNumberTypes, false)

        const subMenu = addSubMenu(menu, keys.numericSubMenu, 'Numeric Operators')

        addMenuItemsNumeric({
            gridStore,
            menu: subMenu,
            fieldsData,
            className,
            idList: selectedRows.map(e => e.id),
            allFieldsData,
        });
    })();

}

const headerMenuHandler = async (e) => {
    const menu = e.detail.menu
    removeMenuItemsIfPresent(menu)
    const activeHeader = menu.activeHeader.dataIndex
    const selectedRows = e.detail.selectedRows
    const gridStore = e.detail.object.store

    const classId = e.detail.classId
    const classes = e.detail.classes
    const className = classes.find(c => c.id === classId).name;

    const columns = e.detail.object.columns;
    const columnsConfig = getColumnsConfig(columns);

    (() => {
        if (!columnsConfig.find(f => f.dataIndex === activeHeader && allowedStringTypes.includes(f.type))) {
            return
        }

        const fieldsData = getFieldsData(columnsConfig, allowedStringTypes)
        const allFieldsData = getFieldsData(columnsConfig, allowedStringTypes, false)

        const notEditableError = fieldsData.find(f => f.value === activeHeader) ? false : true

        const subMenu = addSubMenu(menu, keys.stringSubMenu, 'String Operators')

        addMenuItemsString({
            gridStore,
            menu: subMenu,
            fieldsData,
            className,
            idList: selectedRows.map(e => e.id),
            activeHeader,
            notEditableError,
            showSelect: false,
            allFieldsData,
        });
    })();

    (() => {
        if (!columnsConfig.find(f => f.dataIndex === activeHeader && allowedNumberTypes.includes(f.type))) {
            return
        }

        const fieldsData = getFieldsData(columnsConfig, allowedNumberTypes)

        const notEditableError = fieldsData.find(f => f.value === activeHeader) ? false : true

        const subMenu = addSubMenu(menu, keys.numericSubMenu, 'Numeric Operators')

        addMenuItemsNumeric({
            gridStore,
            menu: subMenu,
            fieldsData,
            className,
            idList: selectedRows.map(e => e.id),
            activeHeader,
            notEditableError,
            showSelect: false,
        })
    })();

}


const removeMenuItemsIfPresent = (menu) => {
    const menuKeys = menu.items.keys

    Object.values(keys).forEach(item => {
        if (menuKeys.includes(item)) {
            menu.remove(item);
        }
    })
}

const addSubMenu = (menu, key, text) => {
    const subMenu = menu.add({
        itemId: key,
        text: text,
        iconCls: "pimcore_icon_folder",
        menu: {
            items: []
        }
    })

    return subMenu.menu
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

const getFieldsData = (columnsConfig, allowedTypes, onlyEditable = true) => {
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

const addMenuItemsString = (config) => {
    const {
        gridStore,
        menu,
        fieldsData,
        className,
        idList = [],
        activeHeader = '',
        notEditableError = false,
        showSelect = true,
        allFieldsData = [],
    } = config

    menu.add({
        itemId: keys.replaceSelected,
        text: "Replace selected",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            if (showEditableError(notEditableError)) return
            replaceWindow('Replace selected', '/admin/string_replace/selected', gridStore, fieldsData, className, activeHeader, showSelect, idList)
        }
    });

    menu.add({
        itemId: keys.replaceAll,
        text: "Replace all",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            if (showEditableError(notEditableError)) return
            replaceWindow('Replace all', '/admin/string_replace/all', gridStore, fieldsData, className, activeHeader, showSelect, idList)
        }
    });

    menu.add({
        itemId: keys.concatSelected,
        text: "Concatenate selected",
        iconCls: "pimcore_icon_operator_concatenator",
        handler: () => {
            concatWindow('Concatenate selected', '/admin/string_concat/selected', gridStore, fieldsData, allFieldsData, className, activeHeader, idList)
        }
    });

    menu.add({
        itemId: keys.concatAll,
        text: "Concatenate all",
        iconCls: "pimcore_icon_operator_concatenator",
        handler: () => {
            concatWindow('Concatenate all', '/admin/string_concat/all', gridStore, fieldsData, allFieldsData, className, activeHeader, idList)
        }
    });

    menu.add({
        itemId: keys.caseSelected,
        text: "Case convert selected",
        iconCls: "pimcore_icon_operator_caseconverter",
        handler: () => {
            convertWindow('Concatenate selected', '/admin/string_convert/selected', gridStore, fieldsData, className, activeHeader, idList)
        }
    });

    menu.add({
        itemId: keys.caseAll,
        text: "Case convert all",
        iconCls: "pimcore_icon_operator_caseconverter",
        handler: () => {
            convertWindow('Concatenate all', '/admin/string_convert/all', gridStore, fieldsData, className, activeHeader, idList)
        }
    });

    pimcore.layout.refresh();
}

const addMenuItemsNumeric = (config) => {
    const {
        gridStore,
        menu,
        fieldsData,
        className,
        idList = [],
        activeHeader = '',
        notEditableError = false,
        showSelect = true,
    } = config

    menu.add({
        itemId: keys.changeSelected,
        text: "Change selected",
        iconCls: "pimcore_icon_data_group_numeric",
        handler: () => {
            if (showEditableError(notEditableError)) return
            numericWindow('Change selected', '/admin/number_change/selected', gridStore, fieldsData, className, activeHeader, showSelect, idList)
        }
    });

    menu.add({
        itemId: keys.changeAll,
        text: "Change all",
        iconCls: "pimcore_icon_data_group_numeric",
        handler: () => {
            if (showEditableError(notEditableError)) return
            numericWindow('Change all', '/admin/number_change/all', gridStore, fieldsData, className, activeHeader, showSelect, idList)
        }
    });

    pimcore.layout.refresh();
}

document.addEventListener(pimcore.events.prepareOnRowContextmenu, rowContextMenuHandler);
document.addEventListener('beforeGridHeaderContextMenuShow', headerMenuHandler)
