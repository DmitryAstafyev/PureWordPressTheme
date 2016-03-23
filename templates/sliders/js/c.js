(function () {
    if (typeof window.pure          !== "object") { window.pure         = {}; }
    if (typeof window.pure.sliders  !== "object") { window.pure.sliders = {}; }
    "use strict";
    window.pure.sliders.C = {
        storage : {
            data    : {},
            add     : function(id, nodes, items, containers){
                var storage = pure.sliders.C.storage.data;
                if (typeof storage[id] === 'undefined'){
                    storage[id] = {
                        nodes       : nodes,
                        items       : items,
                        containers  : containers,
                        current     : 0
                    };
                    return true;
                }
                return false;
            },
            get     : function(id){
                var storage = pure.sliders.C.storage.data;
                return (typeof storage[id] !== 'undefined' ? storage[id] : null);
            }
        },
        init    : function () {
            var instances   = pure.nodes.select.all("div[data-engine-element=\"Slider.C\"]:not([data-element-inited])");
            if (instances !== null) {
                if (typeof instances.length === "number") {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance){
                            var id          = Math.round(Math.random() * 1000 * Math.random()),
                                nodes       = null,
                                containers  = null,
                                items       = null,
                                _items      = [];
                            instance.setAttribute('data-engine-element-id', id      );
                            instance.setAttribute('data-element-inited',    'true'  );
                            nodes = {
                                left        : pure.nodes.select.first('*[data-engine-element-id="' + id + '"] *[data-engine-slider-control="Slider.C.Button.Left"]'),
                                right       : pure.nodes.select.first('*[data-engine-element-id="' + id + '"] *[data-engine-slider-control="Slider.C.Button.Right"]'),
                                container   : instance
                            };
                            if (pure.tools.objects.isValueIn(nodes, null) === false){
                                containers  = pure.nodes.select.all('*[data-engine-element-id="' + id + '"] *[data-engine-element="Slider.C.Item.Container"]');
                                items       = pure.nodes.select.all('*[data-engine-element-id="' + id + '"] *[data-engine-element="Slider.C.Item.Value"]');
                                if (items !== null && containers !== null){
                                    for (var index = 0, max_index = items.length; index < max_index; index += 1){
                                        _items.push(items[index].cloneNode(true));
                                    }
                                    pure.sliders.C.storage.add(id, nodes, _items, containers);
                                    pure.sliders.C.resize.update(id);
                                    pure.events.add(
                                        nodes.left,
                                        'click',
                                        function(){
                                            pure.sliders.C.actions.left(id);
                                            pure.sliders.C.resize.update(id);
                                        }
                                    );
                                    pure.events.add(
                                        nodes.right,
                                        'click',
                                        function(){
                                            pure.sliders.C.actions.right(id);
                                            pure.sliders.C.resize.update(id);
                                        }
                                    );
                                    pure.events.add(
                                        window,
                                        'resize',
                                        function(){
                                            pure.sliders.C.resize.update(id);
                                        }
                                    );
                                }
                            }
                        }(instances[index]));
                    }
                }
            }
        },
        resize  : {
            elements    : {
                container   : function(container){
                    var size = pure.nodes.render.size(container),
                        rate = 100/25;
                    size.height             = size.width / rate;
                    container.style.height  = size.height + 'px';
                    return size;
                },
                containers  : function(containers, size, count){
                    function oneRow(){
                        left    = 0;
                        top     = 0;
                        for(var index = 0, max_index = count; index < max_index; index += 1){
                            containers[index].style.top      = top       + 'px';
                            containers[index].style.left     = left      + 'px';
                            containers[index].style.width    = width     + 'px';
                            containers[index].style.height   = height    + 'px';
                            containers[index].setAttribute('data-engine-element-style', 'small');
                            left += width;
                        }
                    }
                    function twoRows(){
                        containers[0].style.height     = (height * 2) + 'px';
                        containers[0].style.width      = (width * 2) + 'px';
                        containers[0].style.top        = 0 + 'px';
                        containers[0].style.left       = 0 + 'px';
                        containers[0].setAttribute('data-engine-element-style', 'large');
                        left    = width * 2;
                        top     = 0;
                        for(var index = 1, max_index = count; index < max_index; index += 1){
                            containers[index].style.top      = top       + 'px';
                            containers[index].style.left     = left      + 'px';
                            containers[index].style.width    = width     + 'px';
                            containers[index].style.height   = height    + 'px';
                            containers[index].setAttribute('data-engine-element-style', 'small');
                            if (top > 0){
                                left += width;
                                top = 0;
                            }else{
                                top += height;
                            }
                        }
                    }
                    var rate    = 100 / 60,
                        top     = 0,
                        left    = 0,
                        width   = (size.height * rate) / 2,
                        height  = size.height / 2;
                    if (containers.length > 0){
                        if (size.height < 200){
                            width   = size.height * rate;
                            height  = size.height;
                            oneRow();
                        }else{
                            width   = (size.height * rate) / 2;
                            height  = size.height / 2;
                            twoRows();
                        }
                    }
                }
            },
            update      : function(id){
                var data = pure.sliders.C.storage.get(id),
                    size = null;
                if (data !== null){
                    size = pure.sliders.C.resize.elements.container(data.nodes.container);
                    pure.sliders.C.resize.elements.containers(data.containers, size, data.items.length);
                }
            }
        },
        actions : {
            move    : function(id, offset){
                function left(data){
                    var current = data.current,
                        count   = data.containers.length;
                    for(var index = 0, max_index = data.containers.length; index < max_index; index += 1){
                        (function(container, items, current, count){
                            var first   = null,
                                second  = null;
                            container.innerHTML = '';
                            first               = items[current].cloneNode(true);
                            second              = items[(current + 1 === count ? 0 : current + 1)].cloneNode(true);
                            first.style.left    = '0px';
                            first.style.top     = '0px';
                            second.style.left   = '100%';
                            second.style.top    = '0px';
                            container.appendChild(first);
                            container.appendChild(second);
                            setTimeout(function(){
                                first.style.left    = '-100%';
                                second.style.left   = '0';
                            }, 50);
                        }(data.containers[index], data.items, current, count));
                        current = (current + 1 === count ? 0 : current + 1);
                    }
                };
                function right(data){
                    var current = data.current,
                        count   = data.containers.length;
                    for(var index = 0, max_index = data.containers.length; index < max_index; index += 1){
                        (function(container, items, current, count){
                            var first   = null,
                                second  = null;
                            container.innerHTML = '';
                            first               = items[current].cloneNode(true);
                            second              = items[(current - 1 < 0 ? count - 1 : current - 1)].cloneNode(true);
                            first.style.left    = '0%';
                            first.style.top     = '0px';
                            second.style.left   = '-100%';
                            second.style.top    = '0px';
                            container.appendChild(first);
                            container.appendChild(second);
                            setTimeout(function(){
                                first.style.left    = '100%';
                                second.style.left   = '0%';
                            }, 50);
                        }(data.containers[index], data.items, current, count));
                        current = (current + 1 === count ? 0 : current + 1);
                    }
                };
                var data = pure.sliders.C.storage.get(id);
                if (data !== null){
                    if (offset > 0){
                        left(data);
                        data.current = (data.current + 1 === data.items.length ? 0 : data.current + 1);
                    }else{
                        right(data);
                        data.current = (data.current - 1 < 0 ? data.items.length - 1 : data.current - 1);
                    }
                }

            },
            left    : function(id){
                pure.sliders.C.actions.move(id, -1);
            },
            right   : function(id){
                pure.sliders.C.actions.move(id, +1);
            }
        },
        WindowsResize   : {
            handles : {
                data    : [],
                add     : function(handle){
                    if (pure.sliders.B.WindowsResize.init !== null){
                        pure.sliders.B.WindowsResize.init();
                        pure.sliders.B.WindowsResize.init = null;
                    }
                    pure.sliders.B.WindowsResize.handles.data.push(handle);
                },
                get : function(){
                    return pure.sliders.B.WindowsResize.handles.data;
                }
            },
            inited  : false,
            init    : function(){
                if (pure.sliders.B.WindowsResize.inited === false){
                    pure.events.add(window,"resize", pure.sliders.B.WindowsResize.resize);
                    pure.sliders.B.WindowsResize.inited = true;
                }
            },
            resize  : function(event){
                var handles = pure.sliders.B.WindowsResize.handles.get();
                for(var index = handles.length - 1; index >= 0; index -= 1){
                    pure.system.runHandle(handles[index], null, '', event);
                }
            }
        }
    };
    pure.system.start.add(pure.sliders.C.init);
}());