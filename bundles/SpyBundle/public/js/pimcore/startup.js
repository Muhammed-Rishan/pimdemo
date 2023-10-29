pimcore.registerNS("pimcore.plugin.SpyBundle");

pimcore.plugin.SpyBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // Check if the user has specific permissions
        var user = pimcore.globalmanager.get("user");
        var permissions = user.permissions;

        if (permissions.indexOf("objects") !== -1) {
            var navigationUl = Ext.get(Ext.query("#pimcore_navigation UL"));
            var newMenuItem = Ext.DomHelper.createDom('<li id="pimcore_menu_new-item" data-menu-tooltip="Track" class="pimcore_menu_item icon-book_open"></li>');
            navigationUl.appendChild(newMenuItem);
            pimcore.helpers.initMenuTooltips();
            var iconImage = document.createElement("img");
            iconImage.src = "/bundles/pimcoreadmin/img/icon/database.png";
            newMenuItem.appendChild(iconImage);
            newMenuItem.onclick = function () {
                // Handle the click event for the custom menu item
                alert("Custom menu item clicked");
            };
        }

        alert("SpyBundle ready!");
    }
});

var SpyBundlePlugin = new pimcore.plugin.SpyBundle();
