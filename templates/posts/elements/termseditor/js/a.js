(function () {
    if (typeof window.pure                          !== "object") { window.pure                             = {}; }
    if (typeof window.pure.posts                    !== "object") { window.pure.posts                       = {}; }
    if (typeof window.pure.posts.elements           !== "object") { window.pure.posts.elements              = {}; }
    "use strict";
    window.pure.posts.elements.terms = {
        init : {
            input   : function(){
                var instances = pure.nodes.select.all('*[data-terms-engine-input]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(instance, _index, source){
                            var id          = instance.getAttribute('data-terms-engine-input'),
                                popup       = pure.nodes.select.first('*[data-terms-engine-list="' + id + '"]'),
                                items       = pure.nodes.select.all('*[data-terms-engine-list-item="' + id + '"]'),
                                valuesNodes = pure.nodes.select.all('*[data-terms-engine-list-item="' + id + '"] span'),
                                countsNodes = pure.nodes.select.all('*[data-terms-engine-list-item="' + id + '"] sup'),
                                values      = [];
                            if (popup !== null && items !== null && valuesNodes !== null){
                                if (valuesNodes.length === items.length){
                                    pure.events.add(
                                        instance,
                                        'blur',
                                        function(){
                                            pure.posts.elements.terms.input.focus.lost(popup);
                                            return true;
                                        }
                                    );
                                    pure.events.add(
                                        instance,
                                        'focus',
                                        function(){
                                            pure.posts.elements.terms.input.focus.get(id, instance, values, items, popup);
                                        }
                                    );
                                    pure.events.add(
                                        instance,
                                        'click',
                                        function(){
                                            pure.posts.elements.terms.input.focus.get(id, instance, values, items, popup);
                                        }
                                    );
                                    for(var index = valuesNodes.length - 1; index >= 0; index -= 1){
                                        values.unshift(valuesNodes[index].innerHTML.toLowerCase());
                                        (function(button, id, value, count){
                                            pure.events.add(
                                                button,
                                                'mousedown',
                                                function(){
                                                    pure.posts.elements.terms.input.addNew(id, value, count);
                                                }
                                            );
                                        }(items[index], id, values[0], countsNodes[index].innerHTML));
                                    }
                                    pure.events.add(
                                        instance,
                                        'keyup',
                                        function(event){
                                            pure.posts.elements.terms.input.change(event, instance, values, items, popup, id);
                                        }
                                    );
                                }
                            }
                            instance.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            add     : function(){
                var instances = pure.nodes.select.all('*[data-terms-engine-add]:not([data-element-inited])');
                if (instances !== null) {
                    Array.prototype.forEach.call(
                        instances,
                        function (instance, _index, source) {
                            var id          = instance.getAttribute('data-terms-engine-add'),
                                container   = pure.nodes.select.first('*[data-terms-container-id="' + id + '"]');
                            if (container !== null) {
                                pure.events.add(
                                    instance,
                                    'click',
                                    function(){
                                        pure.posts.elements.terms.add(id, container);
                                    }
                                );
                            }
                            instance.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            init    : function(){
                pure.posts.elements.terms.init.input();
                pure.posts.elements.terms.init.add();
                pure.posts.elements.terms.history.update();
            }
        },
        history : {
            data    : {},
            add     : function(id, value){
                var data = pure.posts.elements.terms.history.data;
                if (typeof data[id] === 'undefined'){
                    data[id] = [];
                }
                data[id].push(value);
            },
            remove  : function(id, value){
                var data = pure.posts.elements.terms.history.data;
                if (typeof data[id] !== 'undefined'){
                    if (data[id].indexOf(value) !== -1){
                        data[id].splice(data[id].indexOf(value), 1);
                    }
                }
            },
            isIn    : function(id, value){
                var data = pure.posts.elements.terms.history.data;
                if (typeof data[id] !== 'undefined'){
                    if (data[id].indexOf(value) !== -1){
                        return true;
                    }
                }
                return false;
            },
            removeFromHistory : function(id, name){
                if (typeof id === 'string' && typeof name === 'string'){
                    pure.posts.elements.terms.history.remove(id, name);
                }
            },
            update : function(){
                var nodes = pure.nodes.select.all('*[data-keyword-engine-exiting]');
                if (nodes !== null){
                    Array.prototype.forEach.call(
                        nodes,
                        function(node, _index, source){
                            var id = node.getAttribute('data-keyword-engine-exiting');
                            if (id !== null){
                                pure.posts.elements.terms.history.add(id, node.innerHTML);
                            }
                            node.removeAttribute('data-keyword-engine-exiting');
                        }
                    );
                }
            }
        },
        add     : function(id, container){
            var input = pure.nodes.select.first('input[data-terms-engine-input="' + id + '"]');
            if (input !== null && pure.system.getInstanceByPath('pure.components.multiitems.actions.add') !== null && container !== null){
                if (input.value.trim().length >= 2){
                    if (pure.posts.elements.terms.history.isIn(id, input.value.trim().toLowerCase()) === false){
                        pure.posts.elements.terms.input.current.name           = input.value.trim().toLowerCase();
                        pure.posts.elements.terms.input.current.count          = 0;
                        pure.posts.elements.terms.input.current.instance_id    = id;
                        pure.components.multiitems.actions.add(
                            null,
                            id,
                            [],
                            pure.posts.elements.terms.getFields,
                            container
                        );
                        pure.posts.elements.terms.history.add(id, pure.posts.elements.terms.input.current.name);
                        input.value = '';
                        return true;
                    }
                }
            }
            return false;
        },
        input   : {
            current : {
                name        : '',
                count       : '',
                instance_id : ''
            },
            focus   : {
                get     : function(id, input, values, nodes, popup){
                    if (pure.posts.elements.terms.input.refresh(id, input, values, nodes) !== false){
                        popup.style.display = 'block';
                    }
                },
                lost    : function(popup){
                    popup.style.display = 'none';
                }
            },
            refresh : function(id, input, values, nodes){
                var text        = input.value.toLowerCase(),
                    displayed   = false;
                for (var index = values.length - 1; index >= 0; index -= 1){
                    if (values[index].indexOf(text) !== -1 && pure.posts.elements.terms.history.isIn(id, values[index]) === false){
                        nodes[index].style.display  = '';
                        displayed                   = true;
                    }else{
                        nodes[index].style.display  = 'none';
                    }
                }
                return displayed;
            },
            change  : function(event, input, values, nodes, popup, id){
                if (event.keyCode == 13){
                    if (pure.posts.elements.terms.add(
                            id,
                            pure.nodes.select.first('*[data-terms-container-id="' + id + '"]')
                        ) !== false){
                        popup.style.display = 'none';
                    }
                }else{
                    pure.posts.elements.terms.input.current.name           = input.value.toLowerCase();
                    pure.posts.elements.terms.input.current.count          = 0;
                    pure.posts.elements.terms.input.current.instance_id    = id;
                    if (pure.posts.elements.terms.input.refresh(id, input, values, nodes) === false){
                        popup.style.display = 'none';
                    }else{
                        popup.style.display = 'block';
                    }
                }
            },
            addNew : function(id, value, count){
                var container = pure.nodes.select.first('*[data-terms-container-id="' + id + '"]');
                if (container !== null){
                    pure.posts.elements.terms.input.current.name           = value;
                    pure.posts.elements.terms.input.current.count          = count;
                    pure.posts.elements.terms.input.current.instance_id    = id;
                    pure.components.multiitems.actions.add(
                        null,
                        id,
                        [],
                        pure.posts.elements.terms.getFields,
                        container
                    );
                    pure.posts.elements.terms.history.add(id, value);
                }
            }
        },
        getFields : function(){
            return [
                { name : '__name__' ,           value : pure.posts.elements.terms.input.current.name.          toString()  },
                { name : '__count__' ,          value : pure.posts.elements.terms.input.current.count.         toString() },
                { name : '__instance_id__' ,    value : pure.posts.elements.terms.input.current.instance_id.   toString() }
            ];
        }
    };
    pure.system.start.add(pure.posts.elements.terms.init.init);
}());