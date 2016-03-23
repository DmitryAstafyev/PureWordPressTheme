(function () {
    if (typeof window.pure                  !== "object") { window.pure                 = {}; }
    if (typeof window.pure.comments         !== "object") { window.pure.comments        = {}; }
    if (typeof window.pure.comments.posts   !== "object") { window.pure.comments.posts  = {}; }
    "use strict";
    window.pure.comments.posts.A = {
        message     : {
            sending    : {
                progress    : {
                    data    : {},
                    is      : function(editorID){
                        return (typeof pure.comments.posts.A.message.sending.progress.data[editorID] === 'undefined' ? false : true);
                    },
                    set     : function(editorID, container){
                        var progress = pure.templates.progressbar.A.show(
                            container,
                            'background:rgba(255,255,255,0.7);',
                            '',
                            '',
                            'wait please'
                        );
                        pure.comments.posts.A.message.sending.progress.data[editorID] = progress;
                    },
                    clear   : function(editorID){
                        if (typeof pure.comments.posts.A.message.sending.progress.data[editorID] !== 'undefined'){
                            pure.templates.progressbar.A.hide(pure.comments.posts.A.message.sending.progress.data[editorID]);
                            pure.comments.posts.A.message.sending.progress.data[editorID] = null;
                            delete pure.comments.posts.A.message.sending.progress.data[editorID];
                        }
                    }
                },
                reset       : function(editorID){
                    var editor = pure.nodes.select.first('*[data-engine-comment-editor="' + editorID + '"] textarea');
                    if (editor !== null){
                        editor.value = '';
                        pure.wordpress.media.images.reset('*[data-engine-comment-editor="' + editorID + '"]');
                    }
                },
                send        : function(editorID, commentID, message){
                    var editor      = pure.nodes.select.first('*[data-engine-comment-editor="' + editorID + '"] textarea'),
                        container   = pure.nodes.select.first('*[data-engine-comment-editor="' + editorID + '"]'),
                        attachment  = pure.nodes.select.first('img[data-storage-id="' + commentID + '"]'),
                        request     = null,
                        message     = (typeof message !== 'string' ? null : message);
                    if (editor !== null && container !== null){
                        if (pure.comments.posts.A.message.sending.progress.is(editorID) === false){
                            if (editor.value.length > 1 || message !== null){
                                if (editor.value.length < pure.comments.posts.configuration.maxLength){
                                    if (attachment !== null){
                                        attachment = attachment.getAttribute('pure-wordpress-media-images-id');
                                        attachment = (attachment !== null ? parseInt(attachment, 10) : 0);
                                        attachment = (attachment > 0 ? attachment : 0);
                                    }
                                    pure.comments.posts.A.message.sending.progress.set(editorID, container);
                                    message = (message !== null ? message : editor.value);
                                    request = pure.comments.posts.configuration.requests.send;
                                    request = request.replace(/\[comment\]/gi,          message     );
                                    request = request.replace(/\[comment_id\]/gi,       commentID   );
                                    request = request.replace(/\[attachment_id\]/gi,    attachment  );
                                    pure.tools.request.send({
                                        type        : 'POST',
                                        url         : pure.comments.posts.configuration.requestURL,
                                        request     : request,
                                        onrecieve   : function (id_request, response) {
                                            pure.comments.posts.A.message.sending.onRecieve(id_request, response, editorID);
                                        },
                                        onreaction  : null,
                                        onerror     : function (id_request) {
                                            pure.comments.posts.A.message.sending.onError(id_request, editorID);
                                        },
                                        ontimeout   : function (id_request) {
                                            pure.comments.posts.A.message.sending.onError(id_request, editorID);
                                        }
                                    });
                                }else{
                                    pure.comments.posts.A.dialogs.info('Cannot do it', 'Your message is too big. You can use only ' + pure.comments.posts.configuration.maxLength + ' symbols.');
                                }
                            }else{
                                pure.comments.posts.A.dialogs.info('Cannot do it', 'Write some message to send it. ');
                            }
                        }
                    }
                },
                onRecieve   : function(id_request, response, editorID){
                    var message = pure.comments.posts.A.dialogs.info,
                        data    = null;
                    pure.comments.posts.A.message.sending.progress.clear(editorID);
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
                                pure.comments.posts.A.message.sending.reset(editorID);
                                if (parseInt(data.parent, 10) === 0) {
                                    pure.comments.posts.A.templates.comments.root.action.add(data);
                                    pure.comments.posts.A.more.counter.deepUpdateTotal(data.post_id, 1);
                                }else{
                                    pure.comments.posts.A.templates.comments.included.action.add(data);
                                }
                                pure.comments.posts.A.more.mana([data]);
                                pure.comments.posts.A.hotUpdate.call();
                            }catch (e){
                                message('Error', 'Sorry, but some unknown error was during getting responce from server.');
                            }
                            break;
                    }
                },
                onError     : function(id_request, editorID){
                    pure.comments.posts.A.message.sending.progress.clear(editorID);
                    pure.comments.posts.A.dialogs.info('Server error', 'Server did not proceed request. Please, try a bit later.');
                }
            }
        },
        templates   : {
            common          : {
                init            : function(element, storagePath){
                    function getNodeTemplate(outerHTML){
                        var node = document.createElement('div');
                        node.innerHTML = outerHTML;
                        return (node.childNodes.length === 1 ? node.childNodes[0] : null);
                    }
                    var templates   = pure.system.getInstanceByPath('pure.comments.posts.configuration.templates.' + element),
                        data        = pure.system.getInstanceByPath(storagePath);
                    if (templates !== null){
                        for(var postID in templates){
                            if (templates[postID] !== null && templates[postID] !== ""){
                                (function(template, postID){
                                    template = pure.convertor.BASE64.decode(template);
                                    template = pure.convertor.UTF8.  decode(template);
                                    template = getNodeTemplate(template);
                                    if (typeof data[postID] === 'undefined'){
                                        data[postID] = {
                                            innerHTML   : template.innerHTML,
                                            attributes  : pure.nodes.attributes.get(template, ['data-engine-comment-element', 'style']),
                                            nodeName    : template.nodeName
                                        };
                                    }
                                }(templates[postID], postID));
                            }
                        }
                        return data;
                    }
                    return null;
                },
                isMeme          : function(message){
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
                hasAttachment   : function(message){
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
                                comment : clearMessage,
                                url     : result
                            };
                        }
                    }
                    return null;
                },
                parseComment    : function(message, commentNode, quoteNode){
                    function addComment(commentNode, nodes, strValue){
                        var _node = null;
                        if (strValue !== ''){
                            _node = commentNode.cloneNode(true);
                            _node.innerHTML = _node.innerHTML.replace(/\[comment\]/gi, strValue.replace(/[\n\r]/gim, '<br/>'));
                            nodes.push(_node);
                        }
                    };
                    function addQuote(quoteNode, nodes, strValue){
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
                    var quotes      = message.match(/\[quote:begin\](.|\n|\r|\n\r(?!\[quote:begin\]))*?\[quote:end\]/gim),
                        _message    = message,
                        parts       = null,
                        separator   = 'sep' + (Math.random()*10000 + Math.random()*10000) + 'sep',
                        nodes       = [];
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
                                addComment(commentNode, nodes, parts[index]);
                                if (typeof quotes[index] !== 'undefined'){
                                    addQuote(quoteNode, nodes, quotes[index]);
                                }
                            }
                            return nodes;
                        }
                    }
                    addComment(commentNode, nodes, message);
                    return nodes;
                }
            },
            editor          : {
                data    : {},
                init    : function(){
                    pure.comments.posts.A.templates.common.init(
                        'editor',
                        'pure.comments.posts.A.templates.editor.data'
                    );
                },
                getData : function(postID){
                    var data = pure.comments.posts.A.templates.editor.data;
                    return (typeof data[postID] !== 'undefined' ? data[postID] : null);
                },
                actions : {
                    show : function(params){
                        var data        = pure.comments.posts.A.templates.editor.getData(params.postID),
                            editor      = null,
                            mark        = null,
                            buttons     = null;
                        if (data !== null){
                            editor  = pure.nodes.select.first('*[data-engine-comment-editor="' + params.editorID + '"]');
                            if (editor !== null){
                                //Remove previous
                                editor.parentNode.removeChild(editor);
                            }else{
                                //Create new
                                mark    = pure.nodes.select.first('*[data-engine-comment-mark="' + params.editorID + '"]');
                                if (mark !== null){
                                    editor              = document.createElement(data.nodeName);
                                    pure.nodes.attributes.set(editor, data.attributes);
                                    editor.innerHTML    = data.     innerHTML.replace(/\[editorID\]/gi,     params.editorID );
                                    editor.innerHTML    = editor.   innerHTML.replace(/\[commentID\]/gi,    params.commentID);
                                    editor.setAttribute('data-engine-comment-editor',       params.editorID );
                                    mark.parentNode.insertBefore(editor, mark);
                                    buttons = {
                                        send        : pure.nodes.select.first('*[data-engine-comment-editor="' + params.editorID + '"] *[ data-engine-comment-element="Editor.Button.Send"]'        ),
                                        quote       : pure.nodes.select.first('*[data-engine-comment-editor="' + params.editorID + '"] *[ data-engine-comment-element="Editor.Button.Quote"]'       ),
                                        attachment  : pure.nodes.select.first('*[data-engine-comment-editor="' + params.editorID + '"] *[ data-engine-comment-element="Editor.Button.Attachment"]'  ),
                                        meme        : pure.nodes.select.first('*[data-engine-comment-editor="' + params.editorID + '"] *[ data-engine-comment-element="Editor.Button.Meme"]'        )
                                    };
                                    if (buttons.send !== null){
                                        pure.events.add(
                                            buttons.send,
                                            'click',
                                            function(){
                                                pure.comments.posts.A.message.sending.send(params.editorID, params.commentID);
                                            }
                                        );
                                    }
                                    if (buttons.quote !== null){
                                        pure.events.add(
                                            buttons.quote,
                                            'click',
                                            function(){
                                                pure.comments.posts.A.quotes.onClick(params.editorID, params.postID);
                                            }
                                        );
                                    }
                                    pure.comments.posts.A.memes.buttons.init(buttons.meme, params.editorID, params.commentID);
                                    pure.wordpress.media.images.init();
                                }
                            }
                        }
                    }
                }
            },
            callers         : {
                editors : function(){
                    var callers = pure.nodes.select.all('*[data-engine-comment-element="Editor.Caller"]:not([data-engine-element-inited])');
                    if (callers !== null){
                        for(var index = callers.length - 1; index >= 0; index -= 1){
                            (function(caller){
                                var params = {
                                    postID      : caller.getAttribute('data-engine-comment-postID'      ),
                                    commentID   : caller.getAttribute('data-engine-comment-commentID'   ),
                                    editorID    : caller.getAttribute('data-engine-comment-editorID'    ),
                                    event       : caller.getAttribute('data-engine-comment-event'       )
                                };
                                if (pure.tools.objects.isValueIn(params, null) === false){
                                    pure.events.add(
                                        caller,
                                        params.event,
                                        function(){
                                            pure.comments.posts.A.templates.editor.actions.show(params);
                                        }
                                    );
                                }
                                caller.setAttribute('data-engine-element-inited', 'true');
                                caller.disabled = false;
                            }(callers[index]));
                        }
                    }
                },
                quotes : function(){
                    var comments = pure.nodes.select.all('*[data-engine-comment-element="Comment.Value"]:not([data-engine-element-inited])');
                    if (comments !== null){
                        for(var index = comments.length - 1; index >= 0; index -= 1){
                            (function(comment){
                                pure.events.add(
                                    comment,
                                    'mouseup',
                                    function(event){
                                        pure.comments.posts.A.quotes.onSelection(event, null, null, null);
                                    }
                                );
                                comment.setAttribute('data-engine-element-inited', 'true');
                            }(comments[index]));
                        }
                    }
                },
                init : function(){
                    pure.comments.posts.A.templates.callers.editors();
                    pure.comments.posts.A.templates.callers.quotes();
                }
            },
            comments        : {
                root    : {
                    data    : {},
                    init    : function () {
                        pure.comments.posts.A.templates.common.init(
                            'comment',
                            'pure.comments.posts.A.templates.comments.root.data'
                        );
                    },
                    getData : function (postID) {
                        var data = pure.comments.posts.A.templates.comments.root.data;
                        return (typeof data[postID] !== 'undefined' ? data[postID] : null);
                    },
                    action  : {
                        add         : function(params, moreMark){
                            var data        = pure.comments.posts.A.templates.comments.root.getData(params.post_id),
                                template    = null,
                                mark        = pure.nodes.select.first('*[data-engine-comment-root-mark="' + params.post_id + '"]'),
                                moreMark    = (typeof moreMark !== 'undefined' ? moreMark : null),
                                nodes       = null,
                                memeSRC     = null,
                                attachment  = pure.comments.posts.A.templates.common.hasAttachment(params.comment),
                                comment     = (attachment === null ? params.comment : attachment.comment),
                                comments    = null;
                            if (data !== null && mark !== null){
                                template              = document.createElement(data.nodeName);
                                pure.nodes.attributes.set(template, data.attributes);
                                template.setAttribute('data-engine-comment-commentID',  params.id   );
                                template.innerHTML    = data.       innerHTML.replace(/\[name\]/gi,        params.name         );
                                template.innerHTML    = template.   innerHTML.replace(/\[avatar\]/gi,      params.avatar       );
                                template.innerHTML    = template.   innerHTML.replace(/\[date\]/gi,        params.date         );
                                template.innerHTML    = template.   innerHTML.replace(/\[home\]/gi,        params.home         );
                                //template.innerHTML    = template.   innerHTML.replace(/\[comment\]/gi,     comment             );
                                template.innerHTML    = template.   innerHTML.replace(/\[comment_id\]/gi,  params.id           );
                                template.innerHTML    = template.   innerHTML.replace(/\[post_id\]/gi,     params.post_id      );
                                if (moreMark !== null){
                                    moreMark.parentNode.insertBefore(template, moreMark);
                                }else{
                                    pure.nodes.insertAfter(template, mark);
                                    pure.comments.posts.A.more.counter.updateInclude(params.id);
                                    pure.comments.posts.A.more.counter.deepUpdateShown(params.post_id);
                                }
                                nodes = {
                                    comment     : pure.nodes.select.first('*[data-engine-comment-commentID="' + params.id + '"][data-engine-comment-element="Comment.Value"]'       ),
                                    quote       : pure.nodes.select.first('*[data-engine-comment-commentID="' + params.id + '"][data-engine-comment-element="Comment.Quote"]'       ),
                                    attachment  : pure.nodes.select.first('*[data-engine-comment-commentID="' + params.id + '"][data-engine-comment-element="Comment.Attachment"]'  ),
                                    meme        : pure.nodes.select.first('*[data-engine-comment-commentID="' + params.id + '"][data-engine-comment-element="Comment.Meme"]'        )
                                };
                                if (nodes.meme !== null){
                                    memeSRC = pure.comments.posts.A.templates.common.isMeme(params.comment);
                                    if (memeSRC !== null){
                                        nodes.meme.innerHTML = nodes.meme.innerHTML.replace(/\[meme\]/gi, memeSRC);
                                        nodes.comment.  parentNode.removeChild(nodes.comment);
                                        nodes.quote.    parentNode.removeChild(nodes.quote  );
                                        nodes.comment   = null;
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
                                if (nodes.comment !== null && nodes.quote !== null){
                                    comments = pure.comments.posts.A.templates.common.parseComment(
                                        comment,
                                        nodes.comment,
                                        nodes.quote
                                    );
                                    for(var index = 0, max_index = comments.length; index < max_index; index += 1){
                                        nodes.comment.parentNode.insertBefore(comments[index], nodes.comment);
                                    }
                                    nodes.comment.  parentNode.removeChild(nodes.comment);
                                    nodes.quote.    parentNode.removeChild(nodes.quote  );
                                }
                                pure.comments.posts.A.templates.callers.init();
                            }
                        },
                        addFromMore : function(postID, comments, mark){
                            function addChildren(postID, comments){
                                for(var index = 0, max_index = comments.length; index < max_index; index += 1){
                                    pure.comments.posts.A.templates.comments.included.action.add(
                                        parseRecord(comments[index], postID)
                                    );
                                    if (typeof comments[index].children !== 'undefined'){
                                        if (comments[index].children instanceof Array){
                                            addChildren(postID, comments[index].children);
                                        }
                                    }
                                }
                            };
                            function parseRecord(source, postID){
                                return {
                                    id      : source.comment.id,
                                    name    : source.author.name,
                                    avatar  : source.author.avatar,
                                    home    : source.author.home,
                                    date    : source.comment.date,
                                    parent  : source.comment.parent,
                                    post_id : postID,
                                    comment : source.comment.value
                                };
                            };
                            for(var index = 0, max_index = comments.length; index < max_index; index += 1){
                                pure.comments.posts.A.templates.comments.root.action.add(
                                    parseRecord(comments[index], postID),
                                    mark
                                );
                                if (typeof comments[index].children !== 'undefined'){
                                    if (comments[index].children instanceof Array){
                                        addChildren(postID, comments[index].children);
                                        pure.comments.posts.A.more.counter.updateInclude(comments[index].comment.id);
                                    }
                                }
                            }
                            pure.comments.posts.A.more.counter.updateInclude();
                        },
                        addFromHotUpdate : function(postID, comments){
                            function parseRecord(source, postID){
                                return {
                                    id      : source.comment.id,
                                    name    : source.author.name,
                                    avatar  : source.author.avatar,
                                    home    : source.author.home,
                                    date    : source.comment.date,
                                    parent  : source.comment.parent,
                                    post_id : postID,
                                    comment : source.comment.value
                                };
                            };
                            for(var index = 0, max_index = comments.length; index < max_index; index += 1) {
                                (function(comment, postID){
                                    var container = pure.nodes.select.first('*[data-engine-comment-postID="' + postID + '"][data-engine-comment-commentID="' + comment.comment.id + '"]');
                                    if (container === null){
                                        if (parseInt(comment.comment.parent, 10) === 0) {
                                            pure.comments.posts.A.templates.comments.root.action.add(parseRecord(comment, postID));
                                            pure.comments.posts.A.more.counter.deepUpdateTotal(postID, 1);
                                        }else{
                                            pure.comments.posts.A.templates.comments.included.action.add(parseRecord(comment, postID));
                                        }
                                    }
                                }(comments[index], postID));
                            }
                        }
                    }
                },
                included: {
                    data    : {},
                    init    : function () {
                        pure.comments.posts.A.templates.common.init(
                            'included_comment',
                            'pure.comments.posts.A.templates.comments.included.data'
                        );
                    },
                    getData : function (postID) {
                        var data = pure.comments.posts.A.templates.comments.included.data;
                        return (typeof data[postID] !== 'undefined' ? data[postID] : null);
                    },
                    action : {
                        add: function(params){
                            var data        = pure.comments.posts.A.templates.comments.included.getData(params.post_id),
                                template    = null,
                                nodes       = null,
                                memeSRC     = null,
                                container   = pure.nodes.select.first('*[data-engine-comment-included="Container"][data-engine-comment-commentID="' + params.parent + '"]'),
                                attachment  = pure.comments.posts.A.templates.common.hasAttachment(params.comment),
                                comment     = (attachment === null ? params.comment : attachment.comment),
                                comments    = null;
                            if (data !== null && container !== null){
                                template              = document.createElement(data.nodeName);
                                pure.nodes.attributes.set(template, data.attributes);
                                template.setAttribute('data-engine-comment-commentID',  params.id   );
                                template.innerHTML    = data.       innerHTML.replace(/\[name\]/gi,        params.name         );
                                template.innerHTML    = template.   innerHTML.replace(/\[avatar\]/gi,      params.avatar       );
                                template.innerHTML    = template.   innerHTML.replace(/\[date\]/gi,        params.date         );
                                template.innerHTML    = template.   innerHTML.replace(/\[home\]/gi,        params.home         );
                                //template.innerHTML    = template.   innerHTML.replace(/\[comment\]/gi,     comment             );
                                template.innerHTML    = template.   innerHTML.replace(/\[comment_id\]/gi,  params.id           );
                                template.innerHTML    = template.   innerHTML.replace(/\[post_id\]/gi,     params.post_id      );
                                if (container.childNodes.length > 0){
                                    container.insertBefore(template, container.firstChild);
                                }else{
                                    container.appendChild(template);
                                }
                                pure.comments.posts.A.more.counter.updateInclude(params.parent);
                                nodes = {
                                    comment     : pure.nodes.select.first('*[data-engine-comment-commentID="' + params.id + '"][data-engine-comment-element="Comment.Value"]'       ),
                                    quote       : pure.nodes.select.first('*[data-engine-comment-commentID="' + params.id + '"][data-engine-comment-element="Comment.Quote"]'       ),
                                    attachment  : pure.nodes.select.first('*[data-engine-comment-commentID="' + params.id + '"][data-engine-comment-element="Comment.Attachment"]'  ),
                                    meme        : pure.nodes.select.first('*[data-engine-comment-commentID="' + params.id + '"][data-engine-comment-element="Comment.Meme"]'        )
                                };
                                if (nodes.meme !== null){
                                    memeSRC = pure.comments.posts.A.templates.common.isMeme(params.comment);
                                    if (memeSRC !== null){
                                        nodes.meme.innerHTML = nodes.meme.innerHTML.replace(/\[meme\]/gi, memeSRC);
                                        nodes.comment.  parentNode.removeChild(nodes.comment);
                                        nodes.quote.    parentNode.removeChild(nodes.quote  );
                                        nodes.comment   = null;
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
                                if (nodes.comment !== null && nodes.quote !== null){
                                    comments = pure.comments.posts.A.templates.common.parseComment(
                                        comment,
                                        nodes.comment,
                                        nodes.quote
                                    );
                                    for(var index = 0, max_index = comments.length; index < max_index; index += 1){
                                        nodes.comment.parentNode.insertBefore(comments[index], nodes.comment);
                                    }
                                    nodes.comment.  parentNode.removeChild(nodes.comment);
                                    nodes.quote.    parentNode.removeChild(nodes.quote  );
                                }
                                pure.comments.posts.A.templates.callers.init();
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
                    var templates = pure.system.getInstanceByPath('pure.comments.posts.configuration.templates.quote');
                    if (templates !== null){
                        for(var postID in templates){
                            (function(template, postID){
                                template = pure.convertor.BASE64.decode(template);
                                template = pure.convertor.UTF8.  decode(template);
                                pure.comments.posts.A.templates.notification.data = getNodeTemplate(template);
                            }(templates[postID], postID));
                        }
                        return true;
                    }
                    return null;
                },
                show : function(parent){
                    var data = pure.comments.posts.A.templates.notification.data,
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
            init        : function(){
                pure.comments.posts.A.templates.editor.             init();
                pure.comments.posts.A.templates.comments.root.      init();
                pure.comments.posts.A.templates.comments.included.  init();
                pure.comments.posts.A.templates.notification.       init();
                //Callers should be last to don't touch templates
                pure.comments.posts.A.templates.callers.            init();
            }
        },
        more        : {
            init        : function(){
                function proceed(buttons, type){
                    if (buttons !== null){
                        for(var index = buttons.length - 1; index >= 0; index -= 1){
                            (function(button, type){
                                var postID  = button.getAttribute('data-engine-comment-postID'),
                                    mark    = null;
                                if (postID !== null && postID !== ''){
                                    mark = pure.nodes.select.first('*[data-engine-comment-more-mark="' + postID + '"]');
                                    if(mark !== null){
                                        pure.events.add(
                                            button,
                                            'click',
                                            function(){
                                                pure.comments.posts.A.more.get.send(postID, button, type, mark);
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
                        next: pure.nodes.select.all('*[data-engine-comment-element="Comments.More.Package"]:not([data-engine-element-inited])'),
                        all : pure.nodes.select.all('*[data-engine-comment-element="Comments.More.All"]:not([data-engine-element-inited])')
                    };
                proceed(buttons.next,   'next'  );
                proceed(buttons.all,    'all'   );
                pure.comments.posts.A.more.counter.updateInclude();
            },
            progress    : {
                data    : {},
                is      : function(postID){
                    return (typeof pure.comments.posts.A.more.progress.data[postID] === 'undefined' ? false : true);
                },
                set     : function(postID, mark){
                    var progress = pure.templates.progressbar.D.show(mark);
                    pure.comments.posts.A.more.progress.data[postID] = progress;
                },
                clear   : function(postID){
                    if (typeof pure.comments.posts.A.more.progress.data[postID] !== 'undefined'){
                        pure.templates.progressbar.A.hide(pure.comments.posts.A.more.progress.data[postID]);
                        pure.comments.posts.A.more.progress.data[postID] = null;
                        delete pure.comments.posts.A.more.progress.data[postID];
                    }
                }
            },
            get         : {
                send        : function(postID, button, type, mark){
                    var shown   = null,
                        allFlag = '',
                        request = '';
                    if (pure.comments.posts.A.more.progress.is(postID) === false){
                        pure.comments.posts.A.more.progress.set(postID, mark);
                        shown   = button.getAttribute('data-engine-comment-more-shown');
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
                        request = pure.comments.posts.configuration.requests.more;
                        request = request.replace(/\[post_id\]/gi,  postID  );
                        request = request.replace(/\[shown\]/gi,    shown   );
                        request = request.replace(/\[all\]/gi,      allFlag );
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.comments.posts.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.comments.posts.A.more.get.onRecieve(id_request, response, postID, button, mark, shown);
                            },
                            onreaction  : null,
                            onerror     : function (id_request) {
                                pure.comments.posts.A.more.get.onError(id_request, postID);
                            },
                            ontimeout   : function (id_request) {
                                pure.comments.posts.A.more.get.onError(id_request, postID);
                            }
                        });
                    }
                },
                onRecieve   : function(id_request, response, postID, button, mark, shown){
                    var message = pure.comments.posts.A.dialogs.info,
                        data    = null;
                    pure.comments.posts.A.more.progress.clear(postID);
                    switch (response){
                        case 'no access':
                            message('Error', 'Cannot get access to comments of this post.');
                            break;
                        default :
                            try{
                                data = JSON.parse(response);
                                if (parseInt(data.shown, 10) > 0){
                                    pure.comments.posts.A.more.counter.updateShown(postID, shown + parseInt(data.shown, 10));
                                    pure.comments.posts.A.templates.comments.root.action.addFromMore(postID, data.comments, mark);
                                    pure.comments.posts.A.more.mana(data.comments);
                                }
                            }catch (e){
                                message('Error', 'Sorry, but some unknown error was during getting response from server.');
                            }
                            break;
                    }
                },
                onError     : function(id_request, postID){
                    pure.comments.posts.A.more.progress.clear(postID);
                    pure.comments.posts.A.dialogs.info('Server error', 'Server did not proceed request. Please, try a bit later.');
                }
            },
            counter     : {
                updateShown     : function(postID, shown){
                    var nodes = {
                        next    : pure.nodes.select.all('*[data-engine-comment-postID="' + postID + '"][data-engine-comment-element="Comments.More.Package"][data-engine-element-inited]'),
                        all     : pure.nodes.select.all('*[data-engine-comment-postID="' + postID + '"][data-engine-comment-element="Comments.More.All"][data-engine-element-inited]'),
                        labels  : pure.nodes.select.all('*[data-engine-comment-postID="' + postID + '"][data-engine-comment-element="Comments.Counter.Shown"]')
                    };
                    if (nodes.next !== null){
                        for(var index = nodes.next.length - 1; index >= 0; index -= 1){
                            nodes.next[index].setAttribute('data-engine-comment-more-shown', shown);
                        }
                    }
                    if (nodes.all !== null){
                        for(var index = nodes.all.length - 1; index >= 0; index -= 1){
                            nodes.all[index].setAttribute('data-engine-comment-more-shown', shown);
                        }
                    }
                    if (nodes.labels !== null){
                        for(var index = nodes.labels.length - 1; index >= 0; index -= 1){
                            nodes.labels[index].innerHTML = shown;
                        }
                    }
                },
                deepUpdateShown   : function(postID){
                    var rootComments = pure.nodes.select.all('*[data-engine-comment-type="root"][data-engine-comment-postID="' + postID + '"]');
                    if (rootComments !== null){
                        pure.comments.posts.A.more.counter.updateShown(postID, rootComments.length);
                    }
                },
                deepUpdateTotal : function(postID, change){
                    var labels  = pure.nodes.select.all('*[data-engine-comment-postID="' + postID + '"][data-engine-comment-element="Comments.Counter.Total"]'),
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
                updateInclude   : function(commentID){
                    function setFlag(commentID, isActive){
                        var flags = pure.nodes.select.all('*[data-engine-comment-commentID="' + commentID + '"] *[data-engine-comment-included-flag]'),
                            value = null;
                        if (flags !== null){
                            for (var index = flags.length - 1; index >= 0; index -= 1){
                                value = flags[index].getAttribute('data-engine-comment-included-flag');
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
                    var commentID   = (typeof commentID !== 'undefined' ? commentID : null),
                        nodes       = null,
                        value       = null,
                        included    = null;
                    if (commentID === null){
                        nodes = pure.nodes.select.all('*[data-engine-comment-included="Count"]');
                    }else{
                        nodes = pure.nodes.select.all('*[data-engine-comment-commentID="' + commentID + '"] *[data-engine-comment-included="Count"]');
                    }
                    if (nodes !== null){
                        for(var index = nodes.length - 1; index >= 0; index -= 1){
                            if (commentID !== null){
                                included    = pure.nodes.select.all('*[data-engine-comment-included="Container"][data-engine-comment-commentID="' + commentID + '"] > *[data-engine-comment-postID]');
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
                            if (commentID !== null){
                                setFlag(commentID, (value === 0 ? false : true));
                            }
                        }
                    }
                }
            },
            mana        : function(comments){
                function collectIDs(comments){
                    for(var index = comments.length - 1; index >= 0; index -= 1){
                        IDs.push(
                            {
                                object_id   : (typeof comments[index].id !== 'undefined' ? parseInt(comments[index].id,  10) : parseInt(comments[index].comment.id,  10)),
                                user_id     : (typeof comments[index].user_id !== 'undefined' ? parseInt(comments[index].user_id,   10) : parseInt(comments[index].author.id,   10))
                            }
                        );
                        if (comments[index].children instanceof Array){
                            collectIDs(comments[index].children);
                        }
                    }
                };
                var IDs = [];
                collectIDs(comments);
                pure.appevents.Actions.call(
                    'pure.mana.icons',
                    'new',
                    {
                        object  :'comment',
                        IDs     :IDs
                    }
                );
            }
        },
        memes       : {
            data        : null,
            load        : {
                send        : function(){
                    var request = pure.comments.posts.configuration.requests.getMemes;
                    if (pure.comments.posts.configuration.allowMemes === 'on'){
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.comments.posts.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.comments.posts.A.memes.load.onRecieve(id_request, response);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.comments.posts.A.memes.load.onError(event, id_request);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.comments.posts.A.memes.load.onError(event, id_request);
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
                                        pure.comments.posts.A.memes.data = data;
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
                init : function(button, editorID, commentID){
                    var data = pure.comments.posts.A.memes.data;
                    if (data !== null){
                        pure.events.add(
                            button,
                            'click',
                            function(){
                                pure.comments.posts.A.memes.select.show(editorID, commentID);
                            }
                        );
                    }else{
                        button.parentNode.removeChild(button);
                    }
                }
            },
            select : {
                dialogID    : null,
                show        : function(editorID, commentID){
                    var memes   = '',
                        data    = pure.comments.posts.A.memes.data;
                    if (data instanceof Array){
                        for(var index = data.length - 1; index >= 0; index -= 1){
                            memes += '<a data-post-element-type="Pure.Posts.Comment.A.Meme.Dialog" data-meme-src="' + data[index] + '"><img alt="" data-post-element-type="Pure.Posts.Comment.A.Meme.Dialog" src="' + data[index] + '" /></a>';
                        }
                        pure.comments.posts.A.memes.select.dialogID = pure.components.dialogs.B.open({
                            title       : 'Memes',
                            innerHTML   : '<div data-post-element-type="Pure.Posts.Comment.A.Memes.Dialog">' + memes + '</div>',
                            width       : 70,
                            parent      : document.body,
                            fullHeight  : true,
                            afterInit   : function(){
                                pure.comments.posts.A.memes.select.afterInit(editorID, commentID);
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
                afterInit   : function(editorID, commentID){
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
                                            pure.comments.posts.A.message.sending.send(
                                                editorID,
                                                commentID,
                                                '[meme:begin]' + memeSrc + '[meme:end]'
                                            );
                                            if (pure.comments.posts.A.memes.select.dialogID !== null){
                                                pure.components.dialogs.B.close(
                                                    pure.comments.posts.A.memes.select.dialogID
                                                );
                                                pure.comments.posts.A.memes.select.dialogID = null;
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
                pure.comments.posts.A.memes.load.send();
            }
        },
        hotUpdate   : {
            inited      : false,
            init        : function(){
                if (pure.comments.posts.A.hotUpdate.inited === false){
                    if (pure.comments.posts.configuration.hotUpdate === 'on'){
                        pure.appevents.Actions.listen(
                            'webSocketServerEvents',
                            'post_comment',
                            pure.comments.posts.A.hotUpdate.processing,
                            'post_comment_update_handle'
                        );
                        pure.comments.posts.A.hotUpdate.inited = true;
                    }
                }
            },
            call        : function(){
                if (pure.comments.posts.configuration.hotUpdate === 'on'){
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
                        pure.comments.posts.A.hotUpdate.send.send(parameters.post_id, parameters.created);
                    }
                }
            },
            send : {
                send : function(postID, afterDate){
                    var testNode    = pure.nodes.select.first('*[data-engine-comment-more-mark="' + postID + '"]'),
                        request     = pure.comments.posts.configuration.requests.update;
                    if (testNode !== null){
                        if (pure.comments.posts.configuration.hotUpdate === 'on'){
                            request = request.replace(/\[post_id\]/gi,      postID      );
                            request = request.replace(/\[after_date\]/gi,   afterDate   );
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : pure.comments.posts.configuration.requestURL,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.comments.posts.A.hotUpdate.send.onRecieve(id_request, response, postID);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.comments.posts.A.hotUpdate.send.onError(event, id_request, postID);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.comments.posts.A.hotUpdate.send.onError(event, id_request, postID);
                                }
                            });
                        }
                    }
                },
                onRecieve   : function(id_request, response, postID){
                    var data    = null;
                    try{
                        data = JSON.parse(response);
                        if (parseInt(data.shown, 10) > 0){
                            //pure.comments.posts.A.more.counter.updateShown(postID, shown + parseInt(data.shown, 10));
                            pure.comments.posts.A.templates.comments.root.action.addFromHotUpdate(postID, data.comments);
                        }
                    }catch (e){
                    }
                },
                onError     : function(id_request, postID){
                    //Do nothing. It's background process
                }
            }
        },
        quotes      : {
            current     : null,
            onSelection : function(event, editorID, commentID, postID){
                function getAttributes(node){
                    var attributes = {
                        mark        : node.getAttribute('data-engine-comment-element'   ),
                        commentID   : node.getAttribute('data-engine-comment-commentID' )
                    };
                    if (pure.tools.objects.isValueIn(attributes, null   ) === false &&
                        pure.tools.objects.isValueIn(attributes, ''     ) === false){
                        return (attributes.mark === 'Comment.Value' ? attributes : null);
                    }
                    return null;
                }
                function getPost(node){
                    var container   = pure.nodes.find.parentByAttr(node, { name : 'data-engine-comment-postID', value: null}),
                        postID      = null;
                    if (container !== null){
                        postID = container.getAttribute('data-engine-comment-postID');
                        postID = (postID !== '' ? postID : null);
                        if (postID !== null){
                            return {
                                postID      : postID,
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
                function getAuthorAndTime(postID, commentID){
                    var author  = pure.nodes.select.first('*[data-engine-comment-postID="' + postID + '"][data-engine-comment-commentID="' + commentID + '"] *[data-engine-comment-element="Comment.Author.Name"]'),
                        time    = pure.nodes.select.first('*[data-engine-comment-postID="' + postID + '"][data-engine-comment-commentID="' + commentID + '"] *[data-engine-comment-element="Comment.DateTime"]');
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
                                    info = getAuthorAndTime(post.postID, attributes.commentID);
                                    if (info !== null){
                                        pure.comments.posts.A.quotes.current = {
                                            text        : getSelected(),
                                            commentID   : attributes.commentID,
                                            postID      : post.postID,
                                            author      : info.author,
                                            time        : info.time
                                        };
                                        pure.comments.posts.A.templates.notification.show(parent.parentNode);
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
                pure.comments.posts.A.quotes.current = null;
                return false;
            },
            onClick     : function(editorID, postID){
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
                var current     = pure.comments.posts.A.quotes.current,
                    editor      = pure.nodes.select.first('*[data-engine-comment-editor="' + editorID + '"] textarea'),
                    quote       = '',
                    position    = null,
                    value       = '';
                if (current !== null && editor !== null){
                    if (current.postID === postID && current.text !== ''){
                        quote       =   '[quote:begin]' +
                                            '[author:begin]' + current.author + '[author:end]' +
                                            '[date:begin]' + current.time + '[date:end]' +
                                            current.text +
                                        '[quote:end]';
                        position        = getPosition(editor);
                        value           = editor.value;
                        editor.value    = value.substr(0, position) + '\x0A' + quote + '\x0A' + value.substr(position, value.length);
                        pure.comments.posts.A.quotes.current = null;
                    }
                }
            }
        },
        dialogs     : {
            info: function (title, message) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-post-element-type="Pure.Posts.Comment.A.Dialog">' + message + '</p>',
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
        isPossible  : function(){
            var result = true;
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.comments.posts.configuration.user_id'             ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.comments.posts.configuration.allowMemes'          ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.comments.posts.configuration.allowAttachment'     ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.comments.posts.configuration.maxLength'           ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.comments.posts.configuration.hotUpdate'           ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.comments.posts.configuration.requestURL'          ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.comments.posts.configuration.requests.getMemes'   ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.comments.posts.configuration.requests.send'       ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.comments.posts.configuration.requests.more'       ) === null ? false : true));
            result = (result === false ? false : (pure.system.getInstanceByPath('pure.comments.posts.configuration.requests.update'     ) === null ? false : true));
            if (result !== false){
                pure.comments.posts.configuration.user_id    = parseInt(pure.comments.posts.configuration.user_id,    10);
                pure.comments.posts.configuration.maxLength  = parseInt(pure.comments.posts.configuration.maxLength,  10);
            }
            return result;
        },
        init        : function(){
            if (pure.comments.posts.A.isPossible() !== false){
                pure.comments.posts.A.templates.init();
                pure.comments.posts.A.memes.    init();
                pure.comments.posts.A.more.     init();
                pure.comments.posts.A.hotUpdate.init();
            }
        }
    };
    pure.system.start.add(pure.comments.posts.A.init);
}());