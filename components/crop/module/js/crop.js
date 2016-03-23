(function () {
    if (typeof window.pure                  !== "object") { window.pure                 = {}; }
    if (typeof window.pure.components       !== "object") { window.pure.components      = {}; }
    if (typeof window.pure.components.crop  !== "object") { window.pure.components.crop = {}; }
    "use strict";
    window.pure.components.crop.module = {
        data: {
            storage : {},
            set     : function (id, params, size) {
                var id = (typeof id === "string" ? id : pure.tools.IDs.get("components.crop.module"));
                if (typeof pure.components.crop.module.data.storage[id] !== "object") {
                    pure.components.crop.module.data.storage[id] = {
                        id      : id,
                        nodes   : {
                            target      : params.target,
                            selected    : null,
                            container   : null,
                            corners     : {
                                tl  : null,
                                tr  : null,
                                bl  : null,
                                br  : null
                            },
                            borders     : {
                                t   : null,
                                r   : null,
                                b   : null,
                                l   : null
                            }
                        },
                        size    : {
                            node    : {
                                width   : size.node.width,
                                height  : size.node.height
                            },
                            image   : {
                                width   : size.image.width,
                                height  : size.image.height
                            },
                            ratio   : {
                                byX     : null,
                                byY     : null
                            }
                        },
                        selection   : params.selection,
                        min         : params.min,
                        max         : params.max,
                        ratio       : params.ratio,
                        action      : {
                            current     : null,
                            direction   : null,
                            x           : null,
                            y           : null
                        }
                    };
                    return pure.components.crop.module.data.storage[id];
                }
                return null;
            },
            get     : function (id) {
                return (typeof pure.components.crop.module.data.storage[id] === "object" ? pure.components.crop.module.data.storage[id] : null);
            },
            remove  : function (id) {
                pure.components.crop.module.data.storage[id] = null;
                delete pure.components.crop.module.data.storage[id];
            },
            getAll : function(){
                var data = [];
                for(var key in pure.components.crop.module.data.storage){
                    data.push(pure.components.crop.module.data.storage[key]);
                }
                return data;
            }
        },
        global  : {
            inited  : false,
            init    : function(){
                if (pure.components.crop.module.global.inited === false){
                    pure.components.crop.module.global.inited = true;
                    pure.events.add(
                        window,
                        'resize',
                        function(){
                            var instances = pure.components.crop.module.data.getAll();
                            Array.prototype.forEach.call(
                                instances,
                                function(instance, _index, soruce){
                                    pure.components.crop.module.render.size.update(instance);
                                }
                            );
                        }
                    );
                }
            }
        },
        methods : {
            /*
            (!)Warning(!)
            <img> should have position: relative or absolute
            */
            attach          : function(params){
                function validateSizes(sizeObj, imageSize, ratio, type){
                    function getDefault(imageSize, type){
                        switch (type){
                            case "selection":
                                sizeObj = {
                                    x : 0,
                                    y : 0,
                                    w : Math.round(imageSize.image.width * 0.5),
                                    h : Math.round(imageSize.image.height * 0.5)
                                };
                                break;
                            case "max":
                                sizeObj = {
                                    w : imageSize.image.width,
                                    h : imageSize.image.height
                                };
                                break;
                            case "min":
                                sizeObj = {
                                    w : Math.round(imageSize.image.width * 0.1),
                                    h : Math.round(imageSize.image.height * 0.1)
                                };
                                break;
                        }
                        return sizeObj;
                    };
                    function applyRatio(sizeObj, imageSize, ratio){
                        // ww/hh => width / height => 16 / 9 => 16:9
                        function getCorrection(sizeObj, imageSize){
                            if ((sizeObj.w + sizeObj.x) > imageSize.image.width){
                                return imageSize.image.width / (sizeObj.w + sizeObj.x);
                            }
                            if ((sizeObj.h + sizeObj.y) > imageSize.image.height){
                                return imageSize.image.height / (sizeObj.h + sizeObj.y);
                            }
                            return 1;
                        };
                        var correction = 1;
                        if (ratio > 0){
                            if (sizeObj.w / sizeObj.h !== ratio){
                                sizeObj.w = sizeObj.h * ratio;
                            }
                            correction  = getCorrection(sizeObj, imageSize);
                            sizeObj.w   = sizeObj.w * correction;
                            sizeObj.h   = sizeObj.h * correction;
                        }
                        return sizeObj;
                    };
                    if (pure.tools.objects.validate(sizeObj, [  { name: "x", type: "number", value: 0   },
                                                                { name: "y", type: "number", value: 0   },
                                                                { name: "w", type: "number"             },
                                                                { name: "h", type: "number"             }]) === true) {
                        if (sizeObj.x < 0                                   || sizeObj.y < 0                        ||
                            sizeObj.w < 0                                   || sizeObj.h < 0                        ||
                            sizeObj.x > imageSize.image.width               || sizeObj.y > imageSize.image.height   ||
                            sizeObj.x + sizeObj.w > imageSize.image.width   || sizeObj.y + sizeObj.h > imageSize.image.height){
                            sizeObj = getDefault(imageSize, type);
                        }
                    }else{
                        sizeObj = getDefault(imageSize, type);
                    }
                    return applyRatio(sizeObj, imageSize, ratio);
                };
                var imageSize   = {node: null, image : null, ratio: null},
                    instance    = null;
                if (pure.tools.objects.validate(params,[{ name: "target",       type: "node"                },
                                                        { name: "selection",    type: "object", value: {}   },
                                                        { name: "id",           type: "string", value: pure.tools.IDs.get("components.crop.module") },
                                                        { name: "ratio",        type: "number", value: -1   },
                                                        { name: "min",          type: "object", value: {}   },
                                                        { name: "max",          type: "object", value: {}   }]) === true) {
                    //Get sizes
                    imageSize.node  = pure.nodes.render.size        (params.target);
                    imageSize.image = pure.nodes.render.imageSize   (params.target);
                    if (imageSize.node.width    > 0 && imageSize.node.height    > 0 &&
                        imageSize.image.width   > 0 && imageSize.image.height   > 0){
                        //Correct options
                        params.ratio        = (params.ratio > 10 ? 10 : params.ratio);
                        params.selection    = validateSizes(params.selection,  imageSize, params.ratio, 'selection' );
                        params.max          = validateSizes(params.max,        imageSize, params.ratio, 'max'       );
                        params.min          = validateSizes(params.min,        imageSize, params.ratio, 'min'       );
                        //Save data
                        instance = pure.components.crop.module.data.set(params.id, params, imageSize);
                        if (instance !== null){
                            //Try render crop box
                            instance = pure.components.crop.module.render.make(instance);
                            if (instance !== null){
                                //Set default size
                                pure.components.crop.module.render.position.set(instance, true  );
                                pure.components.crop.module.render.size.    set(instance, false );
                                //Attach events
                                pure.components.crop.module.events.init(instance);
                                //Global
                                pure.components.crop.module.global.init();
                                return params.id;
                            }else{
                                pure.components.crop.module.data.remove(params.id);
                            }
                        }
                    }
                }
                return false;
            },
            getSelection    : function(id){
                var instance = (typeof id === 'string' ? pure.components.crop.module.data.get(id) : null);
                if (instance !== null){
                    return {
                        x : instance.selection.x,
                        y : instance.selection.y,
                        w : instance.selection.w,
                        h : instance.selection.h
                    }
                }
                return null;
            },
            destroy         : function(id){
                var instance = (typeof id === 'string' ? pure.components.crop.module.data.get(id) : null);
                if (instance !== null){
                    instance.nodes.container.parentNode.removeChild(instance.nodes.container);
                    pure.components.crop.module.data.remove(id);
                    return true;
                }
                return null;
            }
        },
        render : {
            makeup      : {
                container: {
                    container   : { node: "DIV", name: "data-type-element", value: "Pure.Components.Crop.Container"  },
                    border      : {
                        container   : { node: "DIV", name: "data-type-element", value: "Pure.Components.Crop.Border"    },
                        tl          : { node: "DIV", name: "data-type-element", value: "Pure.Components.Crop.Corner.TL" },
                        tr          : { node: "DIV", name: "data-type-element", value: "Pure.Components.Crop.Corner.TR" },
                        bl          : { node: "DIV", name: "data-type-element", value: "Pure.Components.Crop.Corner.BL" },
                        br          : { node: "DIV", name: "data-type-element", value: "Pure.Components.Crop.Corner.BR" },
                        t           : { node: "DIV", name: "data-type-element", value: "Pure.Components.Crop.Sides.T"   },
                        r           : { node: "DIV", name: "data-type-element", value: "Pure.Components.Crop.Sides.R"   },
                        b           : { node: "DIV", name: "data-type-element", value: "Pure.Components.Crop.Sides.B"   },
                        l           : { node: "DIV", name: "data-type-element", value: "Pure.Components.Crop.Sides.L"   },
                        settingup   : {
                            parent  : "container",
                            childs  : ['t', 'r', 'b', 'l', 'tl', 'tr', 'bl', 'br']
                        }
                    },
                    settingup   : {
                        parent  : "container",
                        childs  : ["border"]
                    }
                }
            },
            make        : function(instance){
                var instance    = (typeof instance === 'object' ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null)),
                    _nodes      = null;
                if (instance !== null){
                    _nodes = pure.nodes.builder(pure.components.crop.module.render.makeup.container);
                    if (_nodes !== null){
                        instance.nodes.container    = _nodes.container;
                        instance.nodes.selected     = _nodes.border.container;
                        instance.nodes.corners.tl   = _nodes.border.tl;
                        instance.nodes.corners.tr   = _nodes.border.tr;
                        instance.nodes.corners.bl   = _nodes.border.bl;
                        instance.nodes.corners.br   = _nodes.border.br;
                        instance.nodes.borders.t    = _nodes.border.t;
                        instance.nodes.borders.r    = _nodes.border.r;
                        instance.nodes.borders.b    = _nodes.border.b;
                        instance.nodes.borders.l    = _nodes.border.l;
                        instance.nodes.target.parentNode.appendChild(_nodes.container);
                        return instance;
                    }
                }
                return null;
            },
            position    : {
                set     : function(instance, calculate){
                    var instance    = (typeof instance  === 'object'    ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null)),
                        calculate   = (typeof calculate === 'boolean'   ? calculate : false);
                    if (instance !== null){
                        instance = (calculate === true ? pure.components.crop.module.render.size.update(instance) : instance);
                        instance.nodes.selected.style.top                   = ((instance.size.ratio.byY * instance.selection.y) / instance.size.node.height) * 100 + '%';
                        instance.nodes.selected.style.left                  = ((instance.size.ratio.byX * instance.selection.x) / instance.size.node.width) * 100 + '%';
                        return true;
                    }
                },
                offset  : function(instance, offsetX, offsetY, calculate){
                    var instance    = (typeof instance  === 'object'    ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null)),
                        calculate   = (typeof calculate === 'boolean'   ? calculate : false),
                        values      = {x: 0, y: 0},
                        previous    = {x: 0, y: 0};
                    if (instance !== null){
                        instance = (calculate === true ? pure.components.crop.module.render.size.update(instance) : instance);
                        previous.x  = instance.selection.x;
                        previous.y  = instance.selection.y;
                        values.x    = instance.selection.x + offsetX / instance.size.ratio.byX;
                        values.y    = instance.selection.y + offsetY / instance.size.ratio.byY;
                        values.x    = (values.x < 0 ? 0 : values.x);
                        values.y    = (values.y < 0 ? 0 : values.y);
                        instance.selection.x = (values.x + instance.selection.w > instance.size.image.width ? instance.size.image.width - instance.selection.w : values.x);
                        instance.selection.y = (values.y + instance.selection.h > instance.size.image.height ? instance.size.image.height - instance.selection.h : values.y);
                        return {
                            x   : instance.selection.x - previous.x,
                            y   : instance.selection.y - previous.y,
                            _x  : (instance.selection.x - previous.x) * instance.size.ratio.byX,
                            _y  : (instance.selection.y - previous.y) * instance.size.ratio.byY
                        };
                    }
                }
            },
            size        : {
                update      : function(instance){
                    var instance = (typeof instance === 'object' ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null)),
                        size     = null;
                    if (instance !== null){
                        size                = pure.nodes.render.size(instance.nodes.target);
                        instance.size.node  = {
                            width   : size.width,
                            height  : size.height
                        };
                        instance.size.ratio = {
                            byX     : instance.size.node.width / instance.size.image.width,
                            byY     : instance.size.node.height / instance.size.image.height
                        };
                        instance.nodes.container.style.width    = instance.size.node.width + 'px';
                        instance.nodes.container.style.height   = instance.size.node.height + 'px';
                        return instance;
                    }
                    return false;
                },
                set         : function(instance, calculate){
                    var instance    = (typeof instance  === 'object'    ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null)),
                        calculate   = (typeof calculate === 'boolean'   ? calculate : false);
                    if (instance !== null){
                        instance = (calculate === true ? pure.components.crop.module.render.size.update(instance) : instance);
                        instance.nodes.selected.style.height    = ((instance.size.ratio.byY * instance.selection.h) / instance.size.node.height) * 100 + '%';
                        instance.nodes.selected.style.width     = ((instance.size.ratio.byX * instance.selection.w) / instance.size.node.width) * 100 + '%';
                        instance.nodes.container.style.width    = instance.size.node.width + 'px';
                        instance.nodes.container.style.height   = instance.size.node.height + 'px';
                        return true;
                    }
                },
                correction  : function(instance, offsetX, offsetY, updateSize){
                    function applyRatio(instance, values){
                        // ww/hh => width / height => 16 / 9 => 16:9
                        function getCorrection(instance, size){
                            if ((size.w + instance.selection.x) > instance.size.image.width){
                                return instance.size.image.width / (size.w + instance.selection.x);
                            }
                            if ((size.h + instance.selection.y) > instance.size.image.height){
                                return instance.size.image.height / (size.h + instance.selection.y);
                            }
                            return 1;
                        };
                        var correction  = 1,
                            size        = { w: values.w, h: values.h };
                        if (instance.ratio > 0) {
                            if (values.w / values.h !== instance.ratio) {
                                size.h = values.h;
                                size.w = size.h * instance.ratio;
                            }
                            correction = getCorrection(instance, size);
                            size.w = size.w * correction;
                            size.h = size.h * correction;
                        }
                        return size;
                    };
                    var instance    = (typeof instance  === 'object'    ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null)),
                        calculate   = (typeof calculate === 'boolean'   ? calculate : false),
                        values      = {w: 0, h: 0},
                        previous    = {w: 0, h: 0},
                        ratioSize   = null;
                    if (instance !== null){
                        instance    = (calculate === true ? pure.components.crop.module.render.size.update(instance) : instance);
                        previous.w  = instance.selection.w;
                        previous.h  = instance.selection.h;
                        values.w    = previous.w + offsetX / instance.size.ratio.byX;
                        values.h    = previous.h + offsetY / instance.size.ratio.byY;
                        values.w    = (values.w > instance.max.w ? instance.max.w : values.w);
                        values.h    = (values.h > instance.max.h ? instance.max.h : values.h);
                        values.w    = (values.w < instance.min.w ? instance.min.w : values.w);
                        values.h    = (values.h < instance.min.h ? instance.min.h : values.h);
                        values.w    = (values.w + instance.selection.x > instance.size.image.width ? instance.size.image.width - instance.selection.x : values.w);
                        values.h    = (values.h + instance.selection.y > instance.size.image.height ? instance.size.image.height - instance.selection.y : values.h);
                        ratioSize   = applyRatio(instance, values);
                        values.w    = ratioSize.w;
                        values.h    = ratioSize.h;
                        return {
                            w   : values.w - previous.w,
                            h   : values.h - previous.h,
                            _w  : (values.w - previous.w) * instance.size.ratio.byX,
                            _h  : (values.h - previous.h) * instance.size.ratio.byY
                        };
                    }
                },
                offset      : function(instance, offsetX, offsetY, calculate, updateSize){
                    var instance    = (typeof instance  === 'object'    ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null)),
                        calculate   = (typeof calculate === 'boolean'   ? calculate : false),
                        values      = {w: 0, h: 0},
                        previous    = {w: 0, h: 0};
                    if (instance !== null){
                        if (calculate === true){
                            values      = pure.components.crop.module.render.size.correction(instance.id, offsetX, offsetY, updateSize);
                            values.w    = instance.selection.w + values.w;
                            values.h    = instance.selection.h + values.h;
                        }else{
                            values = {
                                w : instance.selection.w + offsetX / instance.size.ratio.byX,
                                h : instance.selection.h + offsetY / instance.size.ratio.byY
                            }
                        }
                        previous.w              = instance.selection.w;
                        previous.h              = instance.selection.h;
                        instance.selection.w    = values.w;
                        instance.selection.h    = values.h;
                        return {
                            w   : instance.selection.w - previous.w,
                            h   : instance.selection.h - previous.h,
                            _w  : (instance.selection.w - previous.w) * instance.size.ratio.byX,
                            _h  : (instance.selection.h - previous.h) * instance.size.ratio.byY
                        };
                    }
                }
            }
        },
        events : {
            init : function(instance){
                var instance = (typeof instance  === 'object'    ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                if (instance !== null){
                    pure.components.crop.module.events.move.            init(instance);
                    pure.components.crop.module.events.resize.corners.  init(instance);
                    pure.components.crop.module.events.resize.borders.  init(instance);
                    return true;
                }
            },
            move    : {
                init        : function(instance){
                    var instance = (typeof instance  === 'object'    ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                    if (instance !== null){
                        pure.events.add(
                            instance.nodes.selected,
                            'mousedown',
                            function(event){
                                pure.components.crop.module.events.move.mousedown(event, instance.id);
                                event.preventDefault();
                                return false;
                            }
                        );
                        pure.events.add(
                            window,
                            'mousemove',
                            function(event){
                                pure.components.crop.module.events.move.mousemove(event, instance.id);
                            }
                        );
                        pure.events.add(
                            window,
                            'mouseup',
                            function(event){
                                pure.components.crop.module.events.move.mouseup(event, instance.id);
                            }
                        );
                        return true;
                    }
                },
                mousedown   : function(event, instance){
                    var instance = (typeof instance  === 'object'? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                    if (instance !== null) {
                        if (event.target === instance.nodes.selected){
                            pure.components.crop.module.render.position.set(instance.id, true);
                            instance.action.current = 'move';
                            instance.action.x       = event._clientX;
                            instance.action.y       = event._clientY;
                            event.preventDefault();
                            return false;
                        }
                    }
                },
                mousemove   : function(event, instance){
                    var instance = (typeof instance  === 'object'? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                    if (instance !== null) {
                        if (instance.action.current === 'move'){
                            if (event._clientX !== instance.action.x || event._clientY !== instance.action.y){
                                pure.components.crop.module.render.position.offset  (instance.id, -(instance.action.x - event._clientX), -(instance.action.y - event._clientY), false);
                                pure.components.crop.module.render.position.set     (instance.id, false);
                                instance.action.x = event._clientX;
                                instance.action.y = event._clientY;
                                event.preventDefault();
                                return false;
                            }
                        }
                    }
                },
                mouseup     : function(event, instance){
                    var instance = (typeof instance  === 'object'? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                    if (instance !== null) {
                        if (instance.action.current === 'move'){
                            if (event._clientX !== instance.action.x || event._clientY !== instance.action.y){
                                pure.components.crop.module.render.position.offset  (event._clientX - instance, instance.action.x, event._clientY - instance.action.y, false);
                                pure.components.crop.module.render.position.set     (instance, false);
                            }
                            instance.action.current = null;
                            instance.action.x       = null;
                            instance.action.y       = null;
                            event.preventDefault();
                            return false;
                        }
                    }
                }
            },
            resize  : {
                corners : {
                    init        : function(instance){
                        var instance = (typeof instance  === 'object'    ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                        if (instance !== null){
                            pure.events.add(
                                instance.nodes.corners.tl,
                                'mousedown',
                                function(event){
                                    pure.components.crop.module.events.resize.corners.mousedown(event, instance.id, 'tl');
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            pure.events.add(
                                instance.nodes.corners.tr,
                                'mousedown',
                                function(event){
                                    pure.components.crop.module.events.resize.corners.mousedown(event, instance.id, 'tr');
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            pure.events.add(
                                instance.nodes.corners.bl,
                                'mousedown',
                                function(event){
                                    pure.components.crop.module.events.resize.corners.mousedown(event, instance.id, 'bl');
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            pure.events.add(
                                instance.nodes.corners.br,
                                'mousedown',
                                function(event){
                                    pure.components.crop.module.events.resize.corners.mousedown(event, instance.id, 'br');
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            pure.events.add(
                                window,
                                'mousemove',
                                function(event){
                                    pure.components.crop.module.events.resize.corners.mousemove(event, instance.id);
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            pure.events.add(
                                window,
                                'mouseup',
                                function(event){
                                    pure.components.crop.module.events.resize.corners.mouseup(event, instance.id);
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            return true;
                        }
                    },
                    mousedown   : function(event, instance, type){
                        var instance = (typeof instance  === 'object'? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                        if (instance !== null) {
                            pure.components.crop.module.render.size.set(instance.id, true);
                            instance.action.current     = 'resize_corners';
                            instance.action.direction   = type;
                            instance.action.x           = event._clientX;
                            instance.action.y           = event._clientY;
                            event.preventDefault();
                            return false;
                        }
                    },
                    mousemove   : function(event, instance){
                        var instance    = (typeof instance  === 'object'? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null)),
                            available   = null,
                            offsets     = null;
                        if (instance !== null) {
                            if (instance.action.current === 'resize_corners'){
                                if (event._clientX !== instance.action.x || event._clientY !== instance.action.y){
                                    switch (instance.action.direction){
                                        case 'tl':
                                            offsets = pure.components.crop.module.render.size.correction(instance.id, instance.action.x - event._clientX, instance.action.y - event._clientY, false);
                                            if (offsets.w !== 0 || offsets.h !== 0){
                                                available = pure.components.crop.module.render.position.offset  (instance.id, -offsets._w, -offsets._h, false);
                                                pure.components.crop.module.render.size.                offset  (instance.id, -available._x, -available._y, false, false);
                                                pure.components.crop.module.render.size.                set     (instance.id, false);
                                                pure.components.crop.module.render.position.            set     (instance.id, false);
                                            }
                                            break;
                                        case 'tr':
                                            offsets = pure.components.crop.module.render.size.correction(instance.id, -(instance.action.x - event._clientX), instance.action.y - event._clientY, false);
                                            if (offsets.w !== 0 || offsets.h !== 0) {
                                                available = pure.components.crop.module.render.position.offset  (instance.id, 0, -offsets._h, false);
                                                pure.components.crop.module.render.size.                offset  (instance.id, offsets._w, -available._y, false, false);
                                                pure.components.crop.module.render.size.                set     (instance.id, false);
                                                pure.components.crop.module.render.position.            set     (instance.id, false);
                                            }
                                            break;
                                        case 'bl':
                                            offsets = pure.components.crop.module.render.size.correction(instance.id, instance.action.x - event._clientX, -(instance.action.y - event._clientY), false);
                                            if (offsets.w !== 0 || offsets.h !== 0) {
                                                available = pure.components.crop.module.render.position.offset  (instance.id, -offsets._w, 0, false);
                                                pure.components.crop.module.render.size.                offset  (instance.id, -available._x, offsets._h, false, false);
                                                pure.components.crop.module.render.size.                set     (instance.id, false);
                                                pure.components.crop.module.render.position.            set     (instance.id, false);
                                            }
                                            break;
                                        case 'br':
                                            offsets = pure.components.crop.module.render.size.correction(instance.id, -(instance.action.x - event._clientX), -(instance.action.y - event._clientY), false);
                                            if (offsets.w !== 0 || offsets.h !== 0) {
                                                pure.components.crop.module.render.size.                offset  (instance.id, offsets._w, offsets._h, false, false);
                                                pure.components.crop.module.render.size.                set     (instance.id, false);
                                            }
                                            break;
                                    }
                                    instance.action.x = event._clientX;
                                    instance.action.y = event._clientY;
                                    event.preventDefault();
                                    return false;
                                }
                            }
                        }
                    },
                    mouseup     : function(event, instance){
                        var instance = (typeof instance  === 'object'? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                        if (instance !== null) {
                            if (instance.action.current === 'resize_corners'){
                                instance.action.current     = null;
                                instance.action.direction   = null;
                                instance.action.x           = null;
                                instance.action.y           = null;
                                event.preventDefault();
                                return false;
                            }
                        }
                    }
                },
                borders : {
                    init        : function(instance){
                        var instance = (typeof instance  === 'object'    ? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                        if (instance !== null){
                            pure.events.add(
                                instance.nodes.borders.t,
                                'mousedown',
                                function(event){
                                    pure.components.crop.module.events.resize.borders.mousedown(event, instance.id, 't');
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            pure.events.add(
                                instance.nodes.borders.r,
                                'mousedown',
                                function(event){
                                    pure.components.crop.module.events.resize.borders.mousedown(event, instance.id, 'r');
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            pure.events.add(
                                instance.nodes.borders.b,
                                'mousedown',
                                function(event){
                                    pure.components.crop.module.events.resize.borders.mousedown(event, instance.id, 'b');
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            pure.events.add(
                                instance.nodes.borders.l,
                                'mousedown',
                                function(event){
                                    pure.components.crop.module.events.resize.borders.mousedown(event, instance.id, 'l');
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            pure.events.add(
                                window,
                                'mousemove',
                                function(event){
                                    pure.components.crop.module.events.resize.borders.mousemove(event, instance.id);
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            pure.events.add(
                                window,
                                'mouseup',
                                function(event){
                                    pure.components.crop.module.events.resize.borders.mouseup(event, instance.id);
                                    event.preventDefault();
                                    return false;
                                }
                            );
                            return true;
                        }
                    },
                    mousedown   : function(event, instance, type){
                        var instance = (typeof instance  === 'object'? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                        if (instance !== null) {
                            pure.components.crop.module.render.size.set(instance.id, true);
                            instance.action.current     = 'resize_borders';
                            instance.action.direction   = type;
                            instance.action.x           = event._clientX;
                            instance.action.y           = event._clientY;
                            event.preventDefault();
                            return false;
                        }
                    },
                    mousemove   : function(event, instance){
                        var instance    = (typeof instance  === 'object'? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null)),
                            available   = null,
                            offsets     = null;
                        if (instance !== null) {
                            if (instance.action.current === 'resize_borders'){
                                if (event._clientX !== instance.action.x || event._clientY !== instance.action.y){
                                    switch (instance.action.direction){
                                        case 't':
                                            offsets = pure.components.crop.module.render.size.correction(instance.id, 0, instance.action.y - event._clientY, false);
                                            if (offsets.w !== 0 || offsets.h !== 0) {
                                                available = pure.components.crop.module.render.position.offset  (instance.id, -offsets._w, -offsets._h, false);
                                                pure.components.crop.module.render.size.                offset  (instance.id, -available._x, -available._y, false, false);
                                                pure.components.crop.module.render.size.                set     (instance.id, false);
                                                pure.components.crop.module.render.position.            set     (instance.id, false);
                                            }
                                            break;
                                        case 'r':
                                            offsets = pure.components.crop.module.render.size.correction(instance.id, -(instance.action.x - event._clientX), 0, false);
                                            if (offsets.w !== 0 || offsets.h !== 0) {
                                                pure.components.crop.module.render.size.                offset  (instance.id, offsets._w, 0, false, false);
                                                pure.components.crop.module.render.size.                set     (instance.id, false);
                                            }
                                            break;
                                        case 'b':
                                            offsets = pure.components.crop.module.render.size.correction(instance.id, 0, -(instance.action.y - event._clientY), false);
                                            if (offsets.w !== 0 || offsets.h !== 0) {
                                                pure.components.crop.module.render.size.                offset  (instance.id, offsets._w, offsets._h, false, false);
                                                pure.components.crop.module.render.size.                set     (instance.id, false);
                                            }
                                            break;
                                        case 'l':
                                            offsets = pure.components.crop.module.render.size.correction(instance.id, instance.action.x - event._clientX, 0, false);
                                            if (offsets.w !== 0 || offsets.h !== 0) {
                                                available = pure.components.crop.module.render.position.offset  (instance.id, -offsets._w, 0, false);
                                                pure.components.crop.module.render.size.                offset  (instance.id, -available._x, 0, false, false);
                                                pure.components.crop.module.render.size.                set     (instance.id, false);
                                                pure.components.crop.module.render.position.            set     (instance.id, false);
                                            }
                                            break;
                                    }
                                    instance.action.x = event._clientX;
                                    instance.action.y = event._clientY;
                                    event.preventDefault();
                                    return false;
                                }
                            }
                        }
                    },
                    mouseup     : function(event, instance){
                        var instance = (typeof instance  === 'object'? instance : (typeof instance === 'string' ? pure.components.crop.module.data.get(instance) : null));
                        if (instance !== null) {
                            if (instance.action.current === 'resize_borders'){
                                instance.action.current     = null;
                                instance.action.direction   = null;
                                instance.action.x           = null;
                                instance.action.y           = null;
                                event.preventDefault();
                                return false;
                            }
                        }
                    }
                }
            }
        }
    };
}());