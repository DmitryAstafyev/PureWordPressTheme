(function () {
    if (typeof window.pure                              !== "object") { window.pure                             = {}; }
    if (typeof window.pure.buddypress                   !== "object") { window.pure.buddypress                  = {}; }
    if (typeof window.pure.buddypress.personalsettings  !== "object") { window.pure.buddypress.personalsettings = {}; }
    "use strict";
    window.pure.buddypress.personalsettings.A = {
        init        : function () {
            window.pure.wordpress.media.images.             init();
            pure.buddypress.personalsettings.A.initialize.  init();
            pure.buddypress.personalsettings.A.loaded.      all();
            pure.buddypress.personalsettings.A.backgrounds. init();
            pure.buddypress.personalsettings.A.privacy.     init();
            pure.buddypress.personalsettings.A.email.       init();
            pure.buddypress.personalsettings.A.password.    init();
        },
        initialize  : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-engine-element="PersonalSettings.Container"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        (function(node){
                            function attachEvents(eventType, id){
                                var callers = pure.nodes.select.all('*[data-engine-personalsettings-' + eventType + '-caller="' + id + '"]:not([data-type-element-inited])');
                                if (callers !== null){
                                    for(var index = callers.length - 1; index >= 0; index -= 1){
                                        if (eventType === 'open'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.personalsettings.A.render.show(id);
                                            });
                                        }
                                        if (eventType === 'close'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.personalsettings.A.render.hide(id);
                                            });
                                        }
                                        callers[index].setAttribute('data-type-element-inited', 'true');
                                    }
                                }
                            };
                            var id      = node.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.buddypress.personalsettings.A.render.hide          (id);
                                pure.buddypress.personalsettings.A.render.orderOnTop    (id);
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
                var instances = pure.nodes.select.all('*[data-engine-element="PersonalSettings.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        document.body.appendChild(instances[index]);
                    }
                }
            },
            show        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="PersonalSettings.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    pure.buddypress.personalsettings.A.render.orderOnTop (id);
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = '';
                    }
                    pure.buddypress.personalsettings.A.password.clear(id);
                    pure.buddypress.personalsettings.A.email.clear(id, false);
                }
            },
            hide        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="PersonalSettings.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = 'none';
                    }
                }
            }
        },
        images      : {
            init    : function(type){
                var instances = pure.nodes.select.all('input[data-field-type="Image.' + type + '.Image"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance, type){
                            var id      = instance.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.events.add(
                                    instance,
                                    'change',
                                    function(event){
                                        pure.buddypress.personalsettings.A.backgrounds.event.onChange(event, id, instance, type);
                                    }
                                );
                            }
                            instance.setAttribute('data-type-element-inited', 'true');
                        }(instances[index], type));
                    }
                }
            },
            reset   : function(type, id){
                var image       = pure.nodes.select.first('*[data-field-type="Image.' + type + '.Image"][data-engine-element-id="' + id + '"]'),
                    _image      = document.createElement('IMG');
                if (image !== null){
                    pure.nodes.attributes.set(_image, pure.nodes.attributes.get(image));
                    image.parentNode.insertBefore(_image, image);
                    image.parentNode.removeChild(image);
                    image = null;
                    return _image;
                }
                return null;
            },
            preview : {
                get isPossible(){
                    return (window.FileReader === undefined ? false : true);
                },
                make        : function(file, image, onFinish){
                    var fileReader = null;
                    if (window.FileReader !== undefined){
                        pure.buddypress.personalsettings.A.images.preview.savePrevious(image);
                        fileReader = new FileReader();
                        pure.events.add(fileReader, 'load',
                            function(event){
                                var _image  = new Image();
                                _image.src  = event.target.result;
                                image.src   = _image.src;
                                pure.system.runHandle(onFinish, null, '', this);
                            });
                        fileReader.readAsDataURL(file);
                        return true;
                    }
                    return false;
                },
                replaceSRC  : function(url, image, onLoad){
                    pure.buddypress.personalsettings.A.images.preview.savePrevious(image);
                    if (onLoad !== null){
                        pure.events.add(
                            image,
                            'load',
                            onLoad
                        );
                    }
                    if (url === ''){
                        if (image.getAttribute('data-src-noimage') !== ''){
                            image.src = image.getAttribute('data-src-noimage');
                            return true;
                        }
                    }
                    image.src = url;
                },
                savePrevious    : function(image){
                    image.setAttribute('data-src-previous', image.src);
                },
                restore         : function(image){
                    var src = image.getAttribute('data-src-previous');
                    if (src !== null && src !== ''){
                        image.src = src;
                    }
                }
            },
            crop    : {
                attach      : function(id, image, type){
                    var ratio = (type === 'avatar' ? 1 : 3.5);
                    pure.components.crop.module.methods.destroy(id);
                    pure.components.crop.module.methods.attach({
                        target      : image,
                        selection   : {
                            x:0,
                            y:0,
                            w:100,
                            h:100
                        },
                        ratio       : ratio,
                        id          : id
                    });
                },
                destroy     : function(id){
                    pure.components.crop.module.methods.destroy(id);
                },
                coordinates : function(id){
                    return pure.components.crop.module.methods.getSelection(id);
                }
            }
        },
        loaded      : {
            all     : function(){
                pure.buddypress.personalsettings.A.loaded.init('Avatar' );
                pure.buddypress.personalsettings.A.loaded.init('Title'  );
            },
            init    : function(type){
                var instances = pure.nodes.select.all('input[data-field-type="Image.' + type + '.File"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance){
                            var id      = instance.getAttribute('data-engine-element-id'),
                                nodes   = null;
                            if (id !== null && id !== ''){
                                nodes = {
                                    file    : instance,
                                    image   : pure.nodes.select.first('*[data-field-type="Image.' + type + '.Image"][data-engine-element-id="' + id + '"]'),
                                    load    : pure.nodes.select.first('*[data-field-type="Image.' + type + '.Button.Load"][data-engine-element-id="' + id + '"]'),
                                    save    : pure.nodes.select.first('*[data-field-type="Image.' + type + '.Button.Save"][data-engine-element-id="' + id + '"]'),
                                    remove  : pure.nodes.select.first('*[data-field-type="Image.' + type + '.Button.Remove"][data-engine-element-id="' + id + '"]'),
                                    field   : type.toLowerCase(),
                                    type    : type
                                };
                                if (pure.tools.objects.isValueIn(nodes, null) === false){
                                    //Attach events
                                    pure.events.add(
                                        nodes.load,
                                        'click',
                                        function(event){
                                            pure.buddypress.personalsettings.A.loaded.load.onClick(event, id, nodes);
                                        }
                                    );
                                    pure.events.add(
                                        nodes.remove,
                                        'click',
                                        function(event){
                                            pure.buddypress.personalsettings.A.loaded.remove.onClick(event, id, nodes);
                                        }
                                    );
                                    pure.events.add(
                                        nodes.file,
                                        'change',
                                        function(event){
                                            pure.buddypress.personalsettings.A.loaded.file.onChange(event, id, nodes);
                                        }
                                    );
                                    pure.events.add(
                                        nodes.save,
                                        'click',
                                        function(event){
                                            pure.buddypress.personalsettings.A.loaded.save.onClick(event, id, nodes);
                                        }
                                    );
                                    pure.buddypress.personalsettings.A.loaded.buttons.hide(nodes.save);
                                }
                            }
                            instance.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            },
            load    : {
                onClick : function(event, id, nodes){
                    pure.events.call(nodes.file, 'click');
                }
            },
            remove  : {
                onClick : function(event, id, nodes){
                    pure.buddypress.personalsettings.A.loaded.remove.send(id, nodes);
                },
                send    : function(id, nodes){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.request.' + nodes.field + '.remove'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.destination'),
                        isCrop      = pure.buddypress.personalsettings.A.images.crop.coordinates(id);
                    if (request !== null && destination !== null){
                        if (isCrop === null){
                            if (pure.buddypress.personalsettings.A.progress.global.isBusy(id) === false){
                                pure.buddypress.personalsettings.A.loaded.buttons.hide(nodes.save);
                                pure.buddypress.personalsettings.A.loaded.buttons.show(nodes.load);
                                pure.buddypress.personalsettings.A.progress.global.busy(id);
                                pure.tools.request.send({
                                    type        : 'POST',
                                    url         : destination,
                                    request     : request,
                                    onrecieve   : function (id_request, response) {
                                        pure.buddypress.personalsettings.A.loaded.remove.received(id_request, response, id, nodes);
                                    },
                                    onreaction  : null,
                                    onerror     : function (event, id_request) {
                                        pure.buddypress.personalsettings.A.loaded.remove.error(event, id_request, id, nodes);
                                    },
                                    ontimeout   : function (event, id_request) {
                                        pure.buddypress.personalsettings.A.loaded.remove.error(event, id_request, id, nodes);
                                    }
                                });
                            }
                        }else{
                            pure.buddypress.personalsettings.A.images.crop.     destroy (id);
                            pure.buddypress.personalsettings.A.images.preview.  restore (nodes.image);
                            pure.buddypress.personalsettings.A.loaded.buttons.  show    (nodes.load);
                            pure.buddypress.personalsettings.A.loaded.buttons.  hide    (nodes.save);
                        }
                    }
                },
                received    : function(id_request, response, id, nodes){
                    var result  = null,
                        message = pure.buddypress.personalsettings.A.dialogs.info;
                    try{
                        result = JSON.parse(response);
                    }catch (e){
                        //Just continue
                    }
                    if (typeof result === 'object'){
                        switch (result.message){
                            case 'success':
                                pure.buddypress.personalsettings.A.images.preview.replaceSRC(result.url, nodes.image, null);
                                break;
                            case 'fail':
                                message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                                break;
                            case 'no access':
                                message('Fail operation', 'Sorry, but you have no access to do it. Try login again.');
                                break;
                        }
                    }else{
                        message('Fail operation', 'Sorry, but server give an incorrect response. Please, try later.');
                    }
                    pure.buddypress.personalsettings.A.progress.global.free(id);
                },
                error       : function(event, id_request, id, nodes){
                    var message = pure.buddypress.personalsettings.A.dialogs.info;
                    message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                    pure.buddypress.personalsettings.A.progress.global.free(id);
                }
            },
            save    : {
                onClick : function(event, id, nodes){
                    pure.buddypress.personalsettings.A.loaded.file.save.send(id, nodes);
                }
            },
            buttons : {
                show : function(node){
                    node.style.display = '';
                },
                hide : function(node){
                    node.style.display = 'none';
                }
            },
            file    : {
                onChange    : function(event, id, nodes){
                    var message     = pure.buddypress.personalsettings.A.dialogs.info,
                        ext         = null,
                        cropHandle  = null;
                    if (pure.buddypress.personalsettings.A.progress.global.isBusy(id) === false){
                        if (typeof nodes.file.files !== 'undefined' && typeof nodes.file.value === 'string'){
                            if (nodes.file.files.length === 1){
                                ext = (nodes.file.value.match(/\.([^\.]+)$/)[1]).toLowerCase();
                                if (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg'){
                                    cropHandle  = function(){
                                        pure.buddypress.personalsettings.A.loaded.buttons.hide(nodes.load);
                                        pure.buddypress.personalsettings.A.loaded.buttons.show(nodes.save);
                                        pure.buddypress.personalsettings.A.images.crop.     attach  (id, nodes.image, nodes.field);
                                        pure.buddypress.personalsettings.A.progress.global. free    (id);
                                    };
                                    pure.buddypress.personalsettings.A.progress.global. busy(id);
                                    if (pure.buddypress.personalsettings.A.images.preview.isPossible !== false){
                                        //Load image locally and crop
                                        nodes.image = pure.buddypress.personalsettings.A.images.reset(nodes.type, id);
                                        pure.buddypress.personalsettings.A.images.preview.  make(
                                            nodes.file.files[0],
                                            nodes.image,
                                            cropHandle
                                        );
                                        return true;
                                    }else{
                                        //Upload image on server before crop
                                        pure.buddypress.personalsettings.A.loaded.file.preload.send(
                                            nodes.file.files[0],
                                            id,
                                            nodes,
                                            cropHandle
                                        );
                                        return true;
                                    }
                                }
                                message('You cannot do that', 'Sorry you can use only GIF, PNG, JPEG or JPG.');
                                return false;
                            }
                            message('You cannot do that', 'Sorry you can choose only one file.');
                            return false;
                        }
                    }
                },
                preload     : {
                    send        : function(file, id, nodes, cropHandle){
                        var command     = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.commands.' + nodes.field),
                            destination = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.destination'),
                            user_id     = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.user_id');
                        if (command !== null && destination !== null && user_id !== null){
                            pure.buddypress.personalsettings.A.loaded.buttons.hide(nodes.load);
                            pure.buddypress.personalsettings.A.loaded.buttons.show(nodes.save);
                            pure.components.uploader.module.upload(
                                file,
                                destination,
                                {
                                    ready : function(params){
                                        pure.buddypress.personalsettings.A.loaded.file.preload.received(params, id, nodes, cropHandle);
                                    },
                                    error : function(params){
                                        pure.buddypress.personalsettings.A.loaded.file.preload.error(params, id, nodes);
                                    },
                                    timeout : function(params){
                                        pure.buddypress.personalsettings.A.loaded.file.preload.error(params, id, nodes);
                                    }
                                },
                                null,
                                'file',
                                [
                                    { name:'command',   value: command  },
                                    { name:'user',      value: user_id  },
                                    { name:'x',         value: '-1'     },
                                    { name:'y',         value: '-1'     },
                                    { name:'height',    value: '-1'     },
                                    { name:'width',     value: '-1'     },
                                    { name:'path',      value: ''       }
                                ]
                            );
                        }
                    },
                    received    : function(params, id, nodes, cropHandle){
                        function parseResponse(params){
                            if (typeof params === 'object'){
                                if (typeof params.response === 'string'){
                                    try{
                                        return JSON.parse(params.response);
                                    }catch (e){
                                        return null;
                                    }
                                }
                            }
                            return null;
                        };
                        var message     = pure.buddypress.personalsettings.A.dialogs.info,
                            response    = parseResponse(params);
                        if (response !== null){
                            if (response.url !== '' && response.message === 'ready_for_crop'){
                                //Save path
                                pure.buddypress.personalsettings.A.variables.add(id, 'path', response.path);
                                //Reset image
                                nodes.image = pure.buddypress.personalsettings.A.images.reset(nodes.type, id);
                                //Update image
                                pure.buddypress.personalsettings.A.images.preview.replaceSRC(
                                    response.url,
                                    nodes.image,
                                    function(){
                                        //Run crop handle
                                        pure.system.runHandle(cropHandle, null, '', this);
                                    }
                                );
                            }else{
                                switch (response.message){
                                    case 'error_during_saving':
                                        message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                                        break;
                                    case 'too_large_filesize':
                                        message('Fail operation', 'Sorry, your file is too large.');
                                        break;
                                }
                                pure.buddypress.personalsettings.A.progress.global.free(id);
                                pure.buddypress.personalsettings.A.loaded.buttons.show(nodes.load);
                                pure.buddypress.personalsettings.A.loaded.buttons.hide(nodes.save);
                            }
                        }else{
                            message('Fail operation', 'Sorry, server give an incorrect response. Please, try later.');
                            pure.buddypress.personalsettings.A.progress.global.free(id);
                            pure.buddypress.personalsettings.A.loaded.buttons.show(nodes.load);
                            pure.buddypress.personalsettings.A.loaded.buttons.hide(nodes.save);
                        }
                    },
                    error       : function(params, id, nodes){
                        var message = pure.buddypress.personalsettings.A.dialogs.info;
                        message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                        pure.buddypress.personalsettings.A.progress.global.free(id);
                        pure.buddypress.personalsettings.A.loaded.buttons.show(nodes.load);
                        pure.buddypress.personalsettings.A.loaded.buttons.hide(nodes.save);
                    }
                },
                save        : {
                    send        : function(id, nodes){
                        var command     = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.commands.' + nodes.field),
                            destination = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.destination'),
                            user_id     = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.user_id'),
                            coords      = pure.buddypress.personalsettings.A.images.crop.coordinates(id),
                            path        = pure.buddypress.personalsettings.A.variables.get(id, 'path');
                        if (command !== null && destination !== null && user_id !== null && coords !== null){
                            if (pure.buddypress.personalsettings.A.progress.global.isBusy(id) === false){
                                pure.buddypress.personalsettings.A.progress.global.busy(id);
                                pure.components.uploader.module.upload(
                                    nodes.file.files[0],
                                    destination,
                                    {
                                        ready : function(params){
                                            pure.buddypress.personalsettings.A.loaded.file.save.received(params, id, nodes);
                                        },
                                        error : function(params){
                                            pure.buddypress.personalsettings.A.loaded.file.save.error(params, id, nodes);
                                        },
                                        timeout : function(params){
                                            pure.buddypress.personalsettings.A.loaded.file.save.error(params, id, nodes);
                                        }
                                    },
                                    null,
                                    'file',
                                    [
                                        { name:'command',   value: command                      },
                                        { name:'user',      value: user_id                      },
                                        { name:'x',         value: coords.x.toString()          },
                                        { name:'y',         value: coords.y.toString()          },
                                        { name:'height',    value: coords.h.toString()          },
                                        { name:'width',     value: coords.w.toString()          },
                                        { name:'path',      value: (path === null ? '' : path)  }
                                    ]
                                );
                            }
                        }
                    },
                    received    : function(params, id, nodes){
                        function parseResponse(params){
                            if (typeof params === 'object'){
                                if (typeof params.response === 'string'){
                                    try{
                                        return JSON.parse(params.response);
                                    }catch (e){
                                        return null;
                                    }
                                }
                            }
                            return null;
                        };
                        var message     = pure.buddypress.personalsettings.A.dialogs.info,
                            response    = parseResponse(params);
                        if (response !== null){
                            if (response.url !== '' && response.message === 'success'){
                                //Reset image
                                nodes.image = pure.buddypress.personalsettings.A.images.reset(nodes.type, id);
                                //Update image
                                pure.buddypress.personalsettings.A.images.preview.replaceSRC(
                                    response.url,
                                    nodes.image,
                                    function(){
                                        pure.buddypress.personalsettings.A.images.crop.destroy(id);
                                        message('Successful operation', 'Your setting was changed');
                                    }
                                );
                            }else{
                                switch (response.message){
                                    case 'error_during_saving':
                                        message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                                        break;
                                    case 'too_large_filesize':
                                        message('Fail operation', 'Sorry, your file is too large.');
                                        break;
                                }
                                pure.buddypress.personalsettings.A.images.crop.     destroy (id);
                                pure.buddypress.personalsettings.A.images.preview.  restore (nodes.image);
                            }
                        }else{
                            message('Fail operation', 'Sorry, server give an incorrect response. Please, try later.');
                            pure.buddypress.personalsettings.A.images.crop.     destroy (id);
                            pure.buddypress.personalsettings.A.images.preview.  restore (nodes.image);
                        }
                        pure.buddypress.personalsettings.A.progress.global.free(id);
                        pure.buddypress.personalsettings.A.loaded.buttons.show(nodes.load);
                        pure.buddypress.personalsettings.A.loaded.buttons.hide(nodes.save);
                    },
                    error       : function(params, id, nodes){
                        var message = pure.buddypress.personalsettings.A.dialogs.info;
                        message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                        pure.buddypress.personalsettings.A.progress.global.free(id);
                        pure.buddypress.personalsettings.A.loaded.buttons.show(nodes.load);
                        pure.buddypress.personalsettings.A.loaded.buttons.hide(nodes.save);
                    }
                }
            }
        },
        settings    : {
            send        : function(id, settings){
                var request     = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.request.settings'),
                    destination = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.destination');
                if (request !== null && destination !== null){
                    if (pure.buddypress.personalsettings.A.progress.global.isBusy(id) === false){
                        request     = request.replace(/\[settings\]/, JSON.stringify(settings));
                        pure.buddypress.personalsettings.A.progress.global.busy(id);
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.personalsettings.A.settings.received(id_request, response, id);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.personalsettings.A.settings.error(event, id_request, id);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.buddypress.personalsettings.A.settings.error(event, id_request, id);
                            }
                        });
                    }
                }
            },
            received    : function(id_request, response, id){
                var message = pure.buddypress.personalsettings.A.dialogs.info;
                switch (response){
                    case 'updated':
                        message('Success', 'Configuration was updated. You will see changes after reloading page.');
                        break;
                    default :
                        message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                        break
                }
                pure.buddypress.personalsettings.A.progress.global.free(id);
            },
            error       : function(event, id_request, id){
                var message = pure.buddypress.personalsettings.A.dialogs.info;
                message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                pure.buddypress.personalsettings.A.progress.global.free(id);
            }
        },
        backgrounds : {
            init    : function(){
                //pure.buddypress.personalsettings.A.images.init('Title'      );
                pure.buddypress.personalsettings.A.images.init('Background' );
            },
            event   : {
                onChange : function(event, id, input, type){
                    var settings    = {},
                        property    = (type === 'Title' ? 'header_background' : 'background'),
                        value       = parseInt(input.value, 10);
                    settings[property] = {
                        attachment_id   : (value > 0 ? value : false),
                        settings        : false
                    };
                    pure.buddypress.personalsettings.A.settings.send(
                        id,
                        settings
                    );
                }
            }
        },
        privacy     : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-field-type="Privacy.Button.Save"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance){
                            var id = instance.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.events.add(
                                    instance,
                                    'click',
                                    function(event){
                                        pure.buddypress.personalsettings.A.privacy.event.onClick(event, id);
                                    }
                                );
                            }
                            instance.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            },
            event   : {
                onClick : function(event, id){
                    var inputs = pure.nodes.select.all('input[data-field-type="Privacy.Field"][data-engine-element-id="' + id + '"]');
                    if (inputs !== null){
                        for(var index = inputs.length - 1; index >= 0; index -= 1){
                            if (inputs[index].checked === true){
                                pure.buddypress.personalsettings.A.settings.send(
                                    id,
                                    {
                                        privacy : {
                                            mode : inputs[index].value
                                        }
                                    }
                                );
                                return true;
                            }
                        }
                    }
                }
            }
        },
        email       : {
            data    : {},
            init    : function(){
                var instances = pure.nodes.select.all('*[data-field-type="Security.Button.Email"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance){
                            var id = instance.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.events.add(
                                    instance,
                                    'click',
                                    function(event){
                                        pure.buddypress.personalsettings.A.email.event.onClick(event, id);
                                    }
                                );
                            }
                            instance.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            },
            clear   : function(id, update){
                var data    = pure.buddypress.personalsettings.A.email.data,
                    input   = pure.nodes.select.first('input[data-field-type="Security.Field.Email"][data-engine-element-id="' + id + '"]');
                if (input !== null){
                    if (typeof data[id] !== 'undefined' && update === false){
                        input.value = data[id];
                    }else{
                        data[id] = input.value;
                    }
                }
            },
            event   : {
                onClick : function(event, id){
                    var input   = pure.nodes.select.first('input[data-field-type="Security.Field.Email"][data-engine-element-id="' + id + '"]'),
                        message = pure.buddypress.personalsettings.A.dialogs.info;
                    if (input !== null){
                        if (pure.tools.validate.email(input.value) !== false){
                            pure.buddypress.personalsettings.A.email.request.send(id, input.value);
                        }else{
                            message('Cannot do it', 'Your email is not valid. Please, check it and try again.');
                        }
                    }
                }
            },
            request : {
                send        : function(id, email){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.request.email'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.destination');
                    if (request !== null && destination !== null){
                        if (pure.buddypress.personalsettings.A.progress.global.isBusy(id) === false){
                            request     = request.replace(/\[email\]/, email);
                            pure.buddypress.personalsettings.A.progress.global.busy(id);
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.buddypress.personalsettings.A.email.request.received(id_request, response, id);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.buddypress.personalsettings.A.email.request.error(event, id_request, id);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.buddypress.personalsettings.A.email.request.error(event, id_request, id);
                                }
                            });
                        }
                    }
                },
                received    : function(id_request, response, id){
                    var message = pure.buddypress.personalsettings.A.dialogs.info;
                    switch (response){
                        case 'success':
                            message('Success', 'Configuration was updated. You will see changes after reloading page.');
                            pure.buddypress.personalsettings.A.email.clear(id, true);
                            break;
                        default :
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break
                    }
                    pure.buddypress.personalsettings.A.progress.global.free(id);
                },
                error       : function(event, id_request, id){
                    var message = pure.buddypress.personalsettings.A.dialogs.info;
                    message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                    pure.buddypress.personalsettings.A.progress.global.free(id);
                }
            }
        },
        password    : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-field-type="Security.Button.Password"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance){
                            var id = instance.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.events.add(
                                    instance,
                                    'click',
                                    function(event){
                                        pure.buddypress.personalsettings.A.password.event.onClick(event, id);
                                    }
                                );
                            }
                            instance.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            },
            clear   : function(id){
                var input_old   = pure.nodes.select.first('input[data-field-type="Security.Field.Password.Old"][data-engine-element-id="' + id + '"]'),
                    input_new   = pure.nodes.select.all('input[data-field-type="Security.Field.Password.New"][data-engine-element-id="' + id + '"]');
                if (input_old !== null && input_new !== null){
                    input_old.value = '';
                    Array.prototype.forEach.call(
                        input_new,
                        function(item, index, source){
                            item.value = '';
                        }
                    );
                }
            },
            event   : {
                onClick : function(event, id){
                    var input_old   = pure.nodes.select.first('input[data-field-type="Security.Field.Password.Old"][data-engine-element-id="' + id + '"]'),
                        input_new   = pure.nodes.select.all('input[data-field-type="Security.Field.Password.New"][data-engine-element-id="' + id + '"]'),
                        message     = pure.buddypress.personalsettings.A.dialogs.info;
                    if (input_old !== null && input_new !== null){
                        if (input_new.length === 2){
                            if (input_old.value.length < 6){
                                message('Cannot do it', 'Check your current password. It seems it is too short.');
                                return false;
                            }
                            if (input_new[0].value.length < 6){
                                message('Cannot do it', 'Check your new password. It seems it is too short.');
                                return false;
                            }
                            if (input_new[0].value !== input_new[1].value){
                                message('Cannot do it', 'Check your new password, it does not match.');
                                return false;
                            }
                            pure.buddypress.personalsettings.A.password.request.send(id, input_old.value, input_new[0].value);
                        }
                    }
                }
            },
            request : {
                send        : function(id, old_password, new_password){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.request.password'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.personalsettings.configuration.destination');
                    if (request !== null && destination !== null){
                        if (pure.buddypress.personalsettings.A.progress.global.isBusy(id) === false){
                            request     = request.replace(/\[old\]/, old_password);
                            request     = request.replace(/\[new\]/, new_password);
                            pure.buddypress.personalsettings.A.progress.global.busy(id);
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.buddypress.personalsettings.A.password.request.received(id_request, response, id);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.buddypress.personalsettings.A.password.request.error(event, id_request, id);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.buddypress.personalsettings.A.password.request.error(event, id_request, id);
                                }
                            });
                        }
                    }
                },
                received    : function(id_request, response, id){
                    var message = pure.buddypress.personalsettings.A.dialogs.info;
                    switch (response){
                        case 'success':
                            message('Success', 'Your password was changed.');
                            break;
                        case 'wrong password':
                            message('Fail operation', 'Your old password is incorrect. Try again.');
                            break;
                        default :
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break
                    }
                    pure.buddypress.personalsettings.A.progress.global.free(id);
                    pure.buddypress.personalsettings.A.password.clear(id);
                },
                error       : function(event, id_request, id){
                    var message = pure.buddypress.personalsettings.A.dialogs.info;
                    message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                    pure.buddypress.personalsettings.A.progress.global.free(id);
                    pure.buddypress.personalsettings.A.password.clear(id);
                }
            }
        },
        progress    : {
            global    : {
                data    : {},
                isBusy : function(instance_id){
                    var data = pure.buddypress.personalsettings.A.progress.global.data;
                    return (typeof data[instance_id] !== 'undefined' ? true : false);
                },
                busy    : function(instance_id){
                    var data        = pure.buddypress.personalsettings.A.progress.global.data,
                        instance    = pure.nodes.select.first('*[data-engine-element="PersonalSettings.Page"][data-engine-element-id="' + instance_id + '"]');
                    if (instance !== null){
                        data[instance_id] = pure.templates.progressbar.A.show(instance, "background: -moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%, rgba(255,255,255,1) 5%, rgba(255,255,255,1) 8%, rgba(255,255,255,0) 100%); background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,1)), color-stop(5%,rgba(255,255,255,1)), color-stop(8%,rgba(255,255,255,1)), color-stop(100%,rgba(255,255,255,0))); background: -webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -o-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: radial-gradient(ellipse at center,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#00ffffff',GradientType=1 );z-index:10000;");
                    }
                },
                free    : function(instance_id){
                    var data = pure.buddypress.personalsettings.A.progress.global.data;
                    if (typeof data[instance_id] !== 'undefined'){
                        pure.templates.progressbar.B.hide(data[instance_id]);
                        data[instance_id] = null;
                        delete data[instance_id];
                    }
                }
            }
        },
        variables   : {
            storage : {},
            add     : function(group, name, value){
                var storage = pure.buddypress.personalsettings.A.variables.storage;
                storage[group] = (typeof storage[group] === 'undefined' ? {} : storage[group] );
                storage[group][name] = value;
            },
            get     : function(group, name){
                var storage = pure.buddypress.personalsettings.A.variables.storage;
                return (typeof storage[group] !== 'undefined' ? (typeof storage[group][name] !== 'undefined' ? storage[group][name] : null) : null);
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
    pure.system.start.add(pure.buddypress.personalsettings.A.init);
}());