(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.buddypress               !== "object") { window.pure.buddypress              = {}; }
    if (typeof window.pure.buddypress.managequotes  !== "object") { window.pure.buddypress.managequotes = {}; }
    "use strict";
    window.pure.buddypress.managequotes.A = {
        init        : function () {
            pure.buddypress.managequotes.A.initialize.init();
        },
        initialize  : {
            init : function(){
                var instances = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.Container"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        (function(node){
                            function attachEvents(eventType, id){
                                var callers = pure.nodes.select.all('*[data-engine-managequotes-' + eventType + '-caller="' + id + '"]:not([data-type-element-inited])');
                                if (callers !== null){
                                    for(var index = callers.length - 1; index >= 0; index -= 1){
                                        if (eventType === 'open'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.managequotes.A.render.show(id);
                                            });
                                        }
                                        if (eventType === 'close'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.managequotes.A.render.hide(id);
                                            });
                                        }
                                        callers[index].setAttribute('data-type-element-inited', 'true');
                                    }
                                }
                            };
                            var id      = node.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.buddypress.managequotes.A.render.hide          (id);
                                pure.buddypress.managequotes.A.render.orderOnTop    (id);
                                pure.buddypress.managequotes.A.templates.init       (id);
                                attachEvents('open',    id);
                                attachEvents('close',   id);
                            }
                            node.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                    pure.buddypress.managequotes.A.add.init();
                    pure.buddypress.managequotes.A.list.init();
                }
            }
        },
        render      : {
            orderOnTop  : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        document.body.appendChild(instances[index]);
                    }
                }
            },
            show        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    pure.buddypress.managequotes.A.render.orderOnTop (id);
                    pure.buddypress.managequotes.A.add.clear();
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = '';
                    }
                }
            },
            hide        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = 'none';
                    }
                }
            }
        },
        list        : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.List.Quote"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance){
                            var quote_id    = instance.getAttribute('data-engine-quote-id'      ),
                                state       = instance.getAttribute('data-engine-quote-state'   );
                            if (quote_id !== null && quote_id !== '' && state !== null && state !== ''){
                                pure.buddypress.managequotes.A.list.buttons.update  (quote_id, state);
                                pure.buddypress.managequotes.A.list.buttons.init    (quote_id);
                            }
                            instance.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            },
            buttons : {
                update : function(quote_id, state){
                    function setDisplayProperty(buttons, displayProperty){
                        if (buttons !== null){
                            for(var index = buttons.length - 1; index >= 0; index -= 1){
                                buttons[index].style.display = displayProperty;
                            }
                        }
                    };
                    var buttons = {
                        activate    : pure.nodes.select.all('*[data-engine-element="Quotes.Manage.List.Quote.Button.Activate"][data-engine-quote-id="' + quote_id + '"]'),
                        deactivate  : pure.nodes.select.all('*[data-engine-element="Quotes.Manage.List.Quote.Button.Deactivate"][data-engine-quote-id="' + quote_id + '"]')
                    };
                    switch(state){
                        case 'active':
                            setDisplayProperty(buttons.activate,    'none'  );
                            setDisplayProperty(buttons.deactivate,  ''      );
                            break;
                        case 'deactive':
                            setDisplayProperty(buttons.activate,    ''      );
                            setDisplayProperty(buttons.deactivate,  'none'  );
                            break;
                    }
                },
                init : function(quote_id){
                    function attachEvents(buttons, action, quote_id){
                        if (buttons !== null){
                            for(var index = buttons.length - 1; index >= 0; index -= 1){
                                (function(button, action, quote_id){
                                    pure.events.add(
                                        buttons[index],
                                        'click',
                                        function(){
                                            pure.buddypress.managequotes.A.list.actions.action(quote_id, action, button);
                                        }
                                    );
                                }(buttons[index], action, quote_id));
                            }
                        }
                    }
                    var buttons = {
                        activate    : pure.nodes.select.all('*[data-engine-element="Quotes.Manage.List.Quote.Button.Activate"][data-engine-quote-id="' + quote_id + '"]'),
                        deactivate  : pure.nodes.select.all('*[data-engine-element="Quotes.Manage.List.Quote.Button.Deactivate"][data-engine-quote-id="' + quote_id + '"]'),
                        remove      : pure.nodes.select.all('*[data-engine-element="Quotes.Manage.List.Quote.Button.Remove"][data-engine-quote-id="' + quote_id + '"]')
                    };
                    attachEvents(buttons.activate,      'activate',     quote_id);
                    attachEvents(buttons.deactivate,    'deactivate',   quote_id);
                    attachEvents(buttons.remove,        'remove',       quote_id);
                }
            },
            progress    : {
                data    : {},
                isBusy : function(quote_id){
                    var data = pure.buddypress.managequotes.A.list.progress.data;
                    return (typeof data[quote_id] !== 'undefined' ? true : false);
                },
                busy    : function(quote_id, button){
                    var data = pure.buddypress.managequotes.A.list.progress.data;
                    data[quote_id] = pure.templates.progressbar.B.show(button);
                },
                free    : function(quote_id){
                    var data = pure.buddypress.managequotes.A.list.progress.data;
                    if (typeof data[quote_id] !== 'undefined'){
                        pure.templates.progressbar.B.hide(data[quote_id]);
                        data[quote_id] = null;
                        delete data[quote_id];
                    }
                }
            },
            actions     : {
                action : function(quote_id, action, button){
                    if (pure.buddypress.managequotes.A.list.progress.isBusy(quote_id) === false){
                        switch (action){
                            case  'activate':
                                pure.buddypress.managequotes.A.list.actions.state.send(quote_id, action, button);
                                break;
                            case  'deactivate':
                                pure.buddypress.managequotes.A.list.actions.state.send(quote_id, action, button);
                                break;
                            case  'remove':
                                pure.buddypress.managequotes.A.list.actions.remove.send(quote_id, action, button);
                                break;
                        }
                    }
                },
                state :{
                    accept : function(quote_id, state){
                        var instances = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.List.Quote"][data-engine-quote-id="' + quote_id + '"]');
                        if (instances !== null){
                            for(var index = instances.length - 1; index >= 0; index -= 1){
                                instances[index].setAttribute('data-engine-quote-state', state);
                            }
                            pure.buddypress.managequotes.A.list.buttons.update(quote_id, state);
                        }
                    },
                    send   : function(quote_id, action, button){
                        var request     = pure.system.getInstanceByPath('pure.buddypress.managequotes.configuration.request.activate'),
                            destination = pure.system.getInstanceByPath('pure.buddypress.managequotes.configuration.destination');
                        if (request !== null && destination !== null){
                            pure.buddypress.managequotes.A.list.progress.busy(quote_id, button);
                            request     = request.replace(/\[quote_id\]/, quote_id);
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.buddypress.managequotes.A.list.actions.state.received(id_request, response, quote_id);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.buddypress.managequotes.A.list.actions.state.error(event, id_request, quote_id);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.buddypress.managequotes.A.list.actions.state.error(event, id_request, quote_id);
                                }
                            });
                        }
                    },
                    received    :function(id_request, response, quote_id){
                        var message = pure.buddypress.managequotes.A.dialogs.info;
                        pure.buddypress.managequotes.A.list.progress.free(quote_id);
                        switch (response){
                            case 'activated':
                                pure.buddypress.managequotes.A.list.actions.state.accept(quote_id, 'active');
                                break;
                            case 'deactivated':
                                pure.buddypress.managequotes.A.list.actions.state.accept(quote_id, 'deactive');
                                break;
                            case 'error_during_updating':
                                message('Fail operation', 'Some error was during updating quote. Please contact with administrator.');
                                break;
                            case 'incorrect_user_data':
                                message('Fail operation', 'Server get incorrect user data. Please contact with administrator.');
                                break;
                            case 'fail':
                                message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                                break;
                            default :
                                message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                                break;
                        }
                    },
                    error       : function(event, id_request, quote_id){
                        var message = pure.buddypress.managequotes.A.dialogs.info;
                        pure.buddypress.managequotes.A.list.progress.free(quote_id);
                        message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                    }
                },
                remove : {
                    accept : function(quote_id){
                        function remove(nodes){
                            if (nodes !== null){
                                for(var index = nodes.length - 1; index >= 0; index -= 1){
                                    nodes[index].parentNode.removeChild(nodes[index]);
                                }
                            }
                        };
                        function addLabel(nodes){
                            var container   = null,
                                instance_id = null;
                            if (nodes !== null){
                                if (nodes.length > 0){
                                    container = pure.nodes.find.parentByAttr(nodes[0], {name:'data-engine-element', value:'Quotes.Manage.Page.Quotes.List'});
                                    if (container !== null){
                                        instance_id = container.getAttribute('data-engine-element-id');
                                        if (instance_id !== null && instance_id !== ''){
                                            pure.buddypress.managequotes.A.add.label.add(instance_id);
                                        }
                                    }
                                }
                            }
                        }
                        var bodies      = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.List.Quote"][data-engine-quote-id="' + quote_id + '"]'),
                            controls    = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.List.Quote.Controls"][data-engine-quote-id="' + quote_id + '"]');
                        addLabel(bodies);
                        remove(bodies);
                        remove(controls);
                    },
                    send   : function(quote_id, action, button){
                        var message     = pure.buddypress.managequotes.A.dialogs.info,
                            request     = pure.system.getInstanceByPath('pure.buddypress.managequotes.configuration.request.remove'),
                            destination = pure.system.getInstanceByPath('pure.buddypress.managequotes.configuration.destination');
                        if (request !== null && destination !== null){
                            request     = request.replace(/\[quote_id\]/, quote_id);
                            message(
                                'Please, confirm operation',
                                'Are you sure? Remove this quote?',
                                [
                                    {
                                        title       : 'CANCEL',
                                        handle      : null,
                                        closeAfter  : true
                                    },
                                    {
                                        title       : 'REMOVE',
                                        handle      : function(){
                                            pure.buddypress.managequotes.A.list.progress.busy(quote_id, button);
                                            pure.tools.request.send({
                                                type        : 'POST',
                                                url         : destination,
                                                request     : request,
                                                onrecieve   : function (id_request, response) {
                                                    pure.buddypress.managequotes.A.list.actions.remove.received(id_request, response, quote_id);
                                                },
                                                onreaction  : null,
                                                onerror     : function (event, id_request) {
                                                    pure.buddypress.managequotes.A.list.actions.remove.error(event, id_request, quote_id);
                                                },
                                                ontimeout   : function (event, id_request) {
                                                    pure.buddypress.managequotes.A.list.actions.remove.error(event, id_request, quote_id);
                                                }
                                            });
                                        },
                                        closeAfter  : true
                                    }
                                ]
                            );
                        }
                    },
                    received    :function(id_request, response, quote_id){
                        var message = pure.buddypress.managequotes.A.dialogs.info;
                        pure.buddypress.managequotes.A.list.progress.free(quote_id);
                        switch (response){
                            case 'removed':
                                pure.buddypress.managequotes.A.list.actions.remove.accept(quote_id);
                                break;
                            case 'error_during_updating':
                                message('Fail operation', 'Some error was during updating quote. Please contact with administrator.');
                                break;
                            case 'incorrect_user_data':
                                message('Fail operation', 'Server get incorrect user data. Please contact with administrator.');
                                break;
                            case 'fail':
                                message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                                break;
                            default :
                                message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                                break;
                        }
                    },
                    error       : function(event, id_request, quote_id){
                        var message = pure.buddypress.managequotes.A.dialogs.info;
                        pure.buddypress.managequotes.A.list.progress.free(quote_id);
                        message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                    }
                }
            }
        },
        add         : {
            init        : function(){
                var textareas   = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.Field.Quote"]:not([data-type-element-inited])');
                if (textareas !== null){
                    for(var index = textareas.length - 1; index >= 0; index -= 1){
                        (function(textarea){
                            var id      = textarea.getAttribute('data-engine-element-id'),
                                buttons = null;
                            if (id !== null && id !== ''){
                                buttons = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.Field.Quote.Send"][data-engine-element-id="' + id + '"]');
                                if (buttons !== null){
                                    for(var index = buttons.length - 1; index >= 0; index -= 1){
                                        (function(textarea, button, id){
                                            pure.events.add(
                                                button,
                                                'click',
                                                function(){
                                                    pure.buddypress.managequotes.A.add.actions.send(textarea, button, id);
                                                }
                                            );
                                        }(textarea, buttons[index], id));
                                    }
                                }
                            }
                            textarea.setAttribute('data-type-element-inited', 'true');
                        }(textareas[index]));
                    }
                }
            },
            clear       : function(){
                var textareas   = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.Field.Quote"]');
                if (textareas !== null) {
                    for (var index = textareas.length - 1; index >= 0; index -= 1) {
                        textareas[index].value = '';
                    }
                }
            },
            progress    : {
                data    : {},
                isBusy : function(instance_id){
                    var data = pure.buddypress.managequotes.A.add.progress.data;
                    return (typeof data[instance_id] !== 'undefined' ? true : false);
                },
                busy    : function(instance_id){
                    var data        = pure.buddypress.managequotes.A.add.progress.data,
                        instance    = pure.nodes.select.first('*[data-engine-element="Quotes.Manage.Page"][data-engine-element-id="' + instance_id + '"]');
                    if (instance !== null){
                        data[instance_id] = pure.templates.progressbar.A.show(instance, "background: -moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%, rgba(255,255,255,1) 5%, rgba(255,255,255,1) 8%, rgba(255,255,255,0) 100%); background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,1)), color-stop(5%,rgba(255,255,255,1)), color-stop(8%,rgba(255,255,255,1)), color-stop(100%,rgba(255,255,255,0))); background: -webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -o-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: radial-gradient(ellipse at center,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#00ffffff',GradientType=1 );z-index:10000;");
                    }
                },
                free    : function(instance_id){
                    var data = pure.buddypress.managequotes.A.add.progress.data;
                    if (typeof data[instance_id] !== 'undefined'){
                        pure.templates.progressbar.B.hide(data[instance_id]);
                        data[instance_id] = null;
                        delete data[instance_id];
                    }
                }
            },
            actions     :{
                accept      : function(params, instance_id){
                    function getNodeTemplate(outerHTML){
                        var node = document.createElement('div');
                        node.innerHTML = outerHTML;
                        return node.childNodes;
                    }
                    var template    = pure.system.getInstanceByPath('pure.buddypress.managequotes.configuration.template.quote'),
                        container   = pure.nodes.select.first('*[data-engine-element="Quotes.Manage.Page.Quotes.List"][data-engine-element-id="' + instance_id + '"]');
                    if (template !== null && container !== null){
                        template = pure.convertor.BASE64.decode(template);
                        template = pure.convertor.UTF8.  decode(template);
                        template = template.replace(/\[quote_id\]/gim, params.quote_id);
                        template = template.replace(/\[state\]/gim,    (parseInt(params.active, 10) === 1 ? 'active' : 'deactive'));
                        template = template.replace(/\[quote\]/gim,    params.quote);
                        template = template.replace(/\[info\]/gim,     params.user_name + ', ' + params.date_created);
                        template = getNodeTemplate(template);
                        if (template !== null){
                            for(var index = 0, max_index = template.length; index < max_index; index += 1){
                                container.appendChild(template[0]);
                            }
                            pure.buddypress.managequotes.A.list.buttons.update  (params.quote_id, (parseInt(params.active, 10) === 1 ? 'active' : 'deactive'));
                            pure.buddypress.managequotes.A.list.buttons.init    (params.quote_id);
                            pure.buddypress.managequotes.A.add.         clear   ();
                            pure.buddypress.managequotes.A.pages.goTo.  list    (instance_id);
                            pure.buddypress.managequotes.A.add.label.   remove  (instance_id);
                        }
                    }
                },
                send        : function(textarea, button, id){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.managequotes.configuration.request.add'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.managequotes.configuration.destination'),
                        quote       = textarea.value,
                        message     = pure.buddypress.managequotes.A.dialogs.info;
                    if (request !== null && destination !== null){
                        if (pure.buddypress.managequotes.A.add.progress.isBusy(id) === false){
                            if (quote.length < 10 || quote.length > 500){
                                message('You cannot do it', 'Quote should be not longer than 500 symbols. Minimal length is 10 symbols. Try again, please.');
                            }else{
                                pure.buddypress.managequotes.A.add.progress.busy(id);
                                request = request.replace(/\[quote\]/, quote);
                                pure.tools.request.send({
                                    type        : 'POST',
                                    url         : destination,
                                    request     : request,
                                    onrecieve   : function (id_request, response) {
                                        pure.buddypress.managequotes.A.add.actions.received(id_request, response, id);
                                    },
                                    onreaction  : null,
                                    onerror     : function (event, id_request) {
                                        pure.buddypress.managequotes.A.add.actions.error(event, id_request, id);
                                    },
                                    ontimeout   : function (event, id_request) {
                                        pure.buddypress.managequotes.A.add.actions.error(event, id_request, id);
                                    }
                                });
                            }
                        }
                    }
                },
                received    : function(id_request, response, instance_id){
                    var message = pure.buddypress.managequotes.A.dialogs.info,
                        result  = null;
                    pure.buddypress.managequotes.A.add.progress.free(instance_id);
                    try{
                        result = JSON.parse(response);
                    }catch (e){
                        result = response;
                    }
                    if (typeof result === 'object'){
                        if (result.status === 'added'){
                            pure.buddypress.managequotes.A.add.actions.accept(result, instance_id);
                        }
                    }else{
                        switch (response){
                            case 'error_during_updating':
                                message('Fail operation', 'Some error was during updating quote. Please contact with administrator.');
                                break;
                            case 'incorrect_user_data':
                                message('Fail operation', 'Server get incorrect user data. Please contact with administrator.');
                                break;
                            case 'fail':
                                message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                                break;
                            default :
                                message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                                break;
                        }
                    }
                },
                error       : function(event, id_request, instance_id){
                    var message = pure.buddypress.managequotes.A.dialogs.info;
                    pure.buddypress.managequotes.A.add.progress.free(instance_id);
                    message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                }
            },
            label : {
                remove  : function(instance_id){
                    var labels = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.List.NoQuotes"][data-engine-id="' + instance_id + '"]');
                    if (labels !== null){
                        for (var index = labels.length - 1; index >= 0; index -= 1){
                            labels[index].parentNode.removeChild(labels[index]);
                        }
                    }
                },
                add     : function(instance_id){
                    function getNodeTemplate(outerHTML){
                        var node = document.createElement('div');
                        node.innerHTML = outerHTML;
                        return node.childNodes;
                    }
                    var template    = pure.system.getInstanceByPath('pure.buddypress.managequotes.configuration.template.noQuotes'),
                        container   = pure.nodes.select.first('*[data-engine-element="Quotes.Manage.Page.Quotes.List"][data-engine-element-id="' + instance_id + '"]'),
                        quotes      = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.Page.Quotes.List"][data-engine-element-id="' + instance_id + '"] *[data-engine-element="Quotes.Manage.List.Quote"]');
                    if (template !== null && container !== null){
                        if (quotes !== null){
                            if (quotes.length > 1){
                                return false;
                            }
                        }
                        template = pure.convertor.BASE64.decode(template);
                        template = pure.convertor.UTF8.  decode(template);
                        template = template.replace(/\[instance_id\]/gim, instance_id);
                        template = getNodeTemplate(template);
                        if (template !== null){
                            for(var index = 0, max_index = template.length; index < max_index; index += 1){
                                container.appendChild(template[0]);
                            }
                        }
                    }
                }
            }
        },
        templates   : {
            init : function(instance_id){
                var buttons = pure.nodes.select.all('*[data-engine-element="Quotes.Manage.Template.Quote.Send"][data-engine-element-id="' + instance_id + '"]:not([data-type-element-inited])');
                if (buttons !== null){
                    for(var index = buttons.length - 1; index >= 0; index -= 1){
                        (function(button, instance_id){
                            pure.events.add(
                                button,
                                'click',
                                function(){
                                    pure.buddypress.managequotes.A.templates.send(instance_id);
                                }
                            );
                            button.setAttribute('data-type-element-inited', 'true');
                        }(buttons[index], instance_id));
                    }
                }
            },
            send : function(instance_id){
                function getTemplate(instance_id){
                    var inputs = pure.nodes.select.all('input[data-engine-element="Quotes.Manage.Quotes.Field.Template"][data-engine-element-id="' + instance_id + '"]');
                    if (inputs !== null){
                        for(var index = inputs.length - 1; index >= 0; index -= 1){
                            if (inputs[index].checked === true){
                                return inputs[index].value;
                            }
                        }
                    }
                    return null;
                }
                var request     = pure.system.getInstanceByPath('pure.buddypress.managequotes.configuration.request.settings'),
                    destination = pure.system.getInstanceByPath('pure.buddypress.managequotes.configuration.destination'),
                    settings    = null;
                if (request !== null && destination !== null){
                    if (pure.buddypress.managequotes.A.add.progress.isBusy(instance_id) === false){
                        settings = {
                            quotes      : {
                                template : getTemplate(instance_id),
                                settings : null
                            }
                        };
                        if (settings.quotes.template !== null){
                            settings.quotes.template = (settings.quotes.template === 'off' ? false : settings.quotes.template);
                            pure.buddypress.managequotes.A.add.progress.busy(instance_id);
                            request = request.replace(/\[settings\]/, JSON.stringify(settings));
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.buddypress.managequotes.A.templates.received(id_request, response, instance_id);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.buddypress.managequotes.A.templates.error(event, id_request, instance_id);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.buddypress.managequotes.A.templates.error(event, id_request, instance_id);
                                }
                            });
                        }
                    }
                }
            },
            received    :function(id_request, response, instance_id){
                var message = pure.buddypress.managequotes.A.dialogs.info;
                pure.buddypress.managequotes.A.add.progress.free(instance_id);
                switch (response){
                    case 'updated':
                        message(
                            'Successful operation',
                            'You updated your configuration. To see changes, you should reload page.',
                            [
                                {
                                    title       : 'CANCEL',
                                    handle      : null,
                                    closeAfter  : true
                                },
                                {
                                    title       : 'RELOAD PAGE NOW',
                                    handle      : function(){
                                        window.location.href = window.location.href;
                                    },
                                    closeAfter  : true
                                }
                            ]
                        );
                        break;
                    case 'error_during_updating':
                        message('Fail operation', 'Some error was during updating quote. Please contact with administrator.');
                        break;
                    case 'incorrect_user_data':
                        message('Fail operation', 'Server get incorrect user data. Please contact with administrator.');
                        break;
                    case 'fail':
                        message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                        break;
                    default :
                        message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
                        break;
                }
            },
            error       : function(event, id_request, instance_id){
                var message = pure.buddypress.managequotes.A.dialogs.info;
                pure.buddypress.managequotes.A.add.progress.free(instance_id);
                message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
            }
        },
        pages       : {
            goTo : {
                list : function(instance_id){
                    var input   = pure.nodes.select.first('input[data-engine-element="Quotes.Manage.Page.Quotes.List.Switcher"][data-engine-element-id="' + instance_id + '"]');
                    if (input !== null){
                        input.checked = true;
                    }
                }
            }
        },
        dialogs     : {
            info: function (title, message, _buttons) {
                var _buttons    = (_buttons instanceof Array ? _buttons : null),
                    buttons     = null;
                if (_buttons !== null){
                    buttons = _buttons;
                }else{
                    buttons = [{
                        title       : 'OK',
                        handle      : null,
                        closeAfter  : true
                    }];
                }
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-element-type="Pure.Social.Groups.ManageQuotes.A.Dialog">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : buttons
                });
            }
        }
    };
    pure.system.start.add(pure.buddypress.managequotes.A.init);
}());