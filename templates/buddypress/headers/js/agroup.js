(function () {
    if (typeof window.pure                      !== "object") { window.pure                     = {}; }
    if (typeof window.pure.buddypress           !== "object") { window.pure.buddypress          = {}; }
    if (typeof window.pure.buddypress.header    !== "object") { window.pure.buddypress.header   = {}; }
    "use strict";
    window.pure.buddypress.header.AGroup = {
        init : function(){
            var instances = pure.nodes.select.all('*[data-field-type="Group.Membership.Action"]:not([data-type-element-inited])');
            if (instances !== null) {
                Array.prototype.forEach.call(
                    instances,
                    function(item, index, source){
                        var instance_id = item.getAttribute('data-engine-element-id'),
                            data        = null;
                        if (instance_id !== null){
                            data = {
                                nodes : {
                                    button  : item,
                                    label   : pure.nodes.select.first('*[data-field-type="Group.Membership.Label"][data-engine-element-id="' + instance_id + '"]')
                                },
                                labels : {
                                    join    : item.getAttribute('data-engine-membership-label-join'     ),
                                    leave   : item.getAttribute('data-engine-membership-label-leave'    ),
                                    banned  : item.getAttribute('data-engine-membership-label-banned'   ),
                                    cancel  : item.getAttribute('data-engine-membership-label-cancel'   ),
                                    request : item.getAttribute('data-engine-membership-label-request'  )
                                },
                                attributes : {
                                    name    : item.getAttribute('data-engine-membership-attr-name'      ),
                                    join    : item.getAttribute('data-engine-membership-attr-join'      ),
                                    leave   : item.getAttribute('data-engine-membership-attr-leave'     ),
                                    banned  : item.getAttribute('data-engine-membership-attr-banned'    ),
                                    cancel  : item.getAttribute('data-engine-membership-attr-cancel'    ),
                                    request : item.getAttribute('data-engine-membership-attr-request'   )
                                }
                            };
                            if (pure.tools.objects.isValueIn(data, null, true) === false){
                                pure.events.add(item, 'click', function(event){
                                    pure.buddypress.header.AGroup.onClick(event, instance_id, data);
                                });
                                pure.buddypress.header.AGroup.state.update(data);
                            }
                        }
                        item.setAttribute('data-type-element-inited', 'true');
                    }
                );
            }
        },
        state :{
            update  : function(data){
                var state = data.nodes.button.getAttribute('data-engine-membership-state');
                if (state !== null){
                    if (['join', 'leave', 'cancel', 'request', 'banned'].indexOf(state) !== -1){
                        data.nodes.label.innerHTML = data.labels[state];
                        data.nodes.button.setAttribute(
                            data.attributes.name,
                            data.attributes[state]
                        );
                    }
                }
            },
            get     : function(data){
                return data.nodes.button.getAttribute('data-engine-membership-state');
            },
            set     : function(data, state){
                data.nodes.button.setAttribute('data-engine-membership-state', state);
                pure.buddypress.header.AGroup.state.update(data);
            }
        },
        onClick : function(event, instance_id, data){
            var state       = pure.buddypress.header.AGroup.state.get(data),
                message     = pure.buddypress.header.AGroup.dialogs.info;
            if (state !== null){
                if (pure.buddypress.header.AGroup.progress.isBusy(instance_id) === false){
                    if (state === 'banned'){
                        message('Your status', 'Sorry, but you are banned in this group. You cannot do anything for now.');
                    }else if (state === 'join' || state === 'leave' || state === 'cancel'){
                        pure.buddypress.header.AGroup.action.send(instance_id, data, state);
                    }else if (state === 'request'){
                        pure.buddypress.header.AGroup.request.send(instance_id, data, state);
                    }
                }
            }
        },
        action  : {
            send        : function(instance_id, data, state){
                var request     = pure.system.getInstanceByPath('pure.buddypress.membership.configuration.request.action'),
                    destination = pure.system.getInstanceByPath('pure.buddypress.membership.configuration.destination'),
                    message     = pure.buddypress.header.AGroup.dialogs.info,
                    handle      = null,
                    _message    = '',
                    _title      = '';
                if (request !== null && destination !== null){
                    handle = function(){
                        pure.buddypress.header.AGroup.progress.busy(instance_id);
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.header.AGroup.action.received(id_request, response, instance_id, data);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.header.AGroup.action.error(event, id_request, instance_id, data);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.buddypress.header.AGroup.action.error(event, id_request, instance_id, data);
                            }
                        });
                    };
                    switch(state){
                        case 'join':
                            _message    = 'Are you really want to join this group?';
                            _title      = 'YES, JOIN';
                            break;
                        case 'leave':
                            _message    = 'Are you really want to leave this group?';
                            _title      = 'YES, LEAVE';
                            break;
                        case 'cancel':
                            _message    = 'You request is still in progress. Are you really want cancel it?';
                            _title      = 'YES, REJECT IT';
                            break;
                    }
                    message(
                        'Confirm operation',
                        _message,
                        [
                            {
                                title       : 'CANCEL',
                                handle      : null,
                                closeAfter  : true
                            },
                            {
                                title       : _title,
                                handle      : handle,
                                closeAfter  : true
                            }
                        ]
                    );
                }
            },
            received    : function(id_request, response, instance_id, data){
                var message = pure.buddypress.header.AGroup.dialogs.info;
                switch (response){
                    case 'user_joined_to_group':
                        pure.buddypress.header.AGroup.state.set(data, 'leave');
                        window.location.reload();
                        break;
                    case 'request_for_membership_sent':
                        pure.buddypress.header.AGroup.state.set(data, 'cancel');
                        break;
                    case 'this_is_hidden_group':
                        pure.buddypress.header.AGroup.state.set(data, 'join');
                        message('Fail', 'Sorry, but this group is hidden.');
                        break;
                    case 'user_removed_from_group':
                        pure.buddypress.header.AGroup.state.set(data, 'join');
                        window.location.reload();
                        break;
                    case 'user_rejected_request_for_membership':
                        pure.buddypress.header.AGroup.state.set(data, 'join');
                        break;
                    case 'user_was_banned':
                        pure.buddypress.header.AGroup.state.set(data, 'banned');
                        message('Fail', 'Sorry, but you are banned.');
                        break;
                    default:
                        message('Fail', 'Sorry, but some error there is. Please, try a bit later or contact with administrator.');
                        break;
                }
                pure.buddypress.header.AGroup.progress.free(instance_id)
            },
            error       : function(event, id_request, instance_id, data){
                var message = pure.buddypress.header.AGroup.dialogs.info;
                pure.buddypress.header.AGroup.progress.free(instance_id);
                message('Fail', 'Sorry, but some error there is. Please, try a bit later or contact with administrator.');
            }
        },
        request : {
            send        : function(instance_id, data, state){
                var request     = pure.system.getInstanceByPath('pure.buddypress.membership.configuration.request.income_invite'),
                    destination = pure.system.getInstanceByPath('pure.buddypress.membership.configuration.destination'),
                    message     = pure.buddypress.header.AGroup.dialogs.info,
                    handle      = null,
                    _message    = '',
                    _title      = '';
                if (request !== null && destination !== null){
                    pure.buddypress.header.AGroup.dialogs.info(
                        'Confirm operation',
                        'You have received a invitation of membership in this group. What do you want to do?',
                        [
                            {
                                title       : 'CANCEL',
                                handle      : null,
                                closeAfter  : true
                            },
                            {
                                title       : 'BE MEMBER',
                                handle      : function(){
                                    pure.buddypress.header.AGroup.progress.busy(instance_id);
                                    request = request.replace(/\[action\]/, 'accept');
                                    pure.tools.request.send({
                                        type        : 'POST',
                                        url         : destination,
                                        request     : request,
                                        onrecieve   : function (id_request, response) {
                                            pure.buddypress.header.AGroup.request.received(id_request, response, instance_id, data);
                                        },
                                        onreaction  : null,
                                        onerror     : function (event, id_request) {
                                            pure.buddypress.header.AGroup.request.error(event, id_request, instance_id, data);
                                        },
                                        ontimeout   : function (event, id_request) {
                                            pure.buddypress.header.AGroup.request.error(event, id_request, instance_id, data);
                                        }
                                    });
                                },
                                closeAfter  : true
                            },
                            {
                                title       : 'REJECT IT',
                                handle      : function(){
                                    pure.buddypress.header.AGroup.progress.busy(instance_id);
                                    request = request.replace(/\[action\]/, 'deny');
                                    pure.tools.request.send({
                                        type        : 'POST',
                                        url         : destination,
                                        request     : request,
                                        onrecieve   : function (id_request, response) {
                                            pure.buddypress.header.AGroup.request.received(id_request, response, instance_id, data);
                                        },
                                        onreaction  : null,
                                        onerror     : function (event, id_request) {
                                            pure.buddypress.header.AGroup.request.error(event, id_request, instance_id, data);
                                        },
                                        ontimeout   : function (event, id_request) {
                                            pure.buddypress.header.AGroup.request.error(event, id_request, instance_id, data);
                                        }
                                    });
                                },
                                closeAfter  : true
                            }
                        ]
                    );
                }
            },
            received    : function(id_request, response, instance_id, data){
                var message = pure.buddypress.header.AGroup.dialogs.info;
                switch (response){
                    case 'accepted':
                        pure.buddypress.header.AGroup.state.set(data, 'leave');
                        window.location.reload();
                        break;
                    case 'denied':
                        pure.buddypress.header.AGroup.state.set(data, 'join');
                        break;
                    default:
                        message('Fail', 'Sorry, but some error there is. Please, try a bit later or contact with administrator.');
                        break;
                }
                pure.buddypress.header.AGroup.progress.free(instance_id)
            },
            error       : function(event, id_request, instance_id, data){
                var message = pure.buddypress.header.AGroup.dialogs.info;
                pure.buddypress.header.AGroup.progress.free(id_request);
                message('Fail', 'Sorry, but some error there is. Please, try a bit later or contact with administrator.');
            }
        },
        progress    : {
            data    : {},
            isBusy : function(instance_id){
                var data = pure.buddypress.header.AGroup.progress.data;
                return (typeof data[instance_id] !== 'undefined' ? true : false);
            },
            busy    : function(instance_id){
                var data        = pure.buddypress.header.AGroup.progress.data,
                    instance    = pure.nodes.select.first('*[data-field-type="Group.Membership.Action"][data-engine-element-id="' + instance_id + '"]');
                if (instance !== null){
                    data[instance_id] = pure.templates.progressbar.A.show(instance);
                }
            },
            free    : function(instance_id){
                var data = pure.buddypress.header.AGroup.progress.data;
                if (typeof data[instance_id] !== 'undefined'){
                    pure.templates.progressbar.A.hide(data[instance_id]);
                    data[instance_id] = null;
                    delete data[instance_id];
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
    pure.system.start.add(pure.buddypress.header.AGroup.init);
}());