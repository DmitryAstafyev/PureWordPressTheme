(function () {
    if (typeof window.pure                      !== "object") { window.pure                     = {}; }
    if (typeof window.pure.components           !== "object") { window.pure.components          = {}; }
    if (typeof window.pure.components.slider    !== "object") { window.pure.components.slider   = {}; }
    "use strict";
    /*
    CONTROLS
    ELEMENT                 ATTRIBUTE                           VALUE                               INFO
    Element container       data-type-use                       Pure.Components.Slider.JCrop
                            data-type-slider-ID                                                     ID of element
                            data-slider-handle-onchange                                             this handle will be called on change
                            data-slider-handle-onchange-param                                       parameter for [change] handle
                            data-slider-handle-onfinish                                             this handle will be called on finish changing
                            data-slider-handle-onfinish-param                                       parameter for [finish changing] handle
    background of line      data-type-slider                    line
    progress line           data-type-slider                    progress
                            data-type-slider-position                                               current position
    pointer                 data-type-slider                    pointer
                            data-type-slider-position                                               current position
    addition progress bar   data-type-slider-ghost              some name of bar                    this bar is only for information, user cannot click on it or change
     */
    window.pure.components.slider.A = {
        Storage : {
            data    : {},
            set     : function(id, nodes, handles, current, ghosts){
                var data = pure.components.slider.A.Storage.data;
                if (typeof data[id] !== 'object'){
                    data[id] = {
                        id                      : id,
                        nodes                   : {
                            container   : nodes.container,
                            cover       : nodes.cover,
                            line        : nodes.line,
                            progress    : nodes.progress,
                            pointer     : nodes.pointer
                        },
                        handles                 : {
                            onChange        : handles.onChange,
                            onChangeParam   : handles.onChangeParam,
                            onFinish        : handles.onFinish,
                            onFinishParam   : handles.onFinishParam
                        },
                        ghosts                  : ghosts,
                        current                 : current,
                        currentPointerEvent     : null,
                        currentProgressEvent    : null
                    };
                    return data[id];
                }
                return false;
            },
            get     : function(id){
                return (typeof pure.components.slider.A.Storage.data[id] === 'object' ? pure.components.slider.A.Storage.data[id] : null);
            }
        },
        init : function(){
            function initInstance(instance){
                function getHandles(instance){
                    var handles = {
                        onChange        : instance.getAttribute('data-slider-handle-onchange'       ),
                        onChangeParam   : instance.getAttribute('data-slider-handle-onchange-param' ),
                        onFinish        : instance.getAttribute('data-slider-handle-onfinish'       ),
                        onFinishParam   : instance.getAttribute('data-slider-handle-onfinish-param' )
                    };
                    handles.onChange        = (typeof handles.onChange      === 'string' ? (handles.onChange        !== '' ? handles.onChange       : null) : null);
                    handles.onChangeParam   = (typeof handles.onChangeParam === 'string' ? (handles.onChangeParam   !== '' ? handles.onChangeParam  : null) : null);
                    handles.onFinish        = (typeof handles.onFinish      === 'string' ? (handles.onFinish        !== '' ? handles.onFinish       : null) : null);
                    handles.onFinishParam   = (typeof handles.onFinishParam === 'string' ? (handles.onFinishParam   !== '' ? handles.onFinishParam  : null) : null);
                    handles.onChange        = (handles.onChange === null ? null : (typeof pure.system.getInstanceByPath(handles.onChange) === 'function' ? pure.system.getInstanceByPath(handles.onChange) : handles.onChange));
                    handles.onFinish        = (handles.onFinish === null ? null : (typeof pure.system.getInstanceByPath(handles.onFinish) === 'function' ? pure.system.getInstanceByPath(handles.onFinish) : handles.onFinish));
                    return handles;
                };
                function getID(instance){
                    var id          = instance.getAttribute('data-type-slider-ID'),
                        parentID    = null;
                    if (typeof id === 'string' && id !== ''){
                        return id;
                    }
                    parentID = pure.tools.IDs.getGlobalParentID(instance);
                    return (parentID !== null ? parentID : pure.tools.IDs.get('Pure.Components.Slider.A'));
                };
                function getGhosts(id){
                    var nodes   = pure.nodes.select.all('*[data-type-slider-ID="' + id + '"] *[data-type-slider-ghost]'),
                        name    = null,
                        ghosts  = {};
                    if (nodes !== null){
                        for (var index = nodes.length - 1; index >= 0; index -= 1){
                            name = nodes[index].getAttribute('data-type-slider-ghost');
                            if (typeof name === 'string'){
                                name = name.replace(/\W/, '_');
                                if (name !== ''){
                                    if (typeof ghosts[name] === 'undefined'){
                                        ghosts[name] = (function(node){ return node;}(nodes[index]));
                                    }
                                }
                            }
                        }
                    }
                    return ghosts;
                };
                function addCover(nodes){
                    nodes.cover                     = document.createElement('DIV');
                    nodes.cover.style.position      = 'absolute';
                    nodes.cover.style.width         = '100%';
                    nodes.cover.style.height        = '100%';
                    nodes.cover.style.top           = '0';
                    nodes.cover.style.left          = '0';
                    nodes.cover.style.background    = 'rgba(255,255,255,0);';
                    nodes.progress.parentNode.insertBefore(nodes.cover, nodes.progress);
                };
                var id      = getID(instance),
                    nodes   = {
                        container   : null,
                        cover       : null,
                        line        : null,
                        progress    : null,
                        pointer     : null
                    },
                    handles = getHandles(instance),
                    current = null;
                instance.setAttribute('data-type-slider-ID', id);
                nodes.container = instance;
                nodes.line      = pure.nodes.select.first('*[data-type-slider-ID="' + id + '"] *[data-type-slider="line"]'      );
                nodes.progress  = pure.nodes.select.first('*[data-type-slider-ID="' + id + '"] *[data-type-slider="progress"]'  );
                nodes.pointer   = pure.nodes.select.first('*[data-type-slider-ID="' + id + '"] *[data-type-slider="pointer"]'   );
                if (nodes.line === null){
                    if (instance.getAttribute('data-type-slider') === 'line'){
                        nodes.line = instance;
                    }
                }
                if (nodes.line !== null && (nodes.progress !== null || nodes.pointer !== null)){
                    if (nodes.progress !== null){
                        current = pure.components.slider.A.Position.values.get(nodes.progress);
                        addCover(nodes);
                    }
                    if (nodes.pointer !== null && current !== null){
                        current = pure.components.slider.A.Position.values.get(nodes.pointer);
                    }
                    current = (current === null ? 0 : current);
                    if (pure.components.slider.A.Storage.set(id, nodes, handles, current, getGhosts(id)) !== false){
                        //Make cover
                        instance.setAttribute('data-type-component-inited', 'true');
                        if (nodes.progress !== null){
                            pure.components.slider.A.Position.values.set(nodes.progress,    current);
                        }
                        if (nodes.pointer !== null){
                            pure.components.slider.A.Position.values.set(nodes.pointer,     current);
                        }
                        pure.components.slider.A.Events.attach(id, nodes);
                        return true;
                    }
                }
                instance.setAttribute('data-type-component-inited', 'fail'  );
                return false;
            };
            var instances = pure.nodes.select.all('*[data-type-use="Pure.Components.Slider.A"]:not([data-type-component-inited])');
            if (instances !== null){
                for (var index = instances.length - 1; index >= 0; index -= 1){
                    initInstance(instances[index]);
                }
            }
            pure.components.slider.A.More.init();
        },
        Position : {
            values : {
                property    : 'data-type-slider-position',
                get         : function(node){
                    var current = node.getAttribute(pure.components.slider.A.Position.values.property);
                    return (current === '' ? null : (current === null ? null : parseFloat(current)));
                },
                set         : function(node, value){
                    node.setAttribute(pure.components.slider.A.Position.values.property, value.toString());
                }
            },
            update : function(instance){
                var instance = (typeof instance === 'object' ? instance : pure.components.slider.A.Storage.get(instance));
                if (instance !== null){
                    if (instance.nodes.progress !== null){
                        instance.nodes.progress.style.width = (instance.current * 100) + '%';
                    }
                    if (instance.nodes.pointer !== null){
                        instance.nodes.pointer.style.left = (instance.current * 100) + '%';
                    }
                }
            }
        },
        Ghost : {
            update : function(instance, ghost, value){
                var instance = (typeof instance === 'object' ? instance : pure.components.slider.A.Storage.get(instance));
                if (instance !== null){
                    if (typeof instance.ghosts[ghost] !== 'undefined'){
                        instance.ghosts[ghost].style.width = (value * 100) + '%';
                    }
                }
            }
        },
        Events : {
            attach : function(id, nodes){
                pure.events.add((nodes.cover !== null ? nodes.cover : nodes.line), 'click', function(event){ pure.components.slider.A.Events.actions.line.click(id, event);});
                if (nodes.progress !== null){
                    pure.events.add(nodes.progress, 'mousedown',    function(event){ pure.components.slider.A.Events.actions.progress.mousedown (id, event);});
                    pure.events.add(window,         'mouseup',      function(event){ pure.components.slider.A.Events.actions.progress.mouseup   (id, event);});
                    pure.events.add(window,         'mousemove',    function(event){ pure.components.slider.A.Events.actions.progress.mousemove (id, event);});
                }
                if (nodes.pointer !== null){
                    pure.events.add(nodes.pointer,  'mousedown',    function(event){ pure.components.slider.A.Events.actions.pointer.mousedown   (id, event);});
                    pure.events.add(window,         'mouseup',      function(event){ pure.components.slider.A.Events.actions.pointer.mouseup     (id, event);});
                    pure.events.add(window,         'mousemove',    function(event){ pure.components.slider.A.Events.actions.pointer.mousemove   (id, event);});
                }
            },
            actions : {
                line        : {
                    click   : function(id, event){
                        var instance    = pure.components.slider.A.Storage.get(id),
                            size        = null;
                        if (instance !== null){
                            if (instance.nodes.line === event.target || instance.nodes.cover === event.target){
                                size = pure.nodes.render.size(instance.nodes.line);
                                instance.current = event._offsetX / size.width;
                                pure.components.slider.A.Position.update(instance);
                                pure.components.slider.A.Events.handles.onFinish(instance);
                            }
                        }
                    }
                },
                progress    : {
                    mousedown   : function(id, event){
                        var instance    = pure.components.slider.A.Storage.get(id);
                        if (instance !== null){
                            instance.currentProgressEvent = {
                                size    : pure.nodes.render.size(instance.nodes.line),
                                x       : event._clientX,
                                y       : event._clientY
                            };
                            instance.current = event._offsetX / instance.currentProgressEvent.size.width;
                            pure.components.slider.A.Position.update(instance);
                            event.preventDefault();
                            return false;
                        }
                    },
                    mouseup     : function(id, event){
                        var instance = pure.components.slider.A.Storage.get(id);
                        if (instance !== null){
                            if (instance.currentProgressEvent !== null) {
                                instance.currentProgressEvent = null;
                                pure.components.slider.A.Events.handles.onFinish(instance);
                                event.preventDefault();
                                return false;
                            }
                        }
                    },
                    mousemove   : function(id, event){
                        var instance    = pure.components.slider.A.Storage.get(id),
                            offsetX     = null;
                        if (instance !== null){
                            if (instance.currentProgressEvent !== null){
                                offsetX = event._clientX - instance.currentProgressEvent.x;
                                offsetX = offsetX / instance.currentProgressEvent.size.width;
                                instance.current += offsetX;
                                instance.current = (instance.current < 0 ? 0 : instance.current);
                                instance.current = (instance.current > 1 ? 1 : instance.current);
                                instance.currentProgressEvent.x = event._clientX;
                                pure.components.slider.A.Position.update(instance);
                                pure.components.slider.A.Events.handles.onChange(instance);
                                event.preventDefault();
                                return false;
                            }
                        }
                    }
                },
                pointer     : {
                    mousedown   : function(id, event){
                        var instance    = pure.components.slider.A.Storage.get(id);
                        if (instance !== null){
                            instance.currentPointerEvent = {
                                size    : pure.nodes.render.size(instance.nodes.line),
                                x       : event._clientX,
                                y       : event._clientY
                            };
                            event.preventDefault();
                            return false;
                        }
                    },
                    mouseup     : function(id, event){
                        var instance = pure.components.slider.A.Storage.get(id);
                        if (instance !== null){
                            if (instance.currentPointerEvent !== null){
                                instance.currentPointerEvent = null;
                                pure.components.slider.A.Events.handles.onFinish(instance);
                                event.preventDefault();
                                return false;
                            }
                        }
                    },
                    mousemove   : function(id, event){
                        var instance    = pure.components.slider.A.Storage.get(id),
                            offsetX     = null;
                        if (instance !== null){
                            if (instance.currentPointerEvent !== null){
                                offsetX = event._clientX - instance.currentPointerEvent.x;
                                offsetX = offsetX / instance.currentPointerEvent.size.width;
                                instance.current += offsetX;
                                instance.current = (instance.current < 0 ? 0 : instance.current);
                                instance.current = (instance.current > 1 ? 1 : instance.current);
                                instance.currentPointerEvent.x = event._clientX;
                                pure.components.slider.A.Position.update(instance);
                                pure.components.slider.A.Events.handles.onChange(instance);
                                event.preventDefault();
                                return false;
                            }
                        }
                    }
                }
            },
            handles : {
                getHandle : function(handle){
                    if (typeof  handle === 'function'){
                        return handle;
                    }else{
                        return (typeof pure.system.getInstanceByPath(handle) === 'function' ? pure.system.getInstanceByPath(handle) : handle);
                    }
                },
                onChange : function(instance){
                    var instance = (typeof instance === 'object' ? instance : pure.components.slider.A.Storage.get(instance));
                    if (instance !== null) {
                        if (instance.handles.onChange !== null && instance.currentPointerEvent === null && instance.currentProgressEvent == null){
                            instance.handles.onChange = pure.components.slider.A.Events.handles.getHandle(instance.handles.onChange);
                            if (typeof instance.handles.onChange === 'function') {
                                instance.handles.onChange(instance.handles.onChangeParam, instance.current);
                            }
                        }
                    }
                },
                onFinish : function(instance){
                    var instance = (typeof instance === 'object' ? instance : pure.components.slider.A.Storage.get(instance));
                    if (instance !== null) {
                        if (instance.handles.onFinish !== null && instance.currentPointerEvent === null && instance.currentProgressEvent == null){
                            instance.handles.onFinish = pure.components.slider.A.Events.handles.getHandle(instance.handles.onFinish);
                            if (typeof instance.handles.onFinish === 'function') {
                                instance.handles.onFinish(instance.handles.onFinishParam, instance.current);
                            }
                        }
                    }
                }
            }
        },
        Handles : {
            set     : function(id, value){
                var instance    = pure.components.slider.A.Storage.get(id);
                if (instance !== null ) {
                    if (typeof value === 'number' && instance.currentPointerEvent === null && instance.currentProgressEvent == null){
                        if (value >= 0 && value <= 1){
                            instance.current = value;
                            pure.components.slider.A.Position.update(instance);
                        }
                    }
                }
            },
            ghost   : function(id, ghost, value){
                var instance    = pure.components.slider.A.Storage.get(id),
                    ghost       = ghost.replace(/\W/, '_');
                if (instance !== null) {
                    if (typeof instance.ghosts[ghost] !== 'undefined'){
                        if (typeof value === 'number'){
                            if (value >= 0 && value <= 1){
                                pure.components.slider.A.Ghost.update(instance, ghost, value);
                            }
                        }
                    }
                }
            }
        },
        More : {
            initialized : false,
            init        : function(){
                if (pure.components.slider.A.More.initialized === false){
                    pure.appevents.Actions.listen('pure.more',          'done', function(){ pure.components.slider.A.init(); }, 'pure.components.slider.A.init');
                    pure.appevents.Actions.listen('pure.positioning',   'new',  function(){ pure.components.slider.A.init(); }, 'pure.components.slider.A.init');
                    pure.components.slider.A.More.initialized = true;
                }
            }
        }
    };
    pure.system.start.add(pure.components.slider.A.init);
    pure.appevents.Events.methods.register('pure.components.slider', 'ready');
    pure.appevents.Actions.call('pure.components.slider', 'ready', null, null);
}());