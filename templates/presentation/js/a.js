(function () {
    if (typeof window.pure              !== "object") { window.pure                 = {}; }
    if (typeof window.pure.presentation !== "object") { window.pure.presentation    = {}; }
    "use strict";
    window.pure.presentation.A = {
        init : function(){
            var instances   = pure.nodes.select.all('*[data-engine-presentation-container]'),
                id          = 0;
            if (instances !== null){
                Array.prototype.forEach.call(
                    instances,
                    function(item, _index, source){
                        var nodes   = null,
                            idAttr  = 'data-engine-presentation-ID';
                        item.setAttribute(idAttr, id);
                        nodes = {
                            sliders     : pure.nodes.select.all('*[data-engine-presentation-ID="' + id + '"] ul[data-engine-presentation-items] > li'),
                            previous    : pure.nodes.select.first('*[data-engine-presentation-ID="' + id + '"] *[data-engine-presentation-button="previous"]'),
                            next        : pure.nodes.select.first('*[data-engine-presentation-ID="' + id + '"] *[data-engine-presentation-button="next"]')
                        };
                        if (pure.tools.objects.isValueIn(nodes, null) === false){
                            if (nodes.sliders.length > 1){
                                pure.events.add(
                                    nodes.previous,
                                    'click',
                                    function(){
                                        pure.presentation.A.actions.previous(nodes.sliders, id);
                                    }
                                );
                                pure.events.add(
                                    nodes.next,
                                    'click',
                                    function(){
                                        pure.presentation.A.actions.next(nodes.sliders, id);
                                    }
                                );
                            }
                            pure.presentation.A.timer.proceed(nodes.sliders, id);
                        }
                        id += 1;
                    }
                );
            }
        },
        actions     : {
            clone       : null,
            previous    : function(sliders, id){
                pure.presentation.A.actions.move(sliders, 'previous');
            },
            next        : function(sliders, id){
                pure.presentation.A.actions.move(sliders, 'next');
            },
            move        : function(sliders, direction, id){
                var next    = null,
                    clone   = null;
                pure.presentation.A.timer.clear(id);
                if (pure.presentation.A.actions.clone !== null){
                    pure.presentation.A.actions.clone.parentNode.removeChild(pure.presentation.A.actions.clone);
                }
                for(var index = 0, max_index = sliders.length; index < max_index; index += 1){
                    if (sliders[index].style.display !== 'none'){
                        switch (direction){
                            case 'previous':
                                next = (index > 0 ? index - 1 : sliders.length - 1);
                                break;
                            case 'next':
                                next = (index < sliders.length - 1 ? index +1 : 0);
                                break;
                        }
                        clone                               = sliders[index].cloneNode(true);
                        clone.style.position                = "absolute";
                        clone.style.top                     = '0';
                        clone.style.left                    = '0';
                        sliders[next    ].style.display     = '';
                        sliders[next    ].parentNode.insertBefore(clone, sliders[next]);
                        clone.removeAttribute('data-presentation-animate');
                        sliders[index   ].style.display     = 'none';
                        pure.presentation.A.actions.clone   = clone;
                        pure.presentation.A.timer.proceed(sliders, id);
                        return true;
                    }
                }
            }
        },
        timer       : {
            ids     : {},
            clear   : function(id){
                if (typeof pure.presentation.A.timer.ids[id] !== 'undefined'){
                    clearTimeout(pure.presentation.A.timer.ids[id]);
                }
            },
            proceed : function(sliders, id){
                pure.presentation.A.timer.ids[id] = setTimeout(
                    function(){
                        pure.presentation.A.actions.move(sliders, 'next');
                    },
                    10000
                );
            }
        }
    };
    pure.system.start.add(pure.presentation.A.init);
}());