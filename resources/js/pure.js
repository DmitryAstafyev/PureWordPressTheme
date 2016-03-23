(function () {
    "use strict";
    var addListener = (window.addEventListener ? window.addEventListener : (window.attachEvent ? window.attachEvent : null));
    if (typeof window.pure          !== "object") { window.pure         = {}; }
    if (typeof window.pure.system   !== "object") { window.pure.system  = {}; }
    window.pure.system.getInstanceByPath = function(path){
        var scheme      = path.split('.'),
            instance    = window;
        for (var index = 0; index < scheme.length; index += 1) {
            if (typeof instance[scheme[index]] !== "undefined") {
                instance = instance[scheme[index]];
            } else {
                return null;
            }
        }
        return instance;
    };
    window.pure.system.setInstanceByPath = function(path, value){
        var scheme      = path.split('.'),
            instance    = window;
        for (var index = 0; index < scheme.length - 1; index += 1) {
            if (typeof instance[scheme[index]] === "undefined") {
                instance[scheme[index]] = {};
            }
            instance = instance[scheme[index]];
        }
        instance[scheme[scheme.length - 1]] = value;
        return instance;
    };
    window.pure.system.runHandle = function (handle_body, handle_arguments, call_point, this_argument) {
        function getMessage(e) {
            var message= e.name + ": " + e.message + "\r\n--------------------------------------------";
            for (var property in e) {
                if (property !== "name" && property !== "message") {
                    message = message + "\r\n  " + property + "=" + e[property];
                }
            }
            return message;
        };
        var handle_body         = (typeof handle_body   === "function"  ? handle_body       : null),
            handle_arguments    = (handle_arguments     !== "undefined" ? handle_arguments  : null),
            call_point          = (typeof call_point    === "string"    ? call_point        : null),
            this_argument       = (typeof this_argument !== "undefined" ? this_argument     : null);
        if (handle_body !== null) {
            try {
                if (handle_arguments === null) {
                    return handle_body.call(this_argument);
                } else {
                    if (handle_arguments instanceof Array) {
                        return handle_body.apply(this_argument, handle_arguments);
                    } else {
                        return handle_body.call(this_argument, handle_arguments);
                    }
                }
            } catch (e) {
                if (typeof window.console !== 'undefined'){
                    if (typeof window.console.log === 'function'){
                        window.console.log("Initializer runFunction method catch error: \r\n" + getMessage(e) + "\r\n Call point: " + call_point);
                    }
                }
                return null;
            }
        }
        return null;
    };
    window.pure.system.safelyPureHandle = function(str_handle, params){
        function wait(str_handle, params){
            var handle = pure.system.getInstanceByPath(str_handle);
            if (handle !== null){
                if (typeof handle === 'function'){
                    handle(params);
                }
            }else{
                if (loop < 100){
                    setTimeout(function () {
                        wait(str_handle, params);
                    }, 50);
                    loop += 1;
                }
            }
        };
        var loop = 0;
        wait(str_handle, params);
    };
    window.pure.system.start    = {
        handles : [],
        add     : function(handle){
            if (pure.system.loaded === false){
                pure.system.start.handles.push({
                    handle : handle,
                    runned : false
                });
            }else{
                pure.system.runHandle(
                    handle,
                    null,
                    'pure.system.start.add',
                    this
                );
            }
        },
        run     : function(){
            if (pure.system.loaded !== false){
                for(var index = pure.system.start.handles.length - 1; index >= 0; index -= 1){
                    if (pure.system.start.handles[index].runned === false){
                        pure.system.start.handles[index].runned = true;
                        pure.system.runHandle(
                            pure.system.start.handles[index].handle,
                            null,
                            'pure.system.start.run',
                            this
                        );
                    }
                    pure.system.start.handles.splice(index, 1);
                }
            }
        },
        block   : function(){
            pure.system.loaded = false;
        },
        unblock : function(){
            pure.system.loaded = true;
        }
    };
    window.pure.system.loaded   = false;
    //Safely handles
    window.pure.safelyHandles = {
        mapsGoogleInit : function(){
            pure.system.safelyPureHandle("pure.components.maps.google.init");
        }
    };
    addListener(
        "load",
        function () {
            var system = pure.system;
            window.pure.system.loaded = true;
            //Addition resources via attacher. Background loading.
            (system.getInstanceByPath("pure.components.attacher.module"     ) !== null ? system.getInstanceByPath("pure.components.attacher.module"     ).init() : null);
            //(!)Attention(!) don't add any thing below
            pure.system.start.run();
        },
        false);
}());