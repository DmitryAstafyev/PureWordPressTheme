(function () {
    if (typeof window.pure          !== "object") { window.pure         = {}; }
    if (typeof window.pure.sliders  !== "object") { window.pure.sliders = {}; }
    "use strict";
    window.pure.sliders.B = {
        data: {
            storage : { },
            set : function (id, nodes, count, size) {
                var id              = (typeof id === "string" ? id : pure.tools.IDs.get("slider.B.ID.")),
                    current_record  = null;
                if (typeof pure.sliders.B.data.storage[id] !== "object") {
                    pure.sliders.B.data.storage[id] = {};
                }
                current_record          = pure.sliders.B.data.storage[id];
                current_record.id       = id;
                current_record.nodes    = nodes;
                current_record.frames   = {};
                current_record.count    = count;
                current_record.size     = size;
                current_record.current  = 0;
                return current_record;
            },
            get : function (id) {
                return (typeof pure.sliders.B.data.storage[id] === "object" ? pure.sliders.B.data.storage[id] : null);
            }
        },
        init    : function () {
            function set(instance) {
                function getSize(parent, node){
                    //data-engine-element="parent"
                    var size    = null,
                        mark    = document.createElement("DIV");
                    if (parent !== null){
                        parent.parentNode.insertBefore(mark, parent);
                        document.body.appendChild(parent);
                        size = pure.nodes.render.size(node);
                        mark.parentNode.insertBefore(parent, mark);
                        mark.parentNode.removeChild(mark);
                        return size;
                    }
                    return null;
                };
                var id              = null,
                    id_attribute    = "data-engine-element-id",
                    nodes           = {
                        buttons             : { left: null, right: null },
                        container           : null,
                        contentContainer    : null,
                        content             : null,
                        items               : null
                    },
                    size            = null,
                    parent          = pure.nodes.find.parentByAttr(instance, {name : 'data-engine-element', value: 'parent'}),
                    resize          = instance.getAttribute('data-engine-windowresize');
                if (parent !== null){
                    resize                  = (resize !== null ? (resize === 'true' ? true : false) : false);
                    id                      = pure.tools.IDs.get("slider.B.ID.");
                    instance.setAttribute(id_attribute,             id      );
                    instance.setAttribute('data-element-inited',    'true'  );
                    nodes.container         = instance;
                    nodes.contentContainer  = pure.nodes.select.first   ("div[data-engine-element=\"Slider.B\"][" + id_attribute + "=\"" + id + "\"] div[data-engine-type=\"Slider.B.Content.Container\"]"  );
                    nodes.content           = pure.nodes.select.first   ("div[data-engine-element=\"Slider.B\"][" + id_attribute + "=\"" + id + "\"] div[data-engine-type=\"Slider.B.Content\"]"            );
                    nodes.items             = pure.nodes.select.all     ("div[data-engine-element=\"Slider.B\"][" + id_attribute + "=\"" + id + "\"] div[data-engine-type=\"Slider.B.Item\"]"               );
                    nodes.buttons.left      = pure.nodes.find.childByAttr(parent, '*', {name :'data-engine-type', value: 'Slider.B.Button.Left'});
                    nodes.buttons.right     = pure.nodes.find.childByAttr(parent, '*', {name :'data-engine-type', value: 'Slider.B.Button.Right'});
                    if (nodes.content           !== null && nodes.items         !== null &&
                        (nodes.buttons.left     !== null || nodes.buttons.right !== null) ) {
                        if (typeof nodes.items.length === "number") {
                            size = pure.nodes.render.size(nodes.contentContainer);
                            if (size.width === 0 || size.height === 0){
                                size = getSize(parent, instance.parentNode);
                            }
                            if (size !== null){
                                //Set sizes of items
                                for (var item_index = 0, max_item_index = nodes.items.length; item_index < max_item_index; item_index += 1) {
                                    nodes.items[item_index].style.width     = size.width    + 'px';
                                    //nodes.items[item_index].style.height    = size.height   + 'px';
                                }
                                //Set resize (if necessary)
                                if (resize === true){
                                    pure.sliders.B.WindowsResize.handles.add(
                                        function(){
                                            pure.sliders.B.actions.update(id);
                                        }
                                    );
                                }
                                //Set global sidebar event
                                pure.appevents.Actions.listen(
                                    'global.layout.sidebar',
                                    'update',
                                    function(){
                                        pure.sliders.B.actions.update(id);
                                        pure.appevents.Actions.call('effects.fixscroll.sidebar', 'force');
                                    },
                                    'pure.sliders.B.WindowsResize.resize'
                                );
                                //Save data and return instance
                                return pure.sliders.B.data.set(id, nodes, nodes.items.length, size);
                            }
                        }
                    }
                }
                return null;
            };
            var instances   = pure.nodes.select.all("div[data-engine-element=\"Slider.B\"]:not([data-element-inited])"),
                record      = null;
            if (instances !== null) {
                if (typeof instances.length === "number") {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        record = set(instances[index]);
                        if (record !== null) {
                            pure.sliders.B.actions.init     (record.id, null);
                            pure.sliders.B.actions.resize   (record.id, true);
                        }
                    }
                }
            }
            pure.sliders.B.More.init();
        },
        actions         : {
            init    : function (id) {
                var data = pure.sliders.B.data.get(id);
                if (data !== null) {
                    pure.appevents.Actions.listen(
                        'pure.positioning',
                        'resize',
                        function () { pure.sliders.B.actions.update(id); },
                        id
                    );
                    if (data.nodes.buttons.left !== null){
                        pure.events.add(data.nodes.buttons.left,    "click",    function (event) { pure.sliders.B.actions.left      (event, id);    });
                    }
                    if (data.nodes.buttons.right !== null){
                        pure.events.add(data.nodes.buttons.right,   "click",    function (event) { pure.sliders.B.actions.right     (event, id);    });
                    }
                }
            },
            right   : function (event, id) {
                var data = pure.sliders.B.data.get(id);
                if (data !== null) {
                    if (-data.current < data.count * data.size.width) {
                        data.current -= data.size.width;
                        if (-data.current >= data.count * data.size.width){
                            data.current = 0;
                        }
                        pure.sliders.B.actions.show(id);
                    }
                }
                pure.tools.selections.clear();
                event.stopPropagation ? event.stopPropagation() : (event.cancelBubble = true);
            },
            left    : function (event, id) {
                var data = pure.sliders.B.data.get(id);
                if (data !== null) {
                    if (data.current < 0) {
                        data.current += data.size.width;
                    }
                    pure.sliders.B.actions.show(id);
                }
                pure.tools.selections.clear();
                event.stopPropagation ? event.stopPropagation() : (event.cancelBubble = true);
            },
            resize  : function (id, force) {
                var data    = pure.sliders.B.data.get(id),
                    force   = (typeof force === 'boolean' ? force : false);
                if (data !== null) {
                    pure.sliders.B.actions.update(id, force);
                }
            },
            show    : function (id) {
                var data = pure.sliders.B.data.get(id);
                if (data !== null) {
                    data.nodes.content.style.left = data.current + 'px';
                }
            },
            update  : function(id, force){
                function checkFrames(item_index, data, size){
                    var frame = null;
                    if (typeof data.frames[item_index] === 'undefined'){
                        frame                   = pure.nodes.find.childByType(data.nodes.items[item_index], 'IFRAME');
                        data.frames[item_index] = (frame === null ? false : frame);
                    }
                    if (data.frames[item_index] !== false){
                        if (data.frames[item_index].getAttribute('width') !== ''){
                            data.frames[item_index].setAttribute('width', Math.round(size.width));
                        }
                    }
                };
                function getSize(data){
                    var size = null;
                    data.nodes.content.style.display    = 'none';
                    pure.nodes.render.redraw(data.nodes.contentContainer);
                    size                                = pure.nodes.render.size(data.nodes.contentContainer);
                    data.nodes.content.style.display    = '';
                    return size;
                }
                var data    = pure.sliders.B.data.get(id),
                    force   = (typeof force === 'boolean' ? force : false),
                    size    = null;
                if (data !== null) {
                    size    = getSize(data);
                    if (size.width !== data.size.width || size.height !== data.size.height || force === true){
                        data.size.width     = size.width;
                        data.size.height    = size.height;
                        //Set sizes of items
                        for (var item_index = 0, max_item_index = data.nodes.items.length; item_index < max_item_index; item_index += 1) {
                            data.nodes.items[item_index].style.width        = size.width    + 'px';
                            checkFrames(item_index, data, size);
                        }
                        data.current                    = Math.round(data.current / size.width);
                        data.current                    = (-data.current >= data.count ? -(data.count - 1) : data.current);
                        data.current                    = (isNaN(data.current) === true ? 0 : data.current);
                        data.current                    = data.current * size.width;
                        data.nodes.content.style.left   = data.current + 'px';
                    }
                }
            }
        },
        More            : {
            initialized : false,
            init        : function(){
                if (pure.sliders.B.More.initialized === false){
                    pure.appevents.Actions.listen('pure.more',              'done',     function(){ pure.sliders.B.init(); }, 'pure.sliders.B.init');
                    pure.appevents.Actions.listen('pure.positioning',       'new',      function(){ pure.sliders.B.init(); }, 'pure.sliders.B.init');
                    pure.sliders.B.More.initialized = true;
                }
            }
        },
        WindowsResize   : {
            handles : {
                data    : [],
                add     : function(handle){
                    if (pure.sliders.B.WindowsResize.init !== null){
                        pure.sliders.B.WindowsResize.init();
                        pure.sliders.B.WindowsResize.init = null;
                    }
                    pure.sliders.B.WindowsResize.handles.data.push(handle);
                },
                get : function(){
                    return pure.sliders.B.WindowsResize.handles.data;
                }
            },
            inited  : false,
            init    : function(){
                if (pure.sliders.B.WindowsResize.inited === false){
                    pure.events.add(window,"resize", pure.sliders.B.WindowsResize.resize);
                    pure.sliders.B.WindowsResize.inited = true;
                }
            },
            resize  : function(event){
                var handles = pure.sliders.B.WindowsResize.handles.get();
                for(var index = handles.length - 1; index >= 0; index -= 1){
                    pure.system.runHandle(handles[index], null, '', event);
                }
            }
        },
        getID           : function(parent, do_init){
            var instance    = pure.nodes.find.childByAttr(parent, 'div', { name : 'data-engine-element-id', value: null }),
                do_init     = typeof do_init === 'boolean' ? do_init : true;
            if (instance !== null){
                return instance.getAttribute('data-engine-element-id');
            }else{
                if (do_init){
                    //Try to init sliders (maybe it's new one)
                    pure.sliders.B.init();
                    return pure.sliders.B.getID(parent, false);
                }
            }
            return null;
        },
        getCurrentPosition  : function(sliderID){
            var data = pure.sliders.B.data.get(sliderID);
            return (data !== null ? -Math.round(data.current / data.size.width) : null);
        },
        update              : function(sliderID){
            pure.sliders.B.actions.update(sliderID);
        }
    };
    pure.system.start.add(pure.sliders.B.init);
}());