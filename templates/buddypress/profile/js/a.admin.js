(function () {
    if (typeof window.pure                  !== "object") { window.pure                     = {}; }
    if (typeof window.pure.profile          !== "object") { window.pure.profile             = {}; }
    if (typeof window.pure.profile.admin    !== "object") { window.pure.profile.admin       = {}; }
    "use strict";
    window.pure.profile.admin.A = {
        init    : function () {
            pure.profile.admin.A.initialize.buttons.all();
        },
        initialize  : {
            buttons : {
                all             : function(){
                    pure.profile.admin.A.initialize.buttons.updateProfile();
                    pure.profile.admin.A.initialize.buttons.visibilitySelector();
                },
                updateProfile   : function(){
                    var instances = pure.nodes.select.all('*[data-engine-element="user_profile_update"]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var params      = pure.profile.admin.A.Helpers.readDataFromAttributes(node);
                                    if (params.id !== null && params.progress !== null && params.destination !== null && params.user !== null){
                                        pure.events.add(node, 'click', function(event){
                                            pure.profile.admin.A.Actions.updateProfile.onClick(event, node, params.id, params);
                                        });
                                        node.setAttribute('data-type-element-inited', 'true');
                                    }
                                }(instances[index]));
                            }
                        }
                    }
                },
                visibilitySelector : function(){
                    var instances = pure.nodes.select.all('*[data-engine-basic-visibility-selector]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var params      = pure.profile.admin.A.Helpers.readDataFromAttributes(node),
                                        selectors   = null;
                                    if (params.id !== null && params.visibility !== null){
                                        selectors = pure.nodes.select.all('*[data-engine-id="' + params.id + '"][data-engine-visibility-group="' + params.visibility + '"]:not([data-engine-basic-visibility-selector])');
                                        if (selectors !== null){
                                            pure.events.add(node, 'change', function(event){
                                                pure.profile.admin.A.Actions.visibilitySelector.onChange(event, node, selectors, params.id, params.visibility);
                                            });
                                        }
                                        node.setAttribute('data-type-element-inited', 'true');
                                    }
                                }(instances[index]));
                            }
                        }
                    }
                }
            }
        },
        Actions     : {
            common              : {
                permissionMessages : function(response, button){
                    var message  = pure.profile.admin.A.Actions.message;
                    switch (response){
                        case 'wrong_user':
                            message('Fail operation', 'Sorry, you cannot do it, because you has wrong user data.', null, null   );
                            return true;
                            break;
                        case 'wrong_file':
                            message('Fail operation', 'Sorry, you cannot do it, because file is incorrect.', null, null   );
                            return true;
                            break;
                    }
                    return false;
                }
            },
            updateProfile       : {
                onClick     : function(event, button, id, params){
                    var fields = null;
                    if (pure.profile.admin.A.Status.isBusy(button) === false){
                        fields = pure.profile.admin.A.Actions.updateProfile.getFields(id);
                        if (fields !== null){
                            return pure.profile.admin.A.Actions.updateProfile.request.send(button, id, fields, params);
                        }
                    }
                },
                getFields   : function(id){
                    function union(fields){
                        function removeByID(fields, id){
                            for (var index = fields.length - 1; index >= 0; index -= 1) {
                                if (fields[index].id === id){
                                    fields.splice(index, 1);
                                }
                            }
                            return fields;
                        };
                        var id          = null,
                            group       = [],
                            value       = null;
                        for (var index = fields.length - 1; index >= 0; index -= 1){
                            for (var _index = fields.length - 1; _index >= 0; _index -= 1){
                                if (_index !== index && fields[index].id === fields[_index].id){
                                    if (group.length === 0){
                                        group.push({
                                            id          : fields[index].id,
                                            group       : fields[index].group,
                                            value       : fields[index].value,
                                            selected    : fields[index].selected
                                        });
                                    }
                                    group.push({
                                        id          : fields[_index].id,
                                        group       : fields[_index].group,
                                        value       : fields[_index].value,
                                        selected    : fields[_index].selected
                                    });
                                }
                            }
                            if (group.length > 0){
                                value   = [];
                                for(var __index = group.length - 1; __index >= 0; __index -= 1){
                                    if (group[__index].selected === true){
                                        value.push(group[__index].value);
                                    }
                                }
                                fields = removeByID(fields, group[0].id);
                                fields.push({
                                    id          : group[0].id,
                                    group       : group[0].group,
                                    value       : value,
                                    json        : true,
                                    selected    : null
                                });
                                break;
                            }
                        }
                        if (group.length > 0){
                            fields = union(fields);
                        }
                        return fields;
                    };
                    function visibility(fields){
                        var node = null;
                        for(var index = fields.length - 1; index >= 0; index -= 1){
                            node = pure.nodes.select.first('*[data-engine-id="' + id + '"][data-engine-visibility-field-id="' + fields[index].id + '"]');
                            if (node !== null){
                                fields[index].visibility = node.options[node.selectedIndex].value;
                            }
                        }
                        return fields;
                    }
                    var nodes   = pure.nodes.select.all('*[data-engine-id="' + id + '"][data-engine-field-id]'),
                        fields  = [],
                        field   = null;
                    if (nodes !== null){
                        for(var index = nodes.length - 1; index >= 0; index -= 1){
                            field = (function(node){
                                var field_id        = node.getAttribute('data-engine-field-id'          ),
                                    field_group     = node.getAttribute('data-engine-collection-type'   ),
                                    field_value     = null,
                                    field_selected  = null,
                                    attribute       = null;
                                if (field_id !== null && field_group !== null){
                                    if (field_id !== '' && field_group !== ''){
                                        switch (node.nodeName.toLowerCase()){
                                            case 'input':
                                                attribute = node.getAttribute('type');
                                                if (attribute === 'text' || attribute === 'date' || attribute === 'url' || attribute === 'number'){
                                                    field_value     = node.value;
                                                }else if(attribute === 'radio' || attribute === 'checkbox'){
                                                    field_selected  = node.checked;
                                                    field_value     = node.value;
                                                }
                                                break;
                                            case 'option':
                                                field_selected  = node.selected;
                                                field_value     = node.value;
                                                break;
                                            case 'select':
                                                attribute = node.getAttribute('multiple');
                                                if (attribute === 'multiple'){
                                                    if (typeof node.options !== 'undefined'){
                                                        field_value = [];
                                                        for(var index = node.options.length - 1; index >= 0; index -= 1){
                                                            if (node.options[index].selected === true){
                                                                field_value.push(node.options[index].value);
                                                            }
                                                        }
                                                        field_value = (field_value.length > 0 ? field_value.join(',') : null);
                                                    }
                                                }else{
                                                    field_value = node.options[node.selectedIndex].value;
                                                }
                                                break;
                                            case 'textarea':
                                                field_value = node.value;
                                                break;
                                        }
                                        return {
                                            id          : field_id,
                                            group       : field_group,
                                            value       : field_value,
                                            selected    : field_selected
                                        };
                                    }
                                }
                                return null;
                            }(nodes[index]));
                            if (field !== null){
                                if (field.value !== null){
                                    fields.push({
                                        id          : field.id,
                                        group       : field.group,
                                        value       : field.value,
                                        selected    : field.selected
                                    });
                                }
                            }
                        }
                        return visibility(union(fields));
                    }
                    return null;
                },
                request     :{
                    send : function(button, id, fields, params){
                        var parameters  =   'command='  + 'templates_of_profile_update' +   '&' +
                                            'user='     + params.user +                     '&' +
                                            'fields='   + JSON.stringify(fields),
                            progress    = null;
                        pure.profile.admin.A.Status.busy(button);
                        if (typeof pure.templates.progressbar[params.progress] === 'object'){
                            progress = pure.templates.progressbar[params.progress].show(button);
                        }
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : params.destination,
                            request     : parameters,
                            onrecieve   : function (id_request, response) {
                                pure.profile.admin.A.Actions.updateProfile.request.received(id_request, response, button, id, params, progress);
                            },
                            onreaction  : null,
                            onerror     : function (id_request) {
                                pure.profile.admin.A.Actions.updateProfile.request.error(id_request, button, id, params, progress);
                            },
                            ontimeout   : function (id_request) {
                                pure.profile.admin.A.Actions.updateProfile.request.error(id_request, button, id, params, progress);
                            }
                        });
                    },
                    received : function(id_request, response, button, id, params, progress){
                        var message = pure.profile.admin.A.Actions.message;
                        pure.profile.admin.A.Status.clear(button);
                        if (progress !== null) {
                            pure.templates.progressbar[params.progress].hide(progress);
                            progress = null;
                        }
                        switch (response){
                            case 'updated':
                                message('Successful operation', 'You updated your profile.', null, null   );
                                break;
                            case 'error_during_saving':
                                message('Fail operation', 'There are some error on server side. Please contact with administrator.', null, null   );
                                break;
                            case 'incorrect_data':
                                message('Fail operation', 'There are some error on server side. Server cannot process you data. Please contact with administrator.', null, null   );
                                break;
                            case 'fail':
                                message('Fail operation', 'There are some error on server side. You send incorrect data or you have no necessary permissions. Please contact with administrator.', null, null   );
                                break;
                        }
                    },
                    error : function(id_request, button, id, params, progress){
                        var message = pure.profile.admin.A.Actions.message;
                        pure.profile.admin.A.Status.clear(button);
                        if (progress !== null) {
                            pure.templates.progressbar[params.progress].hide(progress);
                            progress = null;
                        }
                        message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.', null, null   );
                    }
                }
            },
            visibilitySelector  : {
                onChange : function(event, basicSelector, selectors, id, visibilityGroup){
                    if (basicSelector.selectedIndex > 0){
                        for(var index = selectors.length - 1; index >= 0; index -= 1){
                            if (selectors[index].getAttribute('disabled') === null){
                                selectors[index].selectedIndex = basicSelector.selectedIndex - 1;
                            }
                        }
                    }
                }
            },
            message         : function(title, message, node, handle, template){
                var template = (typeof template === 'string' ? template : 'B');
                pure.components.dialogs[template].open({
                    title       : title,
                    innerHTML   : '<p>' + message + '</p>',
                    buttons     : [
                        {
                            title   : 'OK',
                            handle  : handle
                        }
                    ],
                    parent      : (node !== null ? pure.nodes.find.parentByAttr(node, {name :'data-engine-element', value:'dialog_parent'}) : document.body),
                    width       : 70
                });
            }
        },
        Status  : {
            isBusy  : function(node){
                var status = node.getAttribute('data-engine-status');
                return (typeof status !== 'string' ? false : (status === '' ? false : (status === 'free' ? false : true )));
            },
            busy    : function(node, group){
                var group = (typeof group === 'object' ? group : null),
                    nodes = null;
                if (group === null){
                    node.setAttribute('data-engine-status', 'busy');
                }else{
                    nodes = pure.nodes.select.all('*[' + group.name + '="' + group.value + '"]');
                    if (nodes !== null){
                        for(var index = nodes.length - 1; index >= 0; index -= 1){
                            nodes[index].setAttribute('data-engine-status', 'busy');
                        }
                    }
                }
            },
            clear   : function(node, group){
                var group = (typeof group === 'object' ? group : null),
                    nodes = null;
                if (group === null){
                    node.setAttribute('data-engine-status', 'free');
                }else{
                    nodes = pure.nodes.select.all('*[' + group.name + '="' + group.value + '"]');
                    if (nodes !== null){
                        for(var index = nodes.length - 1; index >= 0; index -= 1){
                            nodes[index].setAttribute('data-engine-status', 'free');
                        }
                    }
                }
            }
        },
        Helpers : {
            readDataFromAttributes : function(node){
                var params = {
                        id              : node.getAttribute('data-engine-id'                ),
                        user            : node.getAttribute('data-engine-data-user'         ),
                        request         : node.getAttribute('data-engine-data-request'      ),
                        progress        : node.getAttribute('data-engine-data-progress'     ),
                        destination     : node.getAttribute('data-engine-data-destination'  ),
                        visibility      : node.getAttribute('data-engine-visibility-group'  )
                    };
                for(var key in params){
                    params[key] = (typeof params[key] === 'string' ? (params[key] !== '' ? params[key] : null) : null);
                }
                return params;
            }
        }
    };
    pure.system.start.add(pure.profile.admin.A.init);
}());