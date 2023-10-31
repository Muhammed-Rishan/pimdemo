pimcore.registerNS("pimcore.plugin.SpyBundle");
pimcore.plugin.SpyBundle = Class.create({
    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        const user = pimcore.globalmanager.get("user");
        const permissions = user.permissions;

        if (permissions.indexOf("objects") !== -1) {
            const navigationUl = Ext.get(Ext.query("#pimcore_navigation UL"));
            const newMenuItem = Ext.DomHelper.createDom('<li id="pimcore_menu_new-item" data-menu-tooltip="Track" class="pimcore_menu_item pimcore_menu_needs_children icon-database"></li>');
            navigationUl.appendChild(newMenuItem);
            pimcore.helpers.initMenuTooltips();
            const iconImage = document.createElement("img");
            iconImage.src = "/bundles/pimcoreadmin/img/icon/database.png";
            newMenuItem.appendChild(iconImage);
            newMenuItem.onclick = function () {

                // alert("Custom menu item clicked");
                if (!this.adminLogPanel) {
                    this.openTabPanel();
                }
            }.bind(this);
        }

        // alert("SpyBundle ready!");
    },
    openTabPanel: function () {

        const store = Ext.create('Ext.data.Store', {
            fields: ['id','adminuserid', 'action', 'timestamp'],
            pageSize: 100,
            proxy: {
                type: 'ajax',
                url: '/spy',
                reader: {
                    type: 'json',
                    rootProperty: 'data',
                },
            },
            autoLoad: true,
        });

        this.adminLogPanel = new Ext.grid.Panel({
            title: "Admin Log",
            iconCls: "pimcore_icon_table",
            store: store,
            columns: [
                {text: "ID", dataIndex: "id"},
                {text: "Admin User ID", dataIndex: "adminuserid"},
                {text: "Action", dataIndex: "action"},
                {text: "Timestamp", dataIndex: "timestamp"},
            ],
            closable: true,
            bbar: Ext.create('Ext.PagingToolbar', {
                store: store,
                displayInfo: true,
                displayMsg: 'Displaying {0} - {1} of {2}',
                emptyMsg: "No data to display",
            }),
        });


        this.adminLogPanel.on('beforeclose', function (tab) {
            this.adminLogPanel = null;
        }.bind(this));
        // });

        const mainPanel = Ext.getCmp("pimcore_panel_tabs");
        mainPanel.add(this.adminLogPanel);
        mainPanel.setActiveTab(this.adminLogPanel);
    },
});

const SpyBundlePlugin = new pimcore.plugin.SpyBundle();
