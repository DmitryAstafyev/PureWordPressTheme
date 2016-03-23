(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.buddypress               !== "object") { window.pure.buddypress              = {}; }
    if (typeof window.pure.buddypress.quoterender   !== "object") { window.pure.buddypress.quoterender  = {}; }
    "use strict";
    window.pure.buddypress.quoterender.A = {
        init        : function () {
            pure.buddypress.quoterender.A.initialize.init();
        },
        initialize  : {
            init : function(){
                var instances = pure.nodes.select.all('*[data-engine-element="Quotes.Render.Quote.Instance"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        (function(node){
                            function attachEvents(eventType, id){
                                var callers = pure.nodes.select.all('*[data-engine-element="Quotes.Render.Button.' + eventType + '"][data-engine-quote-id="' + id + '"]:not([data-type-element-inited])');
                                if (callers !== null){
                                    for(var index = callers.length - 1; index >= 0; index -= 1){
                                        if (eventType === 'Attach'){
                                            (function(caller, id){
                                                pure.events.add(caller, 'click', function(event){
                                                    pure.buddypress.quoterender.A.attach.send(id, caller);
                                                });
                                            }(callers[index], id));
                                        }
                                        if (eventType === 'Detach'){
                                            (function(caller, id){
                                                pure.events.add(caller, 'click', function(event){
                                                    pure.buddypress.quoterender.A.detach.send(id, caller);
                                                });
                                            }(callers[index], id));
                                        }
                                        callers[index].setAttribute('data-type-element-inited', 'true');
                                    }
                                }
                            };
                            var id      = node.getAttribute('data-engine-quote-id');
                            if (id !== null && id !== ''){
                                attachEvents('Attach',  id);
                                attachEvents('Detach',  id);
                                pure.buddypress.quoterender.A.buttons.update(id);
                            }
                            node.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            }
        },
        buttons : {
            update : function(quote_id){
                function setDisplayProperty(buttons, displayProperty){
                    if (buttons !== null){
                        for(var index = buttons.length - 1; index >= 0; index -= 1){
                            buttons[index].style.display = displayProperty;
                        }
                    }
                };
                var instance    = pure.nodes.select.first('*[data-engine-element="Quotes.Render.Quote.Instance"][data-engine-quote-id="' + quote_id + '"]'),
                    state       = null,
                    buttons     = null;
                if (instance !== null) {
                    state = instance.getAttribute('data-engine-quote-attachet-state');
                    if (state !== null && state !== ''){
                        buttons = {
                            attach : pure.nodes.select.all('*[data-engine-element="Quotes.Render.Button.Attach"][data-engine-quote-id="' + quote_id + '"]'),
                            detach : pure.nodes.select.all('*[data-engine-element="Quotes.Render.Button.Detach"][data-engine-quote-id="' + quote_id + '"]')
                        };
                        if (state === 'attached'){
                            setDisplayProperty(buttons.attach, 'none'   );
                            setDisplayProperty(buttons.detach, ''       );
                        }else if(state === 'detached'){
                            setDisplayProperty(buttons.attach, ''       );
                            setDisplayProperty(buttons.detach, 'none'   );
                        }
                    }
                }
            },
            setState : function(quote_id, state, linkedQuoteID){
                function setLinkedQuoteID(quote_id, linkedQuoteID){
                    var detach  = pure.nodes.select.all('*[data-engine-element="Quotes.Render.Button.Detach"][data-engine-quote-id="' + quote_id + '"]');
                    if (detach !== null){
                        for(var index = detach.length - 1; index >= 0; index -= 1){
                            detach[index].setAttribute('data-engine-linked-quote-id', linkedQuoteID);
                        }
                    }
                }
                var instances       = pure.nodes.select.all('*[data-engine-element="Quotes.Render.Quote.Instance"][data-engine-quote-id="' + quote_id + '"]'),
                    linkedQuoteID   = (typeof linkedQuoteID !== 'undefined' ? linkedQuoteID : null);
                if (instances !== null) {
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].setAttribute('data-engine-quote-attachet-state', state);
                    }
                    if(linkedQuoteID !== null){
                        setLinkedQuoteID(quote_id, linkedQuoteID);
                    }
                }
            }
        },
        progress    : {
            data    : {},
            isBusy : function(quote_id){
                var data = pure.buddypress.quoterender.A.progress.data;
                return (typeof data[quote_id] !== 'undefined' ? true : false);
            },
            busy    : function(quote_id, button){
                var data = pure.buddypress.quoterender.A.progress.data;
                data[quote_id] = pure.templates.progressbar.A.show(button);
            },
            free    : function(quote_id){
                var data = pure.buddypress.quoterender.A.progress.data;
                if (typeof data[quote_id] !== 'undefined'){
                    pure.templates.progressbar.A.hide(data[quote_id]);
                    data[quote_id] = null;
                    delete data[quote_id];
                }
            }
        },
        attach : {
            send : function(quote_id, button){
                var request         = pure.system.getInstanceByPath('pure.buddypress.quoterender.configuration.request.import'),
                    destination     = pure.system.getInstanceByPath('pure.buddypress.quoterender.configuration.destination');
                if (request !== null && destination !== null){
                    if (pure.buddypress.quoterender.A.progress.isBusy(quote_id) === false){
                        pure.buddypress.quoterender.A.progress.busy(quote_id, button);
                        request     = request.replace(/\[quote_id\]/, quote_id);
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.quoterender.A.attach.received(id_request, response, quote_id, button);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.quoterender.A.attach.error(event, id_request, quote_id);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.buddypress.quoterender.A.attach.error(event, id_request, quote_id);
                            }
                        });
                    }
                }
            },
            received    :function(id_request, response, quote_id, button){
                var message = pure.buddypress.quoterender.A.dialogs.info,
                    result  = null;
                pure.buddypress.quoterender.A.progress.free(quote_id);
                try{
                    result = JSON.parse(response);
                }catch (e){
                    result = response;
                }
                if (typeof result === 'object'){
                    if (result.status === 'imported'){
                        pure.buddypress.quoterender.A.buttons.setState(quote_id, 'attached', result.quote_id);
                        pure.buddypress.quoterender.A.buttons.update(quote_id);
                    }
                }else {
                    switch (response){
                        case 'removed':
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
                }
            },
            error       : function(event, id_request, quote_id){
                var message = pure.buddypress.quoterender.A.dialogs.info;
                pure.buddypress.quoterender.A.progress.free(quote_id);
                message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
            }
        },
        detach      : {
            send : function(quote_id, button){
                var request         = pure.system.getInstanceByPath('pure.buddypress.quoterender.configuration.request.detach'),
                    destination     = pure.system.getInstanceByPath('pure.buddypress.quoterender.configuration.destination'),
                    linkedQuoteID   = button.getAttribute('data-engine-linked-quote-id');
                if (request !== null && destination !== null && linkedQuoteID !== null && linkedQuoteID !== ''){
                    if (pure.buddypress.quoterender.A.progress.isBusy(quote_id) === false){
                        pure.buddypress.quoterender.A.progress.busy(quote_id, button);
                        request     = request.replace(/\[quote_id\]/, linkedQuoteID);
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.quoterender.A.detach.received(id_request, response, quote_id);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.quoterender.A.detach.error(event, id_request, quote_id);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.buddypress.quoterender.A.detach.error(event, id_request, quote_id);
                            }
                        });
                    }
                }
            },
            received    :function(id_request, response, quote_id){
                var message = pure.buddypress.quoterender.A.dialogs.info;
                pure.buddypress.quoterender.A.progress.free(quote_id);
                switch (response){
                    case 'removed':
                        pure.buddypress.quoterender.A.buttons.setState(quote_id, 'detached');
                        pure.buddypress.quoterender.A.buttons.update(quote_id);
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
                var message = pure.buddypress.quoterender.A.dialogs.info;
                pure.buddypress.quoterender.A.progress.free(quote_id);
                message('Fail operation', 'There are (on server) some unknown error. Please contact with administrator.');
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
                    innerHTML   : '<p>' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : buttons
                });
            }
        }
    };
    pure.system.start.add(pure.buddypress.quoterender.A.init);
}());