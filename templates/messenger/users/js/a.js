(function () {
    if (typeof window.pure                      !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components           !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.messenger !== "object") { window.pure.components.messenger    = {}; }
    "use strict";
    window.pure.components.messenger.users = {
        loader      : {
            isPossible      : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.user_id'                   ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.user_avatar'               ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.user_name'                 ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requestURL'                ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.users.friends'    ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.users.groups'     ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.users.recipients' ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.users.talks'      ) === null ? false : true));
                return result;
            },
            data : {
                friends     : {
                    members : null,
                    shown   : -1,
                    total   : -1,
                    ready   : false
                },
                groups      : {
                    groups  : null,
                    shown   : -1,
                    total   : -1,
                    ready   : false
                },
                recipients  : {
                    members : null,
                    shown   : -1,
                    total   : -1,
                    ready   : false
                },
                talks       : {
                    members : null,
                    shown   : -1,
                    total   : -1,
                    ready   : false,
                    reset   : function(){
                        var storage = pure.components.messenger.users.loader.data.talks;
                        storage.shown   = -1;
                        storage.total   = -1;
                        storage.members = null;
                        storage.ready   = false;
                    }
                }
            },
            get : {
                friends     : function(){
                    var storage = pure.components.messenger.users.loader.data.friends,
                        request = pure.components.messenger.configuration.requests.users.friends;
                    if (pure.components.messenger.users.loader.isPossible() !== false){
                        if ((storage.shown === -1 && storage.total === -1) || storage.shown < storage.total){
                            storage.shown = (storage.shown === -1 ? 0 : storage.shown);
                            storage.total = (storage.total === -1 ? 0 : storage.total);
                            request = request.replace(/\[shown\]/,      storage.shown   );
                            request = request.replace(/\[maxcount\]/,   50              );
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : pure.components.messenger.configuration.requestURL,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.components.messenger.users.loader.get.events.friends.onRecieve(id_request, response);
                                },
                                onreaction  : null,
                                onerror     : function (id_request) {
                                    pure.components.messenger.users.loader.get.events.friends.onError(event, id_request);
                                },
                                ontimeout   : function (id_request) {
                                    pure.components.messenger.users.loader.get.events.friends.onError(event, id_request);
                                }
                            });
                        }
                    }
                },
                groups      : function(){
                    var storage = pure.components.messenger.users.loader.data.groups,
                        request = pure.components.messenger.configuration.requests.users.groups;
                    if (pure.components.messenger.users.loader.isPossible() !== false){
                        if ((storage.shown === -1 && storage.total === -1) || storage.shown < storage.total){
                            storage.shown = (storage.shown === -1 ? 0 : storage.shown);
                            storage.total = (storage.total === -1 ? 0 : storage.total);
                            request = request.replace(/\[shown\]/,      storage.shown   );
                            request = request.replace(/\[maxcount\]/,   50              );
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : pure.components.messenger.configuration.requestURL,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.components.messenger.users.loader.get.events.groups.onRecieve(id_request, response);
                                },
                                onreaction  : null,
                                onerror     : function (id_request) {
                                    pure.components.messenger.users.loader.get.events.groups.onError(event, id_request);
                                },
                                ontimeout   : function (id_request) {
                                    pure.components.messenger.users.loader.get.events.groups.onError(event, id_request);
                                }
                            });
                        }
                    }
                },
                recipients  : function(){
                    var storage = pure.components.messenger.users.loader.data.recipients,
                        request = pure.components.messenger.configuration.requests.users.recipients;
                    if (pure.components.messenger.users.loader.isPossible() !== false){
                        if ((storage.shown === -1 && storage.total === -1) || storage.shown < storage.total){
                            storage.shown = (storage.shown === -1 ? 0 : storage.shown);
                            storage.total = (storage.total === -1 ? 0 : storage.total);
                            request = request.replace(/\[shown\]/,      storage.shown   );
                            request = request.replace(/\[maxcount\]/,   50              );
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : pure.components.messenger.configuration.requestURL,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.components.messenger.users.loader.get.events.recipients.onRecieve(id_request, response);
                                },
                                onreaction  : null,
                                onerror     : function (id_request) {
                                    pure.components.messenger.users.loader.get.events.recipients.onError(event, id_request);
                                },
                                ontimeout   : function (id_request) {
                                    pure.components.messenger.users.loader.get.events.recipients.onError(event, id_request);
                                }
                            });
                        }
                    }
                },
                talks       : function(update){
                    var storage = pure.components.messenger.users.loader.data.talks,
                        request = pure.components.messenger.configuration.requests.users.talks,
                        update  = (typeof update === 'boolean' ? update : false);
                    if (pure.components.messenger.users.loader.isPossible() !== false){
                        if (update === true){
                            storage.reset();
                        }
                        if ((storage.shown === -1 && storage.total === -1) || storage.shown < storage.total){
                            storage.shown = (storage.shown === -1 ? 0 : storage.shown);
                            storage.total = (storage.total === -1 ? 0 : storage.total);
                            request = request.replace(/\[shown\]/,      storage.shown   );
                            request = request.replace(/\[maxcount\]/,   50              );
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : pure.components.messenger.configuration.requestURL,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.components.messenger.users.loader.get.events.talks.onRecieve(id_request, response);
                                },
                                onreaction  : null,
                                onerror     : function (id_request) {
                                    pure.components.messenger.users.loader.get.events.talks.onError(event, id_request);
                                },
                                ontimeout   : function (id_request) {
                                    pure.components.messenger.users.loader.get.events.talks.onError(event, id_request);
                                }
                            });
                        }
                    }
                },
                events      : {
                    friends     : {
                        onRecieve   : function(id_request, response){
                            var storage = pure.components.messenger.users.loader.data.friends,
                                data    = null;
                            if (response !== 'no access'){
                                try{
                                    data = JSON.parse(response);
                                    if (typeof data.members     !== 'undefined' &&
                                        typeof data.shown       !== 'undefined' &&
                                        typeof data.total       !== 'undefined'){
                                        if (data.members instanceof Array){
                                            data.shown      = parseInt(data.shown);
                                            data.total      = parseInt(data.total);
                                            storage.shown   +=    data.shown;
                                            storage.total   =     data.total;
                                            storage.members = (storage.members === null ? [] : storage.members);
                                            storage.members = storage.members.concat(data.members);
                                            if (storage.shown < storage.total){
                                                pure.components.messenger.users.loader.get.friends();
                                            }else{
                                                pure.components.messenger.users.fill.friends.load(storage.members);
                                                storage.ready = true;
                                            }
                                            return true;
                                        }
                                    }
                                }catch (e){
                                }
                            }
                            return false;
                        },
                        onError     : function(event, id_request){
                            //Repeat attempt
                            setTimeout(pure.components.messenger.users.loader.get.friends, 5000);
                        }
                    },
                    groups      : {
                        onRecieve   : function(id_request, response){
                            var storage = pure.components.messenger.users.loader.data.groups,
                                data    = null;
                            if (response !== 'no access'){
                                try{
                                    data = JSON.parse(response);
                                    if (typeof data.groups      !== 'undefined' &&
                                        typeof data.shown       !== 'undefined' &&
                                        typeof data.total       !== 'undefined'){
                                        if (data.groups instanceof Array){
                                            data.shown      = parseInt(data.shown);
                                            data.total      = parseInt(data.total);
                                            storage.shown   +=    data.shown;
                                            storage.total   =     data.total;
                                            storage.groups  = (storage.groups === null ? [] : storage.groups);
                                            storage.groups  = storage.groups.concat(data.groups);
                                            if (storage.shown < storage.total){
                                                pure.components.messenger.users.loader.get.groups();
                                            }else{
                                                pure.components.messenger.users.fill.groups.load(storage.groups);
                                                storage.ready = true;
                                            }
                                            return true;
                                        }
                                    }
                                }catch (e){
                                }
                            }
                            return false;
                        },
                        onError     : function(event, id_request){
                            //Repeat attempt
                            setTimeout(pure.components.messenger.users.loader.get.groups, 5000);
                        }
                    },
                    recipients  : {
                        onRecieve   : function(id_request, response){
                            var storage = pure.components.messenger.users.loader.data.recipients,
                                data    = null;
                            if (response !== 'no access'){
                                try{
                                    data = JSON.parse(response);
                                    if (typeof data.members     !== 'undefined' &&
                                        typeof data.shown       !== 'undefined' &&
                                        typeof data.total       !== 'undefined'){
                                        if (data.members instanceof Array){
                                            data.shown      = parseInt(data.shown);
                                            data.total      = parseInt(data.total);
                                            storage.shown   +=    data.shown;
                                            storage.total   =     data.total;
                                            storage.members = (storage.members === null ? [] : storage.members);
                                            storage.members = storage.members.concat(data.members);
                                            if (storage.shown < storage.total){
                                                pure.components.messenger.users.loader.get.recipients();
                                            }else{
                                                pure.components.messenger.users.fill.last.load(storage.members);
                                                storage.ready = true;
                                            }
                                            return true;
                                        }
                                    }
                                }catch (e){
                                }
                            }
                            return false;
                        },
                        onError     : function(event, id_request){
                            //Repeat attempt
                            setTimeout(pure.components.messenger.users.loader.get.recipients, 5000);
                        }
                    },
                    talks       : {
                        onRecieve   : function(id_request, response){
                            var storage = pure.components.messenger.users.loader.data.talks,
                                data    = null;
                            if (response !== 'no access'){
                                try{
                                    data = JSON.parse(response);
                                    if (typeof data.members     !== 'undefined' &&
                                        typeof data.shown       !== 'undefined' &&
                                        typeof data.total       !== 'undefined'){
                                        if (data.members instanceof Array){
                                            data.shown      = parseInt(data.shown);
                                            data.total      = parseInt(data.total);
                                            storage.shown   +=    data.shown;
                                            storage.total   =     data.total;
                                            storage.members = (storage.members === null ? [] : storage.members);
                                            storage.members = storage.members.concat(data.members);
                                            if (storage.shown < storage.total){
                                                pure.components.messenger.users.loader.get.talks();
                                            }else{
                                                //pure.components.messenger.users.fill.last.load(storage.members);
                                                storage.ready = true;
                                            }
                                            return true;
                                        }
                                    }
                                }catch (e){
                                }
                            }
                            return false;
                        },
                        onError     : function(event, id_request){
                            //Repeat attempt
                            setTimeout(pure.components.messenger.users.loader.get.talks, 5000);
                        }
                    }
                }
            }
        },
        buttons     : {
            storage : {},
            find    : function(){
                var buttons = pure.components.messenger.users.buttons.storage;
                buttons.select = pure.nodes.select.first('*[data-messenger-users-engine-element="select"]');
                buttons.cancel = pure.nodes.select.first('*[data-messenger-users-engine-element="cancel"]');
            },
            attach  : function(){
                var buttons = pure.components.messenger.users.buttons.storage;
                if (pure.tools.objects.isValueIn(buttons, null) === false){
                    pure.events.add(
                        buttons.select,
                        'click',
                        pure.components.messenger.users.select.ready
                    );
                    pure.events.add(
                        buttons.cancel,
                        'click',
                        function(event){
                            pure.components.messenger.users.windows.close();
                            pure.components.messenger.users.select. close();
                        }
                    );
                }
            },
            init    : function(){
                pure.components.messenger.users.buttons.find();
                pure.components.messenger.users.buttons.attach();
            }
        },
        tabs        : {
            storage : {},
            find    : function(){
                var tabs = pure.components.messenger.users.tabs.storage;
                tabs.containers = {
                    last    : pure.nodes.select.first('*[data-messenger-users-engine-element="tab.last"]'   ),
                    friends : pure.nodes.select.first('*[data-messenger-users-engine-element="tab.friends"]'),
                    groups  : pure.nodes.select.first('*[data-messenger-users-engine-element="tab.groups"]' )
                };
                tabs.switchers = {
                    last    : pure.nodes.select.first('*[data-messenger-users-engine-element="tab.switcher.last"]'   ),
                    friends : pure.nodes.select.first('*[data-messenger-users-engine-element="tab.switcher.friends"]'),
                    groups  : pure.nodes.select.first('*[data-messenger-users-engine-element="tab.switcher.groups"]' )
                };
            },
            get     : function(tab){
                var tabs = pure.components.messenger.users.tabs.storage;
                if (typeof tabs.containers === 'object'){
                    return (typeof tabs.containers[tab] !== 'undefined' ? tabs.containers[tab] : null);
                }
                return null;
            },
            current : function(){
                var tabs = pure.components.messenger.users.tabs.storage;
                if (typeof tabs.containers === 'object'){
                    if (tabs.switchers.last.checked === true){
                        return 'last';
                    }
                    if (tabs.switchers.friends.checked === true){
                        return 'friends';
                    }
                    if (tabs.switchers.groups.checked === true){
                        return 'groups';
                    }
                }
                return null;
            },
            init    : function(){
                pure.components.messenger.users.tabs.find();
            }
        },
        cache       : {
            last    : [],
            friends : [],
            groups  : [],
            add     : function(tab, name, avatar, id, switcher, container){
                var storage = pure.components.messenger.users.cache;
                if (typeof storage[tab] !== 'undefined'){
                    storage[tab].push({
                        name        : name,
                        avatar      : avatar,
                        id          : id,
                        switcher    : switcher,
                        container   : container
                    });
                }
            },
            get     : function(tab){
                var storage = pure.components.messenger.users.cache,
                    users   = [];
                if (typeof storage[tab] !== 'undefined'){
                    for(var index = storage[tab].length - 1; index >= 0; index -= 1){
                        if (storage[tab][index].switcher.checked === true){
                            users.push({
                                name        : storage[tab][index].name,
                                avatar      : storage[tab][index].avatar,
                                id          : storage[tab][index].id
                            });
                        }
                    }
                }
                return users;
            },
            reset   : function(tab){
                var storage = pure.components.messenger.users.cache;
                if (typeof storage[tab] !== 'undefined'){
                    for(var index = storage[tab].length - 1; index >= 0; index -= 1){
                        if (storage[tab][index].switcher.checked === true){
                            storage[tab][index].switcher.checked = false;
                        }
                    }
                }
            },
            select : function(tab, users){
                var storage = pure.components.messenger.users.cache;
                if (typeof storage[tab] !== 'undefined'){
                    for(var index = storage[tab].length - 1; index >= 0; index -= 1){
                        for(var _index = users.length - 1; _index >= 0; _index -= 1){
                            if (storage[tab][index].id === users[_index].id){
                                storage[tab][index].switcher.checked = true;
                            }
                        }
                    }
                }
            }
        },
        template    : {
            storage : null,
            init    : function(){
                var template = pure.nodes.select.first('*[data-messenger-users-engine-element="user.template"]');
                if (template !== null){
                    pure.components.messenger.users.template.storage = {
                        nodeName    : template.nodeName,
                        innerHTML   : template.innerHTML,
                        attributes  : pure.nodes.attributes.get(template, ['data-messenger-users-engine-element'])
                    };
                    template.parentNode.removeChild(template);
                }
            },
            add     : function(user, tab){
                var storage = pure.components.messenger.users.template.storage,
                    node    = null,
                    nodes   = null,
                    _tab    = pure.components.messenger.users.tabs.get(tab);
                if (storage !== null && tab !== null){
                    node            = document.createElement(storage.nodeName);
                    node.innerHTML  = storage.innerHTML;
                    pure.nodes.attributes.set(node, storage.attributes);
                    node.setAttribute('data-users-engine-user-id', user.id);
                    _tab.appendChild(node);
                    nodes  = {
                        name        : pure.nodes.select.first('*[data-messenger-users-engine-element="tab.' + tab + '"] *[data-users-engine-user-id="' + user.id +'"] *[data-messenger-users-engine-element="user.template.name"]'      ),
                        avatar      : pure.nodes.select.first('*[data-messenger-users-engine-element="tab.' + tab + '"] *[data-users-engine-user-id="' + user.id +'"] *[data-messenger-users-engine-element="user.template.avatar"]'    ),
                        switcher    : pure.nodes.select.first('*[data-messenger-users-engine-element="tab.' + tab + '"] *[data-users-engine-user-id="' + user.id +'"] *[data-messenger-users-engine-element="user.template.switcher"]'  )
                    };
                    if (pure.tools.objects.isValueIn(nodes, null) === false){
                        nodes.name.innerHTML                = user.name.replace(/\s/, '<br />');
                        nodes.avatar.style.backgroundImage  = 'url(' + user.avatar + ')';
                        pure.components.messenger.users.cache.add(tab, user.name, user.avatar, user.id, nodes.switcher, node);
                        return true;
                    }
                    _tab.removeChild(node);
                    return false;
                }
            }
        },
        fill        : {
            last : {
                load        : function(users){
                    for(var index = 0, max_index = users.length; index < max_index; index += 1){
                        pure.components.messenger.users.template.add(users[index], 'last');
                    }
                }
            },
            friends : {
                load        : function(users){
                    for(var index = 0, max_index = users.length; index < max_index; index += 1){
                        pure.components.messenger.users.template.add(users[index], 'friends');
                    }
                }
            },
            groups : {
                load        : function(groups){
                    function isIn(cache, id){
                        for (var index = cache.length - 1; index >= 0; index -= 1){
                            if (cache[index].id === id){
                                return true;
                            }
                        }
                        return false;
                    }
                    var cache = [{id:pure.components.messenger.configuration.user_id}];
                    for(var index = 0, max_index = groups.length; index < max_index; index += 1){
                        for(var _index = 0, _max_index = groups[index].members.members.length; _index < _max_index; _index += 1){
                            if (isIn(cache, groups[index].members.members[_index].id) === false){
                                cache.push(groups[index].members.members[_index]);
                                pure.components.messenger.users.template.add(groups[index].members.members[_index], 'groups');
                            }
                        }
                    }
                }
            }
        },
        filter      : {
            storage : null,
            init    : function(){
                var filter = pure.nodes.select.first('input[data-messenger-users-engine-element="filter"]');
                if (filter !== null){
                    pure.events.add(
                        filter,
                        'keyup',
                        function(event){
                            pure.components.messenger.users.filter.change(filter);
                        }
                    );
                }
            },
            change : function(input){
                var tab     = pure.components.messenger.users.tabs.current(),
                    cache   = pure.components.messenger.users.cache,
                    text    = input.value;
                if (tab !== null){
                    cache = cache[tab];
                    for(var index = cache.length - 1; index >= 0; index -= 1){
                        if (cache[index].name.toLowerCase().indexOf(text.toLowerCase()) === -1){
                            cache[index].container.style.display = 'none';
                        }else{
                            cache[index].container.style.display = '';
                        }
                    }
                }
            }
        },
        windows     : {
            storage : {},
            find    : function(){
                var nodes = pure.components.messenger.users.windows.storage;
                nodes.container = pure.nodes.select.first('*[data-messenger-users-engine-element="window"]');
            },
            close   : function(){
                var nodes = pure.components.messenger.users.windows.storage;
                if (typeof nodes.container.style !== 'undefined'){
                    nodes.container.style.display = 'none';
                    pure.components.messenger.users.select.close();
                }
            },
            open    : function(){
                var nodes = pure.components.messenger.users.windows.storage;
                if (typeof nodes.container.style !== 'undefined'){
                    pure.components.messenger.users.cache.reset('last'      );
                    pure.components.messenger.users.cache.reset('friends'   );
                    pure.components.messenger.users.cache.reset('groups'    );
                    nodes.container.style.display = '';
                }
            },
            init    : function(){
                pure.components.messenger.users.windows.find();
                pure.components.messenger.users.windows.close();
            }
        },
        select      : {
            current : {
                handle : null
            },
            select      : function(handle, selected){
                pure.components.messenger.users.select.current.handle = handle;
                pure.components.messenger.users.windows.open();
                pure.components.messenger.users.cache.select(
                    pure.components.messenger.users.tabs.current(),
                    selected
                );
            },
            ready       : function(){
                if (typeof pure.components.messenger.users.select.current.handle === 'function'){
                    pure.components.messenger.users.select.current.handle(
                        pure.components.messenger.users.cache.get(
                            pure.components.messenger.users.tabs.current()
                        )
                    );
                }
                pure.components.messenger.users.windows.close();
            },
            close       : function(){
                pure.components.messenger.users.select.current.handle = null;
            }
        },
        get         : function(target, handle, update){
            function check(target, handle){
                if (pure.components.messenger.users.loader.data.friends.    ready === false ||
                    pure.components.messenger.users.loader.data.groups.     ready === false ||
                    pure.components.messenger.users.loader.data.recipients. ready === false ||
                    pure.components.messenger.users.loader.data.talks.      ready === false){
                    if (handle !== null){
                        setTimeout(
                            function(){
                                pure.components.messenger.users.get(target, handle);
                            },
                            50
                        );
                    }
                    return false;
                }
                return true;
            };
            function give(target, handle){
                var storage     = pure.components.messenger.users.loader.data[target],
                    property    = (typeof storage.members !== 'undefined' ? 'members' : 'groups');
                if (handle !== null){
                    pure.system.runHandle(
                        handle,
                        {items : pure.tools.arrays.copy(storage[property])},
                        'pure.components.messenger.users.get',
                        this
                    );
                }else{
                    return pure.tools.arrays.copy(storage[property]);
                }
            };
            var update = (typeof update === 'boolean' ? update : false);
            if (typeof pure.components.messenger.users.loader.data[target] !== 'undefined'){
                if (update === true){
                    pure.components.messenger.users.loader.get[target](true);
                }
                if (check(target, handle) !== false){
                    return give(target, handle);
                }
            }
            return null
        },
        getUser     : function(userID){
            function searchNotInGroups(type, userID){
                var members = pure.components.messenger.users.loader.data[type].members;
                for (var index = members.length - 1; index >= 0; index -= 1){
                    if (members[index].id == userID){
                        return pure.tools.objects.copy(null, members[index]);
                    }
                }
                return false;
            }
            function searchInGroups(userID){
                var groups  = pure.components.messenger.users.loader.data.groups.groups,
                    members = null;
                for (var index = groups.length - 1; index >= 0; index -= 1){
                    members = groups[index].members.members;
                    for (var _index = members.length - 1; _index >= 0; _index -= 1){
                        if (members[_index].id == userID){
                            return pure.tools.objects.copy(null, members[_index]);
                        }
                    }
                }
                return false;
            }
            var user = false;
            if (pure.components.messenger.configuration.user_id == userID){
                return {
                    id      : pure.components.messenger.configuration.user_id,
                    name    : pure.components.messenger.configuration.user_name,
                    avatar  : pure.components.messenger.configuration.user_avatar
                }
            }
            user = (user === false ? searchInGroups(userID)                     : user);
            user = (user === false ? searchNotInGroups('friends',       userID) : user);
            user = (user === false ? searchNotInGroups('recipients',    userID) : user);
            user = (user === false ? searchNotInGroups('talks',         userID) : user);
            return user;
        },
        doOnReady   : function(handle){
            function isReady(target){
                return pure.components.messenger.users.loader.data[target].ready;
            };
            var ready = true;
            ready = (ready !== false ? isReady('friends'    ) : ready);
            ready = (ready !== false ? isReady('recipients' ) : ready);
            ready = (ready !== false ? isReady('talks'      ) : ready);
            ready = (ready !== false ? isReady('groups'     ) : ready);
            if (ready === false){
                setTimeout(
                    function(){
                        pure.components.messenger.users.doOnReady(handle);
                    },
                    50
                );
            }else{
                pure.system.runHandle(
                    handle,
                    null,
                    'pure.components.messenger.users.doOnReady',
                    this
                );
            }
        },
        findGroup   : function(users){
            var groups      = pure.components.messenger.users.loader.data.groups.groups,
                members     = null,
                _members    = null;
            if (users instanceof Array){
                users = pure.components.messenger.module.helpers.arrayToInt(users);
                for (var index = groups.length - 1; index >= 0; index -= 1){
                    members = groups[index].members.members;
                    if (users.length === members.length){
                        _members = pure.components.messenger.module.helpers.arrayFromProperties(members, 'id');
                        _members = pure.components.messenger.module.helpers.arrayToInt(_members);
                        if (pure.components.messenger.module.helpers.isArraysSame(users, _members) === true){
                            return {
                                name    : groups[index].name,
                                avatar  : groups[index].avatar
                            };
                        }
                    }
                }
            }
            return null;
        },
        getGroupMembers : function(group_id){
            var groups      = pure.components.messenger.users.loader.data.groups.groups;
            if (parseInt(group_id, 10) > 0){
                for (var index = groups.length - 1; index >= 0; index -= 1){
                    if (groups[index].id == group_id){
                        return pure.tools.arrays.copy(groups[index].members.members);
                    }
                }
            }
            return null;
        },
        init        : function(){
            pure.components.messenger.users.windows.    init();
            pure.components.messenger.users.buttons.    init();
            pure.components.messenger.users.tabs.       init();
            pure.components.messenger.users.template.   init();
            pure.components.messenger.users.filter.     init();
            pure.components.messenger.users.loader.get. recipients();
            pure.components.messenger.users.loader.get. friends();
            pure.components.messenger.users.loader.get. groups();
            pure.components.messenger.users.loader.get. talks();
        }
    };
    pure.system.start.add(pure.components.messenger.users.init);
}());