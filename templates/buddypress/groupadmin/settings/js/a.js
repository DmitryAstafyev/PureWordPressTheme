(function () {
    if (typeof window.pure                              !== "object") { window.pure                             = {}; }
    if (typeof window.pure.buddypress                   !== "object") { window.pure.buddypress                  = {}; }
    if (typeof window.pure.buddypress.groupsettings     !== "object") { window.pure.buddypress.groupsettings    = {}; }
    "use strict";
    window.pure.buddypress.groupsettings.A = {
        init        : function () {
            window.pure.wordpress.media.images.             init();
            pure.buddypress.groupsettings.A.initialize.     init();
            pure.buddypress.groupsettings.A.loaded.         all();
            pure.buddypress.groupsettings.A.backgrounds.    init();
            pure.buddypress.groupsettings.A.privacy.        init();
            pure.buddypress.groupsettings.A.basic.          init();
        },
        initialize  : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-engine-element="groupsettings.Container"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        (function(node){
                            function attachEvents(eventType, id){
                                var callers = pure.nodes.select.all('*[data-engine-groupsettings-' + eventType + '-caller="' + id + '"]:not([data-type-element-inited])');
                                if (callers !== null){
                                    for(var index = callers.length - 1; index >= 0; index -= 1){
                                        if (eventType === 'open'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.groupsettings.A.render.show(id);
                                            });
                                        }
                                        if (eventType === 'close'){
                                            pure.events.add(callers[index], 'click', function(event){
                                                pure.buddypress.groupsettings.A.render.hide(id);
                                            });
                                        }
                                        callers[index].setAttribute('data-type-element-inited', 'true');
                                    }
                                }
                            };
                            var id      = node.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.buddypress.groupsettings.A.render.hide          (id);
                                pure.buddypress.groupsettings.A.render.orderOnTop    (id);
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
                var instances = pure.nodes.select.all('*[data-engine-element="groupsettings.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        document.body.appendChild(instances[index]);
                    }
                }
            },
            show        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="groupsettings.Container"][data-engine-element-id="' + id + '"]');
                if (instances !== null){
                    pure.buddypress.groupsettings.A.render.orderOnTop (id);
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        instances[index].style.display = '';
                    }
                    pure.buddypress.groupsettings.A.basic.clear(id, false);
                }
            },
            hide        : function(id){
                var instances = pure.nodes.select.all('*[data-engine-element="groupsettings.Container"][data-engine-element-id="' + id + '"]');
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
                                        pure.buddypress.groupsettings.A.backgrounds.event.onChange(event, id, instance, type);
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
                make    : function(file, image, onFinish){
                    var fileReader = null;
                    if (window.FileReader !== undefined){
                        pure.buddypress.groupsettings.A.images.preview.savePrevious(image);
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
                replaceSRC : function(url, image, onLoad){
                    pure.buddypress.groupsettings.A.images.preview.savePrevious(image);
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
                    var ratio = (type === 'avatar' ? 1 : 3);
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
                pure.buddypress.groupsettings.A.loaded.init('Avatar' );
                pure.buddypress.groupsettings.A.loaded.init('Title'  );
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
                                            pure.buddypress.groupsettings.A.loaded.load.onClick(event, id, nodes);
                                        }
                                    );
                                    pure.events.add(
                                        nodes.remove,
                                        'click',
                                        function(event){
                                            pure.buddypress.groupsettings.A.loaded.remove.onClick(event, id, nodes);
                                        }
                                    );
                                    pure.events.add(
                                        nodes.file,
                                        'change',
                                        function(event){
                                            pure.buddypress.groupsettings.A.loaded.file.onChange(event, id, nodes);
                                        }
                                    );
                                    pure.events.add(
                                        nodes.save,
                                        'click',
                                        function(event){
                                            pure.buddypress.groupsettings.A.loaded.save.onClick(event, id, nodes);
                                        }
                                    );
                                    pure.buddypress.groupsettings.A.loaded.buttons.hide(nodes.save);
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
                    pure.buddypress.groupsettings.A.loaded.remove.send(id, nodes);
                },
                send    : function(id, nodes){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.request.' + nodes.field + '.remove'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.destination'),
                        isCrop      = pure.buddypress.groupsettings.A.images.crop.coordinates(id);
                    if (request !== null && destination !== null){
                        if (isCrop === null){
                            if (pure.buddypress.groupsettings.A.progress.global.isBusy(id) === false){
                                pure.buddypress.groupsettings.A.loaded.buttons.hide(nodes.save);
                                pure.buddypress.groupsettings.A.loaded.buttons.show(nodes.load);
                                pure.buddypress.groupsettings.A.progress.global.busy(id);
                                pure.tools.request.send({
                                    type        : 'POST',
                                    url         : destination,
                                    request     : request,
                                    onrecieve   : function (id_request, response) {
                                        pure.buddypress.groupsettings.A.loaded.remove.received(id_request, response, id, nodes);
                                    },
                                    onreaction  : null,
                                    onerror     : function (event, id_request) {
                                        pure.buddypress.groupsettings.A.loaded.remove.error(event, id_request, id, nodes);
                                    },
                                    ontimeout   : function (event, id_request) {
                                        pure.buddypress.groupsettings.A.loaded.remove.error(event, id_request, id, nodes);
                                    }
                                });
                            }
                        }else{
                            pure.buddypress.groupsettings.A.images.crop.     destroy (id);
                            pure.buddypress.groupsettings.A.images.preview.  restore (nodes.image);
                            pure.buddypress.groupsettings.A.loaded.buttons.  show    (nodes.load);
                            pure.buddypress.groupsettings.A.loaded.buttons.  hide    (nodes.save);
                        }
                    }
                },
                received    : function(id_request, response, id, nodes){
                    var message = pure.buddypress.groupsettings.A.dialogs.info,
                        noImage = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.noImageIcon');
                    switch (response){
                        case 'success':
                            if (noImage !== null){
                                pure.buddypress.groupsettings.A.images.preview.replaceSRC(noImage, nodes.image, null);
                            }
                            break;
                        case 'fail':
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break;
                    }
                    pure.buddypress.groupsettings.A.progress.global.free(id);
                },
                error       : function(event, id_request, id, nodes){
                    var message = pure.buddypress.groupsettings.A.dialogs.info;
                    message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                    pure.buddypress.groupsettings.A.progress.global.free(id);
                }
            },
            save    : {
                onClick : function(event, id, nodes){
                    pure.buddypress.groupsettings.A.loaded.file.save.send(id, nodes);
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
                    var message     = pure.buddypress.groupsettings.A.dialogs.info,
                        ext         = null,
                        cropHandle  = null;
                    if (pure.buddypress.groupsettings.A.progress.global.isBusy(id) === false){
                        if (typeof nodes.file.files !== 'undefined' && typeof nodes.file.value === 'string'){
                            if (nodes.file.files.length === 1){
                                ext = (nodes.file.value.match(/\.([^\.]+)$/)[1]).toLowerCase();
                                if (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg'){
                                    cropHandle  = function(){
                                        pure.buddypress.groupsettings.A.loaded.buttons.hide(nodes.load);
                                        pure.buddypress.groupsettings.A.loaded.buttons.show(nodes.save);
                                        pure.buddypress.groupsettings.A.images.crop.     attach  (id, nodes.image, nodes.field);
                                        pure.buddypress.groupsettings.A.progress.global. free    (id);
                                    };
                                    pure.buddypress.groupsettings.A.progress.global. busy(id);
                                    if (pure.buddypress.groupsettings.A.images.preview.isPossible !== false){
                                        //Load image locally and crop
                                        nodes.image = pure.buddypress.groupsettings.A.images.reset(nodes.type, id);
                                        pure.buddypress.groupsettings.A.images.preview.  make(
                                            nodes.file.files[0],
                                            nodes.image,
                                            cropHandle
                                        );
                                        return true;
                                    }else{
                                        //Upload image on server before crop
                                        pure.buddypress.groupsettings.A.loaded.file.preload.send(
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
                        var command     = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.commands.' + nodes.field),
                            destination = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.destination'),
                            user_id     = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.user_id'),
                            group_id    = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.group_id');
                        if (command !== null && destination !== null && user_id !== null && group_id !== null){
                            pure.buddypress.groupsettings.A.loaded.buttons.hide(nodes.load);
                            pure.buddypress.groupsettings.A.loaded.buttons.show(nodes.save);
                            pure.components.uploader.module.upload(
                                file,
                                destination,
                                {
                                    ready : function(params){
                                        pure.buddypress.groupsettings.A.loaded.file.preload.received(params, id, nodes, cropHandle);
                                    },
                                    error : function(params){
                                        pure.buddypress.groupsettings.A.loaded.file.preload.error(params, id, nodes);
                                    },
                                    timeout : function(params){
                                        pure.buddypress.groupsettings.A.loaded.file.preload.error(params, id, nodes);
                                    }
                                },
                                null,
                                'file',
                                [
                                    { name:'command',   value: command  },
                                    { name:'user',      value: user_id  },
                                    { name:'group',     value: group_id },
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
                        var message     = pure.buddypress.groupsettings.A.dialogs.info,
                            response    = parseResponse(params);
                        if (response !== null){
                            if (response.url !== '' && response.message === 'ready_for_crop'){
                                //Save path
                                pure.buddypress.groupsettings.A.variables.add(id, 'path', response.path);
                                //Reset image
                                nodes.image = pure.buddypress.groupsettings.A.images.reset(nodes.type, id);
                                //Update image
                                pure.buddypress.groupsettings.A.images.preview.replaceSRC(
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
                                pure.buddypress.groupsettings.A.progress.global.free(id);
                                pure.buddypress.groupsettings.A.loaded.buttons.show(nodes.load);
                                pure.buddypress.groupsettings.A.loaded.buttons.hide(nodes.save);
                            }
                        }else{
                            message('Fail operation', 'Sorry, server give an incorrect response. Please, try later.');
                            pure.buddypress.groupsettings.A.progress.global.free(id);
                            pure.buddypress.groupsettings.A.loaded.buttons.show(nodes.load);
                            pure.buddypress.groupsettings.A.loaded.buttons.hide(nodes.save);
                        }
                    },
                    error       : function(params, id, nodes){
                        var message = pure.buddypress.groupsettings.A.dialogs.info;
                        message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                        pure.buddypress.groupsettings.A.progress.global.free(id);
                        pure.buddypress.groupsettings.A.loaded.buttons.show(nodes.load);
                        pure.buddypress.groupsettings.A.loaded.buttons.hide(nodes.save);
                    }
                },
                save        : {
                    send        : function(id, nodes){
                        var command     = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.commands.' + nodes.field),
                            destination = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.destination'),
                            user_id     = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.user_id'),
                            group_id    = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.group_id'),
                            coords      = pure.buddypress.groupsettings.A.images.crop.coordinates(id),
                            path        = pure.buddypress.groupsettings.A.variables.get(id, 'path');
                        if (command !== null && destination !== null && user_id !== null && group_id !== null && coords !== null){
                            if (pure.buddypress.groupsettings.A.progress.global.isBusy(id) === false){
                                pure.buddypress.groupsettings.A.progress.global.busy(id);
                                pure.components.uploader.module.upload(
                                    nodes.file.files[0],
                                    destination,
                                    {
                                        ready : function(params){
                                            pure.buddypress.groupsettings.A.loaded.file.save.received(params, id, nodes);
                                        },
                                        error : function(params){
                                            pure.buddypress.groupsettings.A.loaded.file.save.error(params, id, nodes);
                                        },
                                        timeout : function(params){
                                            pure.buddypress.groupsettings.A.loaded.file.save.error(params, id, nodes);
                                        }
                                    },
                                    null,
                                    'file',
                                    [
                                        { name:'command',   value: command                      },
                                        { name:'user',      value: user_id                      },
                                        { name:'group',     value: group_id                     },
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
                        var message     = pure.buddypress.groupsettings.A.dialogs.info,
                            response    = parseResponse(params);
                        if (response !== null){
                            if (response.url !== '' && response.message === 'success'){
                                //Reset image
                                nodes.image = pure.buddypress.groupsettings.A.images.reset(nodes.type, id);
                                //Update image
                                pure.buddypress.groupsettings.A.images.preview.replaceSRC(
                                    response.url,
                                    nodes.image,
                                    function(){
                                        pure.buddypress.groupsettings.A.images.crop.destroy(id);
                                        message('Successful operation', 'Group settings was changed');
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
                                pure.buddypress.groupsettings.A.images.crop.     destroy (id);
                                pure.buddypress.groupsettings.A.images.preview.  restore (nodes.image);
                            }
                        }else{
                            message('Fail operation', 'Sorry, server give an incorrect response. Please, try later.');
                            pure.buddypress.groupsettings.A.images.crop.     destroy (id);
                            pure.buddypress.groupsettings.A.images.preview.  restore (nodes.image);
                        }
                        pure.buddypress.groupsettings.A.progress.global.free(id);
                        pure.buddypress.groupsettings.A.loaded.buttons.show(nodes.load);
                        pure.buddypress.groupsettings.A.loaded.buttons.hide(nodes.save);
                    },
                    error       : function(params, id, nodes){
                        var message = pure.buddypress.groupsettings.A.dialogs.info;
                        message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                        pure.buddypress.groupsettings.A.progress.global.free(id);
                        pure.buddypress.groupsettings.A.loaded.buttons.show(nodes.load);
                        pure.buddypress.groupsettings.A.loaded.buttons.hide(nodes.save);
                    }
                }
            }
        },
        settings    : {
            send        : function(id, settings){
                var request     = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.request.settings'),
                    destination = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.destination');
                if (request !== null && destination !== null){
                    if (pure.buddypress.groupsettings.A.progress.global.isBusy(id) === false){
                        request     = request.replace(/\[settings\]/, JSON.stringify(settings));
                        pure.buddypress.groupsettings.A.progress.global.busy(id);
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : destination,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.buddypress.groupsettings.A.settings.received(id_request, response, id);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.buddypress.groupsettings.A.settings.error(event, id_request, id);
                            },
                            ontimeout   : function (event, id_request) {
                                pure.buddypress.groupsettings.A.settings.error(event, id_request, id);
                            }
                        });
                    }
                }
            },
            received    : function(id_request, response, id){
                var message = pure.buddypress.groupsettings.A.dialogs.info;
                switch (response){
                    case 'updated':
                        message('Success', 'Configuration was updated. You will see changes after reloading page.');
                        break;
                    default :
                        message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                        break
                }
                pure.buddypress.groupsettings.A.progress.global.free(id);
            },
            error       : function(event, id_request, id){
                var message = pure.buddypress.groupsettings.A.dialogs.info;
                message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                pure.buddypress.groupsettings.A.progress.global.free(id);
            }
        },
        backgrounds : {
            init    : function(){
                pure.buddypress.groupsettings.A.images.init('Background' );
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
                    pure.buddypress.groupsettings.A.settings.send(
                        id,
                        settings
                    );
                }
            }
        },
        privacy     : {
            init    : function(){
                var instances = pure.nodes.select.all('*[data-field-type="GroupSettings.Button.Privacy"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance){
                            var id = instance.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.events.add(
                                    instance,
                                    'click',
                                    function(event){
                                        pure.buddypress.groupsettings.A.privacy.event.onClick(event, id);
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
                    function getValue(nodes){
                        for(var index = nodes.length - 1; index >= 0; index -= 1) {
                            if (nodes[index].checked !== false){
                                return nodes[index].value;
                            }
                        }
                        return null;
                    }
                    var privacy = pure.nodes.select.all('input[data-field-type="Privacy.Field"][data-engine-element-id="' + id + '"]'),
                        invite  = pure.nodes.select.all('input[data-field-type="Invite.Field"][data-engine-element-id="' + id + '"]');
                    if (privacy !== null && invite !== null){
                        privacy = getValue(privacy);
                        invite  = getValue(invite);
                        if (privacy !== null && invite !== null){
                            pure.buddypress.groupsettings.A.privacy.request.send(id, privacy, invite);
                        }
                    }
                }
            },
            request : {
                send        : function(id, privacy, invite){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.request.privacy'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.destination');
                    if (request !== null && destination !== null){
                        if (pure.buddypress.groupsettings.A.progress.global.isBusy(id) === false){
                            pure.buddypress.groupsettings.A.progress.global.busy(id);
                            request     = request.replace(/\[status\]/,           privacy   );
                            request     = request.replace(/\[invite_status\]/,    invite    );
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.buddypress.groupsettings.A.privacy.request.received(id_request, response, id);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.buddypress.groupsettings.A.privacy.request.error(event, id_request, id);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.buddypress.groupsettings.A.privacy.request.error(event, id_request, id);
                                }
                            });
                        }
                    }
                },
                received    : function(id_request, response, id){
                    var message = pure.buddypress.groupsettings.A.dialogs.info;
                    switch (response){
                        case 'success':
                            message('Success', 'Configuration was updated.');
                            pure.buddypress.groupsettings.A.basic.clear(id, true);
                            break;
                        default :
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break
                    }
                    pure.buddypress.groupsettings.A.progress.global.free(id);
                },
                error       : function(event, id_request, id){
                    var message = pure.buddypress.groupsettings.A.dialogs.info;
                    message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                    pure.buddypress.groupsettings.A.progress.global.free(id);
                }
            }
        },
        basic       : {
            data    : {},
            init    : function(){
                var instances = pure.nodes.select.all('*[data-field-type="GroupSettings.Button.Basic"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        (function(instance){
                            var id = instance.getAttribute('data-engine-element-id');
                            if (id !== null && id !== ''){
                                pure.events.add(
                                    instance,
                                    'click',
                                    function(event){
                                        pure.buddypress.groupsettings.A.basic.event.onClick(event, id);
                                    }
                                );
                            }
                            instance.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            },
            clear   : function(id, update){
                var data        = pure.buddypress.groupsettings.A.basic.data,
                    name        = pure.nodes.select.first('textarea[data-field-type="GroupSettings.Field.Name"][data-engine-element-id="' + id + '"]'),
                    description = pure.nodes.select.first('textarea[data-field-type="GroupSettings.Field.Description"][data-engine-element-id="' + id + '"]');
                if (name !== null && description !== null){
                    if (typeof data[id] !== 'undefined' && update === false){
                        name.value          = data[id].name;
                        description.value   = data[id].description;
                    }else{
                        data[id] = {
                            name        : name.value,
                            description : description.value
                        };
                    }
                }
            },
            event   : {
                onClick : function(event, id){
                    var name        = pure.nodes.select.first('textarea[data-field-type="GroupSettings.Field.Name"][data-engine-element-id="' + id + '"]'),
                        description = pure.nodes.select.first('textarea[data-field-type="GroupSettings.Field.Description"][data-engine-element-id="' + id + '"]'),
                        message     = pure.buddypress.groupsettings.A.dialogs.info;
                    if (name !== null && description !== null){
                        if (name.value.length < 3 || name.value > 255){
                            message('Cannot do it', 'Name of group should be not more than 255 symbols and not less 3.');
                            return false;
                        }
                        if (description.value.length < 12 || description.value > 500){
                            message('Cannot do it', 'Description of group should be not more than 500 symbols and not less 12.');
                            return false;
                        }
                        pure.buddypress.groupsettings.A.basic.request.send(id, name.value, description.value);
                    }
                }
            },
            request : {
                send        : function(id, name, description){
                    var request     = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.request.basic'),
                        destination = pure.system.getInstanceByPath('pure.buddypress.groupsettings.configuration.destination');
                    if (request !== null && destination !== null){
                        if (pure.buddypress.groupsettings.A.progress.global.isBusy(id) === false){
                            request     = request.replace(/\[name\]/,           name        );
                            request     = request.replace(/\[description\]/,    description );
                            pure.buddypress.groupsettings.A.progress.global.busy(id);
                            pure.buddypress.groupsettings.A.dialogs.info(
                                'Notification',
                                'Do you want notify members of this group about this changes? Pay your attention, notification process will take some time.',
                                [
                                    {
                                        title       : 'NO',
                                        handle      : function(){
                                            request = request.replace(/\[notifications\]/, 'off');
                                            pure.tools.request.send({
                                                type        : 'POST',
                                                url         : destination,
                                                request     : request,
                                                onrecieve   : function (id_request, response) {
                                                    pure.buddypress.groupsettings.A.basic.request.received(id_request, response, id);
                                                },
                                                onreaction  : null,
                                                onerror     : function (event, id_request) {
                                                    pure.buddypress.groupsettings.A.basic.request.error(event, id_request, id);
                                                },
                                                ontimeout   : function (event, id_request) {
                                                    pure.buddypress.groupsettings.A.basic.request.error(event, id_request, id);
                                                }
                                            });
                                        },
                                        closeAfter  : true
                                    },
                                    {
                                        title       : 'YES, NOTIFY THEM',
                                        handle      : function(){
                                            request = request.replace(/\[notifications\]/, 'on');
                                            pure.tools.request.send({
                                                type        : 'POST',
                                                url         : destination,
                                                request     : request,
                                                onrecieve   : function (id_request, response) {
                                                    pure.buddypress.groupsettings.A.basic.request.received(id_request, response, id);
                                                },
                                                onreaction  : null,
                                                onerror     : function (event, id_request) {
                                                    pure.buddypress.groupsettings.A.basic.request.error(event, id_request, id);
                                                },
                                                ontimeout   : function (event, id_request) {
                                                    pure.buddypress.groupsettings.A.basic.request.error(event, id_request, id);
                                                }
                                            });
                                        },
                                        closeAfter  : true
                                    }
                                ]
                            );
                        }
                    }
                },
                received    : function(id_request, response, id){
                    var message = pure.buddypress.groupsettings.A.dialogs.info;
                    switch (response){
                        case 'success':
                            message('Success', 'Configuration was updated.');
                            pure.buddypress.groupsettings.A.basic.clear(id, true);
                            break;
                        default :
                            message('Fail operation', 'Sorry, some error is on server side. Please, try again later');
                            break
                    }
                    pure.buddypress.groupsettings.A.progress.global.free(id);
                },
                error       : function(event, id_request, id){
                    var message = pure.buddypress.groupsettings.A.dialogs.info;
                    message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.');
                    pure.buddypress.groupsettings.A.progress.global.free(id);
                }
            }
        },
        progress    : {
            global    : {
                data    : {},
                isBusy : function(instance_id){
                    var data = pure.buddypress.groupsettings.A.progress.global.data;
                    return (typeof data[instance_id] !== 'undefined' ? true : false);
                },
                busy    : function(instance_id){
                    var data        = pure.buddypress.groupsettings.A.progress.global.data,
                        instance    = pure.nodes.select.first('*[data-engine-element="groupsettings.Page"][data-engine-element-id="' + instance_id + '"]');
                    if (instance !== null){
                        data[instance_id] = pure.templates.progressbar.A.show(instance, "background: -moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%, rgba(255,255,255,1) 5%, rgba(255,255,255,1) 8%, rgba(255,255,255,0) 100%); background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,1)), color-stop(5%,rgba(255,255,255,1)), color-stop(8%,rgba(255,255,255,1)), color-stop(100%,rgba(255,255,255,0))); background: -webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -o-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: -ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); background: radial-gradient(ellipse at center,  rgba(255,255,255,1) 0%,rgba(255,255,255,1) 5%,rgba(255,255,255,1) 8%,rgba(255,255,255,0) 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#00ffffff',GradientType=1 );z-index:10000;");
                    }
                },
                free    : function(instance_id){
                    var data = pure.buddypress.groupsettings.A.progress.global.data;
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
                var storage = pure.buddypress.groupsettings.A.variables.storage;
                storage[group] = (typeof storage[group] === 'undefined' ? {} : storage[group] );
                storage[group][name] = value;
            },
            get     : function(group, name){
                var storage = pure.buddypress.groupsettings.A.variables.storage;
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
    pure.system.start.add(pure.buddypress.groupsettings.A.init);
}());