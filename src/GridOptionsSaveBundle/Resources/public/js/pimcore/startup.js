document.addEventListener(pimcore.events.prepareOnRowContextmenu, async (e) => {
    let menu = e.detail.menu
    let selectedRows = e.detail.selectedRows

    if (selectedRows.length === 0) {
        return
    }

    const keys = Object.keys(selectedRows[0].data.inheritedFields)
    const className = selectedRows[0].data.classname

    if (keys.length === 0) {
        return
    }

    let fieldsToSelect = []
    keys.forEach(key => {
        if (typeof (selectedRows[0].data[key]) === "string") {
            fieldsToSelect.push(key)
        }
    });

    fieldsToSelectData = fieldsToSelect.map(e => ({ value: e, optionName: e }))

    menu.add({
        text: "String replace selected",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            makeWindow('Replace selected', '/admin/string_replace/selected', fieldsToSelectData, className, selectedRows.map(e => e.id))
        }
    });

    menu.add({
        text: "String replace all",
        iconCls: "pimcore_icon_operator_stringreplace",
        handler: () => {
            makeWindow('Replace all', '/admin/string_replace/all', fieldsToSelectData, className)
        }
    });

    pimcore.layout.refresh();
});
