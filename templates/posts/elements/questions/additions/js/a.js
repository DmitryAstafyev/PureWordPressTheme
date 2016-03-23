(function () {
    if (typeof window.pure                                      !== "object") { window.pure                                     = {}; }
    if (typeof window.pure.posts                                !== "object") { window.pure.posts                               = {}; }
    if (typeof window.pure.posts.elements                       !== "object") { window.pure.posts.elements                      = {}; }
    if (typeof window.pure.posts.elements.questions             !== "object") { window.pure.posts.elements.questions            = {}; }
    if (typeof window.pure.posts.elements.questions.additions   !== "object") { window.pure.posts.elements.questions.additions  = {}; }
    "use strict";
    window.pure.posts.elements.questions.additions.A = {
        init        : {
            open : function(){
                var instances = pure.nodes.select.all('*[data-addition-engine-switcher-editor]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(switcher, _index, source){
                            var id          = switcher.getAttribute('data-addition-engine-switcher-editor'),
                                place       = pure.nodes.select.first('*[data-addition-engine-editor-container="' + id + '"]');
                            if (place !== null && typeof tinyMCE !== 'undefined'){
                                switcher.disabled = false;
                                pure.events.add(
                                    switcher,
                                    'change',
                                    function(){
                                        pure.posts.elements.questions.additions.A.editors.toggle(
                                            id,
                                            '*[data-addition-engine-editor-container="' + id + '"]'
                                        );
                                    }
                                );
                            }
                            switcher.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            send: function(){
                var instances = pure.nodes.select.all('*[data-addition-engine-editor-send]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(button, _index, source){
                            var id = button.getAttribute('data-addition-engine-editor-send');
                            if (id !== null){
                                pure.events.add(
                                    button,
                                    'click',
                                    function(){
                                        pure.posts.elements.questions.additions.A.update.send(id);
                                    }
                                );
                            }
                            button.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            remove: function(){
                var instances = pure.nodes.select.all('*[data-addition-engine-remove]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(button, _index, source){
                            var id = button.getAttribute('data-addition-engine-remove');
                            if (id !== null){
                                pure.events.add(
                                    button,
                                    'click',
                                    function(){
                                        pure.posts.elements.questions.additions.A.remove.onClick(id);
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
                    addition    : pure.system.getInstanceByPath('pure.posts.elements.questions.additions.templates.addition')
                };
                if (pure.tools.objects.isValueIn(templates, null) === false){
                    pure.posts.elements.questions.additions.templates.addition      = pure.convertor.UTF8.decode(
                        pure.convertor.BASE64.decode(pure.posts.elements.questions.additions.templates.addition)
                    );
                }
            },
            all         : function(){
                pure.posts.elements.questions.additions.A.init.open             ();
                pure.posts.elements.questions.additions.A.init.send             ();
                pure.posts.elements.questions.additions.A.init.remove           ();
                pure.posts.elements.questions.additions.A.init.templates        ();
                pure.posts.elements.questions.additions.A.titles.globalUpdate   ();
            }
        },
        titles  : {
            globalUpdate : function(){
                var instances = pure.nodes.select.all('*[data-addition-engine-title]');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(title, _index, source){
                            var question_id = title.getAttribute('data-addition-engine-title'),
                                additions   = pure.nodes.select.all('*[data-addition-engine-item="' + question_id + '"]');
                            if (question_id !== null && additions !== null){
                                if (additions.length > 0){
                                    title.style.display = '';
                                }else{
                                    title.style.display = 'none';
                                }
                            }
                        }
                    );
                }
            },
            update      : function(question_id){
                var title       = pure.nodes.select.first('*[data-addition-engine-title="' + question_id + '"]'),
                    additions   = pure.nodes.select.all('*[data-addition-engine-item="' + question_id + '"]');
                if (title !== null && additions !== null){
                    if (additions.length > 0){
                        title.style.display = '';
                    }else{
                        title.style.display = 'none';
                    }
                }
            }
        },
        editors     : {
            data : {},
            open : function(id, selector){
                var data        = pure.posts.elements.questions.additions.A.editors.data,
                    content     = pure.nodes.select.first('*[data-addition-engine-editor-content="' + id + '"]');
                if (typeof data[id] === 'undefined'){
                    tinyMCE.init({
                        selector                : selector,
                        menubar                 : false,
                        skin                    : 'lightgray',
                        theme                   : 'modern',
                        plugins                 : 'wplink fullscreen',
                        toolbar                 : 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | wplink | sh4tinymce | fullscreen',
                        init_instance_callback  : function(editor) {
                            data[id] = editor.id;
                            if (content !== null){
                                editor.setContent(content.innerHTML);
                                content.style.display = 'none';
                            }
                        }
                    });
                }
            },
            close : function(id){
                var data        = pure.posts.elements.questions.additions.A.editors.data,
                    content     = pure.nodes.select.first('*[data-addition-engine-editor-content="' + id + '"]'),
                    instance    = null;
                if (typeof data[id] !== 'undefined'){
                    instance = tinyMCE.get(data[id]);
                    if (instance !== null){
                        instance.setContent('');
                        instance.destroy();
                        data[id] = null;
                        delete data[id];
                        if (content !== null){
                            content.style.display = '';
                        }
                    }
                }
            },
            toggle  : function(id, selector){
                var data = pure.posts.elements.questions.additions.A.editors.data;
                if (typeof data[id] === 'undefined') {
                    pure.posts.elements.questions.additions.A.editors.open(id, selector);
                }else{
                    pure.posts.elements.questions.additions.A.editors.close(id);
                }
            },
            content : function(id){
                var data        = pure.posts.elements.questions.additions.A.editors.data,
                    instance    = null;
                if (typeof data[id] !== 'undefined'){
                    instance = tinyMCE.get(data[id]);
                    if (instance !== null){
                        return instance.getContent();
                    }
                }
                return null;
            }
        },
        remove      : {
            onClick : function(id){
                pure.posts.elements.questions.additions.A.dialogs.question(
                    'Confirm operation',
                    'Are you really want to remove this additions with all attachments (if it exists)?',
                    [
                        {
                            title       : 'NO',
                            handle      : null,
                            closeAfter  : true
                        },
                        {
                            title       : 'YES, REMOVE',
                            handle      : function(){
                                pure.posts.elements.questions.additions.A.remove.send(id);
                            },
                            closeAfter  : true
                        }
                    ]
                );
            },
            send : function(id){
                var request         = pure.system.getInstanceByPath('pure.posts.elements.questions.additions.requests.remove'       ),
                    destination     = pure.system.getInstanceByPath('pure.posts.elements.questions.additions.requests.direction'    );
                if (request !== null && destination !== null) {
                    if (pure.posts.elements.questions.additions.A.progress.isBusy(id) === false) {
                        pure.posts.elements.questions.additions.A.progress.busy(id);
                        pure.tools.request.sendWithFields({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.posts.elements.questions.additions.A.remove.receive(id_request, response, id);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.posts.elements.questions.additions.A.remove.error(event, id_request, id);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.posts.elements.questions.additions.A.remove.error(event, id_request, id);
                                }
                            },
                            {
                                addition_id : id
                            }
                        );
                    }
                }
            },
            receive : function(id_request, response, id){
                pure.posts.elements.questions.additions.A.progress.clear(id);
                switch (response){
                    case 'success':
                        pure.posts.elements.questions.additions.A.remove.remove(id);
                        break;
                    case 'fail':
                        break;
                    case 'error':
                        break;
                }
            },
            error   : function(event, id_request, id){
                pure.posts.elements.questions.additions.A.progress.clear(id);
            },
            remove : function(id){
                var item        = pure.nodes.select.first('*[data-addition-engine-id="' + id + '"]'),
                    question_id = null;
                if (item !== null){
                    question_id = item.getAttribute('data-addition-engine-item');
                    item.parentNode.removeChild(item);
                    if (question_id !== null){
                        pure.posts.elements.questions.additions.A.titles.update(question_id);
                    }
                }
            }
        },
        progress    : {
            data    : {},
            isBusy  : function(id){
                var data = pure.posts.elements.questions.additions.A.progress.data;
                return (typeof data[id] !== 'undefined' ? true : false);
            },
            busy    : function(id){
                var data = pure.posts.elements.questions.additions.A.progress.data,
                    node = pure.nodes.select.first('*[data-addition-engine-editor-progress="' + id + '"]');
                if (typeof data[id] === 'undefined' && node !== null){
                    data[id] = pure.templates.progressbar.A.show(node, 'background:rgba(255,255,255,0.8);');
                }
            },
            clear   : function(id){
                var data = pure.posts.elements.questions.additions.A.progress.data;
                if (typeof data[id] !== 'undefined'){
                    pure.templates.progressbar.A.hide(data[id]);
                    data[id] = null;
                    delete data[id];
                }
            }
        },
        update      : {
            nodes   : {
                updateExisting : function(id, content){
                    var node        = pure.nodes.select.first('*[data-addition-engine-editor-content="' + id + '"]'),
                        switcher    = pure.nodes.select.first('*[data-addition-engine-switcher-editor="' + id + '"]');
                    if (node !== null){
                        node.innerHTML = content;
                        if (switcher !== null){
                            if (switcher.checked !== false){
                                switcher.checked = false;
                                pure.posts.elements.questions.additions.A.editors.close(id);
                            }
                        }
                    }
                },
                addNew : function(post_id, addition_id, content, date){
                    var node        = pure.nodes.select.first('*[data-addition-engine-editor-content="' + addition_id + '"]'),
                        mark        = pure.nodes.select.first('*[data-addition-engine-mark="' + post_id + '"]'),
                        switcher    = pure.nodes.select.first('*[data-addition-engine-switcher-editor="' + post_id + '"]'),
                        template    = pure.system.getInstanceByPath('pure.posts.elements.questions.additions.templates.addition'),
                        addition    = document.createElement('DIV');
                    if (node === null && switcher !== null && mark !== null && template !== null){
                        template            = template.replace(/\[question_id\]/gi,    post_id      );
                        template            = template.replace(/\[addition_id\]/gi,    addition_id  );
                        template            = template.replace(/\[content\]/gi,        content      );
                        template            = template.replace(/\[date\]/gi,           date         );
                        //template            = template.replace(/\[attachments\]/gi,    ''           );
                        addition.innerHTML  = template;
                        pure.nodes.move.insertChildsBefore(mark, addition.childNodes);
                        pure.posts.elements.questions.additions.A.init.open     ();
                        pure.posts.elements.questions.additions.A.init.send     ();
                        pure.posts.elements.questions.additions.A.init.remove   ();
                        if (switcher !== null){
                            if (switcher.checked !== false){
                                switcher.checked = false;
                                pure.posts.elements.questions.additions.A.editors.close(post_id);
                            }
                        }
                        pure.posts.elements.questions.additions.A.titles.update(post_id);
                        pure.appevents.Actions.call(
                            'pure.fileloader',
                            'update.upload.button',
                            null,
                            null
                        );
                        pure.appevents.Actions.call(
                            'pure.fileloader',
                            'update.titles',
                            null,
                            null
                        );
                    }
                }
            },
            send    : function(id){
                var request         = pure.system.getInstanceByPath('pure.posts.elements.questions.additions.requests.update'       ),
                    destination     = pure.system.getInstanceByPath('pure.posts.elements.questions.additions.requests.direction'    ),
                    content         = pure.posts.elements.questions.additions.A.editors.content(id),
                    addition_node   = pure.nodes.select.first('*[data-addition-engine-editor-content="' + id + '"]');
                if (request !== null && destination !== null && content !== null){
                    if (pure.posts.elements.questions.additions.A.progress.isBusy(id) === false){
                        if (content.length > 3 && content.length < 10000){
                            content = pure.convertor.UTF8.  encode(content);
                            content = pure.convertor.BASE64.encode(content);
                            pure.posts.elements.questions.additions.A.progress.busy(id);
                            pure.tools.request.sendWithFields({
                                    type        : 'POST',
                                    url         : destination,
                                    request     : request,
                                    onrecieve   : function (id_request, response) {
                                        pure.posts.elements.questions.additions.A.update.receive(id_request, response, id);
                                    },
                                    onreaction  : null,
                                    onerror     : function (event, id_request) {
                                        pure.posts.elements.questions.additions.A.update.error(event, id_request, id);
                                    },
                                    ontimeout   : function (event, id_request) {
                                        pure.posts.elements.questions.additions.A.update.error(event, id_request, id);
                                    }
                                },
                                {
                                    addition_id : (addition_node !== null ? id : -1),
                                    content     : content
                                }
                            );
                        }
                    }
                }
            },
            receive : function(id_request, response, id){
                var data = null;
                if (response !== 'error'){
                    try{
                        data = JSON.parse(response);
                        if (typeof data.content !== 'undefined' && typeof data.addition_id  !== 'undefined' &&
                            typeof data.date    !== 'undefined' && typeof data.post_id      !== 'undefined'){
                            //If addition exists
                            pure.posts.elements.questions.additions.A.update.nodes.updateExisting(
                                data.addition_id,
                                data.content
                            );
                            //If addition new
                            pure.posts.elements.questions.additions.A.update.nodes.addNew(
                                data.post_id,
                                data.addition_id,
                                data.content,
                                data.date
                            );
                        }
                    }catch (e){

                    }
                }else{

                }
                pure.posts.elements.questions.additions.A.progress.clear(id);
            },
            error   : function(event, id_request, id){
                pure.posts.elements.questions.additions.A.progress.clear(id);
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
            },
            question    : function (title, message, buttons) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-post-element-type="Pure.Posts.Members.A.Dialog">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : buttons
                });
            }
        }
    };
    pure.system.start.add(pure.posts.elements.questions.additions.A.init.all);
}());