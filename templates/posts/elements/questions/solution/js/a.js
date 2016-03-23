(function () {
    if (typeof window.pure                                      !== "object") { window.pure                                     = {}; }
    if (typeof window.pure.posts                                !== "object") { window.pure.posts                               = {}; }
    if (typeof window.pure.posts.elements                       !== "object") { window.pure.posts.elements                      = {}; }
    if (typeof window.pure.posts.elements.questions             !== "object") { window.pure.posts.elements.questions            = {}; }
    if (typeof window.pure.posts.elements.questions.solution    !== "object") { window.pure.posts.elements.questions.solution   = {}; }
    "use strict";
    window.pure.posts.elements.questions.solution.A = {
        init        : {
            buttons : function(){
                function button(type){
                    var instances = pure.nodes.select.all('*[data-engine-solution-button-' + type + ']:not([data-element-inited])');
                    if (instances !== null) {
                        Array.prototype.forEach.call(
                            instances,
                            function (button, _index, source) {
                                var object_id   = button.getAttribute('data-engine-solution-button-' + type),
                                    object_type = button.getAttribute('data-engine-solution-object_type'),
                                    question_id = button.getAttribute('data-engine-solution-question_id');
                                if (object_id !== null && object_type !== null && question_id !== null){
                                    pure.events.add(
                                        button,
                                        'click',
                                        function(){
                                            pure.posts.elements.questions.solution.A.change.open(
                                                question_id,
                                                object_id,
                                                object_type,
                                                (type === 'yes' ? true : false)
                                            );
                                        }
                                    );
                                }
                                button.setAttribute('data-element-inited', 'true');
                            }
                        );
                    }
                };
                button('yes');
                button('no' );
            },
            templates   : function(){
                var templates = {
                    container   : pure.system.getInstanceByPath('pure.posts.elements.questions.solution.template'),
                    active      : pure.system.getInstanceByPath('pure.posts.elements.questions.solution.icons.active'),
                    inactive    : pure.system.getInstanceByPath('pure.posts.elements.questions.solution.icons.inactive')
                };
                if (pure.tools.objects.isValueIn(templates, null) === false){
                    pure.posts.elements.questions.solution.template = pure.convertor.UTF8.decode(
                        pure.convertor.BASE64.decode(pure.posts.elements.questions.solution.template)
                    );
                    pure.posts.elements.questions.solution.icons.active = pure.convertor.UTF8.decode(
                        pure.convertor.BASE64.decode(pure.posts.elements.questions.solution.icons.active)
                    );
                    pure.posts.elements.questions.solution.icons.inactive = pure.convertor.UTF8.decode(
                        pure.convertor.BASE64.decode(pure.posts.elements.questions.solution.icons.inactive)
                    );
                }
            },
            all         : function(){
                pure.posts.elements.questions.solution.A.init.      buttons     ();
                pure.posts.elements.questions.solution.A.init.      templates   ();
                pure.posts.elements.questions.solution.A.update.    init        ();
                pure.posts.elements.questions.solution.A.hotUpdate. init        ();
            }
        },
        change      : {
            isActive : function(question_id, object_id, object_type){
                var icon        = pure.nodes.select.first('img' +   '[data-engine-solution-question_id="' + question_id + '"]' +
                                                                    '[data-engine-solution-icon="' + object_id + '"]' +
                                                                    '[data-engine-solution-object_type="' + object_type + '"]'),
                    active      = pure.system.getInstanceByPath('pure.posts.elements.questions.solution.icons.active'),
                    inactive    = pure.system.getInstanceByPath('pure.posts.elements.questions.solution.icons.inactive');
                if (icon !== null && active !== null && inactive !== null){
                    return (icon.src == active ? true : false);
                }
                return null;
            },
            setActive : function(question_id, object_id, object_type, is_active){
                var icon        = pure.nodes.select.first('img' +   '[data-engine-solution-question_id="' + question_id + '"]' +
                                                                    '[data-engine-solution-icon="' + object_id + '"]' +
                                                                    '[data-engine-solution-object_type="' + object_type + '"]'),
                    active      = pure.system.getInstanceByPath('pure.posts.elements.questions.solution.icons.active'),
                    inactive    = pure.system.getInstanceByPath('pure.posts.elements.questions.solution.icons.inactive');
                if (icon !== null && active !== null && inactive !== null){
                    icon.src = (is_active === false ? inactive : active);
                    return true;
                }
                return false;
            },
            counts  : function(question_id){
                var active          = pure.system.getInstanceByPath('pure.posts.elements.questions.solution.icons.active'),
                    inactive        = pure.system.getInstanceByPath('pure.posts.elements.questions.solution.icons.inactive'),
                    icons_active    = pure.nodes.select.all('img' + '[data-engine-solution-question_id="' + question_id + '"]' +
                                                                    '[src="' + active + '"]'),
                    icons_inactive  = pure.nodes.select.all('img' + '[data-engine-solution-question_id="' + question_id + '"]' +
                                                                    '[src="' + inactive + '"]');
                if (icons_active !== null && icons_inactive !== null){
                    return {
                        active      : icons_active.length,
                        inactive    : icons_inactive.length
                    };
                }
            },
            open    : function(question_id, object_id, object_type, activate){
                var is_active   = pure.posts.elements.questions.solution.A.change.isActive(question_id, object_id, object_type),
                    counts      = null;
                if (activate !== is_active){
                    counts = pure.posts.elements.questions.solution.A.change.counts(question_id);
                    if (counts !== null){
                        if (activate === false && counts.active === 1){
                            pure.posts.elements.questions.solution.A.dialogs.question(
                                'Confirm operation',
                                'It is last items, which are marked as solution. If you cancel it, your questions will be without solution.',
                                [
                                    {
                                        title       : 'RETURN',
                                        handle      : null,
                                        closeAfter  : true
                                    },
                                    {
                                        title       : 'DEACTIVATE',
                                        handle      : function(){
                                            pure.posts.elements.questions.solution.A.change.send(question_id, object_id, object_type, activate);
                                        },
                                        closeAfter  : true
                                    }
                                ]
                            );
                            return true;
                        }
                        if (activate === true && counts.active >= 1){
                            pure.posts.elements.questions.solution.A.dialogs.question(
                                'Confirm operation',
                                'Your question has solutions. You can define more then one solutions for sure, but you have to confirm it.',
                                [
                                    {
                                        title       : 'RETURN',
                                        handle      : null,
                                        closeAfter  : true
                                    },
                                    {
                                        title       : 'ACTIVATE',
                                        handle      : function(){
                                            pure.posts.elements.questions.solution.A.change.send(question_id, object_id, object_type, activate);
                                        },
                                        closeAfter  : true
                                    }
                                ]
                            );
                            return true;
                        }
                        pure.posts.elements.questions.solution.A.change.send(question_id, object_id, object_type, activate);
                    }
                }
            },
            send    : function(question_id, object_id, object_type, activate){
                var request         = pure.system.getInstanceByPath('pure.posts.elements.questions.solution.requests.set'       ),
                    destination     = pure.system.getInstanceByPath('pure.posts.elements.questions.solution.requests.direction' );
                if (request !== null && destination !== null){
                    if (pure.posts.elements.questions.solution.A.progress.isBusy(question_id, object_id, object_type) === false){
                        pure.posts.elements.questions.solution.A.progress.busy(question_id, object_id, object_type);
                        pure.tools.request.sendWithFields({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.posts.elements.questions.solution.A.change.receive(id_request, response, question_id, object_id, object_type, activate);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.posts.elements.questions.solution.A.change.error(event, id_request, question_id, object_id, object_type, activate);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.posts.elements.questions.solution.A.change.error(event, id_request, question_id, object_id, object_type, activate);
                                }
                            },
                            {
                                object_id   : object_id,
                                object_type : object_type,
                                question_id : question_id
                            }
                        );
                    }
                }
            },
            receive : function(id_request, response, question_id, object_id, object_type, activate){
                var data = null;
                pure.posts.elements.questions.solution.A.progress.clear(question_id, object_id, object_type);
                if (response !== 'error'){
                    try{
                        data = JSON.parse(response);
                        if (typeof data.is_item_active === 'boolean'){
                            pure.posts.elements.questions.solution.A.change.setActive(
                                question_id,
                                object_id,
                                object_type,
                                data.is_item_active
                            );
                            pure.posts.elements.questions.solution.A.hotUpdate.call();
                        }
                    }catch (e){

                    }
                }
            },
            error   : function(event, id_request, question_id, object_id, object_type, activate){
                pure.posts.elements.questions.solution.A.progress.clear(question_id, object_id, object_type);
            }
        },
        update      : {
            inited : false,
            init : function(){
                if (pure.posts.elements.questions.solution.A.update.inited === false){
                    pure.appevents.Actions.listen(
                        'pure.questions.solution',
                        'new',
                        pure.posts.elements.questions.solution.A.update.handle,
                        'pure.questions.solution.new'
                    );
                }
            },
            handle : function(params){
                if (typeof params.question_id !== 'undefined' && typeof params.object_id !== 'undefined' &&
                    typeof params.object_type !== 'undefined' && typeof params.is_active !== 'undefined'){
                    pure.posts.elements.questions.solution.A.init.buttons();
                    pure.posts.elements.questions.solution.A.change.setActive(
                        params.question_id,
                        params.object_id,
                        params.object_type,
                        params.is_active
                    );
                }
             }
        },
        progress    : {
            isBusy      : function(question_id, object_id, object_type){
                return pure.templates.progressbar.A.wrapper.isBusy(question_id + object_type + object_id);
            },
            busy        : function(question_id, object_id, object_type){
                pure.templates.progressbar.A.wrapper.busy(
                    question_id + object_type + object_id,
                    '*[data-engine-solution-container="' + object_id + '"]'+
                    '[data-engine-solution-object_type="' + object_type + '"]'+
                    '[data-engine-solution-question_id="' + question_id + '"]',
                    'background:rgba(255,255,255,0.8);'
                );
            },
            clear       : function(question_id, object_id, object_type){
                pure.templates.progressbar.A.wrapper.clear(question_id + object_type + object_id);
            }
        },
        hotUpdate   : {
            inited      : false,
            init        : function(){
                if (pure.posts.elements.questions.solution.A.hotUpdate.inited === false){
                    pure.appevents.Actions.listen(
                        'webSocketServerEvents',
                        'questions_solution_update',
                        pure.posts.elements.questions.solution.A.hotUpdate.processing,
                        'questions_solution_update_handle'
                    );
                    pure.posts.elements.questions.solution.A.hotUpdate.inited = true;
                }
            },
            call        : function(){
                //Server notification
                pure.appevents.Actions.call('webSocketsServer','wakeup', null, null);
            },
            processing  : function(params){
                var parameters = (typeof params.parameters === 'object' ? params.parameters : null);
                if (parameters !== null){
                    if (typeof params.parameters !== 'undefined'){
                        pure.posts.elements.questions.solution.A.update.handle(params.parameters);
                    }
                }
            }
        },
        dialogs     : {
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
    pure.system.start.add(pure.posts.elements.questions.solution.A.init.all);
}());