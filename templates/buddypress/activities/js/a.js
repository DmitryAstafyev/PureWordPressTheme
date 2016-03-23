(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.buddypress               !== "object") { window.pure.buddypress              = {}; }
    if (typeof window.pure.buddypress.activities    !== "object") { window.pure.buddypress.activities   = {}; }
    "use strict";
    window.pure.buddypress.activities.A = {
        message     : {
            sending    : {
                progress    : {
                    data    : {},
                    is      : function(editorID){
                        return (typeof pure.buddypress.activities.A.message.sending.progress.data[editorID] === 'undefined' ? false : true);
                    },
                    set     : function(editorID, container){
                        var progress = pure.templates.progressbar.A.show(
                            container,
                            'background:rgba(255,255,255,0.7);',
                            '',
                            '',
                            'wait please'
                        );
                        pure.buddypress.activities.A.message.sending.progress.data[editorID] = progress;
                    },
                    clear   : function(editorID){
                        if (typeof pure.buddypress.activities.A.message.sending.progress.data[editorID] !== 'undefined'){
                            pure.templates.progressbar.A.hide(pure.buddypress.activities.A.message.sending.progress.data[editorID]);
                            pure.buddypress.activities.A.message.sending.progress.data[editorID] = null;
                            delete pure.buddypress.activities.A.message.sending.progress.data[editorID];
                        }
                    }
                },
                reset       : function(editorID){
                    var editor = pure.nodes.select.first('*[data-engine-activity-editor="' + editorID + '"] textarea');
                    if (editor !== null){
                        editor.value = '';
                    }else{
                        if (pure.buddypress.activities.A.templates.rootEditor.id !== null){
                            editor = tinyMCE.get(pure.buddypress.activities.A.templates.rootEditor.id);
                            if (editor !== null){
                                editor.setContent('');
                            }
                        }
                    }
                    pure.wordpress.media.images.reset('*[data-engine-activity-editor="' + editorID + '"]');
                },
                send        : function(params, message){
                    var editor      = pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"] textarea'),
                        container   = pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"]'),
                        attachment  = pure.nodes.select.first('img[data-storage-id="' + params.activityID + '"]'),
                        request     = null,
                        message     = (typeof message !== 'string' ? null : message);
                    if (editor !== null && container !== null){
                        if (pure.buddypress.activities.A.message.sending.progress.is(params.editorID) === false){
                            if (editor.value.length > 1 || message !== null){
                                if (editor.value.length < pure.buddypress.activities.configuration.maxLength){
                                    if (attachment !== null){
                                        attachment = attachment.getAttribute('pure-wordpress-media-images-id');
                                        attachment = (attachment !== null ? parseInt(attachment, 10) : 0);
                                        attachment = (attachment > 0 ? attachment : 0);
                                    }
                                    pure.buddypress.activities.A.message.sending.progress.set(params.editorID, container);
                                    message = (message !== null ? message : editor.value);
                                    request = pure.buddypress.activities.configuration.requests.sendComment;
                                    request = request.replace(/\[comment\]/gi,          message             );
                                    request = request.replace(/\[root_id\]/gi,          params.rootID       );
                                    request = request.replace(/\[activity_id\]/gi,      params.activityID   );
                                    request = request.replace(/\[attachment_id\]/gi,    attachment          );
                                    pure.tools.request.send({
                                        type        : 'POST',
                                        url         : pure.buddypress.activities.configuration.requestURL,
                                        request     : request,
                                        onrecieve   : function (id_request, response) {
                                            pure.buddypress.activities.A.message.sending.onRecieve(id_request, response, params);
                                        },
                                        onreaction  : null,
                                        onerror     : function (id_request) {
                                            pure.buddypress.activities.A.message.sending.onError(id_request, params);
                                        },
                                        ontimeout   : function (id_request) {
                                            pure.buddypress.activities.A.message.sending.onError(id_request, params);
                                        }
                                    });
                                }else{
                                    pure.buddypress.activities.A.dialogs.info('Cannot do it', 'Your message is too big. You can use only ' + pure.buddypress.activities.configuration.maxLength + ' symbols.');
                                }
                            }else{
                                pure.buddypress.activities.A.dialogs.info('Cannot do it', 'Write some message to send it. ');
                            }
                        }
                    }
                },
                sendRoot    : function(params){
                    var container   = pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"]'),
                        attachment  = pure.nodes.select.first('img[data-storage-id="' + params.activityID + '"]'),
                        request     = null,
                        post        = tinyMCE.get(pure.buddypress.activities.A.templates.rootEditor.id);
                    if (post !== null && container !== null){
                        if (pure.buddypress.activities.A.message.sending.progress.is(params.editorID) === false){
                            post = post.getContent();
                            if (post.length > 1){
                                if (post.length < pure.buddypress.activities.configuration.maxLength){
                                    post = pure.convertor.UTF8.  encode(post);
                                    post = pure.convertor.BASE64.encode(post);
                                    if (attachment !== null){
                                        attachment = attachment.getAttribute('pure-wordpress-media-images-id');
                                        attachment = (attachment !== null ? parseInt(attachment, 10) : 0);
                                        attachment = (attachment > 0 ? attachment : 0);
                                    }
                                    pure.buddypress.activities.A.message.sending.progress.set(params.editorID, container);
                                    request = pure.buddypress.activities.configuration.requests.sendPost;
                                    request = request.replace(/\[post\]/gi,             post                );
                                    request = request.replace(/\[object_id\]/gi,        params.objectID     );
                                    request = request.replace(/\[object_type\]/gi,      params.objectType   );
                                    request = request.replace(/\[attachment_id\]/gi,    attachment          );
                                    pure.tools.request.send({
                                        type        : 'POST',
                                        url         : pure.buddypress.activities.configuration.requestURL,
                                        request     : request,
                                        onrecieve   : function (id_request, response) {
                                            pure.buddypress.activities.A.message.sending.onRecieve(id_request, response, params);
                                        },
                                        onreaction  : null,
                                        onerror     : function (id_request) {
                                            pure.buddypress.activities.A.message.sending.onError(id_request, params);
                                        },
                                        ontimeout   : function (id_request) {
                                            pure.buddypress.activities.A.message.sending.onError(id_request, params);
                                        }
                                    });
                                }else{
                                    pure.buddypress.activities.A.dialogs.info('Cannot do it', 'Your message is too big. You can use only ' + pure.buddypress.activities.configuration.maxLength + ' symbols.');
                                }
                            }else{
                                pure.buddypress.activities.A.dialogs.info('Cannot do it', 'Write some message to send it. ');
                            }
                        }
                    }
                },
                onRecieve   : function(id_request, response, params){
                    var message = pure.buddypress.activities.A.dialogs.info,
                        data    = null;
                    pure.buddypress.activities.A.message.sending.progress.clear(params.editorID);
                    switch (response){
                        case 'no access':
                            message('Error', 'Cannot send your comment, because you have no access.');
                            break;
                        case 'short comment':
                            message('Error', 'Your comment is too short.');
                            break;
                        case 'big comment':
                            message('Error', 'Your comment is too big.');
                            break;
                        case 'closed':
                            message('Error', 'Sorry, but comments for this post are closed.');
                            break;
                        case 'error during saving':
                            message('Error', 'Sorry, but some error was during saving your comment. Try again a bit later.');
                            break;
                        default :
                            try{
                                data = JSON.parse(response);
                                pure.buddypress.activities.A.message.sending.reset(params.editorID);
                                data.object_id      = params.objectID;
                                data.object_type    = params.objectType;
                                if (parseInt(data.parent, 10) === 0) {
                                    pure.buddypress.activities.A.templates.activities.root.action.add(data);
                                    pure.buddypress.activities.A.more.counter.deepUpdateTotal(data.object_id, 1);
                                }else{
                                    pure.buddypress.activities.A.templates.activities.included.action.add(data);
                                }
                                pure.buddypress.activities.A.more.mana([data], params.objectID);
                                pure.buddypress.activities.A.remove.init();
                                pure.buddypress.activities.A.hotUpdate.call();
                            }catch (e){
                                message('Error', 'Sorry, but some unknown error was during getting responce from server.');
                            }
                            break;
                    }
                },
                onError     : function(id_request, params){
                    pure.buddypress.activities.A.message.sending.progress.clear(params.editorID);
                    pure.buddypress.activities.A.dialogs.info('Server error', 'Server did not proceed request. Please, try a bit later.');
                }
            }
        },
        templates   : {
            common          : {
                init                : function(element, storagePath){
                    function getNodeTemplate(outerHTML){
                        var node = document.createElement('div');
                        node.innerHTML = outerHTML;
                        return (node.childNodes.length === 1 ? node.childNodes[0] : null);
                    }
                    var templates   = pure.system.getInstanceByPath('pure.buddypress.activities.configuration.templates.' + element),
                        data        = pure.system.getInstanceByPath(storagePath);
                    if (templates !== null){
                        for(var ID in templates){
                            if (templates[ID] !== null && templates[ID] !== ""){
                                (function(template, ID){
                                    template = pure.convertor.BASE64.decode(template);
                                    template = pure.convertor.UTF8.  decode(template);
                                    template = getNodeTemplate(template);
                                    if (typeof data[ID] === 'undefined'){
                                        data[ID] = {
                                            innerHTML   : template.innerHTML,
                                            attributes  : pure.nodes.attributes.get(template, ['data-engine-activity-element', 'style']),
                                            nodeName    : template.nodeName
                                        };
                                    }
                                }(templates[ID], ID));
                            }
                        }
                        return data;
                    }
                    return null;
                },
                isMeme              : function(message){
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
                },
                hasAttachment       : function(message){
                    var matches         = message.match(/\[attachment:begin\](.*)\[attachment:end\]/gi),
                        result          = '',
                        clearMessage    = '';
                    if (matches instanceof Array){
                        if (matches.length === 1){
                            result          = matches[0];
                            result          = result.replace('[attachment:begin]', '');
                            result          = result.replace('[attachment:end]',   '');
                            clearMessage    = message.replace(/\[attachment:begin\](.*)\[attachment:end\]/gi, '');
                            return {
                                content : clearMessage,
                                url     : result
                            };
                        }
                    }
                    return null;
                },
                parseActivity       : function(content, action, contentNode, quoteNode){
                    function addComment(contentNode, strValue){
                        function getNodes(strValue){
                            var div     = document.createElement('DIV'),
                                _nodes  = [],
                                added   = false;
                            div.innerHTML = strValue;
                            for(var index = 0, max_index = div.childNodes.length; index < max_index; index += 1){
                                if (div.childNodes[index].nodeName.toLowerCase() === 'p'){
                                    added = true;
                                }
                                _nodes.push(div.childNodes[index]);
                            }
                            nodes = (added !== false ? nodes.concat(_nodes) : nodes);
                            return added;
                        }
                        var _node   = null,
                            inside  = getNodes(strValue);
                        if (strValue !== '' && inside === false){
                            _node = contentNode.cloneNode(true);
                            _node.innerHTML = _node.innerHTML.replace(/\[content\]/gi, strValue.replace(/[\n\r]/gim, '<br/>'));
                            nodes.push(_node);
                        }
                    };
                    function addQuote(quoteNode, strValue){
                        var author  = strValue.match(/\[author:begin\](.*)\[author:end\]/gi),
                            date    = strValue.match(/\[date:begin\](.*)\[date:end\]/gi),
                            value   = strValue.replace('[quote:begin]', '').replace('[quote:end]', ''),
                            _node   = null;
                        if (author instanceof Array && date instanceof Array){
                            if (author.length === 1 && date.length === 1){
                                author  = author[0].replace('[author:begin]',   '').replace('[author:end]', '');
                                date    = date  [0].replace('[date:begin]',     '').replace('[date:end]',   '');
                                value   = value.replace(/\[author:begin\](.*)\[author:end\]/gi, '');
                                value   = value.replace(/\[date:begin\](.*)\[date:end\]/gi,     '');
                                _node   = quoteNode.cloneNode(true);
                                _node.innerHTML = _node.innerHTML.replace(/\[quote\]/gi,        value.replace(/[\n\r]/gim, '<br/>'));
                                _node.innerHTML = _node.innerHTML.replace(/\[quote_author\]/gi, author);
                                _node.innerHTML = _node.innerHTML.replace(/\[quote_date\]/gi,   date);
                                nodes.push(_node);
                            }
                        }
                    };
                    var quotes      = content.match(/\[quote:begin\](.|\n|\r|\n\r(?!\[quote:begin\]))*?\[quote:end\]/gim),
                        _message    = content,
                        parts       = null,
                        separator   = 'sep' + (Math.random()*10000 + Math.random()*10000) + 'sep',
                        nodes       = [];
                    if (content !== ''){
                        if (quotes instanceof Array){
                            if (quotes.length > 0){
                                for(var index = quotes.length - 1; index >= 0; index -= 1){
                                    _message = _message.replace(quotes[index], separator);
                                }
                                parts = _message.split(separator);
                                for(var index = parts.length - 1; index >= 0; index -= 1){
                                    parts[index] = parts[index].replace(/[\n\r\s]*$/gi, '').replace(/^[\n\r\s]*/gi, '');
                                }
                                for(var index = 0, max_index = parts.length; index < max_index; index += 1){
                                    addComment(contentNode, parts[index]);
                                    if (typeof quotes[index] !== 'undefined'){
                                        addQuote(quoteNode, quotes[index]);
                                    }
                                }
                                return nodes;
                            }
                        }
                        addComment(contentNode, content);
                    }else{
                        addComment(contentNode, action);
                    }
                    return nodes;
                },
                normalizeChildren   : function(activities){
                    var children = [];
                    if (typeof activities.children === 'undefined'){
                        activities.children = false;
                        return activities;
                    }
                    if (typeof activities.children === 'object'){
                        if (Object.keys(activities.children).length > 0){
                            for(var key in activities.children){
                                children.push(activities.children[key]);
                            }
                            activities.children = children;
                            return activities;
                        }else{
                            activities.children = false;
                            return activities;
                        }
                    }
                    if (activities.children instanceof Array){
                        if (activities.children.length === 0){
                            activities.children = false;
                            return activities;
                        }else{
                            return activities;
                        }
                    }
                    activities.children = false;
                    return activities;
                }
            },
            editor          : {
                data    : {},
                init    : function(){
                    pure.buddypress.activities.A.templates.common.init(
                        'editor',
                        'pure.buddypress.activities.A.templates.editor.data'
                    );
                },
                getData : function(objectID, objectType){
                    var data = pure.buddypress.activities.A.templates.editor.data;
                    return (typeof data[objectID + objectType] !== 'undefined' ? data[objectID + objectType] : null);
                },
                actions : {
                    show : function(params){
                        var data        = pure.buddypress.activities.A.templates.editor.getData(params.objectID, params.objectType),
                            editor      = null,
                            mark        = null,
                            buttons     = null;
                        if (data !== null){
                            editor  = pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"]');
                            if (editor !== null){
                                //Remove previous
                                editor.parentNode.removeChild(editor);
                            }else{
                                //Create new
                                mark    = pure.nodes.select.first('*[data-engine-activity-mark="' + params.editorID + '"]');
                                if (mark !== null){
                                    editor              = document.createElement(data.nodeName);
                                    pure.nodes.attributes.set(editor, data.attributes);
                                    editor.innerHTML    = data.     innerHTML.replace(/\[editorID\]/gi,     params.editorID     );
                                    editor.innerHTML    = editor.   innerHTML.replace(/\[activityID\]/gi,   params.activityID   );
                                    editor.setAttribute('data-engine-activity-editor',       params.editorID );
                                    mark.parentNode.insertBefore(editor, mark);
                                    buttons = {
                                        send        : pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"] *[ data-engine-activity-element="Editor.Button.Send"]'        ),
                                        quote       : pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"] *[ data-engine-activity-element="Editor.Button.Quote"]'       ),
                                        attachment  : pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"] *[ data-engine-activity-element="Editor.Button.Attachment"]'  ),
                                        meme        : pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"] *[ data-engine-activity-element="Editor.Button.Meme"]'        )
                                    };
                                    if (buttons.send !== null){
                                        pure.events.add(
                                            buttons.send,
                                            'click',
                                            function(){
                                                pure.buddypress.activities.A.message.sending.send(params);
                                            }
                                        );
                                    }
                                    if (buttons.quote !== null){
                                        pure.events.add(
                                            buttons.quote,
                                            'click',
                                            function(){
                                                pure.buddypress.activities.A.quotes.onClick(params.editorID, params.objectID);
                                            }
                                        );
                                    }
                                    pure.buddypress.activities.A.memes.buttons.init(buttons.meme, params);
                                    pure.wordpress.media.images.init();
                                }
                            }
                        }
                    }
                }
            },
            rootEditor      : {
                data    : {},
                id      : null,
                init    : function(){
                    pure.buddypress.activities.A.templates.common.init(
                        'root_editor',
                        'pure.buddypress.activities.A.templates.rootEditor.data'
                    );
                },
                getData : function(objectID, objectType){
                    var data = pure.buddypress.activities.A.templates.rootEditor.data;
                    return (typeof data[objectID + objectType] !== 'undefined' ? data[objectID + objectType] : null);
                },
                actions : {
                    show : function(params){
                        var data        = pure.buddypress.activities.A.templates.rootEditor.getData(params.objectID, params.objectType),
                            editor      = null,
                            mark        = null,
                            buttons     = null,
                            selector    = null,
                            container   = null,
                            instance    = null;
                        if (data !== null){
                            editor  = pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"]');
                            if (editor !== null){
                                if (pure.buddypress.activities.A.templates.rootEditor.id !== null){
                                    instance = tinyMCE.get(pure.buddypress.activities.A.templates.rootEditor.id);
                                    if (instance !== null){
                                        instance.destroy();
                                    }
                                    pure.buddypress.activities.A.templates.rootEditor.id = null;
                                }
                                //Remove previous
                                editor.parentNode.removeChild(editor);
                            }else{
                                //Create new
                                mark    = pure.nodes.select.first('*[data-engine-activity-mark="' + params.editorID + '"]');
                                if (mark !== null){
                                    editor              = document.createElement(data.nodeName);
                                    pure.nodes.attributes.set(editor, data.attributes);
                                    editor.innerHTML    = data.     innerHTML.replace(/\[editorID\]/gi,     params.editorID     );
                                    editor.innerHTML    = editor.   innerHTML.replace(/\[activityID\]/gi,   params.activityID   );
                                    editor.setAttribute('data-engine-activity-editor',       params.editorID );
                                    mark.parentNode.insertBefore(editor, mark);
                                    selector            = '*[data-engine-activity-editor="' + params.editorID + '"] *[data-engine-activity-element="Root.Editor.Container"]';
                                    container           = pure.nodes.select.first(selector);
                                    if (container !== null){
                                        tinyMCE.init({
                                            selector                : selector,
                                            menubar                 : false,
                                            skin                    : 'lightgray',
                                            theme                   : 'modern',
                                            plugins                 : 'wplink',
                                            init_instance_callback  : function(editor) {
                                                pure.buddypress.activities.A.templates.rootEditor.id = editor.id;
                                            }
                                        });
                                        buttons = {
                                            send        : pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"] *[data-engine-activity-element="Editor.Button.Send"]'        ),
                                            attachment  : pure.nodes.select.first('*[data-engine-activity-editor="' + params.editorID + '"] *[data-engine-activity-element="Editor.Button.Attachment"]'  )
                                        };
                                        if (buttons.send !== null){
                                            pure.events.add(
                                                buttons.send,
                                                'click',
                                                function(){
                                                    pure.buddypress.activities.A.message.sending.sendRoot(params);
                                                }
                                            );
                                        }
                                        pure.wordpress.media.images.init();
                                    }else{
                                        editor.parentNode.removeChild(editor);
                                    }
                                }
                            }
                        }
                    }
                }
            },
            callers         : {
                editors : function(){
                    var callers = pure.nodes.select.all('*[data-engine-activity-element="Editor.Caller"]:not([data-engine-element-inited])');
                    if (callers !== null){
                        for(var index = callers.length - 1; index >= 0; index -= 1){
                            (function(caller){
                                var params = {
                                        objectID    : caller.getAttribute('data-engine-activity-objectID'   ),
                                        objectType  : caller.getAttribute('data-engine-activity-objectType' ),
                                        activityID  : caller.getAttribute('data-engine-activity-activityID' ),
                                        rootID      : caller.getAttribute('data-engine-activity-rootID'     ),
                                        editorID    : caller.getAttribute('data-engine-activity-editorID'   ),
                                        event       : caller.getAttribute('data-engine-activity-event'      )
                                    },
                                    handle = null;
                                if (pure.tools.objects.isValueIn(params, null) === false){
                                    if (parseInt(params.activityID, 10) === 0 && parseInt(params.rootID, 10) === 0){
                                        handle = function(){
                                            pure.buddypress.activities.A.templates.rootEditor.actions.show(params);
                                        };
                                    }else{
                                        handle = function(){
                                            pure.buddypress.activities.A.templates.editor.actions.show(params);
                                        };
                                    }
                                    pure.events.add(
                                        caller,
                                        params.event,
                                        handle
                                    );
                                }
                                caller.setAttribute('data-engine-element-inited', 'true');
                                caller.disabled = false;
                            }(callers[index]));
                        }
                    }
                },
                quotes : function(){
                    var comments = pure.nodes.select.all('*[data-engine-activity-element="Activity.Value"]:not([data-engine-element-inited])');
                    if (comments !== null){
                        for(var index = comments.length - 1; index >= 0; index -= 1){
                            (function(comment){
                                pure.events.add(
                                    comment,
                                    'mouseup',
                                    function(event){
                                        pure.buddypress.activities.A.quotes.onSelection(event, null, null, null);
                                    }
                                );
                                comment.setAttribute('data-engine-element-inited', 'true');
                            }(comments[index]));
                        }
                    }
                },
                init : function(){
                    pure.buddypress.activities.A.templates.callers.editors();
                    pure.buddypress.activities.A.templates.callers.quotes();
                }
            },
            activities      : {
                root    : {
                    data    : {},
                    init    : function () {
                        pure.buddypress.activities.A.templates.common.init(
                            'root_comment',
                            'pure.buddypress.activities.A.templates.activities.root.data'
                        );
                    },
                    getData : function (objectID, objectType) {
                        var data = pure.buddypress.activities.A.templates.activities.root.data;
                        return (typeof data[objectID + objectType] !== 'undefined' ? data[objectID + objectType] : null);
                    },
                    action  : {
                        add         : function(params, moreMark){
                            var data        = pure.buddypress.activities.A.templates.activities.root.getData(params.object_id, params.object_type),
                                template    = null,
                                mark        = pure.nodes.select.first('*[data-engine-activity-root-mark="' + params.object_id + params.object_type + '"]'),
                                moreMark    = (typeof moreMark !== 'undefined' ? moreMark : null),
                                nodes       = null,
                                memeSRC     = null,
                                attachment  = pure.buddypress.activities.A.templates.common.hasAttachment(params.content),
                                contents    = null;
                            if (data !== null && mark !== null){
                                params.content        = (attachment === null ? params.content : attachment.content);
                                template              = document.createElement(data.nodeName);
                                pure.nodes.attributes.set(template, data.attributes);
                                template.setAttribute('data-engine-activity-activityID',  params.id   );
                                template.innerHTML    = data.       innerHTML.replace(/\[name\]/gi,         params.name         );
                                template.innerHTML    = template.   innerHTML.replace(/\[avatar\]/gi,       params.avatar       );
                                template.innerHTML    = template.   innerHTML.replace(/\[home\]/gi,         params.home         );
                                template.innerHTML    = template.   innerHTML.replace(/\[date\]/gi,         params.date         );
                                template.innerHTML    = template.   innerHTML.replace(/\[activity_id\]/gi,  params.id           );
                                template.innerHTML    = template.   innerHTML.replace(/\[root_id\]/gi,      params.root         );
                                template.innerHTML    = template.   innerHTML.replace(/\[object_id\]/gi,    params.object_id    );
                                template.innerHTML    = template.   innerHTML.replace(/\[object_type\]/gi,  params.object_type  );
                                if (moreMark !== null){
                                    moreMark.parentNode.insertBefore(template, moreMark);
                                }else{
                                    pure.nodes.insertAfter(template, mark);
                                    pure.buddypress.activities.A.more.counter.updateInclude(params.id);
                                    pure.buddypress.activities.A.more.counter.deepUpdateShown(params.object_id, params.object_type);
                                }
                                nodes = {
                                    container   : pure.nodes.select.first('*[data-engine-activity-activityID="' + params.id + '"][data-engine-activity-element="Activity.Container"]'   ),
                                    content     : pure.nodes.select.first('*[data-engine-activity-activityID="' + params.id + '"][data-engine-activity-element="Activity.Value"]'       ),
                                    quote       : pure.nodes.select.first('*[data-engine-activity-activityID="' + params.id + '"][data-engine-activity-element="Activity.Quote"]'       ),
                                    attachment  : pure.nodes.select.first('*[data-engine-activity-activityID="' + params.id + '"][data-engine-activity-element="Activity.Attachment"]'  ),
                                    meme        : pure.nodes.select.first('*[data-engine-activity-activityID="' + params.id + '"][data-engine-activity-element="Activity.Meme"]'        )
                                };
                                if (nodes.meme !== null){
                                    memeSRC = pure.buddypress.activities.A.templates.common.isMeme(params.content);
                                    if (memeSRC !== null){
                                        nodes.meme.innerHTML = nodes.meme.innerHTML.replace(/\[meme\]/gi, memeSRC);
                                        nodes.container.parentNode.removeChild(nodes.container  );
                                        nodes.content.  parentNode.removeChild(nodes.content    );
                                        nodes.quote.    parentNode.removeChild(nodes.quote      );
                                        nodes.container = null;
                                        nodes.content   = null;
                                        nodes.quote     = null;
                                    }else{
                                        nodes.meme.parentNode.removeChild(nodes.meme);
                                    }
                                }
                                if (nodes.attachment !== null){
                                    if (attachment !== null){
                                        nodes.attachment.innerHTML = nodes.attachment.innerHTML.replace(/\[attachment\]/gi, attachment.url);
                                    }else{
                                        nodes.attachment.parentNode.removeChild(nodes.attachment);
                                    }
                                }
                                if (nodes.content !== null && nodes.quote !== null){
                                    contents = pure.buddypress.activities.A.templates.common.parseActivity(
                                        params.content,
                                        params.action,
                                        nodes.content,
                                        nodes.quote
                                    );
                                    for(var index = 0, max_index = contents.length; index < max_index; index += 1){
                                        nodes.container.appendChild(contents[index]);
                                    }
                                    nodes.content.  parentNode.removeChild(nodes.content);
                                    nodes.quote.    parentNode.removeChild(nodes.quote  );
                                }
                                pure.buddypress.activities.A.templates.callers.init();
                            }
                        },
                        addFromMore         : function(objectID, objectType, activities, mark){
                            function addChildren(objectID, objectType, activities, parentID, rootID){
                                for (var index = 0, max_index = activities.length; index < max_index; index += 1){
                                    pure.buddypress.activities.A.templates.activities.included.action.add(
                                        parseRecord(activities[index], objectID, objectType, parentID, rootID)
                                    );
                                    activities[index] = pure.buddypress.activities.A.templates.common.normalizeChildren(activities[index]);
                                    if (activities[index].children instanceof Array){
                                        addChildren(objectID, objectType, activities[index].children, activities[index].id, rootID);
                                    }
                                }
                            };
                            function parseRecord(source, objectID, objectType, parentID, rootID){
                                return {
                                    id          : source.id,
                                    name        : source.user.name,
                                    avatar      : source.user.avatar,
                                    home        : source.user.home,
                                    date        : source.date_recorded,
                                    parent      : parentID,
                                    root        : rootID,
                                    object_id   : objectID,
                                    object_type : objectType,
                                    action      : source.action,
                                    content     : source.content
                                };
                            };
                            for(var index = 0, max_index = activities.length; index < max_index; index += 1){
                                pure.buddypress.activities.A.templates.activities.root.action.add(
                                    parseRecord(activities[index], objectID, objectType, 0, 0),
                                    mark
                                );
                                activities[index] = pure.buddypress.activities.A.templates.common.normalizeChildren(activities[index]);
                                if (activities[index].children instanceof Array){
                                    addChildren(objectID, objectType, activities[index].children, activities[index].id, activities[index].id);
                                    pure.buddypress.activities.A.more.counter.updateInclude(activities[index].id, objectID, objectType);
                                }
                            }
                            pure.buddypress.activities.A.more.counter.updateInclude(null, objectID, objectType);
                        },
                        addFromHotUpdate    : function(objectID, comments){
                            function parseRecord(source, objectID){
                                return {
                                    id      : source.comment.id,
                                    name    : source.author.name,
                                    avatar  : source.author.avatar,
                                    date    : source.comment.date,
                                    parent  : source.comment.parent,
                                    object_id : objectID,
                                    comment : source.comment.value
                                };
                            };
                            for(var index = 0, max_index = comments.length; index < max_index; index += 1) {
                                (function(comment, objectID){
                                    var container = pure.nodes.select.first('*[data-engine-activity-objectID="' + objectID + '"][data-engine-activity-activityID="' + comment.comment.id + '"]');
                                    if (container === null){
                                        if (parseInt(comment.comment.parent, 10) === 0) {
                                            pure.buddypress.activities.A.templates.activities.root.action.add(parseRecord(comment, objectID));
                                            pure.buddypress.activities.A.more.counter.deepUpdateTotal(objectID, 1);
                                        }else{
                                            pure.buddypress.activities.A.templates.activities.included.action.add(parseRecord(comment, objectID));
                                        }
                                    }
                                }(comments[index], objectID));
                            }
                        }
                    }
                },
                included: {
                    data    : {},
                    init    : function () {
                        pure.buddypress.activities.A.templates.common.init(
                            'comment',
                            'pure.buddypress.activities.A.templates.activities.included.data'
                        );
                    },
                    getData : function (objectID, objectType) {
                        var data = pure.buddypress.activities.A.templates.activities.included.data;
                        return (typeof data[objectID + objectType] !== 'undefined' ? data[objectID + objectType] : null);
                    },
                    action : {
                        add: function(params){
                            var data        = pure.buddypress.activities.A.templates.activities.included.getData(params.object_id, params.object_type),
                                template    = null,
                                nodes       = null,
                                memeSRC     = null,
                                container   = pure.nodes.select.first('*[data-engine-activity-included="Container"][data-engine-activity-activityID="' + params.parent + '"]'),
                                attachment  = pure.buddypress.activities.A.templates.common.hasAttachment(params.content),
                                contents    = null;
                            if (data !== null && container !== null){
                                params.content        = (attachment === null ? params.content : attachment.content);
                                template              = document.createElement(data.nodeName);
                                pure.nodes.attributes.set(template, data.attributes);
                                template.setAttribute('data-engine-activity-activityID',  params.id   );
                                template.innerHTML    = data.       innerHTML.replace(/\[name\]/gi,         params.name         );
                                template.innerHTML    = template.   innerHTML.replace(/\[avatar\]/gi,       params.avatar       );
                                template.innerHTML    = template.   innerHTML.replace(/\[home\]/gi,         params.home         );
                                template.innerHTML    = template.   innerHTML.replace(/\[date\]/gi,         params.date         );
                                template.innerHTML    = template.   innerHTML.replace(/\[activity_id\]/gi,  params.id           );
                                template.innerHTML    = template.   innerHTML.replace(/\[root_id\]/gi,      params.root         );
                                template.innerHTML    = template.   innerHTML.replace(/\[object_id\]/gi,    params.object_id    );
                                template.innerHTML    = template.   innerHTML.replace(/\[object_type\]/gi,  params.object_type  );
                                if (container.childNodes.length > 0){
                                    container.insertBefore(template, container.firstChild);
                                }else{
                                    container.appendChild(template);
                                }
                                pure.buddypress.activities.A.more.counter.updateInclude(params.parent);
                                nodes = {
                                    container   : pure.nodes.select.first('*[data-engine-activity-activityID="' + params.id + '"][data-engine-activity-element="Activity.Container"]'   ),
                                    content     : pure.nodes.select.first('*[data-engine-activity-activityID="' + params.id + '"][data-engine-activity-element="Activity.Value"]'       ),
                                    quote       : pure.nodes.select.first('*[data-engine-activity-activityID="' + params.id + '"][data-engine-activity-element="Activity.Quote"]'       ),
                                    attachment  : pure.nodes.select.first('*[data-engine-activity-activityID="' + params.id + '"][data-engine-activity-element="Activity.Attachment"]'  ),
                                    meme        : pure.nodes.select.first('*[data-engine-activity-activityID="' + params.id + '"][data-engine-activity-element="Activity.Meme"]'        )
                                };
                                if (nodes.meme !== null){
                                    memeSRC = pure.buddypress.activities.A.templates.common.isMeme(params.content);
                                    if (memeSRC !== null){
                                        nodes.meme.innerHTML = nodes.meme.innerHTML.replace(/\[meme\]/gi, memeSRC);
                                        nodes.container.    parentNode.removeChild(nodes.container  );
                                        nodes.content.      parentNode.removeChild(nodes.content    );
                                        nodes.quote.        parentNode.removeChild(nodes.quote      );
                                        nodes.container = null;
                                        nodes.content   = null;
                                        nodes.quote     = null;
                                    }else{
                                        nodes.meme.parentNode.removeChild(nodes.meme);
                                    }
                                }
                                if (nodes.attachment !== null){
                                    if (attachment !== null){
                                        nodes.attachment.innerHTML = nodes.attachment.innerHTML.replace(/\[attachment\]/gi, attachment.url);
                                    }else{
                                        nodes.attachment.parentNode.removeChild(nodes.attachment);
                                    }
                                }
                                if (nodes.content !== null && nodes.quote !== null){
                                    contents = pure.buddypress.activities.A.templates.common.parseActivity(
                                        params.content,
                                        params.action,
                                        nodes.content,
                                        nodes.quote
                                    );
                                    for(var index = 0, max_index = contents.length; index < max_index; index += 1){
                                        nodes.container.appendChild(contents[index]);
                                    }
                                    nodes.content.  parentNode.removeChild(nodes.content);
                                    nodes.quote.    parentNode.removeChild(nodes.quote  );
                                }
                                pure.buddypress.activities.A.templates.callers.init();
                            }
                        }
                    }
                }
            },
            notification    : {
                data            : null,
                init            : function(){
                    function getNodeTemplate(outerHTML){
                        var node = document.createElement('div');
                        node.innerHTML = outerHTML;
                        return (node.childNodes.length === 1 ? node.childNodes[0] : null);
                    }
                    var templates = pure.system.getInstanceByPath('pure.buddypress.activities.configuration.templates.quote');
                    if (templates !== null){
                        for(var postID in templates){
                            (function(template, postID){
                                template = pure.convertor.BASE64.decode(template);
                                template = pure.convertor.UTF8.  decode(template);
                                pure.buddypress.activities.A.templates.notification.data = getNodeTemplate(template);
                            }(templates[postID], postID));
                        }
                        return true;
                    }
                    return null;
                },
                show : function(parent){
                    var data = pure.buddypress.activities.A.templates.notification.data,
                        node = null;
                    if (data !== null){
                        node                = data.cloneNode(true);
                        node.style.display  = 'block';
                        parent.appendChild(node);
                        setTimeout(
                            function(){
                                node.parentNode.removeChild(node);
                            },
                            2000
                        );
                    }
                }
            },
            init            : function(){
                pure.buddypress.activities.A.templates.editor.              init();
                pure.buddypress.activities.A.templates.rootEditor.          init();
                pure.buddypress.activities.A.templates.activities.root.     init();
                pure.buddypress.activities.A.templates.activities.included. init();
                pure.buddypress.activities.A.templates.notification.        init();
                //Callers should be last to don't touch templates
                pure.buddypress.activities.A.templates.callers.             init();
            }
        },
        remove      : {
            init : function(){
                var buttons = pure.nodes.select.all('*[data-engine-activity-element="Activities.Remove"]:not([data-engine-element-inited])');
                if (buttons !== null){
                    for (var index = buttons.length - 1; index >= 0; index -= 1){
                        (function(button){
                            var activity_id = button.getAttribute('data-engine-activity-activityID');
                            if (activity_id !== null && activity_id !== ''){
                                pure.events.add(
                                    button,
                                    'click',
                                    function(){
                                        pure.buddypress.activities.A.dialogs.question(
                                            'Confirm operation',
                                            'Do you really want remove this element? Please, confirm operation.',
                                            'REMOVE IT',
                                            function(){
                                                pure.buddypress.activities.A.remove.request.send(activity_id);
                                            }
                                        );
                                    }
                                );
                            }
                            button.setAttribute('data-engine-element-inited', 'true');
                        }(buttons[index]));
                    }
                }
            },
            remove  : function(activity_id){
                var containers  = pure.nodes.select.all('*[data-engine-activity-remove="remove"][data-engine-activity-activityID="' + activity_id + '"]');
                if (containers !== null){
                    for (var index = containers.length - 1; index >= 0; index -= 1){
                        containers[index].parentNode.removeChild(containers[index]);
                    }
                }
            },
            request : {
                progress : {
                    add : function(containers){
                        var progresses = [];
                        for(var index = containers.length - 1; index >= 0; index -= 1){
                            progresses.push(
                                (function(container){
                                    return pure.templates.progressbar.A.show(
                                        container,
                                        'background:rgba(255,255,255,0.7);',
                                        '',
                                        '',
                                        'wait please'
                                    );
                                }(containers[index]))
                            );
                        }
                        return progresses;
                    },
                    remove : function(progresses){
                        for(var index = progresses.length - 1; index >= 0; index -= 1){
                            pure.templates.progressbar.A.hide(progresses[index]);
                        }
                    }

                },
                send : function(activity_id){
                    var containers  = pure.nodes.select.all('*[data-engine-activity-remove="remove"][data-engine-activity-activityID="' + activity_id + '"]'),
                        progresses  = [],
                        request     = null;
                    if (containers !== null){
                        progresses  = pure.buddypress.activities.A.remove.request.progress.add(containers);
                        request     = pure.buddypress.activities.configuration.requests.remove;
                        request     = request.replace(/\[activity_id\]/gi,  activity_id  );
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.buddypress.activities.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.activities.A.remove.request.onRecieve(id_request, response, activity_id, progresses);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.activities.A.more.get.onError(event, id_request, progresses);
                            },
                            ontimeout   : function (id_request) {
                                pure.buddypress.activities.A.more.get.onError(id_request, id_request, progresses);
                            }
                        });

                    }
                },
                onRecieve   : function(id_request, response, activity_id, progresses){
                    var message = pure.buddypress.activities.A.dialogs.info;
                    pure.buddypress.activities.A.remove.request.progress.remove(progresses);
                    switch (response){
                        case 'success':
                            pure.buddypress.activities.A.remove.remove(activity_id);
                            break;
                        case 'fail':
                            message('Fail', 'Sorry, but server cannot remove this item.');
                            break;
                        case 'no permit to remove':
                            message('Fail', 'Sorry, but you have not the permit to remove this item.');
                            break;
                        case 'cannot find root':
                            message('Fail', 'Sorry, but server cannot find root activity item.');
                            break;
                        case 'cannot remove':
                            message('Fail', 'Sorry, but server cannot remove this item.');
                            break;
                        case 'no access':
                            message('Fail', 'Sorry, but you have not access.');
                            break;
                    }
                },
                onError     : function(event, id_request, progresses){
                    var message = pure.buddypress.activities.A.dialogs.info;
                    pure.buddypress.activities.A.remove.request.progress.remove(progresses);
                    message('Fail', 'Sorry, but we have some connection error, try again a bit later.');
                }
            }
        },
        more        : {
            init        : function(){
                function proceed(buttons, type){
                    if (buttons !== null){
                        for(var index = buttons.length - 1; index >= 0; index -= 1){
                            (function(button, type){
                                var objectID    = button.getAttribute('data-engine-activity-objectID'),
                                    objectType  = button.getAttribute('data-engine-activity-objectType'),
                                    mark        = null;
                                if (objectID !== null && objectID !== '' && objectType !== null && objectType !== ''){
                                    mark = pure.nodes.select.first('*[data-engine-activity-more-mark="' + objectID + objectType + '"]');
                                    if(mark !== null){
                                        pure.events.add(
                                            button,
                                            'click',
                                            function(){
                                                pure.buddypress.activities.A.more.get.send(objectID, objectType, button, type, mark);
                                            }
                                        );
                                    }
                                }
                                button.setAttribute('data-engine-element-inited', 'true');
                            }(buttons[index], type));
                        }
                    }
                };
                var buttons = {
                        next: pure.nodes.select.all('*[data-engine-activity-element="Activities.More.Package"]:not([data-engine-element-inited])'),
                        all : pure.nodes.select.all('*[data-engine-activity-element="Activities.More.All"]:not([data-engine-element-inited])')
                    };
                proceed(buttons.next,   'next'  );
                proceed(buttons.all,    'all'   );
                pure.buddypress.activities.A.more.counter.updateInclude();
            },
            progress    : {
                data    : {},
                is      : function(objectID, objectType){
                    return (typeof pure.buddypress.activities.A.more.progress.data[(objectID + objectType)] === 'undefined' ? false : true);
                },
                set     : function(objectID, objectType, mark){
                    var progress = pure.templates.progressbar.D.show(mark);
                    pure.buddypress.activities.A.more.progress.data[(objectID + objectType)] = progress;
                },
                clear   : function(objectID, objectType){
                    if (typeof pure.buddypress.activities.A.more.progress.data[(objectID + objectType)] !== 'undefined'){
                        pure.templates.progressbar.A.hide(pure.buddypress.activities.A.more.progress.data[(objectID + objectType)]);
                        pure.buddypress.activities.A.more.progress.data[(objectID + objectType)] = null;
                        delete pure.buddypress.activities.A.more.progress.data[(objectID + objectType)];
                    }
                }
            },
            get         : {
                send        : function(objectID, objectType, button, type, mark){
                    var shown   = null,
                        allFlag = '',
                        request = '';
                    if (pure.buddypress.activities.A.more.progress.is(objectID, objectType) === false){
                        pure.buddypress.activities.A.more.progress.set(objectID, objectType, mark);
                        shown   = button.getAttribute('data-engine-activity-more-shown');
                        shown   = (shown !== null ? (shown !== '' ? parseInt(shown, 10) : 0) : 0);
                        shown   = (shown > 0 ? shown : 0);
                        switch (type){
                            case 'next':
                                allFlag = 'no';
                                break;
                            case 'all':
                                allFlag = 'yes';
                                break;
                        }
                        request = pure.buddypress.activities.configuration.requests.more;
                        request = request.replace(/\[object_type\]/gi,  objectType  );
                        request = request.replace(/\[object_id\]/gi,    objectID    );
                        request = request.replace(/\[shown\]/gi,        shown       );
                        request = request.replace(/\[all\]/gi,          allFlag     );
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.buddypress.activities.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.activities.A.more.get.onRecieve(id_request, response, objectID, objectType, button, mark, shown);
                            },
                            onreaction  : null,
                            onerror     : function (id_request) {
                                pure.buddypress.activities.A.more.get.onError(id_request, objectID, objectType);
                            },
                            ontimeout   : function (id_request) {
                                pure.buddypress.activities.A.more.get.onError(id_request, objectID, objectType);
                            }
                        });
                    }
                },
                onRecieve   : function(id_request, response, objectID, objectType, button, mark, shown){
                    var message = pure.buddypress.activities.A.dialogs.info,
                        data    = null;
                    pure.buddypress.activities.A.more.progress.clear(objectID, objectType);
                    switch (response){
                        case 'fail':
                            message('Error', 'Cannot get access to activities.');
                            break;
                        default :
                            try{
                                data = JSON.parse(response);
                                if (parseInt(data.shown, 10) > 0){
                                    pure.buddypress.activities.A.more.counter.updateShown(objectID, objectType, shown + parseInt(data.shown, 10));
                                    pure.buddypress.activities.A.templates.activities.root.action.addFromMore(objectID, objectType, data.activities, mark);
                                    pure.buddypress.activities.A.more.mana(data.activities, objectID);
                                    pure.buddypress.activities.A.remove.init();
                                }
                            }catch (e){
                                message('Error', 'Sorry, but some unknown error was during getting response from server.');
                            }
                            break;
                    }
                },
                onError     : function(id_request, objectID, objectType){
                    pure.buddypress.activities.A.more.progress.clear(objectID, objectType);
                    pure.buddypress.activities.A.dialogs.info('Server error', 'Server did not proceed request. Please, try a bit later.');
                }
            },
            counter     : {
                updateShown     : function(objectID, objectType, shown){
                    var nodes = {
                        next    : pure.nodes.select.all('*[data-engine-activity-objectID="' + objectID + '"][data-engine-activity-objectType="' + objectType + '"][data-engine-activity-element="Activities.More.Package"][data-engine-element-inited]'),
                        all     : pure.nodes.select.all('*[data-engine-activity-objectID="' + objectID + '"][data-engine-activity-objectType="' + objectType + '"][data-engine-activity-element="Activities.More.All"][data-engine-element-inited]'),
                        labels  : pure.nodes.select.all('*[data-engine-activity-objectID="' + objectID + '"][data-engine-activity-objectType="' + objectType + '"][data-engine-activity-element="Activities.Counter.Shown"]')
                    };
                    if (nodes.next !== null){
                        for(var index = nodes.next.length - 1; index >= 0; index -= 1){
                            nodes.next[index].setAttribute('data-engine-activity-more-shown', shown);
                        }
                    }
                    if (nodes.all !== null){
                        for(var index = nodes.all.length - 1; index >= 0; index -= 1){
                            nodes.all[index].setAttribute('data-engine-activity-more-shown', shown);
                        }
                    }
                    if (nodes.labels !== null){
                        for(var index = nodes.labels.length - 1; index >= 0; index -= 1){
                            nodes.labels[index].innerHTML = shown;
                        }
                    }
                },
                deepUpdateShown   : function(objectID, objectType){
                    var rootComments = pure.nodes.select.all('*[data-engine-activity-type="root"][data-engine-activity-objectID="' + objectID + '"]');
                    if (rootComments !== null){
                        pure.buddypress.activities.A.more.counter.updateShown(objectID, objectType, rootComments.length);
                    }
                },
                deepUpdateTotal : function(objectID, change){
                    var labels  = pure.nodes.select.all('*[data-engine-activity-objectID="' + objectID + '"][data-engine-activity-element="Activities.Counter.Total"]'),
                        value   = null;
                    if (labels !== null){
                        if (labels.length > 0){
                            value = (labels[0].innerHTML !== '' ? parseInt(labels[0].innerHTML, 10) : 0);
                            value += change;
                            for(var index = labels.length - 1; index >= 0; index -= 1){
                                labels[index].innerHTML = value;
                            }
                        }
                    }
                },
                updateInclude   : function(activityID, objectID, objectType){
                    function setFlag(activityID, isActive){
                        var flags = pure.nodes.select.all('*[data-engine-activity-activityID="' + activityID + '"] *[data-engine-activity-included-flag]'),
                            value = null;
                        if (flags !== null){
                            for (var index = flags.length - 1; index >= 0; index -= 1){
                                value = flags[index].getAttribute('data-engine-activity-included-flag');
                                if (value !== null && value !== ''){
                                    if (isActive === false){
                                        flags[index].removeAttribute(value);
                                    }else{
                                        flags[index].setAttribute(value, 'true');
                                    }
                                }
                            }
                        }
                    };
                    var activityID      = (typeof activityID    !== 'undefined' ? activityID    : null),
                        objectID        = (typeof objectID      !== 'undefined' ? objectID      : null),
                        objectType      = (typeof objectType    !== 'undefined' ? objectType    : null),
                        nodes           = null,
                        value           = null,
                        included        = null;
                    if (activityID === null){
                        if (objectID !== null && objectType !== null){
                            nodes = pure.nodes.select.all('*[data-engine-activity-objectID="' + objectID + '"][data-engine-activity-objectType="' + objectType + '"] *[data-engine-activity-included="Count"]');
                        }else{
                            nodes = pure.nodes.select.all('*[data-engine-activity-included="Count"]');
                        }
                    }else{
                        nodes = pure.nodes.select.all('*[data-engine-activity-activityID="' + activityID + '"] *[data-engine-activity-included="Count"]');
                    }
                    if (nodes !== null){
                        for(var index = nodes.length - 1; index >= 0; index -= 1){
                            if (activityID !== null){
                                included    = pure.nodes.select.all('*[data-engine-activity-included="Container"][data-engine-activity-activityID="' + activityID + '"] > *[data-engine-activity-objectID]');
                                value       = (included !== null ? included.length : 0);
                            }else{
                                value       = (nodes[index].innerHTML !== '' ? parseInt(nodes[index].innerHTML, 10) : 0);
                            }
                            nodes[index].innerHTML = value;
                            if (value === 0){
                                nodes[index].parentNode.style.display = 'none';
                            }else{
                                nodes[index].parentNode.style.display = '';
                            }
                            if (activityID !== null){
                                setFlag(activityID, (value === 0 ? false : true));
                            }
                        }
                    }
                }
            },
            mana        : function(activities, objectID){
                function collectIDs(activities){
                    for(var index = activities.length - 1; index >= 0; index -= 1){
                        IDs.push(
                            {
                                object_id   : parseInt(activities[index].id,        10),
                                user_id     : (typeof activities[index].user_id !== 'undefined' ? parseInt(activities[index].user_id,   10) : parseInt(activities[index].user.id,   10))
                            }
                        );
                        pure.buddypress.activities.A.templates.common.normalizeChildren(activities[index]);
                        if (activities[index].children instanceof Array){
                            collectIDs(activities[index].children);
                        }
                    }
                };
                var IDs = [];
                collectIDs(activities, objectID);
                pure.appevents.Actions.call(
                    'pure.mana.icons',
                    'new',
                    {
                        object  :'activity',
                        field   :objectID,
                        IDs     :IDs
                    }
                );
            }
        },
        memes       : {
            data        : null,
            load        : {
                send        : function(){
                    var request = pure.buddypress.activities.configuration.requests.getMemes;
                    if (pure.buddypress.activities.configuration.allowMemes === 'on'){
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.buddypress.activities.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.activities.A.memes.load.onRecieve(id_request, response);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.activities.A.memes.load.onError(event, id_request);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.buddypress.activities.A.memes.load.onError(event, id_request);
                            }
                        });
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
                                        pure.buddypress.activities.A.memes.data = data;
                                        return true;
                                    }
                                }
                            }catch (e){
                            }
                            break;
                    }
                    return false;
                },
                onError     : function(event, id_request){
                    //do nothing
                }
            },
            buttons : {
                init : function(button, params){
                    var data = pure.buddypress.activities.A.memes.data;
                    if (data !== null){
                        pure.events.add(
                            button,
                            'click',
                            function(){
                                pure.buddypress.activities.A.memes.select.show(params);
                            }
                        );
                    }else{
                        button.parentNode.removeChild(button);
                    }
                }
            },
            select : {
                dialogID    : null,
                show        : function(params){
                    var memes   = '',
                        data    = pure.buddypress.activities.A.memes.data;
                    if (data instanceof Array){
                        for(var index = data.length - 1; index >= 0; index -= 1){
                            memes += '<a data-post-element-type="Pure.BuddyPress.Activities.A.Meme.Dialog" data-meme-src="' + data[index] + '"><img alt="" data-post-element-type="Pure.BuddyPress.Activities.A.Meme.Dialog" src="' + data[index] + '" /></a>';
                        }
                        pure.buddypress.activities.A.memes.select.dialogID = pure.components.dialogs.B.open({
                            title       : 'Memes',
                            innerHTML   : '<div data-post-element-type="Pure.BuddyPress.Activities.A.Memes.Dialog">' + memes + '</div>',
                            width       : 70,
                            parent      : document.body,
                            fullHeight  : true,
                            afterInit   : function(){
                                pure.buddypress.activities.A.memes.select.afterInit(params);
                            },
                            buttons     : [
                                {
                                    title       : 'CANCEL',
                                    handle      : null,
                                    closeAfter  : true
                                }
                            ]
                        });
                    }
                },
                afterInit   : function(params){
                    var memes = pure.nodes.select.all('a[data-meme-src]');
                    if (memes !== null){
                        for(var index = memes.length - 1; index >= 0; index -= 1){
                            (function(node){
                                var memeSrc = node.getAttribute('data-meme-src');
                                if (memeSrc !== ''){
                                    pure.events.add(
                                        node,
                                        'click',
                                        function(){
                                            pure.buddypress.activities.A.message.sending.send(
                                                params,
                                                '[meme:begin]' + memeSrc + '[meme:end]'
                                            );
                                            if (pure.buddypress.activities.A.memes.select.dialogID !== null){
                                                pure.components.dialogs.B.close(
                                                    pure.buddypress.activities.A.memes.select.dialogID
                                                );
                                                pure.buddypress.activities.A.memes.select.dialogID = null;
                                            }
                                        }
                                    );
                                }
                            }(memes[index]));
                        }
                    }
                }
            },
            init : function(){
                pure.buddypress.activities.A.memes.load.send();
            }
        },
        hotUpdate   : {
            init        : function(){
                if (pure.buddypress.activities.configuration.hotUpdate === 'on'){
                    pure.appevents.Actions.listen(
                        'webSocketServerEvents',
                        'post_comment',
                        pure.buddypress.activities.A.hotUpdate.processing,
                        'post_comment_update_handle'
                    );
                }
            },
            call        : function(){
                if (pure.buddypress.activities.configuration.hotUpdate === 'on'){
                    //Server notification
                    pure.appevents.Actions.call('webSocketsServer','wakeup', null, null);
                }
            },
            processing  : function(params){
                var parameters = (typeof params.parameters === 'object' ? params.parameters : null);
                if (parameters !== null){
                    if (typeof parameters.comment_id    !== 'undefined' &&
                        typeof parameters.post_id       !== 'undefined' &&
                        typeof parameters.created       !== 'undefined'){
                        pure.buddypress.activities.A.hotUpdate.send.send(parameters.post_id, parameters.created);
                    }
                }
            },
            send : {
                send : function(objectID, afterDate){
                    var testNode    = pure.nodes.select.first('*[data-engine-activity-more-mark="' + objectID + '"]'),
                        request     = pure.buddypress.activities.configuration.requests.update;
                    if (testNode !== null){
                        if (pure.buddypress.activities.configuration.hotUpdate === 'on'){
                            request = request.replace(/\[post_id\]/gi,      objectID      );
                            request = request.replace(/\[after_date\]/gi,   afterDate   );
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : pure.buddypress.activities.configuration.requestURL,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.buddypress.activities.A.hotUpdate.send.onRecieve(id_request, response, objectID);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.buddypress.activities.A.hotUpdate.send.onError(event, id_request, objectID);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.buddypress.activities.A.hotUpdate.send.onError(event, id_request, objectID);
                                }
                            });
                        }
                    }
                },
                onRecieve   : function(id_request, response, objectID){
                    var data    = null;
                    try{
                        data = JSON.parse(response);
                        if (parseInt(data.shown, 10) > 0){
                            pure.buddypress.activities.A.templates.activities.root.action.addFromHotUpdate(objectID, data.comments);
                        }
                    }catch (e){
                    }
                },
                onError     : function(id_request, objectID){
                    //Do nothing. It's background process
                }
            }
        },
        quotes      : {
            current     : null,
            onSelection : function(event, editorID, activityID, objectID){
                function getAttributes(node){
                    var attributes = {
                        mark        : node.getAttribute('data-engine-activity-element'   ),
                        activityID   : node.getAttribute('data-engine-activity-activityID' )
                    };
                    if (pure.tools.objects.isValueIn(attributes, null   ) === false &&
                        pure.tools.objects.isValueIn(attributes, ''     ) === false){
                        return (attributes.mark === 'Activity.Value' ? attributes : null);
                    }
                    return null;
                }
                function getPost(node){
                    var container   = pure.nodes.find.parentByAttr(node, { name : 'data-engine-activity-objectID', value: null}),
                        objectID      = null;
                    if (container !== null){
                        objectID = container.getAttribute('data-engine-activity-objectID');
                        objectID = (objectID !== '' ? objectID : null);
                        if (objectID !== null){
                            return {
                                objectID    : objectID,
                                container   : container
                            }
                        }
                    }
                    return null;
                }
                function getSelected(){
                    var text = "";
                    if (window.getSelection) {
                        text = window.getSelection().toString();
                    }else if (document.getSelection) {
                        text = document.getSelection().toString();
                    }else if (document.selection) {
                        text = document.selection.createRange().text;
                    }
                    return text;
                }
                function getAuthorAndTime(objectID, activityID){
                    var author  = pure.nodes.select.first('*[data-engine-activity-objectID="' + objectID + '"][data-engine-activity-activityID="' + activityID + '"] *[data-engine-activity-element="Activity.Author.Name"]'),
                        time    = pure.nodes.select.first('*[data-engine-activity-objectID="' + objectID + '"][data-engine-activity-activityID="' + activityID + '"] *[data-engine-activity-element="Activity.DateTime"]');
                    if (author !== null && time !== null){
                        return {
                            author  : author.innerHTML,
                            time    : time.innerHTML
                        };
                    }
                    return null;
                }
                var post        = null,
                    selection   = null,
                    range       = null,
                    parent      = null,
                    attributes  = null,
                    info        = null;
                if (typeof window.getSelection != "undefined") {
                    selection = window.getSelection();
                    if (selection.rangeCount) {
                        range       = selection.getRangeAt(0);
                        if (range.startContainer.parentNode === range.endContainer.parentNode){
                            parent      = range.startContainer.parentNode;
                            attributes  = getAttributes(parent);
                            if (attributes !== null){
                                post = getPost(parent);
                                if (post !== null){
                                    info = getAuthorAndTime(post.objectID, attributes.activityID);
                                    if (info !== null){
                                        pure.buddypress.activities.A.quotes.current = {
                                            text        : getSelected(),
                                            activityID  : attributes.activityID,
                                            objectID    : post.objectID,
                                            author      : info.author,
                                            time        : info.time
                                        };
                                        pure.buddypress.activities.A.templates.notification.show(parent.parentNode);
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
                pure.buddypress.activities.A.quotes.current = null;
                return false;
            },
            onClick     : function(editorID, objectID){
                function getPosition (editor) {
                    // Initialize
                    var iCaretPos = 0;
                    // IE Support
                    if (document.selection) {
                        // Set focus on the element
                        editor.focus ();
                        // To get cursor position, get empty selection range
                        var oSel = document.selection.createRange ();
                        // Move selection start to 0 position
                        oSel.moveStart ('character', -editor.value.length);
                        // The caret position is selection length
                        iCaretPos = oSel.text.length;
                    }
                    // Firefox support
                    else if (editor.selectionStart || editor.selectionStart == '0')
                        iCaretPos = editor.selectionStart;
                    // Return results
                    return (iCaretPos);
                }
                var current     = pure.buddypress.activities.A.quotes.current,
                    editor      = pure.nodes.select.first('*[data-engine-activity-editor="' + editorID + '"] textarea'),
                    quote       = '',
                    position    = null,
                    value       = '';
                if (current !== null && editor !== null){
                    if (current.objectID === objectID && current.text !== ''){
                        quote       =   '[quote:begin]' +
                                            '[author:begin]' + current.author + '[author:end]' +
                                            '[date:begin]' + current.time + '[date:end]' +
                                            current.text +
                                        '[quote:end]';
                        position        = getPosition(editor);
                        value           = editor.value;
                        editor.value    = value.substr(0, position) + '\x0A' + quote + '\x0A' + value.substr(position, value.length);
                        pure.buddypress.activities.A.quotes.current = null;
                    }
                }
            }
        },
        dialogs     : {
            info: function (title, message) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-post-element-type="Pure.BuddyPress.Activities.A.Dialog">' + message + '</p>',
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
            question: function (title, message, yes_title, handle) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-post-element-type="Pure.BuddyPress.Activities.A.Dialog">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : [
                        {
                            title       : 'CANCEL',
                            handle      : null,
                            closeAfter  : true
                        },
                        {
                            title       : yes_title,
                            handle      : handle,
                            closeAfter  : true
                        }
                    ]
                });
            }
        },
        isPossible  : function(){
            var result = true;
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.user_id'              ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.allowMemes'           ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.allowAttachment'      ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.allowRemoveActivity'  ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.allowRemoveComment'   ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.maxLength'            ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.hotUpdate'            ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.requestURL'           ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.requests.getMemes'    ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.requests.sendComment' ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.requests.sendPost'    ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.requests.remove'      ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.requests.more'        ) === null ? false : true));
            //result = (result === false ? false : (pure.system.getInstanceByPath('pure.buddypress.activities.configuration.requests.update'     ) === null ? false : true));
            if (result !== false){
                pure.buddypress.activities.configuration.user_id                = parseInt(pure.buddypress.activities.configuration.user_id,                10);
                pure.buddypress.activities.configuration.maxLength              = parseInt(pure.buddypress.activities.configuration.maxLength,              10);
                pure.buddypress.activities.configuration.allowRemoveActivity    = parseInt(pure.buddypress.activities.configuration.allowRemoveActivity,    10);
                pure.buddypress.activities.configuration.allowRemoveComment     = parseInt(pure.buddypress.activities.configuration.allowRemoveComment,     10);
                pure.buddypress.activities.configuration.allowRemoveActivity    = (pure.buddypress.activities.configuration.allowRemoveActivity === 1 ? true : false);
                pure.buddypress.activities.configuration.allowRemoveComment     = (pure.buddypress.activities.configuration.allowRemoveComment  === 1 ? true : false);
            }
            return result;
        },
        init        : function(){
            if (pure.buddypress.activities.A.isPossible() !== false){
                pure.buddypress.activities.A.templates. init();
                pure.buddypress.activities.A.memes.     init();
                pure.buddypress.activities.A.more.      init();
                pure.buddypress.activities.A.remove.    init();
                //pure.buddypress.activities.A.hotUpdate.init();
            }
        }
    };
    pure.system.start.add(pure.buddypress.activities.A.init);
}());