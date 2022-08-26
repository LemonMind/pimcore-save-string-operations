function formHandler(form, waitMask, modal, gridStore) {
    if (!form.isValid()) {
        pimcore.helpers.showNotification(t("error"), t("Your form is invalid!"), "error");
        return
    }

    waitMask.show();

    form.submit({
        success: function (form, action) {
            waitMask.hide();
            modal.hide();
            gridStore.loadPage(gridStore.currentPage)
            pimcore.helpers.showNotification(t("success"), t("Changes Saved"), "success");
        },
        failure: function (form, action) {
            waitMask.hide();
            pimcore.helpers.showNotification(t("error"), t("Error when saving"), "error");
        },
    });
}