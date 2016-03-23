(function () {
    if (typeof window.pure                      !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components           !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.messenger !== "object") { window.pure.components.messenger    = {}; }
    "use strict";
    window.pure.components.messenger.mails = {
        data        : {
            inbox   : {
                shown           : -1,
                total           : -1,
                maxCount        : -1,
                messages        : [],
                init                        : function(){
                    if (pure.components.messenger.mails.data.inbox.shown === -1){
                        pure.components.messenger.mails.data.inbox.shown    = 0;
                        pure.components.messenger.mails.data.inbox.maxCount = pure.components.messenger.configuration.mails.maxCount;
                        return true;
                    }
                    return false;
                },
                add                         : function(message){
                    message.nested = (message.nested instanceof Array ? message.nested : []);
                    pure.components.messenger.mails.data.inbox.messages.push(message);
                },
                addNewBasic                 : function(message){
                    var messages = pure.components.messenger.mails.data.inbox.messages,
                        isExist  = false;
                    for(var index = messages.length - 1; index >= 0; index -= 1){
                        if (messages[index].id == message.id && messages[index].thread_id == message.thread_id){
                            isExist = true;
                            break;
                        }
                    }
                    if (isExist === false){
                        message.nested = (message.nested instanceof Array ? message.nested : []);
                        messages.unshift(pure.tools.objects.copy(null, message));
                        pure.components.messenger.mails.data.inbox.shown    += 1;
                        pure.components.messenger.mails.data.inbox.total    += 1;
                        pure.components.messenger.mails.common.more.update('inbox');
                        return true;
                    }
                    return false;
                },
                addNewNested                : function(thread_id, message){
                    var basic   = pure.components.messenger.mails.data.inbox.getBasicMessageByThreadID(thread_id, true),
                        isExist = false;
                    if (basic !== null){
                        basic.nested = (typeof basic.nested !== 'undefined' ? basic.nested : []);
                        for(var index = basic.nested.length - 1; index >= 0; index -= 1){
                            if (basic.nested[index].id == message.id && basic.nested[index].thread_id == message.thread_id){
                                isExist = true;
                                break;
                            }
                        }
                        if (isExist === false){
                            basic.nested.unshift(pure.tools.objects.copy(null, message));
                            return true;
                        }
                        return false;
                    }
                },
                setAsRead                   : function(thread_id, message_id){
                    var basic = pure.components.messenger.mails.data.inbox.getBasicMessageByThreadID(thread_id, true);
                    if (basic !== null){
                        if (basic.message_id == message_id){
                            basic.is_unread = 0;
                        }else{
                            if (basic.nested instanceof Array){
                                for (var index = basic.nested.length - 1; index >= 0; index -= 1){
                                    if (basic.nested[index].message_id == message_id){
                                        basic.nested[index].is_unread = 0;
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                    return false;
                },
                getUnreadInThread           : function(thread_id){
                    var basic = pure.components.messenger.mails.data.inbox.getBasicMessageByThreadID(thread_id),
                        count = 0;
                    if (basic !== null){
                        if (basic.nested instanceof Array){
                            for (var index = basic.nested.length - 1; index >= 0; index -= 1){
                                count += parseInt(basic.nested[index].is_unread, 10);
                            }
                        }
                    }
                    return count;
                },
                getNestedInThread           : function(thread_id){
                    var basic = pure.components.messenger.mails.data.inbox.getBasicMessageByThreadID(thread_id),
                        count = 0;
                    if (basic !== null){
                        if (basic.nested instanceof Array){
                            count = basic.nested.length;
                        }
                    }
                    return count;
                },
                getBasicMessageByThreadID   : function(thread_id, direct){
                    var messages    = pure.components.messenger.mails.data.inbox.messages,
                        direct      = (typeof direct === 'boolean' ? direct : false);
                    for(var index = messages.length - 1; index >= 0; index -= 1){
                        if (messages[index].thread_id == thread_id){
                            return (direct === false ? pure.tools.objects.copy(null, messages[index]) : messages[index]);
                        }
                    }
                    return null;
                },
                getRecipientsByThreadID     : function(thread_id){
                    var message     = pure.components.messenger.mails.data.inbox.getBasicMessageByThreadID(thread_id),
                        recipients  = [],
                        cache       = [];
                    if (message !== null){
                        if (message.sender_id == pure.components.messenger.configuration.user_id){
                            if (typeof message.nested !== 'undefined'){
                                for(var index = message.nested.length - 1; index >= 0; index -= 1){
                                    if (message.nested[index].sender_id != pure.components.messenger.configuration.user_id){
                                        if (cache.indexOf(message.nested[index].sender_id) === -1){
                                            recipients. push(pure.tools.objects.copy(null, message.nested[index].sender));
                                            cache.      push(message.nested[index].sender_id);
                                        }
                                    }
                                }
                            }
                        }else{
                            if (typeof message.sender !== 'undefined'){
                                recipients.push(pure.tools.objects.copy(null, message.sender));
                            }
                        }
                    }
                    return (recipients.length > 0 ? recipients : null);
                }
            },
            outbox  : {
                shown       : -1,
                total       : -1,
                maxCount    : -1,
                messages    : [],
                init        : function(){
                    if (pure.components.messenger.mails.data.outbox.shown === -1){
                        pure.components.messenger.mails.data.outbox.shown    = 0;
                        pure.components.messenger.mails.data.outbox.maxCount = pure.components.messenger.configuration.mails.maxCount;
                        return true;
                    }
                    return false;
                },
                add : function(message){
                    pure.components.messenger.mails.data.outbox.messages.push(message);
                }
            }
        },
        loader      : {
            isPossible      : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.user_id'                           ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requestURL'                        ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.mails.inbox'              ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.mails.inboxByThreadAfter' ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.mails.messagesOfThread'   ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.mails.maxCount'                    ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.mails.maxSize'                     ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.mails.maxSubjectSize'              ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.mails.allowAttachment'             ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.mails.attachment.preload' ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.mails.attachment.remove'  ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.mails.attachmentMaxSize'           ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.mails.attachmentsMaxCount'         ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.mails.updateReadUnread'   ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.mails.getUnreadCount'     ) === null ? false : true));
                return result;
            },
            getMessages     : function(box){
                var request = pure.components.messenger.configuration.requests.mails[box];
                if (pure.components.messenger.mails.loader.isPossible() !== false){
                    if (pure.components.messenger.mails.data[box].init() !== false){
                        pure.components.messenger.module.progress.show();
                        request = request.replace(/\[shown\]/,      pure.components.messenger.mails.data[box].shown    );
                        request = request.replace(/\[maxcount\]/,   pure.components.messenger.mails.data[box].maxCount );
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.components.messenger.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.components.messenger.mails.loader.events.onRecieve(id_request, response, box);
                                pure.components.messenger.module.progress.hide();
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.components.messenger.mails.loader.events.onError(event, id_request, box);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.components.messenger.mails.loader.events.onError(event, id_request, box);
                            }
                        });
                    }
                }
            },
            events          : {
                onRecieve   : function(id_request, response, box){
                    var data = null;
                    if (response !== 'no access'){
                        try{
                            data = JSON.parse(response);
                            if (typeof data.messages    !== 'undefined' &&
                                typeof data.shown       !== 'undefined' &&
                                typeof data.total       !== 'undefined'){
                                if (data.messages instanceof Array){
                                    data.shown                                          = parseInt(data.shown, 10);
                                    data.total                                          = parseInt(data.total, 10);
                                    pure.components.messenger.mails.data[box].shown    += data.shown;
                                    pure.components.messenger.mails.data[box].total    = data.total;
                                    pure.components.messenger.mails.common.     add(data, box);
                                    pure.components.messenger.mails.common.more.update(box);
                                    if (box === 'inbox') {
                                        pure.components.messenger.mails.common.addExternal();
                                    }
                                    return true;
                                }
                            }
                        }catch (e){
                        }
                    }
                    return false;
                },
                onError     : function(event, id_request, box){
                    //alert(id_request);
                }
            }
        },
        inbox       : {
            more    : {
                request : {
                    progress    : null
                }
            }
        },
        outbox      : {
            more    : {
                request : {
                    progress    : null
                }
            }
        },
        common      : {
            add         : function(data, box){
                function doAdd(message, box){
                    pure.components.messenger.mails.data[box].add(message);
                    pure.components.messenger.mails.templates[box].basic.add(message);
                }
                function correction(box){
                    pure.components.messenger.mails.data[box].shown -= 1;
                    pure.components.messenger.mails.data[box].total -= 1;
                }
                for(var index = 0, max_index = data.messages.length; index < max_index; index += 1){
                    if (box === 'inbox'){
                        if (pure.components.messenger.mails.data.inbox.getBasicMessageByThreadID(data.messages[index].thread_id) === null){
                            doAdd(data.messages[index], box);
                        }else{
                            correction(box);
                        }
                    }else{
                        doAdd(data.messages[index], box);
                    }
                }
            },
            addNew      : function(message){
                function updateCounter(box){
                    pure.components.messenger.mails.data[box].shown += 1;
                    pure.components.messenger.mails.data[box].total += 1;
                    pure.components.messenger.mails.common.more.update(box);
                };
                var basicMessage    = pure.components.messenger.mails.data.inbox.getBasicMessageByThreadID(message.thread_id),
                    nestedContainer = null;
                if (basicMessage !== null){
                    nestedContainer = pure.nodes.select.first('*[data-mails-engine-element="inbox"] *[data-mails-engine-message-id="' + basicMessage.message_id + '"]' + ' *[data-mails-engine-template-item="nested.container"]');
                    if (nestedContainer !== null){
                        pure.components.messenger.mails.data.inbox.             addNewNested(message.thread_id, message);
                        pure.components.messenger.mails.templates.inbox.nested. add         (message, nestedContainer, true);
                    }
                }
                if (typeof message.recipients !== 'undefined'){
                    pure.components.messenger.mails.templates.outbox.basic.add(message, true);
                    updateCounter('outbox');
                }
            },
            addNewInbox : function(message){
                var basicMessage    = pure.components.messenger.mails.data.inbox.getBasicMessageByThreadID(message.thread_id),
                    nestedContainer = null;
                if (basicMessage !== null){
                    nestedContainer = pure.nodes.select.first('*[data-mails-engine-element="inbox"] *[data-mails-engine-message-id="' + basicMessage.message_id + '"]' + ' *[data-mails-engine-template-item="nested.container"]');
                    if (nestedContainer !== null){
                        if (pure.components.messenger.mails.data.inbox.addNewNested(message.thread_id, message) !== false){
                            pure.components.messenger.mails.templates.inbox.nested.     add         (message, nestedContainer, true);
                            pure.components.messenger.mails.templates.inbox.external.   add         (message);
                            pure.components.messenger.mails.templates.inbox.basic.      updateCounts(message.thread_id);
                            pure.components.messenger.mails.readUnread.counter.storage. change      (message.thread_id, +1);
                            pure.components.messenger.mails.readUnread.counter.         update      ();
                        }
                    }
                }else{
                    if (parseInt(message.count_in_thread, 10) > 1){
                        pure.components.messenger.mails.update.thread.update(message.thread_id);
                    }else{
                        if (pure.components.messenger.mails.data.inbox.addNewBasic (message) !== false){
                            pure.components.messenger.mails.templates.inbox.basic.          add         (message, true);
                            pure.components.messenger.mails.templates.inbox.external.       add         (message);
                            pure.components.messenger.mails.readUnread.counter.storage.     change      (message.thread_id, +1);
                            pure.components.messenger.mails.readUnread.counter.             update      ();
                        }
                    }
                }
            },
            addExternal : function(){
                var shown       = pure.components.messenger.mails.templates.inbox.external.data,
                    messages    = pure.components.messenger.mails.data.inbox.messages;
                if (shown !== null){
                    shown = shown.shown;
                    if (shown.length < 5){
                        for(var index = messages.length - 1; index >= 0; index -= 1){
                            if (messages[index].is_unread == 1 && shown.indexOf(messages[index].message_id) === -1){
                                pure.components.messenger.mails.templates.inbox.external.add(messages[index]);
                                shown.push(messages[index].message_id);
                            }
                            if (shown.length >= 5) {
                                pure.components.messenger.mails.templates.inbox.external.progress.hide();
                                return true;
                            }
                            if (messages[index].nested instanceof Array){
                                for(var _index = messages[index].nested.length - 1; _index >= 0; _index -= 1){
                                    if (messages[index].nested[_index].is_unread == 1 && shown.indexOf(messages[index].nested[_index].message_id) === -1){
                                        pure.components.messenger.mails.templates.inbox.external.add(messages[index].nested[_index]);
                                        shown.push(messages[index].nested[_index].message_id);
                                    }
                                    if (shown.length >= 5) {
                                        pure.components.messenger.mails.templates.inbox.external.progress.hide();
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                    pure.components.messenger.mails.templates.inbox.external.progress.hide();
                }
            },
            more        : {
                init    : function(box){
                    var button = pure.nodes.select.first('*[data-mails-engine-element="' + box + '"] *[data-mails-engine-template="' + box + '.more"]');
                    if (button !== null){
                        pure.events.add(
                            button,
                            'click',
                            function (event) {
                                pure.components.messenger.mails.common.more.request.send(event, button, box);
                            }
                        );
                    }
                },
                request : {
                    progress    : null,
                    send        : function(event, button, box){
                        var request = pure.components.messenger.configuration.requests.mails[box];
                        if (pure.components.messenger.mails.data[box].shown < pure.components.messenger.mails.data[box].total &&
                            pure.components.messenger.mails[box].more.request.progress === null){
                            pure.components.messenger.mails[box].more.request.progress = pure.templates.progressbar.B.show(button);
                            request = request.replace(/\[shown\]/,      pure.components.messenger.mails.data[box].shown    );
                            request = request.replace(/\[maxcount\]/,   pure.components.messenger.mails.data[box].maxCount );
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : pure.components.messenger.configuration.requestURL,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.components.messenger.mails.common.more.request.onRecieve(id_request, response, box);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.components.messenger.mails.common.more.request.onError(event, id_request, box);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.components.messenger.mails.common.more.request.onError(event, id_request, box);
                                }
                            });
                        }
                    },
                    onRecieve   : function(id_request, response, box){
                        pure.templates.progressbar.B.hide(pure.components.messenger.mails[box].more.request.progress);
                        pure.components.messenger.mails[box].more.request.progress = null;
                        pure.components.messenger.mails.loader.events.onRecieve(id_request, response, box);
                    },
                    onError     : function(event, id_request, box){
                        pure.templates.progressbar.B.hide(pure.components.messenger.mails[box].more.request.progress);
                        pure.components.messenger.mails[box].more.request.progress = null;
                    }
                },
                update  : function(box){
                    var data = pure.components.messenger.mails.templates[box].basic.data;
                    if (data !== null){
                        data.counts.shown.innerHTML = pure.components.messenger.mails.data[box].shown;
                        data.counts.total.innerHTML = pure.components.messenger.mails.data[box].total;
                    }
                }
            }
        },
        templates   : {
            inbox   : {
                basic       : {
                    data            : null,
                    init            : function(){
                        var template    = pure.nodes.select.first('*[data-mails-engine-element="inbox"] *[data-mails-engine-template="list.basic"]'),
                            counts      = {
                                shown : pure.nodes.select.first('*[data-mails-engine-element="inbox"] *[data-mails-engine-template-item="inbox.shown"]'),
                                total : pure.nodes.select.first('*[data-mails-engine-element="inbox"] *[data-mails-engine-template-item="inbox.total"]')
                            },
                            mark        = null,
                            new_mark    = null,
                            attachment  = pure.nodes.select.first('*[data-mails-engine-element="inbox"] *[data-mails-engine-template="attachment.container"] *[data-mails-engine-template="attachment.view"]');
                        if (template !== null && attachment !== null &&
                            pure.tools.objects.isValueIn(counts, null)                  === false &&
                            pure.components.messenger.mails.templates.inbox.basic.data  === null){
                            mark                    = document.createElement('DIV');
                            new_mark                = document.createElement('DIV');
                            mark.style.display      = 'none';
                            new_mark.style.display  = 'none';
                            pure.components.messenger.mails.templates.inbox.basic.data = {
                                nodeName    : template.nodeName,
                                innerHTML   : '',
                                attributes  : pure.nodes.attributes.get(template, ['data-mails-engine-template']),
                                mark        : mark,
                                new_mark    : new_mark,
                                counts      : counts,
                                attachment  :{
                                    nodeName    : attachment.nodeName,
                                    innerHTML   : attachment.innerHTML,
                                    attributes  : pure.nodes.attributes.get(attachment, ['data-mails-engine-template'])
                                }
                            };
                            attachment.parentNode.removeChild(attachment);
                            pure.components.messenger.mails.templates.inbox.basic.data.innerHTML = template.innerHTML;
                            template.parentNode.insertBefore(mark, template);
                            mark.parentNode.insertBefore(new_mark, mark);
                            template.parentNode.removeChild(template);
                        }
                    },
                    add             : function(message, new_flag){
                        var data        = pure.components.messenger.mails.templates.inbox.basic.data,
                            new_flag    = (typeof new_flag === 'boolean' ? new_flag : false),
                            nodes       = null,
                            node        = null,
                            recipients  = null,
                            attachment  = null,
                            temp_str    = null;
                        if (data !== null){
                            node            = document.createElement(data.nodeName);
                            node.innerHTML  = data.innerHTML.replace(/data-mails-engine-random-group-id/gim, pure.tools.IDs.get('random-group-id'));
                            pure.nodes.attributes.set(node, data.attributes);
                            node.setAttribute('data-mails-engine-message-id', message.message_id);
                            if (new_flag === false){
                                data.mark.parentNode.insertBefore(node, data.mark);
                            }else{
                                pure.nodes.insertAfter(node, data.new_mark);
                            }
                            nodes = {
                                name        : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="name"]'              ),
                                avatar      : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="avatar"]'            ),
                                subject     : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="subject"]'           ),
                                message     : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="message"]'           ),
                                date        : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="date"]'              ),
                                count       : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="nested.count"]'      ),
                                nested      : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="nested.container"]'  ),
                                reply       : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="reply"]'             ),
                                full        : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="full"]'              ),
                                remove      : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="remove"]'            ),
                                attachments : pure.nodes.select.first('*[data-mails-engine-element="inbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template="attachment.container"]'   )
                            };
                            node.style.display = 'none';
                            if (pure.tools.objects.isValueIn(nodes, null) === false){
                                //(!) Will be released in the future
                                nodes.remove.style.display = 'none';
                                //(!) Will be released in the future
                                nodes.name.innerHTML                = message.sender.name.replace(/\s/, '<br />');
                                nodes.avatar.style.backgroundImage  = 'url(' + message.sender.avatar + ')';
                                nodes.subject.innerHTML             = message.subject;
                                nodes.message.innerHTML             = message.message;
                                nodes.date.innerHTML                = message.created.replace(/\s/, '<br />');
                                node.style.display                  = '';
                                if (message.nested !== false){
                                    for(var index = 0, max_index = message.nested.length; index < max_index; index += 1){
                                        pure.components.messenger.mails.templates.inbox.nested.add(
                                            message.nested[index],
                                            nodes.nested,
                                            false
                                        );
                                    }
                                    nodes.count.innerHTML = 'include <span data-mails-engine-message-id="' + message.message_id + '"></span>' +
                                                            ' <sup data-mails-engine-message-id="' + message.message_id + '"></sup>' +
                                                            ' messages';
                                }else{
                                    nodes.count.    parentNode.removeChild(nodes.count  );
                                    nodes.nested.   parentNode.removeChild(nodes.nested );
                                }
                                //Read / unread
                                pure.components.messenger.mails.readUnread.defaults(message, nodes.subject);
                                pure.components.messenger.mails.templates.inbox.basic.updateCounts(message.thread_id);
                                //Add attachments
                                if (typeof message.attachments !== 'undefined'){
                                    if (message.attachments instanceof Array){
                                        for(var index = message.attachments.length - 1; index >= 0; index -= 1){
                                            attachment              = document.createElement(data.attachment.nodeName);
                                            temp_str                = data.attachment.innerHTML.replace('[name]',  message.attachments[index].original_name);
                                            temp_str                = temp_str.replace('[url]',   message.attachments[index].url          );
                                            attachment.innerHTML    = temp_str;
                                            pure.nodes.attributes.set(attachment, data.attachment.attributes);
                                            nodes.attachments.appendChild(attachment);
                                            attachment              = null;
                                        }
                                    }
                                }
                                //Get recipients of message
                                recipients = pure.components.messenger.mails.data.inbox.getRecipientsByThreadID(message.thread_id);
                                //Attach events
                                pure.events.add(
                                    nodes.reply,
                                    'click',
                                    function(){
                                        pure.components.messenger.mails.create.open(
                                            recipients,
                                            'Re: ' + message.subject,
                                            message.message_id
                                        );
                                    }
                                );
                                pure.events.add(
                                    nodes.full,
                                    'click',
                                    function(){
                                        pure.components.messenger.mails.readUnread.update(message, nodes.subject);
                                        pure.components.messenger.mails.dialogs.message(
                                            message.subject,
                                            message.message
                                        );
                                    }
                                );
                            }else{
                                node.parentNode.removeChild(node);
                            }
                        }
                    },
                    updateCounts    : function(thread_id){
                        var nodeCount       = null,
                            nodeUnread      = null,
                            basicMessage    = pure.components.messenger.mails.data.inbox.getBasicMessageByThreadID(thread_id),
                            unread          = null,
                            count           = null;
                        if (basicMessage !== null){
                            nodeCount   = pure.nodes.select.first('span[data-mails-engine-message-id="' + basicMessage.message_id + '"]');
                            nodeUnread  = pure.nodes.select.first('sup[data-mails-engine-message-id="' + basicMessage.message_id + '"]');
                            unread      = pure.components.messenger.mails.data.inbox.getUnreadInThread(thread_id);
                            count       = pure.components.messenger.mails.data.inbox.getNestedInThread(thread_id);
                            if (unread      !== null && count       !== null &&
                                nodeUnread  !== null && nodeCount   !== null){
                                nodeUnread. innerHTML = unread;
                                nodeCount.  innerHTML = count;
                                if (unread > 0){
                                    nodeUnread.style.display = '';
                                }else{
                                    nodeUnread.style.display = 'none';
                                }
                                if (count > 0){
                                    nodeCount.parentNode.style.display = '';
                                }else{
                                    nodeCount.parentNode.style.display = 'none';
                                }
                            }
                        }
                    }
                },
                nested      : {
                    data    : null,
                    init    : function(){
                        var template    = pure.nodes.select.first('*[data-mails-engine-element="inbox"] *[data-mails-engine-template="list.nested"]'),
                            attachment  = pure.nodes.select.first('*[data-mails-engine-element="inbox"] *[data-mails-engine-template="attachment.container.nested"] *[data-mails-engine-template="attachment.view"]');
                        if (template !== null && attachment !== null && pure.components.messenger.mails.templates.inbox.nested.data === null){
                            pure.components.messenger.mails.templates.inbox.nested.data = {
                                nodeName    : template.nodeName,
                                innerHTML   : '',
                                attributes  : pure.nodes.attributes.get(template, ['data-mails-engine-template']),
                                attachment  :{
                                    nodeName    : attachment.nodeName,
                                    innerHTML   : attachment.innerHTML,
                                    attributes  : pure.nodes.attributes.get(attachment, ['data-mails-engine-template'])
                                }
                            };
                            attachment.parentNode.removeChild(attachment);
                            pure.components.messenger.mails.templates.inbox.nested.data.innerHTML = template.innerHTML;
                            template.parentNode.removeChild(template);
                        }
                    },
                    add     : function(message, container, insertAsFirst){
                        var data            = pure.components.messenger.mails.templates.inbox.nested.data,
                            nodes           = null,
                            node            = null,
                            insertAsFirst   = (typeof insertAsFirst === 'boolean' ? insertAsFirst : false),
                            attachment      = null,
                            temp_str        = '';
                        if (data !== null){
                            node            = document.createElement(data.nodeName);
                            node.innerHTML  = data.innerHTML.replace(/data-mails-engine-random-group-id/gim, pure.tools.IDs.get('random-group-id'));
                            pure.nodes.attributes.set(node, data.attributes);
                            node.setAttribute('data-mails-engine-message-id', message.message_id);
                            if (insertAsFirst === false){
                                container.appendChild(node);
                            }else{
                                container.insertBefore(node, container.firstChild);
                            }
                            nodes = {
                                name        : pure.nodes.select.first(data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="name"]'                  ),
                                subject     : pure.nodes.select.first(data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="subject"]'               ),
                                message     : pure.nodes.select.first(data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="message"]'               ),
                                full        : pure.nodes.select.first(data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="full"]'                  ),
                                date        : pure.nodes.select.first(data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="date"]'                  ),
                                attachments : pure.nodes.select.first(data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template="attachment.container.nested"]')
                            };
                            node.style.display = 'none';
                            if (pure.tools.objects.isValueIn(nodes, null) === false){
                                nodes.name.innerHTML                = message.sender.name;
                                nodes.subject.innerHTML             = message.subject;
                                nodes.message.innerHTML             = message.message;
                                nodes.date.innerHTML                = message.created.replace(/\s/, '<br />');
                                node.style.display                  = '';
                                if (parseInt(message.sender.id, 10) !== parseInt(pure.components.messenger.configuration.user_id, 10)){
                                    node.setAttribute('data-addition-type', 'income');
                                }
                                //Read / unread
                                pure.components.messenger.mails.readUnread.defaults(message, nodes.subject);
                                //Add attachments
                                if (typeof message.attachments !== 'undefined'){
                                    if (message.attachments instanceof Array){
                                        for(var index = message.attachments.length - 1; index >= 0; index -= 1){
                                            attachment              = document.createElement(data.attachment.nodeName);
                                            temp_str                = data.attachment.innerHTML.replace('[name]',  message.attachments[index].original_name);
                                            temp_str                = temp_str.replace('[url]',   message.attachments[index].url          );
                                            attachment.innerHTML    = temp_str;
                                            pure.nodes.attributes.set(attachment, data.attachment.attributes);
                                            nodes.attachments.appendChild(attachment);
                                            attachment              = null;
                                        }
                                    }
                                }
                                //Attach events
                                pure.events.add(
                                    nodes.full,
                                    'click',
                                    function(){
                                        pure.components.messenger.mails.readUnread.update(message, nodes.subject);
                                        pure.components.messenger.mails.dialogs.message(
                                            message.subject,
                                            message.message
                                        );
                                    }
                                );
                            }else{
                                container.removeChild(node);
                            }
                        }
                    }
                },
                external    : {
                    data        : null,
                    init        : function(){
                        var templates   = {
                                messages    : pure.nodes.select.all('*[data-mails-external-engine-template="message"]'      ),
                                containers  : pure.nodes.select.all('*[data-mails-external-engine-template="container"]'    ),
                                counts      : pure.nodes.select.all('*[data-mails-external-engine-template="count"]'        )
                            },
                            progress    = pure.nodes.select.all('*[data-mails-external-engine-template="progress"]');
                        if (pure.tools.objects.isValueIn(templates, null) == false &&
                            pure.components.messenger.mails.templates.inbox.external.data  === null){
                            pure.components.messenger.mails.templates.inbox.external.data = {
                                messages            : [],
                                counts              : [],
                                containers          : [],
                                progress            : progress,
                                shown               : []
                            };
                            for(var key in templates){
                                for(var index = templates[key].length - 1; index >= 0; index -= 1){
                                    (function(data, node, key){
                                        if (key === 'messages'){
                                            var mark = document.createElement('DIV');
                                            mark.style.display  = 'none';
                                            data[key].push({
                                                nodeName    : node.nodeName,
                                                innerHTML   : node.innerHTML,
                                                attributes  : pure.nodes.attributes.get(node, ['data-mails-external-engine-template', 'style']),
                                                mark        : mark
                                            });
                                            node.parentNode.insertBefore(mark, node);
                                            node.parentNode.removeChild(node);
                                        }else{
                                            data[key].push(node);
                                        }
                                    }(
                                        pure.components.messenger.mails.templates.inbox.external.data,
                                        templates[key][index],
                                        key
                                    ));
                                }
                            }
                            pure.components.messenger.mails.templates.inbox.external.progress.show();
                        }
                    },
                    add         : function(message){
                        var data    = pure.components.messenger.mails.templates.inbox.external.data,
                            node    = null,
                            str     = null;
                        if (data !== null){
                            data = data.messages;
                            for(var index = data.length - 1; index >= 0; index -= 1){
                                str             = message.message;
                                str             = str.replace(/<.*?>/gim, '');
                                str             = (str.length > 100 ? str.substr(0, 50) + ' ...' : str);
                                str             = '<strong>' + message.subject + '</strong><br/>' + str;
                                node            = document.createElement(data[index].nodeName);
                                node.innerHTML  = data[index].innerHTML;
                                node.innerHTML  = node.innerHTML.replace(/\[avatar\]/gim,   message.sender.avatar   );
                                node.innerHTML  = node.innerHTML.replace(/\[name\]/gim,     message.sender.name     );
                                node.innerHTML  = node.innerHTML.replace(/\[message\]/gim,  str                     );
                                node.setAttribute('data-mails-external-message-id', message.message_id);
                                pure.nodes.attributes.set(node, data[index].attributes);
                                data[index].mark.parentNode.insertBefore(node, data[index].mark);
                            }
                        }
                    },
                    remove      : function(message_id){
                        var messages    = pure.nodes.select.all('*[data-mails-external-message-id="' + message_id + '"]'),
                            shown       = pure.components.messenger.mails.templates.inbox.external.data;
                        if (messages !== null && shown !== null){
                            shown = shown.shown;
                            for (var index = messages.length - 1; index >= 0; index -= 1){
                                messages[index].parentNode.removeChild(messages[index]);
                            }
                            if (shown.indexOf(message_id) !== -1){
                                shown.splice(shown.indexOf(message_id), 1);
                                pure.components.messenger.mails.common.addExternal();
                            }
                        }
                    },
                    update      : function(){
                        function show(containers){
                            for(var index = containers.length - 1; index >= 0; index -= 1){
                                containers[index].style.display = '';
                            }
                        };
                        function hide(containers){
                            for(var index = containers.length - 1; index >= 0; index -= 1){
                                containers[index].style.display = 'none';
                            }
                        };
                        function update(counts, count){
                            for(var index = counts.length - 1; index >= 0; index -= 1){
                                counts[index].innerHTML = count;
                            }
                        }
                        var data    = pure.components.messenger.mails.templates.inbox.external.data,
                            count   = pure.components.messenger.mails.readUnread.counter.storage.total();
                        if (data !== null){
                            if (count > 0){
                                show(data.containers);
                                update(data.counts, count);
                            }else{
                                hide(data.containers);
                            }
                        }
                    },
                    progress    :{
                        data : null,
                        show : function(){
                            var containers  = pure.components.messenger.mails.templates.inbox.external.data.progress,
                                progress    = null;
                            if (containers !== null){
                                pure.components.messenger.mails.templates.inbox.external.progress.data = [];
                                for (var index = containers.length - 1; index >= 0; index -= 1){
                                    progress = pure.templates.progressbar.A.show(containers[index], 'margin-left:-0.35em;margin-top:0.2em;');
                                    (function(progress){
                                        pure.components.messenger.mails.templates.inbox.external.progress.data.push(progress);
                                    }(progress));
                                }
                            }
                        },
                        hide : function(){
                            var progresses = pure.components.messenger.mails.templates.inbox.external.progress.data;
                            if (progresses !== null){
                                for(var index = progresses.length - 1; index >= 0; index -= 1){
                                    pure.templates.progressbar.A.hide(progresses[index]);
                                }
                                progresses = null;
                                pure.components.messenger.mails.templates.inbox.external.progress.data = null;
                            }
                        }
                    }
                }
            },
            outbox  : {
                basic : {
                    data    : null,
                    init    : function(){
                        var template    = pure.nodes.select.first('*[data-mails-engine-element="outbox"] *[data-mails-engine-template="list.basic"]'),
                            counts      = {
                                shown : pure.nodes.select.first('*[data-mails-engine-element="outbox"] *[data-mails-engine-template-item="outbox.shown"]'),
                                total : pure.nodes.select.first('*[data-mails-engine-element="outbox"] *[data-mails-engine-template-item="outbox.total"]')
                            },
                            mark        = null,
                            attachment  = pure.nodes.select.first('*[data-mails-engine-element="outbox"] *[data-mails-engine-template="attachment.container"] *[data-mails-engine-template="attachment.view"]');
                        if (template !== null && attachment !== null &&
                            pure.tools.objects.isValueIn(counts, null)                  === false &&
                            pure.components.messenger.mails.templates.outbox.basic.data  === null){
                            mark                = document.createElement('DIV');
                            mark.style.display  = 'none';
                            pure.components.messenger.mails.templates.outbox.basic.data = {
                                nodeName    : template.nodeName,
                                innerHTML   : '',
                                attributes  : pure.nodes.attributes.get(template, ['data-mails-engine-template']),
                                mark        : mark,
                                counts      : counts,
                                attachment  :{
                                    nodeName    : attachment.nodeName,
                                    innerHTML   : attachment.innerHTML,
                                    attributes  : pure.nodes.attributes.get(attachment, ['data-mails-engine-template'])
                                }
                            };
                            attachment.parentNode.removeChild(attachment);
                            pure.components.messenger.mails.templates.outbox.basic.data.innerHTML = template.innerHTML;
                            template.parentNode.insertBefore(mark, template);
                            template.parentNode.removeChild(template);
                        }
                    },
                    add     : function(message, insertAsFirst){
                        var data            = pure.components.messenger.mails.templates.outbox.basic.data,
                            nodes           = null,
                            node            = null,
                            recipient       = null,
                            insertAsFirst   = (typeof insertAsFirst === 'boolean' ? insertAsFirst : false),
                            firstMessage    = null,
                            attachment      = null,
                            temp_str        = null;
                        if (data !== null){
                            node            = document.createElement(data.nodeName);
                            node.innerHTML  = data.innerHTML.replace(/data-mails-engine-random-group-id/gim, pure.tools.IDs.get('random-group-id'));
                            pure.nodes.attributes.set(node, data.attributes);
                            node.setAttribute('data-mails-engine-message-id', message.message_id);
                            if (insertAsFirst === false){
                                data.mark.parentNode.insertBefore(node, data.mark);
                            }else{
                                firstMessage = pure.nodes.select.first('*[data-mails-engine-element="outbox"] ' + data.nodeName + '[data-mails-engine-message-id]');
                                if (firstMessage !== null){
                                    data.mark.parentNode.insertBefore(node, firstMessage);
                                }else{
                                    data.mark.parentNode.insertBefore(node, data.mark);
                                }
                            }
                            nodes = {
                                recipient   : pure.nodes.select.first('*[data-mails-engine-element="outbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="recipient"]'         ),
                                subject     : pure.nodes.select.first('*[data-mails-engine-element="outbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="subject"]'           ),
                                message     : pure.nodes.select.first('*[data-mails-engine-element="outbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="message"]'           ),
                                date        : pure.nodes.select.first('*[data-mails-engine-element="outbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="date"]'              ),
                                repeat      : pure.nodes.select.first('*[data-mails-engine-element="outbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="repeat"]'            ),
                                full        : pure.nodes.select.first('*[data-mails-engine-element="outbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="full"]'              ),
                                remove      : pure.nodes.select.first('*[data-mails-engine-element="outbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template-item="remove"]'            ),
                                attachments : pure.nodes.select.first('*[data-mails-engine-element="outbox"] ' + data.nodeName + '[data-mails-engine-message-id="' + message.message_id + '"]' + ' *[data-mails-engine-template="attachment.container"]'   )
                            };
                            node.style.display = 'none';
                            if (pure.tools.objects.isValueIn(nodes, null) === false){
                                //(!) Will be released in the future
                                nodes.remove.style.display = 'none';
                                //(!) Will be released in the future
                                for(var index = message.recipients.length - 1; index >= 0; index -= 1){
                                    recipient           = nodes.recipient.cloneNode(true);
                                    recipient.innerHTML = recipient.innerHTML.replace('[name]', message.recipients[index].name.replace(/\s/, '<br />'));
                                    recipient.innerHTML = recipient.innerHTML.replace('[avatar]', message.recipients[index].avatar);
                                    if (message.recipients.length > 1){
                                        recipient.setAttribute('data-addition-type', 'small');
                                    }
                                    nodes.recipient.parentNode.appendChild(recipient);
                                }
                                nodes.recipient.parentNode.removeChild(nodes.recipient);
                                nodes.subject.innerHTML             = message.subject;
                                nodes.message.innerHTML             = message.message;
                                nodes.date.innerHTML                = message.created.replace(/\s/, '<br />');
                                node.style.display                  = '';
                                //Add attachments
                                if (typeof message.attachments !== 'undefined'){
                                    if (message.attachments instanceof Array){
                                        for(var index = message.attachments.length - 1; index >= 0; index -= 1){
                                            attachment              = document.createElement(data.attachment.nodeName);
                                            temp_str                = data.attachment.innerHTML.replace('[name]',  message.attachments[index].original_name);
                                            temp_str                = temp_str.replace('[url]',   message.attachments[index].url          );
                                            attachment.innerHTML    = temp_str;
                                            pure.nodes.attributes.set(attachment, data.attachment.attributes);
                                            nodes.attachments.appendChild(attachment);
                                            attachment              = null;
                                        }
                                    }
                                }
                                //Attach events
                                pure.events.add(
                                    nodes.repeat,
                                    'click',
                                    function(){
                                        pure.components.messenger.mails.create.open(
                                            message.recipients,
                                            'Add: ' + message.subject,
                                            message.message_id
                                        );
                                    }
                                );
                                //Attach events
                                pure.events.add(
                                    nodes.full,
                                    'click',
                                    function(){
                                        pure.components.messenger.mails.dialogs.message(
                                            message.subject,
                                            message.message
                                        );
                                    }
                                );
                            }else{
                                node.parentNode.removeChild(node);
                            }
                        }
                    }
                }
            },
            init    : function(){
                pure.components.messenger.mails.templates.inbox.nested.     init();
                pure.components.messenger.mails.templates.inbox.basic.      init();
                pure.components.messenger.mails.templates.inbox.external.   init();
                pure.components.messenger.mails.templates.outbox.basic. init();
                pure.components.messenger.mails.common.more.            init('inbox');
                pure.components.messenger.mails.common.more.            init('outbox');
            }
        },
        switchers   : {
            data        : null,
            find        : function(){
                if (pure.components.messenger.mails.switchers.data === null){
                    pure.components.messenger.mails.switchers.data = {
                        inbox           : pure.nodes.select.first('*[data-mails-engine-element="switcher.inbox"]'           ),
                        outbox          : pure.nodes.select.first('*[data-mails-engine-element="switcher.outbox"]'          ),
                        create          : pure.nodes.select.first('*[data-mails-engine-element="switcher.create"]'          ),
                        createButton    : pure.nodes.select.first('*[data-mails-engine-element="switcher.button.create"]'   ),
                        current         : 'inbox'
                    };
                }
            },
            events      : {
                append  : function(){
                    var data = pure.components.messenger.mails.switchers.data;
                    if (data !== null){
                        pure.events.add(
                            data.createButton,
                            'click',
                            pure.components.messenger.mails.switchers.events.create
                        );
                        pure.events.add(
                            data.inbox,
                            'change',
                            pure.components.messenger.mails.switchers.events.inbox
                        );
                        pure.events.add(
                            data.outbox,
                            'change',
                            pure.components.messenger.mails.switchers.events.outbox
                        );
                    }
                },
                create  : function(){
                    var allows = pure.components.messenger.mails.create.access.allows;
                    if (pure.components.messenger.mails.switchers.data.create.checked   === false &&
                        pure.components.messenger.mails.create.current.isSending()      === false){
                        //set access
                        allows.recipients   = true;
                        allows.subject      = true;
                        pure.components.messenger.mails.create.access.apply();
                        //clear
                        pure.components.messenger.mails.create.reset();
                    }
                },
                inbox   : function(){
                    var data = pure.components.messenger.mails.switchers.data;
                    if (data !== null) {
                        if (data.inbox.checked === true){
                            if (pure.components.messenger.mails.create.current.isSending() !== false){
                                data.create.checked = true;
                            }else{
                                data.current    = 'inbox';
                            }
                        }
                    }
                },
                outbox  : function(){
                    var data = pure.components.messenger.mails.switchers.data;
                    if (data !== null){
                        if (data.outbox.checked === true){
                            if (pure.components.messenger.mails.create.current.isSending() !== false){
                                data.create.checked = true;
                            }else{
                                data.current    = 'outbox';
                            }
                        }
                    }
                }
            },
            switchTo    : function(switcher){
                var data = pure.components.messenger.mails.switchers.data;
                if (data !== null){
                    switch (switcher){
                        case 'inbox':
                            data.inbox.checked  = true;
                            break;
                        case 'outbox':
                            data.outbox.checked = true;
                            break;
                        case 'create':
                            data.create.checked = true;
                            break;
                        default :
                            pure.components.messenger.mails.switchers.switchTo(data.current);
                            break;

                    }
                }
            },
            init        : function(){
                pure.components.messenger.mails.switchers.          find();
                pure.components.messenger.mails.switchers.events.   append();
            }
        },
        create      : {
            current     : {
                message_id      : -1,
                recipients      : [],
                progress        : null,
                setMessageID    : function(message_id){
                    if (typeof message_id === 'number'){
                        if (message_id > 0 || message_id === -1){
                            pure.components.messenger.mails.create.current.message_id = message_id;
                            return true;
                        }
                    }
                    pure.components.messenger.mails.create.current.message_id = -1;
                    return false;
                },
                addRecipient    : function(recipient_id){
                    var recipients = pure.components.messenger.mails.create.current.recipients;
                    if (typeof recipient_id === 'number'){
                        if (recipients.indexOf(recipient_id) === -1){
                            recipients.push(recipient_id);
                            return true;
                        }
                    }
                    return false;
                },
                removeRecipient : function(recipient_id){
                    var recipients = pure.components.messenger.mails.create.current.recipients;
                    if (typeof recipient_id === 'number'){
                        if (recipients.indexOf(recipient_id) !== -1){
                            recipients.splice(recipients.indexOf(recipient_id), 1);
                            return true;
                        }
                    }
                    return false;
                },
                get             : function(){
                    return {
                        message_id : pure.components.messenger.mails.create.current.message_id,
                        recipients : pure.components.messenger.mails.create.current.recipients
                    }
                },
                reset           : function(){
                    pure.components.messenger.mails.create.current.message_id = -1;
                    pure.components.messenger.mails.create.current.recipients = [];
                },
                isSending : function(){
                    return (pure.components.messenger.mails.create.current.progress !== null ? true : false);
                }
            },
            access      : {
                allows  : {
                    subject     : true,
                    recipients  : true
                },
                apply   : function(){
                    var allows = pure.components.messenger.mails.create.access.allows,
                        titles = pure.components.messenger.mails.create.nodes.data.titles;
                    if (allows.subject === true){
                        pure.components.messenger.mails.create.nodes.data.subject.disabled = false;
                        titles.subject.removeAttribute('data-addition-disabled-field');
                    }else{
                        pure.components.messenger.mails.create.nodes.data.subject.disabled = true;
                        titles.subject.setAttribute('data-addition-disabled-field', 'true');
                    }
                    if (allows.recipients === true){
                        titles.recipients.removeAttribute('data-addition-disabled-field');
                    }else{
                        titles.recipients.setAttribute('data-addition-disabled-field', 'true');
                    }
                }
            },
            send        : {
                isPossible  : function(){
                    var result = true;
                    result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.mails.send'  ) === null ? false : true));
                    return result;
                },
                send        : function(event, button){
                    var instance        = pure.components.messenger.mails.editor.get(),
                        message         = null,
                        subject         = null,
                        request         = null,
                        current         = pure.components.messenger.mails.create.current.get(),
                        attachments_key = pure.components.messenger.mails.create.attaches.key.get(false);
                    if (instance !== null && pure.components.messenger.mails.create.send.isPossible() !== false &&
                        pure.components.messenger.mails.create.current.isSending() === false){
                        request     = pure.components.messenger.configuration.requests.mails.send;
                        message     = instance.getContent();
                        subject     = pure.components.messenger.mails.create.nodes.data.subject.value;
                        if (message !== '' && subject !== '' && current.recipients.length > 0){
                            message = pure.convertor.UTF8.  encode(message);
                            message = pure.convertor.BASE64.encode(message);
                            subject = pure.convertor.UTF8.  encode(subject);
                            subject = pure.convertor.BASE64.encode(subject);
                            if (message.length > pure.components.messenger.configuration.mails.maxSize){
                                pure.components.messenger.mails.dialogs.info(
                                    'Cannot send this message',
                                    'Message is too big. You should try to use only ' + pure.components.messenger.configuration.mails.maxSize + ' symbols per message.'
                                );
                                return false;
                            }
                            if (subject.length > pure.components.messenger.configuration.mails.maxSubjectSize){
                                pure.components.messenger.mails.dialogs.info(
                                    'Cannot send this message',
                                    'Subject is too big. You should try to use only ' + pure.components.messenger.configuration.mails.maxSubjectSize + ' symbols'
                                );
                                return false;
                            }
                            pure.components.messenger.mails.create.current.progress = pure.templates.progressbar.B.show(button);
                            request = request.replace(/\[message_id\]/,         current.message_id                                  );
                            request = request.replace(/\[message\]/,            message                                             );
                            request = request.replace(/\[subject\]/,            subject                                             );
                            request = request.replace(/\[recipients\]/,         current.recipients.join(',')                        );
                            request = request.replace(/\[attachments_key\]/,    (attachments_key === null ? '' : attachments_key)   );
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : pure.components.messenger.configuration.requestURL,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.components.messenger.mails.create.send.onRecieve(id_request, response);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.components.messenger.mails.create.send.onError(event, id_request);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.components.messenger.mails.create.send.onError(event, id_request);
                                }
                            });
                        }
                    }
                },
                onRecieve   : function(id_request, response){
                    var message = null;
                    switch (response){
                        case 'no access':
                            pure.components.messenger.mails.dialogs.info('Authorization error.', 'Try login again.');
                            break;
                        case 'fail during sending':
                            pure.components.messenger.mails.dialogs.info('Server error.', 'Server did not save message. There are some error.');
                            break;
                        case 'bad message or subject':
                            pure.components.messenger.mails.dialogs.info('Server error.', 'Text of message or/and text of subject has some denied symbols or is emptry.');
                            break;
                        case 'cannot find thread_id':
                            pure.components.messenger.mails.dialogs.info('Server error.', 'Cannot send message, because server cannot assign thread with message.');
                            break;
                        case 'too big message':
                            pure.components.messenger.mails.dialogs.info('Server error.', 'Message is too big. Server did not save it.');
                            break;
                        case 'too big subject':
                            pure.components.messenger.mails.dialogs.info('Server error.', 'Subject of message is too big. Server did not save it.');
                            break;
                        default :
                            try{
                                message = JSON.parse(response);
                                pure.components.messenger.mails.common.addNew(message);
                                //Server nofification
                                pure.appevents.Actions.call('webSocketsServer','wakeup', null, null);
                            }catch (e){
                                pure.components.messenger.mails.dialogs.info('Connection error.', 'Server sent wrong answer: [' + response + ']. Cannot decode response');
                            }
                            break;
                    }
                    pure.templates.progressbar.B.hide(pure.components.messenger.mails.create.current.progress);
                    pure.components.messenger.mails.create.current.progress = null;
                    pure.components.messenger.mails.create.buttons.events.cancel();
                },
                onError     : function(event, id_request){
                    pure.components.messenger.mails.dialogs.info('Connection error.', 'Cannot connect to server.');
                    pure.templates.progressbar.B.hide(pure.components.messenger.mails.create.current.progress);
                    pure.components.messenger.mails.create.current.progress = null;
                }
            },
            buttons     : {
                data    : null,
                find    : function(){
                    if (pure.components.messenger.mails.create.buttons.data === null){
                        pure.components.messenger.mails.create.buttons.data = {
                            recipients  : pure.nodes.select.first('*[data-mails-engine-element="buttons.recipients"]'           ),
                            cancel      : pure.nodes.select.first('*[data-mails-engine-element="buttons.cancel"]'               ),
                            send        : pure.nodes.select.first('*[data-mails-engine-element="buttons.send"]'                 )
                            //attach      : pure.nodes.select.first('*[data-mails-engine-element="buttons.attach"]:not(input)'    ),
                            //attacher    : pure.nodes.select.first('input[data-mails-engine-element="buttons.attach"]'           )
                        };
                    }
                },
                events  : {
                    append      : function(){
                        var data = pure.components.messenger.mails.create.buttons.data;
                        if (data !== null){
                            pure.events.add(
                                data.recipients,
                                'click',
                                pure.components.messenger.mails.create.buttons.events.recipients
                            );
                            pure.events.add(
                                data.cancel,
                                'click',
                                pure.components.messenger.mails.create.buttons.events.cancel
                            );
                            pure.events.add(
                                data.send,
                                'click',
                                function(event){
                                    pure.components.messenger.mails.create.buttons.events.send(event, data.send);
                                }

                            );
                        }
                    },
                    recipients  : function(){
                        var allows = pure.components.messenger.mails.create.access.allows;
                        if (allows.recipients === true && pure.components.messenger.mails.create.current.isSending() === false){
                            pure.components.messenger.users.select.select(
                                function(users){
                                    pure.components.messenger.mails.create.recipients.actions.clear();
                                    for (var index = users.length - 1; index >= 0; index -= 1){
                                        pure.components.messenger.mails.create.recipients.actions.add(users[index]);
                                    }
                                    pure.components.messenger.mails.create.recipients.current.set(users);
                                },
                                pure.components.messenger.mails.create.recipients.current.get()
                            );
                        }
                    },
                    cancel      : function(){
                        if (pure.components.messenger.mails.create.current.isSending() === false){
                            pure.components.messenger.mails.switchers.switchTo('back');
                        }
                    },
                    send        : function(event, button){
                        if (pure.components.messenger.mails.create.current.isSending() === false){
                            pure.components.messenger.mails.create.send.send(event, button);
                        }
                    },
                    attach      : function(event, input, button){
                        pure.components.messenger.mails.create.attaches.onClick(event, input, button);
                    }
                },
                init    : function(){
                    pure.components.messenger.mails.create.buttons.         find();
                    pure.components.messenger.mails.create.buttons.events.  append();
                }
            },
            attaches    : {
                key : {
                    data    : null,
                    get     : function(generate){
                        var generate = (typeof generate === 'boolean' ? generate : true);
                        if (pure.components.messenger.mails.create.attaches.key.data === null && generate !== false){
                            pure.components.messenger.mails.create.attaches.key.data = pure.tools.IDs.get('attachment_key');
                        }
                        return pure.components.messenger.mails.create.attaches.key.data;
                    },
                    reset   : function(){
                        pure.components.messenger.mails.create.attaches.key.data = null;
                    }
                },
                template : {
                    data        : null,
                    find        : function(){
                        var template    = pure.nodes.select.first('*[data-mails-engine-template="attachment"]'),
                            mark        = null;
                        if (template !== null && pure.components.messenger.mails.create.attaches.template.data === null){
                            mark                = document.createElement('DIV');
                            mark.style.display  = 'none';
                            template.parentNode.insertBefore(mark, template);
                            pure.components.messenger.mails.create.attaches.template.data = {
                                nodeName    : template.nodeName,
                                innerHTML   : template.innerHTML,
                                attributes  : pure.nodes.attributes.get(template, ['data-mails-engine-template']),
                                mark        : mark,
                                key         : pure.tools.IDs.get('attachment_key')
                            };
                            template.parentNode.removeChild(template);
                        }
                    },
                    add         : function(){
                        var data    = pure.components.messenger.mails.create.attaches.template.data,
                            item    = null,
                            nodes   = null,
                            id      = pure.tools.IDs.get('mails_attachment');
                        if (data !== null){
                            item                = document.createElement(data.nodeName);
                            item.innerHTML      = data.innerHTML;
                            item.style.display  = 'none';
                            pure.nodes.attributes.set(item, data.attributes);
                            item.setAttribute('data-mail-attachment-id', id);
                            data.mark.parentNode.insertBefore(item, data.mark);
                            nodes = {
                                input       : pure.nodes.select.first('*[data-mail-attachment-id="' + id + '"] *[data-mails-engine-element="attachment.input"]' ),
                                name        : pure.nodes.select.first('*[data-mail-attachment-id="' + id + '"] *[data-mails-engine-element="attachment.name"]'  ),
                                remove      : pure.nodes.select.first('*[data-mail-attachment-id="' + id + '"] *[data-mails-engine-element="attachment.remove"]'),
                                container   : item,
                                id          : id
                            };
                            if (pure.tools.objects.isValueIn(nodes, null) === false){
                                pure.events.add(
                                    nodes.input,
                                    'change',
                                    function(event){
                                        pure.components.messenger.mails.create.attaches.events.onInputChange(
                                            event,
                                            nodes,
                                            pure.components.messenger.mails.create.attaches.key.get()
                                        );
                                    }
                                );
                                pure.events.call(nodes.input, 'click');
                                pure.events.add(
                                    nodes.remove,
                                    'click',
                                    function(){
                                        pure.components.messenger.mails.create.attaches.remove.send(
                                            pure.components.messenger.mails.create.attaches.key.get(),
                                            nodes
                                        );
                                    }
                                );
                            }else{
                                item.parentNode.removeChild(item);
                            }
                        }
                    },
                    remove      : function(nodes){
                        if (typeof nodes.container.parentNode !== 'undefined'){
                            if (nodes.container.parentNode !== null){
                                nodes.container.parentNode.removeChild(nodes.container);
                            }
                        }
                    },
                    reset       : function(){
                        var attaches = pure.nodes.select.all('*[data-mail-attachment-id]');
                        if (attaches !== null){
                            for(var index = attaches.length - 1; index >= 0; index -= 1){
                                attaches[index].parentNode.removeChild(attaches[index]);
                            }
                        }
                    }
                },
                send    : {
                    send        : function(key, nodes){
                        nodes.progress = pure.templates.progressbar.A.show(nodes.container, 'background:rgba(255,255,255,0.8);');
                        pure.components.uploader.module.upload(
                            nodes.input.files[0],
                            pure.components.messenger.configuration.requestURL,
                            {
                                ready : function(params){
                                    pure.components.messenger.mails.create.attaches.send.onRecieve(params, key, nodes);
                                },
                                error : function(params){
                                    pure.components.messenger.mails.create.attaches.send.onError(params, key, nodes);
                                },
                                timeout : function(params){
                                    pure.components.messenger.mails.create.attaches.send.onError(params, key, nodes);
                                }
                            },
                            null,
                            'attachment',
                            [
                                { name:'command',   value: pure.components.messenger.configuration.requests.mails.attachment.preload    },
                                { name:'user_id',   value: pure.components.messenger.configuration.user_id                              },
                                { name:'key',       value: key                                                                          }
                            ]
                        );
                    },
                    onRecieve   : function(params, key, nodes){
                        var attachment_id = null;
                        pure.templates.progressbar.A.hide(nodes.progress);
                        switch (params.response){
                            case 'fail to save':
                                pure.components.messenger.mails.dialogs.info('Server error', 'Cannot save file on server.');
                                pure.components.messenger.mails.create.attaches.template.remove(nodes);
                                break;
                            case 'file not loaded':
                                pure.components.messenger.mails.dialogs.info('Server error', 'Cannot load file on server.');
                                pure.components.messenger.mails.create.attaches.template.remove(nodes);
                                break;
                            case 'too big file':
                                pure.components.messenger.mails.dialogs.info('Server error', 'File is too big.');
                                pure.components.messenger.mails.create.attaches.template.remove(nodes);
                                break;
                            case 'file not found':
                                pure.components.messenger.mails.dialogs.info('Server error', 'Cannot detect file on server side.');
                                pure.components.messenger.mails.create.attaches.template.remove(nodes);
                                break;
                            case 'attachments are not allowed':
                                pure.components.messenger.mails.dialogs.info('Server error', 'Sorry, you cannot attach file to message. Attachments in mails are not allowed.');
                                pure.components.messenger.mails.create.attaches.template.remove(nodes);
                                break;
                            case 'no access':
                                pure.components.messenger.mails.dialogs.info('Server error', 'Sorry, you have not access.');
                                pure.components.messenger.mails.create.attaches.template.remove(nodes);
                                break;
                            case 'too many attachments':
                                pure.components.messenger.mails.dialogs.info('Server error', 'Sorry, too many attachments for one message.');
                                pure.components.messenger.mails.create.attaches.template.remove(nodes);
                                break;
                            default:
                                //Success. Should be number (id of attachment)
                                attachment_id = parseInt(params.response, 10);
                                if (attachment_id > 0){
                                    nodes.container.setAttribute('data-attachment-server-id', attachment_id);
                                }else{
                                    pure.components.messenger.mails.dialogs.info('Server error', 'On server some unkown error is. Please, contact with administrator.');
                                    pure.components.messenger.mails.create.attaches.template.remove(nodes);
                                }
                                break;
                        }
                    },
                    onError     : function(params, key, nodes){
                        pure.templates.progressbar.A.hide(nodes.progress);
                        pure.components.messenger.mails.dialogs.info('Server error', 'Sorry, but server did not answer for request. No connection.');
                        pure.components.messenger.mails.create.attaches.template.remove(nodes);
                    }
                },
                remove  : {
                    send : function(key, nodes){
                        var attachment_id   = nodes.container.getAttribute('data-attachment-server-id'),
                            request         = pure.components.messenger.configuration.requests.mails.attachment.remove;
                        if (attachment_id !== null && attachment_id !== ''){
                            attachment_id = parseInt(attachment_id, 10);
                            if (attachment_id > 0){
                                nodes.progress  = pure.templates.progressbar.A.show(nodes.container, 'background:rgba(255,255,255,0.8);');
                                request         = request.replace(/\[attachment_id\]/, attachment_id);
                                pure.tools.request.send({
                                    type        : 'POST',
                                    url         : pure.components.messenger.configuration.requestURL,
                                    request     : request,
                                    onrecieve   : function (id_request, response) {
                                        pure.components.messenger.mails.create.attaches.remove.onRecieve(id_request, response, nodes);
                                    },
                                    onreaction  : null,
                                    onerror     : function (event, id_request) {
                                        pure.components.messenger.mails.create.attaches.remove.onError(event, id_request, nodes);
                                    },
                                    ontimeout   : function (event, id_request) {
                                        pure.components.messenger.mails.create.attaches.remove.onError(event, id_request, nodes);
                                    }
                                });
                                return true;
                            }
                        }
                        pure.components.messenger.mails.dialogs.info('Client error', 'Cannot find ID of attachment. Please contact with administrator.');
                        return false;
                    },
                    onRecieve : function(id_request, response, nodes){
                        pure.templates.progressbar.A.hide(nodes.progress);
                        if (response === 'success'){
                            pure.components.messenger.mails.create.attaches.template.remove(nodes);
                        }else{
                            pure.components.messenger.mails.dialogs.info('Server error', 'Cannot remove attachment from server.');
                        }
                    },
                    onError : function(event, id_request, nodes){
                        pure.templates.progressbar.A.hide(nodes.progress);
                        pure.components.messenger.mails.dialogs.info('Server error', 'Sorry, but server did not answer for request. No connection.');
                    }
                },
                events  : {
                    onInputChange  : function(event, nodes, key){
                        function RemoveAttachment(nodes){
                            nodes.container.parentNode.removeChild(nodes.container);
                        };
                        var ext = null;
                        if (typeof nodes.input.files !== 'undefined' && typeof nodes.input.value === 'string') {
                            if (nodes.input.files.length === 1) {
                                ext = (nodes.input.value.match(/\.([^\.]+)$/)[1]).toLowerCase();
                                if (ext !== 'exe' && ext !== 'bat' && ext !== 'cmd') {
                                    if (nodes.input.files[0].size > pure.components.messenger.configuration.mails.attachmentMaxSize){
                                        pure.components.messenger.mails.dialogs.info('Warning', 'Sorry, but you can attach only files which has size not more ' +pure.components.messenger.configuration.mails.attachmentMaxSize + ' bytes');
                                        RemoveAttachment(nodes);
                                    }else{
                                        nodes.name.innerHTML            = nodes.input.value.replace(/[<>]/gi, '');
                                        nodes.container.style.display   = '';
                                        pure.components.messenger.mails.create.attaches.send.send(key, nodes);
                                    }
                                }else{
                                    RemoveAttachment(nodes);
                                }
                            }else{
                                RemoveAttachment(nodes);
                            }
                        }else{
                            RemoveAttachment(nodes);
                        }
                    }
                },
                attach : {
                    init    : function(){
                        var button = pure.nodes.select.first('*[data-mails-engine-element="buttons.attach"]:not(input)');
                        if (button !== null){
                            if (pure.components.messenger.configuration.mails.allowAttachment === 'off'){
                                button.parentNode.removeChild(button);
                            }else if(pure.components.messenger.configuration.mails.allowAttachment === 'on'){
                                pure.events.add(
                                    button,
                                    'click',
                                    function(event){
                                        pure.components.messenger.mails.create.attaches.attach.add(event);
                                    }

                                );
                            }
                        }
                    },
                    add     : function(event){
                        var attaches = pure.nodes.select.all('*[data-mails-engine-element="attachment.input"]');
                        if (attaches !== null){
                            if (attaches.length < parseInt(pure.components.messenger.configuration.mails.attachmentsMaxCount, 10)){
                                pure.components.messenger.mails.create.attaches.template.add();
                            }else{
                                pure.components.messenger.mails.dialogs.info('Warning', 'Sorry, but you can attach more than ' + pure.components.messenger.configuration.mails.attachmentsMaxCount + ' file(s) per one message.');
                            }
                        }
                    }
                },
                init    : function(){
                    pure.components.messenger.mails.create.attaches.template.   find();
                    pure.components.messenger.mails.create.attaches.attach.     init();
                }
            },
            nodes       : {
                data    : null,
                find    : function(){
                    if (pure.components.messenger.mails.create.nodes.data === null){
                        pure.components.messenger.mails.create.nodes.data = {
                            subject : pure.nodes.select.first('*[data-mails-engine-element="create.subject"]'),
                            titles  : {
                                recipients  : pure.nodes.select.first('*[data-mails-engine-element="create.title.recipients"]'  ),
                                subject     : pure.nodes.select.first('*[data-mails-engine-element="create.title.subject"]'     )
                            }
                        };
                    }
                }
            },
            recipients  : {
                data    : null,
                init    : function(){
                    var template    = pure.nodes.select.first('*[data-mails-engine-template="create.user"]'             ),
                        container   = pure.nodes.select.first('*[data-mails-engine-element="create.users.container"]'   );
                    if (template !== null && container !== null &&
                        pure.components.messenger.mails.create.recipients.data  === null){
                        pure.components.messenger.mails.create.recipients.data = {
                            nodeName    : template.nodeName,
                            innerHTML   : template.innerHTML,
                            attributes  : pure.nodes.attributes.get(template, ['data-mails-engine-template']),
                            container   : container
                        };
                        template.parentNode.removeChild(template);
                    }
                },
                actions : {
                    clear   : function(){
                        var data = pure.components.messenger.mails.create.recipients.data;
                        if (data !== null){
                            data.container.innerHTML = '';
                            pure.components.messenger.mails.create.recipients.current.reset();
                        }
                    },
                    add     : function(user){
                        var data    = pure.components.messenger.mails.create.recipients.data,
                            nodes   = null,
                            node    = null,
                            allows  = pure.components.messenger.mails.create.access.allows;
                        if (data !== null){
                            node            = document.createElement(data.nodeName);
                            node.innerHTML  = data.innerHTML;
                            pure.nodes.attributes.set(node, data.attributes);
                            node.setAttribute('data-mails-engine-recipient-id', user.id);
                            data.container.appendChild(node);
                            nodes = {
                                name    : pure.nodes.select.first('*[data-mails-engine-recipient-id="' + user.id + '"]' + ' *[data-mails-engine-template-item="name"]'      ),
                                avatar  : pure.nodes.select.first('*[data-mails-engine-recipient-id="' + user.id + '"]' + ' *[data-mails-engine-template-item="avatar"]'    ),
                                remove  : pure.nodes.select.first('*[data-mails-engine-recipient-id="' + user.id + '"]' + ' *[data-mails-engine-template-item="remove"]'    )
                            };
                            node.style.display = 'none';
                            if (pure.tools.objects.isValueIn(nodes, null) === false){
                                nodes.name.innerHTML                = user.name.replace(/\s/, '<br />');
                                nodes.avatar.style.backgroundImage  = 'url(' + user.avatar + ')';
                                node.style.display                  = '';
                                if (allows.recipients === true){
                                    pure.events.add(
                                        nodes.remove,
                                        'click',
                                        function(){
                                            node.parentNode.removeChild(node);
                                            pure.components.messenger.mails.create.recipients.current.remove(user.id);
                                            pure.components.messenger.mails.create.current.removeRecipient(parseInt(user.id, 10));
                                        }
                                    );
                                }else{
                                    nodes.remove.parentNode.removeChild(nodes.remove);
                                }
                                pure.components.messenger.mails.create.current.addRecipient(parseInt(user.id, 10));
                            }else{
                                node.parentNode.removeChild(node);
                            }
                        }
                    }
                },
                current : {
                    data    : null,
                    set     : function(users){
                        pure.components.messenger.mails.create.recipients.current.data = pure.tools.arrays.copy(users);
                    },
                    get     : function(){
                        if (pure.components.messenger.mails.create.recipients.current.data instanceof Array){
                            return pure.tools.arrays.copy(
                                pure.components.messenger.mails.create.recipients.current.data
                            );
                        }
                        return [];
                    },
                    remove  : function(id){
                        var data = pure.components.messenger.mails.create.recipients.current.data;
                        if (data instanceof Array){
                            for(var index = data.length - 1; index >= 0; index -= 1){
                                if (data[index].id === id){
                                    data.splice(index, 1);
                                    return true;
                                }
                            }
                        }
                        return false;
                    },
                    reset   : function(){
                        pure.components.messenger.mails.create.recipients.current.data = null;
                    }
                }
            },
            reset       : function(){
                var instance = pure.components.messenger.mails.editor.get();
                if (instance !== null){
                    pure.components.messenger.mails.create.recipients.actions.  clear();
                    pure.components.messenger.mails.create.current.             reset();
                    pure.components.messenger.mails.create.attaches.key.        reset();
                    pure.components.messenger.mails.create.attaches.template.   reset();
                    pure.components.messenger.mails.create.nodes.data.subject.value = '';
                    instance.setContent('');
                }
            },
            open        : function(users, subject, message_id){
                var allows = pure.components.messenger.mails.create.access.allows;
                //set access
                allows.recipients   = false;
                allows.subject      = false;
                pure.components.messenger.mails.create.access.apply();
                //clear
                pure.components.messenger.mails.create.reset();
                //open dialog
                pure.components.messenger.mails.switchers.switchTo('create');
                //add recipients
                for(var index = users.length - 1; index >= 0; index -= 1){
                    pure.components.messenger.mails.create.recipients.actions.add(users[index]);
                }
                //set subject
                pure.components.messenger.mails.create.nodes.data.subject.value = subject;
                //set message id
                pure.components.messenger.mails.create.current.setMessageID(parseInt(message_id, 10));
            },
            mailTo      : function(user){
                var allows  = pure.components.messenger.mails.create.access.allows,
                    user    = (typeof user === 'object' ? user : null);
                if (user !== null){
                    if (typeof user.id === 'number' && typeof user.name === 'string' && typeof user.avatar === 'string' ){
                        //set access
                        allows.recipients   = false;
                        allows.subject      = true;
                        pure.components.messenger.mails.create.access.apply();
                        //clear
                        pure.components.messenger.mails.create.reset();
                        //open dialog
                        pure.components.messenger.mails.switchers.switchTo('create');
                        //add recipients
                        pure.components.messenger.mails.create.recipients.actions.add(user);
                    }
                }
            },
            init        : function(){
                pure.components.messenger.mails.create.nodes.       find();
                pure.components.messenger.mails.create.buttons.     init();
                pure.components.messenger.mails.create.recipients.  init();
                pure.components.messenger.mails.create.attaches.    init();
            }
        },
        update      : {
            init        : function(){
                pure.appevents.Actions.listen(
                    'webSocketServerEvents',
                    'mail_message',
                    pure.components.messenger.mails.update.processing,
                    'mail_update_handle'
                );
            },
            processing  : function(params){
                var parameters = (typeof params.parameters === 'object' ? params.parameters : null);
                if (parameters !== null){
                    if (typeof parameters.message_id    !== 'undefined' &&
                        typeof parameters.created       !== 'undefined' &&
                        typeof parameters.thread_id     !== 'undefined'){
                        pure.components.messenger.mails.update.send(parameters.thread_id, parameters.created);
                    }
                }
            },
            send        : function(thread_id, date){
                var request = pure.components.messenger.configuration.requests.mails.inboxByThreadAfter;
                request = request.replace(/\[thread_id\]/,  thread_id   );
                request = request.replace(/\[date\]/,       date        );
                pure.tools.request.send({
                    type        : 'POST',
                    url         : pure.components.messenger.configuration.requestURL,
                    request     : request,
                    onrecieve   : function (id_request, response) {
                        pure.components.messenger.mails.update.onRecieve(id_request, response, thread_id);
                    },
                    onreaction  : null,
                    onerror     : function (event, id_request) {
                        pure.components.messenger.mails.update.onError(event, id_request);
                    },
                    ontimeout   : function (event, id_request) {
                        pure.components.messenger.mails.update.onError(event, id_request);
                    }
                });
            },
            onRecieve   : function(id_request, response, thread_id){
                var data = null;
                if (response !== 'no access'){
                    try{
                        data = JSON.parse(response);
                        if (data !== false){
                            if (data instanceof Array){
                                for (var index = data.length - 1; index >= 0; index -= 1){
                                    pure.components.messenger.mails.common.addNewInbox(data[index]);
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
            },
            thread : {
                update : function(thread_id){
                    pure.components.messenger.mails.update.thread.send(thread_id);
                },
                send        : function(thread_id){
                    var request = pure.components.messenger.configuration.requests.mails.messagesOfThread;
                    request = request.replace(/\[thread_id\]/,  thread_id   );
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : pure.components.messenger.configuration.requestURL,
                        request     : request,
                        onrecieve   : function (id_request, response) {
                            pure.components.messenger.mails.update.thread.onRecieve(id_request, response, thread_id);
                        },
                        onreaction  : null,
                        onerror     : function (event, id_request) {
                            pure.components.messenger.mails.update.thread.onError(event, id_request);
                        },
                        ontimeout   : function (event, id_request) {
                            pure.components.messenger.mails.update.thread.onError(event, id_request);
                        }
                    });
                },
                onRecieve   : function(id_request, response, thread_id){
                    var data    = null,
                        message = null;
                    if (response !== 'no access'){
                        try{
                            data = JSON.parse(response);
                            if (data !== false){
                                if (data instanceof Array){
                                    message         = pure.tools.objects.copy(null, data[data.length - 1]);
                                    message.nested  = [];
                                    for (var index = 0, max_index = data.length - 1; index < max_index; index += 1){
                                        message.nested.push(pure.tools.objects.copy(null, data[index]));
                                    }
                                    pure.components.messenger.mails.common.addNewInbox(message);
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
            }
        },
        readUnread  : {
            defaults    : function(message, subject){
                if (parseInt(message.is_unread, 10) === 1){
                    subject.setAttribute('data-mails-attribute-unread', 'true');
                }else{
                    subject.setAttribute('data-mails-attribute-unread', 'false');
                }
            },
            update      : function(message, subject){
                if (parseInt(message.is_unread, 10) === 1){
                    message.is_unread = 0;
                    pure.components.messenger.mails.data.inbox.                 setAsRead(message.thread_id, message.message_id);
                    pure.components.messenger.mails.readUnread.                 defaults(message, subject);
                    pure.components.messenger.mails.readUnread.request.         send(parseInt(message.message_id, 10));
                    pure.components.messenger.mails.readUnread.counter.storage. change(message.thread_id, -1);
                    pure.components.messenger.mails.readUnread.counter.         update();
                    pure.components.messenger.mails.templates.inbox.basic.      updateCounts(message.thread_id);
                    pure.components.messenger.mails.templates.inbox.external.   remove(message.message_id);
                }
            },
            request     : {
                send        : function(messageID){
                    var request     = pure.components.messenger.configuration.requests.mails.updateReadUnread,
                        messageID   = (typeof messageID === 'number' ? messageID : null);
                    if (messageID !== null){
                        request = request.replace('[message_id]', messageID);
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.components.messenger.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.components.messenger.mails.readUnread.request.onRecieve(id_request, response, messageID);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.components.messenger.mails.readUnread.request.onError(event, id_request, messageID);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.components.messenger.mails.readUnread.request.onError(event, id_request, messageID);
                            }
                        });
                    }
                },
                onRecieve   : function(id_request, response, message_id){
                    if (response === 'success'){
                        //success :: Do nothing
                    }else{
                        //fail :: Do nothing
                    }
                },
                onError     : function(event, id_request, messageID){
                    //Do nothing
                }
            },
            counter      : {
                storage    : {
                    data    : null,
                    upload  : function(data){
                        var _data = null;
                        pure.components.messenger.mails.readUnread.counter.storage.data = [];
                        _data = pure.components.messenger.mails.readUnread.counter.storage.data;
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
                        var data = pure.components.messenger.mails.readUnread.counter.storage.data;
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
                        var data = pure.components.messenger.mails.readUnread.counter.storage.data;
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
                                    count       : (value > 0 ? value : 0),
                                    thread_id   : thread_id
                                });
                            }
                        }
                        return false;
                    },
                    total   : function(){
                        var data    = pure.components.messenger.mails.readUnread.counter.storage.data,
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
                update : function(){
                    var counters    = pure.nodes.select.all('*[data-messenger-engine-counter="mails"]'),
                        total       = pure.components.messenger.mails.readUnread.counter.storage.total();
                    if (counters !== null){
                        for(var index = counters.length - 1; index >= 0; index -= 1){
                            counters[index].innerHTML = (total === 0 ? '' : total);
                        }
                        pure.components.messenger.mails.templates.inbox.external.update();
                    }
                },
                request     : {
                    send        : function(){
                        var request     = pure.components.messenger.configuration.requests.mails.getUnreadCount;
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.components.messenger.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.components.messenger.mails.readUnread.counter.request.onRecieve(id_request, response);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.components.messenger.mails.readUnread.counter.request.onError(event, id_request);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.components.messenger.mails.readUnread.counter.request.onError(event, id_request);
                            }
                        });
                    },
                    onRecieve   : function(id_request, response){
                        var data = null;
                        if (response !== 'no access' && response !== 'fail'){
                            try{
                                data = JSON.parse(response);
                                if (data instanceof Array){
                                    pure.components.messenger.mails.readUnread.counter.storage.upload(data);
                                    pure.components.messenger.mails.readUnread.counter.update();
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
                    pure.components.messenger.mails.readUnread.counter.request.send();
                }
            }
        },
        editor      : {
            id      : null,
            init    : function(){
                var container = pure.nodes.select.first('*[data-mails-engine-element="mail.editor"]');
                if (container !== null){
                    tinyMCE.init({
                        selector                : '*[data-mails-engine-element="mail.editor"]',
                        menubar                 : false,
                        skin                    : 'lightgray',
                        theme                   : 'modern',
                        plugins                 : 'wplink',
                        init_instance_callback  : function(editor) {
                            pure.components.messenger.mails.editor.id = editor.id;
                        }
                    });
                }
                return false;
            },
            get : function(){
                if (pure.components.messenger.mails.editor.id !== null){
                    return tinyMCE.get(pure.components.messenger.mails.editor.id);
                }
                return null;
            }
        },
        dialogs     : {
            info : function(title, message){
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

            },
            message : function(subject, message){
                pure.components.dialogs.B.open({
                    title       : subject,
                    innerHTML   : '<div data-element-type="Pure.Messenger.Mails.FullView.Content">' + message + '</div>',
                    width       : 70,
                    parent      : document.body,
                    fullHeight  : true,
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
        mailTo      : function(user){
            pure.components.messenger.mails.create.mailTo(user);
        },
        init        : function(){
            pure.components.messenger.mails.templates.          init();
            pure.components.messenger.mails.switchers.          init();
            pure.components.messenger.mails.create.             init();
            pure.components.messenger.mails.editor.             init();
            pure.components.messenger.mails.readUnread.counter. init();
            pure.components.messenger.mails.update.             init();
            pure.components.messenger.mails.loader.     getMessages('inbox');
            pure.components.messenger.mails.loader.     getMessages('outbox');
        }
    };
}());