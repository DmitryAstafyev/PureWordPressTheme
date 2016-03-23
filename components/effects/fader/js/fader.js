(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components               !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.effects       !== "object") { window.pure.components.effects      = {}; }
    "use strict";
    /*
    * data-effects-fader="here selector of scrolling object",
    * for example: data-effects-fader="body" OR data-effects-fader="*[id=|3333|]" USE | instead "
    *
    * CALLER OF FORCE UPDATE
     data-effects-fader-update-caller="change"          <-- event
     data-effects-fader-update-property-name="checked"
     data-effects-fader-update-property-type="boolean"
     data-effects-fader-update-property-value="true"    <-- value to do update
    * */
    window.pure.components.effects.fader = {
        storage     : [],
        init        : function(){
            var instances = pure.nodes.select.all('*[data-effects-fader]:not([data-type-element-inited])');
            if (instances !== null){
                for(var index = instances.length - 1; index >= 0; index -= 1){
                    (function(instance){
                        var selector    = instance.getAttribute('data-effects-fader'),
                            parent      = null;
                        if (selector !== null){
                            parent = pure.nodes.select.first(selector.replace(/\|/gi, '"'));
                            if (parent !== null){
                                pure.events.add(
                                    (parent === document.body ? window : parent),
                                    'scroll',
                                    function(){
                                        pure.components.effects.fader.scroll(parent, instance);
                                    }
                                );
                                pure.components.effects.fader.storage.push(
                                    {
                                        parent  : parent,
                                        child   : instance
                                    }
                                );
                            }
                        }
                        instance.setAttribute('data-type-element-inited', 'true');
                    }(instances[index]));
                }
                pure.components.effects.fader.forceUpdate.init();
                pure.components.effects.fader.exclusions.init();
            }
        },
        scroll      : function(parent, child){
            function getScrollTop(target){
                if (target !== document.body){
                    return (typeof parent.pageYOffset !== 'undefined' ? parent.pageYOffset : (typeof parent.scrollTop !== 'undefined' ? parent.scrollTop : -1));
                }else{
                    return pure.nodes.render.windowScroll().top;
                }
            };
            var element     = {},
                scroll      = {},
                space       = 0;
            if (pure.components.effects.fader.exclusions.isFixed(child) === false){
                try{
                    element = {
                        offset  : pure.nodes.render.offset(child).top,
                        height  : pure.nodes.render.size(child).height
                    };
                    scroll  = {
                        top     : getScrollTop(parent),
                        height  : (parent === document.body ? pure.nodes.render.windowSize().height : pure.nodes.render.size(parent).height)
                    };
                    space   = scroll.height * 0.25;
                    if (element.offset + element.height >= scroll.top && element.offset < scroll.top + scroll.height){
                        if (element.offset + element.height > scroll.top && element.offset + element.height < scroll.top + space){
                            child.style.opacity = (element.offset + element.height - scroll.top) / space;
                        }else if (element.offset < scroll.top + scroll.height && element.offset > scroll.top + scroll.height - space){
                            child.style.opacity = (scroll.top + scroll.height - element.offset) / space;
                        }else{
                            child.style.opacity = 1;
                        }
                    }else{
                        child.style.opacity = 0;
                    }
                }catch (e){}
            }
        },
        forceUpdate : {
            init : function(){
                var instances   = pure.nodes.select.all('*[data-effects-fader-update-caller]'),
                    handle      = null;
                if (instances !== null){
                    handle = function(){
                        for(var _index = pure.components.effects.fader.storage.length - 1; _index >= 0; _index -= 1){
                            if (pure.components.effects.fader.exclusions.isFixed(pure.components.effects.fader.storage[_index].child) === false) {
                                pure.nodes.render.redraw(pure.components.effects.fader.storage[_index].child);
                                pure.components.effects.fader.scroll(
                                    pure.components.effects.fader.storage[_index].parent,
                                    pure.components.effects.fader.storage[_index].child
                                );
                            }
                        }
                    };
                    Array.prototype.forEach.call(
                        instances,
                        function(item, index, source){
                            var attrs = {
                                    event       : item.getAttribute('data-effects-fader-update-caller'),
                                    property    : item.getAttribute('data-effects-fader-update-property-name'),
                                    type        : item.getAttribute('data-effects-fader-update-property-type'),
                                    value       : item.getAttribute('data-effects-fader-update-property-value')
                                };
                            if (pure.tools.objects.isValueIn(attrs, null) === false){
                                if (typeof item[attrs.property] !== 'undefined'){
                                    pure.events.add(
                                        item,
                                        attrs.event,
                                        function(){
                                            if (item[attrs.property] === pure.components.effects.fader.forceUpdate.getValue(attrs.value, attrs.type)){
                                                handle();
                                            }
                                        }
                                    );
                                }
                            }
                        }
                    );
                    pure.appevents.Actions.listen(
                        'pure.components.effects.fixscroll',
                        'addFixed',
                        handle,
                        'addFixedListenerForceUpdate'
                    );
                    pure.appevents.Actions.listen(
                        'pure.components.effects.fixscroll',
                        'removeFixed',
                        handle,
                        'removeFixedListenerForceUpdate'
                    );
                }
            },
            getValue : function(value, type){
                switch (type){
                    case 'number':
                        return parseInt(value, 10);
                        break;
                    case 'boolean':
                        return (value.toLowerCase() === 'false' ? false : true);
                        break;
                    case 'string':
                        //do nothing
                        break;
                }
                return null;
            }
        },
        exclusions  : {
            inited  : false,
            attrs   : {
                id          : 'data-temp-fader-id',
                exclusion   : 'data-fader-exclusion'
            },
            init    : function(){
                if (pure.components.effects.fader.exclusions.inited === false){
                    pure.appevents.Actions.listen(
                        'pure.components.effects.fixscroll',
                        'addFixed',
                        pure.components.effects.fader.exclusions.fixed,
                        'addFixedListener'
                    );
                    pure.appevents.Actions.listen(
                        'pure.components.effects.fixscroll',
                        'removeFixed',
                        pure.components.effects.fader.exclusions.unfixed,
                        'removeFixedListener'
                    );
                    pure.components.effects.fader.exclusions.inited = true;
                }
            },
            setState   : function(child, isFixed){
                var id          = Math.round(Math.random()*1000000),
                    instances   = null;
                child.setAttribute(pure.components.effects.fader.exclusions.attrs.id, id);
                instances = pure.nodes.select.all('*[data-temp-fader-id="' + id + '"] *[data-effects-fader]');
                child.removeAttribute(pure.components.effects.fader.exclusions.attrs.id);
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(item, _index, source){
                            if (isFixed === false){
                                item.removeAttribute(pure.components.effects.fader.exclusions.attrs.exclusion);
                            }else{
                                item.setAttribute(pure.components.effects.fader.exclusions.attrs.exclusion, 'true');
                            }
                        }
                    );
                }
            },
            fixed   : function(child){
                pure.components.effects.fader.exclusions.setState(child, true);
            },
            unfixed : function(child){
                pure.components.effects.fader.exclusions.setState(child, false);
            },
            isFixed : function(node){
                return (node.getAttribute(pure.components.effects.fader.exclusions.attrs.exclusion) === null ? false : true);
            }
        }
    };
    pure.system.start.add(pure.components.effects.fader.init);
}());