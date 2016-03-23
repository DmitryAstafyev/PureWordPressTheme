(function () {
    if (typeof window.pure                                  !== "object") { window.pure                                 = {}; }
    if (typeof window.pure.templates                        !== "object") { window.pure.templates                       = {}; }
    if (typeof window.pure.templates.authorization          !== "object") { window.pure.templates.authorization         = {}; }
    if (typeof window.pure.templates.authorization.reset    !== "object") { window.pure.templates.authorization.reset   = {}; }
    "use strict";
    window.pure.templates.authorization.reset.A = {
        reset   : {
            progress    : null,
            isPossible  : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.templates.authorization.login.configuration.requestURL'       ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.templates.authorization.reset.configuration.requests.reset'   ) === null ? false : true));
                return result;
            },
            proceed     : function(nodes, closeHandle){
                var question = pure.templates.authorization.reset.A.dialogs.question;
                if (pure.templates.authorization.reset.A.reset.isPossible() !== false){
                    if (nodes.fields.login.value.length > 2 && nodes.fields.email.value.length > 4){
                        question(
                            'Confirm operation',
                            'Do you really want reset your password? In this case your new password will be generated and sent to your mail.',
                            function(){
                                pure.templates.authorization.reset.A.reset.send(nodes, closeHandle);
                            }
                        );
                    }
                }
            },
            send        : function(nodes, closeHandle){
                var request = null;
                pure.templates.authorization.reset.A.reset.progress = pure.templates.progressbar.A.show(nodes.container, 'background:rgba(255,255,255,0.8);z-index:10000;');
                request = pure.templates.authorization.reset.configuration.requests.reset;
                request = request.replace(/\[login\]/gi,    nodes.fields.login.value);
                request = request.replace(/\[email\]/gi,    nodes.fields.email.value);
                pure.tools.request.send({
                    type        : 'POST',
                    url         : pure.templates.authorization.login.configuration.requestURL,
                    request     : request,
                    onrecieve   : function (id_request, response) {
                        pure.templates.authorization.reset.A.reset.onRecieve(id_request, response, closeHandle);
                    },
                    onreaction  : null,
                    onerror     : function (id_request) {
                        pure.templates.authorization.reset.A.reset.onError(event, id_request);
                    },
                    ontimeout   : function (id_request) {
                        pure.templates.authorization.reset.A.reset.onError(id_request, id_request);
                    }
                });
            },
            onRecieve   : function(id_request, response, closeHandle){
                var message = pure.templates.authorization.reset.A.dialogs.info;
                switch (response){
                    case 'success':
                        message('Success', 'Check your mailbox. You will find there your new password.' );
                        closeHandle();
                        break;
                    case 'fail':
                        message('Error', 'Sorry, but we cannot reset your password. Contact with administrator.' );
                        break;
                    case 'no such email':
                        message('Error', 'Sorry, but such email was not found.' );
                        break;
                    case 'no such login':
                        message('Error', 'Sorry, but such login was not found.' );
                        break;
                    case 'bad email':
                        message('Error', 'Sorry, but such email was not found.' );
                        break;
                    case 'error':
                        message('Error', 'Sorry, but there were some error. Contact with administrator.' );
                        break;
                }
                pure.templates.progressbar.A.hide(pure.templates.authorization.reset.A.reset.progress);
            },
            onError     : function(event, id_request, nodes){
                var message = pure.templates.authorization.reset.A.dialogs.info;
                message('Error', 'Sorry, but there are some connection error. Please, try again a bit later.' );
                pure.templates.progressbar.A.hide(pure.templates.authorization.reset.A.reset.progress);
            }
        },
        buttons     : {
            init : function(){
                var buttons = pure.nodes.select.all('*[data-engine-reset-form="Button.Show"]:not([data-engine-element-inited])');
                if (buttons !== null){
                    for(var index = buttons.length - 1; index >= 0; index -= 1){
                        (function(button){
                            pure.events.add(
                                button,
                                'click',
                                pure.templates.authorization.reset.A.actions.show
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
                    'authorization.reset',
                    'update.buttons',
                    pure.templates.authorization.reset.A.buttons.init,
                    'pure.templates.authorization.reset.A.buttons.init'
                );
            }
        },
        template    : {
            data    : null,
            init    : function(){
                var template = pure.nodes.select.first('*[data-engine-reset-form="Container"]');
                if (template !== null){
                    pure.templates.authorization.reset.A.template.data = {
                        innerHTML   : template.innerHTML,
                        attributes  : pure.nodes.attributes.get(template, ['style', 'data-engine-reset-form']),
                        nodeName    : template.nodeName
                    };
                    template.parentNode.removeChild(template);
                }
            },
            show    : function(){
                var data        = pure.templates.authorization.reset.A.template.data,
                    node        = null,
                    nodes       = null,
                    closeHandle = null;
                if (data !== null){
                    node            = document.createElement(data.nodeName);
                    node.innerHTML  = data.innerHTML;
                    pure.nodes.attributes.set(node, data.attributes);
                    document.body.appendChild(node);
                    nodes = {
                        fields  : {
                            login       : pure.nodes.select.first('input[data-engine-reset-form="Field.Login"]'),
                            email       : pure.nodes.select.first('input[data-engine-reset-form="Field.Email"]')
                        },
                        buttons : {
                            reset       : pure.nodes.select.first('*[data-engine-reset-form="Button.Reset"]'),
                            cancel      : pure.nodes.select.first('*[data-engine-reset-form="Button.Cancel"]')
                        },
                        container : pure.nodes.select.first('*[data-engine-reset-form="Container.Modal"]')
                    };
                    if (pure.tools.objects.isValueIn(nodes, null, true) === false){
                        closeHandle = function(){
                            node.parentNode.removeChild(node);
                        };
                        pure.events.add(
                            nodes.buttons.cancel,
                            'click',
                            closeHandle
                        );
                        pure.events.add(
                            nodes.buttons.reset,
                            'click',
                            function(){
                                pure.templates.authorization.reset.A.reset.proceed(nodes, closeHandle);
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
                pure.templates.authorization.reset.A.template.show();
            }

        },
        dialogs         : {
            info    : function (title, message) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-element-type="Pure.RegistrationForm.A.Dialog">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : [
                        {
                            title       : 'OK',
                            handle      : null,
                            closeAfter  : true
                        }
                    ]
                });
            },
            question    : function (title, message, handle) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-element-type="Pure.ResetForm.A.Dialog">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : [
                        {
                            title       : 'CANCEL',
                            handle      : null,
                            closeAfter  : true
                        },
                        {
                            title       : 'RESET',
                            handle      : handle,
                            closeAfter  : true
                        }
                    ]
                });
            },
        },
        init : function(){
            pure.templates.authorization.reset.A.template.  init();
            pure.templates.authorization.reset.A.buttons.   init();
            pure.templates.authorization.reset.A.events.    init();
        }
    };
    pure.system.start.add(pure.templates.authorization.reset.A.init);
}());