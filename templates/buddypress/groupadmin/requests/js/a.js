(function () {
    if (typeof window.pure                                  !== "object") { window.pure                                     = {}; }
    if (typeof window.pure.buddypress                       !== "object") { window.pure.buddypress                          = {}; }
    if (typeof window.pure.buddypress.groupadmin            !== "object") { window.pure.buddypress.groupadmin               = {}; }
    if (typeof window.pure.buddypress.groupadmin.requests   !== "object") { window.pure.buddypress.groupadmin.requests      = {}; }
    "use strict";
    window.pure.buddypress.groupadmin.requests.A = {
        init        : function () {
            pure.buddypress.groupadmin.requests.A.initialize.   init();
            pure.buddypress.groupadmin.requests.A.actions.      init();
        },
        initialize  : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Requests.Container"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        (function(node){
                            function attachEvents(eventType, id){
                                var callers = pure.nodes.select.all('*[data-engine-grouprequests-' + eventType + '-caller="' + id + '"]:not([data-type-element-inited])');
                                if (callers !== null){
                                    for(var index = callers.length - 1; index >= 0; index -= 1){
                                        if (eventType === 'open'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.groupadmin.requests.A.render.show(id);
                                            });
                                        }
                                        if (eventType === 'close'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.groupadmin.requests.A.render.hide(id);
                                            });
                                        }
                                        callers[index].setAttribute('data-type-element-inited', 'true');
                                    }
                                }
                            };
                            var id      = node.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.buddypress.groupadmin.requests.A.render.hide          (id);
                                pure.buddypress.groupadmin.requests.A.render.orderOnTop    (id);
                                attachEvents('open',    id);
                                attachEvents('close',   id);
                            }
                            node.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            }
        },
        render      : {
            orderOnTop  : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Requests.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        document.body.appendChild(instances[index]);
                    }
                }
            },
            show        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Requests.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    pure.buddypress.groupadmin.requests.A.render.orderOnTop (id);
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = '';
                    }
                }
            },
            hide        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="Group.Requests.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = 'none';
                    }
                }
            }
        },
        actions     : {
            init    : function(){
                function getCallers(type){
                    var callers = pure.nodes.select.all('*[data-field-type="Group.Requests.Button.' + type + '"]:not([data-type-element-inited])');
                    if (callers !== null) {
                        Array.prototype.forEach.call(
                            callers,
                            function(item, index, source){
                                var data = {
                                    member_id   : item.getAttribute('data-engine-data-memberID'     ),
                                    member_name : item.getAttribute('data-engine-data-memberName'   ),
                                    request_id  : item.getAttribute('data-engine-data-request'      ),
                                    instance_id : item.getAttribute('data-engine-element-id'        )
                                };
                                if (pure.tools.objects.isValueIn(data, null) === false){
                                    pure.events.add(
                                        item,
                                        'click',
                                        function(){
                                            pure.buddypress.groupadmin.requests.A.actions.request.send(data, item, type.toLowerCase());
                                        }
                                    );
                                }
                            }
                        );
                    }
                };
                getCallers('Deny'   );
                getCallers('Accept' );
            },
            remove  : function(data){
                var row = pure.nodes.select.first('*[data-field-type="Group.Requests.Row"][data-engine-element-id="' + data.instance_id + '"][data-engine-data-memberID="' + data.member_id + '"]');
                if (row !== null){
                    if (typeof row.parentNode !== 'undefined'){
                        if (typeof row.parentNode.removeChild === 'function'){
                            row.parentNode.removeChild(row);
                        }
                    }
                }
            },
            request : {
                send : function(data, button, type){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.groupadmin.requests.configuration.request.action'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.groupadmin.requests.configuration.destination'   );
                    if (request !== null && destination !== null) {
                        if (type === 'accept' || type === 'deny'){
                            request = request.replace(/\[waited_user\]/,    data.member_id  );
                            request = request.replace(/\[action\]/,         type            );
                            request = request.replace(/\[request_id\]/,     data.request_id );
                            if (pure.buddypress.groupadmin.requests.A.progress.global.isBusy(data.instance_id) === false){
                                pure.buddypress.groupadmin.requests.A.progress.global.busy(data.instance_id);
                                pure.tools.request.send({
                                    type        : 'POST',
                                    url         : destination,
                                    request     : request,
                                    onrecieve   : function (id_request, response) {
                                        pure.buddypress.groupadmin.requests.A.actions.request.onrecieve(id_request, response, data);
                                    },
                                    onreaction  : null,
                                    onerror     : function (event, id_request) {
                                        pure.buddypress.groupadmin.requests.A.actions.request.error(event, id_request, data);
                                    },
                                    ontimeout   : function (event, id_request) {
                                        pure.buddypress.groupadmin.requests.A.actions.request.error(event, id_request, data);
                                    }
                                });
                            }
                        }
                    }
                },
                onrecieve   : function(id_request, response, data){
                    var message = pure.buddypress.groupadmin.requests.A.dialogs.info;
                    switch (response){
                        case 'accepted':
                            message('Success', data.member_name + ' was added to group.');
                            pure.buddypress.groupadmin.requests.A.actions.remove(data);
                            break;
                        case 'denied':
                            message('Success', 'Request of ' + data.member_name + ' was not accepted.');
                            pure.buddypress.groupadmin.requests.A.actions.remove(data);
                            break;
                        default :
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break;
                    }
                    pure.buddypress.groupadmin.requests.A.progress.global.free(data.instance_id);
                },
                error       : function(event, id_request, data){
                    var message = pure.buddypress.groupadmin.requests.A.dialogs.info;
                    pure.buddypress.groupadmin.requests.A.progress.global.free(data.instance_id);
                    message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                }
            }
        },
        progress    : {
            global    : {
                data    : {},
                isBusy : function(instance_id){
                    var data = pure.buddypress.groupadmin.requests.A.progress.global.data;
                    return (typeof data[instance_id] !== 'undefined' ? true : false);
                },
                busy    : function(instance_id){
                    var data        = pure.buddypress.groupadmin.requests.A.progress.global.data,
                        instance    = pure.nodes.select.first('*[data-engine-element="Group.Requests.Page"][data-engine-element-id="' + instance_id + '"]');
                    if (instance !== null){
                        data[instance_id] = pure.templates.progressbar.A.show(instance, "background: -moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%, rgba(255,255,255,1) 5%, rgba(255,255,255,1) 8%, rgba(255,255,255,0) 100%); background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,1)), color-stop(5%,rgba(255,255,255,1)), color-stop(8%,rgba(255,255,255,1)), color-stop(100%,rgba(255,255,255,0))); background: -webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -o-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: radial-gradient(ellipse at center,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#00ffffff',GradientType=1 );z-index:10000;");
                    }
                },
                free    : function(instance_id){
                    var data = pure.buddypress.groupadmin.requests.A.progress.global.data;
                    if (typeof data[instance_id] !== 'undefined'){
                        pure.templates.progressbar.B.hide(data[instance_id]);
                        data[instance_id] = null;
                        delete data[instance_id];
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
                    innerHTML   : '<p>' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : buttons
                });
            }
        }
    };
    pure.system.start.add(pure.buddypress.groupadmin.requests.A.init);
}());