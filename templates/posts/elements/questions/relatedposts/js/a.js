(function () {
    if (typeof window.pure                                          !== "object") { window.pure                                         = {}; }
    if (typeof window.pure.posts                                    !== "object") { window.pure.posts                                   = {}; }
    if (typeof window.pure.posts.elements                           !== "object") { window.pure.posts.elements                          = {}; }
    if (typeof window.pure.posts.elements.questions                 !== "object") { window.pure.posts.elements.questions                = {}; }
    if (typeof window.pure.posts.elements.questions.relatedPosts    !== "object") { window.pure.posts.elements.questions.relatedPosts   = {}; }
    "use strict";
    window.pure.posts.elements.questions.relatedPosts.A = {
        init        : {
            attach  : function(){
                var instances = pure.nodes.select.all('*[data-relatedposts-engine-attach]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(button, _index, source){
                            var question_id = button.getAttribute('data-relatedposts-engine-attach'),
                                place       = pure.nodes.select.first('*[data-relatedposts-engine-label="' + question_id + '"]'),
                                url         = pure.nodes.select.first('*[data-relatedposts-engine-url="' + question_id + '"]');
                            if (place !== null && url !== null){
                                pure.events.add(
                                    button,
                                    'click',
                                    function(){
                                        pure.posts.elements.questions.relatedPosts.A.attach.send(question_id, place, url);
                                    }
                                );
                            }
                            button.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            remove  : function(){
                var instances = pure.nodes.select.all('*[data-relatedposts-engine-remove]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(button, _index, source){
                            var question_id = button.getAttribute('data-relatedposts-engine-question_id'),
                                post_id     = button.getAttribute('data-relatedposts-engine-remove');
                            if (post_id !== null && question_id !== null){
                                pure.events.add(
                                    button,
                                    'click',
                                    function(){
                                        pure.posts.elements.questions.relatedPosts.A.remove.send(question_id, post_id);
                                    }
                                );
                            }
                            button.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            templates   : function(){
                var templates = {
                    post    : pure.system.getInstanceByPath('pure.posts.elements.questions.relatedPosts.templates.post')
                };
                if (pure.tools.objects.isValueIn(templates, null) === false){
                    pure.posts.elements.questions.relatedPosts.templates.post = pure.convertor.UTF8.decode(
                        pure.convertor.BASE64.decode(pure.posts.elements.questions.relatedPosts.templates.post)
                    );
                }
            },
            all         : function(){
                pure.posts.elements.questions.relatedPosts.A.init.attach        ();
                pure.posts.elements.questions.relatedPosts.A.init.remove        ();
                pure.posts.elements.questions.relatedPosts.A.init.templates     ();
                pure.posts.elements.questions.relatedPosts.A.titles.globalUpdate();
            }
        },
        titles      : {
            globalUpdate : function(){
                var instances = pure.nodes.select.all('*[data-relatedposts-engine-label]');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(title, _index, source){
                            var question_id = title.getAttribute('data-relatedposts-engine-label'),
                                additions   = pure.nodes.select.all('*[data-relatedposts-engine-item="' + question_id + '"]');
                            if (question_id !== null && additions !== null){
                                if (additions.length > 0){
                                    title.style.display = 'none';
                                }else{
                                    title.style.display = '';
                                }
                            }
                        }
                    );
                }
            },
            update      : function(question_id){
                var title       = pure.nodes.select.first('*[data-relatedposts-engine-label="' + question_id + '"]'),
                    additions   = pure.nodes.select.all('*[data-relatedposts-engine-item="' + question_id + '"]');
                if (title !== null && additions !== null){
                    if (additions.length > 0){
                        title.style.display = 'none';
                    }else{
                        title.style.display = '';
                    }
                }
            }
        },
        remove      : {
            send : function(question_id, post_id){
                var request         = pure.system.getInstanceByPath('pure.posts.elements.questions.relatedPosts.requests.remove'    ),
                    destination     = pure.system.getInstanceByPath('pure.posts.elements.questions.relatedPosts.requests.direction' );
                if (request !== null && destination !== null) {
                    if (pure.posts.elements.questions.relatedPosts.A.progress.remove.isBusy(question_id, post_id) === false) {
                        pure.posts.elements.questions.relatedPosts.A.progress.remove.busy(question_id, post_id);
                        pure.tools.request.sendWithFields({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.posts.elements.questions.relatedPosts.A.remove.receive(id_request, response, question_id, post_id);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.posts.elements.questions.relatedPosts.A.remove.error(event, id_request, question_id, post_id);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.posts.elements.questions.relatedPosts.A.remove.error(event, id_request, question_id, post_id);
                                }
                            },
                            {
                                question_id : question_id,
                                post_id     : post_id
                            }
                        );
                    }
                }
            },
            receive : function(id_request, response, question_id, post_id){
                var node = {
                    button  : pure.nodes.select.first('*[data-relatedposts-engine-question_id="' + question_id + '"][data-relatedposts-engine-remove="' + post_id + '"]'),
                    info    : pure.nodes.select.first('*[data-relatedposts-engine-question_id="' + question_id + '"][data-relatedposts-engine-wait="' + post_id + '"]'),
                    item    : pure.nodes.select.first('*[data-relatedposts-engine-item="' + question_id + '"][data-relatedposts-engine-post_id="' + post_id + '"]')
                };
                pure.posts.elements.questions.relatedPosts.A.progress.remove.clear(question_id, post_id);
                switch (response){
                    case 'error':
                        break;
                    case 'removed':
                        if (pure.tools.objects.isValueIn(node, null) === false){
                            node.item.parentNode.removeChild(node.item);
                            pure.posts.elements.questions.relatedPosts.A.titles.update(question_id);
                        }
                        break;
                    case 'wait':
                        if (pure.tools.objects.isValueIn(node, null) === false){
                            node.button.parentNode.removeChild(node.button);
                            node.info.style.display = 'inline-block';
                        }
                        break;
                }
            },
            error   : function(event, id_request, question_id, post_id){
                pure.posts.elements.questions.relatedPosts.A.progress.remove.clear(question_id, post_id);
            }
        },
        attach      : {
            send : function(question_id, place, url){
                var request         = pure.system.getInstanceByPath('pure.posts.elements.questions.relatedPosts.requests.add'       ),
                    destination     = pure.system.getInstanceByPath('pure.posts.elements.questions.relatedPosts.requests.direction' );
                if (request !== null && destination !== null) {
                    if (pure.posts.elements.questions.relatedPosts.A.progress.attach.isBusy(question_id) === false) {
                        if (url.value.length > 0){
                            pure.posts.elements.questions.relatedPosts.A.progress.attach.busy(question_id);
                            pure.tools.request.sendWithFields({
                                    type        : 'POST',
                                    url         : destination,
                                    request     : request,
                                    onrecieve   : function (id_request, response) {
                                        pure.posts.elements.questions.relatedPosts.A.attach.receive(id_request, response, question_id, place, url);
                                    },
                                    onreaction  : null,
                                    onerror     : function (event, id_request) {
                                        pure.posts.elements.questions.relatedPosts.A.attach.error(event, id_request, question_id, place, url);
                                    },
                                    ontimeout   : function (event, id_request) {
                                        pure.posts.elements.questions.relatedPosts.A.attach.error(event, id_request, question_id, place, url);
                                    }
                                },
                                {
                                    question_id : question_id,
                                    post_url    : url.value
                                }
                            );
                        }
                    }
                }
            },
            receive : function(id_request, response, question_id, place, url){
                var data = null;
                pure.posts.elements.questions.relatedPosts.A.progress.attach.clear(question_id);
                url.value = '';
                switch (response){
                    case 'error':
                        break;
                    case 'fail':
                        break;
                    default :
                        try{
                            data = JSON.parse(response);
                            pure.posts.elements.questions.relatedPosts.A.attach.addNew(data);
                            pure.posts.elements.questions.relatedPosts.A.titles.update(data.question_id);
                        }catch (e){

                        }
                        break;
                }
            },
            error   : function(event, id_request, question_id, place, url){
                pure.posts.elements.questions.relatedPosts.A.progress.attach.clear(question_id);
                url.value = '';
            },
            addNew  : function(params){
                var template    = pure.system.getInstanceByPath('pure.posts.elements.questions.relatedPosts.templates.post'),
                    place       = pure.nodes.select.first('*[data-relatedposts-engine-place="' + params.question_id + '"]'),
                    wrapper     = document.createElement('DIV');
                if (template !== null){
                    params.post_title   = pure.convertor.BASE64.decode(params.post_title);
                    params.post_title   = pure.convertor.UTF8.  decode(params.post_title);
                    params.post_excerpt = pure.convertor.BASE64.decode(params.post_excerpt);
                    params.post_excerpt = pure.convertor.UTF8.  decode(params.post_excerpt);
                    template    = template.replace(/\[question_id\]/gi,         params.question_id          );
                    template    = template.replace(/\[object_id\]/gi,           params.post_id              );
                    template    = template.replace(/\[post_id\]/gi,             params.post_id              );
                    template    = template.replace(/\[post_title\]/gi,          params.post_title           );
                    template    = template.replace(/\[post_created\]/gi,        params.post_created         );
                    template    = template.replace(/\[post_attached_by\]/gi,    params.post_attached_by     );
                    template    = template.replace(/\[post_author\]/gi,         params.post_author          );
                    template    = template.replace(/\[post_excerpt\]/gi,        params.post_excerpt         );
                    template    = template.replace(/\[post_url\]/gi,            params.post_url             );
                    wrapper.innerHTML = template;
                    pure.nodes.move.insertChildsBefore(place, wrapper.childNodes);
                    pure.appevents.Actions.call(
                        'pure.mana.icons',
                        'new',
                        {
                            object  : 'question_related_post',
                            IDs     : [{
                                object_id   : params.post_id ,
                                user_id     : params.post_attached_by_id
                            }],
                            field   : params.question_id
                        },
                        null
                    );
                    pure.appevents.Actions.call(
                        'pure.questions.solution',
                        'new',
                        {
                            question_id     : params.question_id,
                            object_id       : params.post_id,
                            object_type     : 'related_post',
                            is_active       : false
                        },
                        null
                    );
                }
            }
        },
        progress    : {
            attach : {
                data    : {},
                isBusy  : function(id){
                    var data = pure.posts.elements.questions.relatedPosts.A.progress.attach.data;
                    return (typeof data[id] !== 'undefined' ? true : false);
                },
                busy    : function(id){
                    var data = pure.posts.elements.questions.relatedPosts.A.progress.attach.data,
                        node = pure.nodes.select.first('*[data-relatedposts-engine-editor="' + id + '"]');
                    if (typeof data[id] === 'undefined' && node !== null){
                        data[id] = pure.templates.progressbar.A.show(node, 'background:rgba(255,255,255,0.8);');
                    }
                },
                clear   : function(id){
                    var data = pure.posts.elements.questions.relatedPosts.A.progress.attach.data;
                    if (typeof data[id] !== 'undefined'){
                        pure.templates.progressbar.A.hide(data[id]);
                        data[id] = null;
                        delete data[id];
                    }
                }
            },
            remove : {
                data    : {},
                isBusy  : function(question_id, post_id){
                    var data = pure.posts.elements.questions.relatedPosts.A.progress.remove.data;
                    return (typeof data[question_id + post_id] !== 'undefined' ? true : false);
                },
                busy    : function(question_id, post_id){
                    var data = pure.posts.elements.questions.relatedPosts.A.progress.remove.data,
                        node = pure.nodes.select.first('*[data-relatedposts-engine-question_id="' + question_id + '"][data-relatedposts-engine-remove="' + post_id + '"]');
                    if (typeof data[question_id + post_id] === 'undefined' && node !== null){
                        data[question_id + post_id] = pure.templates.progressbar.A.show(node, 'background:rgba(255,255,255,0.8);');
                    }
                },
                clear   : function(question_id, post_id){
                    var data = pure.posts.elements.questions.relatedPosts.A.progress.remove.data;
                    if (typeof data[question_id + post_id] !== 'undefined'){
                        pure.templates.progressbar.A.hide(data[question_id + post_id]);
                        data[question_id + post_id] = null;
                        delete data[question_id + post_id];
                    }
                }
            }
        },
        dialogs     : {
            info    : function (title, message, handle) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-post-element-type="Pure.Posts.Members.A.Dialog">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : [
                        {
                            title       : 'OK',
                            handle      : handle,
                            closeAfter  : true
                        }
                    ]
                });
            }
        }
    };
    pure.system.start.add(pure.posts.elements.questions.relatedPosts.A.init.all);
}());