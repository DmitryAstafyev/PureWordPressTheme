(function () {
    if (typeof window.pure                                  !== "object") { window.pure                                 = {}; }
    if (typeof window.pure.templates                        !== "object") { window.pure.templates                       = {}; }
    if (typeof window.pure.templates.authorization          !== "object") { window.pure.templates.authorization         = {}; }
    if (typeof window.pure.templates.authorization.login    !== "object") { window.pure.templates.authorization.login   = {}; }
    "use strict";
    window.pure.templates.authorization.login.A = {
        actual  : {
            init        : function(){
                pure.templates.authorization.login.A.actual.send();
            },
            isPossible  : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.templates.authorization.login.configuration.requestURL'       ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.templates.authorization.login.configuration.requests.actual'  ) === null ? false : true));
                return result;
            },
            send        : function(){
                var request = null;
                if (pure.templates.authorization.login.A.actual.isPossible() !== false){
                    request = pure.templates.authorization.login.configuration.requests.actual;
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : pure.templates.authorization.login.configuration.requestURL,
                        request     : request,
                        onrecieve   : function (id_request, response) {
                            pure.templates.authorization.login.A.actual.onRecieve(id_request, response);
                        },
                        onreaction  : null,
                        onerror     : function (id_request) {
                            //do nothing
                        },
                        ontimeout   : function (id_request) {
                            //do nothing
                        }
                    });
                }
            },
            onRecieve   : function(id_request, response){
                if (response === 'need login'){
                    window.location.reload();
                }
            }
        },
        login   : {
            progress    : null,
            isPossible  : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.templates.authorization.login.configuration.requestURL'       ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.templates.authorization.login.configuration.requests.login'   ) === null ? false : true));
                return result;
            },
            keydown     : function(event, nodes){
                if (event.keyCode == 13){
                    pure.templates.authorization.login.A.login.send(nodes);
                }
            },
            send        : function(nodes){
                var request = null;
                if (pure.templates.authorization.login.A.login.isPossible() !== false){
                    if (nodes.fields.login.value.length > 2 && nodes.fields.password.value.length > 4){
                        pure.templates.authorization.login.A.login.progress = pure.templates.progressbar.A.show(nodes.container, 'background:rgba(255,255,255,0.8);z-index:10000;');
                        request = pure.templates.authorization.login.configuration.requests.login;
                        request = request.replace(/\[login\]/gi,    nodes.fields.login.value      );
                        request = request.replace(/\[password\]/gi, nodes.fields.password.value   );
                        request = request.replace(/\[remember\]/gi, (nodes.fields.remember.checked === false ? 'off' : 'on'));
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.templates.authorization.login.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.templates.authorization.login.A.login.onRecieve(id_request, response, nodes);
                            },
                            onreaction  : null,
                            onerror     : function (id_request) {
                                pure.templates.authorization.login.A.login.onError(event, id_request, nodes);
                            },
                            ontimeout   : function (id_request) {
                                pure.templates.authorization.login.A.login.onError(id_request, id_request, nodes);
                            }
                        });
                    }
                }
            },
            onRecieve   : function(id_request, response, nodes){
                if (response === 'success'){
                    window.location.reload();
                }else{
                    pure.templates.progressbar.A.hide(pure.templates.authorization.login.A.login.progress);
                    nodes.fields.password.value = '';
                    nodes.fields.password.focus();
                }
            },
            onError     : function(event, id_request, nodes){
                pure.templates.progressbar.A.hide(pure.templates.authorization.login.A.login.progress);
                nodes.fields.password.value = '';
                nodes.fields.password.focus();
            }
        },
        buttons     : {
            init : function(){
                var buttons = pure.nodes.select.all('*[data-engine-login-form="Button.Show"]:not([data-engine-element-inited])');
                if (buttons !== null){
                    for(var index = buttons.length - 1; index >= 0; index -= 1){
                        (function(button){
                            pure.events.add(
                                button,
                                'click',
                                pure.templates.authorization.login.A.actions.show
                            );
                            button.setAttribute('data-engine-element-inited', 'true');
                        }(buttons[index]));
                    }
                }
            }
        },
        events      : {
            init : function(){
                pure.appevents.Actions.listen(
                    'authorization.login',
                    'update.buttons',
                    pure.templates.authorization.login.A.buttons.init,
                    'pure.templates.authorization.login.A.buttons.init'
                );
            }
        },
        template    : {
            data    : null,
            init    : function(){
                var template = pure.nodes.select.first('*[data-engine-login-form="Container"]');
                if (template !== null){
                    pure.templates.authorization.login.A.template.data = {
                        innerHTML   : template.innerHTML,
                        attributes  : pure.nodes.attributes.get(template, ['style', 'data-engine-login-form']),
                        nodeName    : template.nodeName
                    };
                    template.parentNode.removeChild(template);
                }
            },
            show    : function(){
                var data    = pure.templates.authorization.login.A.template.data,
                    node    = null,
                    nodes   = null;
                if (data !== null){
                    node            = document.createElement(data.nodeName);
                    node.innerHTML  = data.innerHTML;
                    pure.nodes.attributes.set(node, data.attributes);
                    document.body.appendChild(node);
                    nodes = {
                        fields  : {
                            login       : pure.nodes.select.first('input[data-engine-login-form="Field.Login"]'),
                            remember    : pure.nodes.select.first('input[data-engine-login-form="Field.Remember"]'),
                            password    : pure.nodes.select.first('input[data-engine-login-form="Field.Password"]')
                        },
                        buttons : {
                            login       : pure.nodes.select.first('*[data-engine-login-form="Button.Login"]'),
                            cancel      : pure.nodes.select.first('*[data-engine-login-form="Button.Cancel"]')
                        },
                        container : pure.nodes.select.first('*[data-engine-login-form="Container.Modal"]')
                    };
                    if (pure.tools.objects.isValueIn(nodes, null, true) === false){
                        pure.events.add(
                            nodes.buttons.cancel,
                            'click',
                            function(){
                                node.parentNode.removeChild(node);
                            }
                        );
                        pure.events.add(
                            nodes.buttons.login,
                            'click',
                            function(){
                                pure.templates.authorization.login.A.login.send(nodes);
                            }
                        );
                        pure.events.add(
                            nodes.fields.password,
                            'keydown',
                            function(event){
                                pure.templates.authorization.login.A.login.keydown(event, nodes);
                            }
                        );
                        pure.appevents.Actions.call('html', 'sizer.init', null, null);
                    }else{
                        node.parentNode.removeChild(node);
                    }
                }
                return false;
            }
        },
        actions : {
            show : function(){
                pure.templates.authorization.login.A.template.show();
            }

        },
        init : function(){
            pure.templates.authorization.login.A.template.  init();
            pure.templates.authorization.login.A.buttons.   init();
            pure.templates.authorization.login.A.actual.    init();
            pure.templates.authorization.login.A.events.    init();
        }
    };
    pure.system.start.add(pure.templates.authorization.login.A.init);
}());