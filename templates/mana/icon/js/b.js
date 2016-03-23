(function () {
    if (typeof window.pure              !== "object") { window.pure             = {}; }
    if (typeof window.pure.mana         !== "object") { window.pure.mana        = {}; }
    if (typeof window.pure.mana.icon    !== "object") { window.pure.mana.icon   = {}; }
    "use strict";
    window.pure.mana.icon.B = {
        init : {
            buttons : function(){
                function initButtonType(button_type){
                    var instances = pure.nodes.select.all(  '*' +   '[data-engine-manaminimal-button-'+ button_type +']' +
                                                                    '[data-engine-manaminimal-object]' +
                                                                    ':not([data-element-inited])');
                    if (instances !== null){
                        Array.prototype.forEach.call(
                            instances,
                            function(button, index, source){
                                var object_id   = button.getAttribute('data-engine-manaminimal-button-'+ button_type +''),
                                    object_type = button.getAttribute('data-engine-manaminimal-object'),
                                    field       = button.getAttribute('data-engine-manaminimal-field');
                                if (object_id !== null && object_type !== null){
                                    pure.events.add(
                                        button,
                                        'click',
                                        function(){
                                            pure.mana.icon.B.change.send(
                                                object_id,
                                                object_type,
                                                (field === null ? '' : field),
                                                button_type
                                            );
                                        }
                                    );
                                }
                                button.setAttribute('data-element-inited', 'true');
                            }
                        );
                    }
                };
                initButtonType('plus'   );
                initButtonType('minus'  );
            },
            templates   : function(){
                var templates = {
                    container : pure.system.getInstanceByPath('pure.mana.icon.templates.B')
                };
                if (pure.tools.objects.isValueIn(templates, null) === false){
                    pure.mana.icon.templates.B = pure.convertor.UTF8.decode(
                        pure.convertor.BASE64.decode(pure.mana.icon.templates.B)
                    );
                }
            },
            all : function(){
                pure.mana.icon.B.init.      buttons         ();
                pure.mana.icon.B.init.      templates       ();
                pure.mana.icon.B.add.       init            ();
                pure.mana.icon.B.change.    updateAllValues ();
                pure.mana.icon.B.hotUpdate. init            ();
            }
        },
        change : {
            send : function(object_id, object_type, field, direction){
                var request     = pure.system.getInstanceByPath('pure.mana.icons.configuration.requests.set'),
                    destination = pure.system.getInstanceByPath('pure.mana.icons.configuration.requestURL');
                if (request !== null && destination !== null){
                    if (pure.mana.icon.B.progress.isBusy(object_id, object_type) === false){
                        pure.mana.icon.B.progress.busy(object_id, object_type);
                        pure.tools.request.sendWithFields({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.mana.icon.B.change.receive(id_request, response, object_id, object_type, (direction === 'plus' ? 1 : -1));
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.mana.icon.B.change.error(event, id_request, object_id, object_type);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.mana.icon.B.change.error(event, id_request, object_id, object_type);
                                }
                            },
                            {
                                object_id   : object_id,
                                object      : object_type,
                                value       : (direction === 'plus' ? 1 : -1),
                                field       : field
                            }
                        );
                    }
                }
            },
            receive : function(id_request, response, object_id, object_type, value){
                pure.mana.icon.B.progress.clear(object_id, object_type);
                switch (response){
                    case 'success':
                        pure.mana.icon.B.change.    removeButtons   (object_id, object_type);
                        pure.mana.icon.B.change.    updateValues    (object_id, object_type, value);
                        pure.mana.icon.B.hotUpdate. call            ();
                        break;
                    case 'error':
                        pure.mana.icon.B.dialogs.info(
                            'Server error',
                            'Sorry, but server had gotten some error during work. Try again a bit later.',
                            null
                        );
                        break;
                    case 'fail':
                        pure.mana.icon.B.dialogs.info(
                            'Server error',
                            'Sorry, but server had gotten some error during work. Try again a bit later.',
                            null
                        );
                        break;
                    case 'voted':
                        pure.mana.icon.B.dialogs.info(
                            'It seems you had forgotten.',
                            'You had voted before. You cannot vote twice.',
                            null
                        );
                        pure.mana.icon.B.change.removeButtons(object_id, object_type);
                        break;
                }
            },
            error   : function(event, id_request, object_id, object_type){
                pure.mana.icon.B.progress.clear(object_id, object_type);
            },
            removeButtons : function(object_id, object_type){
                function remove(button_type){
                    var button = pure.nodes.select.first(  '*' +    '[data-engine-manaminimal-button-'+ button_type +'="' + object_id + '"]' +
                                                                    '[data-engine-manaminimal-object="' + object_type + '"]');
                    if (button !== null){
                        button.parentNode.removeChild(button);
                    }
                };
                remove('plus');
                remove('minus');
            },
            updateValues : function(object_id, object_type, value){
                var label   = pure.nodes.select.first(  '*' +   '[data-engine-manaminimal-value="' + object_id + '"]' +
                                                                '[data-engine-manaminimal-object="' + object_type + '"]'),
                    attr    = null,
                    current = null;
                if (label !== null){
                    current = parseInt(label.innerHTML, 10);
                    attr    = label.getAttribute('data-engine-manaminimal-value-attribute');
                    if (typeof current === 'number'){
                        if (value !== null){
                            current         = current + value;
                            label.innerHTML = current;
                        }
                        if (typeof attr === 'string'){
                            if (current >= 0){
                                label.setAttribute(attr, 'positive');
                            }else{
                                label.setAttribute(attr, 'negative');
                            }
                        }
                    }
                }
            },
            replaceValues : function(object_id, object_type, value){
                var label   = pure.nodes.select.first(  '*' +   '[data-engine-manaminimal-value="' + object_id + '"]' +
                                                                '[data-engine-manaminimal-object="' + object_type + '"]'),
                    attr    = null;
                if (label !== null){
                    attr    = label.getAttribute('data-engine-manaminimal-value-attribute');
                    if (typeof value === 'number'){
                        label.innerHTML = value;
                        if (typeof attr === 'string'){
                            if (value >= 0){
                                label.setAttribute(attr, 'positive');
                            }else{
                                label.setAttribute(attr, 'negative');
                            }
                        }
                    }
                }
            },
            updateAllValues : function(){
                var labels = pure.nodes.select.all('*' +    '[data-engine-manaminimal-value]' +
                                                            '[data-engine-manaminimal-object]');
                if (labels !== null){
                    Array.prototype.forEach.call(
                        labels,
                        function(label, index, sources){
                            var current = parseInt(label.innerHTML, 10),
                                attr    = label.getAttribute('data-engine-manaminimal-value-attribute');
                            if (attr !== null){
                                if (current >= 0){
                                    label.setAttribute(attr, 'positive');
                                }else{
                                    label.setAttribute(attr, 'negative');
                                }
                            }
                        }
                    );
                }
            }
        },
        marks       : {
            add : function(mark, object_id, object_type, field, value){
                var template    = pure.system.getInstanceByPath('pure.mana.icon.templates.B'),
                    icon        = document.createElement('DIV');
                if (template !== null){
                    template    = template.replace(/\[object_id\]/gi,   object_id   );
                    template    = template.replace(/\[object\]/gi,      object_type );
                    template    = template.replace(/\[field\]/gi,       field       );
                    template    = template.replace(/\[value\]/gi,       value       );
                    icon.innerHTML = template;
                    pure.nodes.move.insertChildsBefore(mark, icon.childNodes);
                    mark.parentNode.removeChild(mark);
                    pure.mana.icon.B.change.updateValues(object_id, object_type, null);
                }
            },
            addPackage : function(data, field){
                for(var key in data){
                    (function(fields, field){
                        var mark = pure.nodes.select.first('*' +    '[data-engine-manaminimal-element="mark"]' +
                                                                    '[data-engine-manaminimal-object_id="'  + fields.object_id      + '"]' +
                                                                    '[data-engine-manaminimal-object="'     + fields.object_type    + '"]' +
                                                                    '[data-engine-manaminimal-field="'      + field                 + '"]');
                        if (mark !== null){
                            pure.mana.icon.B.marks.add(
                                mark,
                                fields.object_id,
                                fields.object_type,
                                field,
                                (fields.plus - fields.minus)
                            );
                        }
                    }(data[key], field));
                }
                pure.mana.icon.B.init.buttons();
            }
        },
        add         : {
            attached    : false,
            init        : function(){
                if (pure.mana.icon.B.add.attached === false){
                    pure.mana.icon.B.add.attached = true;
                    pure.appevents.Actions.listen(
                        'pure.mana.icons',
                        'new',
                        pure.mana.icon.B.add.proceed,
                        'pure.mana.icons.new.B'
                    );
                }
            },
            /*
             * params = {
             *      object  : string,
             *      IDs     : array of {object_id : integer, user_id: integer},
             *      field   : string
             * }
             * */
            proceed     : function(params){
                var object_type = (typeof params.object === 'string'    ? params.object : null),
                    IDs         = (typeof params.IDs    !== 'undefined' ? params.IDs    : null),
                    field       = (typeof params.field  !== 'undefined' ? params.field  : null),
                    user_ids    = [],
                    object_ids  = [];
                if (object_type !== null && IDs !== null){
                    if (IDs instanceof Array !== false){
                        for(var index = IDs.length - 1; index >= 0; index -= 1){
                            if (typeof IDs[index].object_id === 'number' && typeof IDs[index].user_id === 'number'){
                                if (IDs[index].object_id <= 0 || IDs[index].user_id <= 0){
                                    IDs.splice(index, 1);
                                }else{
                                    user_ids.   push(IDs[index].user_id     );
                                    object_ids. push(IDs[index].object_id   );
                                }
                            }else{
                                IDs.splice(index, 1);
                            }
                        }
                        if (IDs.length > 0){
                            pure.mana.icon.B.add.send(object_type, user_ids, object_ids, field);
                        }
                    }
                }
            },
            send : function(object_type, user_ids, object_ids, field){
                var request     = pure.system.getInstanceByPath('pure.mana.icons.configuration.requests.get'),
                    destination = pure.system.getInstanceByPath('pure.mana.icons.configuration.requestURL');
                if (request !== null && destination !== null){
                    pure.tools.request.sendWithFields({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.mana.icon.B.add.receive(id_request, response, field);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.mana.icon.B.add.error(event, id_request);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.mana.icon.B.add.error(event, id_request);
                            }
                        },
                        {
                            user_ids    : user_ids,
                            object      : object_type,
                            object_ids  : object_ids
                        }
                    );
                }
            },
            receive : function(id_request, response, field){
                var data = null;
                try{
                    data = JSON.parse(response);
                    if (typeof data === 'object'){
                        pure.mana.icon.B.marks.addPackage(data, field);
                    }
                }catch(e){}
            },
            error   : function(event, id_request){
                //Error
            }
        },
        hotUpdate   : {
            inited      : false,
            init        : function(){
                if (pure.mana.icon.B.hotUpdate.inited === false){
                    pure.appevents.Actions.listen(
                        'webSocketServerEvents',
                        'mana_update',
                        pure.mana.icon.B.hotUpdate.processing,
                        'post_mana_update_handle_B'
                    );
                    pure.mana.icon.B.hotUpdate.inited = true;
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
                        if (typeof params.parameters.object_type    !== 'undefined' && typeof params.parameters.object_id   !== 'undefined' &&
                            typeof params.parameters.plus           !== 'undefined' && typeof params.parameters.minus       !== 'undefined'){
                            pure.mana.icon.B.change.replaceValues(
                                params.parameters.object_id,
                                params.parameters.object_type,
                                (parseInt(params.parameters.plus, 10) - parseInt(params.parameters.minus, 10))
                            );
                        }
                    }
                }
            }
        },
        progress    : {
            isBusy      : function(object_id, object_type){
                return pure.templates.progressbar.A.wrapper.isBusy(object_type + object_id);
            },
            busy        : function(object_id, object_type){
                pure.templates.progressbar.A.wrapper.busy(
                    object_type + object_id,
                    '*[data-engine-manaminimal-container="' + object_id + '"][data-engine-manaminimal-object="' + object_type + '"]',
                    'background:rgba(255,255,255,0.8);'
                );
            },
            clear       : function(object_id, object_type){
                pure.templates.progressbar.A.wrapper.clear(object_type + object_id);
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
    pure.system.start.add(pure.mana.icon.B.init.all);
}());