(function () {
    if (typeof window.pure                      !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components           !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.messenger !== "object") { window.pure.components.messenger    = {}; }
    "use strict";
    window.pure.components.messenger.notifications = {
        loader      : {
            isPossible          : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.user_id'                               ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requestURL'                            ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.notifications.notificationsMaxCount'   ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.components.messenger.configuration.requests.notifications.get'            ) === null ? false : true));
                return result;
            },
            getNotifications    : {
                send        : function(){
                    var request = pure.components.messenger.configuration.requests.notifications.get;
                    if (pure.components.messenger.notifications.loader.isPossible() !== false){
                        pure.components.messenger.module.progress.show();
                        request = request.replace(/\[shown\]/, 0);
                        request = request.replace(/\[maxcount\]/, pure.components.messenger.configuration.notifications.notificationsMaxCount);
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.components.messenger.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.components.messenger.notifications.loader.getNotifications.onRecieve(id_request, response);
                                pure.components.messenger.module.progress.hide();
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.components.messenger.notifications.loader.getNotifications.onError(event, id_request);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.components.messenger.notifications.loader.getNotifications.onError(event, id_request);
                            }
                        });
                    }
                },
                onRecieve   : function(id_request, response){
                    var data = null;
                    if (response !== 'no access'){
                        try{
                            data = JSON.parse(response);
                            pure.components.messenger.notifications.render.addFromLoader(data.notifications);
                            pure.components.messenger.notifications.render.addExternal(data.notifications);
                            pure.components.messenger.notifications.loader.more.change(
                                parseInt(data.shown, 10),
                                parseInt(data.total, 10)
                            );
                            pure.components.messenger.notifications.render.more.update();
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
            more                : {
                data : {
                    shown : 0,
                    total : 0
                },
                change  : function(shown, total){
                    var data = pure.components.messenger.notifications.loader.more.data;
                    data.shown += shown;
                    data.total += total;
                },
                init    : function(){
                    var button = pure.nodes.select.first('*[data-notification-engine-template="more.button"]');
                    if (button !== null){
                        pure.events.add(
                            button,
                            'click',
                            function(){
                                pure.components.messenger.notifications.loader.more.request.send(button);
                            }
                        );
                    }
                },
                request : {
                    progress    : null,
                    send        : function(button){
                        var request = pure.components.messenger.configuration.requests.notifications.get,
                            data    = pure.components.messenger.notifications.loader.more.data;
                        if (pure.components.messenger.notifications.loader.more.request.progress === null){
                            if (data.shown < data.total){
                                if (pure.components.messenger.notifications.loader.isPossible() !== false){
                                    pure.components.messenger.notifications.loader.more.request.progress = pure.templates.progressbar.B.show(button);
                                    request = request.replace(/\[shown\]/,      data.shown);
                                    request = request.replace(/\[maxcount\]/,   pure.components.messenger.configuration.notifications.notificationsMaxCount);
                                    pure.tools.request.send({
                                        type        : 'POST',
                                        url         : pure.components.messenger.configuration.requestURL,
                                        request     : request,
                                        onrecieve   : function (id_request, response) {
                                            pure.components.messenger.notifications.loader.more.request.onRecieve(id_request, response);
                                        },
                                        onreaction  : null,
                                        onerror     : function (event, id_request) {
                                            pure.components.messenger.notifications.loader.more.request.onError(event, id_request);
                                        },
                                        ontimeout   : function (event, id_request) {
                                            pure.components.messenger.notifications.loader.more.request.onError(event, id_request);
                                        }
                                    });
                                }
                            }
                        }
                    },
                    onRecieve   : function(id_request, response){
                        var data = null;
                        if (response !== 'no access'){
                            try{
                                data = JSON.parse(response);
                                pure.components.messenger.notifications.render.addFromLoader(data.notifications);
                                pure.components.messenger.notifications.render.addExternal(data.notifications);
                                pure.components.messenger.notifications.loader.more.change(
                                    parseInt(data.shown, 10),
                                    0
                                );
                                pure.components.messenger.notifications.render.more.update();
                            }catch (e){
                            }
                        }
                        pure.templates.progressbar.B.hide(pure.components.messenger.notifications.loader.more.request.progress);
                        pure.components.messenger.notifications.loader.more.request.progress = null;
                    },
                    onError     : function(event, id_request){
                        pure.templates.progressbar.B.hide(pure.components.messenger.notifications.loader.more.request.progress);
                        pure.components.messenger.notifications.loader.more.request.progress = null;
                    }
                }
            },
            init                : function(){
                pure.components.messenger.notifications.loader.getNotifications.send();
                pure.components.messenger.notifications.loader.more.init();
            }
        },
        nodes       : {
            store   : {},
            find    : function(){
                var nodes = pure.components.messenger.notifications.nodes.store;
                nodes.areas = {
                    notifications : pure.nodes.select.first('*[data-notification-engine-element="notifications"]' )
                };
                nodes.more = {
                    shown : pure.nodes.select.first('*[data-notification-engine-element="more.shown"]' ),
                    total : pure.nodes.select.first('*[data-notification-engine-element="more.total"]' )
                };
                return (pure.tools.objects.isValueIn(nodes, null) === false ? true : false);
            }
        },
        render      : {
            addFromLoader   : function(notifications){
                if (notifications instanceof Array){
                    for (var index = 0, max_index = notifications.length; index < max_index; index += 1){
                        pure.components.messenger.notifications.templates.notification.add(notifications[index]);
                    }
                }
            },
            addExternal : function(notifications){
                var shown = pure.components.messenger.notifications.templates.external.data;
                if (shown !== null){
                    shown = shown.shown;
                    if (shown.length < 5){
                        for(var index = notifications.length - 1; index >= 0; index -= 1){
                            if (shown.indexOf(notifications[index].id) === -1){
                                pure.components.messenger.notifications.templates.external.add(notifications[index]);
                                shown.push(notifications[index].id);
                            }
                            if (shown.length >= 5) {
                                pure.components.messenger.notifications.templates.external.progress.hide();
                                return true;
                            }
                        }
                    }
                    pure.components.messenger.notifications.templates.external.progress.hide();
                }
            },
            remove          : function(notification_id){
                var notification = pure.nodes.select.first('*[data-notification-message-id="' + notification_id + '"]');
                if (notification !== null){
                    notification.parentNode.removeChild(notification);
                    pure.components.messenger.notifications.templates.external.remove(notification_id);
                }
            },
            more : {
                update  : function(){
                    var globalNodes = pure.components.messenger.notifications.nodes.store,
                        data        = pure.components.messenger.notifications.loader.more.data;
                    globalNodes.more.shown.innerHTML = data.shown;
                    globalNodes.more.total.innerHTML = data.total;
                    pure.components.messenger.notifications.render.more.counter();
                    pure.components.messenger.notifications.templates.external.update();
                },
                counter : function(){
                    var nodes   = pure.nodes.select.all('*[data-messenger-engine-counter="notifications"]'),
                        data    = pure.components.messenger.notifications.loader.more.data;
                    if (nodes !== null){
                        for(var index = nodes.length - 1; index >= 0; index -= 1){
                            nodes[index].innerHTML = data.total;
                            if (data.total > 0){
                                nodes[index].style.display = '';
                            }else{
                                nodes[index].style.display = 'none';
                            }
                        }
                    }
                }
            }
        },
        templates   : {
            notification : {
                data    : null,
                init    : function(){
                    var template    = pure.nodes.select.first('*[data-notification-engine-template="notification"]'),
                        mark        = null;
                    if (template !== null){
                        mark                = document.createElement('DIV');
                        mark.style.display  = 'none';
                        pure.components.messenger.notifications.templates.notification.data = {
                            nodeName    : template.nodeName,
                            innerHTML   : template.innerHTML,
                            attributes  : pure.nodes.attributes.get(template, ['data-notification-engine-template']),
                            mark        : mark
                        };
                        template.parentNode.insertBefore(mark, template);
                        template.parentNode.removeChild(template);
                    }
                },
                add     : function(notification){
                    var globalNodes = pure.components.messenger.notifications.nodes.store,
                        template    = pure.components.messenger.notifications.templates.notification.data,
                        node        = null,
                        nodes       = null,
                        button      = null;
                    if (globalNodes !== null && template !== null){
                        node            = document.createElement(template.nodeName);
                        node.innerHTML  = template.innerHTML;
                        pure.nodes.attributes.set(node, template.attributes);
                        node.setAttribute('data-notification-message-id', notification.id);
                        template.mark.parentNode.insertBefore(node, template.mark);
                        nodes = {
                            subject     : pure.nodes.select.first(template.nodeName + '[data-notification-message-id="' + notification.id + '"]' + ' *[data-notification-engine-template-item="subject"]'   ),
                            content     : pure.nodes.select.first(template.nodeName + '[data-notification-message-id="' + notification.id + '"]' + ' *[data-notification-engine-template-item="content"]'   ),
                            controls    : pure.nodes.select.first(template.nodeName + '[data-notification-message-id="' + notification.id + '"]' + ' *[data-notification-engine-template-item="controls"]'  ),
                            button      : pure.nodes.select.first(template.nodeName + '[data-notification-message-id="' + notification.id + '"]' + ' *[data-notification-engine-template-item="button"]'    ),
                            date        : pure.nodes.select.first(template.nodeName + '[data-notification-message-id="' + notification.id + '"]' + ' *[data-notification-engine-template-item="date"]'      ),
                            target      : pure.nodes.select.first(template.nodeName + '[data-notification-message-id="' + notification.id + '"]' + ' *[data-notification-engine-template-item="target"]'    )
                        };
                        if (pure.tools.objects.isValueIn(nodes, null) === false){
                            nodes.content.innerHTML = notification.description;
                            nodes.date.innerHTML    = notification.date_notified.replace(/\s/, '<br />');
                            if (typeof notification.target !== 'undefined'){
                                nodes.target.innerHTML = nodes.target.innerHTML.replace(/\[name\]/,         (typeof notification.target.name        === 'string' ? notification.target.name         : '' ));
                                nodes.target.innerHTML = nodes.target.innerHTML.replace(/\[avatar\]/,       (typeof notification.target.avatar      === 'string' ? notification.target.avatar       : '' ));
                                nodes.target.innerHTML = nodes.target.innerHTML.replace(/\[description\]/,  (typeof notification.target.description === 'string' ? notification.target.description  : '' ));
                            }else{
                                nodes.target.parentNode.removeChild(nodes.target);
                            }
                            if (typeof notification.actions !== 'undefined'){
                                if (notification.actions instanceof Array){
                                    for(var index = notification.actions.length - 1; index >= 0; index -= 1){
                                        button              = nodes.button.cloneNode(true);
                                        button.innerHTML    = button.innerHTML.replace(/\[title\]/, notification.actions[index].title);
                                        nodes.controls.appendChild(button);
                                        (function(button, request, response, message, title, notification_id){
                                            pure.events.add(
                                                button,
                                                'click',
                                                function(){
                                                    if (pure.components.messenger.notifications.request.progress.isIn(notification_id) === false){
                                                        pure.components.messenger.notifications.dialogs.confirmation(
                                                            title,
                                                            message,
                                                            function(){
                                                                pure.components.messenger.notifications.request.send(
                                                                    notification_id,
                                                                    request,
                                                                    response,
                                                                    button
                                                                );
                                                            }
                                                        );
                                                    }
                                                }
                                            );
                                        }(
                                            button,
                                            notification.actions[index].request,
                                            notification.actions[index].expected_response,
                                            notification.description,
                                            notification.actions[index].title,
                                            notification.id
                                        ));
                                    }
                                    nodes.button.parentNode.removeChild(nodes.button);
                                }else{
                                    nodes.controls.parentNode.removeChild(nodes.controls);
                                }
                            }else{
                                nodes.controls.parentNode.removeChild(nodes.controls);
                            }
                        }
                    }
                }
            },
            external    : {
                data        : null,
                init        : function(){
                    var templates = {
                            notifications   : pure.nodes.select.all('*[data-notifications-external-engine-template="notification"]'     ),
                            containers      : pure.nodes.select.all('*[data-notifications-external-engine-template="container"]'        ),
                            counts          : pure.nodes.select.all('*[data-notifications-external-engine-template="count"]'            )
                        },
                        progress    = pure.nodes.select.all('*[data-notifications-external-engine-template="progress"]');
                    if (pure.tools.objects.isValueIn(templates, null) == false &&
                        pure.components.messenger.notifications.templates.external.data  === null){
                        pure.components.messenger.notifications.templates.external.data = {
                            notifications   : [],
                            counts          : [],
                            containers      : [],
                            progress        : progress,
                            shown           : []
                        };
                        for(var key in templates){
                            for(var index = templates[key].length - 1; index >= 0; index -= 1){
                                (function(data, node, key){
                                    if (key === 'notifications'){
                                        var mark = document.createElement('DIV');
                                        mark.style.display  = 'none';
                                        data[key].push({
                                            nodeName    : node.nodeName,
                                            innerHTML   : node.innerHTML,
                                            attributes  : pure.nodes.attributes.get(node, ['data-notifications-external-engine-template', 'style']),
                                            mark        : mark
                                        });
                                        node.parentNode.insertBefore(mark, node);
                                        node.parentNode.removeChild(node);
                                    }else{
                                        data[key].push(node);
                                    }
                                }(
                                    pure.components.messenger.notifications.templates.external.data,
                                    templates[key][index],
                                    key
                                ));
                            }
                        }
                        pure.components.messenger.notifications.templates.external.progress.show();
                    }
                },
                add         : function(notification){
                    var data    = pure.components.messenger.notifications.templates.external.data,
                        node    = null,
                        str     = null;
                    if (data !== null){
                        data = data.notifications;
                        for(var index = data.length - 1; index >= 0; index -= 1){
                            str             = notification.description;
                            str             = str.replace(/<.*?>/gim, '');
                            str             = (str.length > 100 ? str.substr(0, 50) + ' ...' : str);
                            node            = document.createElement(data[index].nodeName);
                            node.innerHTML  = data[index].innerHTML;
                            node.innerHTML  = node.innerHTML.replace(/\[date\]/gim,   notification.date_notified   );
                            node.innerHTML  = node.innerHTML.replace(/\[description\]/gim,  str                     );
                            node.setAttribute('data-notifications-external-notification-id', notification.id);
                            pure.nodes.attributes.set(node, data[index].attributes);
                            data[index].mark.parentNode.insertBefore(node, data[index].mark);
                        }
                    }
                },
                remove      : function(notification_id){
                    var notifications   = pure.nodes.select.all('*[data-notifications-external-notification-id="' + notification_id + '"]'),
                        shown           = pure.components.messenger.notifications.templates.external.data;
                    if (notifications !== null && shown !== null){
                        shown = shown.shown;
                        for (var index = notifications.length - 1; index >= 0; index -= 1){
                            notifications[index].parentNode.removeChild(notifications[index]);
                        }
                        if (shown.indexOf(notification_id) !== -1){
                            shown.splice(shown.indexOf(notification_id), 1);
                        }
                    }
                },
                update      : function(){
                    function show(containers, counts){
                        for(var index = containers.length - 1; index >= 0; index -= 1){
                            containers[index].style.display = '';
                        }
                        for(var index = counts.length - 1; index >= 0; index -= 1){
                            counts[index].style.display = '';
                        }
                    };
                    function hide(containers, counts){
                        for(var index = containers.length - 1; index >= 0; index -= 1){
                            containers[index].style.display = 'none';
                        }
                        for(var index = counts.length - 1; index >= 0; index -= 1){
                            counts[index].style.display = 'none';
                        }
                    };
                    function update(counts, count){
                        for(var index = counts.length - 1; index >= 0; index -= 1){
                            counts[index].innerHTML = count;
                        }
                    }
                    var data    = pure.components.messenger.notifications.templates.external.data,
                        count   = pure.components.messenger.notifications.loader.more.data.total;
                    if (data !== null){
                        if (count > 0){
                            show(data.containers, data.counts);
                            update(data.counts, count);
                        }else{
                            hide(data.containers, data.counts);
                        }
                    }
                },
                progress    :{
                    data : null,
                    show : function(){
                        var containers  = pure.components.messenger.notifications.templates.external.data.progress,
                            progress    = null;
                        if (containers !== null){
                            pure.components.messenger.notifications.templates.external.progress.data = [];
                            for (var index = containers.length - 1; index >= 0; index -= 1){
                                progress = pure.templates.progressbar.A.show(containers[index],'margin-left:-0.35em;margin-top:0.2em;');
                                (function(progress){
                                    pure.components.messenger.notifications.templates.external.progress.data.push(progress);
                                }(progress));
                            }
                        }
                    },
                    hide : function(){
                        var progresses = pure.components.messenger.notifications.templates.external.progress.data;
                        if (progresses !== null){
                            for(var index = progresses.length - 1; index >= 0; index -= 1){
                                pure.templates.progressbar.A.hide(progresses[index]);
                            }
                            progresses = null;
                            pure.components.messenger.notifications.templates.external.progress.data = null;
                        }
                    }
                }
            },
            init        : function(){
                pure.components.messenger.notifications.templates.notification. init();
                pure.components.messenger.notifications.templates.external.     init();
            }
        },
        request     : {
            progress    : {
                data    : {},
                show    : function(notification_id, button){
                    var data = pure.components.messenger.notifications.request.progress.data;
                    data[notification_id] = pure.templates.progressbar.B.show(button);
                },
                hide    : function(notification_id){
                    var data = pure.components.messenger.notifications.request.progress.data;
                    pure.templates.progressbar.B.hide(data[notification_id]);
                    data[notification_id] = null;
                    delete data[notification_id];
                },
                isIn    : function(notification_id){
                    return (typeof pure.components.messenger.notifications.request.progress.data[notification_id] !== 'undefined' ? true : false);
                }
            },
            send        : function(notification_id, request, expected_response, button){
                pure.components.messenger.notifications.request.progress.show(notification_id, button);
                pure.tools.request.send({
                    type        : 'POST',
                    url         : pure.components.messenger.configuration.requestURL,
                    request     : request,
                    onrecieve   : function (id_request, response) {
                        pure.components.messenger.notifications.request.onRecieve(id_request, response, expected_response, notification_id);
                    },
                    onreaction  : null,
                    onerror     : function (event, id_request) {
                        pure.components.messenger.notifications.request.onError(event, id_request, notification_id);
                    },
                    ontimeout   : function (event, id_request) {
                        pure.components.messenger.notifications.request.onError(event, id_request, notification_id);
                    }
                });
            },
            onRecieve   : function(id_request, response, expected_response, notification_id){
                if (response === expected_response){
                    pure.components.messenger.notifications.dialogs.info('Success', 'Operation was done.');
                    pure.components.messenger.notifications.render.remove(notification_id);
                    pure.components.messenger.notifications.loader.more.change(-1, -1);
                    pure.components.messenger.notifications.render.more.update();
                }else{
                    pure.components.messenger.notifications.dialogs.info('Fail', 'Some error was. Try a bit later.');
                }
                pure.components.messenger.notifications.request.progress.hide(notification_id);
            },
            onError     : function(event, id_request, notification_id){
                pure.components.messenger.notifications.dialogs.info('Fail', 'Some error was. Try a bit later.');
                pure.components.messenger.notifications.request.progress.hide(notification_id);
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
            },
            confirmation : function(title, message, handle){
                pure.components.dialogs.B.open({
                    title       : 'Please confirm operation: <strong>' + title + '</strong>',
                    innerHTML   : '<p data-element-type="Pure.Messenger.Mails.Dialog">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : [
                        {
                            title       : 'CANCEL',
                            handle      : null,
                            closeAfter  : true
                        },
                        {
                            title       : 'DO IT',
                            handle      : handle,
                            closeAfter  : true
                        }
                    ]
                });
            }
        },
        init        : function(){
            if (pure.components.messenger.notifications.loader.isPossible() !== false){
                if (pure.components.messenger.notifications.nodes.find() !== false){
                    pure.components.messenger.notifications.templates.  init();
                    pure.components.messenger.notifications.loader.     init();
                }
            }
        }
    };
}());