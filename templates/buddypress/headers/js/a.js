(function () {
    if (typeof window.pure                      !== "object") { window.pure                     = {}; }
    if (typeof window.pure.buddypress           !== "object") { window.pure.buddypress          = {}; }
    if (typeof window.pure.buddypress.header    !== "object") { window.pure.buddypress.header   = {}; }
    "use strict";
    window.pure.buddypress.header.A = {
        init        : function(){
            var instances = pure.nodes.select.all('*[data-engine-friendship-user_id]:not([data-type-element-inited])');
            if (instances !== null) {
                if (typeof instances.length === "number") {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(node){
                            var member_id   = node.getAttribute('data-engine-friendship-user_id'),
                                data        = {
                                    nodes       : {},
                                    labels      : {},
                                    attributes  : {}
                                };
                            if (member_id !== null){
                                data = {
                                    nodes : {
                                        button  : node,
                                        label   : pure.nodes.select.first('*[data-engine-friendship-label="' + member_id + '"]')
                                    },
                                    labels : {
                                        add     : node.getAttribute('data-engine-friendship-label-add'      ),
                                        remove  : node.getAttribute('data-engine-friendship-label-remove'   ),
                                        cancel  : node.getAttribute('data-engine-friendship-label-cancel'   ),
                                        accept  : node.getAttribute('data-engine-friendship-label-accept'   )
                                    },
                                    attributes : {
                                        name    : node.getAttribute('data-engine-friendship-attr-name'     ),
                                        add     : node.getAttribute('data-engine-friendship-attr-add'      ),
                                        remove  : node.getAttribute('data-engine-friendship-attr-remove'   ),
                                        cancel  : node.getAttribute('data-engine-friendship-attr-cancel'   ),
                                        accept  : node.getAttribute('data-engine-friendship-attr-accept'   )
                                    }
                                };
                                if (pure.tools.objects.isValueIn(data, null, true) === false){
                                    pure.events.add(node, 'click', function(event){
                                        pure.buddypress.header.A.request.prepare(event, member_id, data);
                                    });
                                    pure.buddypress.header.A.state.update(data);
                                }
                            }
                            node.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            }
        },
        state       : {
            update : function(data){
                var state = data.nodes.button.getAttribute('data-engine-friendship-state');
                if (state !== null){
                    if (['add', 'remove', 'cancel', 'accept'].indexOf(state) !== -1){
                        data.nodes.label.innerHTML = data.labels[state];
                        data.nodes.button.setAttribute(
                            data.attributes.name,
                            data.attributes[state]
                        );
                    }
                }
            },
            get : function(data){
                return data.nodes.button.getAttribute('data-engine-friendship-state');
            },
            set : function(data, state){
                data.nodes.button.setAttribute('data-engine-friendship-state', state);
                pure.buddypress.header.A.state.update(data);
            }
        },
        request     : {
            prepare     : function(event, member_id, data){
                var request     = pure.system.getInstanceByPath('pure.buddypress.friendship.configuration.request'),
                    destination = pure.system.getInstanceByPath('pure.buddypress.friendship.configuration.destination'),
                    state       = pure.buddypress.header.A.state.get(data);
                if (request !== null && destination !== null && state !== null){
                    if (pure.buddypress.header.A.progress.isBusy(member_id) === false){
                        request = request.replace(/\[friend\]/, member_id);
                        switch (state){
                            case 'add':
                                pure.buddypress.header.A.request.send(
                                    member_id,
                                    data,
                                    destination,
                                    request,
                                    ''
                                );
                                break;
                            case 'cancel':
                                pure.buddypress.header.A.request.send(
                                    member_id,
                                    data,
                                    destination,
                                    request,
                                    ''
                                );
                                break;
                            case 'remove':
                                pure.buddypress.header.A.dialogs.info(
                                    'Confirm operation',
                                    'Are you really want cancel friendship with this member?',
                                    [
                                        {
                                            title       : 'NO',
                                            handle      : null,
                                            closeAfter  : true
                                        },
                                        {
                                            title       : 'YES, REMOVE',
                                            handle      : function(){
                                                pure.buddypress.header.A.request.send(
                                                    member_id,
                                                    data,
                                                    destination,
                                                    request,
                                                    ''
                                                );
                                            },
                                            closeAfter  : true
                                        }
                                    ]
                                );
                                break;
                            case 'accept':
                                pure.buddypress.header.A.dialogs.info(
                                    'Confirm operation',
                                    'This user has requested your friendship. What will you do?',
                                    [
                                        {
                                            title       : 'LATER ON',
                                            handle      : null,
                                            closeAfter  : true
                                        },
                                        {
                                            title       : 'ACCEPT',
                                            handle      : function(){
                                                pure.buddypress.header.A.request.send(
                                                    member_id,
                                                    data,
                                                    destination,
                                                    request,
                                                    'accept'
                                                );
                                            },
                                            closeAfter  : true
                                        },
                                        {
                                            title       : 'DENY',
                                            handle      : function(){
                                                pure.buddypress.header.A.request.send(
                                                    member_id,
                                                    data,
                                                    destination,
                                                    request,
                                                    'deny'
                                                );
                                            },
                                            closeAfter  : true
                                        }
                                    ]
                                );
                                break;
                        }
                    }
                }
            },
            send        : function(member_id, data, destination, request, action){
                var request = request;
                request = request.replace(/\[action\]/, action);
                pure.buddypress.header.A.progress.busy(member_id);
                pure.tools.request.send({
                    type        : 'POST',
                    url         : destination,
                    request     : request,
                    onrecieve   : function (id_request, response) {
                        pure.buddypress.header.A.request.received(id_request, response, member_id, data);
                    },
                    onreaction  : null,
                    onerror     : function (event, id_request) {
                        pure.buddypress.header.A.request.error(event, id_request, member_id, data);
                    },
                    ontimeout   : function (event, id_request) {
                        pure.buddypress.header.A.request.error(event, id_request, member_id, data);
                    }
                });
            },
            received    : function(id_request, response, member_id, data){
                var message = pure.buddypress.header.A.dialogs.info;
                switch (response){
                    case 'request_for_friendship_is_sent':
                        pure.buddypress.header.A.state.set(data, 'cancel');
                        break;
                    case 'request_for_cancel_friendship_is_sent':
                        pure.buddypress.header.A.state.set(data, 'add');
                        break;
                    case 'cancel_request_for_friendship':
                        pure.buddypress.header.A.state.set(data, 'add');
                        break;
                    case 'friendship_accepted':
                        pure.buddypress.header.A.state.set(data, 'remove');
                        break;
                    case 'friendship_denied':
                        pure.buddypress.header.A.state.set(data, 'add');
                        break;
                    default:
                        message('Fail', 'Sorry, but some error there is. Please, try a bit later or contact with administrator.');
                        break;
                }
                pure.buddypress.header.A.progress.free(member_id)
            },
            error       : function(event, id_request, member_id, data){
                var message = pure.buddypress.header.A.dialogs.info;
                pure.buddypress.header.A.progress.free(member_id);
                message('Fail', 'Sorry, but some error there is. Please, try a bit later or contact with administrator.');
            }
        },
        progress    : {
            data    : {},
            isBusy  : function(member_id){
                var data = pure.buddypress.header.A.progress.data;
                return (typeof data[member_id] !== 'undefined' ? true : false);
            },
            busy    : function(member_id){
                var data        = pure.buddypress.header.A.progress.data,
                    instance    = pure.nodes.select.first('*[data-engine-friendship-user_id="' + member_id + '"]');
                if (instance !== null){
                    data[member_id] = pure.templates.progressbar.A.show(instance);
                }
            },
            free    : function(member_id){
                var data = pure.buddypress.header.A.progress.data;
                if (typeof data[member_id] !== 'undefined'){
                    pure.templates.progressbar.B.hide(data[member_id]);
                    data[member_id] = null;
                    delete data[member_id];
                }
            }
        },
        stream      : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-engine-stream-target_id]:not([data-type-element-inited])');
                if (instances !== null) {
                    if (typeof instances.length === "number") {
                        for (var index = instances.length - 1; index >= 0; index -= 1) {
                            (function(node){
                                var target_id   = node.getAttribute('data-engine-stream-target_id'),
                                    data        = {
                                        nodes       : {},
                                        labels      : {},
                                        attributes  : {}
                                    };
                                if (target_id !== null){
                                    data = {
                                        nodes : {
                                            button  : node,
                                            label   : pure.nodes.select.first('*[data-engine-stream-label="' + target_id + '"]')
                                        },
                                        labels : {
                                            add     : node.getAttribute('data-engine-stream-label-add'      ),
                                            remove  : node.getAttribute('data-engine-stream-label-remove'   )
                                        },
                                        attributes : {
                                            name    : node.getAttribute('data-engine-stream-attr-name'     ),
                                            add     : node.getAttribute('data-engine-stream-attr-add'      ),
                                            remove  : node.getAttribute('data-engine-stream-attr-remove'   )
                                        }
                                    };
                                    if (pure.tools.objects.isValueIn(data, null, true) === false){
                                        pure.events.add(node, 'click', function(event){
                                            pure.buddypress.header.A.stream.request.send(event, target_id, data);
                                        });
                                        pure.buddypress.header.A.stream.state.update(data);
                                    }
                                }
                                node.setAttribute('data-type-element-inited', 'true');
                            }(instances[index]));
                        }
                    }
                }
            },
            state   : {
                update  : function(data){
                    var state = data.nodes.button.getAttribute('data-engine-stream-state');
                    if (state !== null){
                        if (['add', 'remove'].indexOf(state) !== -1){
                            data.nodes.label.innerHTML = data.labels[state];
                            data.nodes.button.setAttribute(
                                data.attributes.name,
                                data.attributes[state]
                            );
                        }
                    }
                },
                get     : function(data){
                    return data.nodes.button.getAttribute('data-engine-stream-state');
                },
                set     : function(data, state){
                    data.nodes.button.setAttribute('data-engine-stream-state', state);
                    pure.buddypress.header.A.stream.state.update(data);
                },
                toggle  : function(data){
                    var state = data.nodes.button.getAttribute('data-engine-stream-state');
                    if (state !== null){
                        pure.buddypress.header.A.stream.state.set(
                            data,
                            (state === 'add' ? 'remove' : 'add')
                        );
                    }
                }
            },
            progress    : {
                data    : {},
                isBusy  : function(target_id){
                    var data = pure.buddypress.header.A.stream.progress.data;
                    return (typeof data[target_id] !== 'undefined' ? true : false);
                },
                busy    : function(target_id){
                    var data        = pure.buddypress.header.A.stream.progress.data,
                        instance    = pure.nodes.select.first('*[data-engine-stream-target_id="' + target_id + '"]');
                    if (instance !== null){
                        data[target_id] = pure.templates.progressbar.A.show(instance);
                    }
                },
                free    : function(target_id){
                    var data = pure.buddypress.header.A.stream.progress.data;
                    if (typeof data[target_id] !== 'undefined'){
                        pure.templates.progressbar.B.hide(data[target_id]);
                        data[target_id] = null;
                        delete data[target_id];
                    }
                }
            },
            request     : {
                send : function(event, target_id, data){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.stream.configuration.request.toggle'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.stream.configuration.request.destination'),
                        state       = pure.buddypress.header.A.stream.state.get(data);
                    if (request !== null && destination !== null && state !== null){
                        if (pure.buddypress.header.A.stream.progress.isBusy(target_id) === false){
                            request = request.replace(/\[target_id\]/, target_id);
                            pure.buddypress.header.A.stream.progress.busy(target_id);
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.buddypress.header.A.stream.request.received(id_request, response, target_id, data);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.buddypress.header.A.stream.request.error(event, id_request, target_id, data);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.buddypress.header.A.stream.request.error(event, id_request, target_id, data);
                                }
                            });
                        }
                    }
                },
                received    : function(id_request, response, target_id, data){
                    var message = pure.buddypress.header.A.dialogs.info;
                    switch (response){
                        case 'done':
                            pure.buddypress.header.A.stream.state.toggle(data);
                            break;
                        default:
                            message('Fail', 'Sorry, but some error there is. Please, try a bit later or contact with administrator.');
                            break;
                    }
                    pure.buddypress.header.A.stream.progress.free(target_id)
                },
                error       : function(event, id_request, target_id, data){
                    var message = pure.buddypress.header.A.dialogs.info;
                    pure.buddypress.header.A.stream.progress.free(target_id);
                    message('Fail', 'Sorry, but some error there is. Please, try a bit later or contact with administrator.');
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
    pure.system.start.add(pure.buddypress.header.A.init);
    pure.system.start.add(pure.buddypress.header.A.stream.init);
}());