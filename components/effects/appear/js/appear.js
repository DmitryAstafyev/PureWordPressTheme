(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components               !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.effects       !== "object") { window.pure.components.effects      = {}; }
    "use strict";
    /*
    * data-effects-appear="here selector of scrolling object",
    * for example: data-effects-appear="body" OR data-effects-appear="*[id=|3333|]" USE (|) instead symbol (")
    *
    * */
    window.pure.components.effects.appear = {
        storage     : [],
        init        : function(){
            var instances = pure.nodes.select.all('*[data-effects-appear]:not([data-type-element-inited])');
            if (instances !== null){
                for(var index = instances.length - 1; index >= 0; index -= 1){
                    (function(instance){
                        var selector    = instance.getAttribute('data-effects-appear'),
                            parent      = null;
                        if (selector !== null){
                            parent = pure.nodes.select.first(selector.replace(/\|/gi, '"'));
                            if (parent !== null){
                                pure.events.add(
                                    (parent === document.body ? window : parent),
                                    'scroll',
                                    function(){
                                        pure.components.effects.appear.scroll(parent, instance);
                                    }
                                );
                                pure.components.effects.appear.storage.push(
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
            try{
                if (child.className.indexOf('PureEffectAppear') === -1){
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
                        child.className = child.className + (child.className !== '' ? ' ' : '') + 'PureEffectAppear';
                    }
                }
            }catch (e){}
        }
    };
    pure.system.start.add(pure.components.effects.appear.init);
}());