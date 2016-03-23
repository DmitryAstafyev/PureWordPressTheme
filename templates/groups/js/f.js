(function () {
    if (typeof window.pure              !== "object") { window.pure             = {}; }
    if (typeof window.pure.groups       !== "object") { window.pure.groups      = {}; }
    "use strict";
    window.pure.groups.F = {
        data: {
            storage : {},
            set     : function (id, instance) {
                var id = (typeof id === "string" ? id : pure.tools.IDs.get("groups.H.ID."));
                if (typeof pure.groups.F.data.storage[id] !== "object") {
                    pure.groups.F.data.storage[id] = {
                        id          : id
                    };
                    return pure.groups.F.data.storage[id];
                }
                return null;
            },
            get     : function (id) {
                return (typeof pure.groups.F.data.storage[id] === "object" ? pure.groups.F.data.storage[id] : null);
            }
        },
        init    : function () {
            var id              = pure.tools.IDs.get("groups.H.ID."),
                id_attribute    = "data-engine-element-id";
            pure.groups.F.initialize.buttons.all(id, id_attribute);
            pure.groups.F.More.init();
        },
        initialize : {
            buttons : {
                all         : function(id, id_attribute){
                    pure.groups.F.initialize.buttons.membership (id, id_attribute);
                    pure.groups.F.initialize.buttons.content    (id, id_attribute);
                },
                content     : function(id, id_attribute){
                    var instances = pure.nodes.select.all('input[data-engine-type="GroupPostsSwitcher"]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var container_id    = node.getAttribute('id'),
                                        container       = null;
                                    if (container_id !== '' && container_id !== null){
                                        container = pure.nodes.select.first('*[data-engine-content-container-id="' + container_id + '"]');
                                        if (container !== null){
                                            pure.events.add(node, 'change', function(event){
                                                if (event.target){
                                                    if (event.target.checked === true){
                                                        pure.nodes.render.redraw(container);
                                                        pure.appevents.Actions.call('pure.positioning', 'update', null, null);
                                                    }
                                                }
                                            });
                                        }
                                    }
                                    node.setAttribute('data-type-element-inited', 'true');
                                }(instances[index]));
                            }
                        }
                    }
                },
                membership : function(id, id_attribute){
                    var instances = pure.nodes.select.all('a[data-engine-groupID]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node, id){
                                    pure.events.add(node, 'click', function(event){
                                        pure.groups.F.actions.membership.send(event, id, node);
                                    });
                                    node.setAttribute('data-type-element-inited', 'true');
                                }(instances[index], id));
                            }
                        }
                    }
                }
            }
        },
        actions: {
            incomeInvites : {
                send : function(event, id, node){
                    var groupID = null;
                    if (pure.system.getInstanceByPath('pure.settings.plugins.thumbnails.groups.incomeInvites.params') !== null){
                        groupID         = node.getAttribute('data-engine-groupID');
                        if (typeof groupID === 'string' && pure.groups.F.actions.membership.busy.isBusy(node) === false){
                            pure.components.dialogs.A.open({
                                title       : 'Do you want join?',
                                innerHTML   : '<p>Please, make decision join or not to this group.</p>',
                                buttons     : [
                                    {
                                        title   : 'CANCEL',
                                        handle  : null
                                    },
                                    {
                                        title   : 'REJECT',
                                        handle  : function(){
                                            pure.groups.F.actions.incomeInvites.action(id, node, groupID, 'deny');
                                        }
                                    },
                                    {
                                        title   : 'JOIN',
                                        handle  : function(){
                                            pure.groups.F.actions.incomeInvites.action(id, node, groupID, 'accept');
                                        }
                                    }
                                ],
                                width       : 50
                            });

                        }
                    }
                },
                action : function(id, node, groupID, action){
                    var parameters  = pure.settings.plugins.thumbnails.groups.incomeInvites.params;
                    parameters      = parameters.replace(/\[group\]/gi, groupID);
                    parameters      = parameters.replace(/\[action\]/gi, action);
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : pure.settings.plugins.thumbnails.groups.basic.url,
                        request     : parameters,
                        onrecieve   : function (id_request, response) { pure.groups.F.actions.incomeInvites.receive(id_request, response, id, node);},
                        onreaction  : null,
                        onerror     : function (id_request) { pure.groups.F.actions.incomeInvites.receive(id_request, id, node); },
                        ontimeout   : function (id_request) { pure.groups.F.actions.incomeInvites.receive(id_request, id, node); }
                    });
                    pure.groups.F.actions.membership.busy.set(node);
                },
                receive : function(id_request, response, id, node){
                    var title   = '',
                        message = '';
                    switch (response){
                        case 'accepted':
                            title   = 'Operation is successful';
                            message = 'You are joined to this group';
                            node.setAttribute('data-engine-membership-action', 'leave');
                            node.innerHTML = 'Leave';
                            break;
                        case 'denied':
                            title   = 'Operation is successful';
                            message = 'You rejected your request for membership';
                            node.removeAttribute('data-engine-membership-action');
                            node.innerHTML = 'Join';
                            break;
                        case 'fail':
                            title   = 'Operation is failed';
                            message = 'Unfortunately server has some error with module BuddyPress.';
                            node.innerHTML = 'Try again';
                            break;
                    }
                    pure.components.dialogs.A.open({
                        title       : title,
                        innerHTML   : '<p>' + message + '</p>',
                        buttons     : [
                            {
                                title: 'OK',
                                handle : null
                            }
                        ],
                        width       : 50
                    });
                    pure.groups.F.actions.membership.busy.clear(node);
                },
                error   : function(id_request, id, node){
                    pure.components.dialogs.A.open({
                        title       : 'Error',
                        innerHTML   : '<p>Unfortunately your request was not sent. Try again.</p>',
                        buttons     : [
                            {
                                title: 'OK',
                                handle : null
                            }
                        ],
                        width       : 50
                    });
                    node.innerHTML = 'Try again';
                    pure.groups.F.actions.membership.busy.clear(node);
                }
            },
            membership : {
                send : function(event, id, node){
                    var groupID         = null,
                        action          = null;
                    if (pure.system.getInstanceByPath('pure.settings.plugins.thumbnails.groups.membership.params') !== null){
                        groupID         = node.getAttribute('data-engine-groupID');
                        action          = node.getAttribute('data-engine-membership-action');
                        if (typeof groupID === 'string' && pure.groups.F.actions.membership.busy.isBusy(node) === false){
                            if (groupID !== ''){
                                action = (typeof action === 'string' ? (action !== '' ? action : '') : '');
                                if (action === 'leave'){
                                    pure.groups.F.actions.membership.leave.open(id, node, groupID);
                                }else if(action === 'invited'){
                                    pure.groups.F.actions.incomeInvites.send(event, id, node);
                                }else{
                                    pure.groups.F.actions.membership.action(id, node, groupID);
                                }
                            }
                        }
                    }
                },
                action : function(id, node, groupID){
                    var parameters  = pure.settings.plugins.thumbnails.groups.membership.params;
                    parameters      = parameters.replace(/\[group\]/gi, groupID);
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : pure.settings.plugins.thumbnails.groups.basic.url,
                        request     : parameters,
                        onrecieve   : function (id_request, response) { pure.groups.F.actions.membership.receive(id_request, response, id, node);},
                        onreaction  : null,
                        onerror     : function (id_request) { pure.groups.F.actions.membership.receive(id_request, id, node); },
                        ontimeout   : function (id_request) { pure.groups.F.actions.membership.receive(id_request, id, node); }
                    });
                    pure.groups.F.actions.membership.busy.set(node);
                },
                leave   : {
                    open : function(id, node, groupID){
                        pure.components.dialogs.A.open({
                            title       : 'Leaving group',
                            innerHTML   : '<p>Are you sure, what you want leave this group?</p>',
                            buttons     : [
                                {
                                    title   : 'CANCEL',
                                    handle  : null
                                },
                                {
                                    title   : 'LEAVE',
                                    handle  : function(){
                                        pure.groups.F.actions.membership.action(id, node, groupID);
                                    }
                                }
                            ],
                            width       : 50
                        });
                    }
                },
                receive : function(id_request, response, id, node){
                    var title   = '',
                        message = '';
                    switch (response){
                        case 'user_joined_to_group':
                            title   = 'Operation is successful';
                            message = 'You are joined to this group';
                            node.setAttribute('data-engine-membership-action', 'leave');
                            node.innerHTML = 'Leave';
                            break;
                        case 'request_for_membership_sent':
                            title   = 'Operation is successful';
                            message = 'This is private group. You have to wait for a confirmation of your request.';
                            node.removeAttribute('data-engine-membership-action');
                            node.innerHTML = 'Reject request';
                            break;
                        case 'user_removed_from_group':
                            title   = 'Operation is successful';
                            message = 'You are leaved this group';
                            node.removeAttribute('data-engine-membership-action');
                            node.innerHTML = 'Join';
                            break;
                        case 'user_rejected_request_for_membership':
                            title   = 'Operation is successful';
                            message = 'You rejected your request for membership';
                            node.removeAttribute('data-engine-membership-action');
                            node.innerHTML = 'Join';
                            break;
                        case 'BuddyPress_error':
                            title   = 'Operation is failed';
                            message = 'Unfortunately server has some error with module BuddyPress.';
                            node.innerHTML = 'Try again';
                            break;
                        case 'wrong_data':
                            title   = 'Operation is failed';
                            message = 'Unfortunately your authorization data cannot be accepted on server.';
                            node.innerHTML = 'Try again';
                            break;
                    }
                    pure.components.dialogs.A.open({
                        title       : title,
                        innerHTML   : '<p>' + message + '</p>',
                        buttons     : [
                            {
                                title: 'OK',
                                handle : null
                            }
                        ],
                        width       : 50
                    });
                    pure.groups.F.actions.membership.busy.clear(node);
                },
                error   : function(id_request, id, node){
                    pure.components.dialogs.A.open({
                        title       : 'Error',
                        innerHTML   : '<p>Unfortunately your request was not sent. Try again.</p>',
                        buttons     : [
                            {
                                title: 'OK',
                                handle : null
                            }
                        ],
                        width       : 50
                    });
                    node.innerHTML = 'Try again';
                    pure.groups.F.actions.membership.busy.clear(node);
                },
                busy : {
                    isBusy  : function(node){
                        var status = node.getAttribute('data-engine-status');
                        return (typeof status !== 'string' ? false : (status === '' ? false : (status === 'free' ? false : true )));
                    },
                    set     : function(node){
                        node.setAttribute('data-engine-status', 'busy');
                    },
                    clear   : function(node){
                        node.setAttribute('data-engine-status', 'free');
                    }
                }
            }
        },
        More : {
            initialized : false,
            init        : function(){
                if (pure.groups.F.More.initialized === false){
                    pure.appevents.Events.methods.register('pure.more', 'done');
                    pure.appevents.Actions.listen('pure.more', 'done', function(){ pure.groups.F.init(); }, 'pure.groups.F.init');
                    pure.groups.F.More.initialized = true;
                }
            }
        }
    };
    pure.system.start.add(pure.groups.F.init);
}());