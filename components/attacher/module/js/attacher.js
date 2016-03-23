(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components               !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.attacher      !== "object") { window.pure.components.attacher     = {}; }
    "use strict";
    window.pure.components.attacher.module = {
        attach : {
            history     : [],
            trusted     : [
                'http://maps.googleapis.com',
                'http://google.com',
                'https://maps.googleapis.com',
                'https://google.com'
            ],
            checkURL    : function(url){
                var trusted = pure.components.attacher.module.attach.trusted,
                    result  = false;
                if (typeof pure.globalsettings.domain === 'string'){
                    if (trusted.indexOf(pure.globalsettings.domain) === -1){
                        trusted.push(pure.globalsettings.domain);
                    }
                }
                for(var index = trusted.length - 1; index >= 0; index -= 1){
                    result = (result === false ? (url.indexOf(trusted[index]) === 0 ? true : false) : true);
                }
                return result;
            },
            connectors  : {
                JS          : function(url){
                    function isScriptExist(url){
                        var scripts = pure.nodes.select.all('script[src*="' + url + '"]');
                        if (scripts.length === 0){
                            scripts = pure.nodes.select.all('script[src*="' + url.toLowerCase() + '"]')
                        }
                        return (scripts.length > 0 ? true : false);
                    };
                    var scriptNode = null;
                    if (isScriptExist(url) === false && pure.components.attacher.module.attach.checkURL(url) === true){
                        scriptNode      = document.createElement("SCRIPT");
                        scriptNode.type = "application/javascript";
                        scriptNode.src  = url;
                        document.head.appendChild(scriptNode);
                    }
                },
                CSS         : function(url){
                    function isLinkExist(url){
                        var links = pure.nodes.select.all('link[href*="' + url + '"]');
                        return (links.length > 0 ? true : false);
                    };
                    var linkNode = null;
                    if (isLinkExist(url) === false && pure.components.attacher.module.attach.checkURL(url) === true) {
                        linkNode        = document.createElement("LINK");
                        linkNode.type   = "text/css";
                        linkNode.href   = url;
                        linkNode.rel    = "stylesheet";
                        document.head.appendChild(linkNode);
                    }
                },
                CSSValue    : function(value){
                    var loaderCSS = document.createElement("style");
                    try {
                        loaderCSS.type = "text/css";
                        if (loaderCSS.styleSheet) {
                            loaderCSS.styleSheet.cssText = value;
                        } else {
                            loaderCSS.appendChild(document.createTextNode(value));
                        }
                        document.head.appendChild(loaderCSS);
                        return loaderCSS;
                    } catch (e) {
                        if (typeof window.console !== 'undefined'){
                            if (typeof window.console.log === 'function'){
                                window.console.log("Attacher INIT method catch error: \r\n" + pure.components.attacher.module.helpers.getMessage(e));
                            }
                        }
                        return null;
                    }
                },
                INIT        : function(handle){
                    try{
                        if (pure.system.loaded === true){
                            (pure.system.getInstanceByPath(handle) !== null ? pure.system.getInstanceByPath(handle).init() : null);
                        }
                    }catch (e){
                        if (typeof window.console !== 'undefined'){
                            if (typeof window.console.log === 'function'){
                                window.console.log("Attacher INIT method catch error: \r\n" + pure.components.attacher.module.helpers.getMessage(e));
                            }
                        }
                        return null;
                    }
                },
                SETTING : function(setting){
                    var setting = setting.split('|');
                    if (setting.length === 2){
                        if (pure.system.getInstanceByPath(setting[0]) === null){
                            pure.system.setInstanceByPath(setting[0], setting[1]);
                        }
                    }
                }
            },
            byURL       : function(url, type){
                if (typeof pure.components.attacher.module.attach.connectors[type] === "function"){
                    if (type === 'JS' || type === 'CSS' || type === 'CSSValue'){
                        if (pure.components.attacher.module.attach.history.indexOf(url) !== -1){
                            return false;
                        }
                    }
                    pure.components.attacher.module.attach.history.push(url);
                    pure.components.attacher.module.attach.connectors[type](url);
                }
            }
        },
        findAndAttach   : function(response){
            function processing(response, type){
                var URLs            = null,
                    history         = [],
                    reg             = new RegExp("<\\!--" + type + "\\:\\[[^>](.*?)\\]-->", "gim"),
                    collections     = response.match(reg);
                if (collections instanceof Array){
                    if (collections.length > 0){
                        for( var index = collections.length - 1; index >= 0; index -= 1){
                            URLs = collections[index].match(/\[(\w.*)\]/i);
                            if (URLs instanceof Array){
                                if (URLs.length === 2){
                                    if (history.indexOf(URLs[1]) === -1){
                                        try{
                                            history.push(URLs[1]);
                                            pure.components.attacher.module.attach.byURL(URLs[1], type);
                                        }catch (e){
                                            if (typeof window.console !== 'undefined'){
                                                if (typeof window.console.log === 'function'){
                                                    window.console.log("Attacher INIT method catch error: \r\n" + pure.components.attacher.module.helpers.getMessage(e));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        return true;
                    }
                }
            };
            var response = (typeof response === "string" ? response : null);
            if (response !== null) {
                processing(response, 'SETTING'  );
                processing(response, 'JS'       );
                processing(response, 'CSSValue' );
                processing(response, 'CSS'      );
                processing(response, 'INIT'     );
            }
        },
        helpers :{
            getMessage : function(e) {
                var message= e.name + ": " + e.message + "\r\n--------------------------------------------";
                for (var property in e) {
                    if (property !== "name" && property !== "message") {
                        message = message + "\r\n  " + property + "=" + e[property];
                    }
                }
                return message;
            }
        },
        afterLoadAttach : function(){
            var container = pure.nodes.select.first('div[id="PureComponentsAttacherAfterLoadCommands"]');
            if (container !== null){
                pure.components.attacher.module.findAndAttach(container.innerHTML);
                container.parentNode.removeChild(container);
            }
        },
        init            : function(){
            pure.components.attacher.module.afterLoadAttach();
        }
    };
    pure.system.start.add(pure.components.attacher.module.init);
}());