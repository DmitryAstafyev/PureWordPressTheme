/// <reference path="pure.js" />
/// <reference path="pure.tools.js" />
/// <reference path="pure.nodes.js" />
/// <reference path="pure.events.js" />
(function () {
    if (typeof window.pure      !== "object") { window.pure         = {}; }
    if (typeof window.pure.menu !== "object") { window.pure.menu    = {}; }
    "use strict";
    window.pure.menu.C = {
        init    : function(){
            pure.menu.C.start.          init();
            pure.menu.C.sizes.          init();
            pure.menu.C.breadcrumbs.    init();
            pure.menu.C.menu.           init();
            pure.menu.C.areas.          init();
            pure.menu.C.more.           init();
            pure.menu.C.toggle.         init();
            pure.menu.C.scroll.         init();
            pure.menu.C.blocksCallers.  init();
        },
        start   : {
            init : function(){
                var container = pure.nodes.select.first('*[data-menu-engine="Menu.Global.Container"]');
                if (container !== null){
                    container.style.display = '';
                }
            }
        },
        sizes   : {
            data        : {
                nodes   : null,
                sizes   : {
                    areas   : {},
                    content : {}
                }
            },
            init        : function () {
                var nodes = {
                    areas   : {
                        logo        : pure.nodes.select.first('*[data-menu-engine="Area.Logo"]'     ),
                        menu        : pure.nodes.select.first('*[data-menu-engine="Area.Menu"]'     ),
                        personal    : pure.nodes.select.first('*[data-menu-engine="Area.Personal"]' )
                    },
                    content : {
                        logo        : pure.nodes.select.first('*[data-menu-engine="Content.Logo"]'  ),
                        menu        : pure.nodes.select.all('*[data-menu-engine="Menu.Item"]'       ),
                        personal    : pure.nodes.select.all('*[data-menu-engine="Personal.Item"]'   ),
                        breadcrumbs : pure.nodes.select.all('*[data-menu-engine="Breadcrumbs.Item"]')
                    },
                    containers: {
                        menu        : pure.nodes.select.first('*[data-menu-engine="Menu.Container"]')
                    }
                };
                if (pure.tools.objects.isValueIn(nodes, null, true) === false) {
                    pure.menu.C.sizes.data.nodes = nodes;
                    pure.menu.C.sizes.update('areas'    );
                    pure.menu.C.sizes.update('content'  );
                    //pure.openmenu.C.sizes.update('content', 'personal'  );
                    //pure.openmenu.C.sizes.update('areas',   'logo'      );
                }
            },
            update      : function (group, target) {
                function fillSizes(nodes, storage) {
                    for (var property in nodes) {
                        if (typeof nodes[property].length === 'number') {
                            storage[property] = [];
                            proceedArray(nodes, storage, property);
                        } else {
                            storage[property] = pure.nodes.render.size(nodes[property]);
                        }
                    }
                };
                function proceedArray(nodes, storage, property) {
                    for (var index = 0, max_index = nodes[property].length; index < max_index; index += 1) {
                        storage[property].push(pure.nodes.render.size(nodes[property][index]));
                    }
                }
                var nodes   = pure.menu.C.sizes.data.nodes,
                    sizes   = pure.menu.C.sizes.data.sizes,
                    target  = (typeof target === 'string' ? target : false);
                if (nodes !== null) {
                    if (typeof nodes[group] !== 'undefined') {
                        if (target === false) {
                            fillSizes(nodes[group], sizes[group]);
                        } else {
                            if (typeof nodes[group][target] !== 'undefined') {
                                if (typeof nodes[group][target].parentNode !== 'undefined') {
                                    sizes[group][target] = pure.nodes.render.size(nodes[group][target]);
                                } else {
                                    sizes[group][target] = [];
                                    proceedArray(nodes[group], sizes[group], target);
                                }
                            }
                        }
                    }
                }
            },
            sum         : function (target) {
                var sum = 0;
                if (target instanceof Array) {
                    target.forEach(
                        function (item, index, source) {
                            sum += item.marginWidth;
                        },
                        target
                    );
                }
                return sum;
            },
            visibleSum  : function (group, target) {
                var sum     = 0,
                    data = pure.menu.C.sizes.data;
                if (typeof data.nodes[group] !== 'undefined' && typeof data.sizes[group] !== 'undefined') {
                    if (typeof data.nodes[group][target] !== 'undefined' && typeof data.sizes[group][target] !== 'undefined') {
                        if (data.sizes[group][target] instanceof Array) {
                            data.sizes[group][target].forEach(
                                function (item, index, source) {
                                    if (pure.menu.C.more.isIn(index) === false) {
                                        sum += item.marginWidth;
                                    }
                                },
                                data.sizes[group][target]
                            );
                        }
                    }
                }
                return sum;
            },
            displaiedSum: function (group, target) {
                var sum = 0,
                    data = pure.menu.C.sizes.data;
                if (typeof data.nodes[group] !== 'undefined' && typeof data.sizes[group] !== 'undefined') {
                    if (typeof data.nodes[group][target] !== 'undefined' && typeof data.sizes[group][target] !== 'undefined') {
                        if (data.sizes[group][target] instanceof Array) {
                            data.sizes[group][target].forEach(
                                function (item, index, source) {
                                    if (data.nodes[group][target][index].style.display !== 'none') {
                                        sum += item.marginWidth;
                                    }
                                },
                                data.sizes[group][target]
                            );
                        }
                    }
                }
                return sum;
            }

        },
        areas       : {
            init    : function(){
                pure.events.add(
                    window,
                    'resize',
                    pure.menu.C.areas.update
                );
                pure.menu.C.areas.update();
            },
            update  : function () {
                var nodes   = pure.menu.C.sizes.data.nodes,
                    sizes   = pure.menu.C.sizes.data.sizes,
                    left    = null,
                    right   = null;
                if (nodes !== null) {
                    left    = sizes.content.logo.marginWidth;
                    right   = pure.menu.C.sizes.sum(sizes.content.personal);
                    nodes.areas.logo.style.width        = left + 'px';
                    if (nodes.areas.logo.style.opacity !== ''){
                        nodes.areas.menu.style.left         = 0 + 'px';
                    }else{
                        nodes.areas.menu.style.left         = left + 'px';
                    }
                    nodes.areas.menu.style.right        = right + 'px';
                    if (pure.menu.C.toggle.personalActive === false) {
                        nodes.areas.personal.style.width = right + 'px';
                    }
                }
            }
        },
        more        : {
            data    : {
                nodes       : null,
                size        : 0,
                indexes     : [],
                attributes  : {
                    names   : [
                        { name: 'data-pointer',     value : 'right' },
                        { name: 'data-height-drop', value : ''      }
                    ],
                    values  : {}
                }
            },
            init        : function () {
                var nodes = {
                        item        : pure.nodes.select.first('*[data-menu-engine="Menu.Item.More"]'        ),
                        container   : pure.nodes.select.first('*[data-menu-engine="Menu.Container.More"]'   )
                    },
                    data = pure.menu.C.more.data;
                if (pure.tools.objects.isValueIn(nodes, null) === false) {
                    data.nodes  = nodes;
                    data.size   = pure.nodes.render.size(nodes.item);
                    pure.menu.C.more.hide();
                }
            },
            updateSize  : function(){
                var data = pure.menu.C.more.data;
                if (data !== null){
                    if (data.size.width === 0 || data.size.height === 0){
                        data.size   = pure.nodes.render.size(data.nodes.item);
                    }
                }
            },
            show        : function(){
                var data = pure.menu.C.more.data;
                if (data.nodes !== null) {
                    if (data.indexes.length > 0) {
                        data.nodes.item.style.display = '';
                    }
                }
            },
            hide        : function(){
                var data = pure.menu.C.more.data;
                if (data.nodes !== null) {
                    data.nodes.item.style.display = 'none';
                }
            },
            isShown     : function(){
                var data = pure.menu.C.more.data;
                if (data.nodes !== null) {
                    return (data.nodes.item.style.display === 'none' ? false : true);
                }
                return false;
            },
            attribures  : {
                reset   : function (node, index) {
                    var attributes  = pure.menu.C.more.data.attributes,
                        attribute   = null;
                    attributes.values[index] = [];
                    for (var _index = 0, max_index = attributes.names.length; _index < max_index; _index += 1) {
                        attribute = node.getAttribute(attributes.names[_index].name);
                        if (attribute !== null) {
                            attributes.values[index].push({
                                name    : attributes.names[_index].name,
                                value   : attribute
                            });
                            node.setAttribute(attributes.names[_index].name, attributes.names[_index].value);
                        }
                    }
                },
                restore : function (node, index) {
                    var attributes = pure.menu.C.more.data.attributes,
                        attribute = null;
                    if (typeof attributes.values[index] !== 'undefined') {
                        for (var _index = 0, max_index = attributes.values[index].length; _index < max_index; _index += 1) {
                            node.setAttribute(attributes.values[index][_index].name, attributes.values[index][_index].value);
                        }
                    }
                    attributes.values[index] = null;
                    delete attributes.values[index];
                }
            },
            add     : function (index) {
                var nodes   = pure.menu.C.sizes.data.nodes.content.menu,
                    data    = pure.menu.C.more.data;
                if (data.indexes.indexOf(index) === -1) {
                    data.nodes.container.appendChild(nodes[index]);
                    data.indexes.push(index);
                    pure.menu.C.more.attribures.reset(nodes[index], index);
                }
                pure.menu.C.more.show();
            },
            remove  : function (index) {
                var nodes = pure.menu.C.sizes.data.nodes,
                    data    = pure.menu.C.more.data;
                if (data.indexes.indexOf(index) !== -1) {
                    nodes.containers.menu.insertBefore(nodes.content.menu[index], data.nodes.item);
                    data.indexes.splice(data.indexes.indexOf(index), 1);
                    pure.menu.C.more.attribures.restore(nodes.content.menu[index], index);
                }
                if (data.indexes.length === 0) {
                    pure.menu.C.more.hide();
                }
            },
            getWidth: function (forse) {
                var data    = pure.menu.C.more.data,
                    size    = 0,
                    forse   = (typeof forse === 'boolean' ? forse : false);
                if (data.nodes !== null) {
                    if (data.nodes.item.style.display !== 'none' || forse !== false) {
                        pure.menu.C.more.updateSize();
                        size = data.size.marginWidth;
                    }
                }
                return size;
            },
            isIn    : function (index) {
                var data = pure.menu.C.more.data;
                return (data.indexes.indexOf(index) !== -1 ? true : false);
            }
        },
        toggle      : {
            personalActive  : false,
            init            : function () {
                pure.events.add(
                    window,
                    'resize',
                    pure.menu.C.toggle.update
                );
                pure.menu.C.toggle.update();
            },
            update          : function () {
                if (pure.menu.C.toggle.personalActive === false) {
                    pure.menu.C.toggle.menu();
                } else {
                    pure.menu.C.toggle.personal();
                }
            },
            menu        : function () {
                var sizes   = pure.menu.C.sizes.data.sizes,
                    width   = pure.menu.C.sizes.visibleSum('content', 'menu'),
                    nodes   = pure.menu.C.sizes.data.nodes.content.menu,
                    offset  = 0;
                if (pure.menu.C.menu.isShown() !== false) {
                    pure.menu.C.sizes.update('areas', 'menu');
                    if (width > (sizes.areas.menu.marginWidth - pure.menu.C.more.getWidth())) {
                        for (var index = nodes.length - 1; index >= 0; index -= 1) {
                            if (pure.menu.C.more.isIn(index) === false) {
                                if ((width - offset) > (sizes.areas.menu.marginWidth - pure.menu.C.more.getWidth())) {
                                    offset += sizes.content.menu[index].marginWidth;
                                    pure.menu.C.more.add(index);
                                }
                            }
                        }
                        if ((width - offset) > (sizes.areas.menu.marginWidth - pure.menu.C.more.getWidth())) {
                            if (pure.menu.C.more.isShown() === true) {
                                pure.menu.C.more.hide();
                                pure.menu.C.toggle.toPersonal();
                            }
                        }
                    } else {
                        if (sizes.areas.menu.marginWidth > pure.menu.C.more.getWidth(true)) {
                            if (pure.menu.C.more.isShown() === false) {
                                pure.menu.C.more.show();
                            }
                        } else {
                            pure.menu.C.toggle.toPersonal();
                        }
                        for (var index = 0, max_index = nodes.length; index < max_index; index += 1) {
                            if (pure.menu.C.more.isIn(index) === true) {
                                offset += sizes.content.menu[index].marginWidth;
                                if ((width + offset) < (sizes.areas.menu.marginWidth - pure.menu.C.more.getWidth())) {
                                    pure.menu.C.more.remove(index);
                                }
                            }
                        }
                    }
                } else {
                    //pure.menu.C.sizes.update('content', 'breadcrumbs');
                    pure.menu.C.breadcrumbs.show();
                    pure.menu.C.breadcrumbs.update();
                }
            },
            toPersonal      : function(){
                var nodes = pure.menu.C.sizes.data.nodes.areas;
                nodes.logo.style.opacity            = 0.001;
                //nodes.logo.style.display            = 'none';
                nodes.personal.style.width          = '100%';
                pure.menu.C.toggle.personalActive   = true;
                pure.menu.C.toggle.personal();
            },
            fromPersonal    : function () {
                var nodes = pure.menu.C.sizes.data.nodes.areas;
                nodes.logo.style.opacity            = '';
                //nodes.logo.style.display            = '';
                nodes.personal.style.width          = '';
                pure.menu.C.toggle.personalActive   = false;
                pure.menu.C.areas.update();
            },
            personal        : function () {
                function isCaller(node) {
                    return (node.getAttribute('data-menu-engine-blocks') === 'Personal.Block.Caller' ? true : false);
                };
                var sizes   = pure.menu.C.sizes.data.sizes,
                    width   = pure.menu.C.sizes.displaiedSum('content', 'personal'),
                    nodes   = pure.menu.C.sizes.data.nodes.content.personal,
                    offset  = 0;
                pure.menu.C.sizes.update('areas', 'personal');
                if (width > sizes.areas.personal.marginWidth) {
                    for (var index = nodes.length - 1; index >= 0; index -= 1) {
                        if (nodes[index].style.display !== 'none') {
                            if ((width - offset) > sizes.areas.personal.marginWidth && isCaller(nodes[index]) === false) {
                                offset += sizes.content.personal[index].marginWidth;
                                nodes[index].style.display = 'none';
                            }
                        }
                    }
                } else {
                    for (var index = 0, max_index = nodes.length; index < max_index; index += 1) {
                        if (nodes[index].style.display === 'none') {
                            offset += sizes.content.personal[index].marginWidth;
                            if ((width + offset) < sizes.areas.personal.marginWidth) {
                                nodes[index].style.display = '';
                            }
                        }
                    }
                    if ((width + offset + sizes.content.logo.marginWidth) < sizes.areas.personal.marginWidth) {
                        pure.menu.C.toggle.fromPersonal();
                    }
                }
                return false;
            }
        },
        breadcrumbs : {
            node    : null,
            init    : function () {
                var node = pure.nodes.select.first('*[data-menu-engine="Breadcrumbs.Container"]');
                if (node !== null) {
                    pure.menu.C.breadcrumbs.node = node;
                    pure.menu.C.sizes.update('content', 'breadcrumbs');
                    pure.menu.C.breadcrumbs.hide();
                }
            },
            hide    : function () {
                if (pure.menu.C.breadcrumbs.node !== null) {
                    pure.menu.C.breadcrumbs.node.style.display = 'none';
                }
            },
            show    : function () {
                if (pure.menu.C.breadcrumbs.node !== null) {
                    pure.menu.C.breadcrumbs.node.style.display = 'block';
                }
            },
            isShown : function () {
                if (pure.menu.C.breadcrumbs.node !== null) {
                    return (pure.menu.C.breadcrumbs.node.style.display === 'none' ? false : true);
                }
                return false;
            },
            update  : function () {
                var sizes   = pure.menu.C.sizes.data.sizes,
                    width   = pure.menu.C.sizes.sum(sizes.content.breadcrumbs),
                    offset  = 0;
                pure.menu.C.sizes.update('areas', 'menu');
                if (width > sizes.areas.menu.marginWidth) {
                    pure.menu.C.breadcrumbs.hide();
                    pure.menu.C.toggle.toPersonal();
                }
            }
        },
        menu        : {
            node    : null,
            init    : function () {
                var node = pure.nodes.select.first('*[data-menu-engine="Menu.Container"]');
                if (node !== null) {
                    pure.menu.C.menu.node = node;
                }
            },
            hide    : function () {
                if (pure.menu.C.menu.node !== null) {
                    pure.menu.C.menu.node.style.display = 'none';
                }
            },
            show    : function () {
                if (pure.menu.C.menu.node !== null) {
                    pure.menu.C.menu.node.style.display = 'block';
                    pure.menu.C.areas.  update();
                    pure.menu.C.toggle. update();
                    pure.menu.C.toggle. menu();
                }
            },
            isShown: function () {
                if (pure.menu.C.menu.node !== null) {
                    return (pure.menu.C.menu.node.style.display === 'none' ? false : true);
                }
                return false;
            },
            applyClass : function(className){
                var node = pure.nodes.select.first('*[data-menu-engine="Menu.Global.Container"]');
                if (node !== null){
                    node.className = className;
                }
            }
        },
        scroll  : {
            flags       : {
                before  : false,
                after   : false
            },
            timerID     : false,
            social  : {
                init : function(){
                    var social      = pure.nodes.select.first('*[data-global-makeup-marks="menu-social-container"]'),
                        addition    = pure.nodes.select.first('*[data-global-makeup-marks="menu-addition-container"]');
                    if (social !== null && addition !== null){
                        pure.menu.C.scroll.social.show = function(){
                            social.style.display    = '';
                            addition.style.display  = '';
                        };
                        pure.menu.C.scroll.social.hide = function(){
                            social.style.display    = 'none';
                            addition.style.display  = 'none';
                        };
                    }
                },
                show : function(){},
                hide : function(){}
            },
            init        : function () {
                var node    = pure.nodes.select.first('*[data-menu-engine="Menu.Global.Container"]'),
                    scroll  = null,
                    height  = { target : 0, basic : 0},
                    offset  = null;
                if (node !== null) {
                    height.target   = node.getAttribute('data-menu-engine-final-height');
                    height.basic    = pure.nodes.render.size(node).marginHeight;
                    scroll          = node.getAttribute('data-menu-engine-scroll-selector'  );
                    offset          = pure.nodes.render.offset(node);
                    if (height.target !== null && scroll !== null) {
                        if (height.target.indexOf('em') !== -1) {
                            height.target = pure.nodes.convert.emToPx(parseFloat(height.target), node.parentNode);
                        }else{
                            height.target = parseFloat(height.target);
                        }
                        if (height.target > 0) {
                            scroll = (scroll.toLowerCase() === 'body' ? window : pure.nodes.select.first(scroll.replace(/\|/gi, '"')));
                            if (scroll !== null) {
                                pure.events.add(
                                    scroll,
                                    'scroll',
                                    function () {
                                        pure.menu.C.scroll.onScroll(scroll, node, height, offset.top);
                                    }
                                );
                                pure.menu.C.scroll.social.init();
                                pure.menu.C.scroll.onScroll(scroll, node, height, offset.top);
                            }
                        }
                    }
                }
            },
            onScroll    : function (scroll, node, height, offset) {
                function updateLogoArea() {
                    pure.menu.C.sizes.  update('content',   'logo');
                    pure.menu.C.sizes.  update('areas',     'logo');
                    pure.menu.C.areas.  update();
                    pure.menu.C.toggle. update();
                };
                var scrollTop   = ((typeof scroll.pageYOffset !== 'undefined' ? scroll.pageYOffset : (typeof scroll.scrollTop !== 'undefined' ? scroll.scrollTop : -1))),
                    src         = pure.menu.C.sizes.data.nodes.content.logo.src;
                if (scrollTop !== -1){
                    if (scrollTop === 0){
                        pure.menu.C.scroll.social.show();
                    }else{
                        pure.menu.C.scroll.social.hide();
                    }
                    if (scrollTop < offset) {
                        node.style.top                                              = (offset - scrollTop) + 'px';
                        node.style.height                                           = (height.basic - (height.basic - height.target) * (scrollTop / offset)) + 'px';
                        pure.menu.C.scroll.flags.after                              = false;
                        pure.menu.C.sizes.data.nodes.content.logo.style.height      = node.style.height;
                        pure.nodes.render.redraw(pure.menu.C.sizes.data.nodes.content.logo);
                        pure.nodes.render.redraw(pure.menu.C.sizes.data.nodes.areas.logo);
                        updateLogoArea();
                        if (pure.menu.C.scroll.flags.before === false) {
                            pure.menu.C.breadcrumbs.hide();
                            pure.menu.C.menu.       show();
                            pure.menu.C.menu.applyClass('');
                            pure.menu.C.scroll.flags.before = true;
                        }
                    } else {
                        if (pure.menu.C.scroll.flags.after === false) {
                            pure.menu.C.scroll.flags.after                          = true;
                            pure.menu.C.scroll.flags.before                         = false;
                            node.style.top                                          = '0px';
                            node.style.height                                       = height.target + 'px';
                            pure.menu.C.sizes.data.nodes.content.logo.style.height  = node.style.height;
                            updateLogoArea();
                            pure.menu.C.breadcrumbs.show();
                            pure.menu.C.menu.       hide();
                            pure.menu.C.menu.applyClass('PureHeaderMenuCContainerSmall');
                            pure.menu.C.breadcrumbs.update();
                        }
                    }
                }
            }
        },
        blocksCallers: {
            nodes       : null,
            opened      : false,
            init        : function () {
                var button      = pure.nodes.select.first('*[data-menu-engine-blocks="Personal.Block.Caller"]'  ),
                    background  = pure.nodes.select.first('*[data-menu-engine="Menu.Blocks.Background"]'        ),
                    container   = pure.nodes.select.first('*[data-menu-engine="Menu.Blocks.Container"]'         );
                if (button !== null && background !== null && container !== null) {
                    pure.events.add(
                        button,
                        'click',
                        function(){
                            pure.menu.C.blocksCallers.open();
                        }
                    );
                    pure.events.add(
                        window,
                        'resize',
                        function () {
                            pure.menu.C.blocksCallers.resize();
                        }
                    );
                    pure.menu.C.blocksCallers.nodes = {
                        button      : button,
                        background  : background,
                        container   : container
                    };
                    pure.menu.C.blocksCallers.initItems();
                }
            },
            initItems   : function () {
                function getAction(li) {
                    var a       = pure.nodes.find.childByType(li, 'a'),
                        href    = null;
                    if (a !== null) {
                        href = a.getAttribute('href');
                        if (href !== null && href !== '' && href !== '#') {
                            return function () { window.location.href = href; };
                        }
                    }
                    return null;
                };
                function getAttrs(li) {
                    function getAttr(node, attrName){
                        var attr = node.getAttribute(attrName);
                        if (attr !== null && attr !== '') {
                            return attr;
                        }
                        return null;
                    };
                    function check(li, attrData){
                        var attr = getAttr(li, attrData.name);
                        if (attr !== null){
                            attrs.push({name : attrData.name, value : attr, event: attrData.event});
                        }
                    };
                    var names   = [
                            {name : 'data-engine-login-form',           event: { group: 'authorization.login',          name: 'update.buttons'}},
                            {name : 'data-engine-reset-form',           event: { group: 'authorization.reset',          name: 'update.buttons'}},
                            {name : 'data-engine-registration-form',    event: { group: 'authorization.registration',   name: 'update.buttons'}},
                            {name : 'data-messenger-engine-button',     event: { group: 'messenger', name: 'buttons.update'}},
                            {name : 'data-messenger-engine-switchTo',   event: { group: 'messenger', name: 'buttons.update'}},
                        ],
                        attrs   = [],
                        a       = pure.nodes.find.childByType(li, 'a');
                    for(var index = names.length - 1; index >= 0; index -= 1){
                        check(li, names[index]);
                        if (a !== null){
                            check(a, names[index]);
                        }
                    }
                    return (attrs.length === 0 ? null : attrs);
                };
                function getLabel(li) {
                    var a       = pure.nodes.find.childByType(li, 'a'),
                        label   = null;
                    if (a !== null) {
                        label = li.getAttribute('data-menu-engine-block-label');
                        if (label === null || label === '') {
                            label = a.innerHTML;
                        }
                        if (label !== null) {
                            if (label.indexOf('<') === -1 && label.indexOf('>') === -1) {
                                return label;
                            }
                        }

                    }
                    return null;
                };
                function getChilds(parent) {
                    var items   = [],
                        item    = null,
                        sub     = null,
                        ul      = pure.nodes.find.childByType(parent, 'ul'),
                        label   = null,
                        action  = null;
                    if (ul !== null) {
                        for (var index = 0, max_index = ul.childNodes.length; index < max_index; index += 1) {
                            if (ul.childNodes[index].nodeName.toLowerCase() === 'li') {
                                label = getLabel(ul.childNodes[index]);
                                if (label !== null) {
                                    item    = {
                                        values: [{ path: "item.sub.caption.innerHTML", value: label }],
                                        action: function () { },
                                        attrs : getAttrs(ul.childNodes[index])
                                    };
                                    sub = getChilds(ul.childNodes[index]);
                                    if (sub !== null) {
                                        item.items = sub;
                                        item.values.push({ path: "item.sub.more.innerHTML", value: '...' });
                                    } else {
                                        action = getAction(ul.childNodes[index]);
                                        if (action !== null) {
                                            item.action = action;
                                        }
                                    }
                                    items.push(item);
                                }
                            }
                        }
                        return (items.length > 0 ? items : null)
                    }
                    return null;
                };
                var items = pure.nodes.select.all('li[data-menu-engine-to-block="true"]'),
                    item    = null,
                    _items  = [],
                    label   = null,
                    sub     = null,
                    action  = null;
                if (items !== null) {
                    for (var index = 0, max_index = items.length; index < max_index; index += 1) {
                        label = getLabel(items[index]);
                        if (label !== null) {
                            item = {
                                values: [{ path: "item.sub.caption.innerHTML", value: label }],
                                action: function () {  },
                                attrs : getAttrs(items[index])
                            };
                            sub = getChilds(items[index]);
                            if (sub !== null) {
                                item.items = sub;
                                item.values.push({ path: "item.sub.more.innerHTML", value: '...' });
                            } else {
                                action = getAction(items[index]);
                                if (action !== null) {
                                    item.action = action;
                                }
                            }
                            _items.push(item);
                        }
                    }
                    _items.push({
                        values: [{ path: "item.sub.caption.innerHTML", value: 'close' }],
                        action: function () {
                            pure.menu.C.blocksCallers.close();
                        }
                    });
                    pure.menu.C.sets.id000001.items = _items;
                }
            },
            open        : function(){
                var nodes = pure.menu.C.blocksCallers.nodes;
                if (nodes !== null) {
                    pure.menu.C.blocksCallers.opened    = true;
                    nodes.background.style.display      = 'block';
                    pure.menu.C.blocks.initialization.all(
                        pure.menu.C.sets,
                        '*[data-menu-engine="Menu.Blocks.Container"]'
                    );
                }
            },
            close       : function () {
                var nodes = pure.menu.C.blocksCallers.nodes;
                if (nodes !== null) {
                    pure.menu.C.blocks.initialization.destroy('id000001');
                    nodes.background.style.display      = 'none';
                    nodes.container.innerHTML           = '';
                    pure.menu.C.blocksCallers.opened    = false;
                }
            },
            resize: function () {
                if (pure.menu.C.blocksCallers.opened === true) {
                    pure.menu.C.blocksCallers.close();
                }
            }
        }
    };
    pure.system.start.add(pure.menu.C.init);
}());
(function () {
    if (typeof window.pure          !== "object") { window.pure         = {}; }
    if (typeof window.pure.menu     !== "object") { window.pure.menu    = {}; }
    if (typeof window.pure.menu.C   !== "object") { window.pure.menu.C  = {}; }
    "use strict";
    window.pure.menu.C.blocks = {
        storage: {
            group   : 'Convis_Menu_Sets',
            data    : {},
            set     : function (params) {
                if (pure.tools.objects.validate(params, [   { name: "parent",       type: "node"    },
                        { name: "templates",    type: "object"  },
                        { name: "items",        type: "array"   },
                        { name: "id",           type: "string", value: "id" + Math.round(Math.random() * 1000000).toString }]) === true) {
                    if (pure.tools.objects.validate(params.templates, [ { name: "item", type: "object" },
                            { name: "back", type: "object" },
                            { name: "home", type: "object" }]) === true) {
                        pure.menu.C.blocks.storage.data[params.id] = {
                            parent      : params.parent,
                            templates   : params.templates,
                            items       : params.items,
                            id          : params.id
                        };
                        return pure.menu.C.blocks.storage.data[params.id];
                    }
                }
                return null;
            },
            get     : function (id) {
                if (typeof pure.menu.C.blocks.storage.data[id] !== 'undefined') {
                    return pure.menu.C.blocks.storage.data[id];
                }
                return null;
            },
            destroy : function(id){
                if (typeof pure.menu.C.blocks.storage.data[id] !== 'undefined') {
                    pure.menu.C.blocks.storage.data[id] = null;
                    delete pure.menu.C.blocks.storage.data[id];
                }
            },
            getByPath: function (instance, path) {
                var current = instance;
                for (var index = 0, max_index = path.length; index < max_index; index += 1) {
                    if (current.items instanceof Array === true) {
                        if (typeof current.items[path[index]] === "object") {
                            current = current.items[path[index]];
                        } else {
                            current = null; break;
                        }
                    } else {
                        current = null; break;
                    }
                }
                return current;
            }
        },
        initialization: {
            all: function (sets, selector) {
                var parents = pure.nodes.select.all(selector),
                    id = null;
                if (typeof sets === "object") {
                    for (var index = parents.length - 1; index >= 0; index -= 1) {
                        id = parents[index].getAttribute("data-engine-dataset");
                        if (id !== null) {
                            if (typeof sets[id] === "object") {
                                pure.menu.C.blocks.initialization.make({
                                    id          : id,
                                    parent      : parents[index],
                                    templates   : sets[id].templates,
                                    items       : sets[id].items
                                });
                            }
                        }
                    }
                }
            },
            make: function (params) {
                var instance        = pure.menu.C.blocks.storage.set(params),
                    nodes           = null;
                if (instance !== null) {
                    if (pure.menu.C.blocks.render.build(instance.id, []) !== null) {
                        pure.menu.C.blocks.events.append(instance.id, []);
                        pure.menu.C.blocks.render.append(instance.id);
                        pure.menu.C.blocks.render.sizer.set(instance.id);
                    }
                }
            },
            destroy: function (id) {
                pure.menu.C.blocks.storage.destroy(id);
            }
        },
        render: {
            build   : function (id, path) {
                function setItemValue(path, node, valueItem) {
                    var current = node;
                    path = path.split('.');
                    for (var index = 0, max_index = path.length - 1; index < max_index; index += 1) {
                        if (typeof current[path[index]] !== "undefined") {
                            current = current[path[index]];
                        } else {
                            current = null; break;
                        }
                    }
                    if (current !== null) {
                        if (typeof current[path[path.length - 1]] !== "undefined") {
                            current[path[path.length - 1]] = valueItem;
                            return true;
                        }
                    }
                    return false;
                };
                var instance    = pure.menu.C.blocks.storage.get(id),
                    path        = (path instanceof Array === true ? path : null),
                    nodes       = null,
                    current     = null,
                    events      = {
                        journal : [],
                        list    : []
                    };
                if (instance !== null && path !== null) {
                    current = pure.menu.C.blocks.storage.getByPath(instance, path);
                    if (current !== null) {
                        if (current.items instanceof Array === true) {
                            instance.nodes  = [];
                            nodes           = instance.nodes;
                            for (var index = 0, max_index = current.items.length; index < max_index; index += 1) {
                                nodes.push(null);
                                nodes[nodes.length - 1] = pure.nodes.builder(instance.templates.item);
                                if (current.items[index].values instanceof Array === true) {
                                    for (var vIndex = current.items[index].values.length - 1; vIndex >= 0; vIndex -= 1) {
                                        if (typeof current.items[index].values[vIndex].path     === "string" &&
                                            typeof current.items[index].values[vIndex].value    === "string") {
                                            if (setItemValue(current.items[index].values[vIndex].path,
                                                    nodes[nodes.length - 1],
                                                    current.items[index].values[vIndex].value) === false) {
                                                return null;
                                            }else{
                                                if (current.items[index].attrs instanceof Array){
                                                    Array.prototype.forEach.call(
                                                        current.items[index].attrs,
                                                        function (attr, _index, source) {
                                                            nodes[nodes.length - 1].container.setAttribute(attr.name, attr.value);
                                                            if (attr.event !== null){
                                                                if (events.journal.indexOf(attr.event.group + attr.event.name) === -1){
                                                                    events.list.push(attr.event);
                                                                }
                                                            }
                                                        }
                                                    );
                                                }
                                            }
                                        } else {
                                            return null;
                                        }
                                    }
                                }
                            }
                            if (path.length === 1 || path.length === 2) {
                                nodes.push(null);
                                nodes[nodes.length - 1] = pure.nodes.builder(instance.templates.back);
                            }
                            if (path.length === 2) {
                                nodes.push(null);
                                nodes[nodes.length - 1] = pure.nodes.builder(instance.templates.home);
                            }
                            if (events.list.length > 0){
                                instance.events = function(){
                                    Array.prototype.forEach.call(
                                        events.list,
                                        function(event, _index, source){
                                            pure.appevents.Actions.call(
                                                event.group,
                                                event.name,
                                                null,
                                                null
                                            );
                                        }
                                    );
                                };
                            }else{
                                instance.events = null;
                            }
                            return true;
                        }
                    }
                }
                return null;
            },
            append  : function (id) {
                var instance = pure.menu.C.blocks.storage.get(id);
                if (instance !== null) {
                    for (var index = 0, max_index = instance.nodes.length; index < max_index; index += 1) {
                        instance.parent.appendChild(instance.nodes[index].container);
                    }
                    setTimeout(
                        function(){
                            if (instance){
                                if (typeof instance.events === "function"){
                                    instance.events();
                                }
                            }
                        },
                        100
                    );
                }
            },
            remove: function (id) {
                var instance = pure.menu.C.blocks.storage.get(id);
                if (instance !== null) {
                    for (var index = 0, max_index = instance.nodes.length; index < max_index; index += 1) {
                        instance.nodes[index].container.style.opacity = 0;
                        (function (node) {
                            setTimeout(function () { node.parentNode.removeChild(node); }, 600);
                        }(instance.nodes[index].container));
                    }
                }
            },
            sizer: {
                set: function (id, parent) {
                    function byRows() {
                        var index = (parent === null ? 0 : instance.nodes.length - 1);
                        for (var row = 0; row < rows; row += 1) {
                            position.left = 0;
                            position.top = row * cell.height;
                            if (row === rows - 1) {
                                if (lastcell.status === true) {
                                    columns += 1;
                                }
                            }
                            for (var column = 0; column < columns; column += 1) {
                                position.left = (row !== rows - 1 ? column * cell.width : column * lastcell.width);
                                if (typeof instance.nodes[index] === "object") {
                                    if (typeof instance.nodes[index].container !== "undefined") {
                                        instance.nodes[index].container.style.position = "absolute";
                                        instance.nodes[index].container.style.opacity = (parent === null ? 1 : 0);
                                        if (row !== rows - 1) {
                                            instance.nodes[index].container.style.width = cell.width + "px";
                                            instance.nodes[index].container.style.height = cell.height + "px";
                                        } else {
                                            instance.nodes[index].container.style.width = lastcell.width + "px";
                                            instance.nodes[index].container.style.height = lastcell.height + "px";
                                        }
                                        instance.nodes[index].container.style.top = (position.top + offset.top) + "px";
                                        instance.nodes[index].container.style.left = (position.left + offset.left) + "px";
                                        index = (parent === null ? index + 1 : index - 1);
                                    }
                                }
                            }
                        }
                    };
                    function byColumns() {
                        var index = 0;
                        for (var column = 0; column < columns; column += 1) {
                            position.left = column * cell.width;
                            position.top = 0;
                            if (column === columns - 1) {
                                if (lastcell.status === true) {
                                    rows += 1;
                                }
                            }
                            for (var row = 0; row < rows; row += 1) {
                                position.top = (column !== columns - 1 ? row * cell.height : row * lastcell.height);
                                if (typeof instance.nodes[index] === "object") {
                                    if (typeof instance.nodes[index].container !== "undefined") {
                                        instance.nodes[index].container.style.position = "absolute";
                                        instance.nodes[index].container.style.opacity = (parent === null ? 1 : 0);
                                        if (column !== columns - 1) {
                                            instance.nodes[index].container.style.width = cell.width + "px";
                                            instance.nodes[index].container.style.height = cell.height + "px";
                                        } else {
                                            instance.nodes[index].container.style.width = lastcell.width + "px";
                                            instance.nodes[index].container.style.height = lastcell.height + "px";
                                        }
                                        instance.nodes[index].container.style.top = (position.top + offset.top) + "px";
                                        instance.nodes[index].container.style.left = (position.left + offset.left) + "px";
                                        index += 1;
                                    }
                                }
                            }
                        }
                    }
                    var instance    = pure.menu.C.blocks.storage.get(id),
                        size        = null,
                        columns     = 0,
                        rows        = 0,
                        sqrt        = 0,
                        cell        = {},
                        lastcell    = {},
                        position    = { left: 0, top: 0 },
                        parent      = (typeof parent === "object" ? parent : null),
                        offset      = { left: 0, top: 0 };
                    if (instance !== null) {
                        size        = pure.nodes.render.size((parent === null ? instance.parent : parent));
                        sqrt        = Math.sqrt(instance.nodes.length);
                        offset.top  = (parent === null ? 0 : parseInt(parent.style.top, 10));
                        offset.left = (parent === null ? 0 : parseInt(parent.style.left, 10));
                        if (sqrt !== Math.floor(sqrt)) {
                            //rows !== columns
                            if (size.width >= size.height) {
                                rows        = Math.floor(sqrt);
                                columns     = Math.floor(instance.nodes.length / rows);
                                lastcell    = {
                                    width   : Math.min(size.width / (columns + (instance.nodes.length - rows * columns)),size.height / rows),
                                    height  : Math.min(size.width / (columns + (instance.nodes.length - rows * columns)),size.height / rows),
                                    status  : true
                                };
                                cell        = {
                                    width   : lastcell.width,
                                    height  : lastcell.height
                                };
                                byRows();
                            } else {
                                columns     = Math.floor(sqrt);
                                rows        = Math.floor(instance.nodes.length / columns);
                                lastcell    = {
                                    width   : Math.min(size.width / columns,size.height / (rows + (instance.nodes.length - rows * columns))),
                                    height  : Math.min(size.width / columns,size.height / (rows + (instance.nodes.length - rows * columns))),
                                    status  : true
                                };
                                cell        = {
                                    width   : lastcell.width,
                                    height  : lastcell.height
                                };
                                byColumns();
                            }
                        } else {
                            //rows === columns
                            columns = sqrt;
                            rows    = sqrt;
                            lastcell = {
                                width   : size.width / columns,
                                height  : size.height / rows,
                                status  : false
                            };
                            cell = {
                                width   : size.width / columns,
                                height  : size.height / rows
                            };
                            byRows();
                        }
                    }
                }
            }
        },
        events: {
            append: function (id, path) {
                var instance    = pure.menu.C.blocks.storage.get(id),
                    path        = (path instanceof Array === true ? path : null),
                    current     = null,
                    types       = { back: false, home: false },
                    type        = "";
                if (instance !== null && path !== null) {
                    current = pure.menu.C.blocks.storage.getByPath(instance, path);
                    for (var index = 0, max_index = instance.nodes.length; index < max_index; index += 1) {
                        if (typeof instance.nodes[index].container !== "undefined") {
                            type = (index >= current.items.length ? (types.back === false ? "back" : "home") : "");
                            types.back = (type === "back" ? true : types.back);
                            types.home = (type === "home" ? true : types.home);
                            (function (path_argument, node, index, id, type) {
                                var path = pure.tools.arrays.copy(path_argument);
                                path.push(index);
                                pure.events.add(
                                    node,
                                    'click',
                                    function (event) {
                                        pure.menu.C.blocks.events.actions.click(event, id, path, type, node);
                                    }
                                );
                            }(path, instance.nodes[index].container, index, id, type));
                        }
                    }
                }
            },
            actions: {
                click: function (event, id, path, type, node) {
                    function getItem(instance, path) {
                        var item = instance;
                        for (var index = 0, max_index = path.length; index < max_index; index += 1) {
                            item = item.items[path[index]];
                        }
                        return item;
                    };
                    function handle(instance, path) {
                        var item = getItem(instance, path);
                        if (typeof item.action === 'function') {
                            pure.system.runHandle(item.action, null, '[Menu C]::: pure.menu.C.blocks.events.actions.click', this);
                        }
                    };
                    var instance = pure.menu.C.blocks.storage.get(id),
                        current = null;
                    if (instance !== null) {
                        current = pure.menu.C.blocks.storage.getByPath(instance, path);
                        if (current !== null || type === "back" || type === "home") {
                            if (type !== "back" && type !== "home" && current !== null) {
                                if (current.items instanceof Array === false) {
                                    handle(instance, path);
                                    return false;
                                }
                            }
                            pure.menu.C.blocks.render.remove(id);
                            switch (type) {
                                case "back":
                                    path.splice(path.length - 2, 2);
                                    break;
                                case "home":
                                    path = [];
                                    break;
                            }
                            if (pure.menu.C.blocks.render.build(id, path) === true) {
                                pure.menu.C.blocks.events.append(id, path);
                                pure.menu.C.blocks.render.sizer.set(id, node);
                                pure.menu.C.blocks.render.append(id);
                                pure.menu.C.blocks.render.sizer.set(id);
                            }
                        }
                    }
                }
            }
        }

    };
}());
(function () {
    if (typeof window.pure          !== "object") { window.pure         = {}; }
    if (typeof window.pure.menu     !== "object") { window.pure.menu    = {}; }
    if (typeof window.pure.menu.C   !== "object") { window.pure.menu.C  = {}; }
    "use strict";
    window.pure.menu.C.sets = {
        id000001: {
            templates: {
                item: {
                    container   : { node: "DIV", name: "data-element-type", value: "Pure.Menu.Blocks.Item.Container" },
                    item        : {
                        container   : { node: "DIV", name: "data-element-type", value: "Pure.Menu.Blocks.Item" },
                        sub         : {
                            container   : { node: "DIV", name: "data-element-type", value: "Pure.Menu.Blocks.Item.Sub" },
                            caption     : { node: "P", name: "data-element-type", value: "Pure.Menu.Blocks.Item" },
                            more        : { node: "P", name: "data-element-type", value: "Pure.Menu.Blocks.Item.More" },
                            settingup: {
                                parent  : "container",
                                childs  : ["caption", "more"]
                            }
                        },
                        settingup   : {
                            parent  : "container",
                            childs  : ["sub"]
                        }
                    },
                    settingup   : {
                        parent  : "container",
                        childs  : ["item"]
                    }
                },
                back: {
                    container   : { node: "DIV", name: "data-element-type", value: "Pure.Menu.Blocks.Item.Container" },
                    item        : {
                        container   : { node: "DIV", name: "data-element-type", value: "Pure.Menu.Blocks.Item.Service" },
                        sub         : {
                            container   : { node: "DIV", name: "data-element-type", value: "Pure.Menu.Blocks.Item.Sub" },
                            caption     : { node: "P", name: "data-element-type", value: "Pure.Menu.Blocks.Item.Service", html: "back" },
                            settingup   : {
                                parent  : "container",
                                childs  : ["caption"]
                            }
                        },
                        settingup   : {
                            parent  : "container",
                            childs  : ["sub"]
                        }
                    },
                    settingup   : {
                        parent  : "container",
                        childs  : ["item"]
                    }
                },
                home: {
                    container       : { node: "DIV", name: "data-element-type", value: "Pure.Menu.Blocks.Item.Container" },
                    item            : {
                        container   : { node: "DIV", name: "data-element-type", value: "Pure.Menu.Blocks.Item.Service" },
                        sub         : {
                            container   : { node: "DIV", name: "data-element-type", value: "Pure.Menu.Blocks.Item.Sub" },
                            caption     : { node: "P", name: "data-element-type", value: "Pure.Menu.Blocks.Item.Service", html:"home" },
                            settingup   : {
                                parent  : "container",
                                childs  : ["caption"]
                            }
                        },
                        settingup   : {
                            parent  : "container",
                            childs  : ["sub"]
                        }
                    },
                    settingup   : {
                        parent  : "container",
                        childs  : ["item"]
                    }
                }
            },
            items: []
        }

    };
}());