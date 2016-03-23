(function () {
    if (typeof window.pure                  !== "object") { window.pure                 = {}; }
    if (typeof window.pure.wordpress        !== "object") { window.pure.wordpress       = {}; }
    if (typeof window.pure.wordpress.media  !== "object") { window.pure.wordpress.media = {}; }
    "use strict";
    /*
A)  To define ADD button use:
        pure-wordpress-media-images-add-selector="some selector here of target IMG"

    Example:    <a pure-wordpress-media-images-add-selector="img[id=|123|]"></a>
                use | instead "
                after <a> will be clicked, and image will be selected <img alt="" id="123"/> will get SRC attribute with image URL

B)  To define REMOVE button use:
        pure-wordpress-media-images-remove-selector="some selector here of target IMG"

    Example:    <a pure-wordpress-media-images-remove-selector="img[id=|123|]"></a>
                use | instead "
                after <a> will be clicked attribute SRC of <img alt="" id="123"/> will be cleared

C)  Also you can define container which contains any data. If image is selected such "switcher" will be visible
    and if image isn't selector switcher will be hidden

    ID of attachment is in [pure-wordpress-media-images-id] attribute of target IMG

D)  To reset all buttons and clear of IMG use:
        pure.wordpress.media.images.reset(selector);
    selector - is selector of container (node) where all buttons are.

    Example:    pure.wordpress.media.images.reset('div[id="123"]');
                will reset all buttons and IMGs inside div[id="123"]

E)  To define default image (after removing) define attribute for IMG:
    pure-wordpress-media-images-default-src="url_to_defailt_image"

    Example:    <img pure-wordpress-media-images-default-src="url_to_default_image"/>

F)  ID of attachment will be saved in attribute of IMG - [pure-wordpress-media-images-id]
    */
    window.pure.wordpress.media.images = {
        init    : function(){
            function processing(attribute) {
                var buttons         = pure.nodes.select.all('*[' + attribute + ']'),
                    storages        = null,
                    selector_value  = null,
                    selector        = null,
                    type            = (attribute.indexOf('-wordpress-media-images-add-') !== -1 ? 'add' : 'remove'),
                    switchers       = null,
                    displayed       = null;
                if (buttons !== null){
                    if (typeof buttons.length === "number"){
                        for (var index = buttons.length - 1; index >= 0; index -= 1){
                            displayed = (buttons[index].getAttribute('pure-wordpress-media-images-displayed') !== null ? true : false);
                            if (displayed === false || (displayed === true && pure.nodes.render.isDisplayed(buttons[index]) === true)){
                                selector_value  = buttons[index].getAttribute(attribute);
                                selector        = selector_value;
                                if (typeof selector === "string"){
                                    if (selector.trim() !== ''){
                                        selector = selector.replace(/\|/gi, '"');
                                        storages = pure.nodes.select.all(selector);
                                        if (storages !== null){
                                            if (typeof storages.length === "number") {
                                                selector    = buttons[index].getAttribute('pure-wordpress-media-images-switch-selector');
                                                selector    = (typeof selector === 'string' ? (selector.trim() === '' ? null : selector.replace(/\|/gi, '"')) : null);
                                                switchers   = (selector !== null ? pure.nodes.select.all(selector) : []);
                                                //Hide that nodes, which marked as SWITCHERs
                                                pure.wordpress.media.images.helpers.hideNodes(switchers);
                                                switch (type) {
                                                    case 'add':
                                                        (function (button, storages) {
                                                            pure.events.add(button, "click",
                                                                function (event) { pure.wordpress.media.images.actions.load(event, button, storages, switchers); });
                                                        }(buttons[index], storages));
                                                        break;
                                                    case 'remove':
                                                        (function (button, storages) {
                                                            pure.events.add(button, "click",
                                                                function (event) { pure.wordpress.media.images.actions.remove(event, button, storages, switchers); });
                                                        }(buttons[index], storages));
                                                        buttons[index].setAttribute('pure-wordpress-media-images-reset-selector', selector_value);
                                                        break;
                                                }
                                                buttons[index].removeAttribute(attribute);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            };
            processing('pure-wordpress-media-images-add-selector');
            processing('pure-wordpress-media-images-remove-selector');
        },
        reset : function(container_selector){
            var buttons             = null,
                storages            = null,
                selector            = null,
                switchers           = null,
                container_selector  = (typeof container_selector === 'string' ? container_selector : null);
            if (container_selector !== null) {
                buttons = pure.nodes.select.all(container_selector + ' ' + '*[pure-wordpress-media-images-reset-selector]');
                if (buttons !== null) {
                    if (typeof buttons.length === "number") {
                        for (var index = buttons.length - 1; index >= 0; index -= 1) {
                            selector = buttons[index].getAttribute('pure-wordpress-media-images-reset-selector');
                            if (typeof selector === "string") {
                                if (selector.trim() !== '') {
                                    selector = selector.replace(/\|/gi, '"');
                                    storages = pure.nodes.select.all(selector);
                                    if (storages !== null) {
                                        if (typeof storages.length === "number") {
                                            selector    = buttons[index].getAttribute('pure-wordpress-media-images-switch-selector');
                                            selector    = (typeof selector === 'string' ? (selector.trim() === '' ? null : selector.replace(/\|/gi, '"')) : null);
                                            switchers   = (selector !== null ? pure.nodes.select.all(selector) : []);
                                            pure.wordpress.media.images.actions.remove(null, buttons[index], storages, switchers);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        },
        actions : {
            load    : function (event, button, storages, switchers) {
                var imagesSelector = wp.media({
                    title   : 'Select image',
                    multiple: false,
                    library : { type: 'image'   },
                    button  : { text: 'insert'  }
                });
                imagesSelector.on('select', function () {
                    var selection = imagesSelector.state().get('selection');
                    selection.each(function (attachment) {
                        for (var index = storages.length - 1; index >= 0; index -= 1) {
                            if (typeof storages[index].setAttribute === "function") {
                                storages[index].setAttribute('src', attachment.attributes.url);
                            }
                            if (typeof storages[index].setAttribute === "function") {
                                storages[index].setAttribute('pure-wordpress-media-images-id', attachment.id);
                            }
                            if (typeof storages[index].value !== "undefined") {
                                storages[index].value = attachment.id;
                                pure.events.call(storages[index], 'change');
                            }
                        }
                        pure.wordpress.media.images.helpers.showNodes(switchers);
                    });
                });
                imagesSelector.open();
            },
            remove  : function (event, button, storages, switchers) {
                var default_src = null;
                for (var index = storages.length - 1; index >= 0; index -= 1){
                    if (typeof storages[index].getAttribute === "function"){
                        default_src = storages[index].getAttribute('pure-wordpress-media-images-default-src');
                    }
                    default_src = (typeof default_src === 'string' ? default_src : '');
                    if (typeof storages[index].setAttribute === "function"){
                        storages[index].setAttribute('src', default_src);
                    }
                    if (typeof storages[index].removeAttribute === "function") {
                        storages[index].removeAttribute('pure-wordpress-media-images-id');
                    }
                    if (typeof storages[index].value !== "undefined") {
                        storages[index].value = '';
                        pure.events.call(storages[index], 'change');
                    }
                }
                pure.wordpress.media.images.helpers.hideNodes(switchers);
            }
        },
        helpers: {
            hideNodes: function (nodes) {
                pure.wordpress.media.images.helpers.setDisplayToNodes(nodes, 'none');
            },
            showNodes: function (nodes) {
                pure.wordpress.media.images.helpers.setDisplayToNodes(nodes, '');
            },
            setDisplayToNodes: function (nodes, value) {
                var display_value = null;
                for (var index = nodes.length - 1; index >= 0; index -= 1) {
                    if (typeof nodes[index].style !== "undefined") {
                        if (typeof nodes[index].style.display !== "undefined") {
                            if (value === '') {
                                display_value   = nodes[index].getAttribute('data-switch-display-value');
                                value           = (typeof display_value === 'string' ? display_value : '');
                            }
                            nodes[index].style.display = value;
                        }
                    }
                }
            }
        }
    };
    pure.system.start.add(pure.wordpress.media.images.init);
}());