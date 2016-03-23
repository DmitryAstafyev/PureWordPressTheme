(function () {
    if (typeof window.pure                                  !== "object") { window.pure                                     = {}; }
    if (typeof window.pure.posts                            !== "object") { window.pure.posts                               = {}; }
    if (typeof window.pure.posts.elements                   !== "object") { window.pure.posts.elements                      = {}; }
    if (typeof window.pure.posts.elements.questions         !== "object") { window.pure.posts.elements.questions            = {}; }
    if (typeof window.pure.posts.elements.questions.answers !== "object") { window.pure.posts.elements.questions.answers    = {}; }
    "use strict";
    window.pure.posts.elements.questions.answers.A = {
        init        : {
            open : function(){
                var instances = pure.nodes.select.all('*[data-answer-engine-switcher-editor]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(switcher, _index, source){
                            var question_id = switcher.getAttribute('data-answer-engine-switcher-editor'),
                                answer_id   = switcher.getAttribute('data-answer-engine-answer_id'),
                                place       = pure.nodes.select.first('*[data-answer-engine-editor-container="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]');
                            if (place !== null && typeof tinyMCE !== 'undefined'){
                                switcher.disabled = false;
                                pure.events.add(
                                    switcher,
                                    'change',
                                    function(){
                                        pure.posts.elements.questions.answers.A.editors.toggle(
                                            question_id,
                                            answer_id,
                                            '*[data-answer-engine-editor-container="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]',
                                            false
                                        );
                                    }
                                );
                            }
                            switcher.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            modify : function(){
                var instances = pure.nodes.select.all('*[data-answer-engine-modify-answer]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(button, _index, source){
                            var question_id = button.getAttribute('data-answer-engine-modify-answer'),
                                answer_id   = button.getAttribute('data-answer-engine-answer_id'),
                                place       = pure.nodes.select.first('*[data-answer-engine-editor-container="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]'),
                                switcher    = pure.nodes.select.first('*[data-answer-engine-switcher-editor="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]');
                            if (place !== null && switcher !== null && typeof tinyMCE !== 'undefined'){
                                pure.events.add(
                                    button,
                                    'click',
                                    function(){
                                        pure.posts.elements.questions.answers.A.editors.modify(
                                            question_id,
                                            answer_id,
                                            '*[data-answer-engine-editor-container="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]',
                                            switcher
                                        );
                                    }
                                );
                            }
                            button.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            send : function(){
                var instances = pure.nodes.select.all('*[data-answer-engine-send]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(button, _index, source){
                            var question_id = button.getAttribute('data-answer-engine-send'),
                                answer_id   = button.getAttribute('data-answer-engine-answer_id');
                            if (answer_id !== null){
                                pure.events.add(
                                    button,
                                    'click',
                                    function(){
                                        pure.posts.elements.questions.answers.A.update.send(question_id, answer_id);
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
                    root        : pure.system.getInstanceByPath('pure.posts.elements.questions.answers.templates.root'),
                    included    : pure.system.getInstanceByPath('pure.posts.elements.questions.answers.templates.included')
                };
                if (pure.tools.objects.isValueIn(templates, null) === false){
                    pure.posts.elements.questions.answers.templates.root      = pure.convertor.UTF8.decode(
                        pure.convertor.BASE64.decode(pure.posts.elements.questions.answers.templates.root)
                    );
                    pure.posts.elements.questions.answers.templates.included      = pure.convertor.UTF8.decode(
                        pure.convertor.BASE64.decode(pure.posts.elements.questions.answers.templates.included)
                    );
                }
            },
            all         : function(){
                pure.posts.elements.questions.answers.A.init.open           ();
                pure.posts.elements.questions.answers.A.init.modify         ();
                pure.posts.elements.questions.answers.A.init.send           ();
                pure.posts.elements.questions.answers.A.init.templates      ();
                pure.posts.elements.questions.answers.A.titles.globalUpdate ();
                pure.posts.elements.questions.answers.A.more.init           ();
                pure.posts.elements.questions.answers.A.hotUpdate.init      ();
            }
        },
        titles      : {
            globalUpdate : function(){
                var titles = pure.nodes.select.all('*[data-answer-engine-included-title]');
                if (titles !== null){
                    Array.prototype.forEach.call(
                        titles,
                        function(title, index, source){
                            var question_id = title.getAttribute('data-answer-engine-included-title'),
                                answer_id   = title.getAttribute('data-answer-engine-answer_id'),
                                included    = pure.nodes.select.all('*[data-answer-engine-included-container="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"] *[data-answer-engine-included-item]'),
                                count       = pure.nodes.select.first('*[data-answer-engine-included-title="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"] span');
                            if (answer_id !== null && included !== null && count !== null){
                                count.innerHTML = included.length;
                                if (included.length === 0){
                                    title.style.display = 'none';
                                }else{
                                    title.style.display = '';
                                }
                                pure.posts.elements.questions.answers.A.titles.checkSwitcher(question_id, answer_id);
                            }
                        }
                    );
                }
            },
            update      : function(question_id, answer_id){
                var title       = pure.nodes.select.first('*[data-answer-engine-included-title="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]'),
                    included    = pure.nodes.select.all('*[data-answer-engine-included-container="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"] *[data-answer-engine-included-item]'),
                    count       = pure.nodes.select.first('*[data-answer-engine-included-title="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"] span');
                if (title !== null && included !== null && count !== null){
                    count.innerHTML = included.length;
                    if (included.length === 0){
                        title.style.display = 'none';
                    }else{
                        title.style.display = '';
                    }
                    pure.posts.elements.questions.answers.A.titles.checkSwitcher(question_id, answer_id);
                }
            },
            checkSwitcher : function(question_id, answer_id){
                var user_id     = pure.system.getInstanceByPath('pure.posts.elements.questions.answers.configuration.user_id'),
                    included    = pure.nodes.select.all('*[data-answer-engine-included-container="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"] *[data-answer-engine-included-item]'),
                    switcher    = null;
                if (user_id === null && included !== null){
                    switcher    = pure.nodes.select.first('*[data-answer-engine-switcher-container="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]');
                    if (switcher !== null){
                        if (included.length === 0){
                            switcher.style.display = 'none';
                        }else{
                            switcher.style.display = '';
                        }
                    }
                }
            }
        },
        editors     : {
            data : {},
            open : function(question_id, answer_id, selector, add_content){
                var data                = pure.posts.elements.questions.answers.A.editors.data,
                    content             = null;
                if (typeof data[question_id + answer_id] === 'undefined'){
                    if (add_content !== false){
                        content = pure.nodes.select.first('*' + '[data-answer-engine-editor-content="' + question_id + '"]' +
                                                                '[data-answer-engine-answer_id="' + answer_id + '"]');
                    }
                    tinyMCE.init({
                        selector                : selector,
                        menubar                 : false,
                        skin                    : 'lightgray',
                        theme                   : 'modern',
                        plugins                 : 'wplink fullscreen',
                        toolbar                 : 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | wplink | code | fullscreen',
                        init_instance_callback  : function(editor) {
                            data[question_id + answer_id] = editor.id;
                            if (content !== null){
                                editor.setContent(content.innerHTML);
                                content.style.display = 'none';
                            }
                        }
                    });
                }
            },
            close : function(question_id, answer_id){
                var data        = pure.posts.elements.questions.answers.A.editors.data,
                    content     = pure.nodes.select.first('*' + '[data-answer-engine-editor-content="' + question_id + '"]' +
                                                                '[data-answer-engine-answer_id="' + answer_id + '"]'),
                    instance    = null;
                if (typeof data[question_id + answer_id] !== 'undefined'){
                    instance = tinyMCE.get(data[question_id + answer_id]);
                    if (instance !== null){
                        instance.setContent('');
                        instance.destroy();
                        data[question_id + answer_id] = null;
                        delete data[question_id + answer_id];
                        if (content !== null){
                            content.style.display = '';
                        }
                    }
                }
            },
            toggle  : function(question_id, answer_id, selector, add_content){
                var data = pure.posts.elements.questions.answers.A.editors.data;
                if (typeof data[question_id + answer_id] === 'undefined') {
                    pure.posts.elements.questions.answers.A.editors.open(question_id, answer_id, selector, add_content);
                }else{
                    pure.posts.elements.questions.answers.A.editors.close(question_id, answer_id);
                }
            },
            modify  : function(question_id, answer_id, selector, switcher){
                if (switcher.checked === false){
                    switcher.checked = true;
                }else{
                    switcher.checked = false;
                }
                pure.posts.elements.questions.answers.A.editors.toggle(
                    question_id,
                    answer_id,
                    selector,
                    true
                );
            },
            content : function(question_id, answer_id){
                var data        = pure.posts.elements.questions.answers.A.editors.data,
                    instance    = null;
                if (typeof data[question_id + answer_id] !== 'undefined'){
                    instance = tinyMCE.get(data[question_id + answer_id]);
                    if (instance !== null){
                        return instance.getContent();
                    }
                }
                return null;
            },
            isModify : function(question_id, answer_id){
                var data        = pure.posts.elements.questions.answers.A.editors.data,
                    content     = pure.nodes.select.first('*' + '[data-answer-engine-editor-content="' + question_id + '"]' +
                                                                '[data-answer-engine-answer_id="' + answer_id + '"]');
                if (typeof data[question_id + answer_id] !== 'undefined' && content !== null){
                    return (content.style.display === 'none' ? true : false);
                }
                return false;
            },
            closeAfterModify : function(question_id, answer_id){
                var switcher = pure.nodes.select.first('*[data-answer-engine-switcher-editor="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]');
                if (switcher !== null){
                    switcher.checked = false;
                    pure.posts.elements.questions.answers.A.editors.close(question_id, answer_id);
                }
            }
        },
        update      : {
            send : function(question_id, answer_id){
                var request         = pure.system.getInstanceByPath('pure.posts.elements.questions.answers.requests.update'       ),
                    destination     = pure.system.getInstanceByPath('pure.posts.elements.questions.answers.requests.direction'    ),
                    content         = pure.posts.elements.questions.answers.A.editors.content(question_id, answer_id),
                    is_modify       = pure.posts.elements.questions.answers.A.editors.isModify(question_id, answer_id);
                if (request !== null && destination !== null && content !== null){
                    if (pure.posts.elements.questions.answers.A.progress.isBusy(question_id, answer_id) === false){
                        if (content.length > 3 && content.length < 10000){
                            pure.posts.elements.questions.answers.A.progress.busy(question_id, answer_id);
                            content = pure.convertor.UTF8.  encode(content);
                            content = pure.convertor.BASE64.encode(content);
                            pure.tools.request.sendWithFields({
                                    type        : 'POST',
                                    url         : destination,
                                    request     : request,
                                    onrecieve   : function (id_request, response) {
                                        pure.posts.elements.questions.answers.A.update.receive(id_request, response, question_id, answer_id, is_modify);
                                    },
                                    onreaction  : null,
                                    onerror     : function (event, id_request) {
                                        pure.posts.elements.questions.answers.A.update.error(event, id_request, question_id, answer_id);
                                    },
                                    ontimeout   : function (event, id_request) {
                                        pure.posts.elements.questions.answers.A.update.error(event, id_request, question_id, answer_id);
                                    }
                                },
                                {
                                    comment_id  : (is_modify === false ? 0 : answer_id),
                                    parent_id   : (is_modify === false ? answer_id : 0),
                                    content     : content
                                }
                            );
                        }else{
                            if (content.length <= 3){
                                pure.posts.elements.questions.answers.A.dialogs.info(
                                    'Incorrect answer',
                                    'Answer should consist of least 4 symbols',
                                    null
                                );
                            }
                            if (content.length > 10000){
                                pure.posts.elements.questions.answers.A.dialogs.info(
                                    'Incorrect answer',
                                    'Answer cannot be longer than 20000 symbols',
                                    null
                                );
                            }
                        }
                    }
                }
            },
            receive : function(id_request, response, question_id, answer_id, is_modify){
                var data = null;
                pure.posts.elements.questions.answers.A.progress.clear(question_id, answer_id);
                if (response !== 'error'){
                    try{
                        data = JSON.parse(response);
                        if (is_modify !== false){
                            pure.posts.elements.questions.answers.A.update.render.update(question_id, answer_id, data.comment);
                        }else{
                            pure.posts.elements.questions.answers.A.update.render.add(question_id, answer_id, data, false);
                        }
                        pure.posts.elements.questions.answers.A.hotUpdate.call();
                    }catch (e){

                    }
                }
            },
            error   : function(event, id_request, question_id, answer_id){
                pure.posts.elements.questions.answers.A.progress.clear(question_id, answer_id);
            },
            render  : {
                update : function(question_id, answer_id, content){
                    var content_node = pure.nodes.select.first('*' +    '[data-answer-engine-editor-content="' + question_id + '"]' +
                                                                        '[data-answer-engine-answer_id="' + answer_id + '"]');
                    if (content_node !== null){
                        content_node.innerHTML = content;
                        pure.posts.elements.questions.answers.A.editors.closeAfterModify(question_id, answer_id);
                    }
                },
                add : function(question_id, answer_id, params, more){
                    var templates   = {
                            root        : pure.system.getInstanceByPath('pure.posts.elements.questions.answers.templates.root'),
                            included    : pure.system.getInstanceByPath('pure.posts.elements.questions.answers.templates.included')
                        },
                        included    = pure.nodes.select.first('*[data-answer-engine-included-container="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]'),
                        root_mark   = pure.nodes.select.first('*[data-answer-engine-root-mark="' + question_id + '"]'),
                        more_mark   = pure.nodes.select.first('*[data-answer-engine-more-progress="' + question_id + '"]'),
                        node        = document.createElement('DIV');
                    if (pure.tools.objects.isValueIn(templates, null) === false) {
                        if (included !== null){
                            //sub level
                            templates.included  = templates.included.replace(/\[answer_id\]/gi,      params.id               );
                            templates.included  = templates.included.replace(/\[question_id\]/gi,    params.post_id          );
                            templates.included  = templates.included.replace(/\[created\]/gi,        params.date             );
                            templates.included  = templates.included.replace(/\[content\]/gi,        params.comment          );
                            templates.included  = templates.included.replace(/\[avatar\]/gi,         params.author_avatar    );
                            templates.included  = templates.included.replace(/\[author\]/gi,         params.author_name      );
                            templates.included  = templates.included.replace(/\[author_url\]/gi,     params.author_url       );
                            node.innerHTML      = templates.included;
                            pure.nodes.move.appendChildsTo(included, node.childNodes);
                            pure.posts.elements.questions.answers.A.update.render.permissions(question_id, params.id, params.author_id);
                            pure.posts.elements.questions.answers.A.init.open   ();
                            pure.posts.elements.questions.answers.A.init.modify ();
                            pure.posts.elements.questions.answers.A.init.send   ();
                            pure.appevents.Actions.call(
                                'pure.mana.icons',
                                'new',
                                {
                                    object  : 'comment',
                                    IDs     : [{
                                        object_id   : params.id ,
                                        user_id     : params.author_id
                                    }],
                                    field   : question_id
                                },
                                null
                            );
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
                            pure.posts.elements.questions.answers.A.editors.closeAfterModify(question_id, answer_id);
                            pure.posts.elements.questions.answers.A.titles.update(question_id, answer_id);
                        }else{
                            if (root_mark !== null && more_mark !== null){
                                //root level
                                templates.root  = templates.root.replace(/\[answer_id\]/gi,      params.id               );
                                templates.root  = templates.root.replace(/\[question_id\]/gi,    params.post_id          );
                                templates.root  = templates.root.replace(/\[created\]/gi,        params.date             );
                                templates.root  = templates.root.replace(/\[content\]/gi,        params.comment          );
                                templates.root  = templates.root.replace(/\[avatar\]/gi,         params.author_avatar    );
                                templates.root  = templates.root.replace(/\[author\]/gi,         params.author_name      );
                                templates.root  = templates.root.replace(/\[author_url\]/gi,     params.author_url       );
                                node.innerHTML  = templates.root;
                                if (more !== false){
                                    pure.nodes.move.insertChildsBefore(more_mark, node.childNodes);
                                }else{
                                    pure.nodes.move.insertChildsAfter(root_mark, node.childNodes);
                                }
                                pure.posts.elements.questions.answers.A.update.render.permissions(question_id, params.id, params.author_id);
                                pure.posts.elements.questions.answers.A.init.open   ();
                                pure.posts.elements.questions.answers.A.init.modify ();
                                pure.posts.elements.questions.answers.A.init.send   ();
                                if (more === false) {
                                    pure.appevents.Actions.call(
                                        'pure.mana.icons',
                                        'new',
                                        {
                                            object: 'comment',
                                            IDs: [{
                                                object_id: params.id,
                                                user_id: params.author_id
                                            }],
                                            field: question_id
                                        },
                                        null
                                    );
                                }
                                pure.appevents.Actions.call(
                                    'pure.questions.solution',
                                    'new',
                                    {
                                        question_id     : question_id,
                                        object_id       : params.id,
                                        object_type     : 'answer',
                                        is_active       : (typeof params.is_solution !== 'undefined' ? params.is_solution : false)
                                    },
                                    null
                                );
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
                                pure.posts.elements.questions.answers.A.editors.closeAfterModify(question_id, answer_id);
                                pure.posts.elements.questions.answers.A.titles.update(question_id, params.id);
                                pure.posts.elements.questions.answers.A.more.count.increase(
                                    question_id,
                                    (more !== false ? false : true)
                                );
                            }
                        }
                    }
                },
                permissions : function(question_id, answer_id, author_id){
                    function denyModify(question_id, answer_id){
                        var attachment  = pure.nodes.select.first('*[data-answer-engine-attachment-answer="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]'),
                            modify      = pure.nodes.select.first('*[data-answer-engine-modify-answer="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]');
                        if (attachment !== null){
                            attachment.parentNode.removeChild(attachment);
                        }
                        if (modify !== null){
                            modify.parentNode.removeChild(modify);
                        }
                    };
                    function denyAll(question_id, answer_id){
                        var reply  = pure.nodes.select.first('*[data-answer-engine-reply-answer="' + question_id + '"][data-answer-engine-answer_id="' + answer_id + '"]');
                        if (reply !== null){
                            reply.parentNode.removeChild(reply);
                        }
                        denyModify(question_id, answer_id);
                    };
                    var user_id = pure.system.getInstanceByPath('pure.posts.elements.questions.answers.configuration.user_id');
                    if (user_id === null){
                        denyAll(question_id, answer_id);
                    }else{
                        if (parseInt(user_id, 10) !== parseInt(author_id, 10)){
                            denyModify(question_id, answer_id);
                        }
                    }
                }
            }
        },
        more        : {
            init : function(){
                function button(type){
                    var instances = pure.nodes.select.all('*[data-answer-engine-more-' + type + ']:not([data-element-inited])');
                    if (instances !== null){
                        Array.prototype.forEach.call(
                            instances,
                            function(button, _index, source){
                                var question_id = button.getAttribute('data-answer-engine-more-' + type + '');
                                pure.events.add(
                                    button,
                                    'click',
                                    function(){
                                        pure.posts.elements.questions.answers.A.more.request.send(question_id, type);
                                    }
                                );
                                button.setAttribute('data-element-inited', 'true');
                            }
                        );
                    }
                };
                button('next');
                button('all');
            },
            count   : {
                get : function(question_id){
                    var shown = pure.nodes.select.first('*[data-answer-engine-count-shown="' + question_id + '"]'),
                        total = pure.nodes.select.first('*[data-answer-engine-count-total="' + question_id + '"]');
                    if (shown !== null && total !== null){
                        return {
                           total : parseInt(total.innerHTML, 10),
                           shown : parseInt(shown.innerHTML, 10)
                        };
                    }
                    return null;
                },
                increase : function(question_id, new_answer){
                    var shown = pure.nodes.select.first('*[data-answer-engine-count-shown="' + question_id + '"]'),
                        total = pure.nodes.select.first('*[data-answer-engine-count-total="' + question_id + '"]');
                    if (shown !== null && total !== null){
                        shown.innerHTML = parseInt(shown.innerHTML, 10) + 1;
                        if (new_answer !== false){
                            total.innerHTML = parseInt(total.innerHTML, 10) + 1;
                        }
                    }
                }

            },
            request : {
                send : function(question_id, type){
                    var count           = pure.posts.elements.questions.answers.A.more.count.get(question_id),
                        request         = pure.system.getInstanceByPath('pure.posts.elements.questions.answers.requests.more'       ),
                        destination     = pure.system.getInstanceByPath('pure.posts.elements.questions.answers.requests.direction'  );
                    if (count !== null && request !== null && destination !== null){
                        if (pure.posts.elements.questions.answers.A.more.progress.isBusy(question_id) === false){
                            pure.posts.elements.questions.answers.A.more.progress.busy(question_id);
                            pure.tools.request.sendWithFields({
                                    type        : 'POST',
                                    url         : destination,
                                    request     : request,
                                    onrecieve   : function (id_request, response) {
                                        pure.posts.elements.questions.answers.A.more.request.receive(id_request, response, question_id);
                                    },
                                    onreaction  : null,
                                    onerror     : function (event, id_request) {
                                        pure.posts.elements.questions.answers.A.more.request.error(event, id_request, question_id);
                                    },
                                    ontimeout   : function (event, id_request) {
                                        pure.posts.elements.questions.answers.A.more.request.error(event, id_request, question_id);
                                    }
                                },
                                {
                                    shown   : count.shown,
                                    all     : type === 'all' ? 'all' : 'next'
                                }
                            );
                        }
                    }
                },
                receive : function(id_request, response, question_id){
                    var data = null;
                    pure.posts.elements.questions.answers.A.more.progress.clear(question_id);
                    if (response !== 'error'){
                        try{
                            data = JSON.parse(response);
                            pure.posts.elements.questions.answers.A.more.add                (question_id, data.comments, true);
                            pure.posts.elements.questions.answers.A.more.requestMana        (question_id, data.comments);
                            pure.posts.elements.questions.answers.A.more.requestAttachments (question_id, data.comments);
                        }catch(e){

                        }
                    }
                },
                error   : function(id_request, response, question_id){
                    pure.posts.elements.questions.answers.A.more.progress.clear(question_id);
                }
            },
            add         : function(question_id, answers, root){
                for(var index = 0, max_index = answers.length; index < max_index; index += 1){
                    pure.posts.elements.questions.answers.A.update.render.add(
                        question_id,
                        answers[index].comment.parent,
                        {
                            id              : answers[index].comment.id,
                            parent          : answers[index].comment.parent,
                            comment         : answers[index].comment.value,
                            post_id         : question_id,
                            date            : answers[index].comment.date,
                            author_id       : answers[index].author.id,
                            author_name     : answers[index].author.name,
                            author_avatar   : answers[index].author.avatar,
                            author_url      : answers[index].author.home,
                            is_solution     : (typeof answers[index].is_solution !== 'undefined' ? answers[index].is_solution : null)
                        },
                        root
                    );
                    if (answers[index].children !== false){
                        pure.posts.elements.questions.answers.A.more.add(question_id, answers[index].children, false);
                    }
                }
            },
            requestMana : function(question_id, answers){
                function getData(data, answers){
                    for(var index = 0, max_index = answers.length; index < max_index; index += 1){
                        data.push(
                            {
                                object_id   : parseInt(answers[index].comment.id, 10),
                                user_id     : parseInt(answers[index].author.id, 10)
                            }
                        );
                        if (answers[index].children !== false){
                            getData(data, answers[index].children, false);
                        }
                    }
                };
                var data = [];
                getData(data, answers);
                pure.appevents.Actions.call(
                    'pure.mana.icons',
                    'new',
                    {
                        object  : 'comment',
                        IDs     : data,
                        field   : question_id
                    },
                    null
                );
            },
            requestAttachments : function(question_id, answers){
                function getData(object_ids, object_types, answers){
                    for(var index = 0, max_index = answers.length; index < max_index; index += 1){
                        object_ids.push(parseInt(answers[index].comment.id, 10));
                        object_types.push('comment');
                        if (answers[index].children !== false){
                            getData(object_ids, object_types, answers[index].children);
                        }
                    }
                };
                var object_ids      = [],
                    object_types    = [];
                getData(object_ids, object_types, answers);
                pure.appevents.Actions.call(
                    'pure.fileloader',
                    'request.new.attachments',
                    {
                        object_ids      : object_ids,
                        object_types    : object_types
                    },
                    null
                );
            },
            progress    : {
                isBusy      : function(question_id){
                    return pure.templates.progressbar.A.wrapper.isBusy('more_questions' + question_id);
                },
                busy        : function(question_id){
                    pure.templates.progressbar.A.wrapper.busy(
                        'more_questions' + question_id,
                        '*[data-answer-engine-more-progress="' + question_id + '"]',
                        'background:rgba(255,255,255,0.8);'
                    );
                },
                clear       : function(question_id){
                    pure.templates.progressbar.A.wrapper.clear('more_questions' + question_id);
                }
            }
        },
        progress    : {
            isBusy      : function(question_id, answer_id){
                return pure.templates.progressbar.A.wrapper.isBusy(question_id + answer_id);
            },
            busy        : function(question_id, answer_id){
                pure.templates.progressbar.A.wrapper.busy(
                    question_id + answer_id,
                    '*[data-answer-engine-editor-progress="' + question_id + '"]'+
                    '[data-answer-engine-answer_id="' + answer_id + '"]',
                    'background:rgba(255,255,255,0.8);'
                );
            },
            clear       : function(question_id, answer_id){
                pure.templates.progressbar.A.wrapper.clear(question_id + answer_id);
            }
        },
        hotUpdate   : {
            inited      : false,
            init        : function(){
                if (pure.posts.elements.questions.answers.A.hotUpdate.inited === false){
                    pure.appevents.Actions.listen(
                        'webSocketServerEvents',
                        'questions_answer_update',
                        pure.posts.elements.questions.answers.A.hotUpdate.processing,
                        'questions_answer_update_handle'
                    );
                    pure.posts.elements.questions.answers.A.hotUpdate.inited = true;
                }
            },
            call        : function(){
                //Server notification
                pure.appevents.Actions.call('webSocketsServer','wakeup', null, null);
            },
            processing  : function(params){
                var parameters = (typeof params.parameters === 'object' ? params.parameters : null);
                if (parameters !== null){
                    if (pure.nodes.select.first('*' +   '[data-answer-engine-editor-content="' + parameters.post_id + '"]' +
                                                        '[data-answer-engine-answer_id="' + parameters.id + '"]') !== null){
                        pure.posts.elements.questions.answers.A.update.render.update(
                            parameters.post_id,
                            parameters.id,
                            parameters.comment
                        );
                    }else{
                        //Check parent before
                        if (pure.nodes.select.first('*' +   '[data-answer-engine-editor-content="' + parameters.post_id + '"]' +
                                                            '[data-answer-engine-answer_id="' + parameters.parent + '"]') !== null){
                            pure.posts.elements.questions.answers.A.update.render.add(
                                parameters.post_id,
                                parameters.parent,
                                parameters,
                                false
                            );
                        }
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
    pure.system.start.add(pure.posts.elements.questions.answers.A.init.all);
}());