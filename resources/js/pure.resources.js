(function () {
    "use strict";
    if (typeof window.pure                  !== "object") { window.pure                 = {}; }
    if (typeof window.pure.compressor       !== "object") { window.pure.compressor      = {}; }
    if (typeof window.pure.compressor.core  !== "object") { window.pure.compressor.core = {}; }
    window.pure.compressor.core = {
        storage: {
            cleared : false,
            means   : {
                localStorage: {
                    config  : {
                        PREFIX : 'PURE.THEME.FIELD'
                    },
                    getValue: function (keyValue) {
                        var value = null;
                        try {
                            value = window.localStorage.getItem(pure.compressor.core.storage.means.localStorage.config.PREFIX + keyValue);
                            if (typeof value !== "string") {
                                value = null;
                            }
                            return value;
                        } catch (e) {
                            return null;
                        }
                    },
                    setValue: function (keyValue, value) {
                        var result_value    = null,
                            PREFIX          = pure.compressor.core.storage.means.localStorage.config.PREFIX;
                        try {
                            window.localStorage.removeItem(PREFIX + keyValue);
                            window.localStorage.setItem(PREFIX + keyValue, value);
                            result_value = window.localStorage.getItem(PREFIX + keyValue);
                            if (result_value !== value){
                                //Here should divide tool
                            }
                            return true;
                        } catch (e) {
                            return false;
                        }
                    }
                }
            },
            controls: {
                getValue: null,
                setValue: null,
                init    : function () {
                    pure.compressor.core.storage.controls.getValue = pure.compressor.core.storage.means.localStorage.getValue;
                    pure.compressor.core.storage.controls.setValue = pure.compressor.core.storage.means.localStorage.setValue;
                }
            },
            reset : function(){
                if (pure.compressor.core.storage.cleared === false){
                    pure.compressor.core.storage.cleared = true;
                    try {
                        for(var key in window.localStorage){
                            if (key.indexOf(pure.compressor.core.storage.means.localStorage.config.PREFIX) === 1){
                                window.localStorage.removeItem(key);
                            }
                        }
                        window.console.log('Cache was cleared.');
                        return true;
                    } catch (e) {
                        return false;
                    }
                }
            }
        },
        attach : {
            css    : function(value, crc32){
                var loaderCSS = document.createElement("style");
                try {
                    loaderCSS.type = "text/css";
                    if (loaderCSS.styleSheet) {
                        loaderCSS.styleSheet.cssText = value;
                    } else {
                        loaderCSS.appendChild(document.createTextNode(value));
                    }
                    document.head.appendChild(loaderCSS);
                    pure.compressor.core.history.remove(crc32);
                    return loaderCSS;
                } catch (e) {
                    if (typeof window.console !== 'undefined'){
                        if (typeof window.console.log === 'function'){
                            window.console.log("Compressor ATTACH method catch error: \r\n" + pure.compressor.core.helpers.getMessage(e));
                        }
                    }
                    return null;
                }
            },
            js      : function (value, crc32) {
                var resourceJS  = null,
                    lines       = null;
                try{
                    resourceJS      = document.createElement("SCRIPT");
                    resourceJS.type = "text/javascript";// application/javascript
                    lines           = value.split('\n');
                    if (lines instanceof Array){
                        for(var index = 0, max_index = lines.length; index < max_index; index += 1){
                            resourceJS.appendChild(document.createTextNode(lines[index]));
                        }
                    }
                    document.body.insertBefore(resourceJS, document.body.childNodes[0]);
                    pure.compressor.core.history.remove(crc32);
                }catch (e){
                    if (typeof window.console !== 'undefined'){
                        if (typeof window.console.log === 'function'){
                            window.console.log("Compressor ATTACH method catch error: \r\n" + pure.compressor.core.helpers.getMessage(e));
                        }
                    }
                }
            },
            parse   : function(value, type, crc32){
                var content = null;
                if (typeof pure.compressor.core.attach[type] === 'function'){
                    content = pure.convertor.BASE64.decode(value);
                    content = pure.convertor.UTF8.decode(content);
                    pure.compressor.core.attach[type](content, crc32);
                }
            }
        },
        link : {
            css     : function(url, crc32){
                function isLinkExist(url){
                    var links = pure.nodes.select.all('link[href*="' + url + '"]');
                    return (links.length > 0 ? true : false);
                };
                var linkNode = null;
                if (isLinkExist(url) === false) {
                    linkNode        = document.createElement("LINK");
                    linkNode.type   = "text/css";
                    linkNode.href   = url;
                    linkNode.rel    = "stylesheet";
                    document.head.appendChild(linkNode);
                    pure.compressor.core.history.remove(crc32);
                }
            },
            js      : function(url, crc32){
                function isScriptExist(url){
                    var scripts = pure.nodes.select.all('script[src*="' + url + '"]');
                    return (scripts.length > 0 ? true : false);
                };
                var scriptNode = null;
                if (isScriptExist(url) === false){
                    scriptNode      = document.createElement("SCRIPT");
                    scriptNode.type = "application/javascript";
                    scriptNode.src  = url;
                    pure.events.add(
                        scriptNode,
                        'load',
                        function(){
                            pure.compressor.core.history.remove(crc32);
                        }
                    );
                    document.head.appendChild(scriptNode);
                    return true;
                }
            },
            parse   : function(url, type, crc32){
                var content = null;
                if (typeof pure.compressor.core.link[type] === 'function'){
                    content = pure.convertor.BASE64.decode(url);
                    content = pure.convertor.UTF8.decode(content);
                    pure.compressor.core.link[type](content, crc32);
                }
            }
        },
        history : {
            items   : [],
            add     : function(crc32){
                var items = pure.compressor.core.history.items;
                items.push(crc32);
            },
            remove  : function(crc32){
                var items = pure.compressor.core.history.items,
                    index = items.indexOf(crc32);
                if (index !== -1){
                    items.splice(index, 1);
                    pure.compressor.core.history.check();
                }
            },
            check : function(){
                if (pure.compressor.core.history.items.length === 0){
                    if (pure.system.getInstanceByPath('pure.components.attacher.module.init') !== null){
                        pure.components.attacher.module.init();
                    }
                    pure.system.start.              unblock     ();
                    pure.system.start.              run         ();
                    pure.compressor.core.progress.  removeCap   ();
                    pure.appevents.Actions.call(
                        'pure.compressor',
                        'finish',
                        null,
                        null
                    );
                }
            },
            fill : function(files){
                pure.system.start.block();
                for(var type in files){
                    for(var index = 0, max_index = files[type].length; index < max_index; index += 1){
                        pure.compressor.core.history.add(files[type][index].crc32);
                    }
                }
            }
        },
        cache   : {
            data    : {},
            add     : function(type, value, crc32){
                var data = pure.compressor.core.cache.data;
                if (typeof data[type] === 'undefined'){
                    data[type] = [];
                }
                data[type].push({
                    cache : value,
                    crc32 : crc32
                });
            },
            proceed : function(){
                var data = pure.compressor.core.cache.data;
                if (Object.keys(data).length > 0){
                    for(var type in data) {
                        for (var index = 0, max_index = data[type].length; index < max_index; index += 1) {
                            pure.compressor.core.attach.parse(
                                data[type][index].cache,
                                type,
                                data[type][index].crc32
                            );
                        }
                    }
                }
            }
        },
        request : {
            data    : [],
            add     : function(value){
                pure.compressor.core.request.data.push(value);
            },
            send    : function(){
                var request     = pure.system.getInstanceByPath('pure.compressor.settings.request'      ),
                    destination = pure.system.getInstanceByPath('pure.compressor.settings.destination'  ),
                    data        = pure.compressor.core.request.data;
                if (request !== null && destination !== null){
                    if (data.length > 0){
                        pure.tools.request.sendWithFields({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.compressor.core.request.receive(id_request, response);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.compressor.core.request.error(event, id_request);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.compressor.core.request.error(event, id_request);
                                }
                            },
                            {
                                crc32  : data.join(',')
                            }
                        );
                    }else{
                        pure.compressor.core.cache.proceed();
                    }
                }
            },
            receive : function(id_request, response){
                var data = null;
                if (response !== 'fail'){
                    try{
                        data = JSON.parse(response);
                        for(var type in data) {
                            for (var index = 0, max_index = data[type].length; index < max_index; index += 1) {
                                pure.compressor.core.cache.add(type, data[type][index].cache, data[type][index].crc32);
                                pure.compressor.core.storage.controls.setValue(data[type][index].crc32, data[type][index].cache);
                            }
                        }
                        pure.compressor.core.cache.proceed();
                    }catch (e){}
                }
            },
            error   : function(event, id_request){

            }
        },
        init    :function(){
            var resource = null;
            pure.compressor.core.storage.controls.init();
            pure.compressor.core.progress.init();
            pure.compressor.core.progress.showBody();
            if (typeof pure.compressor.resources !== 'undefined'){
                if (typeof pure.compressor.resources.files !== 'undefined' &&
                    typeof pure.compressor.resources.debug !== 'undefined'){
                    pure.compressor.core.history.fill(pure.compressor.resources.files);
                    for(var type in pure.compressor.resources.files){
                        for(var index = 0, max_index = pure.compressor.resources.files[type].length; index < max_index; index += 1){
                            if (pure.compressor.resources.debug[type] === true){
                                //DEBUG MODE: all resources will be attached as source code
                                pure.compressor.core.link.parse(
                                    (pure.compressor.resources.minif[type] === true ?
                                        pure.compressor.resources.files[type][index].url :
                                        pure.compressor.resources.files[type][index].source_url),
                                    type,
                                    pure.compressor.resources.files[type][index].crc32
                                );
                            }else{
                                //RELEASE MODE: try get all resources from localStorage
                                resource = pure.compressor.core.storage.controls.getValue(pure.compressor.resources.files[type][index].crc32);
                                if (resource !== null && resource !== ''){
                                    pure.compressor.core.cache.add(
                                        type,
                                        resource,
                                        pure.compressor.resources.files[type][index].crc32
                                    );
                                }else{
                                    pure.compressor.core.request.add(pure.compressor.resources.files[type][index].crc32);
                                    //pure.compressor.core.storage.reset();
                                }
                                resource = true;
                            }
                        }
                    }
                    if (resource !== null){
                        pure.compressor.core.request.send();
                    }
                }
            }
        },
        progress    : {
            init : function(){
                var progress = pure.system.getInstanceByPath('pure.compressor.progress');
                if (progress !== null){
                    for(var key in progress){
                        progress[key]  = pure.convertor.BASE64.decode(progress[key]);
                        progress[key]  = pure.convertor.UTF8.decode(progress[key]);
                    }
                    pure.compressor.core.attach.js(progress.js, -1);
                    pure.compressor.core.attach.css(progress.css, -1);
                }
            },
            removeCap : function(){
                var cap = pure.nodes.select.first('*[id="Pure.Compressor.Cap"]');
                if (cap !== null){
                    cap.parentNode.removeChild(cap);
                }
            },
            showBody : function(){
                document.body.style.opacity = 1;
            }
        },
        helpers     :{
            getMessage : function(e) {
                var message= e.name + ": " + e.message + "\r\n--------------------------------------------";
                for (var property in e) {
                    if (property !== "name" && property !== "message") {
                        message = message + "\r\n  " + property + "=" + e[property];
                    }
                }
                return message;
            }
        }
    };
    pure.system.start.add(pure.compressor.core.init);
}());