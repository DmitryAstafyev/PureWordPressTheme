(function () {
    if (typeof window.pure                      !== "object") { window.pure                     = {}; }
    if (typeof window.pure.elements             !== "object") { window.pure.elements            = {}; }
    if (typeof window.pure.elements.fileloader  !== "object") { window.pure.elements.fileloader = {}; }
    "use strict";
    window.pure.elements.fileloader.A = {
        init        : {
            titles      : function(){
                var instances = pure.nodes.select.all('*[data-fileloader-engine-title]');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(title, _index, source){
                            var object_id   = title.getAttribute('data-fileloader-engine-title'),
                                object_type = title.getAttribute('data-fileloader-engine-object_type'),
                                items       = pure.nodes.select.all('*[data-fileloader-engine-item="' + object_id + '"][data-fileloader-engine-object_type="' + object_type + '"]');
                            if (items !== null){
                                if (items.length === 0){
                                    title.style.display = 'none';
                                }else{
                                    title.style.display = '';
                                }
                            }
                        }
                    );
                }
            },
            upload      : function(){
                var instances = pure.nodes.select.all('*[data-fileloader-engine-upload_button]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(button, _index, source){
                            var object_id   = button.getAttribute('data-fileloader-engine-upload_button'),
                                object_type = button.getAttribute('data-fileloader-engine-object_type');
                            if (object_id !== null && object_type !== null){
                                pure.events.add(
                                    button,
                                    'click',
                                    function(){
                                        pure.elements.fileloader.A.upload.onClick(object_id, object_type);
                                    }
                                );
                            }
                            button.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            },
            remove      : function(){
                var instances = pure.nodes.select.all('*[data-fileloader-engine-remove-button]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(button, _index, source){
                            var attachment_id   = button.getAttribute('data-fileloader-engine-remove-button'),
                                object_id       = button.getAttribute('data-fileloader-engine-object_id'    ),
                                object_type     = button.getAttribute('data-fileloader-engine-object_type'),
                                url             = button.getAttribute('data-fileloader-engine-url'          );
                            if (attachment_id !== null && object_id !== null && object_type !== null &&url !== null){
                                if (url !== '[url]'){
                                    pure.events.add(
                                        button,
                                        'click',
                                        function(){
                                            pure.elements.fileloader.A.remove.send(object_id, object_type, attachment_id, url);
                                        }
                                    );
                                    button.setAttribute('data-element-inited', 'true');
                                }
                            }
                        }
                    );
                }
            },
            templates   : function(){
                var templates = {
                    attachment : pure.system.getInstanceByPath('pure.elements.fileloader.templates.attachment')
                };
                if (pure.tools.objects.isValueIn(templates, null) === false){
                    pure.elements.fileloader.templates.attachment = pure.convertor.UTF8.decode(
                        pure.convertor.BASE64.decode(pure.elements.fileloader.templates.attachment)
                    );
                }
            },
            all         : function(){
                pure.elements.fileloader.A.init.        titles      ();
                pure.elements.fileloader.A.init.        upload      ();
                pure.elements.fileloader.A.init.        remove      ();
                pure.elements.fileloader.A.init.        templates   ();
                pure.elements.fileloader.A.upload.input.init        ();
                pure.elements.fileloader.A.events.      init        ();
                pure.elements.fileloader.A.hotUpdate.   init        ();
            }
        },
        titles      : {
            update : function(object_id, object_type){
                var title   = pure.nodes.select.first('*[data-fileloader-engine-title="' + object_id + '"][data-fileloader-engine-object_type="' + object_type + '"]'),
                    items   = pure.nodes.select.all('*[data-fileloader-engine-item="' + object_id + '"][data-fileloader-engine-object_type="' + object_type + '"]');
                if (items !== null && title !== null){
                    if (items.length === 0){
                        title.style.display = 'none';
                    }else{
                        title.style.display = '';
                    }
                }
            }
        },
        progress    : {
            data    : {},
            isBusy  : function(object_id, attachment_id){
                var data = pure.elements.fileloader.A.progress.data;
                return (typeof data[object_id + attachment_id] !== 'undefined' ? true : false);
            },
            busy    : function(object_id, attachment_id){
                var data = pure.elements.fileloader.A.progress.data,
                    node = pure.nodes.select.first('*[data-fileloader-engine-item="' + object_id + '"][data-fileloader-engine-attachment-id="' + attachment_id + '"]');
                if (typeof data[object_id + attachment_id] === 'undefined' && node !== null){
                    data[object_id + attachment_id] = pure.templates.progressbar.B.show(node, 'background:rgba(255,255,255,0.8);');
                }
            },
            clear   : function(object_id, attachment_id){
                var data = pure.elements.fileloader.A.progress.data;
                if (typeof data[object_id + attachment_id] !== 'undefined'){
                    pure.templates.progressbar.B.hide(data[object_id + attachment_id]);
                    data[object_id + attachment_id] = null;
                    delete data[object_id + attachment_id];
                }
            }
        },
        upload      : {
            input       : {
                file    : null,
                current : null,
                init    : function(){
                    var template    = pure.system.getInstanceByPath('pure.elements.fileloader.nodes.input'),
                        node        = document.createElement('DIV'),
                        file        = null;
                    if (template !== null && pure.elements.fileloader.A.upload.input.file === null){
                        node.innerHTML  = template;
                        pure.nodes.move.appendChildsTo(document.body, node.childNodes);
                        file            = pure.nodes.select.first('input[data-fileloader-engine-input]');
                        if (file !== null){
                            pure.elements.fileloader.A.upload.input.file = file;
                            pure.events.add(
                                file,
                                'change',
                                function(event){
                                    pure.elements.fileloader.A.upload.onChange(event, file);
                                }
                            );
                        }
                    }
                },
                set     : function(object_id, object_type){
                    pure.elements.fileloader.A.upload.input.current = {
                        object_id   : object_id,
                        object_type : object_type
                    };
                },
                reset   : function(){
                    pure.elements.fileloader.A.upload.input.current = null;
                },
                call    : function(){
                    if (pure.elements.fileloader.A.upload.input.file !== null){
                        pure.events.call(
                            pure.elements.fileloader.A.upload.input.file,
                            'click'
                        );
                    }
                }
            },
            onClick     : function(object_id, object_type){
                var parent          = pure.nodes.select.first('*[data-fileloader-engine-place="' + object_id + '"][data-fileloader-engine-object_type="' + object_type + '"]'),
                    attachment      = pure.system.getInstanceByPath('pure.elements.fileloader.templates.attachment');
                if (parent !== null && attachment !== null){
                    pure.elements.fileloader.A.upload.input.set(object_id, object_type);
                    pure.elements.fileloader.A.upload.input.call();
                }
            },
            onChange    : function(event, file){
                var current = pure.elements.fileloader.A.upload.input.current;
                if (current !== null){
                    pure.elements.fileloader.A.upload.input.reset();
                    if (typeof file.files !== 'undefined' && typeof file.value === 'string') {
                        if (file.files.length > 0) {
                            for(var index = 0, maxIndex = file.files.length; index < maxIndex; index += 1){
                                pure.elements.fileloader.A.upload.add(
                                    current.object_id,
                                    current.object_type,
                                    file.files[index]
                                );
                            }
                        }
                    }
                }
            },
            add : function(object_id, object_type, file){
                var parent          = pure.nodes.select.first('*[data-fileloader-engine-place="' + object_id + '"][data-fileloader-engine-object_type="' + object_type + '"]'),
                    attachment      = pure.system.getInstanceByPath('pure.elements.fileloader.templates.attachment'),
                    node            = document.createElement('DIV'),
                    html_file_name  = document.createElement('P'),
                    attachment_id   = '[' + pure.tools.IDs.get() + ']',
                    maxSize         = pure.system.getInstanceByPath('pure.elements.fileloader.configuration.maxSize'),
                    item            = null;
                if (parent !== null && attachment !== null && maxSize !== null){
                    if (file.size > maxSize){
                        pure.elements.fileloader.A.dialogs.info(
                            'Deny to attach file',
                            'Sorry, but file is too large. Maximal allowed size of file is ' + maxSize + ' bytes.',
                            null,
                            'too_big'
                        );
                    }else{
                        html_file_name              = file.name.replace(/\W/gi, '_');
                        attachment                  = attachment.replace(/\[object_id\]/gi,         object_id                   );
                        attachment                  = attachment.replace(/\[object_type\]/gi,       object_type                 );
                        attachment                  = attachment.replace(/\[attachment_id\]/gi,     attachment_id               );
                        attachment                  = attachment.replace(/\[file_name\]/gi,         '[' + html_file_name + ']'  );
                        node.innerHTML              = attachment;
                        pure.nodes.move.appendChildsTo(parent, node.childNodes);
                        item                        = pure.nodes.select.first('*[data-fileloader-engine-item="' + object_id + '"][data-fileloader-engine-attachment-id="' + attachment_id + '"]');
                        pure.elements.fileloader.A.progress.busy(object_id, attachment_id);
                        pure.elements.fileloader.A.upload.request.send(object_id, object_type, attachment_id, file, html_file_name);
                    }
                }
            },
            request : {
                send : function(object_id, object_type, attachment_id, file, html_file_name){
                    var destination = pure.system.getInstanceByPath('pure.elements.fileloader.requests.direction'   ),
                        command     = pure.system.getInstanceByPath('pure.elements.fileloader.requests.commands.add');
                    if (destination !== null && command !== null){
                        pure.components.uploader.module.upload(
                            file,
                            destination,
                            {
                                ready : function(params){
                                    pure.elements.fileloader.A.upload.request.ready(params, object_id, object_type, attachment_id, html_file_name);
                                },
                                error : function(params){
                                    pure.elements.fileloader.A.upload.request.error(params, object_id, object_type, attachment_id);
                                },
                                timeout : function(params){
                                    pure.elements.fileloader.A.upload.request.error(params, object_id, object_type, attachment_id);
                                }
                            },
                            null,
                            'file',
                            [
                                { name:'command',       value: command      },
                                { name:'object_id',     value: object_id    },
                                { name:'object_type',   value: object_type  }
                            ]
                        );
                    }
                },
                ready : function(params, object_id, object_type, attachment_id, html_file_name){
                    var item    = pure.nodes.select.first('*[data-fileloader-engine-item="' + object_id + '"][data-fileloader-engine-attachment-id="' + attachment_id + '"]'),
                        data    = null,
                        regexp  = null;
                    pure.elements.fileloader.A.progress.clear(object_id, attachment_id);
                    if (item !== null){
                        switch (params.response){
                            case 'error':
                                item.parentNode.removeChild(item);
                                break;
                            case 'count_in_object':
                                item.parentNode.removeChild(item);
                                pure.elements.fileloader.A.dialogs.info(
                                    'Deny to attach file',
                                    'Sorry, but you cannot attach more files than you attached.',
                                    null,
                                    'count_in_object'
                                );
                                break;
                            case 'count_in_month':
                                item.parentNode.removeChild(item);
                                pure.elements.fileloader.A.dialogs.info(
                                    'Deny to attach file',
                                    'Sorry, but you attached a lot of files during this month',
                                    null,
                                    'count_in_month'
                                );
                                break;
                            case 'too_big':
                                item.parentNode.removeChild(item);
                                pure.elements.fileloader.A.dialogs.info(
                                    'Deny to attach file',
                                    'Sorry, but file is too large.',
                                    null,
                                    'too_big'
                                );
                                break;
                            default :
                                try{
                                    data = JSON.parse(params.response);
                                    if (typeof data.url === 'string' && typeof data.file_name === 'string' && typeof data.message === 'string'){
                                        if (data.message === 'success'){
                                            item.innerHTML      = item.innerHTML.replace(/\[url\]/gi,       data.url                                        );
                                            item.innerHTML      = item.innerHTML.replace(new RegExp('\\['+ html_file_name + '\\]', 'gi'), data.file_name    );
                                            item.innerHTML      = item.innerHTML.replace(attachment_id,     data.id                                         );
                                            item.setAttribute('data-fileloader-engine-attachment-id', data.id);
                                            pure.elements.fileloader.A.permission.check(object_id, object_type, data.id, data.user_id);
                                            pure.elements.fileloader.A.init.remove();
                                            pure.elements.fileloader.A.titles.update(object_id, object_type);
                                            pure.elements.fileloader.A.hotUpdate.call();
                                        }
                                    }
                                }catch (e){
                                    item.parentNode.removeChild(item);
                                }
                                break;
                        }
                    }
                },
                error : function(params, object_id, object_type, attachment_id){
                    var item = pure.nodes.select.first('*[data-fileloader-engine-item="' + object_id + '"][data-fileloader-engine-attachment-id="' + attachment_id + '"]');
                    pure.elements.fileloader.A.progress.clear(object_id, attachment_id);
                    if (item !== null){
                        item.parentNode.removeChild(item);
                    }
                }
            }
        },
        permission  : {
            check : function(object_id, object_type, attachment_id, user_id){
                var user    = pure.system.getInstanceByPath('pure.elements.fileloader.configuration.user_id'),
                    button  = pure.nodes.select.first('*[data-fileloader-engine-object_id="' + object_id + '"][data-fileloader-engine-object_type="' + object_type + '"][data-fileloader-engine-remove-button="' + attachment_id + '"]');
                if (button !== null){
                    if (user !== null){
                        if (parseInt(user, 10) !== parseInt(user_id, 10)){
                            button.parentNode.removeChild(button);
                        }
                    }else{
                        button.parentNode.removeChild(button);
                    }
                }
            }
        },
        remove      : {
            send : function(object_id, object_type, attachment_id, url){
                var destination = pure.system.getInstanceByPath('pure.elements.fileloader.requests.direction'   ),
                    request     = pure.system.getInstanceByPath('pure.elements.fileloader.requests.remove'      );
                if (destination !== null && request !== null) {
                    if (pure.elements.fileloader.A.progress.isBusy(object_id, attachment_id) === false){
                        pure.elements.fileloader.A.progress.busy(object_id, attachment_id);
                        pure.tools.request.sendWithFields({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.elements.fileloader.A.remove.receive(id_request, response, object_id, object_type, attachment_id);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.elements.fileloader.A.remove.error(event, id_request, object_id, object_type, attachment_id);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.elements.fileloader.A.remove.error(event, id_request, object_id, object_type, attachment_id);
                                }
                            },
                            {
                                url         : url,
                                object_id   : object_id,
                                object_type : object_type
                            }
                        );
                    }
                }
            },
            receive : function(id_request, response, object_id, object_type, attachment_id){
                var item = pure.nodes.select.first('*[data-fileloader-engine-item="' + object_id + '"][data-fileloader-engine-attachment-id="' + attachment_id + '"]');
                pure.elements.fileloader.A.progress.clear(object_id, attachment_id);
                if (response === 'success'){
                    if (item !== null){
                        item.parentNode.removeChild(item);
                        pure.elements.fileloader.A.titles.update(object_id, object_type);
                        pure.elements.fileloader.A.hotUpdate.call();
                    }
                }
            },
            error   : function(event, id_request, object_id, object_type, attachment_id){
                pure.elements.fileloader.A.progress.clear(object_id, attachment_id);
            }
        },
        request     : {
            send    : function(object_ids, object_types){
                var destination = pure.system.getInstanceByPath('pure.elements.fileloader.requests.direction'   ),
                    request     = pure.system.getInstanceByPath('pure.elements.fileloader.requests.request'     );
                if (destination !== null && request !== null) {
                    pure.tools.request.sendWithFields({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.elements.fileloader.A.request.receive(id_request, response);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.elements.fileloader.A.request.error(event, id_request);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.elements.fileloader.A.request.error(event, id_request);
                            }
                        },
                        {
                            object_ids   : object_ids.join(','),
                            object_types : object_types.join(',')
                        }
                    );
                }
            },
            receive : function(id_request, response){
                function processing(data){
                    if (data instanceof Array){
                        for(var index = data.length - 1; index >= 0; index -= 1){
                            if (typeof data[index].id !== 'undefined'){
                                pure.elements.fileloader.A.request.add(data[index]);
                            }else{
                                processing(data[index]);
                            }
                        }
                    }
                };
                var data = null;
                if (response !== 'error'){
                    try{
                        data = JSON.parse(response);
                        processing(data);
                    }catch (e){

                    }
                }
            },
            error   : function(event, id_request){

            },
            add     : function(attachment){
                var parent      = pure.nodes.select.first('*[data-fileloader-engine-place="' + attachment.object_id + '"][data-fileloader-engine-object_type="' + attachment.object_type + '"]'),
                    template    = pure.system.getInstanceByPath('pure.elements.fileloader.templates.attachment'),
                    node        = document.createElement('DIV');
                if (parent !== null && template !== null){
                    template        = template.replace(/\[object_id\]/gi,         attachment.object_id      );
                    template        = template.replace(/\[object_type\]/gi,       attachment.object_type    );
                    template        = template.replace(/\[attachment_id\]/gi,     attachment.id             );
                    template        = template.replace(/\[file_name\]/gi,         attachment.file_name      );
                    template        = template.replace(/\[url\]/gi,               attachment.url            );
                    node.innerHTML  = template;
                    pure.nodes.move.appendChildsTo(parent, node.childNodes);
                    pure.elements.fileloader.A.permission.check(attachment.object_id, attachment.object_type, attachment.id, attachment.user_id);
                    pure.elements.fileloader.A.init.remove();
                    pure.elements.fileloader.A.titles.update(attachment.object_id, attachment.object_type);
                }
            }
        },
        events      : {
            init    : function(){
                pure.appevents.Actions.listen(
                    'pure.fileloader',
                    'update.upload.button',
                    pure.elements.fileloader.A.events.upload,
                    'pure.elements.fileloader.A.events.upload'
                );
                pure.appevents.Actions.listen(
                    'pure.fileloader',
                    'update.titles',
                    pure.elements.fileloader.A.events.titles,
                    'pure.elements.fileloader.A.events.titles'
                );
                pure.appevents.Actions.listen(
                    'pure.fileloader',
                    'request.new.attachments',
                    pure.elements.fileloader.A.events.request,
                    'pure.elements.fileloader.A.events.request'
                );
            },
            upload  : function(){
                pure.elements.fileloader.A.init.upload();
            },
            titles  : function(){
                pure.elements.fileloader.A.init.titles();
            },
            request : function(params){
                if (typeof params.object_ids !== 'undefined' && typeof params.object_types !== 'undefined'){
                    if (params.object_ids instanceof Array && params.object_types instanceof Array ){
                        if (params.object_ids.length === params.object_types.length && params.object_types.length > 0){
                            pure.elements.fileloader.A.request.send(params.object_ids, params.object_types);
                        }
                    }
                }
            }
        },
        hotUpdate   : {
            inited      : false,
            init        : function(){
                if (pure.elements.fileloader.A.hotUpdate.inited === false){
                    pure.appevents.Actions.listen(
                        'webSocketServerEvents',
                        'post_attachments_update',
                        pure.elements.fileloader.A.hotUpdate.processing,
                        'post_attachments_update_handle'
                    );
                    pure.elements.fileloader.A.hotUpdate.inited = true;
                }
            },
            call        : function(){
                //Server notification
                pure.appevents.Actions.call('webSocketsServer','wakeup', null, null);
            },
            processing  : function(params){
                var parameters  = (typeof params.parameters === 'object' ? params.parameters : null),
                    node        = null;
                if (parameters !== null){
                    node = pure.nodes.select.all('*' +  '[data-fileloader-engine-item="' + parameters.attachment.object_id + '"]' +
                                                        '[data-fileloader-engine-object_type="' + parameters.attachment.object_type + '"]' +
                                                        '[data-fileloader-engine-attachment-id="' + parameters.attachment.id + '"]');
                    switch (parameters.action){
                        case 'new':
                            if (node.length === 0){
                                pure.elements.fileloader.A.request.add(parameters.attachment);
                            }
                            break;
                        case 'remove':
                            if (node.length > 0){
                                for (var index = node.length - 1; index >= 0; index -= 1){
                                    node[index].parentNode.removeChild(node[index]);
                                }
                                pure.elements.fileloader.A.titles.update(parameters.attachment.object_id, parameters.attachment.object_type);
                            }
                            break;
                    }
                }
            }
        },
        dialogs     : {
            type : null,
            info : function (title, message, handle, type) {
                if (pure.elements.fileloader.A.dialogs.type !== type){
                    pure.elements.fileloader.A.dialogs.type = type;
                    pure.components.dialogs.B.open({
                        title       : title,
                        innerHTML   : '<p data-post-element-type="Pure.Posts.Members.A.Dialog">' + message + '</p>',
                        width       : 70,
                        parent      : document.body,
                        buttons     : [
                            {
                                title       : 'OK',
                                handle      : function(){
                                    pure.elements.fileloader.A.dialogs.type = null;
                                    if (handle !== null){
                                        pure.system.runHandle(handle, null, 'pure.elements.fileloader.A.dialogs.info', this);
                                    }
                                },
                                closeAfter  : true
                            }
                        ]
                    });
                }
            }
        }
    };
    pure.system.start.add(pure.elements.fileloader.A.init.all);
}());