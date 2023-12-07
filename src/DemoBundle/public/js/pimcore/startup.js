pimcore.registerNS("pimcore.plugin.DemoBundle");

pimcore.plugin.DemoBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("DemoBundle ready!");
    }
});

var DemoBundlePlugin = new pimcore.plugin.DemoBundle();
