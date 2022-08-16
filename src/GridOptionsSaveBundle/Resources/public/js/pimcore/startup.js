pimcore.registerNS("pimcore.plugin.LemonmindGridOptionsSaveBundle");

pimcore.plugin.LemonmindGridOptionsSaveBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.LemonmindGridOptionsSaveBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("LemonmindGridOptionsSaveBundle ready!");
    }
});

var LemonmindGridOptionsSaveBundlePlugin = new pimcore.plugin.LemonmindGridOptionsSaveBundle();
