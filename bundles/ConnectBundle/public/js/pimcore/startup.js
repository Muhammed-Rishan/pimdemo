pimcore.registerNS("pimcore.plugin.ConnectBundle");

pimcore.plugin.ConnectBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("DemoBundle ready!");
    }
});

const ConnectBundlePlugin = new pimcore.plugin.ConnectBundle();
