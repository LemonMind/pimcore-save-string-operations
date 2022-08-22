pimcore.element.helpers.gridColumnConfigExtended = {
    updateGridHeaderContextMenu: function (grid) {
        var columnConfig = new Ext.menu.Item({
            text: t("grid_options"),
            iconCls: "pimcore_icon_table_col pimcore_icon_overlay_edit",
            handler: this.openColumnConfig.bind(this)
        });
        var menu = grid.headerCt.getMenu();
        menu.add(columnConfig);
        //

        var batchAllMenu = new Ext.menu.Item({
            text: t("batch_change"),
            iconCls: "pimcore_icon_table pimcore_icon_overlay_go",
            handler: function (grid) {
                var menu = grid.headerCt.getMenu();
                var column = menu.activeHeader;
                this.batchPrepare(column, false, false, false);
            }.bind(this, grid)
        });
        menu.add(batchAllMenu);

        var batchSelectedMenu = new Ext.menu.Item({
            text: t("batch_change_selected"),
            iconCls: "pimcore_icon_structuredTable pimcore_icon_overlay_go",
            handler: function (grid) {
                var menu = grid.headerCt.getMenu();
                var column = menu.activeHeader;
                this.batchPrepare(column, true, false, false);
            }.bind(this, grid)
        });
        menu.add(batchSelectedMenu);

        var batchAppendAllMenu = new Ext.menu.Item({
            text: t("batch_append_all"),
            iconCls: "pimcore_icon_table pimcore_icon_overlay_go",
            handler: function (grid) {
                var menu = grid.headerCt.getMenu();
                var column = menu.activeHeader;
                this.batchPrepare(column, false, true, false);
            }.bind(this, grid)
        });
        menu.add(batchAppendAllMenu);

        var batchAppendSelectedMenu = new Ext.menu.Item({
            text: t("batch_append_selected"),
            iconCls: "pimcore_icon_structuredTable pimcore_icon_overlay_go",
            handler: function (grid) {
                var menu = grid.headerCt.getMenu();
                var column = menu.activeHeader;
                this.batchPrepare(column, true, true, false);
            }.bind(this, grid)
        });
        menu.add(batchAppendSelectedMenu);


        var batchRemoveAllMenu = new Ext.menu.Item({
            text: t("batch_remove_all"),
            iconCls: "pimcore_icon_table pimcore_icon_overlay_go",
            handler: function (grid) {
                var menu = grid.headerCt.getMenu();
                var column = menu.activeHeader;
                this.batchPrepare(column, false, false, true);
            }.bind(this, grid)
        });
        menu.add(batchRemoveAllMenu);

        var batchRemoveSelectedMenu = new Ext.menu.Item({
            text: t("batch_remove_selected"),
            iconCls: "pimcore_icon_structuredTable pimcore_icon_overlay_go",
            handler: function (grid) {
                var menu = grid.headerCt.getMenu();
                var column = menu.activeHeader;
                this.batchPrepare(column, true, false, true);
            }.bind(this, grid)
        });
        menu.add(batchRemoveSelectedMenu);

        var filterByRelationMenu = new Ext.menu.Item({
            text: t("filter_by_relation"),
            iconCls: "pimcore_icon_filter pimcore_icon_overlay_add",
            handler: function (grid) {
                var menu = grid.headerCt.getMenu();
                var column = menu.activeHeader;
                this.filterPrepare(column);
            }.bind(this, grid)
        });
        menu.add(filterByRelationMenu);

        //
        menu.on('beforeshow', function (batchAllMenu, batchSelectedMenu, grid) {
            var menu = grid.headerCt.getMenu();
            var columnDataIndex = menu.activeHeader.dataIndex;

            if (menu.activeHeader.config && typeof menu.activeHeader.config.getRelationFilter === "function") {
                filterByRelationMenu.show();
            } else {
                filterByRelationMenu.hide();
            }

            // LemonMind custom event
            const beforeGridHeaderContextMenuShow = new CustomEvent('beforeGridHeaderContextMenuShow',
                {
                    detail: {
                        object: grid,
                        selectedRows: grid.getSelectionModel().getSelection(),
                        menu: menu,
                        classId: this.classId,
                        classes: this.object.data.classes,
                    }
                });

            document.dispatchEvent(beforeGridHeaderContextMenuShow);

            // no batch for system properties
            if (Ext.Array.contains(this.systemColumns, columnDataIndex) || Ext.Array.contains(this.noBatchColumns, columnDataIndex)) {
                batchAllMenu.hide();
                batchSelectedMenu.hide();
            } else {
                batchAllMenu.show();
                batchSelectedMenu.show();
            }

            if (!Ext.Array.contains(this.systemColumns, columnDataIndex) && Ext.Array.contains(this.batchAppendColumns ? this.batchAppendColumns : [], columnDataIndex)) {
                batchAppendAllMenu.show();
                batchAppendSelectedMenu.show();
            } else {
                batchAppendAllMenu.hide();
                batchAppendSelectedMenu.hide();
            }

            if (!Ext.Array.contains(this.systemColumns, columnDataIndex) && Ext.Array.contains(this.batchRemoveColumns ? this.batchRemoveColumns : [], columnDataIndex)) {
                batchRemoveAllMenu.show();
                batchRemoveSelectedMenu.show();
            } else {
                batchRemoveAllMenu.hide();
                batchRemoveSelectedMenu.hide();
            }
        }.bind(this, batchAllMenu, batchSelectedMenu, grid));
    }
}

pimcore.element.selector.object.addMethods(pimcore.element.helpers.gridColumnConfigExtended);
pimcore.object.search.addMethods(pimcore.element.helpers.gridColumnConfigExtended);
pimcore.object.variantsTab.addMethods(pimcore.element.helpers.gridColumnConfigExtended);