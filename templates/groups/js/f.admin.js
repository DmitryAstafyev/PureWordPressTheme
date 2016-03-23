(function () {
    if (typeof window.pure              !== "object") { window.pure                 = {}; }
    if (typeof window.pure.groups       !== "object") { window.pure.groups          = {}; }
    if (typeof window.pure.groups.admin !== "object") { window.pure.groups.admin    = {}; }
    "use strict";
    window.pure.groups.admin.F = {
        data: {
            storage : {},
            set     : function (id, input, button, progress, destination, user, group) {
                var id = (typeof id === "string" ? id : pure.tools.IDs.get("groups.F.ID."));
                if (typeof pure.groups.admin.F.data.storage[id] !== "object") {
                    pure.groups.admin.F.data.storage[id] = {
                        id          : id,
                        stage       : '',
                        path        : null,
                        input       : input,
                        button      : button,
                        progress    : progress,
                        progressNode: null,
                        destination : destination,
                        user        : user,
                        group       : group,
                        size        : {
                            windowWidth : 0,
                            boxWidth    : 0,
                            boxHeight   : 0
                        }
                    };
                    return pure.groups.admin.F.data.storage[id];
                }
                return null;
            },
            get     : function (id) {
                return (typeof pure.groups.admin.F.data.storage[id] === "object" ? pure.groups.admin.F.data.storage[id] : null);
            }
        },
        init    : function () {
            pure.groups.admin.F.initialize.buttons.all();
            pure.groups.admin.F.More.init();
        },
        initialize : {
            buttons : {
                all         : function(){
                    pure.groups.admin.F.initialize.buttons.updateAvatar();
                    pure.groups.admin.F.initialize.buttons.updateBasicData();
                    pure.groups.admin.F.initialize.buttons.updateMembersSettings();
                    pure.groups.admin.F.initialize.buttons.updateVisibilitySetting();
                    pure.groups.admin.F.initialize.buttons.updateInvitesManagement();
                    pure.groups.admin.F.initialize.buttons.updateRequestsManagement();
                    pure.groups.admin.F.UpdateContent.events.attach();
                },
                updateAvatar            : function(){
                    var instances = pure.nodes.select.all('*[data-engine-element="group_avatar_file_chooser"]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var params      = pure.groups.admin.F.Helpers.readDataFromAttributes(node),
                                        input       = null,
                                        image       = null;
                                    if (params.id !== null && params.progress !== null && params.destination !== null && params.user !== null && params.group !== null){
                                        input = pure.nodes.select.first('input[data-engine-id="' + params.id + '"][type="file"]');
                                        image = pure.nodes.select.first('img[data-engine-id="' + params.id + '"]');
                                        if (input !== null && image !== null){
                                            pure.events.add(node, 'click', function(event){
                                                pure.groups.admin.F.Actions.avatarUpload.onClick(event, params.id);
                                            });
                                            pure.events.add(input, 'change', function(event){
                                                pure.groups.admin.F.Actions.avatarUpload.openFile(event, params.id);
                                            });
                                            pure.groups.admin.F.data.set(params.id, input, node, params.progress, params.destination, params.user, params.group);
                                            pure.groups.admin.F.Actions.avatarUpload.stages.set(params.id, 'choose');
                                            node.setAttribute('data-type-element-inited', 'true');
                                        }
                                    }
                                }(instances[index]));
                            }
                        }
                    }
                },
                updateBasicData         : function(){
                    var instances = pure.nodes.select.all('*[data-engine-element="group_basic_settings_save"]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var params = pure.groups.admin.F.Helpers.readDataFromAttributes(node);
                                    if (params.id !== null && params.progress !== null && params.destination !== null && params.user !== null && params.group !== null){
                                        pure.events.add(node, 'click', function(event){
                                            pure.groups.admin.F.Actions.basicSettingsSave.onClick(event, node, params);
                                        });
                                        node.setAttribute('data-type-element-inited', 'true');
                                    }
                                }(instances[index]));
                            }
                        }
                    }
                },
                updateVisibilitySetting : function(){
                    var instances = pure.nodes.select.all('*[data-engine-element="group_visibility_settings_save"]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var params = pure.groups.admin.F.Helpers.readDataFromAttributes(node);
                                    if (params.id !== null && params.progress !== null && params.destination !== null && params.user !== null && params.group !== null){
                                        pure.events.add(node, 'click', function(event){
                                            pure.groups.admin.F.Actions.visibilitySetting.onClick(event, node, params);
                                        });
                                        node.setAttribute('data-type-element-inited', 'true');
                                    }
                                }(instances[index]));
                            }
                        }
                    }
                },
                updateMembersSettings   : function(){
                    var instances = pure.nodes.select.all('*[data-engine-element="group_members_settings_data"]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var params  = pure.groups.admin.F.Helpers.readDataFromAttributes(node),
                                        buttons = null;
                                    if (params.id !== null && params.progress !== null && params.destination !== null && params.user !== null && params.member !== null && params.group !== null){
                                        buttons = pure.nodes.select.all('*[data-engine-data-group="' + params.id + params.member + '"][data-engine-action]:not([data-type-element-inited])');
                                        for(var index = buttons.length - 1; index >= 0; index -= 1){
                                            (function(button, params){
                                                var type = button.getAttribute('data-engine-action');
                                                if (typeof type === 'string'){
                                                    if (type !== ''){
                                                        pure.events.add(button, 'click', function(event){
                                                            pure.groups.admin.F.Actions.membersSettings.onClick(event, button, params, type);
                                                        });
                                                    }
                                                }
                                                button.setAttribute('data-type-element-inited', 'true');
                                            }(buttons[index], pure.tools.objects.copy(null, params)));
                                        }
                                        node.setAttribute('data-type-element-inited', 'true');
                                    }
                                }(instances[index]));
                            }
                        }
                    }
                },
                updateRequestsManagement: function(){
                    var instances = pure.nodes.select.all('*[data-engine-element="group_requests_manage_data"]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var params  = pure.groups.admin.F.Helpers.readDataFromAttributes(node),
                                        buttons = null;
                                    if (params.id       !== null && params.progress !== null && params.destination  !== null &&
                                        params.user     !== null && params.member   !== null && params.request      !== null &&
                                        params.group    !== null){
                                        buttons = pure.nodes.select.all('*[data-engine-data-group="' + params.request + '"][data-engine-action]:not([data-type-element-inited])');
                                        for(var index = buttons.length - 1; index >= 0; index -= 1){
                                            (function(button, params){
                                                var type = button.getAttribute('data-engine-action');
                                                if (typeof type === 'string'){
                                                    if (type !== ''){
                                                        pure.events.add(button, 'click', function(event){
                                                            pure.groups.admin.F.Actions.requestsManagement.onClick(event, button, params, type);
                                                        });
                                                    }
                                                }
                                                button.setAttribute('data-type-element-inited', 'true');
                                            }(buttons[index], pure.tools.objects.copy(null, params)));
                                        }
                                        node.setAttribute('data-type-element-inited', 'true');
                                    }
                                }(instances[index]));
                            }
                        }
                    }
                },
                updateInvitesManagement : function(){
                    var instances = pure.nodes.select.all('*[data-engine-element="group_send_invitation"]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var params  = pure.groups.admin.F.Helpers.readDataFromAttributes(node),
                                        buttons = null;
                                    if (params.id   !== null && params.progress !== null && params.destination  !== null &&
                                        params.user !== null && params.group    !== null){
                                        buttons = pure.nodes.select.all('*[data-engine-id="' + params.id + params.group + '"][data-engine-action="reject"]:not([data-type-element-inited])');
                                        for(var index = buttons.length - 1; index >= 0; index -= 1){
                                            (function(button, params){
                                                pure.events.add(button, 'click', function(event){
                                                    pure.groups.admin.F.Actions.invitesManagement.onClick(event, button, params, 'reject');
                                                });
                                                button.setAttribute('data-type-element-inited', 'true');
                                            }(buttons[index], pure.tools.objects.copy(null, params)));
                                        }
                                        pure.events.add(node, 'click', function(event){
                                            pure.groups.admin.F.Actions.invitesManagement.onClick(event, node, params, 'invite');
                                        });
                                        node.setAttribute('data-type-element-inited', 'true');
                                    }
                                }(instances[index]));
                            }
                        }
                    }
                }
            }
        },
        Actions : {
            common              : {
                permissionMessages : function(responce, button){
                    var message  = pure.groups.admin.F.Actions.message;
                    switch (responce){
                        case 'wrong_user':
                            message('Fail operation', 'Sorry, you cannot do it, because you has wrong user data.', button, null   );
                            return true;
                            break;
                        case 'wrong_group':
                            message('Fail operation', 'Sorry, you cannot do it, because you has wrong group data.', button, null   );
                            return true;
                            break;
                        case 'no_permission':
                            message('Fail operation', 'Sorry, you cannot do it, because you are not an administrator of this group.', button, null   );
                            return true;
                            break;
                        case 'wrong_file':
                            message('Fail operation', 'Sorry, you cannot do it, because file is incorrect.', button, null   );
                            return true;
                            break;
                    }
                    return false;
                }
            },
            avatarUpload        : {
                onClick     : function(event, id){
                    var instance = pure.groups.admin.F.data.get(id);
                    if (instance !== null){
                        if (pure.groups.admin.F.Status.isBusy(instance.button) === false){
                            switch (pure.groups.admin.F.Actions.avatarUpload.stages.get(id)){
                                case 'choose':
                                    pure.events.call(instance.input, 'click');
                                    break;
                                case 'crop':
                                    pure.groups.admin.F.Actions.avatarUpload.request.send(id);
                                    break;
                            }
                        }
                    }
                },
                openFile    : function(event, id){
                    var image           = null,
                        message         = pure.groups.admin.F.Actions.message,
                        ext             = null,
                        instance        = pure.groups.admin.F.data.get(id);
                    if (instance !== null){
                        if (typeof instance.input.files !== 'undefined' && typeof instance.input.value === 'string'){
                            if (instance.input.files.length === 1){
                                ext = (instance.input.value.match(/\.([^\.]+)$/)[1]).toLowerCase();
                                if (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg'){
                                    image = pure.groups.admin.F.Actions.avatarUpload.preview.update(id);
                                    if (image !== null){
                                        if (pure.groups.admin.F.Actions.avatarUpload.preview.isPossible === true){
                                            //Crop without upload on server
                                            pure.groups.admin.F.Status.                         busy        (instance.button                );
                                            pure.groups.admin.F.Actions.avatarUpload.preview.   attachCrop  (id, image                      );
                                            pure.groups.admin.F.Actions.avatarUpload.preview.   make        (instance.input.files[0], image );
                                            pure.groups.admin.F.Actions.avatarUpload.stages.    set         (id, 'crop'                     );
                                            return true;
                                        }else{
                                            //Upload image on server before crop
                                            pure.groups.admin.F.Actions.avatarUpload.request.send(id);
                                            return true;
                                        }
                                    }
                                }
                                message('You cannot do that', 'Sorry you can use only GIF, PNG, JPEG or JPG.', instance.button, null);
                                return false;
                            }
                            message('You cannot do that', 'Sorry you can choose only one file.', instance.button, null);
                            return false;
                        }
                        message('Error', 'Sorry some error with your browser. Could not get file name', instance.button, null);
                    }
                    return false;
                },
                request     : {
                    send        : function(id){
                        var instance    = pure.groups.admin.F.data.get(id),
                            coords      = pure.groups.admin.F.Actions.avatarUpload.preview.getCroppedCoords(id);
                        if (instance !== null){
                            pure.groups.admin.F.Actions.avatarUpload.preview.destroyCrop(id);
                            pure.groups.admin.F.Status.busy(instance.button);
                            if (typeof pure.templates.progressbar[instance.progress] === 'object'){
                                instance.progressNode = pure.templates.progressbar[instance.progress].show(instance.button);
                            }
                            pure.components.uploader.module.upload(
                                instance.input.files[0],
                                instance.destination,
                                {
                                    ready : function(params){
                                        pure.groups.admin.F.Actions.avatarUpload.request.received(params, id);
                                    },
                                    error : function(params){
                                        pure.groups.admin.F.Actions.avatarUpload.request.error(params, id);
                                    },
                                    timeout : function(params){
                                        pure.groups.admin.F.Actions.avatarUpload.request.error(params, id);
                                    }
                                },
                                null,
                                'file',
                                [
                                    { name:'command',   value: 'templates_of_groups_set_group_avatar'               },
                                    { name:'user',      value: instance.user                                        },
                                    { name:'group',     value: instance.group                                       },
                                    { name:'x',         value: (coords !== null ? coords.x.toString()  : '-1' )     },
                                    { name:'y',         value: (coords !== null ? coords.y.toString()  : '-1' )     },
                                    { name:'height',    value: (coords !== null ? coords.h.toString()  : '-1' )     },
                                    { name:'width',     value: (coords !== null ? coords.w.toString()  : '-1' )     },
                                    { name:'path',      value: (instance.path === null ? '' : instance.path)        }
                                ]
                            );
                        }
                    },
                    received    : function(params, id){
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
                        var instance = pure.groups.admin.F.data.get(id),
                            message  = pure.groups.admin.F.Actions.message,
                            response = parseResponse(params);
                        if (instance !== null && response !== null){
                            if (instance.progressNode !== null){
                                pure.templates.progressbar[instance.progress].hide(instance.progressNode);
                                instance.progressNode = null;
                            }
                            pure.groups.admin.F.Status.clear(instance.button);
                            if (typeof response.url === 'string' && typeof response.message === 'string'){
                                if (response.url !== '' && (response.message === 'success' || response.message === 'ready_for_crop')){
                                    //Success
                                    switch (pure.groups.admin.F.Actions.avatarUpload.stages.get(id)){
                                        case 'choose':
                                            if (response.message === 'ready_for_crop'){
                                                //Image wasn't cropped. Have to cropped it now
                                                message('Next step', 'You changed group avatar, but you have to cropped it also. Press "OK" to do it.', instance.button,
                                                    function(){
                                                        var image = pure.groups.admin.F.Actions.avatarUpload.preview.update (id);
                                                        pure.groups.admin.F.Actions.avatarUpload.preview.   attachCrop  (id, image                  );
                                                        pure.groups.admin.F.Actions.avatarUpload.preview.   load        (id, response.url, image    );
                                                        pure.groups.admin.F.Actions.avatarUpload.stages.    set         (id, 'crop'                 );
                                                        instance.path = (typeof response.path === 'string' ? response.path : null);
                                                    }
                                                );
                                                return true;
                                            }
                                            break;
                                        case 'crop':
                                            if (response.message === 'success'){
                                                //Image was cropped and ready
                                                pure.groups.admin.F.Actions.avatarUpload.preview.   load        (id, response.url, null     );
                                                pure.groups.admin.F.Actions.avatarUpload.stages.    set         (id, 'choose'               );
                                                pure.groups.admin.F.Actions.avatarUpload.preview.   updateGlobal(id, response.url           );
                                                message('Successful operation', 'You changed group avatar', instance.button, null   );
                                                instance.path = null;
                                                return true;
                                            }
                                            break;
                                    }
                                }else{
                                    if (pure.groups.admin.F.Actions.common.permissionMessages(response.message, instance.button) === false){
                                        switch (response.message){
                                            case 'error_during_saving':
                                                message('Fail operation', 'Sorry, some error is on server side. Please, try again later', instance.button, null   );
                                                break;
                                            case 'too_large_filesize':
                                                message('Fail operation', 'Sorry, your file is too large.', instance.button, null   );
                                                break;
                                        }
                                    }
                                    pure.groups.admin.F.Actions.avatarUpload.stages.set(id, 'choose');
                                }
                            }else{
                                message('Fail operation', 'Sorry, but server has returned wrong information. Please try again later.', instance.button, null   );
                            }
                        }
                    },
                    error       : function(params, id){
                        var instance    = pure.groups.admin.F.data.get(id),
                            message     = pure.groups.admin.F.Actions.message;
                        if (instance !== null) {
                            pure.groups.admin.F.Status.clear(instance.button);
                            if (instance.progressNode !== null) {
                                pure.templates.progressbar[instance.progress].hide(instance.progressNode);
                                instance.progressNode = null;
                            }
                            message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.', instance.button, null   );
                        }

                    }
                },
                preview     : {
                    get isPossible(){
                        return (window.FileReader === undefined ? false : true);
                    },
                    make                : function(file, image){
                        var fileReader = null;
                        if (window.FileReader !== undefined){
                            fileReader = new FileReader();
                            pure.events.add(fileReader, 'load',
                                function(event){
                                    var _image  = new Image();
                                    _image.src  = event.target.result;
                                    image.src   = _image.src;
                                });
                            fileReader.readAsDataURL(file);
                            return true;
                        }
                        return false;
                    },
                    load                : function(id, url, image){
                        var instance    = pure.groups.admin.F.data.get(id),
                            image       = (typeof image !== 'undefined' ? image : null);
                        if (instance !== null){
                            image       = (image === null ? pure.groups.admin.F.Actions.avatarUpload.preview.update(id) : image);
                            image.src   = url;
                        }
                    },
                    update              : function(id){
                        var image   = pure.groups.admin.F.Actions.avatarUpload.preview.getImage(id),
                            _image  = document.createElement('IMG');
                        if (image !== null){
                            _image.setAttribute('data-engine-id', id);
                            _image.setAttribute('data-element-type','Pure.Social.Groups.Item.Details.Controls.GroupAvatar');
                            image.parentNode.insertBefore(_image, image);
                            image.parentNode.removeChild(image);
                            image = null;
                            return _image;
                        }
                        return null;
                    },
                    getImage            : function(id){
                        return pure.nodes.select.first('img[data-engine-id="' + id + '"]');
                    },
                    destroyCrop         : function(id){
                        var instance = pure.groups.admin.F.data.get(id);
                        if (instance !== null) {
                            pure.components.crop.module.methods.destroy(id);
                        }
                    },
                    attachCrop          : function(id, image){
                        var instance = pure.groups.admin.F.data.get(id);
                        if (instance !== null){
                            pure.groups.admin.F.Actions.avatarUpload.preview.destroyCrop(id);
                            pure.events.add(image, 'load',
                                function(){
                                    pure.components.crop.module.methods.attach({
                                        target      : image,
                                        selection   : {
                                            x:0,
                                            y:0,
                                            w:100,
                                            h:100
                                        },
                                        ratio       : 1,
                                        id          : instance.id
                                    });
                                    pure.groups.admin.F.Status.clear(instance.button);
                                }
                            );
                        }
                    },
                    getCroppedCoords    : function(id){
                        var instance = pure.groups.admin.F.data.get(id),
                            coords   = null;
                        if (instance !== null){
                            coords = pure.components.crop.module.methods.getSelection(id);
                        }
                        return coords;
                    },
                    updateGlobal        : function(id, url){
                        var icon = pure.nodes.select.first('*[data-engine-group_avatar="' + id + '"]');
                        if (icon !== null){
                            icon.style.backgroundImage = 'url(' + url + ')';
                        }
                        return null;
                    }
                },
                stages  : {
                    data : {
                        choose  : 'Update group avatar',
                        crop    : 'Crop image and save'
                    },
                    set : function(id, stage){
                        var data        = pure.groups.admin.F.Actions.avatarUpload.stages.data,
                            instance    = pure.groups.admin.F.data.get(id);
                        if (typeof data[stage] === 'string' && instance !== null){
                            instance.button.innerHTML   = data[stage];
                            instance.stage              = stage;
                            return true;
                        }
                        return false;
                    },
                    get : function(id){
                        var instance    = pure.groups.admin.F.data.get(id);
                        if (instance !== null){
                            return instance.stage;
                        }
                        return false;
                    }
                }
            },
            basicSettingsSave   : {
                onClick : function(event, button, params){
                    var name            = null,
                        description     = null,
                        notifications   = null,
                        message         = pure.groups.admin.F.Actions.message;
                    if (pure.groups.admin.F.Status.isBusy(button) === false){
                        name            = pure.nodes.select.first('textarea[data-engine-id="'   + params.id + '"][data-engine-element="group_basic_settings_name"]');
                        description     = pure.nodes.select.first('textarea[data-engine-id="'   + params.id + '"][data-engine-element="group_basic_settings_description"]');
                        notifications   = pure.nodes.select.first('input[data-engine-id="'      + params.id + '"][data-engine-element="group_basic_settings_notifications"]');
                        if (name !== null && description !== null && notifications !== null){
                            if (name.value === ''){
                                message('Wrong data', 'You should write some name of group.', button, null); return false;
                            }
                            if (description.value === ''){
                                message('Wrong data', 'You should write some description of group.', button, null); return false;
                            }
                            pure.groups.admin.F.Actions.basicSettingsSave.request.send(params, button, name.value, description.value, notifications.checked);
                        }
                    }
                },
                request : {
                    send        : function(params, button, name, description, notifications){
                        var parameters      = '',
                            progressNode    = null;
                        if (typeof pure.templates.progressbar[params.progress] === 'object'){
                            progressNode = pure.templates.progressbar[params.progress].show(button);
                        }
                        pure.groups.admin.F.Status.busy(button);
                        parameters =    'command'       + '=' + 'templates_of_groups_set_basic_settings'    + '&'+
                                        'user'          + '=' + params.user                                 + '&'+
                                        'group'         + '=' + params.group                                + '&'+
                                        'name'          + '=' + name                                        + '&'+
                                        'description'   + '=' + description                                 + '&'+
                                        'notifications' + '=' + notifications;
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : params.destination,
                            request     : parameters,
                            onrecieve   : function (id_request, response) {
                                pure.groups.admin.F.Actions.basicSettingsSave.request.received(id_request, response, params, button, progressNode, name, description);
                            },
                            onreaction  : null,
                            onerror     : function (id_request) {
                                pure.groups.admin.F.Actions.basicSettingsSave.request.error(id_request, params, button, progressNode);
                            },
                            ontimeout   : function (id_request) {
                                pure.groups.admin.F.Actions.basicSettingsSave.request.error(id_request, params, button, progressNode);
                            }
                        });
                    },
                    received    : function(id_request, response, params, button, progressNode, name, description){
                        var message  = pure.groups.admin.F.Actions.message;
                        if (progressNode !== null){
                            pure.templates.progressbar[params.progress].hide(progressNode);
                        }
                        pure.groups.admin.F.Status.clear(button);
                        if (pure.groups.admin.F.Actions.common.permissionMessages(response, button) === false){
                            switch (response){
                                case 'no_name':
                                    message('Fail operation', 'You did not define name of group.', button, null   );
                                    break;
                                case 'no_description':
                                    message('Fail operation', 'You did not define description of group.', button, null   );
                                    break;
                                case 'fail':
                                    message('Fail operation', 'Sorry, some error on server side was. Please, try later.', button, null   );
                                    break;
                                case 'success':
                                    message('Successful operation', 'Name and description of group are updated.', button, null   );
                                    pure.groups.admin.F.Actions.basicSettingsSave.update(params.id, name, description);
                                    break;
                            }
                        }
                    },
                    error       : function(id_request, params, button, progressNode){
                        var message  = pure.groups.admin.F.Actions.message;
                        if (progressNode !== null){
                            pure.templates.progressbar[params.progress].hide(progressNode);
                        }
                        pure.groups.admin.F.Status.clear(button);
                        message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.', button, null   );
                    }
                },
                update  : function(id, name, description){
                    var node_name           = pure.nodes.select.first('*[data-engine-group_name="'          + id + '"]'),
                        node_description    = pure.nodes.select.first('*[data-engine-group_description="'   + id + '"]');
                    if (node_name !== null && node_description !== null){
                        node_name.          innerHTML = name;
                        node_description.   innerHTML = description;
                    }

                }
            },
            visibilitySetting   : {
                onClick : function(event, button, params){
                    var nodes           = null,
                        status          = null,
                        invite_status   = null;
                    if (pure.groups.admin.F.Status.isBusy(button) === false) {
                        nodes = {
                            status : {
                                _public  : pure.nodes.select.first('input[name="' + params.id + 'group-status"][value="public"]'),
                                _private : pure.nodes.select.first('input[name="' + params.id + 'group-status"][value="private"]'),
                                _hidden  : pure.nodes.select.first('input[name="' + params.id + 'group-status"][value="hidden"]')
                            },
                            invites : {
                                members : pure.nodes.select.first('input[name="' + params.id + 'group-invite-status"][value="members"]'),
                                mods    : pure.nodes.select.first('input[name="' + params.id + 'group-invite-status"][value="mods"]'),
                                admins  : pure.nodes.select.first('input[name="' + params.id + 'group-invite-status"][value="admins"]')
                            }
                        };
                        if (pure.tools.objects.validate(nodes.status, [ { name: "_public",  type: "node" },
                                                                        { name: "_private", type: "node" },
                                                                        { name: "_hidden",  type: "node" }]) === true) {
                            if (pure.tools.objects.validate(nodes.invites, [{ name: "members",  type: "node" },
                                                                            { name: "mods",     type: "node" },
                                                                            { name: "admins",   type: "node" }]) === true) {
                                status = (nodes.status._public. checked === true ? 'public'     : status);
                                status = (nodes.status._private.checked === true ? 'private'    : status);
                                status = (nodes.status._hidden. checked === true ? 'hidden'     : status);
                                invite_status = (nodes.invites.members. checked === true ? 'members'    : invite_status);
                                invite_status = (nodes.invites.mods.    checked === true ? 'mods'       : invite_status);
                                invite_status = (nodes.invites.admins.  checked === true ? 'admins'     : invite_status);
                                if (status !== null && invite_status !== null){
                                    pure.groups.admin.F.Actions.visibilitySetting.request.send(button, params, status, invite_status);
                                }
                            }
                        }
                    }
                },
                request : {
                    send        : function(button, params, status, invite_status){
                        var parameters      = '',
                            progressNode    = null;
                        if (typeof pure.templates.progressbar[params.progress] === 'object'){
                            progressNode = pure.templates.progressbar[params.progress].show(button);
                        }
                        pure.groups.admin.F.Status.busy(button);
                        parameters =    'command'       + '=' + 'templates_of_groups_set_visibility_settings'   + '&'+
                                        'user'          + '=' + params.user                                     + '&'+
                                        'group'         + '=' + params.group                                    + '&'+
                                        'status'        + '=' + status                                          + '&'+
                                        'invite_status' + '=' + invite_status;
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : params.destination,
                            request     : parameters,
                            onrecieve   : function (id_request, response) {
                                pure.groups.admin.F.Actions.visibilitySetting.request.received(id_request, response, params, button, progressNode);
                            },
                            onreaction  : null,
                            onerror     : function (id_request) {
                                pure.groups.admin.F.Actions.visibilitySetting.request.error(id_request, params, button, progressNode);
                            },
                            ontimeout   : function (id_request) {
                                pure.groups.admin.F.Actions.visibilitySetting.request.error(id_request, params, button, progressNode);
                            }
                        });
                    },
                    received    : function(id_request, response, params, button, progressNode){
                        var message  = pure.groups.admin.F.Actions.message;
                        if (progressNode !== null){
                            pure.templates.progressbar[params.progress].hide(progressNode);
                        }
                        pure.groups.admin.F.Status.clear(button);
                        if (pure.groups.admin.F.Actions.common.permissionMessages(response, button) === false){
                            switch (response){
                                case 'no_status':
                                    message('Fail operation', 'Request has not information about status of group. Bad request data.', button, null   );
                                    break;
                                case 'no_invite_status':
                                    message('Fail operation', 'Request has not information about procedure of invitations. Bad request data.', button, null   );
                                    break;
                                case 'bad_status':
                                    message('Fail operation', 'Invalid value of group status.', button, null   );
                                    break;
                                case 'bad_invite_status':
                                    message('Fail operation', 'Invalid value of procedure of invitations.', button, null   );
                                    break;
                                case 'error':
                                    message('Fail operation', 'Sorry, some error on server side was. Please, try later.', button, null   );
                                    break;
                                case 'fail':
                                    message('Fail operation', 'Sorry, some error on server side was. Please, try later.', button, null   );
                                    break;
                                case 'success':
                                    message('Successful operation', 'Status of group and procedure of invitation are updated.', button, null   );
                                    break;
                            }
                        }
                    },
                    error       : function(id_request, params, button, progressNode){
                        var message  = pure.groups.admin.F.Actions.message;
                        if (progressNode !== null){
                            pure.templates.progressbar[params.progress].hide(progressNode);
                        }
                        pure.groups.admin.F.Status.clear(button);
                        message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.', button, null   );
                    }
                }
            },
            membersSettings     : {
                onClick : function(event, button, params, type){
                    var question    = pure.groups.admin.F.Actions.question,
                        state       = button.getAttribute('data-engine-state');
                    if (pure.groups.admin.F.Status.isBusy(button) === false) {
                        switch (type){
                            case 'admin':
                                if (state === 'active'){
                                    question(
                                        'Please confirm operation',
                                        'Do you really want <strong>remove</strong> administration rights of this user?',
                                        null,
                                        {
                                            yes : function(){ pure.groups.admin.F.Actions.membersSettings.request.send(button, params, type); },
                                            no  : null
                                        }
                                    );
                                }else{
                                    question(
                                        'Please confirm operation',
                                        'Do you really want <strong>give</strong> administration rights to this user?',
                                        null,
                                        {
                                            yes : function(){ pure.groups.admin.F.Actions.membersSettings.request.send(button, params, type); },
                                            no  : null
                                        }
                                    );
                                }
                                break;
                            case 'mod':
                                if (state === 'active'){
                                    question(
                                        'Please confirm operation',
                                        'Do you really want <strong>remove</strong> moderation rights of this user?',
                                        null,
                                        {
                                            yes : function(){ pure.groups.admin.F.Actions.membersSettings.request.send(button, params, type); },
                                            no  : null
                                        }
                                    );
                                }else{
                                    question(
                                        'Please confirm operation',
                                        'Do you really want <strong>give</strong> moderation rights to this user?',
                                        null,
                                        {
                                            yes : function(){ pure.groups.admin.F.Actions.membersSettings.request.send(button, params, type); },
                                            no  : null
                                        }
                                    );
                                }
                                break;
                            case 'ban':
                                if (state === 'active'){
                                    question(
                                        'Please confirm operation',
                                        'Do you really want <strong>cancel banning</strong> of this user?',
                                        null,
                                        {
                                            yes : function(){ pure.groups.admin.F.Actions.membersSettings.request.send(button, params, type); },
                                            no  : null
                                        }
                                    );
                                }else{
                                    question(
                                        'Please confirm operation',
                                        'Do you really want <strong>ban</strong> this user?',
                                        null,
                                        {
                                            yes : function(){ pure.groups.admin.F.Actions.membersSettings.request.send(button, params, type); },
                                            no  : null
                                        }
                                    );
                                }
                                break;
                            case 'remove':
                                question(
                                    'Please confirm operation',
                                    'Do you really want <strong>remove</strong> this user from group? Be careful you cannot cancel this oparation. If you choose "continue", user will be removed from group.',
                                    null,
                                    {
                                        yes : function(){ pure.groups.admin.F.Actions.membersSettings.request.send(button, params, type); },
                                        no  : null
                                    }
                                );
                                break;
                            case 'admonition':
                                question(
                                    'Please confirm operation',
                                    'Do you really want <strong>make admonition</strong> to this user? There are no way back. You can make admonition, but cannot remove it. It will be forever.',
                                    null,
                                    {
                                        yes : function(){ pure.groups.admin.F.Actions.membersSettings.request.send(button, params, type); },
                                        no  : null
                                    }
                                );
                                break;
                        }
                    }
                },
                request : {
                    send        : function(button, params, action){
                        var parameters      = '',
                            progressNode    = null;
                        if (typeof pure.templates.progressbar[params.progress] === 'object'){
                            progressNode = pure.templates.progressbar[params.progress].show(button);
                        }
                        pure.groups.admin.F.Status.busy(null, {name : 'data-engine-data-group', value: params.id + params.member});
                        parameters =    'command'       + '=' + 'templates_of_groups_member_action' + '&'+
                                        'user'          + '=' + params.user                         + '&'+
                                        'group'         + '=' + params.group                        + '&'+
                                        'target_user'   + '=' + params.member                       + '&'+
                                        'comment'       + '=' + ''                                  + '&'+
                                        'action'        + '=' + action;
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : params.destination,
                            request     : parameters,
                            onrecieve   : function (id_request, response) {
                                pure.groups.admin.F.Actions.membersSettings.request.received(id_request, response, params, button, progressNode);
                            },
                            onreaction  : null,
                            onerror     : function (id_request) {
                                pure.groups.admin.F.Actions.membersSettings.request.error(id_request, params, button, progressNode);
                            },
                            ontimeout   : function (id_request) {
                                pure.groups.admin.F.Actions.membersSettings.request.error(id_request, params, button, progressNode);
                            }
                        });
                    },
                    received    : function(id_request, response, params, button, progressNode){
                        var message     = pure.groups.admin.F.Actions.message,
                            name        = button.getAttribute('data-engine-data-name');
                        if (progressNode !== null){
                            pure.templates.progressbar[params.progress].hide(progressNode);
                        }
                        pure.groups.admin.F.Status.clear(null, {name : 'data-engine-data-group', value: params.id + params.member});
                        if (pure.groups.admin.F.Actions.common.permissionMessages(response, button) === false){
                            switch (response){
                                case 'fail':
                                    message('Fail operation', 'Sorry, some error on server side was. Please, try later.', button, null);
                                    break;
                                case 'admin_cannot_remove_admin_rights_of_himself':
                                    message('Fail operation', 'Sorry, you are administrator and you cannot remove your administration rights by yourself. Create new administrator of group and ask him do it.', button, null);
                                    break;
                                case 'moderator_cannot_be_banned':
                                    message('Fail operation', 'Sorry, but moderator (<strong>' + name + '</strong>) cannot be banned. You should remove moderation rights before.', button, null);
                                    break;
                                case 'admin_cannot_be_banned':
                                    message('Fail operation', 'Sorry, but administrator (<strong>' + name + '</strong>) cannot be banned. You should remove administration rights before.', button, null);
                                    break;
                                case 'admin_cannot_remove_himself':
                                    message('Fail operation', 'Sorry, you are administrator and you cannot remove yourself by yourself. Create new administrator of group and ask him do it.', button, null);
                                    break;
                                case 'banned_user_cannot_be_admin':
                                    message('Fail operation', 'Sorry, but <strong>' + name + '</strong> is banned. You cannot make him administrator now.', button, null);
                                    break;
                                case 'banned_user_cannot_be_moderator':
                                    message('Fail operation', 'Sorry, but <strong>' + name + '</strong>  is banned. You cannot make him moderator now.', button, null);
                                    break;
                                case 'admin_removed':
                                    button.removeAttribute('data-engine-state');
                                    pure.groups.admin.F.UpdateContent.controls.set('admins', params.resetAdmins);
                                    break;
                                case 'admin_accepted':
                                    button.setAttribute('data-engine-state', 'active');
                                    pure.groups.admin.F.UpdateContent.controls.set('admins', params.resetAdmins);
                                    break;
                                case 'mod_removed':
                                    button.removeAttribute('data-engine-state');
                                    pure.groups.admin.F.UpdateContent.controls.set('moderators', params.resetModerators);
                                    break;
                                case 'mod_accepted':
                                    button.setAttribute('data-engine-state', 'active');
                                    pure.groups.admin.F.UpdateContent.controls.set('moderators', params.resetModerators);
                                    break;
                                case 'warned':
                                    (function(node){
                                        var span    = pure.nodes.find.childByType(node, 'span'),
                                            count   = 0;
                                        if (span !== null){
                                            count = parseInt(span.innerHTML);
                                            span.innerHTML = count + 1;
                                        }
                                    }(button));
                                    break;
                                case 'unbanned':
                                    button.removeAttribute('data-engine-state');
                                    break;
                                case 'banned':
                                    button.setAttribute('data-engine-state', 'active');
                                    pure.groups.admin.F.UpdateContent.controls.set('members', params.resetMembers);
                                    break;
                                case 'removed':
                                    (function(node, name, message, params){
                                        var parent = pure.nodes.find.parentByAttr(node, { name: 'data-engine-id', value: params.id });
                                        if (parent !== null){
                                            message('Successful operation', 'User <strong>' + name + '</strong> was removed from group.', parent.parentNode, null);
                                            parent.parentNode.removeChild(parent);
                                            pure.groups.admin.F.UpdateContent.controls.set('members', params.resetMembers);
                                        }
                                    }(button, name, message, params));
                                    break;
                            }
                        }
                    },
                    error       : function(id_request, params, button, progressNode){
                        var message  = pure.groups.admin.F.Actions.message;
                        if (progressNode !== null){
                            pure.templates.progressbar[params.progress].hide(progressNode);
                        }
                        pure.groups.admin.F.Status.busy(null, {name : 'data-engine-data-group', value: params.id + params.member});
                        message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.', button, null);
                    }
                }
            },
            requestsManagement  : {
                onClick : function(event, button, params, type){
                    var question    = pure.groups.admin.F.Actions.question,
                        state       = button.getAttribute('data-engine-state');
                    if (pure.groups.admin.F.Status.isBusy(button) === false) {
                        switch (type){
                            case 'accept':
                                question(
                                    'Please confirm operation',
                                    'Do you really want <strong>allow</strong> this user join this group?',
                                    null,
                                    {
                                        yes : function(){ pure.groups.admin.F.Actions.requestsManagement.request.send(button, params, type); },
                                        no  : null
                                    }
                                );
                                break;
                            case 'deny':
                                question(
                                    'Please confirm operation',
                                    'Do you really want <strong>deny</strong> this user join this group?',
                                    null,
                                    {
                                        yes : function(){ pure.groups.admin.F.Actions.requestsManagement.request.send(button, params, type); },
                                        no  : null
                                    }
                                );
                                break;
                        }
                    }
                },
                request : {
                    send        : function(button, params, action){
                        var parameters      = '',
                            progressNode    = null;
                        if (typeof pure.templates.progressbar[params.progress] === 'object'){
                            progressNode = pure.templates.progressbar[params.progress].show(button);
                        }
                        pure.groups.admin.F.Status.busy(null, {name : 'data-engine-data-request', value: params.request});
                        parameters =    'command'       + '=' + 'templates_of_groups_request_action'    + '&'+
                                        'user'          + '=' + params.user                             + '&'+
                                        'group'         + '=' + params.group                            + '&'+
                                        'waited_user'   + '=' + params.member                           + '&'+
                                        'request_id'    + '=' + params.request                          + '&'+
                                        'action'        + '=' + action;
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : params.destination,
                            request     : parameters,
                            onrecieve   : function (id_request, response) {
                                pure.groups.admin.F.Actions.requestsManagement.request.received(id_request, response, params, button, progressNode);
                            },
                            onreaction  : null,
                            onerror     : function (id_request) {
                                pure.groups.admin.F.Actions.requestsManagement.request.error(id_request, params, button, progressNode);
                            },
                            ontimeout   : function (id_request) {
                                pure.groups.admin.F.Actions.requestsManagement.request.error(id_request, params, button, progressNode);
                            }
                        });
                    },
                    received    : function(id_request, response, params, button, progressNode){
                        function removeRequest(node){
                            var parent = pure.nodes.find.parentByAttr(node, { name: 'data-engine-id', value: params.id });
                            if (parent !== null){
                                parent.parentNode.removeChild(parent);
                            }
                        };
                        var message     = pure.groups.admin.F.Actions.message,
                            name        = button.getAttribute('data-engine-data-name');
                        if (progressNode !== null){
                            pure.templates.progressbar[params.progress].hide(progressNode);
                        }
                        pure.groups.admin.F.Status.clear(null, {name : 'data-engine-data-request', value: params.request});
                        if (pure.groups.admin.F.Actions.common.permissionMessages(response, button) === false){
                            switch (response){
                                case 'fail':
                                    message('Fail operation', 'Sorry, some error on server side was. Please, try later.', button, null);
                                    break;
                                case 'error':
                                    message('Fail operation', 'Sorry, some error on server side was. Please, try later.', button, null);
                                    break;
                                case 'accepted':
                                    removeRequest(button);
                                    break;
                                case 'denied':
                                    removeRequest(button);
                                    break;
                            }
                        }
                    },
                    error       : function(id_request, params, button, progressNode){
                        var message  = pure.groups.admin.F.Actions.message;
                        if (progressNode !== null){
                            pure.templates.progressbar[params.progress].hide(progressNode);
                        }
                        pure.groups.admin.F.Status.busy(null, {name : 'data-engine-data-request', value: params.request});
                        message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.', button, null);
                    }
                }
            },
            invitesManagement   : {
                onClick : function(event, button, params, type){
                    var question = pure.groups.admin.F.Actions.question;
                    if (pure.groups.admin.F.Status.isBusy(button) === false) {
                        switch (type){
                            case 'invite':
                                question(
                                    'Please confirm operation',
                                    'Please confirm what you want <strong>invite</strong> all selected users?',
                                    null,
                                    {
                                        yes : function(){ pure.groups.admin.F.Actions.invitesManagement.request.send(button, params, type); },
                                        no  : null
                                    }
                                );
                                break;
                            case 'reject':
                                question(
                                    'Please confirm operation',
                                    'Are you sure what you want <strong>reject</strong> your invitation to join this group?',
                                    null,
                                    {
                                        yes : function(){ pure.groups.admin.F.Actions.invitesManagement.request.send(button, params, type); },
                                        no  : null
                                    }
                                );
                                break;
                        }
                    }
                },
                request : {
                    reverse     : function(params, users_IDs){
                        var invite  = null,
                            reject  = null,
                            state   = null;
                        for(var index = users_IDs.length - 1; index >= 0; index -= 1){
                            invite = pure.nodes.select.first('*[data-engine-id="' + params.id + params.group + '"][data-engine-data-member="' + users_IDs[index] + '"][data-engine-action="invite"]');
                            reject = pure.nodes.select.first('*[data-engine-id="' + params.id + params.group + '"][data-engine-data-member="' + users_IDs[index] + '"][data-engine-action="reject"]');
                            if (invite !== null && reject !== null){
                                state = invite.getAttribute('data-engine-state');
                                if (state !== null){
                                    if (state !== ''){
                                        if (state === 'show'){
                                            invite.setAttribute('data-engine-state', 'hide');
                                            reject.setAttribute('data-engine-state', 'show');
                                        }else{
                                            invite.setAttribute('data-engine-state', 'show');
                                            reject.setAttribute('data-engine-state', 'hide');
                                        }
                                    }
                                }
                            }
                        }
                    },
                    send        : function(button, params, action){
                        var parameters      = '',
                            progressNode    = null,
                            inputs          = null,
                            users           = [],
                            user_id         = null,
                            input           = null,
                            message         = pure.groups.admin.F.Actions.message,
                            members         = null;
                        if (typeof pure.templates.progressbar[params.progress] === 'object'){
                            progressNode = pure.templates.progressbar[params.progress].show(button);
                        }
                        pure.groups.admin.F.Status.busy(button);
                        switch (action){
                            case 'invite':
                                inputs = pure.nodes.select.all('*[data-engine-id="' + params.id + params.group + '"][data-engine-action="invite"][data-engine-state="show"]:not([data-type-element-inited])');
                                if (inputs !== null){
                                    for(var index = inputs.length - 1; index >= 0; index -= 1){
                                        input = pure.nodes.find.childByType(inputs[index], 'input');
                                        if (input !== null){
                                            if(input.checked === true){
                                                user_id = inputs[index].getAttribute('data-engine-data-member');
                                                if (user_id !== null){
                                                    if (user_id !== ''){
                                                        users.push(user_id);
                                                        input.checked = false;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if (users.length === 0){
                                    if (typeof pure.templates.progressbar[params.progress] === 'object'){
                                         pure.templates.progressbar[params.progress].hide(progressNode);
                                    }
                                    pure.groups.admin.F.Status.clear(button);
                                    message('Cannot do it', 'Please select users which should get your invitation.', button, null);
                                    return false;
                                }
                                members = users.join(',');
                                break;
                            case 'reject':
                                user_id = button.getAttribute('data-engine-data-member');
                                if (user_id !== null){
                                    if (user_id !== ''){
                                        members = user_id;
                                        users.push(user_id)
                                    }
                                }
                                if (users.length === 0){
                                    if (typeof pure.templates.progressbar[params.progress] === 'object'){
                                        pure.templates.progressbar[params.progress].hide(progressNode);
                                    }
                                    pure.groups.admin.F.Status.clear(button);
                                    message('Cannot do it', 'Sorry, but here is some error on client side. Please contact with administrator.', button, null);
                                    return false;
                                }
                                break;
                        }
                        parameters =    'command'       + '=' + 'templates_of_groups_invite_action'     + '&'+
                                        'user'          + '=' + params.user                             + '&'+
                                        'group'         + '=' + params.group                            + '&'+
                                        'members'       + '=' + members                                 + '&'+
                                        'action'        + '=' + action;
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : params.destination,
                            request     : parameters,
                            onrecieve   : function (id_request, response) {
                                pure.groups.admin.F.Actions.invitesManagement.request.received(id_request, response, params, button, progressNode, users);
                            },
                            onreaction  : null,
                            onerror     : function (id_request) {
                                pure.groups.admin.F.Actions.invitesManagement.request.error(id_request, params, button, progressNode);
                            },
                            ontimeout   : function (id_request) {
                                pure.groups.admin.F.Actions.invitesManagement.request.error(id_request, params, button, progressNode);
                            }
                        });
                    },
                    received    : function(id_request, response, params, button, progressNode, users){
                        var message     = pure.groups.admin.F.Actions.message;
                        if (progressNode !== null){
                            pure.templates.progressbar[params.progress].hide(progressNode);
                        }
                        pure.groups.admin.F.Status.clear(button);
                        if (pure.groups.admin.F.Actions.common.permissionMessages(response, button) === false){
                            switch (response){
                                case 'fail':
                                    message('Fail operation', 'Sorry, some error on server side was. Please, try later.', button, null);
                                    break;
                                case 'error':
                                    message('Fail operation', 'Sorry, some error on server side was. Please, try later.', button, null);
                                    break;
                                case 'invited':
                                    pure.groups.admin.F.Actions.invitesManagement.request.reverse(params, users);
                                    break;
                                case 'rejected':
                                    pure.groups.admin.F.Actions.invitesManagement.request.reverse(params, users);
                                    break;
                            }
                        }
                    },
                    error       : function(id_request, params, button, progressNode){
                        var message  = pure.groups.admin.F.Actions.message;
                        if (progressNode !== null){
                            pure.templates.progressbar[params.progress].hide(progressNode);
                        }
                        pure.groups.admin.F.Status.busy(button);
                        message('Fail operation', 'Sorry, cannot get answer from server. Please, try later.', button, null);
                    }
                }
            },
            message         : function(title, message, node, handle, template){
                var template = (typeof template === 'string' ? template : 'A');
                pure.components.dialogs[template].open({
                    title       : title,
                    innerHTML   : '<p>' + message + '</p>',
                    buttons     : [
                        {
                            title   : 'OK',
                            handle  : handle
                        }
                    ],
                    parent      : pure.nodes.find.parentByAttr(node, {name :'data-engine-element', value:'dialog_parent'}),
                    width       : 70
                });
            },
            question        : function(title, message, node, handles, template){
                var template = (typeof template === 'string' ? template : 'B');
                pure.components.dialogs[template].open({
                    title       : title,
                    innerHTML   : '<p>' + message + '</p>',
                    buttons     : [
                        {
                            title   : 'cancel',
                            handle  : handles.no
                        },
                        {
                            title   : 'continue',
                            handle  : handles.yes
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
        UpdateContent : {
            storage     : {},
            controls    : {
                set     : function(key, id){
                    var storage         = pure.groups.admin.F.UpdateContent.storage;
                    if (typeof key === 'string' && typeof id === 'string'){
                        storage[key]        = (typeof storage[key] !== 'object' ? {} : storage[key]);
                        storage[key][id]    = true;
                    }
                },
                get     : function(key, id){
                    var storage     = pure.groups.admin.F.UpdateContent.storage;
                    if (typeof key === 'string' && typeof id === 'string'){
                        storage[key]    = (typeof storage[key] !== 'object' ? {} : storage[key]);
                        return (typeof storage[key][id] === 'boolean' ? storage[key][id] : false);
                    }
                    return false;
                },
                clear   : function(key, id){
                    var storage         = pure.groups.admin.F.UpdateContent.storage;
                    if (typeof key === 'string' && typeof id === 'string'){
                        storage[key]        = (typeof storage[key] !== 'object' ? {} : storage[key]);
                        storage[key][id]    = null;
                        delete storage[key][id];
                    }
                }
            },
            events : {
                attach : function(){
                    var instances = pure.nodes.select.all('*[data-engine-element="group_container"]:not([data-type-element-inited])');
                    if (instances !== null) {
                        if (typeof instances.length === "number") {
                            for (var index = instances.length - 1; index >= 0; index -= 1) {
                                (function(node){
                                    var values  = node.getAttribute('data-engine-data-IDs'),
                                        keys    = node.getAttribute('data-engine-data-IDsKeys'),
                                        input   = null;
                                    values  = (typeof values    === 'string' ? (values  !== '' ? values : null) : null);
                                    keys    = (typeof keys      === 'string' ? (keys    !== '' ? keys   : null) : null);
                                    if (values !== null && keys !== null){
                                        values  = values.   split(',');
                                        keys    = keys.     split(',');
                                        if (values.length === keys.length){
                                            for (var index = values.length - 1; index >= 0; index -= 1){
                                                input = pure.nodes.select.first('input[id="' + values[index] + '"]:not([data-type-element-inited])');
                                                if (input !== null){
                                                    (function(input, key, id){
                                                        pure.events.add(input, 'change', function(event){
                                                            pure.groups.admin.F.UpdateContent.events.onChange(event, input, key, id);
                                                        });
                                                        input.setAttribute('data-type-element-inited', 'true');
                                                    }(input, keys[index], values[index]));
                                                }
                                            }
                                        }
                                        node.setAttribute('data-type-element-inited', 'true');
                                    }
                                }(instances[index]));
                            }
                        }
                    }
                },
                onChange : function(event, input, key, id){
                    var storage = pure.groups.admin.F.UpdateContent.controls;
                    if (input.checked === true){
                        if (storage.get(key, id) === true){
                            pure.appevents.Actions.call('pure.more', 'reset', id, null);
                            storage.clear(key, id);
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
                        member          : node.getAttribute('data-engine-data-member'       ),
                        request         : node.getAttribute('data-engine-data-request'      ),
                        group           : node.getAttribute('data-engine-data-group'        ),
                        progress        : node.getAttribute('data-engine-data-progress'     ),
                        destination     : node.getAttribute('data-engine-data-destination'  ),
                        resetMembers    : node.getAttribute('data-engine-reset-members'     ),
                        resetAdmins     : node.getAttribute('data-engine-reset-admins'      ),
                        resetModerators : node.getAttribute('data-engine-reset-moderators'  )
                    };
                for(var key in params){
                    params[key] = (typeof params[key] === 'string' ? (params[key] !== '' ? params[key] : null) : null);
                }
                return params;
            }
        },
        More    : {
            initialized : false,
            init        : function(){
                if (pure.groups.admin.F.More.initialized === false){
                    pure.appevents.Events.methods.register('pure.more', 'done');
                    pure.appevents.Actions.listen('pure.more', 'done', function(){ pure.groups.admin.F.init(); }, 'pure.groups.admin.F.init');
                    pure.groups.admin.F.More.initialized = true;
                }
            }
        }
    };
    pure.system.start.add(pure.groups.admin.F.init);
}());