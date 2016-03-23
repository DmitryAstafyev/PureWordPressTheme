(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.buddypress               !== "object") { window.pure.buddypress              = {}; }
    if (typeof window.pure.buddypress.creategroup   !== "object") { window.pure.buddypress.creategroup  = {}; }
    "use strict";
    window.pure.buddypress.creategroup.A = {
        init        : function () {
            pure.buddypress.creategroup.A.initialize.init();
        },
        initialize  : {
            init : function(){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.New.Container"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        (function(node){
                            function attachEvents(eventType, id){
                                var callers = pure.nodes.select.all('*[data-engine-creategroup-' + eventType + '-caller="' + id + '"]:not([data-type-element-inited])');
                                if (callers !== null){
                                    for(var index = callers.length - 1; index >= 0; index -= 1){
                                        if (eventType === 'open'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.creategroup.A.render.show(id);
                                            });
                                        }
                                        if (eventType === 'close'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.creategroup.A.render.hide(id);
                                            });
                                        }
                                        callers[index].setAttribute('data-type-element-inited', 'true');
                                    }
                                }
                            };
                            var id      = node.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.buddypress.creategroup.A.render.hide       (id);
                                pure.buddypress.creategroup.A.render.orderOnTop (id);
                                attachEvents('open',    id);
                                attachEvents('close',   id);
                                pure.buddypress.creategroup.A.pages.    initFinish  (id);
                                pure.buddypress.creategroup.A.request.  init        (id);
                            }
                            node.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            }
        },
        render      : {
            orderOnTop  : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.New.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        document.body.appendChild(instances[index]);
                    }
                }
            },
            show        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.New.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    pure.buddypress.creategroup.A.render.orderOnTop (id);
                    pure.buddypress.creategroup.A.pages.clear       (id);
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = '';
                    }
                }
            },
            hide        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.New.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = 'none';
                    }
                }
            }
        },
        pages       : {
            clear       : function(id){
                var nodes = pure.buddypress.creategroup.A.helpers.getNodesValues(id, true);
                if (nodes !== null){
                    nodes.name.             value   = '';
                    nodes.description.      value   = '';
                    nodes.visibility[0].    checked = true;
                    nodes.invitations[0].   checked = true;
                    nodes.switcherName.     checked = true
                }
            },
            initFinish  : function(id){
                var instance = pure.nodes.select.first('*[data-engine-element="Group.New.Container"][data-engine-element-id="' + id + '"] input[data-engine-element="Group.New.Switcher.Summary"]');
                if (instance !== null){
                    pure.events.add(
                        instance,
                        'change',
                        function(){
                            if (instance.checked !== false){
                                pure.buddypress.creategroup.A.pages.summary(id);
                            }
                        }
                    );
                }
            },
            summary     : function(id){
                function allow(nodes){
                    nodes.buttons.create.style.display = "";
                };
                function deny(nodes){
                    nodes.buttons.create.style.display = 'none';
                };
                var nodes       = pure.buddypress.creategroup.A.helpers.getNodesValues(id, false),
                    allow_flag  = true;
                if (nodes !== null){
                    nodes._name.innerHTML           = nodes.name;
                    nodes._description.innerHTML    = nodes.description;
                    nodes._visibility.innerHTML     = nodes.visibility.title;
                    nodes._invitations.innerHTML    = nodes.invitations.title;
                    if (nodes.name.length < 3 || nodes.name.length > 255){
                        nodes._name.innerHTML = nodes._name.getAttribute('_value');
                        nodes._name.setAttribute('data-type-validate','bad');
                        allow_flag = false;
                    }else{
                        nodes._name.setAttribute('data-type-validate','good');
                    }
                    if (nodes.description.length < 10){
                        nodes._description.innerHTML = nodes._description.getAttribute('_value');
                        nodes._description.setAttribute('data-type-validate','bad');
                        allow_flag = false;
                    }else{
                        nodes._description.setAttribute('data-type-validate','good');
                    }
                    if (allow_flag === false){
                        deny(nodes);
                    }else{
                        allow(nodes);
                    }
                }
            }
        },
        request     : {
            progress    : {
                isBusy  : function(id){
                    var instance    = pure.nodes.select.first('*[data-engine-element="Group.New.Container"][data-engine-element-id="' + id + '"] *[data-engine-element="Group.New.Window"]'),
                        status      = null;
                    if (instance !== null){
                        status = instance.getAttribute('in-progress');
                        return (status === 'true' ? true : false);
                    }
                    return false;
                },
                busy    : function(id){
                    var instance = pure.nodes.select.first('*[data-engine-element="Group.New.Container"][data-engine-element-id="' + id + '"] *[data-engine-element="Group.New.Window"]');
                    if (instance !== null){
                        instance.setAttribute('in-progress', 'true');
                        return pure.templates.progressbar.A.show(instance, "background: -moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%, rgba(255,255,255,1) 5%, rgba(255,255,255,1) 8%, rgba(255,255,255,0) 100%); background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,1)), color-stop(5%,rgba(255,255,255,1)), color-stop(8%,rgba(255,255,255,1)), color-stop(100%,rgba(255,255,255,0))); background: -webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -o-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: radial-gradient(ellipse at center,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#00ffffff',GradientType=1 );z-index:10000;");
                    }
                    return false;
                },
                free    : function(id, progress){
                    var instance = pure.nodes.select.first('*[data-engine-element="Group.New.Container"][data-engine-element-id="' + id + '"] *[data-engine-element="Group.New.Window"]');
                    if (instance !== null){
                        instance.setAttribute('in-progress', 'false');
                        pure.templates.progressbar.A.hide(progress);
                        return true;
                    }
                    return false;
                }
            },
            init        : function(id){
                var create = pure.nodes.select.first('*[data-engine-element="Group.New.Container"][data-engine-element-id="' + id + '"] *[data-engine-element="Group.New.Button.Create"]');
                if (create !== null){
                    pure.events.add(
                        create,
                        'click',
                        function(){
                            pure.buddypress.creategroup.A.request.send(id);
                        }
                    );
                }
            },
            send        : function(id){
                var nodes       = pure.buddypress.creategroup.A.helpers.getNodesValues(id, false),
                    request     = pure.system.getInstanceByPath('pure.buddypress.creategroup.configuration.request'),
                    destination = pure.system.getInstanceByPath('pure.buddypress.creategroup.configuration.destination'),
                    progress    = null;
                if (nodes !== null && request !== null && destination !== null && pure.buddypress.creategroup.A.request.progress.isBusy(id) === false){
                    progress    = pure.buddypress.creategroup.A.request.progress.busy(id);
                    request     = request.replace(/\[name\]/,           nodes.name                  );
                    request     = request.replace(/\[description\]/,    nodes.description           );
                    request     = request.replace(/\[visibility\]/,     nodes.visibility.value      );
                    request     = request.replace(/\[invitations\]/,    nodes.invitations.value     );
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : destination,
                        request     : request,
                        onrecieve   : function (id_request, response) {
                            pure.buddypress.creategroup.A.request.received(id_request, response, id, progress);
                        },
                        onreaction  : null,
                        onerror     : function (event, id_request) {
                            pure.buddypress.creategroup.A.request.error(event, id_request, id, progress);
                        },
                        ontimeout   : function (event, id_request) {
                            pure.buddypress.creategroup.A.request.error(event, id_request, id, progress);
                        }
                    });
                }
            },
            received    : function(id_request, response, id, progress){
                var message = pure.buddypress.creategroup.A.dialogs.info,
                    result  = null;
                if (progress !== null) {
                    pure.buddypress.creategroup.A.request.progress.free(id, progress);
                    progress = null;
                }
                pure.buddypress.creategroup.A.render.hide(id);
                try{
                    result = JSON.parse(response);
                }catch (e){
                    result = response;
                }
                if (typeof result === 'object'){
                    message(
                        'Successful operation',
                        'You created new group [' + result.name + ']. Now you can manage this group.',
                        [
                            {
                                title       : 'BACK',
                                handle      : null,
                                closeAfter  : true
                            },
                            {
                                title   : 'MANAGE GROUP',
                                handle  : function(){
                                    window.location.href = result.url;
                                },
                                closeAfter  : true
                            }
                        ]
                    );
                }else{
                    switch (result){
                        case 'wrong_name':
                            message('Fail operation', 'Your group is not created. Wrong name of group (should be not less 5 symbols and not more than 255 symbols). Please contact with administrator.', false);
                            break;
                        case 'wrong_description':
                            message('Fail operation', 'Your group is not created. Wrong description of group (should be not less 10 symbols and not more than 500 symbols). Please contact with administrator.', false);
                            break;
                        case 'wrong_visibility':
                            message('Fail operation', 'Your group is not created. Wrong visibility settings. Please contact with administrator.', false);
                            break;
                        case 'wrong_invitations':
                            message('Fail operation', 'Your group is not created. Wrong invitations settings. Please contact with administrator.', false);
                            break;
                        case 'error_during_edit_settings':
                            message('Fail operation', 'There are some error on server side. Your group is created, but some error was during saving configuration of group. Please contact with administrator.', false);
                            break;
                        case 'error_during_creation':
                            message('Fail operation', 'There are some error on server side. Your group is not created. Please contact with administrator.', false);
                            break;
                        case 'no_permissions':
                            message('Fail operation', 'There are some error on server side. You have no necessary permissions. Please contact with administrator.', false);
                            break;
                        case 'unknown_error':
                            message('Fail operation', 'There are some error on server side. You send incorrect data or you have no necessary permissions. Please contact with administrator.', false);
                            break;
                        case 'fail':
                            message('Fail operation', 'There are some error on server side. You send incorrect data or you have no necessary permissions. Please contact with administrator.', false);
                            break;
                    }
                }
            },
            error       : function(event, id_request, id, progress){
                var message = pure.buddypress.creategroup.A.dialogs.info;
                if (progress !== null) {
                    pure.buddypress.creategroup.A.request.progress.free(id, progress);
                    progress = null;
                }
                message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.', false);
            }
        },
        dialogs     : {
            info: function (title, message, _buttons) {
                var _buttons    = (_buttons instanceof Array ? _buttons : null),
                    buttons     = null;
                if (_buttons !== null){
                    buttons = _buttons;
                }else{
                    buttons = [{
                        title       : 'OK',
                        handle      : null,
                        closeAfter  : true
                    }];
                }
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-element-type="Pure.Social.Groups.CreateGroup.A.Dialog">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : buttons
                });
            }
        },
        helpers     : {
            readDataFromAttributes : function(node){
                var params = {
                    id                      : node.getAttribute('data-engine-id'                    ),
                    user                    : node.getAttribute('data-engine-data-user'             ),
                    progress                : node.getAttribute('data-engine-data-progress'         ),
                    destination             : node.getAttribute('data-engine-data-destination'      )
                };
                for(var key in params){
                    params[key] = (typeof params[key] === 'string' ? (params[key] !== '' ? params[key] : null) : null);
                }
                return params;
            },
            getNodesValues : function(id, onlyNodesFlag){
                function getValue(nodes){
                    var title = null;
                    for(var index = nodes.length - 1; index >= 0; index -= 1){
                        if (nodes[index].checked === true){
                            title = nodes[index].getAttribute('_value');
                            if (title !== null && title !== ''){
                                return {
                                    value: nodes[index].value,
                                    title: title
                                }
                            }
                            return null;
                        }
                    }
                    return null;
                };
                var basicSelector   = '*[data-engine-element="Group.New.Container"][data-engine-element-id="' + id + '"]',
                    nodes           = {
                        switcherName        : pure.nodes.select.first   (basicSelector + ' ' + 'input[data-engine-element="Group.New.Switcher.Name"]'       ),
                        switcherDescription : pure.nodes.select.first   (basicSelector + ' ' + 'input[data-engine-element="Group.New.Switcher.Description"]'),
                        switcherSummary     : pure.nodes.select.first   (basicSelector + ' ' + 'input[data-engine-element="Group.New.Switcher.Summary"]'    ),
                        name                : pure.nodes.select.first   (basicSelector + ' ' + 'textarea[data-engine-element="Group.New.Field.Name"]'       ),
                        description         : pure.nodes.select.first   (basicSelector + ' ' + 'textarea[data-engine-element="Group.New.Field.Description"]'),
                        visibility          : pure.nodes.select.all     (basicSelector + ' ' + 'input[data-engine-element="Group.New.Field.Visibility"]'    ),
                        invitations         : pure.nodes.select.all     (basicSelector + ' ' + 'input[data-engine-element="Group.New.Field.Invitations"]'   ),
                        _name               : pure.nodes.select.first   (basicSelector + ' ' + '*[data-engine-element="Group.New.Summary.Name"]'            ),
                        _description        : pure.nodes.select.first   (basicSelector + ' ' + '*[data-engine-element="Group.New.Summary.Description"]'     ),
                        _visibility         : pure.nodes.select.first   (basicSelector + ' ' + '*[data-engine-element="Group.New.Summary.Visibility"]'      ),
                        _invitations        : pure.nodes.select.first   (basicSelector + ' ' + '*[data-engine-element="Group.New.Summary.Invitations"]'     ),
                        buttons             : {
                            create : pure.nodes.select.first(basicSelector + ' ' + '*[data-engine-element="Group.New.Button.Create"]')
                        }
                    };
                if (nodes.startTab !== null && nodes.name !== null && nodes.description !== null && nodes.visibility !== null && nodes.invitations !== null &&
                    nodes._name !== null && nodes._description !== null && nodes._visibility !== null && nodes._invitations !== null &&
                    nodes.buttons.send !== null && nodes.buttons.back !== null){
                    if (onlyNodesFlag === true){
                        return nodes;
                    }else{
                        nodes.visibility    = getValue(nodes.visibility );
                        nodes.invitations   = getValue(nodes.invitations);
                        nodes.name          = nodes.name.value;
                        nodes.description   = nodes.description.value;
                        if (nodes.visibility !== null && nodes.invitations !== null){
                            return nodes;
                        }
                    }
                }
                return null;
            }
        }
    };
    pure.system.start.add(pure.buddypress.creategroup.A.init);
}());