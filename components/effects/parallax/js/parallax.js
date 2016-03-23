(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components               !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.effects       !== "object") { window.pure.components.effects      = {}; }
    "use strict";
    /*
    Make background node (DIV is the best choose). Attach two attributes to it:
    - data-effects-parallax-scroll
    - data-effects-parallax-offset

    [data-effects-parallax-scroll] is the selector of node which should be listen for onScroll event.
    For example:
    <div data-effects-parallax-scroll="body"></div>
    or
    <div data-effects-parallax-scroll="div[date-type=|somevalue|]"></div>
    Use | instead " in selector

    data-effects-parallax-offset is a number from 0 to 100. It's maximum offset of background.

    Also you should define CSS for your background - width should be same + 100 as [data-effects-parallax-offset]
    {
        width : 115%;
    }
    for data-effects-parallax-offset="15"
    */
    window.pure.components.effects.parallax = {
        init : function(){
            var instances = pure.nodes.select.all('*[data-effects-parallax-scroll]:not([data-type-element-inited])');
            if (instances !== null){
                for(var index = instances.length - 1; index >= 0; index -= 1){
                    (function(instance){
                        var data = {
                                scroll      : instance.getAttribute('data-effects-parallax-scroll'),
                                offset      : instance.getAttribute('data-effects-parallax-offset')
                            },
                            style = pure.nodes.render.computedStyle(instance);
                        if (pure.tools.objects.isValueIn(data, null) === false && style !== null){
                            if (style.display !== 'none'){
                                data.scroll     = pure.nodes.select.first(data.scroll.replace(/\|/gi, '"'));
                                data.background = instance;
                                data.offset     = parseInt(data.offset, 10);
                                if (pure.tools.objects.isValueIn(data, null) === false){
                                    pure.events.add(
                                        (data.scroll === document.body ? window : data.scroll),
                                        'scroll',
                                        function(event){
                                            pure.components.effects.parallax.onScroll(event, data);
                                        }
                                    );
                                    pure.components.effects.parallax.onScroll(null, data);
                                }
                            }else{
                                instance.style.display = 'block';
                            }
                        }
                        instance.setAttribute('data-type-element-inited', 'true');
                    }(instances[index]));
                }
            }
        },
        onScroll: function (event, data) {
            function getScroll(target){
                if (target !== document.body){
                    return {
                        top     : (typeof target.pageYOffset !== 'undefined' ? target.pageYOffset : (typeof target.scrollTop !== 'undefined' ? target.scrollTop : -1)),
                        height  : target.scrollHeight
                    };
                }else{
                    return pure.nodes.render.windowScroll();
                }
            };
            var scrollData      = getScroll(data.scroll),
                clientHeight    = (data.scroll === document.body ? pure.nodes.render.windowSize().height : pure.nodes.render.size(data.scroll).height),
                pos             = (data.offset/100) * (scrollData.top/(scrollData.height - clientHeight));
            data.background.style.top = -(pos * 100) + "%";
        }
    };
    pure.system.start.add(pure.components.effects.parallax.init);
}());