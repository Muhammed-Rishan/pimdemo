pimcore.registerNS("pimcore.plugin.Button");
pimcore.plugin.Button = Class.create({
    systemPanel: null,

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    loadAndPopulateData: function () {

        Ext.Ajax.request({
            url: '/load-data',
            method: 'GET',
            success: function (response) {
                const result = Ext.decode(response.responseText);
                if (result.success) {
                    const data = result.data;
                    Ext.getCmp('hotelTypeCombo').setValue(data.hotelType);
                    Ext.getCmp('roomTypeCombo').setValue(data.roomType);
                    Ext.getCmp('hotelNameTextField').setValue(data.hotelName);
                    Ext.getCmp('descriptionTextarea').setValue(data.description);
                    Ext.getCmp('checked').setValue(data.checkboxValue);
                }
            }
        });
    },

    pimcoreReady: function (e) {
        const user = pimcore.globalmanager.get("user");
        const permissions = user.permissions;

        if (permissions.indexOf("objects") !== -1) {
            const navigationUl = Ext.get(Ext.query("#pimcore_navigation UL"));
            const customMenuItem = Ext.DomHelper.createDom('<li id="pimcore_menu_custom-item" data-menu-tooltip="System" class="pimcore_menu_item icon-server"></li>');
            navigationUl.appendChild(customMenuItem);
            pimcore.helpers.initMenuTooltips();
            const iconImage = document.createElement("img");
            iconImage.src = "/bundles/pimcoreadmin/img/icon/server.png";
            customMenuItem.appendChild(iconImage);

            // const systemButton = Ext.get(Ext.query("#pimcore_menu_custom-item"))[0];

            const submenu = new Ext.menu.Menu({
                items: [
                    {
                        text: "System Settings",
                        iconCls: "pimcore_icon_settings",
                        handler: this.toggleSystem.bind(this)
                    }
                ]
            });

            // systemButton.insertSibling(submenu, 'after');
            customMenuItem.onclick = function () {
                var customMenuItemPosition = Ext.get(customMenuItem).getXY();
                // submenu.show(customMenuItem, 'tr-br?');
                submenu.showAt([customMenuItemPosition[0] + customMenuItem.offsetWidth, customMenuItemPosition[1]]);
            };
            // this.loadAndPopulateData();
        }
    },

    toggleSystem: function () {
        if (this.systemPanel) {
            const mainPanel = Ext.getCmp("pimcore_panel_tabs");
            mainPanel.setActiveTab(this.systemPanel);
        } else {
            this.systemPanel = new Ext.Panel({
                title: "System Settings",
                iconCls: "pimcore_icon_settings",
                closable: true,
                layout: 'form',
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Hotel Information',
                        collapsible: true,
                        collapsed: true,
                        items: [
                            {
                                xtype: 'combo',
                                fieldLabel: 'Hotel Type',
                                store: ['Luxury Hotel', 'Business Hotel', 'Resort', 'Boutique Hotel'],
                                id: 'hotelTypeCombo',
                                editable: false,
                            },
                            {
                                xtype: 'combo',
                                multiSelect: true,
                                fieldLabel: 'Room Type',
                                store: ['Single', 'Double', 'Suite', 'Luxury'],
                                id: 'roomTypeCombo',
                                editable: false,
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Hotel Name',
                                id: 'hotelNameTextField'
                            },
                            {
                                xtype: 'textareafield',
                                fieldLabel: 'Description',
                                id: 'descriptionTextarea'
                            },
                            {
                                xtype: 'checkbox',
                                boxLabel: 'This is a checkbox field with some content',
                                id: 'checked'
                            },
                        ],
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Check',
                        collapsible: true,
                        collapsed: true,
                        items: [
                            // {
                            //     xtype: 'checkbox',
                            //     boxLabel: 'This is a checkbox field with some content',
                            //     id: 'checked'
                            // },
                            // {
                            //     xtype: 'textfield',
                            //     fieldLabel: 'Text Field',
                            // },
                        ],
                    },
                ],
                bbar: [
                    '->',
                    {
                        xtype: 'button',
                        text: 'Save',
                        iconCls: 'pimcore_icon_save',
                        handler: this.saveData.bind(this)
                    },
                ],
            });

            const mainPanel = Ext.getCmp("pimcore_panel_tabs");
            mainPanel.add(this.systemPanel);
            mainPanel.setActiveTab(this.systemPanel);
            this.loadAndPopulateData();
        }

        this.systemPanel.on('beforeclose', function (tab) {
            this.systemPanel = null;
        }.bind(this));
    },

    validateForm: function () {

        const hotelName = Ext.getCmp('hotelNameTextField').getValue();
        const description = Ext.getCmp('descriptionTextarea').getValue();
        // const checkboxValue = Ext.getCmp('checked').getValue();


        if (!hotelName) {
            Ext.Msg.alert('Error', 'Hotel Name is a mandatory field.');
            return false;
        }

        if (!description) {
            Ext.Msg.alert('Error', 'Description is a mandatory field.');
            return false;
        }


        return true;
    },

    saveData: function () {
        if (!this.validateForm()) {
            return;
        }

        const hotelType = Ext.getCmp('hotelTypeCombo').getValue();
        const roomType = Ext.getCmp('roomTypeCombo').getValue();
        const hotelName = Ext.getCmp('hotelNameTextField').getValue();
        const description = Ext.getCmp('descriptionTextarea').getValue();
        const checkboxValue = Ext.getCmp('checked').getValue();

        const data = {
            hotelType: hotelType,
            roomType: roomType,
            hotelName: hotelName,
            description: description,
            checkboxValue: checkboxValue,
        };

        Ext.Ajax.request({
            url: '/save-data',
            method: 'POST',
            params: {
                data: Ext.encode(data)
            },
            success: function (response) {
                const result = Ext.decode(response.responseText);
                if (result.success) {
                    Ext.Msg.alert('Success', 'Data saved successfully.');
                } else {
                    Ext.Msg.alert("Failed to save data");
                }
            },
            failure: function (response) {
                Ext.Msg.alert("Failed to save data");
            }
        });
    }
});

const ButtonPlugin = new pimcore.plugin.Button();