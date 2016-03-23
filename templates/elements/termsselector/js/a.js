(function () {
    if (typeof window.pure                          !== "object") { window.pure                             = {}; }
    if (typeof window.pure.posts                    !== "object") { window.pure.posts                       = {}; }
    if (typeof window.pure.posts.elements           !== "object") { window.pure.posts.elements              = {}; }
    "use strict";
    window.pure.posts.elements.termsselector = {
        init : {
            selector   : function(){
                var instances = pure.nodes.select.all('*[data-termsselector-wrapper-id]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(instance, _index, source){
                            var id          = instance.getAttribute('data-termsselector-wrapper-id'),
                                popup       = pure.nodes.select.first('*[data-termsselector-engine-list="' + id + '"]'),
                                close       = pure.nodes.select.first('*[data-termsselector-engine-list-close="' + id + '"]'),
                                title       = pure.nodes.select.first('*[data-termsselector-title-id="' + id + '"]'),
                                items       = pure.nodes.select.all('*[data-termsselector-engine-list-item="' + id + '"]'),
                                valuesNodes = pure.nodes.select.all('*[data-termsselector-engine-list-item="' + id + '"] span'),
                                countsNodes = pure.nodes.select.all('*[data-termsselector-engine-list-item="' + id + '"] sup'),
                                values      = [];
                            if (popup !== null && close !== null  && title !== null && items !== null && valuesNodes !== null){
                                if (valuesNodes.length === items.length){
                                    pure.events.add(
                                        close,
                                        'click',
                                        function(event){
                                            pure.posts.elements.termsselector.selector.focus.lost(popup);
                                            pure.events.stop(event);
                                            return false;
                                        }
                                    );
                                    pure.events.add(
                                        instance,
                                        'click',
                                        function(){
                                            pure.posts.elements.termsselector.selector.focus.get(id, values, items, popup);
                                        }
                                    );
                                    for(var index = valuesNodes.length - 1; index >= 0; index -= 1){
                                        values.unshift(valuesNodes[index].innerHTML.toLowerCase());
                                        (function(button, id, value, count){
                                            pure.events.add(
                                                button,
                                                'mousedown',
                                                function(){
                                                    pure.posts.elements.termsselector.selector.addNew(id, value, count, title);
                                                }
                                            );
                                        }(items[index], id, values[0], countsNodes[index].innerHTML));
                                    }
                                    pure.posts.elements.termsselector.title.check(id, title);
                                }
                            }
                            instance.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            init    : function(){
                pure.posts.elements.termsselector.init.selector ();
                pure.posts.elements.termsselector.history.update();
            }
        },
        history : {
            data                : {},
            add                 : function(id, value){
                var data = pure.posts.elements.termsselector.history.data;
                if (typeof data[id] === 'undefined'){
                    data[id] = [];
                }
                data[id].push(value);
            },
            remove              : function(id, value){
                var data = pure.posts.elements.termsselector.history.data;
                if (typeof data[id] !== 'undefined'){
                    if (data[id].indexOf(value) !== -1){
                        data[id].splice(data[id].indexOf(value), 1);
                    }
                }
            },
            isIn                : function(id, value){
                var data = pure.posts.elements.termsselector.history.data;
                if (typeof data[id] !== 'undefined'){
                    if (data[id].indexOf(value) !== -1){
                        return true;
                    }
                }
                return false;
            },
            isEmpty             : function(id){
                var data = pure.posts.elements.termsselector.history.data;
                if (typeof data[id] !== 'undefined'){
                    return (data[id].length > 0 ? false : true);
                }
                return true;
            },
            removeFromHistory   : function(id, name){
                var title = pure.nodes.select.first('*[data-termsselector-title-id="' + id + '"]');
                if (typeof id === 'string' && typeof name === 'string'){
                    pure.posts.elements.termsselector.history.remove(id, name);
                    if (title !== null){
                        pure.posts.elements.termsselector.title.check(id, title);
                    }
                }
            },
            update              : function(){
                var nodes = pure.nodes.select.all('*[data-keyword-engine-exiting]');
                if (nodes !== null){
                    Array.prototype.forEach.call(
                        nodes,
                        function(node, _index, source){
                            var id = node.getAttribute('data-keyword-engine-exiting');
                            if (id !== null){
                                pure.posts.elements.termsselector.history.add(id, node.innerHTML);
                            }
                            node.removeAttribute('data-keyword-engine-exiting');
                        }
                    );
                }
            }
        },
        selector   : {
            current : {
                name        : '',
                count       : '',
                instance_id : ''
            },
            focus   : {
                get     : function(id, values, nodes, popup){
                    if (pure.posts.elements.termsselector.selector.refresh(id, values, nodes) !== false){
                        popup.style.display = 'block';
                    }
                },
                lost    : function(popup){
                    popup.style.display = 'none';
                }
            },
            refresh : function(id, values, nodes){
                var displayed   = false;
                for (var index = values.length - 1; index >= 0; index -= 1){
                    if (pure.posts.elements.termsselector.history.isIn(id, values[index]) === false){
                        nodes[index].style.display  = '';
                        displayed                   = true;
                    }else{
                        nodes[index].style.display  = 'none';
                    }
                }
                return displayed;
            },
            addNew : function(id, value, count, title){
                var container = pure.nodes.select.first('*[data-termsselector-container-id="' + id + '"]');
                if (container !== null){
                    pure.posts.elements.termsselector.selector.current.name           = value;
                    pure.posts.elements.termsselector.selector.current.count          = count;
                    pure.posts.elements.termsselector.selector.current.instance_id    = id;
                    pure.components.multiitems.actions.add(
                        null,
                        id,
                        [],
                        pure.posts.elements.termsselector.getFields,
                        container
                    );
                    pure.posts.elements.termsselector.history.add(id, value);
                    pure.posts.elements.termsselector.title.check(id, title);
                }
            }
        },
        title       : {
            check : function(id, title){
                if (pure.posts.elements.termsselector.history.isEmpty(id) === false || title.innerHTML === ''){
                    title.style.display = 'none';
                }else{
                    title.style.display = '';
                }
            }
        },
        getFields   : function(){
            return [
                { name : '__name__' ,           value : pure.posts.elements.termsselector.selector.current.name.          toString()  },
                { name : '__count__' ,          value : pure.posts.elements.termsselector.selector.current.count.         toString() },
                { name : '__instance_id__' ,    value : pure.posts.elements.termsselector.selector.current.instance_id.   toString() }
            ];
        }
    };
    pure.system.start.add(pure.posts.elements.termsselector.init.init);
}());