(function () {
    if (typeof window.pure                                      !== "object") { window.pure                                         = {}; }
    if (typeof window.pure.templates                            !== "object") { window.pure.templates                               = {}; }
    if (typeof window.pure.templates.authorization              !== "object") { window.pure.templates.authorization                 = {}; }
    if (typeof window.pure.templates.authorization.registration !== "object") { window.pure.templates.authorization.registration    = {}; }
    "use strict";
    window.pure.templates.authorization.registration.A = {
        registration    : {
            progress    : null,
            isPossible  : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.templates.authorization.login.configuration.requestURL'           ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.templates.authorization.registration.configuration.requests.try'  ) === null ? false : true));
                return result;
            },
            validate    : function(nodes){
                var message = pure.templates.authorization.registration.A.dialogs.info;
                if (nodes.fields.password[0].value !== nodes.fields.password[1].value){
                    message('Error', 'Please, check correction of password.');
                    return false;
                }
                if (nodes.fields.login.value.length < 2){
                    message('Error', 'Please, check your login. It is too short.');
                    return false;
                }
                if (nodes.fields.password[0].value.length < 4){
                    message('Error', 'Please, check your password. It is too short.');
                    return false;
                }
                if (nodes.fields.email.value.length < 4){
                    message('Error', 'Please, check your email. It is too short.');
                    return false;
                }
                return true;
            },
            send        : function(nodes, closeHandle){
                var request = null;
                if (pure.templates.authorization.registration.A.registration.isPossible() !== false){
                    if (pure.templates.authorization.registration.A.registration.validate(nodes) !== false){
                        pure.templates.authorization.registration.A.registration.progress = pure.templates.progressbar.A.show(nodes.container, 'background:rgba(255,255,255,0.8);z-index:10000;');
                        request = pure.templates.authorization.registration.configuration.requests.try;
                        request = request.replace(/\[login\]/gi,    nodes.fields.login.value    );
                        request = request.replace(/\[password\]/gi, nodes.fields.password[0].value );
                        request = request.replace(/\[email\]/gi,    nodes.fields.email.value    );
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.templates.authorization.login.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.templates.authorization.registration.A.registration.onRecieve(id_request, response, closeHandle);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.templates.authorization.registration.A.registration.onError(event, id_request);
                            },
                            ontimeout   : function (id_request) {
                                pure.templates.authorization.registration.A.registration.onError(id_request, id_request);
                            }
                        });
                    }
                }
            },
            onRecieve   : function(id_request, response, closeHandle){
                var message = pure.templates.authorization.registration.A.dialogs.info;
                switch (response){
                    case 'success':
                        message('Success', 'So, everything is OK. But you should make a last step. Check your mailbox and confirm your email. If you do not get any mails from us, you can require mail again by link under email field.' );
                        closeHandle();
                        break;
                    case 'fail':
                        message('Fail', 'We cannot register you. Please, contact with administrator.' );
                        break;
                    case 'email exists':
                        message('Fail', 'Sorry, but this email is in the system. Try other one or recover your password.' );
                        break;
                    case 'login is busy':
                        message('Fail', 'Sorry, but this login is busy. Try other one.' );
                        break;
                    case 'bad email':
                        message('Fail', 'Sorry, but you have written your mail with some error. Check it please.' );
                        break;
                    default :
                        message('Fail', 'We cannot register you. Please, contact with administrator.' );
                        break;
                }
                pure.templates.progressbar.A.hide(pure.templates.authorization.registration.A.registration.progress);
            },
            onError     : function(event, id_request){
                var message = pure.templates.authorization.registration.A.dialogs.info;
                message('Fail', 'We cannot register you. Some problems with connection. Please, try again a bit later.' );
                pure.templates.progressbar.A.hide(pure.templates.authorization.registration.A.registration.progress);
            }
        },
        resend          : {
            progress    : null,
            isPossible  : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.templates.authorization.login.configuration.requestURL'               ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.templates.authorization.registration.configuration.requests.resend'   ) === null ? false : true));
                return result;
            },
            validate    : function(nodes){
                var message = pure.templates.authorization.registration.A.dialogs.info;
                if (nodes.fields.email.value.length < 4){
                    message('Error', 'Please, check your email. It is too short.');
                    return false;
                }
                return true;
            },
            send        : function(nodes){
                var request = null;
                if (pure.templates.authorization.registration.A.resend.isPossible() !== false){
                    if (pure.templates.authorization.registration.A.resend.validate(nodes) !== false){
                        pure.templates.authorization.registration.A.resend.progress = pure.templates.progressbar.A.show(nodes.container, 'background:rgba(255,255,255,0.8);z-index:10000;');
                        request = pure.templates.authorization.registration.configuration.requests.resend;
                        request = request.replace(/\[email\]/gi,    nodes.fields.email.value    );
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.templates.authorization.login.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.templates.authorization.registration.A.resend.onRecieve(id_request, response, nodes);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.templates.authorization.registration.A.resend.onError(event, id_request, nodes);
                            },
                            ontimeout   : function (id_request) {
                                pure.templates.authorization.registration.A.resend.onError(id_request, id_request, nodes);
                            }
                        });
                    }
                }
            },
            onRecieve   : function(id_request, response, nodes){
                var message = pure.templates.authorization.registration.A.dialogs.info;
                switch (response){
                    case 'success':
                        message('Success', 'Check your mailbox and confirm your email.' );
                        break;
                    case 'fail':
                        message('Fail', 'We cannot send mail to you. Please, contact with administrator.' );
                        break;
                    default :
                        message('Fail', 'We cannot send mail to you. Please, contact with administrator.' );
                        break;
                }
                pure.templates.progressbar.A.hide(pure.templates.authorization.registration.A.resend.progress);
            },
            onError     : function(event, id_request, nodes){
                var message = pure.templates.authorization.registration.A.dialogs.info;
                message('Fail', 'We cannot send mail to you. Some problems with connection. Please, try again a bit later.' );
                pure.templates.progressbar.A.hide(pure.templates.authorization.registration.A.resend.progress);
            }
        },
        buttons         : {
            init : function(){
                var buttons = pure.nodes.select.all('*[data-engine-registration-form="Button.Show"]:not([data-engine-element-inited])');
                if (buttons !== null){
                    for(var index = buttons.length - 1; index >= 0; index -= 1){
                        (function(button){
                            pure.events.add(
                                button,
                                'click',
                                pure.templates.authorization.registration.A.actions.show
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
                    'authorization.registration',
                    'update.buttons',
                    pure.templates.authorization.registration.A.buttons.init,
                    'pure.templates.authorization.registration.A.buttons.init'
                );
            }
        },
        template    : {
            data    : null,
            init    : function(){
                var template = pure.nodes.select.first('*[data-engine-registration-form="Container"]');
                if (template !== null){
                    pure.templates.authorization.registration.A.template.data = {
                        innerHTML   : template.innerHTML,
                        attributes  : pure.nodes.attributes.get(template, ['style', 'data-engine-registration-form']),
                        nodeName    : template.nodeName
                    };
                    template.parentNode.removeChild(template);
                }
            },
            show    : function(){
                var data        = pure.templates.authorization.registration.A.template.data,
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
                            login       : pure.nodes.select.first('input[data-engine-registration-form="Field.Login"]'),
                            password    : pure.nodes.select.all('input[data-engine-registration-form="Field.Password"]'),
                            email       : pure.nodes.select.first('input[data-engine-registration-form="Field.Email"]')
                        },
                        buttons : {
                            try         : pure.nodes.select.first('*[data-engine-registration-form="Button.Try"]'),
                            resend      : pure.nodes.select.first('*[data-engine-registration-form="Button.Resend"]'),
                            cancel      : pure.nodes.select.first('*[data-engine-registration-form="Button.Cancel"]')
                        },
                        container : pure.nodes.select.first('*[data-engine-registration-form="Container.Modal"]')
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
                            nodes.buttons.try,
                            'click',
                            function(){
                                pure.templates.authorization.registration.A.registration.send(nodes, closeHandle);
                            }
                        );
                        pure.events.add(
                            nodes.buttons.resend,
                            'click',
                            function(){
                                pure.templates.authorization.registration.A.resend.send(nodes, node);
                            }
                        );
                        pure.appevents.Actions.call('html', 'sizer.init', null, null);
                    }else{
                        if (nodes.container !== null && nodes.buttons.cancel !== null){
                            closeHandle = function(){
                                node.parentNode.removeChild(node);
                            };
                            pure.events.add(
                                nodes.buttons.cancel,
                                'click',
                                closeHandle
                            );
                            pure.appevents.Actions.call('html', 'sizer.init', null, null);
                        }else{
                            node.parentNode.removeChild(node);
                        }
                    }
                }
                return false;
            }
        },
        actions         : {
            show : function(){
                pure.templates.authorization.registration.A.template.show();
            }
        },
        dialogs         : {
            info: function (title, message) {
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
            }
        },
        init            : function(){
            pure.templates.authorization.registration.A.template.   init();
            pure.templates.authorization.registration.A.buttons.    init();
            pure.templates.authorization.registration.A.events.     init();
        }
    };
    pure.system.start.add(pure.templates.authorization.registration.A.init);
}());