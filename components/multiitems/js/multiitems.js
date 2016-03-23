(function () {
    if (typeof window.pure                  !== "object") { window.pure                     = {}; }
    if (typeof window.pure.components       !== "object") { window.pure.components          = {}; }
    "use strict";
    window.pure.components.multiitems = {
        init    : {
            add    : function () {
                var instances = pure.nodes.select.all('*[data-multiitems-engine-add][data-multiitems-engine-template-id]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(instance, _index, source){
                            var templateID      = instance.getAttribute('data-multiitems-engine-template-id'        ),
                                handles         = instance.getAttribute('data-muliitems-engine-afteradd-handles'    ),
                                getFields       = instance.getAttribute('data-muliitems-engine-getFields-handle'    ),
                                container       = instance.getAttribute('data-muliitems-engine-container-for-new'   ),
                                templateNode    = null;
                            if (pure.nodes.find.parentByAttr(instance, {name:'data-multiitems-engine-template', value:null}) === null) {
                                if (templateID !== null){
                                    if (typeof handles === "string"){
                                        handles = handles.split(',');
                                    }else{
                                        handles = [];
                                    }
                                    handles     = (handles instanceof Array === true ? handles : [handles]);
                                    getFields   = (typeof getFields === 'string' ? pure.system.getInstanceByPath(getFields) : null);
                                    if (typeof container === 'string'){
                                        container = pure.nodes.select.first(container.replace(/\|/gi, '"'));
                                    }
                                    pure.events.add(
                                        instance,
                                        'click',
                                        function(){
                                            pure.components.multiitems.actions.add(instance, templateID, handles, getFields, container);
                                        }
                                    );
                                }
                                instance.setAttribute('data-element-inited', 'true');
                            }
                        }
                    );
                }
            },
            template : function(){
                var instances = pure.nodes.select.all('*[data-multiitems-engine-template]:not([data-element-inited])');
                if (instances !== null) {
                    Array.prototype.forEach.call(
                        instances,
                        function (instance, _index, source) {
                            var templateID = instance.getAttribute('data-multiitems-engine-template');
                            pure.components.multiitems.templates.add(templateID);
                            instance.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            remove : function(){
                var instances = pure.nodes.select.all('*[data-multiitems-engine-remove][data-multiitems-engine-itemID]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(instance, _index, source){
                            var itemID  = instance.getAttribute('data-multiitems-engine-itemID'),
                                params  = instance.getAttribute('data-muliitems-engine-afterremove-params'),
                                handles = instance.getAttribute('data-muliitems-engine-afterremove-handles');
                            if (pure.nodes.find.parentByAttr(instance, {name:'data-multiitems-engine-template', value:null}) === null) {
                                if (itemID !== null){
                                    if (typeof handles === "string"){
                                        handles = handles.split(',');
                                    }else{
                                        handles = [];
                                    }
                                    handles = (handles instanceof Array === true ? handles : [handles]);
                                    if (typeof params === "string"){
                                        params = params.split(',');
                                    }else{
                                        params = [];
                                    }
                                    params = (params instanceof Array === true ? params : [params]);
                                    pure.events.add(
                                        instance,
                                        'click',
                                        function(event){
                                            pure.components.multiitems.actions.remove(instance, itemID, handles, params);
                                            pure.events.stop(event);
                                            return false;
                                        }
                                    );
                                }
                                instance.setAttribute('data-element-inited', 'true');
                            }
                        }
                    );
                }
            },
            init : function(){
                pure.components.multiitems.init.add     ();
                pure.components.multiitems.init.remove  ();
                pure.components.multiitems.init.template();
            }
        },
        templates   : {
            data    : {},
            add     : function(id){
                var templates       = pure.components.multiitems.templates.data,
                    templateNode    = null;
                if (typeof templates[id] === 'undefined'){
                    templateNode = pure.nodes.select.first('*[data-multiitems-engine-template="' + id + '"]');
                    if (templateNode !== null) {
                        templates[id] = {
                            innerHTML   : templateNode.innerHTML,
                            attributes  : pure.nodes.attributes.get(templateNode, ['style', 'data-multiitems-engine-template']),
                            nodeName    : templateNode.nodeName
                        };
                        templateNode.parentNode.removeChild(templateNode);
                        return true;
                    }
                }
                return false;
            },
            get     : function(id){
                var templates = pure.components.multiitems.templates.data;
                return (typeof templates[id] === 'undefined' ? null : templates[id]);
            }
        },
        actions     : {
            add     : function(instance, templateID, handles, getFields, container){
                function replaceIndex(sourceStr, newID){
                    var resultStr = sourceStr;
                    //Such way of replacing, because can be something like [[index]][[index]]..[[index]].
                    resultStr  = resultStr.replace(
                        /(\[\[index\]\]){2,}/gi,
                        function(str, p1, offset, s){
                            return str.replace(/\[\[index\]\]/i, '[[-index-]]').replace(/\[\[index\]\]/gi, '[[--index--]]').replace(/\[\[-index-\]\]/gi, '[[index]]');
                        }
                    );
                    resultStr  = resultStr.replace(/\[index\]/gi, '' + newID + '');
                    resultStr  = resultStr.replace(/\[--index--\]/gi, '[index]');
                    return resultStr;
                };
                var newID       = pure.tools.IDs.get(),
                    template    = pure.components.multiitems.templates.get(templateID),
                    node        = null,
                    fields      = (typeof getFields === 'function' ? pure.system.runHandle(getFields, null, 'pure.components.multiitems.actions.add', this) : null),
                    attributes  = null;
                if (template !== null){
                    node        = document.createElement(template.nodeName);
                    attributes  = pure.tools.arrays.copy(template.attributes);
                    node.innerHTML = template.innerHTML;
                    for(var index = attributes.length - 1; index >= 0; index -= 1){
                        if (typeof attributes[index].value === 'string'){
                            attributes[index].value = replaceIndex(attributes[index].value, newID);
                        }
                    }
                    //Set newID
                    node.innerHTML = replaceIndex(node.innerHTML, newID);
                    pure.nodes.attributes.set(node, attributes);
                    //Check fields
                    if (fields instanceof Array){
                        Array.prototype.forEach.call(
                            fields,
                            function(field, _index, source){
                                if (typeof field === 'object'){
                                    if (typeof field.name === 'string' && typeof field.value === 'string'){
                                        node.innerHTML  = node.innerHTML.replace(
                                            new RegExp('(' + field.name + ')', 'gi'),
                                            field.value
                                        );
                                    }
                                }
                            }
                        );
                    }
                    //Attach node
                    if (container !== null){
                        container.appendChild(node);
                    }else{
                        if (instance !== null){
                            instance.parentNode.insertBefore(node, instance);
                        }
                    }
                    //Init
                    pure.components.multiitems.init.add     ();
                    pure.components.multiitems.init.remove  ();
                    //Run handles
                    pure.components.multiitems.helpers.runHandles(handles, 'pure.components.multiitems.actions.add', null);
                }
            },
            remove  : function(instance, itemID, handles, param){
                var parent = pure.nodes.select.first('*[data-multiitems-engine-itemID="' + itemID + '"]:not([data-multiitems-engine-remove])');
                if (parent !== null) {
                    parent.parentNode.removeChild(parent);
                    pure.components.multiitems.helpers.runHandles(handles, 'pure.components.multiitems.actions.remove', param);
                }
            }
        },
        helpers : {
            runHandles : function(handles, place, param){
                Array.prototype.forEach.call(
                    handles,
                    function(_handle, _index, source){
                        var handle = pure.system.getInstanceByPath(_handle);
                        if (handle !== null){
                            pure.system.runHandle(handle, param, place, this);
                        }
                    }
                );
            }
        }
    };
    pure.system.start.add(pure.components.multiitems.init.init);
}());