(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components               !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.effects       !== "object") { window.pure.components.effects      = {}; }
    "use strict";
    window.pure.components.effects.fixscroll = {
        storage     : [],
        init        : function(){
            //return;
            var instances = pure.nodes.select.all('*[data-effects-fixscroll]:not([data-type-element-inited])');
            if (instances !== null){
                for(var index = instances.length - 1; index >= 0; index -= 1){
                    (function(instance){
                        var selector    = instance.getAttribute('data-effects-fixscroll'            ),
                            switcher    = instance.getAttribute('data-effects-fixscroll-switcher'   ),
                            parent      = null,
                            id          = null;
                        if (selector !== null){
                            parent = pure.nodes.select.first(selector.replace(/\|/gi, '"'));
                            if (parent !== null){
                                id = pure.components.effects.fixscroll.resize.add(instance);
                                pure.events.add(
                                    (parent === document.body ? window : parent),
                                    'scroll',
                                    function(event){
                                        pure.components.effects.fixscroll.scroll(parent, instance, id);
                                    }
                                );
                                if (switcher !== null && switcher !== ''){
                                    switcher = pure.nodes.select.first(switcher.replace(/\|/gi, '"'));
                                    if (switcher !== null){
                                        if (switcher.nodeName.toLowerCase() === 'input'){
                                            pure.events.add(
                                                switcher,
                                                'change',
                                                function(){
                                                    instance.style.position = 'absolute';
                                                    pure.nodes.render.redraw(instance);
                                                    pure.components.effects.fixscroll.scroll(parent, instance, id);
                                                }
                                            );
                                            pure.appevents.Actions.listen(
                                                'effects.fixscroll.sidebar',
                                                'force',
                                                function(){
                                                    instance.style.position = 'absolute';
                                                    pure.nodes.render.redraw(instance);
                                                    pure.components.effects.fixscroll.scroll(parent, instance, id);
                                                },
                                                'pure.components.effects.fixscroll.scroll'
                                            );
                                        }
                                    }
                                }
                            }
                        }
                        instance.setAttribute('data-type-element-inited', 'true');
                    }(instances[index]));
                }
                pure.components.effects.fixscroll.resize.init();
            }
        },
        resize      : {
            _index  : 0,
            get index(){
                pure.components.effects.fixscroll.resize._index += 1;
                return pure.components.effects.fixscroll.resize._index;
            },
            data    : {},
            add     : function(node){
                var id = pure.components.effects.fixscroll.resize.index;
                pure.components.effects.fixscroll.resize.data[id] = {
                    size    : pure.nodes.render.size(node),
                    offset  : pure.nodes.render.offset(node),
                    node    : node
                };
                return id;
            },
            init    : function(){
                pure.events.add(
                    window,
                    'resize',
                    pure.components.effects.fixscroll.resize.resize
                );
            },
            resize  : function(){
                var data = pure.components.effects.fixscroll.resize.data;
                for(var id in data){
                    data[id].size   = pure.nodes.render.size    (data[id].node);
                    //data[id].offset = pure.nodes.render.offset  (data[id].node);
                }
            },
            get     : function(id){
                var data = pure.components.effects.fixscroll.resize.data;
                return (typeof data[id] !== 'undefined' ? data[id] : null);
            }
        },
        scroll      : function(parent, child, id){
            function getScrollTop(target){
                if (target !== document.body){
                    return (typeof target.pageYOffset !== 'undefined' ? target.pageYOffset : (typeof target.scrollTop !== 'undefined' ? target.scrollTop : -1));
                }else{
                    return pure.nodes.render.windowScroll().top;
                }
            };
            var element     = {},
                scroll      = {},
                data        = pure.components.effects.fixscroll.resize.get(id);
            if (data !== null){
                try{
                    element = {
                        offset  : data.offset.top,
                        height  : Math.ceil(pure.nodes.render.size(child).height) + 1 /*+1 => it's fix ceil of IE. IE loses px, during detecting size of node*/
                    };
                    scroll  = {
                        top     : getScrollTop(parent),
                        height  : (parent === document.body ? pure.nodes.render.windowSize().height : pure.nodes.render.size(parent).height)
                    };
                    if (element.offset + element.height < scroll.top + scroll.height){
                        if (child.style.position !== 'fixed'){
                            child.style.position    = 'fixed';
                            child.style.top         = -(element.height-scroll.height) + 'px';
                            pure.appevents.Actions.call('pure.components.effects.fixscroll', 'addFixed', child, null);
                        }
                    }else{
                        if (child.style.position !== ''){
                            child.style.position    = '';
                            child.style.top         = '';
                            pure.appevents.Actions.call('pure.components.effects.fixscroll', 'removeFixed', child, null);
                        }
                    }
                }catch (e){}
            }
        }
    };
    pure.system.start.add(pure.components.effects.fixscroll.init);
}());