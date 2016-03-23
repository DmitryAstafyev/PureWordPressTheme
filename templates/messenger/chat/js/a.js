(function () {
    if (typeof window.pure                      !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components           !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.messenger !== "object") { window.pure.components.messenger    = {}; }
    "use strict";
    window.pure.components.messenger.chat = {
        loader      : {
            isPossible      : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.user_id'                               ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requestURL'                            ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.chat.messagesMaxCount'                 ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.chat.messagesMaxSize'                  ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.chat.allowMemes'                       ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.chat.allowedAttachmentSize'            ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.chat.messages'                ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.chat.messagesByThread'        ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.chat.messagesByThreadAfter'   ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.chat.send'                    ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.chat.getMemes'                ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.chat.getUnreadCount'          ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.chat.attachment.command'      ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.chat.attachment.url'          ) === null ? false : true));
                return result;
            },
            getMessages     : {
                send        : function(){
                    var request = pure.components.messenger.configuration.requests.chat.messages;
                    if (pure.components.messenger.chat.loader.isPossible() !== false){
                        pure.components.messenger.module.progress.show();
                        request = request.replace(/\[maxcount\]/, pure.components.messenger.configuration.chat.messagesMaxCount);
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.components.messenger.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.components.messenger.chat.loader.getMessages.onRecieve(id_request, response);
                                pure.components.messenger.module.progress.hide();
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.components.messenger.chat.loader.getMessages.onError(event, id_request);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.components.messenger.chat.loader.getMessages.onError(event, id_request);
                            }
                        });
                    }
                },
                onRecieve   : function(id_request, response){
                    var data = null;
                    if (response !== 'no access'){
                        try{
                            data = JSON.parse(response);
                            pure.components.messenger.chat.messages.threads.addFromLoader(data);
                            return true;
                        }catch (e){
                        }
                    }
                    return false;
                },
                onError     : function(event, id_request){
                    //alert(id_request);
                }
            },
            init            : function(){
                pure.components.messenger.chat.loader.getMessages.send();
            }
        },
        messages    : {
            threads : {
                data            : {},
                addFromLoader       : function(messages){
                    var threads     = pure.components.messenger.chat.messages.threads.data,
                        threadID    = null;
                    if (messages instanceof Array){
                        for(var index = 0, max_index = messages.length; index < max_index; index += 1){
                            threadID            = messages[index].thread_id;
                            threads[threadID]   = (typeof threads[threadID] === 'undefined' ? [] : threads[threadID]);
                            threads[threadID].push(messages[index]);
                            pure.components.messenger.chat.templates.talks.updateLastTalk(messages[index].thread_id, messages[index].created);
                        }
                        return true;
                    }
                    return false;
                },
                addFromMore         : function(messages){
                    var threads     = pure.components.messenger.chat.messages.threads.data,
                        threadID    = null;
                    if (messages instanceof Array){
                        if (messages.length > 0){
                            threadID = messages[0].thread_id;
                            if (typeof threads[threadID] !== 'undefined'){
                                for(var index = 0, max_index = messages.length; index < max_index; index += 1){
                                    threads[threadID].push(messages[index]);
                                }
                            }
                        }
                        pure.components.messenger.chat.messages.unread.storage.change(threadID, -messages.length);
                        pure.components.messenger.chat.messages.unread.counters.update();
                        return true;
                    }
                    return false;
                },
                addNew              : function(message){
                    var threads     = pure.components.messenger.chat.messages.threads.data,
                        threadID    = message.thread_id,
                        isExist     = false;
                    threads[threadID]   = (typeof threads[threadID] === 'undefined' ? [] : threads[threadID]);
                    for(var index = threads[threadID].length - 1; index >= 0; index -= 1){
                        if (threads[threadID][index].id == message.id){
                            isExist = true;
                            break;
                        }
                    }
                    if (isExist === false){
                        threads[threadID].unshift(message);
                    }
                    return (isExist === false ? true : false);
                },
                getByID             : function(thread_id){
                    var threads = pure.components.messenger.chat.messages.threads.data;
                    return (typeof threads[thread_id] !== 'undefined' ? threads[thread_id] : null);
                },
                getThreadForUsers   : function(users) {
                    var _threads    = pure.components.messenger.users.get('talks', null),
                        threads     = {},
                        recipients  = null;
                    if (_threads !== null){
                        for(var index = 0, max_index = _threads.length; index < max_index; index += 1){
                            threads[_threads[index].thread_id] = (typeof threads[_threads[index].thread_id] === 'undefined' ? [] : threads[_threads[index].thread_id]);
                            threads[_threads[index].thread_id].push(parseInt(_threads[index].id, 10));
                        }
                        users = pure.components.messenger.module.helpers.arrayToInt(users);
                        for (var thread_id in threads) {
                            recipients = threads[thread_id];
                            if (pure.components.messenger.module.helpers.isArraysSame(users, recipients) !== false){
                                return thread_id;
                            }
                        }
                    }
                    return null;
                }
            },
            area    : {
                current         : {
                    thread_id   : null,
                    shown       : null
                },
                select          : function(thread_id){
                    var area        = pure.components.messenger.chat.messages.area,
                        messages    = pure.components.messenger.chat.messages.threads.getByID(thread_id);
                    if (pure.components.messenger.chat.messages.editor.isBlocked() === false){
                        pure.components.messenger.chat.switchers.toChat();
                        if (area.current.thread_id != thread_id && parseInt(thread_id, 10) > 0 && messages !== null){
                            pure.components.messenger.chat.lists.create.reset();
                            area.clear();
                            pure.components.messenger.users.doOnReady(
                                function(){
                                    area.current.thread_id  = thread_id;
                                    area.current.shown      = messages.length;
                                    area.fill(thread_id);
                                    if (pure.components.messenger.chat.lists.talks.marks.isHasNew(thread_id) !== false){
                                        pure.components.messenger.chat.messages.unread.storage.change(thread_id, -area.current.shown);
                                        pure.components.messenger.chat.messages.unread.counters.update();
                                    }
                                    pure.components.messenger.chat.lists.talks.marks.asHasNewUnset(thread_id);
                                }
                            );
                        }
                    }
                },
                fill            : function(thread_id){
                    var messages = pure.components.messenger.chat.messages.threads.getByID(thread_id);
                    if (messages !== null){
                        pure.components.messenger.chat.templates.messages.addMore(messages[0].thread_id);
                        for(var index = messages.length - 1, start_index = messages.length - 1; index >= 0; index -= 1){
                            pure.components.messenger.chat.templates.messages.add(
                                messages[index],
                                (index === start_index ? null : messages[index + 1])
                            );
                        }
                        pure.components.messenger.chat.messages.area.scrollToEnd(thread_id);
                    }
                },
                fillFromMore    : function(thread_id, more_button){
                    var messages    = pure.components.messenger.chat.messages.threads.getByID(thread_id),
                        current     = pure.components.messenger.chat.messages.area.current;
                    if (messages !== null && thread_id == current.thread_id){
                        for(var index = current.shown, max_index = messages.length; index < max_index; index += 1){
                            pure.components.messenger.chat.templates.messages.add(
                                messages[index],
                                (index > 0 ? messages[index - 1] : null),
                                more_button
                            );
                            current.shown = messages.length;
                        }
                        pure.components.messenger.chat.lists.talks.marks.asHasNewUnset(thread_id);
                    }
                },
                clear           : function(new_thread){
                    var globalNodes = pure.components.messenger.chat.nodes.store,
                        new_thread  = (typeof new_thread === 'boolean' ? new_thread : false);
                    globalNodes.areas.messages.innerHTML = '';
                    pure.components.messenger.chat.messages.area.current.shown      = 0;
                    pure.components.messenger.chat.messages.area.current.thread_id  = (new_thread === false ? 0 : -1);
                },
                addNew          : function(thread_id){
                    pure.components.messenger.chat.messages.area.current.shown += 1;
                    /*
                    var current = pure.components.messenger.chat.messages.area.current;
                    if (thread_id == current.thread_id) {
                    }*///Remove after tests
                },
                scrollToEnd     : function(thread_id){
                    var current = pure.components.messenger.chat.messages.area.current,
                        nodes   = pure.components.messenger.chat.nodes.store;
                    if (thread_id == current.thread_id) {
                        nodes.areas.messages.scrollTop = nodes.areas.messages.scrollHeight;
                    }
                }
            },
            editor  : {
                getText     : function(){
                    var nodes = pure.components.messenger.chat.nodes.store;
                    return nodes.controls.editor.value;
                },
                clear       : function(){
                    var nodes = pure.components.messenger.chat.nodes.store;
                    nodes.controls.editor.value = '';
                },
                block       : function(){
                    var nodes = pure.components.messenger.chat.nodes.store;
                    nodes.controls.editor.disabled = true;
                },
                unblock     : function(){
                    var nodes = pure.components.messenger.chat.nodes.store;
                    nodes.controls.editor.disabled = false;
                },
                isBlocked   : function(){
                    return pure.components.messenger.chat.nodes.store.controls.editor.disabled;
                }
            },
            more    : {
                progress    : null,
                send        : function(thread_id, button){
                    var request = pure.components.messenger.configuration.requests.chat.messagesByThread,
                        threads = pure.components.messenger.chat.messages.threads.getByID(thread_id);
                    if (threads !== null && pure.components.messenger.chat.messages.more.progress === null){
                        pure.components.messenger.chat.messages.more.progress = pure.templates.progressbar.D.show(button);
                        request = request.replace(/\[thread_id\]/,  thread_id);
                        request = request.replace(/\[shown\]/,      threads.length);
                        request = request.replace(/\[maxcount\]/,   pure.components.messenger.configuration.chat.messagesMaxCount);
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.components.messenger.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.components.messenger.chat.messages.more.onRecieve(id_request, response, thread_id, button);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.components.messenger.chat.messages.more.onError(event, id_request);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.components.messenger.chat.messages.more.onError(event, id_request);
                            }
                        });
                    }
                },
                onRecieve   : function(id_request, response, thread_id, button){
                    var data = null;
                    if (response !== 'no access'){
                        try{
                            data = JSON.parse(response);
                            if (data !== false){
                                pure.components.messenger.chat.messages.threads.addFromMore(data);
                                pure.components.messenger.chat.messages.area.fillFromMore(thread_id, button);
                                pure.templates.progressbar.D.hide(pure.components.messenger.chat.messages.more.progress);
                                pure.components.messenger.chat.messages.more.progress = null;
                                return true;
                            }
                        }catch (e){
                            pure.components.messenger.chat.messages.more.onError(null, id_request);
                        }
                    }
                    pure.components.messenger.chat.messages.more.onError(null, id_request);
                    return false;
                },
                onError     : function(event, id_request){
                    pure.templates.progressbar.D.hide(pure.components.messenger.chat.messages.more.progress);
                    pure.components.messenger.chat.messages.more.progress = null;
                    pure.components.messenger.chat.dialogs.info('Connection error', 'Sorry but server did not answer or answer was incorrect. Try a bit later.');
                }
            },
            create  : {
                init    : function(){
                    var nodes = pure.components.messenger.chat.nodes.store;
                    pure.events.add(
                        nodes.controls.send,
                        'click',
                        function(){
                            pure.components.messenger.chat.messages.create.send(nodes.controls.send);
                        }
                    );
                },
                send    : function(button, message){
                    var nodes       = pure.components.messenger.chat.nodes.store,
                        button      = (button !== null ? button : nodes.controls.send),
                        current     = pure.components.messenger.chat.messages.area.current,
                        thread_id   = parseInt(current.thread_id, 10),
                        message     = (typeof message === 'string' ? message : pure.components.messenger.chat.messages.editor.getText()),
                        _recipients = null,
                        recipients  = null;
                    if (pure.components.messenger.chat.messages.editor.isBlocked() === false){
                        if (message.length > 0 && message.length < pure.components.messenger.configuration.chat.messagesMaxSize){
                            recipients = [];
                            if (thread_id === -1){
                                _recipients = pure.components.messenger.chat.lists.create.data.get();
                                recipients  = pure.components.messenger.module.helpers.arrayFromProperties(_recipients, 'id');
                            }
                            pure.components.messenger.chat.messages.create.request.send(message, thread_id, recipients, button);
                        }else{
                            pure.components.messenger.chat.dialogs.info(
                                'Cannot do it',
                                'Please, check your message. Message should have more than 0 symbols, but not not more than ' + pure.components.messenger.configuration.chat.messagesMaxSize + ' symbols.'
                            );
                        }
                    }
                },
                request : {
                    progress    : null,
                    send        : function(message, thread_id, recipients, button){
                        var request             = pure.components.messenger.configuration.requests.chat.send,
                            message             = (typeof message === 'string' ? message : null),
                            original_message    = null;
                        if (message !== null && pure.components.messenger.chat.messages.create.request.progress === null){
                            pure.components.messenger.chat.messages.create.request.progress = pure.templates.progressbar.A.show(
                                button,
                                'z-index:1;',
                                'z-index:2;'
                            );
                            pure.components.messenger.chat.messages.editor.block();
                            pure.components.messenger.chat.switchers.blocks.listsBlock();
                            original_message    = message;
                            message             = pure.convertor.UTF8.  encode(message);
                            message             = pure.convertor.BASE64.encode(message);
                            request             = request.replace(/\[thread_id\]/,  thread_id           );
                            request             = request.replace(/\[message\]/,    message             );
                            request             = request.replace(/\[recipients\]/, recipients.join(','));
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : pure.components.messenger.configuration.requestURL,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.components.messenger.chat.messages.create.request.onRecieve(id_request, response, thread_id, original_message);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.components.messenger.chat.messages.create.request.onError(event, id_request);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.components.messenger.chat.messages.create.request.onError(event, id_request);
                                }
                            });
                        }
                    },
                    onRecieve   : function(id_request, response, thread_id, message){
                        var data        = null,
                            messages    = null;
                        switch (response){
                            case 'no recipients':
                                pure.components.messenger.chat.dialogs.info('Server error', 'Server cannot find recipients of message.');
                                break;
                            case 'too big message':
                                pure.components.messenger.chat.dialogs.info('Server error', 'You message is too big.');
                                break;
                            case 'error_during_saving':
                                pure.components.messenger.chat.dialogs.info('Server error', 'Sorry, some unknown error was on server. Try again a bit later.');
                                break;
                            case 'no access':
                                pure.components.messenger.chat.dialogs.info('Server error', 'Sorry, but you have not necessary permissions.');
                                break;
                            case 'incorrect meme url':
                                pure.components.messenger.chat.dialogs.info('Server error', 'Sorry, but url of MEME is incorrent.');
                                break;
                            default :
                                try{
                                    data            = JSON.parse(response);
                                    data.message_id = parseInt(data.message_id, 10);
                                    data.thread_id  = parseInt(data.thread_id,  10);
                                    if (data.message_id > 0 && data.thread_id > 0){
                                        if (parseInt(thread_id, 10) === -1){
                                            pure.components.messenger.chat.templates.talks.acceptNewThread(data.thread_id);
                                        }
                                        pure.components.messenger.chat.messages.threads.addNew({
                                            id              : data.message_id,
                                            sender_id       : pure.components.messenger.configuration.user_id,
                                            thread_id       : data.thread_id,
                                            message         : message,
                                            created         : data.created,
                                            attachment_id   : null
                                        });
                                        pure.components.messenger.chat.messages.area.addNew(data.thread_id);
                                        messages = pure.components.messenger.chat.messages.threads.getByID(data.thread_id);
                                        if (messages !== null){
                                            pure.components.messenger.chat.templates.messages.add(
                                                messages[0],
                                                (messages.length > 1 ? messages[1] : null),
                                                null,
                                                true
                                            );
                                            pure.components.messenger.chat.messages.editor.clear();
                                        }
                                        //Server nofification
                                        pure.appevents.Actions.call('webSocketsServer','wakeup', null, null);
                                    }else{
                                        pure.components.messenger.chat.dialogs.info('Server error', 'Sorry, some unknown error was on server. Try again a bit later.');
                                    }
                                }catch (e){
                                    pure.components.messenger.chat.dialogs.info('Server error', 'Sorry, some unknown error was on server. Try again a bit later.');
                                }
                                break;
                        }
                        pure.components.messenger.chat.messages.editor.unblock();
                        pure.components.messenger.chat.switchers.blocks.listsUnblock();
                        pure.templates.progressbar.A.hide(pure.components.messenger.chat.messages.create.request.progress);
                        pure.components.messenger.chat.messages.create.request.progress = null;
                        return false;
                    },
                    onError     : function(event, id_request){
                        pure.templates.progressbar.A.hide(pure.components.messenger.chat.messages.create.request.progress);
                        pure.components.messenger.chat.messages.create.request.progress = null;
                        pure.components.messenger.chat.dialogs.info('Connection error', 'Sorry but server did not answer or answer was incorrect. Try a bit later.');
                        pure.components.messenger.chat.messages.editor.unblock();
                        pure.components.messenger.chat.switchers.blocks.listsUnblock();
                    }
                }
            },
            update  : {
                init        : function(){
                    pure.appevents.Actions.listen(
                        'webSocketServerEvents',
                        'chat_message',
                        pure.components.messenger.chat.messages.update.processing,
                        'chat_update_handle'
                    );
                },
                processing  : function(params){
                    var parameters = (typeof params.parameters === 'object' ? params.parameters : null);
                    if (parameters !== null){
                        if (typeof parameters.message_id    !== 'undefined' &&
                            typeof parameters.created       !== 'undefined' &&
                            typeof parameters.thread_id     !== 'undefined'){
                            pure.components.messenger.chat.messages.update.send(parameters.thread_id, parameters.created);
                        }
                    }
                },
                send        : function(thread_id, date){
                    var request = pure.components.messenger.configuration.requests.chat.messagesByThreadAfter;
                    request = request.replace(/\[thread_id\]/,  thread_id   );
                    request = request.replace(/\[date\]/,       date        );
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : pure.components.messenger.configuration.requestURL,
                        request     : request,
                        onrecieve   : function (id_request, response) {
                            pure.components.messenger.chat.messages.update.onRecieve(id_request, response, thread_id);
                        },
                        onreaction  : null,
                        onerror     : function (event, id_request) {
                            pure.components.messenger.chat.messages.update.onError(event, id_request);
                        },
                        ontimeout   : function (event, id_request) {
                            pure.components.messenger.chat.messages.update.onError(event, id_request);
                        }
                    });
                },
                onRecieve   : function(id_request, response, thread_id){
                    function proceedExistingThread(data){
                        var messages = null;
                        for (var index = data.length - 1; index >= 0; index -= 1){
                            if (pure.components.messenger.chat.messages.threads.addNew(data[index]) !== false){
                                pure.components.messenger.chat.messages.area.addNew(data[index].thread_id);
                                if (parseInt(data[index].thread_id, 10) === parseInt(pure.components.messenger.chat.messages.area.current.thread_id, 10)){
                                    messages = pure.components.messenger.chat.messages.threads.getByID(data[index].thread_id);
                                    if (messages !== null){
                                        pure.components.messenger.chat.templates.messages.add(
                                            messages[0],
                                            (messages.length > 1 ? messages[1] : null),
                                            null,
                                            true
                                        );
                                    }
                                }else{
                                    pure.components.messenger.chat.messages.unread.storage.change(data[index].thread_id, +1);
                                }
                            }
                        }
                        pure.components.messenger.chat.lists.talks.marks.asHasNewSet(data[0].thread_id);
                        pure.components.messenger.chat.messages.unread.counters.update();
                    };
                    function proceedNotExistingThread(data){
                        pure.components.messenger.users.get(
                            'talks',
                            function(response){
                                pure.components.messenger.chat.lists.talks.update(response);
                                proceedExistingThread(data);
                            },
                            true
                        );
                    };
                    var data = null;
                    if (response !== 'no access'){
                        try{
                            data = JSON.parse(response);
                            if (data !== false){
                                if (data instanceof Array){
                                    if (pure.components.messenger.chat.messages.threads.getByID(data[0].thread_id) !== null){
                                        //thread(donuse) is exist
                                        proceedExistingThread(data);
                                    }else{
                                        //new thread(donuse) in chat
                                        proceedNotExistingThread(data);
                                    }
                                }
                                return true;
                            }
                        }catch (e){
                        }
                    }
                    return false;
                },
                onError     : function(event, id_request){
                    pure.components.messenger.chat.dialogs.info('Connection error', 'Sorry but server did not answer or answer was incorrect. Try a bit later.');
                }
            },
            unread  : {
                storage    : {
                    data    : null,
                    upload  : function(data){
                        var _data = null;
                        pure.components.messenger.chat.messages.unread.storage.data = [];
                        _data = pure.components.messenger.chat.messages.unread.storage.data;
                        if (data instanceof Array){
                            for (var index = data.length - 1; index >= 0; index -= 1){
                                _data.push({
                                    thread_id   : data[index].thread_id,
                                    count       : parseInt(data[index].count,10)
                                });
                            }
                        }
                    },
                    get     : function(thread_id){
                        var data = pure.components.messenger.chat.messages.unread.storage.data;
                        if (data !== null){
                            if (data instanceof Array){
                                for (var index = data.length - 1; index >= 0; index -= 1){
                                    if (data[index].thread_id == thread_id){
                                        return data[index].count;
                                    }
                                }
                            }
                        }
                        return 0;
                    },
                    change  : function(thread_id, value){
                        var data    = pure.components.messenger.chat.messages.unread.storage.data;
                        if (data !== null){
                            if (data instanceof Array){
                                for (var index = data.length - 1; index >= 0; index -= 1){
                                    if (data[index].thread_id == thread_id){
                                        data[index].count = data[index].count + value;
                                        data[index].count = (data[index].count < 0 ? 0 : data[index].count);
                                        return true;
                                    }
                                }
                                data.push({
                                    count       : (value < 0 ? 0 : value),
                                    thread_id   : thread_id
                                });
                            }
                        }
                        return false;
                    },
                    total   : function(){
                        var data    = pure.components.messenger.chat.messages.unread.storage.data,
                            count   = 0;
                        if (data !== null){
                            if (data instanceof Array){
                                for (var index = data.length - 1; index >= 0; index -= 1){
                                    count += parseInt(data[index].count, 10);
                                }
                            }
                        }
                        return count;
                    }
                },
                counters  : {
                    update : function(){
                        var counters    = pure.nodes.select.all('*[data-messenger-engine-counter="chat"]'),
                            total       = pure.components.messenger.chat.messages.unread.storage.total();
                        if (counters !== null){
                            for(var index = counters.length - 1; index >= 0; index -= 1){
                                counters[index].innerHTML = (total === 0 ? '' : total);
                            }
                        }
                    }
                },
                request : {
                    send        : function(){
                        var request = pure.components.messenger.configuration.requests.chat.getUnreadCount;
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.components.messenger.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.components.messenger.chat.messages.unread.request.onRecieve(id_request, response);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.components.messenger.chat.messages.unread.request.onError(event, id_request);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.components.messenger.chat.messages.unread.request.onError(event, id_request);
                            }
                        });
                    },
                    onRecieve   : function(id_request, response){
                        var data = null;
                        if (response !== 'no access' && response !== 'error'){
                            try{
                                data = JSON.parse(response);
                                if (data !== false){
                                    if (data instanceof Array){
                                        for(var index = data.length - 1; index >= 0; index -= 1){
                                            if (parseInt(data[index].count, 10) > 0){
                                                pure.components.messenger.chat.lists.talks.marks.asHasNewSet(data[index].thread_id);
                                            }
                                        }
                                        pure.components.messenger.chat.messages.unread.storage.upload(data);
                                        pure.components.messenger.chat.messages.unread.counters.update();
                                    }
                                    return true;
                                }
                            }catch (e){
                            }
                        }
                        return false;
                    },
                    onError     : function(event, id_request){
                        //Do nothing
                    }
                },
                init : function(){
                    pure.components.messenger.chat.messages.unread.request.send();
                }
            },
            init : function(){
                pure.components.messenger.chat.messages.create.init();
                pure.components.messenger.chat.messages.update.init();
            }
        },
        switchers   : {
            blocks      : {
                listsBlock : function(){
                    var nodes = pure.components.messenger.chat.nodes.store;
                    nodes.switchers.talks.  disabled = true;
                    nodes.switchers.friends.disabled = true;
                    nodes.switchers.group.  disabled = true;
                    nodes.switchers.talks.  readOnly = true;
                    nodes.switchers.friends.readOnly = true;
                    nodes.switchers.group.  readOnly = true;
                },
                listsUnblock : function(){
                    var nodes = pure.components.messenger.chat.nodes.store;
                    nodes.switchers.talks.  disabled = false;
                    nodes.switchers.friends.disabled = false;
                    nodes.switchers.group.  disabled = false;
                    nodes.switchers.talks.  readOnly = false;
                    nodes.switchers.friends.readOnly = false;
                    nodes.switchers.group.  readOnly = false;
                }
            },
            toChat      : function(){
                var nodes = pure.components.messenger.chat.nodes.store;
                nodes.chatswitchers.chat.checked = true;
            },
            toChatInfo  : function(){
                var nodes = pure.components.messenger.chat.nodes.store;
                nodes.chatswitchers.chatinfo.checked = true;
            },
            toListInfo  : function(){
                var nodes = pure.components.messenger.chat.nodes.store;
                nodes.chatswitchers.listinfo.checked = true;
            },
            toTalks     : function(){
                var nodes = pure.components.messenger.chat.nodes.store;
                nodes.switchers.talks.checked = true;
            },
            getCurrent  : function(){
                var nodes = pure.components.messenger.chat.nodes.store;
                if (nodes.switchers.talks.checked === true){
                    return 'talks';
                }
                if (nodes.switchers.friends.checked === true){
                    return 'friends';
                }
                if (nodes.switchers.gmembers.checked === true){
                    return 'gmembers';
                }
                if (nodes.switchers.ggroups.checked === true){
                    return 'ggroups';
                }
                return null;
            },
            init        : function(){
                var nodes = pure.components.messenger.chat.nodes.store;
                pure.events.add(
                    nodes.switchers.friends,
                    'change',
                    function(event){
                        if (nodes.switchers.friends.checked === true){
                            pure.components.messenger.chat.messages.area.clear(false);
                            pure.components.messenger.chat.switchers.toListInfo();
                            pure.components.messenger.chat.lists.reset('friends');
                        }
                    }
                );
                pure.events.add(
                    nodes.switchers.group,
                    'change',
                    function(){
                        if (nodes.switchers.group.checked === true){
                            pure.components.messenger.chat.messages.area.clear(false);
                            pure.components.messenger.chat.switchers.toListInfo();
                            pure.components.messenger.chat.lists.reset('gmembers');
                            pure.components.messenger.chat.lists.reset('ggroups');
                        }
                    }
                );
                pure.events.add(
                    nodes.switchers.talks,
                    'change',
                    function(){
                        if (nodes.switchers.talks.checked === true){
                            pure.components.messenger.chat.switchers.toChatInfo();
                        }
                    }
                );
                pure.events.add(
                    nodes.switchers.gmembers,
                    'change',
                    function(){
                        if (nodes.switchers.gmembers.checked === true){
                            pure.components.messenger.chat.messages.area.clear(false);
                            pure.components.messenger.chat.lists.reset('gmembers');
                        }
                    }
                );
                pure.events.add(
                    nodes.switchers.ggroups,
                    'change',
                    function(){
                        if (nodes.switchers.ggroups.checked === true){
                            pure.components.messenger.chat.lists.reset('ggroups');
                        }
                    }
                );
            }
        },
        nodes       : {
            store : {},
            find : function(){
                var nodes = pure.components.messenger.chat.nodes.store;
                nodes.switchers = {
                    talks       : pure.nodes.select.first('input[data-messenger-engine-switcher="talks"]'    ),
                    friends     : pure.nodes.select.first('input[data-messenger-engine-switcher="friends"]'  ),
                    group       : pure.nodes.select.first('input[data-messenger-engine-switcher="groups"]'   ),
                    gmembers    : pure.nodes.select.first('input[data-messenger-engine-switcher="gmembers"]' ),
                    ggroups     : pure.nodes.select.first('input[data-messenger-engine-switcher="ggroups"]'  )
                };
                nodes.chatswitchers = {
                    chat        : pure.nodes.select.first('input[data-messenger-engine-switcher="content.chat"]'    ),
                    listinfo    : pure.nodes.select.first('input[data-messenger-engine-switcher="content.info"]'    ),
                    chatinfo    : pure.nodes.select.first('input[data-messenger-engine-switcher="content.begin"]'   )
                };
                nodes.user = {
                    name    : pure.nodes.select.first('*[data-chat-engine-element="name"]'    ),
                    avatar  : pure.nodes.select.first('*[data-chat-engine-element="avatar"]'  )
                };
                nodes.lists = {
                    talks       : pure.nodes.select.first('*[data-chat-engine-element="list.talks"]'            ),
                    friends     : pure.nodes.select.first('*[data-chat-engine-element="list.friends"]'          ),
                    gmembers    : pure.nodes.select.first('*[data-chat-engine-element="list.gmembers"]'         ),
                    ggroups     : pure.nodes.select.first('*[data-chat-engine-element="list.ggroups"]'          )
                };
                nodes.areas = {
                    chat        : pure.nodes.select.first('*[data-chat-engine-element="aria.chat"]'     ),
                    messages    : pure.nodes.select.first('*[data-chat-engine-element="aria.messages"]' ),
                    info        : pure.nodes.select.first('*[data-chat-engine-element="aria.info"]'     )
                };
                nodes.controls = {
                    editor      : pure.nodes.select.first('*[data-chat-engine-element="controls.editor"]'           ),
                    send        : pure.nodes.select.first('*[data-chat-engine-element="controls.button.send"]'      ),
                    attach      : pure.nodes.select.first('*[data-chat-engine-element="controls.button.attach"]'    )
                };
                return (pure.tools.objects.isValueIn(nodes, null) === false ? true : false);
            }
        },
        templates   : {
            common      : {
                init    : function(tab){
                    var template = pure.nodes.select.first('*[data-chat-engine-element="list.' + tab + '"] *[data-chat-engine-template="member"]');
                    if (template !== null){
                        pure.components.messenger.chat.templates[tab].data = {
                            nodeName    : template.nodeName,
                            innerHTML   : template.innerHTML,
                            attributes  : pure.nodes.attributes.get(template, ['data-chat-engine-template'])
                        };
                        template.parentNode.removeChild(template);
                    }
                }
            },
            messages    : {
                data    : null,
                init    : function(){
                    var templates = {
                            income  : pure.nodes.select.first('*[data-chat-engine-template="chat.message.in"]'   ),
                            outcome : pure.nodes.select.first('*[data-chat-engine-template="chat.message.out"]'  ),
                            date    : pure.nodes.select.first('*[data-chat-engine-template="chat.message.date"]' ),
                            more    : pure.nodes.select.first('*[data-chat-engine-template="chat.message.more"]' ),
                            meme    : pure.nodes.select.first('*[data-chat-engine-template="chat.message.meme"]' ),
                            image   : pure.nodes.select.first('*[data-chat-engine-template="chat.message.image"]')
                        };
                    if (pure.tools.objects.isValueIn(templates, null) === false){
                        pure.components.messenger.chat.templates.messages.data = {
                            income : {
                                nodeName    : templates.income.nodeName,
                                innerHTML   : templates.income.innerHTML,
                                attributes  : pure.nodes.attributes.get(templates.income, ['data-chat-engine-template'])
                            },
                            outcome : {
                                nodeName    : templates.outcome.nodeName,
                                innerHTML   : templates.outcome.innerHTML,
                                attributes  : pure.nodes.attributes.get(templates.outcome, ['data-chat-engine-template'])
                            },
                            date    : {
                                nodeName    : templates.date.nodeName,
                                attributes  : pure.nodes.attributes.get(templates.date, ['data-chat-engine-template'])
                            },
                            more    : {
                                nodeName    : templates.more.nodeName,
                                innerHTML   : templates.more.innerHTML,
                                attributes  : pure.nodes.attributes.get(templates.more, ['data-chat-engine-template'])
                            },
                            meme    : {
                                nodeName    : templates.meme.nodeName,
                                innerHTML   : templates.meme.innerHTML,
                                attributes  : pure.nodes.attributes.get(templates.meme, ['data-chat-engine-template'])
                            },
                            image    : {
                                nodeName    : templates.image.nodeName,
                                innerHTML   : templates.image.innerHTML,
                                attributes  : pure.nodes.attributes.get(templates.image, ['data-chat-engine-template'])
                            }
                        };
                        templates.income.   parentNode.removeChild(templates.income );
                        templates.outcome.  parentNode.removeChild(templates.outcome);
                        templates.date.     parentNode.removeChild(templates.date   );
                        templates.more.     parentNode.removeChild(templates.more   );
                        templates.meme.     parentNode.removeChild(templates.meme   );
                        templates.image.    parentNode.removeChild(templates.image  );
                    }
                },
                add     : function(message, previousMessage, more_button, scrollToMessage){
                    function isMeme(message){
                        var matches = message.match(/\[meme:begin\](.*)\[meme:end\]/gi),
                            result  = '';
                        if (matches instanceof Array){
                            if (matches.length === 1){
                                result = matches[0];
                                result = result.replace('[meme:begin]', '');
                                result = result.replace('[meme:end]',   '');
                                return result;
                            }
                        }
                        return null;
                    };
                    function getDate(date){
                        return (date.getDate() < 10 ? '0' : '') + date.getDate() + '.' + ((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1) + '.' + date.getFullYear();
                    };
                    function addDate(date, messageNode){
                        var data    = pure.components.messenger.chat.templates.messages.data.date,
                            node    = document.createElement(data.nodeName),
                            _date   = getDate(date);
                        node.innerHTML  = _date;
                        pure.nodes.attributes.set(node, data.attributes);
                        node.setAttribute('data-chat-message-date-id', _date);
                        messageNode.parentNode.insertBefore(node, messageNode);
                    };
                    function doesDateNeed(message, previousMessage){
                        if (previousMessage === null){
                            return true;
                        }else{
                            _created = pure.tools.date.YYYYMMDDHHMMSSToObject(previousMessage.created);
                            if (created.getDate()       !== _created.getDate()  ||
                                created.getMonth()      !== _created.getMonth() ||
                                created.getFullYear()   !== _created.getFullYear()){
                                return true;
                            }
                        }
                        return false;
                    };
                    function getDateNode(created){
                        var date = pure.tools.date.YYYYMMDDHHMMSSToObject(created);
                        return pure.nodes.select.first('*[data-chat-message-date-id="' + getDate(date) + '"]');
                    };
                    function showMessage(){
                        node            = document.createElement(data.nodeName);
                        node.innerHTML  = data.innerHTML;
                        pure.nodes.attributes.set(node, data.attributes);
                        node.setAttribute('data-chat-message-id', message.id);
                        if (more_button !== null){
                            if (doesDateNeed(message, previousMessage) === true){
                                more_button.parentNode.insertBefore(node, more_button.nextSibling);
                            }else{
                                date_node = getDateNode(message.created);
                                if (date_node !== null){
                                    date_node.parentNode.insertBefore(node, date_node.nextSibling);
                                }
                            }
                        }else{
                            globalNodes.areas.messages.appendChild(node);
                        }
                        nodes = {
                            avatar  : pure.nodes.select.first(data.nodeName + '[data-chat-message-id="' + message.id + '"]' + ' *[data-chat-engine-template-item="avatar"]'     ),
                            created : pure.nodes.select.first(data.nodeName + '[data-chat-message-id="' + message.id + '"]' + ' *[data-chat-engine-template-item="created"]'    ),
                            message : pure.nodes.select.first(data.nodeName + '[data-chat-message-id="' + message.id + '"]' + ' *[data-chat-engine-template-item="message"]'    )
                        };
                        if (direction === 'income'){
                            nodes.name = pure.nodes.select.first(data.nodeName + '[data-chat-message-id="' + message.id + '"]' + ' *[data-chat-engine-template-item="name"]'    );
                        }
                        if (pure.tools.objects.isValueIn(nodes, null) === false){
                            user = pure.components.messenger.users.getUser(message.sender_id);
                            if (user !== false){
                                nodes.avatar.style.backgroundImage  = 'url(' + user.avatar + ')';
                                nodes.message.innerHTML             = message.message;
                                nodes.created.innerHTML             = (created.getHours() < 10 ? '0' :'') + created.getHours() + ':' + (created.getMinutes() < 10 ? '0' :'') + created.getMinutes();
                                if (direction === 'income'){
                                    nodes.name.innerHTML            = user.name;
                                }
                                //Set date
                                if (doesDateNeed(message, previousMessage) === true){
                                    addDate(created, node);
                                }
                                //Scroll
                                if (scrollToMessage !== false){
                                    pure.components.messenger.chat.messages.area.scrollToEnd(message.thread_id);
                                    //node.scrollIntoView(false);
                                }
                                return true;
                            }
                        }
                        node.parentNode.removeChild(node);
                    };
                    function showMeme(){
                        data            = pure.components.messenger.chat.templates.messages.data.meme;
                        node            = document.createElement(data.nodeName);
                        node.innerHTML  = data.innerHTML;
                        pure.nodes.attributes.set(node, data.attributes);
                        node.setAttribute('data-chat-message-id', message.id);
                        if (more_button !== null){
                            if (doesDateNeed(message, previousMessage) === true){
                                more_button.parentNode.insertBefore(node, more_button.nextSibling);
                            }else{
                                date_node = getDateNode(message.created);
                                if (date_node !== null){
                                    date_node.parentNode.insertBefore(node, date_node.nextSibling);
                                }
                            }
                        }else{
                            globalNodes.areas.messages.appendChild(node);
                        }
                        nodes = {
                            meme    : pure.nodes.select.first(data.nodeName + '[data-chat-message-id="' + message.id + '"]' + ' *[data-chat-engine-template-item="meme"]'       ),
                            created : pure.nodes.select.first(data.nodeName + '[data-chat-message-id="' + message.id + '"]' + ' *[data-chat-engine-template-item="created"]'    ),
                            name    : pure.nodes.select.first(data.nodeName + '[data-chat-message-id="' + message.id + '"]' + ' *[data-chat-engine-template-item="name"]'       )
                        };
                        if (pure.tools.objects.isValueIn(nodes, null) === false){
                            user = pure.components.messenger.users.getUser(message.sender_id);
                            if (user !== false){
                                nodes.meme.src              = memeSrc;
                                nodes.created.  innerHTML   = (created.getHours() < 10 ? '0' :'') + created.getHours() + ':' + (created.getMinutes() < 10 ? '0' :'') + created.getMinutes();
                                nodes.name.     innerHTML   = user.name;
                                //Set date
                                if (doesDateNeed(message, previousMessage) === true){
                                    addDate(created, node);
                                }
                                //Scroll
                                if (scrollToMessage !== false){
                                    pure.components.messenger.chat.messages.area.scrollToEnd(message.thread_id);
                                }
                                return true;
                            }
                        }
                        node.parentNode.removeChild(node);
                    };
                    function showImage(){
                        data            = pure.components.messenger.chat.templates.messages.data.image;
                        node            = document.createElement(data.nodeName);
                        node.innerHTML  = data.innerHTML;
                        pure.nodes.attributes.set(node, data.attributes);
                        node.setAttribute('data-chat-message-id', message.id);
                        if (more_button !== null){
                            if (doesDateNeed(message, previousMessage) === true){
                                more_button.parentNode.insertBefore(node, more_button.nextSibling);
                            }else{
                                date_node = getDateNode(message.created);
                                if (date_node !== null){
                                    date_node.parentNode.insertBefore(node, date_node.nextSibling);
                                }
                            }
                        }else{
                            globalNodes.areas.messages.appendChild(node);
                        }
                        nodes = {
                            image   : pure.nodes.select.first(data.nodeName + '[data-chat-message-id="' + message.id + '"]' + ' *[data-chat-engine-template-item="image"]'      ),
                            created : pure.nodes.select.first(data.nodeName + '[data-chat-message-id="' + message.id + '"]' + ' *[data-chat-engine-template-item="created"]'    ),
                            name    : pure.nodes.select.first(data.nodeName + '[data-chat-message-id="' + message.id + '"]' + ' *[data-chat-engine-template-item="name"]'       )
                        };
                        if (pure.tools.objects.isValueIn(nodes, null) === false){
                            user = pure.components.messenger.users.getUser(message.sender_id);
                            if (user !== false){
                                nodes.image.src             =   pure.components.messenger.configuration.requestURL +
                                                                '?' +
                                                                pure.components.messenger.configuration.requests.chat.attachment.url.replace('[attachment_id]', message.attachment_id);
                                nodes.created.  innerHTML   = (created.getHours() < 10 ? '0' :'') + created.getHours() + ':' + (created.getMinutes() < 10 ? '0' :'') + created.getMinutes();
                                nodes.name.     innerHTML   = user.name;
                                //Set date
                                if (doesDateNeed(message, previousMessage) === true){
                                    addDate(created, node);
                                }
                                //Scroll
                                if (scrollToMessage !== false){
                                    pure.components.messenger.chat.messages.area.scrollToEnd(message.thread_id);
                                }
                                return true;
                            }
                        }
                        node.parentNode.removeChild(node);
                    };
                    var globalNodes     = pure.components.messenger.chat.nodes.store,
                        direction       = (parseInt(message.sender_id, 10) === parseInt(pure.components.messenger.configuration.user_id, 10) ? 'outcome' : 'income'),
                        data            = pure.components.messenger.chat.templates.messages.data[direction],
                        previousMessage = (typeof previousMessage !== 'undefined'   ? previousMessage : null),
                        scrollToMessage = (typeof scrollToMessage === 'boolean'     ? scrollToMessage : false),
                        nodes           = null,
                        node            = null,
                        user            = null,
                        created         = pure.tools.date.YYYYMMDDHHMMSSToObject(message.created),
                        _created        = null,
                        more_button     = (typeof more_button !== 'undefined' ? more_button : null),
                        date_node       = null,
                        memeSrc         = null;
                    if (data !== null){
                        if (message.message === '' && parseInt(message.attachment_id, 10) > 0){
                            showImage();
                        }else{
                            memeSrc = isMeme(message.message);
                            if (memeSrc !== null){
                                showMeme();
                            }else{
                                showMessage();
                            }
                        }
                        pure.components.messenger.chat.templates.talks.updateLastTalk(message.thread_id, message.created);
                    }
                    return false;
                },
                addMore : function(threadID){
                    var globalNodes     = pure.components.messenger.chat.nodes.store,
                        data            = pure.components.messenger.chat.templates.messages.data.more,
                        node            = null;
                    if (data !== null) {
                        node = document.createElement(data.nodeName);
                        node.innerHTML = data.innerHTML;
                        pure.nodes.attributes.set(node, data.attributes);
                        node.setAttribute('data-chat-thread-id', threadID);
                        globalNodes.areas.messages.appendChild(node);
                        //Attach event
                        pure.events.add(
                            node,
                            'click',
                            function(){
                                pure.components.messenger.chat.messages.more.send(threadID, node);
                            }
                        );
                    }
                }
            },
            talks       : {
                data            : null,
                init            : function(){
                    pure.components.messenger.chat.templates.common.init('talks');
                },
                add             : function(user){
                    var data            = pure.components.messenger.chat.templates.talks.data,
                        globalNodes     = pure.components.messenger.chat.nodes.store,
                        nodes           = null,
                        node            = null,
                        _node           = null,
                        threadID        = user[0].thread_id,
                        group           = null,
                        users           = null;
                    if (data !== null){
                        node            = document.createElement(data.nodeName);
                        node.innerHTML  = data.innerHTML;
                        pure.nodes.attributes.set(node, data.attributes);
                        node.setAttribute('data-chat-user-thread-id', threadID);
                        if (threadID === -1){
                            node.setAttribute('data-addition-style-type', 'new');
                        }
                        globalNodes.lists.talks.appendChild(node);
                        nodes = {
                            name        : pure.nodes.select.first('*[data-chat-engine-element="list.talks"] ' + data.nodeName + '[data-chat-user-thread-id="' + threadID + '"]' + ' *[data-chat-engine-template-item="name"]'   ),
                            last        : pure.nodes.select.first('*[data-chat-engine-element="list.talks"] ' + data.nodeName + '[data-chat-user-thread-id="' + threadID + '"]' + ' *[data-chat-engine-template-item="last"]'   ),
                            avatar      : pure.nodes.select.first('*[data-chat-engine-element="list.talks"] ' + data.nodeName + '[data-chat-user-thread-id="' + threadID + '"]' + ' *[data-chat-engine-template-item="avatar"]' ),
                            multi       : pure.nodes.select.first('*[data-chat-engine-element="list.talks"] ' + data.nodeName + '[data-chat-user-thread-id="' + threadID + '"]' + ' *[data-chat-engine-template="member.multi"]'),
                            group       : pure.nodes.select.first('*[data-chat-engine-element="list.talks"] ' + data.nodeName + '[data-chat-user-thread-id="' + threadID + '"]' + ' *[data-chat-engine-template="member.group"]'),
                            expand      : pure.nodes.select.first('*[data-chat-engine-element="list.talks"] ' + data.nodeName + '[data-chat-user-thread-id="' + threadID + '"]' + ' *[data-chat-engine-template-item="expand"]' )
                        };
                        if (pure.tools.objects.isValueIn(nodes, null) === false){
                            if (user.length === 1){
                                nodes.name.innerHTML                = user[0].name;
                                nodes.avatar.style.backgroundImage  = 'url(' + user[0].avatar + ')';
                                nodes.multi.    parentNode.removeChild(nodes.multi  );
                                nodes.expand.   parentNode.removeChild(nodes.expand );
                                nodes.group.    parentNode.removeChild(nodes.group  );
                            }else{
                                //check group
                                users = pure.components.messenger.module.helpers.arrayFromProperties(user, 'id');
                                users.push(pure.components.messenger.configuration.user_id);
                                group = pure.components.messenger.users.findGroup(users);
                                if (group !== null){
                                    nodes.group.innerHTML = nodes.group.innerHTML.replace('[name]',     group.name    );
                                    nodes.group.innerHTML = nodes.group.innerHTML.replace('[avatar]',   group.avatar  );
                                }else{
                                    nodes.group.parentNode.removeChild(nodes.group);
                                }
                                nodes.name.     parentNode.removeChild(nodes.name   );
                                nodes.avatar.   parentNode.removeChild(nodes.avatar );
                                for(var index = 0, max_index = user.length; index < max_index; index += 1){
                                    _node = nodes.multi.cloneNode(true);
                                    _node.innerHTML = _node.innerHTML.replace('[name]',     user[index].name    );
                                    _node.innerHTML = _node.innerHTML.replace('[avatar]',   user[index].avatar  );
                                    node.insertBefore(_node, nodes.last);
                                }
                                nodes.multi.    parentNode.removeChild(nodes.multi  );
                                if (user.length > 1){
                                    //Set default
                                    node.style.maxHeight = '4.6em';
                                    //Attach event
                                    pure.events.add(
                                        nodes.expand,
                                        'click',
                                        function(){
                                            if (node.style.maxHeight !== ''){
                                                node.style.maxHeight = '';
                                            }else{
                                                node.style.maxHeight = '4.6em';
                                            }
                                        }
                                    );
                                }else{
                                    nodes.expand.   parentNode.removeChild(nodes.expand );
                                }
                                node.setAttribute('data-addition-type', 'multi');
                            }
                            //Attach event
                            pure.events.add(
                                node,
                                'click',
                                function(event){
                                    var thread_id = parseInt(node.getAttribute('data-chat-user-thread-id'), 10);
                                    pure.components.messenger.chat.messages.area.select(thread_id);
                                }
                            );
                        }else{
                            node.parentNode.removeChild(node);
                        }
                    }
                },
                acceptNewThread : function(thread_id){
                    var thread = pure.nodes.select.first('*[data-chat-user-thread-id="-1"]');
                    if (thread !== null){
                        thread.setAttribute('data-chat-user-thread-id',thread_id);
                        thread.removeAttribute('data-addition-style-type');
                    }
                },
                updateLastTalk  : function(thread_id, message_date_str){
                    function getDate(date_str){
                        var date = pure.tools.date.YYYYMMDDHHMMSSToObject(date_str),
                            d = date.getDate(),
                            m = date.getMonth() + 1,
                            y = date.getFullYear();
                        return y + '-' + (m < 10 ? '0' + m : m) + '-'+ (d < 10 ? '0' + d : d);
                    };
                    var label       = pure.nodes.select.first('*[data-chat-user-thread-id="' + thread_id + '"]' + ' *[data-chat-engine-template-item="last"] span'),
                        old_date    = null,
                        new_date    = null;
                    if (label !== null){
                        old_date = label.innerHTML;
                        if (old_date === ''){
                            label.innerHTML = getDate(message_date_str);
                        }else{
                            old_date = pure.tools.date.YYYYMMDDHHMMSSToObject(old_date);
                            new_date = pure.tools.date.YYYYMMDDHHMMSSToObject(message_date_str);
                            if (new_date.valueOf() > old_date.valueOf()){
                                label.innerHTML = getDate(message_date_str);
                            }
                        }
                    }
                }
            },
            friends     : {
                data    : null,
                init    : function(){
                    pure.components.messenger.chat.templates.common.init('friends');
                },
                add     : function(user){
                    var data        = pure.components.messenger.chat.templates.friends.data,
                        globalNodes = pure.components.messenger.chat.nodes.store,
                        nodes       = null,
                        node        = null;
                    if (data !== null){
                        node            = document.createElement(data.nodeName);
                        node.innerHTML  = data.innerHTML;
                        pure.nodes.attributes.set(node, data.attributes);
                        node.setAttribute('data-chat-user-id', user.id);
                        globalNodes.lists.friends.appendChild(node);
                        nodes = {
                            name        : pure.nodes.select.first('*[data-chat-engine-element="list.friends"] ' + data.nodeName + '[data-chat-user-id="' + user.id + '"]' + ' *[data-chat-engine-template-item="name"]'   ),
                            avatar      : pure.nodes.select.first('*[data-chat-engine-element="list.friends"] ' + data.nodeName + '[data-chat-user-id="' + user.id + '"]' + ' *[data-chat-engine-template-item="avatar"]' )
                        };
                        if (pure.tools.objects.isValueIn(nodes, null) === false){
                            nodes.name.innerHTML                = user.name;
                            nodes.avatar.style.backgroundImage  = 'url(' + user.avatar + ')';
                        }else{
                            node.parentNode.removeChild(node);
                        }
                    }
                }
            },
            gmembers    : {
                data    : null,
                init    : function(){
                    pure.components.messenger.chat.templates.common.init('gmembers');
                },
                add     : function(user){
                    var data        = pure.components.messenger.chat.templates.gmembers.data,
                        globalNodes = pure.components.messenger.chat.nodes.store,
                        nodes       = null,
                        node        = null;
                    if (data !== null){
                        node            = document.createElement(data.nodeName);
                        node.innerHTML  = data.innerHTML;
                        pure.nodes.attributes.set(node, data.attributes);
                        node.setAttribute('data-chat-user-id', user.id);
                        globalNodes.lists.gmembers.appendChild(node);
                        nodes = {
                            name        : pure.nodes.select.first('*[data-chat-engine-element="list.gmembers"] ' + data.nodeName + '[data-chat-user-id="' + user.id + '"]' + ' *[data-chat-engine-template-item="name"]'   ),
                            avatar      : pure.nodes.select.first('*[data-chat-engine-element="list.gmembers"] ' + data.nodeName + '[data-chat-user-id="' + user.id + '"]' + ' *[data-chat-engine-template-item="avatar"]' )
                        };
                        if (pure.tools.objects.isValueIn(nodes, null) === false){
                            nodes.name.innerHTML                = user.name;
                            nodes.avatar.style.backgroundImage  = 'url(' + user.avatar + ')';
                        }else{
                            node.parentNode.removeChild(node);
                        }
                    }
                }
            },
            ggroups     : {
                data    : null,
                init    : function(){
                    pure.components.messenger.chat.templates.common.init('ggroups');
                },
                add     : function(group){
                    var data        = pure.components.messenger.chat.templates.ggroups.data,
                        globalNodes = pure.components.messenger.chat.nodes.store,
                        nodes       = null,
                        node        = null;
                    if (data !== null){
                        node            = document.createElement(data.nodeName);
                        node.innerHTML  = data.innerHTML;
                        pure.nodes.attributes.set(node, data.attributes);
                        node.setAttribute('data-chat-group-id', group.id);
                        globalNodes.lists.ggroups.appendChild(node);
                        nodes = {
                            name        : pure.nodes.select.first('*[data-chat-engine-element="list.ggroups"] ' + data.nodeName + '[data-chat-group-id="' + group.id + '"]' + ' *[data-chat-engine-template-item="name"]'   ),
                            avatar      : pure.nodes.select.first('*[data-chat-engine-element="list.ggroups"] ' + data.nodeName + '[data-chat-group-id="' + group.id + '"]' + ' *[data-chat-engine-template-item="avatar"]' )
                        };
                        if (pure.tools.objects.isValueIn(nodes, null) === false){
                            nodes.name.innerHTML                = group.name;
                            nodes.avatar.style.backgroundImage  = 'url(' + group.avatar + ')';
                        }else{
                            node.parentNode.removeChild(node);
                        }
                    }
                }
            },
            init        : function(){
                pure.components.messenger.chat.templates.talks.     init();
                pure.components.messenger.chat.templates.friends.   init();
                pure.components.messenger.chat.templates.gmembers.  init();
                pure.components.messenger.chat.templates.ggroups.   init();
                pure.components.messenger.chat.templates.messages.  init();
            }
        },
        lists       : {
            common      : {
                getFromModule : function(target, handle){
                    if (pure.system.getInstanceByPath('pure.components.messenger.users.get') !== null){
                        pure.components.messenger.users.get(target, handle);
                    }else{
                        setTimeout(
                            function(){
                                pure.components.messenger.chat.lists.common.getFromModule(target, handle);
                            },
                            50
                        );
                    }
                }
            },
            talks       : {
                get     : function(){
                    pure.components.messenger.module.progress.show();
                    pure.components.messenger.chat.lists.common.getFromModule(
                        'talks',
                        function(response){
                            pure.components.messenger.chat.lists.talks.fill(response);
                            pure.components.messenger.chat.messages.unread.init();
                            pure.components.messenger.module.progress.hide();
                        }
                    );
                },
                fill    : function(response){
                    var items   = (typeof response.items !== 'undefined' ? response.items : null),
                        _items  = [];
                    if (items !== null){
                        if (items instanceof Array){
                            for(var index = 0, max_index = items.length; index < max_index; index += 1){
                                if (_items.length > 0){
                                    if (_items[_items.length - 1].thread_id != items[index].thread_id){
                                        pure.components.messenger.chat.templates.talks.add(_items);
                                        _items = [];
                                    }
                                }
                                _items.push(items[index]);
                                if (index === max_index - 1){
                                    pure.components.messenger.chat.templates.talks.add(_items);
                                }
                            }
                        }
                    }
                },
                update  : function(response){
                    var items   = (typeof response.items !== 'undefined' ? response.items : null),
                        _items  = [];
                    if (items !== null){
                        if (items instanceof Array){
                            for(var index = 0, max_index = items.length; index < max_index; index += 1){
                                if (pure.components.messenger.chat.messages.threads.getByID(items[index].thread_id) === null){
                                    if (_items.length > 0){
                                        if (_items[_items.length - 1].thread_id != items[index].thread_id){
                                            pure.components.messenger.chat.templates.talks.add(_items);
                                            _items = [];
                                        }
                                    }
                                    _items.push(items[index]);
                                    if (index === max_index - 1){
                                        pure.components.messenger.chat.templates.talks.add(_items);
                                    }
                                }
                            }
                        }
                    }
                },
                marks   : {
                    asHasNewSet     : function(thread_id){
                        var node    = pure.nodes.select.first('*[data-chat-user-thread-id="' + thread_id + '"]'),
                            current = pure.components.messenger.chat.messages.area.current;
                        if (node !== null){
                            if (parseInt(current.thread_id, 10) !== parseInt(thread_id, 10)){
                                node.setAttribute('data-addition-style-type', 'has_new_messages');
                            }
                        }
                    },
                    asHasNewUnset   : function(thread_id){
                        var node    = pure.nodes.select.first('*[data-chat-user-thread-id="' + thread_id + '"]');
                        if (node !== null){
                            if (pure.components.messenger.chat.messages.unread.storage.get(thread_id) === 0){
                                node.removeAttribute('data-addition-style-type');
                            }
                        }
                    },
                    isHasNew        : function(thread_id){
                        var node = pure.nodes.select.first('*[data-chat-user-thread-id="' + thread_id + '"]');
                        if (node !== null){
                            return (node.getAttribute('data-addition-style-type') === 'has_new_messages' ? true : false);
                        }
                        return false;
                    }
                }
            },
            friends     : {
                get     : function(){
                    pure.components.messenger.module.progress.show();
                    pure.components.messenger.chat.lists.common.getFromModule(
                        'friends',
                        function(response){
                            pure.components.messenger.chat.lists.friends.fill(response);
                            pure.components.messenger.module.progress.hide();
                        }
                    );
                },
                fill    : function(response){
                    var items = (typeof response.items !== 'undefined' ? response.items : null);
                    if (items !== null){
                        if (items instanceof Array){
                            for(var index = items.length - 1; index >= 0; index -= 1){
                                pure.components.messenger.chat.templates.friends.add(items[index]);
                            }
                        }
                    }
                }
            },
            gmembers    : {
                get     : function(){
                    pure.components.messenger.module.progress.show();
                    pure.components.messenger.chat.lists.common.getFromModule(
                        'groups',
                        function(response){
                            pure.components.messenger.chat.lists.gmembers.fill(response);
                            pure.components.messenger.module.progress.hide();
                        }
                    );
                },
                fill    : function(response){
                    var items   = (typeof response.items !== 'undefined' ? response.items : null),
                        list    = [],
                        IDs     = [],
                        members = null;
                    if (items !== null){
                        if (items instanceof Array){
                            for(var index = 0, max_index = items.length; index < max_index; index += 1){
                                members = items[index].members.members;
                                for(var _index = 0, _max_index = members.length; _index < _max_index; _index += 1){
                                    if (IDs.indexOf(members[_index].id) === -1 && pure.components.messenger.configuration.user_id != members[_index].id){
                                        IDs.push(members[_index].id);
                                        list.push(members[_index]);
                                    }
                                }
                            }
                            for(var index = list.length - 1; index >= 0; index -= 1){
                                pure.components.messenger.chat.templates.gmembers.add(list[index]);
                            }
                        }
                    }
                }
            },
            ggroups     : {
                get     : function(){
                    pure.components.messenger.module.progress.show();
                    pure.components.messenger.chat.lists.common.getFromModule(
                        'groups',
                        function(response){
                            pure.components.messenger.chat.lists.ggroups.fill(response);
                            pure.components.messenger.module.progress.hide();
                        }
                    );
                },
                fill    : function(response){
                    var items   = (typeof response.items !== 'undefined' ? response.items : null);
                    if (items !== null){
                        if (items instanceof Array){
                            for(var index = 0, max_index = items.length; index < max_index; index += 1){
                                pure.components.messenger.chat.templates.ggroups.add(items[index]);
                            }
                        }
                    }
                }
            },
            reset       : function(list){
                var items = pure.nodes.select.all('*[data-chat-engine-element="list.' + list + '"] input[data-chat-engine-element="list.item.selector"]:checked');
                if (items !== null){
                    for(var index = items.length - 1; index >= 0; index -= 1){
                        items[index].checked = false;
                    }
                }
            },
            create      : {
                data        : {
                    data    : null,
                    get     : function(){
                        return pure.components.messenger.chat.lists.create.data.data;
                    },
                    set     : function(users){
                        pure.components.messenger.chat.lists.create.data.data = pure.tools.arrays.copy(users);
                    },
                    reset   : function(){
                        pure.components.messenger.chat.lists.create.data.data = null;
                    }
                },
                init        : function(){
                    var button = pure.nodes.select.first('*[data-chat-engine-element="chat.createnewthread"]');
                    if (button !== null){
                        pure.events.add(
                            button,
                            'click',
                            function(event){
                                pure.components.messenger.chat.lists.create.onClick(event);
                            }
                        );
                    }
                },
                onClick     : function(event){
                    var current     = pure.components.messenger.chat.switchers.getCurrent(),
                        processing  = pure.components.messenger.chat.lists.create.processing;
                    if (current !== null){
                        if (typeof processing[current] === 'function'){
                            processing[current]();
                        }
                    }
                },
                reset       : function(){
                    var thread = pure.nodes.select.first('*[data-chat-user-thread-id="-1"]');
                    if (thread !== null){
                        thread.parentNode.removeChild(thread);
                        pure.components.messenger.chat.lists.create.data.reset();
                    }
                },
                processing  : {
                    common      : {
                        getContacts     : function(list){
                            var contacts = pure.nodes.select.all('*[data-chat-engine-element="list.' + list + '"] input[data-chat-engine-element="list.item.selector"]:checked');
                            if (contacts !== null){
                                if (contacts.length > 0){
                                    return contacts;
                                }else{
                                    pure.components.messenger.chat.dialogs.info('Oops', 'Please select some contact(s) from list.');
                                }
                            }
                            return null;
                        },
                        getUsers        : function(contacts){
                            var users       = [],
                                user_id     = null,
                                user        = null;
                            for(var index = 0, max_index = contacts.length; index < max_index; index += 1){
                                user_id = parseInt(contacts[index].parentNode.getAttribute('data-chat-user-id'), 10);
                                if (user_id > 0){
                                    user = pure.components.messenger.users.getUser(user_id);
                                    if (user !== false){
                                        user.thread_id = -1;
                                        users.push(user);
                                    }
                                }
                            }
                            return users;
                        },
                        getGroupMembers : function(contact){
                            var users           = null,
                                group_id        = parseInt(contact.parentNode.getAttribute('data-chat-group-id'), 10),
                                remove_index    = -1;
                            if (group_id > 0){
                                users = pure.components.messenger.users.getGroupMembers(group_id);
                                if (users !== null){
                                    for (var index = users.length - 1; index >= 0; index -= 1){
                                        users[index].thread_id = -1;
                                        if (users[index].id == pure.components.messenger.configuration.user_id){
                                            remove_index = index;
                                        }
                                    }
                                    if (remove_index !== -1){
                                        users.splice(remove_index, 1);
                                    }
                                }
                            }
                            return users;
                        },
                        apply           : function(users){
                            pure.components.messenger.chat.switchers.           toTalks();
                            pure.components.messenger.chat.messages.area.       clear(true);
                            pure.components.messenger.chat.messages.editor.     clear();
                            pure.components.messenger.chat.switchers.           toChat();
                            pure.components.messenger.chat.templates.talks.     add(users);
                            pure.components.messenger.chat.lists.create.data.   set(users);
                        },
                        switchToThread  : function(thread_id){
                            pure.components.messenger.chat.switchers.           toTalks();
                            pure.components.messenger.chat.messages.editor.     clear();
                            pure.components.messenger.chat.switchers.           toChat();
                            pure.components.messenger.chat.messages.area.       select(thread_id);
                        }
                    },
                    friends     : function(){
                        var contacts    = pure.components.messenger.chat.lists.create.processing.common.getContacts('friends'),
                            users       = [],
                            thread_id   = null;
                        if (contacts !== null){
                            users       = pure.components.messenger.chat.lists.create.processing.common.getUsers(contacts);
                            if (users.length > 0){
                                thread_id = pure.components.messenger.chat.messages.threads.getThreadForUsers(
                                    pure.components.messenger.module.helpers.arrayFromProperties(users, 'id')
                                );
                                if (thread_id !== null){
                                    pure.components.messenger.chat.lists.create.processing.common.switchToThread(thread_id);
                                }else{
                                    pure.components.messenger.chat.lists.create.processing.common.apply(users);
                                }
                            }
                        }
                    },
                    gmembers    : function(){
                        var contacts    = pure.components.messenger.chat.lists.create.processing.common.getContacts('gmembers'),
                            users       = [],
                            thread_id   = null;
                        if (contacts !== null){
                            users       = pure.components.messenger.chat.lists.create.processing.common.getUsers(contacts);
                            if (users.length > 0){
                                thread_id = pure.components.messenger.chat.messages.threads.getThreadForUsers(
                                    pure.components.messenger.module.helpers.arrayFromProperties(users, 'id')
                                );
                                if (thread_id !== null){
                                    pure.components.messenger.chat.lists.create.processing.common.switchToThread(thread_id);
                                }else{
                                    pure.components.messenger.chat.lists.create.processing.common.apply(users);
                                }
                            }
                        }
                    },
                    ggroups     : function(){
                        var contacts    = pure.components.messenger.chat.lists.create.processing.common.getContacts('ggroups'),
                            users       = [],
                            thread_id   = null;
                        if (contacts !== null){
                            if (contacts.length === 1){
                                contacts = contacts[0];
                                users = pure.components.messenger.chat.lists.create.processing.common.getGroupMembers(contacts);
                                if (users !== null){
                                    if (users.length > 0){
                                        thread_id = pure.components.messenger.chat.messages.threads.getThreadForUsers(
                                            pure.components.messenger.module.helpers.arrayFromProperties(users, 'id')
                                        );
                                        if (thread_id !== null){
                                            pure.components.messenger.chat.lists.create.processing.common.switchToThread(thread_id);
                                        }else{
                                            pure.components.messenger.chat.lists.create.processing.common.apply(users);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            },
            init : function(){
                pure.components.messenger.chat.lists.friends.   get();
                pure.components.messenger.chat.lists.gmembers.  get();
                pure.components.messenger.chat.lists.ggroups.   get();
                pure.components.messenger.chat.lists.talks.     get();
                pure.components.messenger.chat.lists.create.    init();
            }
        },
        dialogs     : {
            info: function (title, message) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-element-type="Pure.Messenger.Mails.Dialog">' + message + '</p>',
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
        memes       : {
            data            : null,
            getMessages     : {
                send        : function(){
                    var request = pure.components.messenger.configuration.requests.chat.getMemes;
                    if (pure.components.messenger.chat.loader.isPossible() !== false){
                        if (pure.components.messenger.configuration.chat.allowMemes === 'yes'){
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : pure.components.messenger.configuration.requestURL,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.components.messenger.chat.memes.getMessages.onRecieve(id_request, response);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.components.messenger.chat.memes.getMessages.onError(event, id_request);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.components.messenger.chat.memes.getMessages.onError(event, id_request);
                                }
                            });
                        }
                    }
                },
                onRecieve   : function(id_request, response){
                    var data = null;
                    switch (response){
                        case 'no memes folder':
                            //do nothing
                            break;
                        case 'memes are not allowed':
                            //do nothing
                            break;
                        case 'no access':
                            //do nothing
                            break;
                        default :
                            try{
                                data = JSON.parse(response);
                                if(data instanceof Array){
                                    if (data.length > 0){
                                        pure.components.messenger.chat.memes.data = data;
                                        pure.components.messenger.chat.memes.button.init();
                                        return true;
                                    }
                                }
                            }catch (e){
                            }
                            break;
                    }
                    pure.components.messenger.chat.memes.button.init();
                    return false;
                },
                onError     : function(event, id_request){
                    pure.components.messenger.chat.memes.button.init();
                    //do nothing
                }
            },
            button  : {
                init: function(){
                    var button = pure.nodes.select.first('*[data-chat-engine-element="controls.button.meme"]');
                    if (button !== null){
                        if (pure.components.messenger.chat.memes.data !== null){
                            pure.events.add(
                                button,
                                'click',
                                pure.components.messenger.chat.memes.select.show
                            );
                        }else{
                            button.parentNode.removeChild(button);
                        }
                    }
                }
            },
            select  : {
                dialogID    : null,
                show        : function(){
                    var memes   = '',
                        data    = pure.components.messenger.chat.memes.data;
                    if (data instanceof Array){
                        if (pure.components.messenger.chat.messages.editor.isBlocked() === false){
                            for(var index = data.length - 1; index >= 0; index -= 1){
                                memes += '<a data-element-type="Pure.Messenger.Chat.Meme" data-meme-src="' + data[index] + '"><img alt="" data-element-type="Pure.Messenger.Chat.Meme" src="' + data[index] + '" /></a>';
                            }
                            pure.components.messenger.chat.memes.select.dialogID = pure.components.dialogs.B.open({
                                title       : 'Memes',
                                innerHTML   : '<div data-element-type="Pure.Messenger.Chat.Memes.Container">' + memes + '</div>',
                                width       : 70,
                                parent      : document.body,
                                fullHeight  : true,
                                afterInit   : pure.components.messenger.chat.memes.select.afterInit,
                                buttons     : [
                                    {
                                        title       : 'CANCEL',
                                        handle      : null,
                                        closeAfter  : true
                                    }
                                ]
                            });
                        }
                    }
                },
                afterInit   : function(){
                    var memes = pure.nodes.select.all('a[data-element-type="Pure.Messenger.Chat.Meme"]');
                    if (memes !== null){
                        for(var index = memes.length - 1; index >= 0; index -= 1){
                            (function(node){
                                var memeSrc = node.getAttribute('data-meme-src');
                                if (memeSrc !== ''){
                                    pure.events.add(
                                        node,
                                        'click',
                                        function(){
                                            pure.components.messenger.chat.messages.create.send(
                                                null,
                                                '[meme:begin]' + memeSrc + '[meme:end]'
                                            );
                                            if (pure.components.messenger.chat.memes.select.dialogID !== null){
                                                pure.components.dialogs.B.close(
                                                    pure.components.messenger.chat.memes.select.dialogID
                                                );
                                                pure.components.messenger.chat.memes.select.dialogID = null;
                                            }
                                        }
                                    );
                                }
                            }(memes[index]));
                        }
                    }
                }
            },
            init    : function(){
                pure.components.messenger.chat.memes.getMessages.send();
            }

        },
        attachments : {
            init    : function(){
                var input   = pure.nodes.select.first('input[data-chat-engine-element="controls.button.attach.input"]'),
                    button  = pure.nodes.select.first('*[data-chat-engine-element="controls.button.attach"]');
                if (input !== null && button !== null){
                    pure.events.add(
                        button,
                        'click',
                        function(){
                            if (pure.components.messenger.chat.messages.editor.isBlocked() === false){
                                pure.events.call(input, 'click');
                            }
                        }
                    );
                    pure.events.add(
                        input,
                        'change',
                        function(event){
                            pure.components.messenger.chat.attachments.select.onChange(event, input, button);
                        }
                    );
                }
            },
            select  : {
                progress    : null,
                onChange    : function(event, input, button){
                    var ext         = null,
                        thread_id   = parseInt(pure.components.messenger.chat.messages.area.current.thread_id, 10);
                    if (thread_id > 0 && pure.components.messenger.chat.attachments.select.progress === null){
                        if (typeof input.files !== 'undefined' && typeof input.value === 'string') {
                            if (input.files.length === 1) {
                                ext = (input.value.match(/\.([^\.]+)$/)[1]).toLowerCase();
                                if (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg') {
                                    if (input.files[0].size < pure.components.messenger.configuration.chat.allowedAttachmentSize) {
                                        pure.components.messenger.chat.attachments.select.send(input, thread_id, button);
                                    }else{
                                        pure.components.messenger.chat.dialogs.info(
                                            'You cannot do it',
                                            'Size of file should not be more than ' + pure.components.messenger.configuration.chat.allowedAttachmentSize + ' bytes.'
                                        );
                                    }
                                }else{
                                    pure.components.messenger.chat.dialogs.info(
                                        'You cannot do it',
                                        'You can use only next formats: *.gif, *.png, *.jpeg, *.jpg'
                                    );
                                }
                            }
                        }
                    }
                },
                send        : function(input, thread_id, button){
                    pure.components.messenger.chat.attachments.select.progress = pure.templates.progressbar.A.show(
                        button,
                        'z-index:1;',
                        'z-index:2;'
                    );
                    pure.components.messenger.chat.messages.editor.block();
                    pure.components.messenger.chat.switchers.blocks.listsBlock();
                    pure.components.uploader.module.upload(
                        input.files[0],
                        pure.components.messenger.configuration.requestURL,
                        {
                            ready : function(params){
                                pure.components.messenger.chat.attachments.select.received(params);
                            },
                            error : function(params){
                                pure.components.messenger.chat.attachments.select.error(params);
                            },
                            timeout : function(params){
                                pure.components.messenger.chat.attachments.select.error(params);
                            }
                        },
                        null,
                        'attachment',
                        [
                            { name:'command',   value: pure.components.messenger.configuration.requests.chat.attachment.command },
                            { name:'user_id',   value: pure.components.messenger.configuration.user_id                          },
                            { name:'thread_id', value: thread_id.toString()                                                     }
                        ]
                    );
                },
                received    : function(params){
                    var data        = null,
                        messages    = null;
                    switch (params.response){
                        case 'no access':
                            pure.components.messenger.chat.dialogs.info(
                                'Server error',
                                'You have not necessary permissions to upload images.'
                            );
                            break;
                        case 'no file found':
                            pure.components.messenger.chat.dialogs.info(
                                'Server error',
                                'Some error on server side. Server cannot find file.'
                            );
                            break;
                        case 'file is too big':
                            pure.components.messenger.chat.dialogs.info(
                                'Server error',
                                'Sorry, but your file is too big. Try change its size and send it again.'
                            );
                            break;
                        case 'wrong format':
                            pure.components.messenger.chat.dialogs.info(
                                'Server error',
                                'Sorry, but you can use only: *.gif, *.png, *.jpeg, *.jpg'
                            );
                            break;
                        case 'file not loaded':
                            pure.components.messenger.chat.dialogs.info(
                                'Server error',
                                'Some error on server side. Server cannot upload file. Try again a bit later.'
                            );
                            break;
                        case 'fail to save':
                            pure.components.messenger.chat.dialogs.info(
                                'Server error',
                                'Some error on server side. Server cannot save file on server. Try again a bit later.'
                            );
                            break;
                        default :
                            try{
                                data                = JSON.parse(params.response);
                                data.message_id     = parseInt(data.message_id,     10);
                                data.thread_id      = parseInt(data.thread_id,      10);
                                data.attachment_id  = parseInt(data.attachment_id,  10);
                                if (data.message_id > 0 && data.thread_id > 0 && data.attachment_id > 0){
                                    pure.components.messenger.chat.messages.threads.addNew({
                                        id              : data.message_id,
                                        sender_id       : pure.components.messenger.configuration.user_id,
                                        thread_id       : data.thread_id,
                                        message         : '',
                                        created         : data.created,
                                        attachment_id   : data.attachment_id
                                    });
                                    pure.components.messenger.chat.messages.area.addNew(data.thread_id);
                                    messages = pure.components.messenger.chat.messages.threads.getByID(data.thread_id);
                                    if (messages !== null){
                                        pure.components.messenger.chat.templates.messages.add(
                                            messages[0],
                                            (messages.length > 1 ? messages[1] : null),
                                            null,
                                            true
                                        );
                                        pure.components.messenger.chat.messages.editor.clear();
                                    }
                                    //Server nofification
                                    pure.appevents.Actions.call('webSocketsServer','wakeup', null, null);
                                }else{
                                    pure.components.messenger.chat.dialogs.info('Server error', 'Sorry, some unknown error was on server. Try again a bit later.');
                                }
                            }catch (e){
                                pure.components.messenger.chat.dialogs.info('Server error', 'Sorry, some unknown error was on server. Try again a bit later.');
                            }
                            break;
                    }
                    pure.templates.progressbar.A.hide(pure.components.messenger.chat.attachments.select.progress);
                    pure.components.messenger.chat.messages.editor.unblock();
                    pure.components.messenger.chat.switchers.blocks.listsUnblock();
                    pure.components.messenger.chat.attachments.select.progress = null;
                },
                error       : function(params){
                    pure.templates.progressbar.A.hide(pure.components.messenger.chat.attachments.select.progress);
                    pure.components.messenger.chat.messages.editor.unblock();
                    pure.components.messenger.chat.switchers.blocks.listsUnblock();
                    pure.components.messenger.chat.attachments.select.progress = null;
                    pure.components.messenger.chat.dialogs.info(
                        'Connection error',
                        'Cannot connect to server. Try again a bit later.'
                    );
                }
            }
        },
        init        : function(){
            if (pure.components.messenger.chat.loader.isPossible() !== false){
                if (pure.components.messenger.chat.nodes.find() !== false){
                    pure.components.messenger.chat.templates.   init();
                    pure.components.messenger.chat.lists.       init();
                    pure.components.messenger.chat.loader.      init();
                    pure.components.messenger.chat.switchers.   init();
                    pure.components.messenger.chat.messages.    init();
                    pure.components.messenger.chat.memes.       init();
                    pure.components.messenger.chat.attachments. init();
                }
            }
        }
    };
}());