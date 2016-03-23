(function () {
    if (typeof window.pure              !== "object") { window.pure             = {}; }
    if (typeof window.pure.positioning  !== "object") { window.pure.positioning = {}; }
    "use strict";
    window.pure.positioning.B = {
        data        : {
            storage : {},
            set     : function (id, container, other, columns, size, nodeType, space, collection) {
                var id = (typeof id === "string" ? id : pure.tools.IDs.get("positioning.B.ID."));
                if (typeof pure.positioning.B.data.storage[id] !== "object") {
                    pure.positioning.B.data.storage[id] = {
                        id          : id,
                        container   : container,
                        other       : other,
                        columns     : {
                            width   : columns.width,
                            mode    : columns.mode,
                            count   : null
                        },
                        size        : {
                            width   : size.width,
                            height  : size.height
                        },
                        nodeType    : nodeType,
                        space       : space,
                        collection  : collection,
                        inCollection: {
                            count   : 0,
                            column  : 0
                        },
                        containers  : [],
                        window      : null
                    };
                    return pure.positioning.B.data.storage[id];
                }
                return null;
            },
            get     : function (id) {
                return (typeof pure.positioning.B.data.storage[id] === "object" ? pure.positioning.B.data.storage[id] : null);
            }
        },
        init        : function () {
            function set(instance, id) {
                function getColumnWidth(instance){
                    var width   = instance.getAttribute('data-engine-columnwidth'),
                        result  = null;
                    if (typeof width === 'string'){
                        if (width.indexOf('em') !== -1){
                            width   = parseInt(width);
                            result  = {
                                width   : pure.nodes.convert.emToPx(width, instance),
                                mode    : 'flex' };
                        }
                        if (result === null){
                            if (width.indexOf('px') !== -1){
                                result  = {
                                    width   : parseInt(width),
                                    mode    : 'flex' };
                            }
                        }
                        if (result === null){
                            if (width.indexOf('%') !== -1){
                                result  = {
                                    width   : parseInt(width),
                                    mode    : 'fixed' };
                            }
                        }
                    }
                    return result;
                };
                function getColumnSpace(instance){
                    var width   = instance.getAttribute('data-engine-space'),
                        result  = null;
                    if (typeof width === 'string'){
                        if (width.indexOf('em') !== -1){
                            result  = pure.nodes.convert.emToPx(parseInt(width), instance);
                        }
                        if (result === null){
                            if (width.indexOf('px') !== -1){
                                result = parseInt(width);
                            }
                        }
                        if (result === null){
                            if (width.indexOf('%') !== -1){
                                result  = parseInt(width);
                            }
                        }
                    }
                    return result;
                };
                function getTargetNodeType(instance){
                    var nodeType = instance.getAttribute('data-engine-nodeType');
                    return (typeof nodeType === 'string' ? (nodeType !== '' ? nodeType : '*') : '*');

                };
                function getCollection(instance, nodeType, id){
                    var nodes       = pure.nodes.select.all('*[data-engine-positioning-ID="' + id + '"] ' + nodeType + ':not([data-element-in-collection])'),
                        collection  = [];
                    for(var index = 0, max_index = nodes.length; index < max_index; index += 1){
                        nodes[index].setAttribute('data-element-in-collection', true);
                        collection.push(nodes[index]);
                    }
                    return collection;
                };
                var columns         = getColumnWidth(instance),
                    size            = pure.nodes.render.size(instance),
                    nodeType        = getTargetNodeType(instance),
                    collection      = getCollection(instance, nodeType, id),
                    data            = pure.positioning.B.data.get(id),
                    container       = null,
                    other           = null,
                    progress        = null;
                if (columns !== null){
                    if (data === null){
                        container       = pure.nodes.select.first('*[data-engine-positioning-ID="' + id + '"] div[data-element-type="Pure.Positioning.B.Columns.Container"]'    );
                        progress        = pure.nodes.select.first('*[data-engine-positioning-ID="' + id + '"] div[data-element-type="Pure.Positioning.B.Columns.Loading"]'      );
                        other           = pure.nodes.select.first('*[data-engine-positioning-ID="' + id + '"] div[data-element-type="Pure.Positioning.B.OtherNodes.Container"]' );
                        if (container !== null && progress !== null && other !== null){
                            pure.positioning.B.data.set(id, container, other, columns, size, nodeType, getColumnSpace(instance), collection);
                            progress.parentNode.removeChild(progress);
                            container.style.display = 'block';
                            return 'new';
                        }
                        return 'fail';
                    }else{
                        if (collection.length > 0){
                            for(var index = 0, max_index = collection.length; index < max_index; index += 1){
                                data.collection.push(collection[index]);
                            }
                            return 'update';
                        }else{
                            return 'nothing';
                        }
                    }
                }
                return null;
            };
            function getID(instance){
                var id = instance.getAttribute('data-engine-positioning-ID');
                id = (typeof id === 'string'? (id !== '' ? id : pure.tools.IDs.get("positioning.B.ID.")) : pure.tools.IDs.get("positioning.B.ID."));
                instance.setAttribute('data-engine-positioning-ID', id);
                return id;
            };
            var instances       = pure.nodes.select.all("div[data-engine-element=\"Positioning.B\"]:not([data-type-element-inited])"),
                instance_id     = null;
            /*Attention. We can't set [data-type-element-inited] to [true], because method INIT used for update after more*/
            if (instances !== null) {
                if (typeof instances.length === "number") {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        instance_id = getID(instances[index]);
                        switch (set(instances[index], instance_id)){
                            case 'new':
                                pure.positioning.B.actions.init(instance_id);
                                pure.positioning.B.actions.resize(instance_id, true);
                                break;
                            case 'update':
                                pure.positioning.B.actions.resize(instance_id, false);
                                break;
                            case 'nothing':
                                break;
                        }
                    }
                }
            }
            pure.positioning.B.more.        init();
            pure.positioning.B.switchers.   init();
        },
        actions     : {
            allowWindowResizeFire   : true,
            init                    : function (id) {
                var instance = pure.positioning.B.data.get(id);
                if (instance !== null) {
                    pure.events.add(
                        window,
                        "resize",
                        function (event) {
                            if (pure.positioning.B.actions.allowWindowResizeFire !== false){
                                pure.positioning.B.actions.resize (id);
                            }
                        }
                    );
                    pure.appevents.Actions.listen(
                        'pure.positioning',
                        'update',
                        function(){ pure.positioning.B.actions.resize(id);},
                        id
                    );
                    //Set global sidebar event
                    pure.appevents.Actions.listen(
                        'global.layout.sidebar',
                        'update',
                        function(){ pure.positioning.B.actions.resize(id);},
                        id
                    );
                }
            },
            updateSize              : function(id){
                function setDisplay(instance, value){
                    for(var index = instance.container.childNodes.length - 1; index >=0; index -= 1){
                        if (typeof instance.container.childNodes[index].style !== 'undefined'){
                            if (typeof instance.container.childNodes[index].style.display !== 'undefined'){
                                instance.container.childNodes[index].style.display=value;
                            }
                        }
                    }
                }
                var instance    = pure.positioning.B.data.get(id),
                    size        = null,
                    windowSize  = null;
                if (instance !== null) {
                    setDisplay(instance, 'none');
                    pure.nodes.render.redraw(instance.container);
                    size                    = pure.nodes.render.size(instance.container);
                    instance.size.width    = size.width;
                    instance.size.height   = size.height;
                    setDisplay(instance, '');
                }
            },
            getBasicSize            : function(instance){
                var columns_count = Math.round(instance.size.width / instance.columns.width);
                columns_count = (columns_count === 0 ? 1 : (instance.collection.length === 1 ? 1 : columns_count));
                return {
                    count   : columns_count,
                    width   : Math.round(100 / columns_count)
                };
            },
            build                   : function(id){
                function createColumns(basicSize){
                    var columns = [],
                        column  = null;
                    for (var index = 0; index < basicSize.count; index += 1){
                        column = (function(width){
                            var container   = document.createElement('DIV'),
                                content     = document.createElement('DIV');
                            container.  setAttribute('data-element-type', 'Pure.Positioning.B.Column.Container');
                            content.    setAttribute('data-element-type', 'Pure.Positioning.B.Column');
                            container.style.width       = width + '%';
                            container.  appendChild(content);
                            return content;
                        }(basicSize.width));
                        columns.push(column);
                    }
                    return columns;
                };
                function removeOld(instance){
                    if (instance.containers.length !== 0){
                        for (index = 0, max_index = instance.containers.length; index < max_index; index += 1){
                            instance.containers[index].parentNode.parentNode.removeChild(instance.containers[index].parentNode);
                        }
                    }
                    instance.containers = null;
                };
                function saveAndAddNew(instance, columns){
                    if (instance.container.firstChild !== null){
                        for (var index = columns.length - 1; index >= 0; index -= 1){
                            instance.container.insertBefore(columns[index].parentNode, instance.container.firstChild);
                        }
                    }else{
                        for (var index = 0, max_index = columns.length; index < max_index; index += 1){
                            instance.container.appendChild(columns[index].parentNode);
                        }
                    }
                    instance.containers = columns;
                };
                function replaceOther(instance){
                    var others = pure.nodes.select.all('div[data-engine-positioning-ID="' + instance.id + '"] div[data-element-type="Pure.Positioning.B.Columns.Container"] > *:not([data-element-type="Pure.Positioning.B.Column.Container"])');
                    for (var index = 0, max_index = others.length; index < max_index; index += 1){
                        instance.other.appendChild(others[index]);
                    }
                };
                var instance    = pure.positioning.B.data.get(id),
                    basicSize   = null,
                    columns     = null,
                    column      = 0;
                if (instance !== null) {
                    basicSize               = pure.positioning.B.actions.getBasicSize(instance);
                    columns                 = createColumns(basicSize);
                    for (var index = 0, max_index = instance.collection.length; index < max_index; index += 1){
                        columns[column].appendChild(instance.collection[index]);
                        column += 1;
                        column = (column > basicSize.count - 1 ? 0 : column);
                    }
                    instance.inCollection = {
                        count   : instance.collection.length,
                        column  : column,
                        columns : basicSize.count
                    };
                    removeOld(instance);
                    saveAndAddNew(instance, columns);
                    replaceOther(instance);
                }
            },
            updateCollection        : function(id){
                var instance    = pure.positioning.B.data.get(id),
                    column      = null;
                if (instance !== null) {
                    if (instance.inCollection.count !== instance.collection.length &&
                        instance.inCollection.count < instance.collection.length){
                        column = instance.inCollection.column;
                        for (var index = instance.inCollection.count, max_index = instance.collection.length; index < max_index; index += 1){
                            instance.containers[column].appendChild(instance.collection[index]);
                            column += 1;
                            column = (column > instance.inCollection.columns - 1 ? 0 : column);
                        }
                        instance.inCollection = {
                            count   : instance.collection.length,
                            column  : column,
                            columns : instance.inCollection.columns
                        };
                    }
                }
            },
            resize                  : function (id, force) {
                function updateSizes(instance){
                    var width = instance.size.width / instance.columns.count;
                    for (var index = 0, max_index = instance.collection.length; index < max_index; index += 1){
                        //Width
                        instance.collection[index].style.width  = (width - instance.space) + 'px';
                        //Left align
                        instance.collection[index].style.left   = (instance.space / 2) + 'px';
                        //instance.collection[index].style.width = '100%';
                    }
                };
                var instance    = pure.positioning.B.data.get(id),
                    force       = (typeof force === 'boolean' ? force : false),
                    basicSize   = null;
                if (instance !== null) {
                    pure.positioning.B.actions.updateSize(id);
                    basicSize   = pure.positioning.B.actions.getBasicSize(instance);
                    if (instance.columns.count !== basicSize.count || force === true){
                        pure.positioning.B.actions.build(id);
                        instance.columns.count = basicSize.count;
                    }
                    pure.positioning.B.actions.updateCollection(id);
                    updateSizes(instance);
                    pure.appevents.Actions.call('pure.positioning', 'resize', null, null);
                }
            }
        },
        switchers   : {
            init : function(){
                var instances = pure.nodes.select.all('*[data-engine-positioning-B-caller="change"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1){
                        (function(caller){
                            var id              = caller.getAttribute('data-engine-positioning-B-id'),
                                callerEvent     = caller.getAttribute('data-engine-positioning-B-caller'),
                                property        = {
                                    name    : caller.getAttribute('data-engine-positioning-B-property-name'),
                                    type    : caller.getAttribute('data-engine-positioning-B-property-type'),
                                    value   : caller.getAttribute('data-engine-positioning-B-property-value')
                                },
                                redraw          = caller.getAttribute('data-engine-positioning-B-redraw-selector');
                            if (id !== null){
                                if (pure.tools.objects.isValueIn(property, null) !== false){
                                    property = null;
                                }else{
                                    switch (property.type){
                                        case 'number':
                                            property.value = parseInt(property.value, 10);
                                            break;
                                        case 'boolean':
                                            property.value = (property.value.toLowerCase() === 'false' ? false : true);
                                            break;
                                        case 'string':
                                            //do nothing
                                            break;
                                    }
                                }
                                pure.events.add(
                                    caller,
                                    callerEvent,
                                    function(event){
                                        pure.positioning.B.switchers.fire(event, id, caller, property, redraw);
                                    }
                                );
                                caller.setAttribute('data-type-element-inited', 'true');
                            }
                        }(instances[index]));
                    }
                }
            },
            fire : function(event, id, caller, property, redraw){
                var redrawNode = null;
                if (property !== null){
                    if (typeof caller[property.name] === 'undefined'){
                        return false;
                    }
                    if (caller[property.name] !== property.value){
                        return false;
                    }
                }
                if (redraw !== null){
                    redrawNode = pure.nodes.select.first(redraw.replace(/\|/g, '"'));
                    if (redrawNode !== null){
                        pure.nodes.render.redraw(redrawNode);
                    }
                }
                pure.positioning.B.actions.resize(id, true);
            }
        },
        more    : {
            initialized : false,
            init        : function(){
                if (pure.positioning.B.more.initialized === false){
                    pure.appevents.Events.methods.register('pure.more',         'done'  );
                    pure.appevents.Events.methods.register('pure.positioning',  'resize');
                    pure.appevents.Events.methods.register('pure.positioning',  'update');
                    pure.appevents.Events.methods.register('pure.positioning',  'new'   );
                    pure.appevents.Actions.listen('pure.more', 'done', function(){ pure.positioning.B.init(); }, 'pure.positioning.B.init');
                    pure.positioning.B.more.initialized = true;
                }
            }
        },
        appevents : {
            inited : false,
            init : function(){
                if (pure.positioning.B.appevents.inited === false){
                    pure.appevents.Actions.listen(
                        'pure.positioning',
                        'manual.window.resize.update',
                        function(){
                            pure.positioning.B.actions.allowWindowResizeFire = false;
                        },
                        'pure.positioning.B.manual.window.resize.update'
                    );
                    pure.positioning.B.appevents.inited = true;
                }
            }
        }
    };
    pure.system.start.add(pure.positioning.B.appevents.init);
    pure.system.start.add(pure.positioning.B.init);
}());