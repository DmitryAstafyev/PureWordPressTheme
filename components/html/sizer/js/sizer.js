(function () {
    if (typeof window.pure                  !== "object") { window.pure                 = {}; }
    if (typeof window.pure.components       !== "object") { window.pure.components      = {}; }
    if (typeof window.pure.components.html  !== "object") { window.pure.components.html = {}; }
    "use strict";
    window.pure.components.html.sizer = {
        storage     : {
            data    : {},
            index   : 0,
            get id() {
                pure.components.html.sizer.storage.index += 1;
                return pure.components.html.sizer.storage.index;
            },
            add     : function(id, attributes, container){
                pure.components.html.sizer.storage.data[id] = {
                    id          : id,
                    attr        : attributes,
                    container   : container
                };
            }
        },
        containers  : {
            getAttributes   : function(container){
                var attributes = {
                    height : {
                        basic   : container.getAttribute('data-engine-html-sizer-height-basic'  ),  //em || px || %
                        min     : container.getAttribute('data-engine-html-sizer-height-min'    ),  //em || px || %
                        max     : container.getAttribute('data-engine-html-sizer-height-max'    )   //em || px || %
                    },
                    width : {
                        basic   : container.getAttribute('data-engine-html-sizer-width-basic'  ),   //em || px || %
                        min     : container.getAttribute('data-engine-html-sizer-width-min'    ),   //em || px || %
                        max     : container.getAttribute('data-engine-html-sizer-width-max'    )    //em || px || %
                    },
                    position : {
                        horizontal   : container.getAttribute('data-engine-html-sizer-position-horizontal'  ),  //left || center || right
                        vertical     : container.getAttribute('data-engine-html-sizer-position-vertical'    )   //top || center || bottom
                    },
                    id      : container.getAttribute('data-engine-html-sizer-id'  )
                };
                if (attributes.id === null){
                    attributes.id = pure.components.html.sizer.storage.id;
                    container.setAttribute('data-engine-html-sizer-id', attributes.id);
                }
                if (pure.tools.objects.isValueIn(attributes, null, true) !== null){
                    return attributes;
                }
                return null;
            },
            parseAttributes : function(attributes, container){
                function parseSize(size, parent){
                    var result = null;
                    if (size.indexOf('em') !== -1){
                        size    = parseInt(size);
                        result  = {
                            value   : pure.nodes.convert.emToPx(size, parent),
                            mode    : 'fixed'
                        };
                    }
                    if (result === null){
                        if (size.indexOf('px') !== -1){
                            result  = {
                                value   : parseInt(size),
                                mode    : 'fixed'
                            };
                        }
                    }
                    if (result === null){
                        if (size.indexOf('%') !== -1){
                            result  = {
                                value   : parseInt(size),
                                mode    : 'flex'
                            };
                        }
                    }
                    return result;
                };
                if (attributes !== null){
                    attributes.width.basic  = parseSize(attributes.width.basic,     container.parentNode);
                    attributes.width.max    = parseSize(attributes.width.max,       container.parentNode);
                    attributes.width.min    = parseSize(attributes.width.min,       container.parentNode);
                    attributes.height.basic = parseSize(attributes.height.basic,    container.parentNode);
                    attributes.height.max   = parseSize(attributes.height.max,      container.parentNode);
                    attributes.height.min   = parseSize(attributes.height.min,      container.parentNode);
                    attributes.position.horizontal  = (['left', 'center', 'right'].indexOf(attributes.position.horizontal   ) !== -1 ? attributes.position.horizontal   : null);
                    attributes.position.vertical    = (['top', 'center', 'bottom'].indexOf(attributes.position.vertical     ) !== -1 ? attributes.position.vertical     : null);
                    if (pure.tools.objects.isValueIn(attributes, null, true) !== null){
                        return attributes;
                    }
                    return null;
                }
                return attributes;
            },
            init            : function(){
                var containers = pure.nodes.select.all('div[data-engine-html-sizer-element="Container"]:not([data-engine-html-sizer-inited])');
                if (containers !== null){
                    for (var index = containers.length - 1; index >= 0; index -= 1){
                        (function(container){
                            var attributes = pure.components.html.sizer.containers.getAttributes(container);
                            if (attributes !== null){
                                pure.components.html.sizer.containers.parseAttributes(attributes, container);
                                if (attributes !== null){
                                    pure.components.html.sizer.storage.add(attributes.id, attributes, container);
                                }
                            }
                            container.setAttribute('data-engine-html-sizer-inited', 'true');
                        }(containers[index]));
                    }
                    pure.components.html.sizer.actions.proceed();
                }
            }
        },
        actions : {
            isValid         : function(target){
                var container = pure.nodes.select.first('*[data-engine-html-sizer-id="' + target.id + '"]');
                if (container !== null){
                    return true;
                }else{
                    return false;
                }
            },
            proceed         : function(){
                var storage     = pure.components.html.sizer.storage.data,
                    remove      = [];
                for(var id in storage){
                    if (pure.components.html.sizer.actions.isValid      (storage[id]) !== false){
                        pure.components.html.sizer.actions.applySize    (storage[id]);
                    }else{
                        remove.push(id);
                    }
                }
                for (var index = remove.length - 1; index >= 0; index -= 1){
                    storage[remove[index]] = null;
                    delete storage[remove[index]];
                }
            },
            applySize       : function(target){
                function applyValue(target, pSize, value){
                    var cSize   = null,
                        max     = (target.attr[value].max.mode === 'flex' ? pSize[value] * (target.attr[value].max.value / 100) : target.attr[value].max.value),
                        min     = (target.attr[value].min.mode === 'flex' ? pSize[value] * (target.attr[value].min.value / 100) : target.attr[value].min.value);
                    if (target.attr[value].basic.mode === 'flex'){
                        target.container.style[value] = target.attr[value].basic.value + '%';
                        pure.nodes.render.redraw(target.container);
                    }
                    cSize = pure.nodes.render.size(target.container);
                    if (cSize[value] > max){
                        target.container.style[value] = max + 'px';
                    }else if (cSize[value] < min && pSize[value] > min){
                        target.container.style[value] = min + 'px';
                    }
                    return cSize;
                };
                var pSize = pure.nodes.render.size(target.container.parentNode),
                    cSize = null;
                if (pSize.width !== 0 && pSize.height !== 0){
                    if (typeof target.last === 'object'){
                        if (target.last.pSize.width === pSize.width && target.last.pSize.height === pSize.height){
                            return false;
                        }
                    }
                    cSize       = applyValue(target, pSize, 'width'   );
                    cSize       = applyValue(target, pSize, 'height'  );
                    target.last = {
                        pSize : pSize
                    };
                    pure.components.html.sizer.actions.applyPosition(target);
                }
            },
            applyPosition   : function(target){
                var pSize = target.last.pSize,
                    cSize = pure.nodes.render.size(target.container);
                switch (target.attr.position.horizontal){
                    case 'left':
                        target.container.style.left = '0px';
                        break;
                    case 'center':
                        target.container.style.left = (pSize.width / 2 - cSize.width / 2) +'px';
                        break;
                    case 'right':
                        target.container.style.right = '0px';
                        break;
                }
                switch (target.attr.position.vertical){
                    case 'top':
                        target.container.style.top = '0px';
                        break;
                    case 'center':
                        target.container.style.top = (pSize.height / 2 - cSize.height / 2) +'px';
                        break;
                    case 'bottom':
                        target.container.style.bottom = '0px';
                        break;
                }
            }
        },
        events : {
            status  : false,
            init    : function(){
                if (pure.components.html.sizer.events.status === false){
                    pure.events.add(
                        window,
                        'resize',
                        pure.components.html.sizer.actions.proceed
                    );
                    pure.appevents.Actions.listen(
                        'html',
                        'sizer.update',
                        pure.components.html.sizer.actions.proceed,
                        'pure.components.html.sizer.update'
                    );
                    pure.appevents.Actions.listen(
                        'html',
                        'sizer.init',
                        pure.components.html.sizer.containers.init,
                        'pure.components.html.sizer.init'
                    );
                    pure.components.html.sizer.events.status = true;
                }
            }
        },
        init: function(){
            //pure.components.html.sizer.containers.  init();
            pure.components.html.sizer.events.      init();
        }
    };
    pure.system.start.add(pure.components.html.sizer.init);
}());