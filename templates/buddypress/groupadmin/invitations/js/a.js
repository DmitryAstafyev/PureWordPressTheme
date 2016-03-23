(function () {
    if (typeof window.pure                                  !== "object") { window.pure                                     = {}; }
    if (typeof window.pure.buddypress                       !== "object") { window.pure.buddypress                          = {}; }
    if (typeof window.pure.buddypress.groupadmin            !== "object") { window.pure.buddypress.groupadmin               = {}; }
    if (typeof window.pure.buddypress.groupadmin.invitaions !== "object") { window.pure.buddypress.groupadmin.invitaions    = {}; }
    "use strict";
    window.pure.buddypress.groupadmin.invitaions.A = {
        init        : function () {
            pure.buddypress.groupadmin.invitaions.A.initialize. init();
            pure.buddypress.groupadmin.invitaions.A.invite.     init();
        },
        initialize  : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Invitations.Container"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        (function(node){
                            function attachEvents(eventType, id){
                                var callers = pure.nodes.select.all('*[data-engine-groupinvitations-' + eventType + '-caller="' + id + '"]:not([data-type-element-inited])');
                                if (callers !== null){
                                    for(var index = callers.length - 1; index >= 0; index -= 1){
                                        if (eventType === 'open'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.groupadmin.invitaions.A.render.show(id);
                                            });
                                        }
                                        if (eventType === 'close'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.groupadmin.invitaions.A.render.hide(id);
                                            });
                                        }
                                        callers[index].setAttribute('data-type-element-inited', 'true');
                                    }
                                }
                            };
                            var id      = node.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.buddypress.groupadmin.invitaions.A.render.hide          (id);
                                pure.buddypress.groupadmin.invitaions.A.render.orderOnTop    (id);
                                attachEvents('open',    id);
                                attachEvents('close',   id);
                            }
                            node.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            }
        },
        render      : {
            orderOnTop  : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Invitations.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        document.body.appendChild(instances[index]);
                    }
                }
            },
            show        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Invitations.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    pure.buddypress.groupadmin.invitaions.A.render.orderOnTop (id);
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = '';
                    }
                }
            },
            hide        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Invitations.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = 'none';
                    }
                }
            }
        },
        invite      : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-field-type="Group.Invitations.Button.Send"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance){
                            var instance_id = instance.getAttribute('data-engine-element-id'),
                                nodes       = {};
                            if (instance_id !== null){
                                nodes = {
                                    invites : pure.nodes.select.all('input[data-field-type="Group.Invitations.Button.Invite"][data-engine-element-id="' + instance_id + '"]'),
                                    rejects : pure.nodes.select.all('*[data-field-type="Group.Invitations.Button.Reject"][data-engine-element-id="' + instance_id + '"]')
                                };
                                if (pure.tools.objects.isValueIn(nodes, null) === false){
                                    pure.events.add(
                                        instance,
                                        'click',
                                        function(){
                                            pure.buddypress.groupadmin.invitaions.A.invite.action.invite(instance_id, nodes);
                                        }
                                    );
                                    for(var index = nodes.rejects.length - 1; index >= 0; index -= 1){
                                        (function(button, nodes, instance_id){
                                            pure.events.add(
                                                button,
                                                'click',
                                                function(){
                                                    pure.buddypress.groupadmin.invitaions.A.invite.action.reject(instance_id, nodes, button);
                                                }
                                            );
                                        }(nodes.rejects[index], nodes, instance_id));
                                    }
                                }
                            }
                            instance.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            },
            action : {
                invite  : function(instance_id, nodes){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.groupadmin.invitaions.configuration.request.action'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.groupadmin.invitaions.configuration.destination'),
                        user_id     = null,
                        members     = [];
                    if (request !== null && destination !== null){
                        if (pure.buddypress.groupadmin.invitaions.A.progress.global.isBusy(instance_id) === false){
                            for (var index = nodes.invites.length - 1; index >= 0; index -= 1){
                                user_id = nodes.invites[index].getAttribute('data-engine-data-member');
                                if (user_id !== null){
                                    if (nodes.invites[index].checked !== false){
                                        members.push(user_id);
                                    }
                                }
                            }
                            if (members.length > 0){
                                request = request.replace(/\[members\]/, members.join(',')  );
                                request = request.replace(/\[action\]/, 'invite'            );
                                pure.buddypress.groupadmin.invitaions.A.invite.action.send(
                                    request,
                                    destination,
                                    instance_id,
                                    nodes,
                                    members
                                );
                            }
                        }
                    }
                },
                reject  : function(instance_id, nodes, button){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.groupadmin.invitaions.configuration.request.action'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.groupadmin.invitaions.configuration.destination'),
                        user_id     = null;
                    if (request !== null && destination !== null){
                        if (pure.buddypress.groupadmin.invitaions.A.progress.global.isBusy(instance_id) === false){
                            user_id = button.getAttribute('data-engine-data-member');
                            if (user_id !== null){
                                request = request.replace(/\[members\]/, user_id    );
                                request = request.replace(/\[action\]/, 'reject'    );
                                pure.buddypress.groupadmin.invitaions.A.invite.action.send(
                                    request,
                                    destination,
                                    instance_id,
                                    nodes,
                                    [user_id]
                                );
                            }
                        }
                    }
                },
                send        : function(request, destination, instance_id, nodes, members){
                    pure.buddypress.groupadmin.invitaions.A.progress.global.busy(instance_id);
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : destination,
                        request     : request,
                        onrecieve   : function (id_request, response) {
                            pure.buddypress.groupadmin.invitaions.A.invite.action.received(id_request, response, instance_id, nodes, members);
                        },
                        onreaction  : null,
                        onerror     : function (event, id_request) {
                            pure.buddypress.groupadmin.invitaions.A.invite.action.error(event, id_request, instance_id, nodes, members);
                        },
                        ontimeout   : function (event, id_request) {
                            pure.buddypress.groupadmin.invitaions.A.invite.action.error(event, id_request, instance_id, nodes, members);
                        }
                    });
                },
                received    : function(id_request, response, instance_id, nodes, members){
                    var message = pure.buddypress.groupadmin.invitaions.A.dialogs.info;
                    switch (response){
                        case 'invited':
                            pure.buddypress.groupadmin.invitaions.A.invite.action.reverse(nodes, members);
                            break;
                        case 'rejected':
                            pure.buddypress.groupadmin.invitaions.A.invite.action.reverse(nodes, members);
                            break;
                        default :
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break;
                    }
                    pure.buddypress.groupadmin.invitaions.A.progress.global.free(instance_id);
                },
                error       : function(event, id_request, instance_id, nodes, members, button){
                    var message = pure.buddypress.groupadmin.invitaions.A.dialogs.info;
                    pure.buddypress.groupadmin.invitaions.A.progress.global.free(instance_id);
                    message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                },
                reverse : function(nodes, members){
                    function processing(nodes, members, reset){
                        var state   = null,
                            user_id = null;
                        for (var index = nodes.length - 1; index >= 0; index -= 1){
                            state   = nodes[index].getAttribute('data-engine-state'         );
                            user_id = nodes[index].getAttribute('data-engine-data-member'   );
                            if (state !== null && user_id !== ''){
                                if (members.indexOf(user_id) !== -1){
                                    if (state === 'show'){
                                        nodes[index].setAttribute('data-engine-state', 'hide');
                                    }else{
                                        nodes[index].setAttribute('data-engine-state', 'show');
                                    }
                                    if (reset !== false){
                                        nodes[index].checked = false;
                                    }
                                }
                            }
                        }
                    };
                    processing(nodes.invites, members, true );
                    processing(nodes.rejects, members, false);
                }
            }
        },
        progress    : {
            global    : {
                data    : {},
                isBusy : function(instance_id){
                    var data = pure.buddypress.groupadmin.invitaions.A.progress.global.data;
                    return (typeof data[instance_id] !== 'undefined' ? true : false);
                },
                busy    : function(instance_id){
                    var data        = pure.buddypress.groupadmin.invitaions.A.progress.global.data,
                        instance    = pure.nodes.select.first('*[data-engine-element="Group.Invitations.Page"][data-engine-element-id="' + instance_id + '"]');
                    if (instance !== null){
                        data[instance_id] = pure.templates.progressbar.A.show(instance, "background: -moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%, rgba(255,255,255,1) 5%, rgba(255,255,255,1) 8%, rgba(255,255,255,0) 100%); background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,1)), color-stop(5%,rgba(255,255,255,1)), color-stop(8%,rgba(255,255,255,1)), color-stop(100%,rgba(255,255,255,0))); background: -webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -o-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: radial-gradient(ellipse at center,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#00ffffff',GradientType=1 );z-index:10000;");
                    }
                },
                free    : function(instance_id){
                    var data = pure.buddypress.groupadmin.invitaions.A.progress.global.data;
                    if (typeof data[instance_id] !== 'undefined'){
                        pure.templates.progressbar.B.hide(data[instance_id]);
                        data[instance_id] = null;
                        delete data[instance_id];
                    }
                }
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
                    innerHTML   : '<p>' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : buttons
                });
            }
        }
    };
    pure.system.start.add(pure.buddypress.groupadmin.invitaions.A.init);
}());