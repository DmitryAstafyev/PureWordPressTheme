(function () {
    if (typeof window.pure                      !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components           !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.messenger !== "object") { window.pure.components.messenger    = {}; }
    "use strict";
    window.pure.components.messenger.module = {
        loader  : {
            isPossible      : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.template'      ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.user_id'       ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requestURL'     ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.body' ) === null ? false : true));
                return result;
            },
            getMessenger    : function(){
                if (pure.components.messenger.module.loader.isPossible() !== false){
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : pure.components.messenger.configuration.requestURL,
                        request     : pure.components.messenger.configuration.requests.body,
                        onrecieve   : function (id_request, response) {
                            pure.components.messenger.module.loader.events.onRecieve(id_request, response);
                        },
                        onreaction  : null,
                        onerror     : function (id_request) {
                            pure.components.messenger.module.loader.events.onError(event, id_request);
                        },
                        ontimeout   : function (id_request) {
                            pure.components.messenger.module.loader.events.onError(event, id_request);
                        }
                    });
                }
            },
            events          : {
                onRecieve   : function(id_request, response){
                    if (response !== 'no access'){
                        pure.components.messenger.module.loader.actions.attachBody(response);
                    }
                },
                onError     : function(event, id_request){
                    //Repeat attempt
                    setTimeout(pure.components.messenger.module.init, 5000);
                }
            },
            actions         : {
                attachBody      :function(innerHTML){
                    var container = document.createElement('DIV');
                    try{
                        container.innerHTML = innerHTML;
                        for(var index = 0, max_index = container.childNodes.length; index < max_index; index += 1){
                            document.body.appendChild(container.childNodes[0]);
                        }
                        container = null;
                        pure.components.attacher.module.                findAndAttach(innerHTML);
                        pure.components.messenger.module.nodes.basic.   find();
                        pure.components.messenger.module.state.         init();
                        pure.components.messenger.module.loader.actions.startModules();
                    }catch (e){
                        return false;
                    }
                },
                startModules    : function(){
                    function isPossible(references){
                        var result = true;
                        for (var index = references.length - 1; index >= 0; index -= 1){
                            result = (result === false ? false : (pure.system.getInstanceByPath(references[index]) === null ? false : true));
                        }
                        return result;
                    }
                    function start(references, path){
                        if (isPossible(references) === true){
                            pure.system.getInstanceByPath(path).init();
                        }else{
                            setTimeout(
                                function(){
                                    start(references, path);
                                },
                                100
                            );
                        }
                    };
                    start(['pure.components.messenger.mails.init'           ], 'pure.components.messenger.mails'        );
                    start(['pure.components.messenger.chat.init'            ], 'pure.components.messenger.chat'         );
                    start(['pure.components.messenger.notifications.init'   ], 'pure.components.messenger.notifications');
                }
            }
        },
        init    : function(){
            pure.components.messenger.module.loader.getMessenger();
            pure.components.messenger.module.state. listen      ();
        },
        nodes   : {
            store : {
                basic : {}
            },
            basic : {
                find : function(){
                    var nodes = pure.components.messenger.module.nodes.store.basic;
                    nodes.container = pure.nodes.select.first('*[data-messenger-engine-element="container"]');
                    nodes.buttons   = {
                        open    : pure.nodes.select.all('*[data-messenger-engine-button="open"]'    ),
                        close   : pure.nodes.select.all('*[data-messenger-engine-button="close"]'   )
                    };
                    nodes.switchers = {
                        mails           : pure.nodes.select.first('input[data-messenger-engine-switcher="mails"]'           ),
                        chat            : pure.nodes.select.first('input[data-messenger-engine-switcher="chat"]'            ),
                        notifications   : pure.nodes.select.first('input[data-messenger-engine-switcher="notifications"]'   )
                    };
                }
            },
            getContainer : function(){
                var store = pure.components.messenger.module.nodes.store.basic;
                if (typeof store.container !== 'undefined'){
                    if (store.container !== null){
                        return store.container;
                    }
                }
                return null;
            }
        },
        state       : {
            isOpen  : true,
            init    : function(){
                function attachAll(buttons){
                    if (buttons !== null){
                        Array.prototype.forEach.call(
                            buttons,
                            function(button, _index, source){
                                pure.events.add(
                                    button,
                                    'click',
                                    function(event){
                                        pure.components.messenger.module.state.events.toggle(event, button);
                                    }
                                );
                                button.setAttribute('data-engine-element-inited', 'true');
                            }
                        );
                    }
                };
                var nodes   = pure.components.messenger.module.nodes.store.basic,
                    buttons = {
                        open    : pure.nodes.select.all('*[data-messenger-engine-button="open"]:not([data-engine-element-inited])'    ),
                        close   : pure.nodes.select.all('*[data-messenger-engine-button="close"]:not([data-engine-element-inited])'   )
                    };
                if (nodes.container !== null){
                    attachAll(buttons.open);
                    attachAll(buttons.close);
                    pure.components.messenger.module.state.events.hide();
                }
            },
            listen  : function(){
                pure.appevents.Actions.listen(
                    'messenger',
                    'buttons.update',
                    pure.components.messenger.module.state.init,
                    'pure.components.messenger.module.state.init'
                );
            },
            events  : {
                correctedStyle : null,
                mailTo : {
                    is          : function(switchTo, button){
                        var attributes = {
                            id      : button.getAttribute('data-messenger-engine-recipient-id'),
                            avatar  : button.getAttribute('data-messenger-engine-recipient-avatar'),
                            name    : button.getAttribute('data-messenger-engine-recipient-name')
                        };
                        if (switchTo === 'mails'){
                            if (pure.tools.objects.isValueIn(attributes, null) === false){
                                return attributes;
                            }
                        }
                        return null;
                    },
                    proceed     : function(button){
                        var attributes = pure.components.messenger.module.state.events.mailTo.is('mails', button);
                        if (pure.system.getInstanceByPath('pure.components.messenger.mails.create.mailTo') !== null){
                            pure.components.messenger.mails.mailTo({
                                id      : parseInt(attributes.id, 10),
                                name    : attributes.name,
                                avatar  : attributes.avatar
                            });
                        }
                    }
                },
                toggle : function(event, button){
                    if (pure.components.messenger.module.state.isOpen === false){
                        pure.components.messenger.module.state.events.show(button);
                    }else{
                        pure.components.messenger.module.state.events.hide();
                    }
                },
                show : function(button){
                    function switcher(switchTo){
                        nodes.container.style.display                   = "";
                        pure.components.messenger.module.windows.switchTo(switchTo);
                        pure.components.messenger.module.state.isOpen   = true;
                    };
                    function adoptionCSS(cssText) {
                        try {
                            var loaderCSS = document.createElement("style");
                            loaderCSS.type = "text/css";
                            if (loaderCSS.styleSheet) {
                                loaderCSS.styleSheet.cssText = cssText;
                            } else {
                                loaderCSS.appendChild(document.createTextNode(cssText));
                            }
                            document.head.appendChild(loaderCSS);
                            return loaderCSS;
                        } catch (e) {
                            return null;
                        }
                    }
                    var nodes       = pure.components.messenger.module.nodes.store.basic,
                        switchTo    = button.getAttribute('data-messenger-engine-switchTo'),
                        adminpanel  = pure.nodes.select.first('*[id="wpadminbar"]');
                    if (pure.components.messenger.module.state.events.mailTo.is(switchTo, button) !== null){
                        if (pure.components.messenger.module.progress.isFinish() !== false){
                            switcher(switchTo);
                            pure.components.messenger.module.state.events.mailTo.proceed(button);
                        }
                    }else{
                        switcher(switchTo);
                    }
                    if (adminpanel !== null){
                        adminpanel.style.display = 'none';
                    }
                    pure.components.messenger.module.state.events.correctedStyle = adoptionCSS(
                        'html{ ' +
                            'overflow: hidden!important; ' +
                        '}' +
                        'body{' +
                            'overflow: hidden!important; ' +
                            'height: 100%!important; ' +
                        '}'
                    );
                },
                hide : function(){
                    var nodes       = pure.components.messenger.module.nodes.store.basic,
                        adminpanel  = pure.nodes.select.first('*[id="wpadminbar"]');
                    nodes.container.style.display                   = "none";
                    pure.components.messenger.module.state.isOpen   = false;
                    if (adminpanel !== null){
                        adminpanel.style.display = '';
                    }
                    if (pure.components.messenger.module.state.events.correctedStyle !== null){
                        if (typeof pure.components.messenger.module.state.events.correctedStyle.parentNode !== 'undefined'){
                            if (typeof pure.components.messenger.module.state.events.correctedStyle.parentNode.removeChild === 'function'){
                                pure.components.messenger.module.state.events.correctedStyle.parentNode.removeChild(
                                    pure.components.messenger.module.state.events.correctedStyle
                                );
                            }
                        }
                        pure.components.messenger.module.state.events.correctedStyle = null;
                    }
                }
            }
        },
        windows     : {
            switchTo: function(target){
                var nodes = pure.components.messenger.module.nodes.store.basic;
                switch (target){
                    case 'mails':
                        nodes.switchers.mails.          checked = true;
                        break;
                    case 'chat':
                        nodes.switchers.chat.           checked = true;
                        break;
                    case 'notifications':
                        nodes.switchers.notifications.  checked = true;
                        break;
                    default :
                        nodes.switchers.mails.          checked = true;
                        break;
                }
            }
        },
        progress    : {
            node    : null,
            index   : 0,
            show    : function(){
                var container   = pure.components.messenger.module.nodes.getContainer(),
                    progress    = pure.components.messenger.module.progress;
                if (pure.components.messenger.module.progress.node === null && container !== null){
                    pure.components.messenger.module.progress.node = pure.templates.progressbar.A.show(
                        container,
                        'background:rgba(255,255,255,0.8);z-index:1;',
                        'z-index:2;',
                        'z-index:2;',
                        'loading...'
                    );
                }
                progress.index += 1;
            },
            hide    : function(){
                var progress = pure.components.messenger.module.progress;
                progress.index -= 1;
                if (pure.components.messenger.module.progress.node !== null && progress.index === 0){
                    pure.templates.progressbar.A.hide(pure.components.messenger.module.progress.node);
                }
            },
            isFinish : function(){
                return (pure.components.messenger.module.progress.index === 0 ? true : false);
            }
        },
        helpers : {
            arrayFromProperties : function(target_array, propery_in_array){
                var result = [];
                for (var index = 0, max_index = target_array.length; index < max_index; index += 1){
                    result.push(target_array[index][propery_in_array]);
                }
                return result;
            },
            arrayToInt          : function(source){
                var result = [];
                for (var index = 0, max_index = source.length; index < max_index; index += 1){
                    result.push(parseInt(source[index], 10));
                }
                return result;
            },
            isArraysSame        : function(a, b){
                if (a instanceof Array && b instanceof Array){
                    if (a.length === b.length){
                        for(var index = a.length - 1; index >= 0; index -= 1){
                            if (b.indexOf(a[index]) === -1){
                                return false;
                            }
                        }
                        return true;
                    }
                }
                return false;
            }
        }
    };
    pure.system.start.add(pure.components.messenger.module.init);
}());