const replaceAllKey = 'srtingReplaceAll'
const replaceSelectedKey = 'srtingReplaceSelected'
const allowedTypes = ['input', 'textarea', 'wysiwyg']

const rowContextMenuHandler = async (e) => {
    const menu = e.detail.menu
    const selectedRows = e.detail.selectedRows

    removeMenuItemsIfPresent(menu)

    const className = selectedRows[0].data.classname
    const keys = Object.keys(selectedRows[0].data.inheritedFields)

    const columns = e.detail.object.columns
    const columnsConfig = getColumnsConfig(columns)

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

    addMenuItems(menu, selectedFieldsData, className, selectedRows.map(e => e.id))

}

const headerMenuHandler = async (e) => {
    console.log(e)
    const menu = e.detail.menu
    const selectedRows = e.detail.selectedRows

    removeMenuItemsIfPresent(menu)

    const className = '';

    const columns = e.detail.object.columns
    const columnsConfig = getColumnsConfig(columns)

    const activeHeader = menu.activeHeader.dataIndex

    const field = columnsConfig.find(c => c.dataIndex === activeHeader && allowedTypes.includes(c.type))
    let selectedFieldsData = []
    let notEditableError = false

    if (!field) {
        return
    }

    selectedFieldsData.push({
        value: activeHeader,
        optionName: field.text
    })

    if (field.noteditable) {
        notEditableError = true
    }

    addMenuItems(menu, selectedFieldsData, className, selectedRows.map(e => e.id), activeHeader, notEditableError)
}


const removeMenuItemsIfPresent = (menu) => {
    const menuKeys = menu.items.keys

    if (menuKeys.includes(replaceAllKey)) {
        menu.remove(replaceAllKey);
    }

    if (menuKeys.includes(replaceSelectedKey)) {
        menu.remove(replaceSelectedKey);
    }
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

const addMenuItems = (menu, selectedFieldsData, className, idList = [], activeHeader = '', notEditableError = false) => {
    menu.add({
        itemId: replaceSelectedKey,
        text: "String replace selected",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            if (notEditableError) {
                Ext.MessageBox.alert(t('error'), t('this_element_cannot_be_edited'));
                return
            }
            makeWindow('Replace selected', '/admin/string_replace/selected', selectedFieldsData, className, activeHeader, idList)
        }
    });

    menu.add({
        itemId: replaceAllKey,
        text: "String replace all",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            if (notEditableError) {
                Ext.MessageBox.alert(t('error'), t('this_element_cannot_be_edited'));
                return
            }
            makeWindow('Replace all', '/admin/string_replace/all', selectedFieldsData, className, activeHeader)
        }
    });

    pimcore.layout.refresh();
}

document.addEventListener(pimcore.events.prepareOnRowContextmenu, rowContextMenuHandler);
document.addEventListener('beforeGridHeaderContextMenuShow', headerMenuHandler)
