(function () {
    if (typeof window.pure                              !== "object") { window.pure                                 = {}; }
    if (typeof window.pure.buddypress                   !== "object") { window.pure.buddypress                      = {}; }
    if (typeof window.pure.buddypress.groupadmin        !== "object") { window.pure.buddypress.groupadmin           = {}; }
    if (typeof window.pure.buddypress.groupadmin.manage !== "object") { window.pure.buddypress.groupadmin.manage    = {}; }
    "use strict";
    window.pure.buddypress.groupadmin.manage.A = {
        init        : function () {
            pure.buddypress.groupadmin.manage.A.initialize.     init();
            pure.buddypress.groupadmin.manage.A.admonitions.    init();
            pure.buddypress.groupadmin.manage.A.ban.            init();
            pure.buddypress.groupadmin.manage.A.role.           init();
            pure.buddypress.groupadmin.manage.A.remove.         init();
        },
        initialize  : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Manage.Container"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        (function(node){
                            function attachEvents(eventType, id){
                                var callers = pure.nodes.select.all('*[data-engine-groupmanage-' + eventType + '-caller="' + id + '"]:not([data-type-element-inited])');
                                if (callers !== null){
                                    for(var index = callers.length - 1; index >= 0; index -= 1){
                                        if (eventType === 'open'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.groupadmin.manage.A.render.show(id);
                                            });
                                        }
                                        if (eventType === 'close'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.groupadmin.manage.A.render.hide(id);
                                            });
                                        }
                                        callers[index].setAttribute('data-type-element-inited', 'true');
                                    }
                                }
                            };
                            var id      = node.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.buddypress.groupadmin.manage.A.render.hide          (id);
                                pure.buddypress.groupadmin.manage.A.render.orderOnTop    (id);
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
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Manage.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        document.body.appendChild(instances[index]);
                    }
                }
            },
            show        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Manage.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    pure.buddypress.groupadmin.manage.A.render.orderOnTop (id);
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = '';
                    }
                    pure.buddypress.groupadmin.manage.A.admonitions.reset(id);
                }
            },
            hide        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Manage.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = 'none';
                    }
                }
            }
        },
        admonitions : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-field-type="Group.Manage.Admonition.Send"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance){
                            var instance_id = instance.getAttribute('data-engine-element-id'),
                                nodes       = null,
                                callers     = null;
                            if (instance_id !== null){
                                nodes = {
                                    send        : instance,
                                    switcher    : pure.nodes.select.first('input[data-field-type="Group.Manage.Admonition.Switcher"][data-engine-element-id="' + instance_id + '"]'),
                                    name        : pure.nodes.select.first('*[data-field-type="Group.Manage.Admonition.MemberName"][data-engine-element-id="' + instance_id + '"]'),
                                    reason      : pure.nodes.select.first('textarea[data-field-type="Group.Manage.Admonition.Reason"][data-engine-element-id="' + instance_id + '"]')
                                };
                                if (pure.tools.objects.isValueIn(nodes, null) === false){
                                    callers = pure.nodes.select.all('*[data-field-type="Group.Manage.Admonition.Caller"][data-engine-element-id="' + instance_id + '"]');
                                    if (callers !== null){
                                        Array.prototype.forEach.call(
                                            callers,
                                            function(item, index, source){
                                                var data = {
                                                    member_id   : item.getAttribute('data-engine-data-memberID'     ),
                                                    member_name : item.getAttribute('data-engine-data-memberName'   )
                                                };
                                                if (pure.tools.objects.isValueIn(data, null) === false){
                                                    pure.events.add(
                                                        item,
                                                        'click',
                                                        function(){
                                                            pure.buddypress.groupadmin.manage.A.admonitions.open(instance_id, nodes, data);
                                                        }
                                                    );
                                                }
                                            }
                                        );
                                        pure.events.add(
                                            instance,
                                            'click',
                                            function(){
                                                pure.buddypress.groupadmin.manage.A.admonitions.request.send(instance_id, nodes);
                                            }
                                        );
                                    }
                                }
                            }
                        }(instances[index]));
                    }
                }
            },
            reset   : function(instance_id){
                var switcher = pure.nodes.select.first('input[data-field-type="Group.Manage.Admonition.Switcher"][data-engine-element-id="' + instance_id + '"]');
                if (switcher !== null){
                    switcher.checked = false;
                }
            },
            open    : function(instance_id, nodes, data){
                if (pure.buddypress.groupadmin.manage.A.progress.buttons.isBusy(instance_id, data.member_id, 'admonition') === false){
                    nodes.name.innerHTML    = data.member_name;
                    nodes.switcher.checked  = true;
                    nodes.reason.value      = '';
                    pure.buddypress.groupadmin.manage.A.admonitions.request.current[instance_id] = {
                        member_id      : data.member_id,
                        member_name    : data.member_name
                    };
                }
            },
            close   : function(instance_id, nodes){
                nodes.name.innerHTML    = '';
                nodes.switcher.checked  = false;
                nodes.reason.value      = '';
                pure.buddypress.groupadmin.manage.A.admonitions.request.current[instance_id] = null;
                delete pure.buddypress.groupadmin.manage.A.admonitions.request.current[instance_id];
            },
            counter : function(instance_id, member_id){
                var node    = pure.nodes.select.first('*[data-field-type="Group.Manage.Admonition.Counter"][data-engine-element-id="' + instance_id + '"][data-engine-data-memberID="' + member_id +'"]'),
                    count   = null;
                if (node !== null){
                    count           = parseInt(node.innerHTML, 10);
                    node.innerHTML  = (count + 1);
                }
            },
            request : {
                current     : {},
                send        : function(instance_id, nodes){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.groupadmin.manage.configuration.request.action'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.groupadmin.manage.configuration.destination'   ),
                        data        = pure.buddypress.groupadmin.manage.A.admonitions.request.current,
                        message     = pure.buddypress.groupadmin.manage.A.dialogs.info,
                        member_id   = null;
                    if (request !== null && destination !== null && typeof data[instance_id] !== 'undefined'){
                        if (nodes.reason.value.length < 5 || nodes.reason.value.length > 500){
                            message('Cannot do it', 'Description of the reason should be not less 5 symbols and not more 500.');
                            return false;
                        }
                        member_id   = data[instance_id].member_id;
                        request     = request.replace(/\[comment\]/, nodes.reason.value );
                        request     = request.replace(/\[action\]/, 'admonition'        );
                        request     = request.replace(/\[target_user\]/, member_id      );
                        //close dialog
                        pure.buddypress.groupadmin.manage.A.progress.buttons.busy(instance_id, data[instance_id].member_id, 'admonition');
                        pure.buddypress.groupadmin.manage.A.admonitions.close(instance_id, nodes);
                        //send request
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.groupadmin.manage.A.admonitions.request.onrecieve(id_request, response, instance_id, nodes, member_id);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.groupadmin.manage.A.admonitions.request.error(event, id_request, instance_id, nodes, member_id);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.buddypress.groupadmin.manage.A.admonitions.request.error(event, id_request, instance_id, nodes, member_id);
                            }
                        });
                    }
                },
                onrecieve   : function(id_request, response, instance_id, nodes, member_id){
                    var message = pure.buddypress.groupadmin.manage.A.dialogs.info;
                    switch (response){
                        case 'warned':
                            pure.buddypress.groupadmin.manage.A.admonitions.counter(instance_id, member_id);
                            break;
                        default :
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break;
                    }
                    pure.buddypress.groupadmin.manage.A.progress.buttons.free(instance_id, member_id, 'admonition');
                },
                error       : function(event, id_request, instance_id, nodes, member_id){
                    var message = pure.buddypress.groupadmin.manage.A.dialogs.info;
                    pure.buddypress.groupadmin.manage.A.progress.buttons.free(instance_id, member_id, 'admonition');
                    message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                }
            }
        },
        ban         : {
            init    : function(){
                var callers = pure.nodes.select.all('*[data-field-type="Group.Manage.Ban.Button"]:not([data-type-element-inited])');
                if (callers !== null) {
                    Array.prototype.forEach.call(
                        callers,
                        function(item, index, source){
                            var data = {
                                member_id   : item.getAttribute('data-engine-data-memberID'     ),
                                member_name : item.getAttribute('data-engine-data-memberName'   ),
                                instance_id : item.getAttribute('data-engine-element-id'        )
                            };
                            if (pure.tools.objects.isValueIn(data, null) === false){
                                pure.events.add(
                                    item,
                                    'click',
                                    function(){
                                        pure.buddypress.groupadmin.manage.A.ban.onClick(data, item);
                                    }
                                );
                            }
                        }
                    );
                }
            },
            onClick : function(data, button){
                var state   = button.getAttribute('data-engine-state'),
                    title   = '';
                if (state !== null){
                    if (pure.buddypress.groupadmin.manage.A.progress.buttons.isBusy(data.instance_id, data.member_id, 'ban') === false){
                        if (state !== 'active'){
                            title   = 'ban';
                        }else{
                            title   = 'unban';
                        }
                        pure.buddypress.groupadmin.manage.A.dialogs.info(
                            'Confirm operation',
                            'Do you really want <strong>' + title + '</strong> ' + data.member_name + '?',
                            [
                                {
                                    title       : 'CANCEL',
                                    handle      : null,
                                    closeAfter  : true
                                },
                                {
                                    title       : title.toUpperCase(),
                                    handle      : function(){
                                        pure.buddypress.groupadmin.manage.A.ban.request.send(data);
                                    },
                                    closeAfter  : true
                                }
                            ]
                        );
                    }
                }
            },
            toggle  : function(data){
                var button  = pure.nodes.select.first('*[data-field-type="Group.Manage.Ban.Button"][data-engine-data-memberID="' + data.member_id + '"][data-engine-element-id="' + data.instance_id + '"]'),
                    state   = null;
                if (button !== null){
                    state = button.getAttribute('data-engine-state');
                    if (state !== null){
                        if (state === 'active'){
                            button.setAttribute('data-engine-state', ''         );
                        }else{
                            button.setAttribute('data-engine-state', 'active'   );
                        }
                    }
                }
            },
            request : {
                send        : function(data){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.groupadmin.manage.configuration.request.action'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.groupadmin.manage.configuration.destination'   );
                    if (request !== null && destination !== null){
                        request     = request.replace(/\[comment\]/, ''                 );
                        request     = request.replace(/\[action\]/, 'ban'               );
                        request     = request.replace(/\[target_user\]/, data.member_id );
                        pure.buddypress.groupadmin.manage.A.progress.buttons.busy(data.instance_id, data.member_id, 'ban');
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.groupadmin.manage.A.ban.request.onrecieve(id_request, response, data);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.groupadmin.manage.A.ban.request.error(event, id_request, data);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.buddypress.groupadmin.manage.A.ban.request.error(event, id_request, data);
                            }
                        });
                    }
                },
                onrecieve   : function(id_request, response, data){
                    var message = pure.buddypress.groupadmin.manage.A.dialogs.info;
                    switch (response){
                        case 'banned':
                            message('Success', data.member_name + ' was banned.');
                            pure.buddypress.groupadmin.manage.A.ban.toggle(data);
                            break;
                        case 'unbanned':
                            message('Success', data.member_name + ' was unbanned.');
                            pure.buddypress.groupadmin.manage.A.ban.toggle(data);
                            break;
                        case 'moderator_cannot_be_banned':
                            message('Fail operation', 'Sorry, but you cannot ban moderator.');
                            break;
                        case 'admin_cannot_be_banned':
                            message('Fail operation', 'Sorry, but you cannot ban administrator.');
                            break;
                        default :
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break;
                    }
                    pure.buddypress.groupadmin.manage.A.progress.buttons.free(data.instance_id, data.member_id, 'ban');
                },
                error       : function(event, id_request, data){
                    var message = pure.buddypress.groupadmin.manage.A.dialogs.info;
                    pure.buddypress.groupadmin.manage.A.progress.buttons.free(data.instance_id, data.member_id, 'ban');
                    message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                }
            }
        },
        role        : {
            init    : function(){
                function attachEvents(nodes, type){
                    if (nodes !== null) {
                        Array.prototype.forEach.call(
                            nodes,
                            function(item, index, source){
                                var data = {
                                    member_id   : item.getAttribute('data-engine-data-memberID'     ),
                                    member_name : item.getAttribute('data-engine-data-memberName'   ),
                                    instance_id : item.getAttribute('data-engine-element-id'        )
                                };
                                if (pure.tools.objects.isValueIn(data, null) === false){
                                    pure.events.add(
                                        item,
                                        'click',
                                        function(){
                                            pure.buddypress.groupadmin.manage.A.role.onClick(data, item, type);
                                        }
                                    );
                                }
                            }
                        );
                    }
                };
                var callers =  {
                    admin   : pure.nodes.select.all('*[data-field-type="Group.Manage.Admin.Button"]:not([data-type-element-inited])'),
                    mod     : pure.nodes.select.all('*[data-field-type="Group.Manage.Mod.Button"]:not([data-type-element-inited])')
                };
                attachEvents(callers.admin, 'admin' );
                attachEvents(callers.mod,   'mod'   );
            },
            onClick : function(data, button, type){
                var state   = button.getAttribute('data-engine-state'),
                    title   = '';
                if (state !== null){
                    if (pure.buddypress.groupadmin.manage.A.progress.buttons.isBusy(data.instance_id, data.member_id, 'ban') === false){
                        if (state !== 'active'){
                            title   = 'apply role';
                        }else{
                            title   = 'cancel role';
                        }
                        pure.buddypress.groupadmin.manage.A.dialogs.info(
                            'Confirm operation',
                            'Do you really want <strong>' + title + ' ' + (type === 'admin' ? 'administrator' : 'moderator') + '</strong> for ' + data.member_name + '?',
                            [
                                {
                                    title       : 'CANCEL',
                                    handle      : null,
                                    closeAfter  : true
                                },
                                {
                                    title       : title.toUpperCase(),
                                    handle      : function(){
                                        pure.buddypress.groupadmin.manage.A.role.request.send(data, type);
                                    },
                                    closeAfter  : true
                                }
                            ]
                        );
                    }
                }
            },
            toggle  : function(data, type){
                var button  = pure.nodes.select.first('*[data-field-type="Group.Manage.' + type + '.Button"][data-engine-data-memberID="' + data.member_id + '"][data-engine-element-id="' + data.instance_id + '"]'),
                    state   = null;
                if (button !== null){
                    state = button.getAttribute('data-engine-state');
                    if (state !== null){
                        if (state === 'active'){
                            button.setAttribute('data-engine-state', ''         );
                        }else{
                            button.setAttribute('data-engine-state', 'active'   );
                        }
                    }
                }
            },
            request : {
                send        : function(data, type){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.groupadmin.manage.configuration.request.action'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.groupadmin.manage.configuration.destination'   );
                    if (request !== null && destination !== null){
                        request     = request.replace(/\[comment\]/, ''                 );
                        request     = request.replace(/\[action\]/, type                );
                        request     = request.replace(/\[target_user\]/, data.member_id );
                        pure.buddypress.groupadmin.manage.A.progress.buttons.busy(data.instance_id, data.member_id, type);
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.groupadmin.manage.A.role.request.onrecieve(id_request, response, data, type);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.groupadmin.manage.A.role.request.error(event, id_request, data, type);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.buddypress.groupadmin.manage.A.role.request.error(event, id_request, data, type);
                            }
                        });
                    }
                },
                onrecieve   : function(id_request, response, data, type){
                    var message = pure.buddypress.groupadmin.manage.A.dialogs.info;
                    switch (response){
                        case 'mod_removed':
                            message('Success', data.member_name + ' is not moderator of group now.');
                            pure.buddypress.groupadmin.manage.A.role.toggle(data, 'Mod');
                            break;
                        case 'mod_accepted':
                            message('Success', data.member_name + ' is moderator of group now.');
                            pure.buddypress.groupadmin.manage.A.role.toggle(data, 'Mod');
                            break;
                        case 'banned_user_cannot_be_moderator':
                            message('Fail operation', data.member_name + ' was banned. He cannot be moderator.');
                            break;
                        case 'admin_removed':
                            message('Success', data.member_name + ' is not administrator of group now.');
                            pure.buddypress.groupadmin.manage.A.role.toggle(data, 'Admin');
                            break;
                        case 'admin_accepted':
                            message('Success', data.member_name + ' is administrator of group now.');
                            pure.buddypress.groupadmin.manage.A.role.toggle(data, 'Admin');
                            break;
                        case 'banned_user_cannot_be_admin':
                            message('Fail operation', data.member_name + ' was banned. He cannot be administrator.');
                            break;
                        case 'admin_cannot_remove_admin_rights_of_himself':
                            message('Fail operation', 'Administrator cannot remove administator rights of himself.');
                            break;
                        default :
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break;
                    }
                    pure.buddypress.groupadmin.manage.A.progress.buttons.free(data.instance_id, data.member_id, type);
                },
                error       : function(event, id_request, data, type){
                    var message = pure.buddypress.groupadmin.manage.A.dialogs.info;
                    pure.buddypress.groupadmin.manage.A.progress.buttons.free(data.instance_id, data.member_id, type);
                    message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                }
            }
        },
        remove      : {
            init    : function(){
                var callers = pure.nodes.select.all('*[data-field-type="Group.Manage.Remove.Button"]:not([data-type-element-inited])');
                if (callers !== null) {
                    Array.prototype.forEach.call(
                        callers,
                        function(item, index, source){
                            var data = {
                                member_id   : item.getAttribute('data-engine-data-memberID'     ),
                                member_name : item.getAttribute('data-engine-data-memberName'   ),
                                instance_id : item.getAttribute('data-engine-element-id'        )
                            };
                            if (pure.tools.objects.isValueIn(data, null) === false){
                                pure.events.add(
                                    item,
                                    'click',
                                    function(){
                                        pure.buddypress.groupadmin.manage.A.remove.onClick(data, item);
                                    }
                                );
                            }
                        }
                    );
                }
            },
            onClick : function(data, button){
                if (pure.buddypress.groupadmin.manage.A.progress.buttons.isBusy(data.instance_id, data.member_id, 'remove') === false){
                    pure.buddypress.groupadmin.manage.A.dialogs.info(
                        'Confirm operation',
                        'Do you really want <strong>remove</strong> ' + data.member_name + '?',
                        [
                            {
                                title       : 'CANCEL',
                                handle      : null,
                                closeAfter  : true
                            },
                            {
                                title       : 'REMOVE',
                                handle      : function(){
                                    pure.buddypress.groupadmin.manage.A.remove.request.send(data);
                                },
                                closeAfter  : true
                            }
                        ]
                    );
                }
            },
            remove  : function(data){
                var row = pure.nodes.select.first('*[data-field-type="Group.Manage.Row"][data-engine-element-id="' + data.instance_id + '"][data-engine-data-memberID="' + data.member_id + '"]');
                if (row !== null){
                    if (typeof row.parentNode !== 'undefined'){
                        if (typeof row.parentNode.removeChild === 'function'){
                            row.parentNode.removeChild(row);
                        }
                    }
                }
            },
            request : {
                send        : function(data){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.groupadmin.manage.configuration.request.action'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.groupadmin.manage.configuration.destination'   );
                    if (request !== null && destination !== null){
                        request     = request.replace(/\[comment\]/, ''                 );
                        request     = request.replace(/\[action\]/, 'remove'            );
                        request     = request.replace(/\[target_user\]/, data.member_id );
                        pure.buddypress.groupadmin.manage.A.progress.buttons.busy(data.instance_id, data.member_id, 'remove');
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.groupadmin.manage.A.remove.request.onrecieve(id_request, response, data);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.groupadmin.manage.A.remove.request.error(event, id_request, data);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.buddypress.groupadmin.manage.A.remove.request.error(event, id_request, data);
                            }
                        });
                    }
                },
                onrecieve   : function(id_request, response, data){
                    var message = pure.buddypress.groupadmin.manage.A.dialogs.info;
                    switch (response){
                        case 'removed':
                            message('Success', data.member_name + ' was removed.');
                            pure.buddypress.groupadmin.manage.A.remove.remove(data);
                            break;
                        case 'admin_cannot_remove_himself':
                            message('Fail operation', 'You cannot remove yourself.');
                            break;
                        default :
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break;
                    }
                    pure.buddypress.groupadmin.manage.A.progress.buttons.free(data.instance_id, data.member_id, 'remove');
                },
                error       : function(event, id_request, data){
                    var message = pure.buddypress.groupadmin.manage.A.dialogs.info;
                    pure.buddypress.groupadmin.manage.A.progress.buttons.free(data.instance_id, data.member_id, 'remove');
                    message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                }
            }
        },
        progress    : {
            buttons     : {
                data    : {},
                isBusy  : function(instance_id, member_id, selector){
                    var data    = pure.buddypress.groupadmin.manage.A.progress.buttons.data,
                        index   = (instance_id + '_' + member_id + '_' + selector);
                    return (typeof data[index] !== 'undefined' ? true : false);
                },
                busy    : function(instance_id, member_id, selector){
                    var data        = pure.buddypress.groupadmin.manage.A.progress.buttons.data,
                        instance    = pure.nodes.select.first('*[data-engine-action="' + selector + '"][data-engine-element-id="' + instance_id + '"][data-engine-data-memberID="' + member_id + '"]'),
                        index       = (instance_id + '_' + member_id + '_' + selector);
                    if (instance !== null){
                        data[index] = pure.templates.progressbar.A.show(instance);
                    }
                },
                free    : function(instance_id, member_id, selector){
                    var data    = pure.buddypress.groupadmin.manage.A.progress.buttons.data,
                        index   = (instance_id + '_' + member_id + '_' + selector);
                    if (typeof data[index] !== 'undefined'){
                        pure.templates.progressbar.B.hide(data[index]);
                        data[index] = null;
                        delete data[index];
                    }
                }
            },
            global    : {
                data    : {},
                isBusy  : function(instance_id){
                    var data = pure.buddypress.groupadmin.manage.A.progress.global.data;
                    return (typeof data[instance_id] !== 'undefined' ? true : false);
                },
                busy    : function(instance_id){
                    var data        = pure.buddypress.groupadmin.manage.A.progress.global.data,
                        instance    = pure.nodes.select.first('*[data-engine-element="Group.Manage.Page"][data-engine-element-id="' + instance_id + '"]');
                    if (instance !== null){
                        data[instance_id] = pure.templates.progressbar.A.show(instance, "background: -moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%, rgba(255,255,255,1) 5%, rgba(255,255,255,1) 8%, rgba(255,255,255,0) 100%); background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,1)), color-stop(5%,rgba(255,255,255,1)), color-stop(8%,rgba(255,255,255,1)), color-stop(100%,rgba(255,255,255,0))); background: -webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -o-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: radial-gradient(ellipse at center,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#00ffffff',GradientType=1 );z-index:10000;");
                    }
                },
                free    : function(instance_id){
                    var data = pure.buddypress.groupadmin.manage.A.progress.global.data;
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
    pure.system.start.add(pure.buddypress.groupadmin.manage.A.init);
}());