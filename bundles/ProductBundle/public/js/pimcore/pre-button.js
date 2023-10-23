document.addEventListener(pimcore.events.postOpenObject, (e) => {
    if (e.detail.object.data.general.className === 'Product') {
        e.detail.object.toolbar.add({
            text: t('preview'),
            iconCls: 'pimcore_icon_preview',
            scale: 'small',
            handler: function (obj) {
                // console.log(obj)
                // const id = obj.data.general.id;
                const id = e.detail.object.id

                // window.open('/product/'+id , '_blank')
                const xhr = new XMLHttpRequest();
                xhr.open('GET', `/product/` + id, true);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const newTab = window.open(``, '_blank');
                        newTab.document.open();
                        newTab.document.write(xhr.responseText);
                        newTab.document.close();
                        newTab.location.href = '/product/' + id;
                    }
                }

                xhr.send();

            }.bind(this, e.detail.object)
        });
        pimcore.layout.refresh();
    }
});