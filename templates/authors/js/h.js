(function () {
    if (typeof window.pure              !== "object") { window.pure             = {}; }
    if (typeof window.pure.authors      !== "object") { window.pure.authors     = {}; }
    "use strict";
    window.pure.authors.H = {
        data: {
            storage : {},
            set     : function (id, instance) {
                var id = (typeof id === "string" ? id : pure.tools.IDs.get("authors.H.ID."));
                if (typeof pure.authors.H.data.storage[id] !== "object") {
                    pure.authors.H.data.storage[id] = {
                        id          : id
                    };
                    return pure.authors.H.data.storage[id];
                }
                return null;
            },
            get     : function (id) {
                return (typeof pure.authors.H.data.storage[id] === "object" ? pure.authors.H.data.storage[id] : null);
            }
        },
        init    : function () {
            var id              = pure.tools.IDs.get("authors.H.ID."),
                id_attribute    = "data-engine-element-id";
            pure.authors.H.initialize.buttons.all(id, id_attribute);
            pure.authors.H.More.init();
        },
        initialize : {
            buttons : {
                all     : function(id, id_attribute){
                    pure.authors.H.initialize.buttons.posts     (id, id_attribute);
                    pure.authors.H.initialize.buttons.friendship(id, id_attribute);
                },
                posts   : function(id, id_attribute){
                    var instances = pure.nodes.select.all('input[data-engine-type="Switcher"][data-type-addition="Posts"]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var container_id    = node.getAttribute('data-engine-container-id'),
                                        container       = null;
                                    if (container_id !== '' && container_id !== null){
                                        container = pure.nodes.select.first('*[data-engine-container-id="' + container_id + '"]:not(input)');
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
                friendship : function(id, id_attribute){
                    var instances = pure.nodes.select.all('*[data-engine-friendID]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node, id){
                                    pure.events.add(node, 'click', function(event){
                                        pure.authors.H.actions.friendship.send(event, id, node);
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
            friendship : {
                send : function(event, id, node){
                    //pure.authors.H.actions.friendship.busy.set(node);return;
                    var friendID        = null,
                        action          = null,
                        parameters      = '';
                    if (pure.system.getInstanceByPath('pure.settings.plugins.thumbnails.authors.friendship.params') !== null){
                        friendID        = node.getAttribute('data-engine-friendID');
                        action          = node.getAttribute('data-engine-friendship-action');
                        if (typeof friendID === 'string' && pure.authors.H.actions.friendship.busy.isBusy(node) === false){
                            if (friendID !== ''){
                                action          = (typeof action === 'string' ? (action !== '' ? action : '') : '');
                                if (action === 'request'){
                                    pure.authors.H.actions.friendship.request.open(id, node, friendID);
                                }else if(action === 'remove'){
                                    pure.authors.H.actions.friendship.remove.open(id, node, friendID);
                                }else{
                                    pure.authors.H.actions.friendship.action(id, node, friendID, action);
                                }
                            }
                        }
                    }
                },
                action : function(id, node, friendID, action){
                    var parameters  = pure.settings.plugins.thumbnails.authors.friendship.params;
                    parameters      = parameters.replace(/\[friend\]/gi, friendID   );
                    parameters      = parameters.replace(/\[action\]/gi, action         );
                    pure.authors.H.actions.friendship.busy.set(node);
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : pure.settings.plugins.thumbnails.authors.basic.url,
                        request     : parameters,
                        onrecieve   : function (id_request, response) { pure.authors.H.actions.friendship.receive(id_request, response, id, node);},
                        onreaction  : null,
                        onerror     : function (id_request) { pure.authors.H.actions.friendship.receive(id_request, id, node); },
                        ontimeout   : function (id_request) { pure.authors.H.actions.friendship.receive(id_request, id, node); }
                    });
                    pure.authors.H.actions.friendship.busy.set(node);
                },
                request : {
                    open : function(id, node, friendID){
                        pure.components.dialogs.B.open({
                            title       : 'Request of friendship',
                            innerHTML   : '<p>This user has requested the friendship with you. You can accept it and you will be a friends or deny.</p>',
                            buttons     : [
                                {
                                    title   : 'DO NOTHING',
                                    handle  : null
                                },
                                {
                                    title   : 'DENY',
                                    handle  : function(){
                                        pure.authors.H.actions.friendship.action(id, node, friendID, 'deny');
                                    }
                                },
                                {
                                    title   : 'ACCEPT',
                                    handle  : function(){
                                        pure.authors.H.actions.friendship.action(id, node, friendID, 'accept');
                                    }
                                }
                            ],
                            parent      : document.body,
                            width       : 70
                        });
                    }
                },
                remove : {
                    open : function(id, node, friendID){
                        pure.components.dialogs.B.open({
                            title       : 'Removing friendship',
                            innerHTML   : '<p>Are you sure, what you want remove this user from list of your friends?</p>',
                            buttons     : [
                                {
                                    title   : 'CANCEL',
                                    handle  : null
                                },
                                {
                                    title   : 'REMOVE',
                                    handle  : function(){
                                        pure.authors.H.actions.friendship.action(id, node, friendID, '');
                                    }
                                }
                            ],
                            parent      : document.body,
                            width       : 70
                        });
                    }
                },
                receive : function(id_request, response, id, node){
                    var title   = '',
                        message = '',
                        parent  = pure.nodes.find.parentByAttr(node, {name :'data-engine-element', value:'dialog_parent'});
                    switch (response){
                        case 'request_for_friendship_is_sent':
                            title   = 'Operation is successful';
                            message = 'Your request for friendship was sent to user. Now you should wait for his acceptation.';
                            node.removeAttribute('data-engine-friendship-action');
                            node.innerHTML = 'Cancel request';
                            break;
                        case 'request_for_cancel_friendship_is_sent':
                            title   = 'Operation is successful';
                            message = 'You canceled friendship with this user.';
                            node.removeAttribute('data-engine-friendship-action');
                            node.innerHTML = 'Add';
                            break;
                        case 'cancel_request_for_friendship':
                            title   = 'Operation is successful';
                            message = 'Your request for friendship was revoked.';
                            node.removeAttribute('data-engine-friendship-action');
                            node.innerHTML = 'Add';
                            break;
                        case 'friendship_accepted':
                            title   = 'Operation is successful';
                            message = 'From now you and this user are friends.';
                            node.setAttribute('data-engine-friendship-action', 'remove');
                            node.innerHTML = 'Remove';
                            break;
                        case 'friendship_denied':
                            title   = 'Operation is successful';
                            message = 'You denied in request for friendship from this user.';
                            node.removeAttribute('data-engine-friendship-action');
                            node.innerHTML = 'Add';
                            break;
                        case 'BuddyPress_error':
                            title   = 'Operation is failed';
                            message = 'Unfortunately server has some error with module BuddyPress.';
                            node.innerHTML = 'Try again';
                            break;
                        case 'wrong_user':
                            title   = 'Operation is failed';
                            message = 'Unfortunately your authorization data cannot be accepted on server.';
                            node.innerHTML = 'Try again';
                            break;
                    }
                    if (parent !== null){
                        pure.components.dialogs.A.open({
                            title       : title,
                            innerHTML   : '<p>' + message + '</p>',
                            buttons     : [
                                {
                                    title: 'OK',
                                    handle : null
                                }
                            ],
                            parent      : parent,
                            width       : 70
                        });
                    }
                    pure.authors.H.actions.friendship.busy.clear(node);
                },
                error : function(id_request, id, node){
                    pure.components.dialogs.A.open({
                        title       : 'Error',
                        innerHTML   : '<p>Unfortunately your request was not sent. Try again.</p>',
                        buttons     : [
                            {
                                title: 'OK',
                                handle : null
                            }
                        ],
                        parent      : pure.nodes.find.parentByAttr(node, {name :'data-engine-element', value:'dialog_parent'}),
                        width       : 70
                    });
                    node.innerHTML = 'Try again';
                    pure.authors.H.actions.friendship.busy.clear(node);
                },
                busy : {
                    isBusy  : function(node){
                        var status = node.getAttribute('data-engine-status');
                        return (typeof status !== 'string' ? false : (status === '' ? false : (status === 'free' ? false : true )));
                    },
                    set     : function(node){
                        pure.templates.progressbar.B.show(node);
                        node.setAttribute('data-engine-status', 'busy');
                    },
                    clear   : function(node){
                        pure.templates.progressbar.B.hideByParent(node);
                        node.setAttribute('data-engine-status', 'free');
                    }
                }
            }
        },
        More : {
            initialized : false,
            init        : function(){
                if (pure.authors.H.More.initialized === false){
                    pure.appevents.Events.methods.register('pure.more', 'done');
                    pure.appevents.Actions.listen('pure.more', 'done', function(){ pure.authors.H.init(); }, 'pure.authors.H.init');
                    pure.authors.H.More.initialized = true;
                }
            }
        }
    };
}());