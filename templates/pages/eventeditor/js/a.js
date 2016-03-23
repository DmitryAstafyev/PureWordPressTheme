(function () {
    if (typeof window.pure      !== "object") { window.pure         = {}; }
    if (typeof window.pure.post !== "object") { window.pure.post    = {}; }
    "use strict";
    window.pure.post.create = {
        map         : {
            init : function(){
                var buttons = pure.nodes.select.all('*[data-create-event-engine-element="map.search.button"]:not([data-engine-element-inited])');
                if (buttons !== null){
                    for (var index = buttons.length - 1; index >= 0; index -= 1){
                        (function(button){
                            var id      = button.getAttribute('data-create-event-engine-id'),
                                field   = null;
                            if (id !== null){
                                field = pure.nodes.select.first('*[data-create-event-engine-element="map.search.field"][data-create-event-engine-id="' + id + '"]');
                                if (field !== null){
                                    pure.events.add(
                                        button,
                                        'click',
                                        function(){
                                            pure.post.create.map.search(id, field);
                                        }
                                    );
                                }
                            }
                            button.setAttribute('data-engine-element-inited', 'true');
                        }(buttons[index]));
                    }
                }
            },
            search : function(id, field){
                var value = null;
                if (pure.system.getInstanceByPath('pure.components.maps.google') !== null){
                    value = (typeof field.value === 'string' ? field.value : null);
                    if (value !== null){
                        if (value.length > 2){
                            pure.components.maps.google.search(
                                id,
                                value,
                                {
                                    success : function(params){
                                        pure.post.create.map.callback.success(params, field);
                                    },
                                    fail    : function(params){
                                        pure.post.create.map.callback.fail(params, field);
                                    }
                                }
                            );
                        }
                    }
                }
            },
            callback : {
                success : function(params, field){
                    field.removeAttribute('data-bad-place-on-map');
                },
                fail    : function(params, field){
                    field.setAttribute('data-bad-place-on-map', 'true');
                }
            }
        },
        controls    : {
            init    : function(){
                var nodes = {
                        form    : pure.nodes.select.first('form[data-create-post-engine-element="form"]'        ),
                        publish : pure.nodes.select.first('*[data-create-post-engine-element="button.publish"]' ),
                        draft   : pure.nodes.select.first('*[data-create-post-engine-element="button.draft"]'   ),
                        preview : pure.nodes.select.first('*[data-create-post-engine-element="button.preview"]' ),
                        update  : pure.nodes.select.first('*[data-create-post-engine-element="button.update"]'  ),
                        remove  : pure.nodes.select.first('*[data-create-post-engine-element="button.remove"]'  ),
                        action  : pure.nodes.select.first('*[data-create-post-engine-element="action"]'         )
                    },
                    fields = {
                        header          : pure.nodes.select.first('textarea[name="post_title"]'                     ),
                        content         : pure.nodes.select.first('textarea[name="post_content"]'                   ),
                        quote           : pure.nodes.select.first('textarea[name="post_excerpt"]'                   ),
                        miniature       : pure.nodes.select.first('input[name="post_miniature"]'                    ),
                        categories      : pure.nodes.select.all('input[name="post_category"]'                       ),
                        event           : {
                            start : {
                                dd  : pure.nodes.select.first('select[name="event_start_day"]'                      ),
                                mm  : pure.nodes.select.first('select[name="event_start_month"]'                    ),
                                yy  : pure.nodes.select.first('select[name="event_start_year"]'                     ),
                                h   : pure.nodes.select.first('select[name="event_start_hour"]'                     ),
                                m   : pure.nodes.select.first('select[name="event_start_minute"]'                   )
                            },
                            finish : {
                                dd  : pure.nodes.select.first('select[name="event_finish_day"]'                     ),
                                mm  : pure.nodes.select.first('select[name="event_finish_month"]'                   ),
                                yy  : pure.nodes.select.first('select[name="event_finish_year"]'                    ),
                                h   : pure.nodes.select.first('select[name="event_finish_hour"]'                    ),
                                m   : pure.nodes.select.first('select[name="event_finish_minute"]'                  )
                            }
                        },
                        registration    : {
                            start   : {
                                dd  : pure.nodes.select.first('select[name="event_registration_start_day"]'         ),
                                mm  : pure.nodes.select.first('select[name="event_registration_start_month"]'       ),
                                yy  : pure.nodes.select.first('select[name="event_registration_start_year"]'        ),
                                h   : pure.nodes.select.first('select[name="event_registration_start_hour"]'        ),
                                m   : pure.nodes.select.first('select[name="event_registration_start_minute"]'      )
                            },
                            finish  : {
                                dd  : pure.nodes.select.first('select[name="event_registration_finish_day"]'        ),
                                mm  : pure.nodes.select.first('select[name="event_registration_finish_month"]'      ),
                                yy  : pure.nodes.select.first('select[name="event_registration_finish_year"]'       ),
                                h   : pure.nodes.select.first('select[name="event_registration_finish_hour"]'       ),
                                m   : pure.nodes.select.first('select[name="event_registration_finish_minute"]'     )
                            },
                            same        : pure.nodes.select.first('input[name="registration_time_is_same"]'             ),
                            container   : pure.nodes.select.first('*[data-create-post-engine-element="registration.container"]')
                        },
                        place           : {
                            map     : pure.nodes.select.first('input[name="event_on_map"]'                          ),
                            name    : pure.nodes.select.first('input[name="event_place_name"]'                      )
                        },
                        limit           : pure.nodes.select.first('input[name="event_members_limit"]'               )
                    };
                if (nodes.form !== null && nodes.action !== null && pure.tools.objects.isValueIn(fields, null, true) === false){
                    if (nodes.publish !== null){
                        pure.events.add(
                            nodes.publish,
                            'click',
                            function(){
                                pure.post.create.controls.actions.proceed('publish', nodes.form, nodes.action, fields);
                            }
                        );
                    }
                    if (nodes.draft !== null){
                        pure.events.add(
                            nodes.draft,
                            'click',
                            function(){
                                pure.post.create.controls.actions.proceed('draft', nodes.form, nodes.action, fields);
                            }
                        );
                    }
                    if (nodes.preview !== null){
                        pure.events.add(
                            nodes.preview,
                            'click',
                            function(){
                                pure.post.create.controls.actions.proceed('preview', nodes.form, nodes.action, fields);
                            }
                        );
                    }
                    if (nodes.update !== null){
                        pure.events.add(
                            nodes.update,
                            'click',
                            function(){
                                pure.post.create.controls.actions.proceed('update', nodes.form, nodes.action, fields);
                            }
                        );
                    }
                    if (nodes.remove !== null){
                        pure.events.add(
                            nodes.remove,
                            'click',
                            function(){
                                pure.post.create.controls.actions.remove('remove', nodes.form, nodes.action, fields);
                            }
                        );
                    }
                    pure.events.add(
                        fields.registration.same,
                        'change',
                        function(){
                            pure.post.create.controls.actions.onSame(fields.registration.same, fields);
                        }
                    );
                    pure.post.create.controls.actions.correction(nodes.form, fields);
                }
            },
            actions : {
                onSame      : function(checkbox, fields){
                    if (checkbox.checked !== false){
                        for(var key in fields.registration.start){
                            fields.registration.start   [key].value  = fields.event.start   [key].value;
                            fields.registration.finish  [key].value  = fields.event.finish  [key].value;
                        }
                        fields.registration.container.style.display = 'none';
                    }else{
                        for(var key in fields.registration.start){
                            fields.registration.container.style.display = '';
                        }
                    }
                },
                validate    : function(fields){
                    function validateDateTime(nodes){
                        function isLeapYear(year){
                            if ((year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0)){
                                return true;
                            }else{
                                return false;
                            }
                        };
                        var datetime =  nodes.yy.value + '/' +
                                        nodes.mm.value + '/' +
                                        nodes.dd.value + ' ' +
                                        nodes.h.value  + ':' +
                                        nodes.m.value,
                            daysInMonth = [31,28,31,30,31,30,31,31,30,31,30,31];
                        datetime = Date.parse(datetime);
                        if (typeof datetime === 'number'){
                            if (isLeapYear(parseInt(nodes.yy.value, 10)) !== false){
                                daysInMonth[1] += 1;
                            }
                            if (parseInt(nodes.dd.value, 10) <= daysInMonth[parseInt(nodes.mm.value, 10) - 1]){
                                return true;
                            }
                        }
                        return false;
                    };
                    function validatePlace(node){
                        return (node.getAttribute('data-bad-place-on-map') !== null ? false : true);
                    }
                    var message     = pure.post.create.dialogs.info,
                        categories  = false,
                        tags        = pure.nodes.select.all('input[data-posteditor-fieldID="post_tags"]');
                    if (tags.length < 1){
                        message(
                            'Please, define field',
                            'You do not define any tags of your event. Please, do it.'
                        );
                        return false;
                    }
                    //Title
                    if (fields.header.value.length < 3){
                        message(
                            'Please, define field',
                            'You do not write name of event. Please, do it.'
                        );
                        return false;
                    }
                    //Categories
                    for(var index = fields.categories.length - 1; index >= 0; index -=1){
                        categories = (categories === true ? true : fields.categories[index].checked);
                    }
                    if (categories === false){
                        message(
                            'Please, define field',
                            'You should define at lease one category for your event.'
                        );
                        return false;
                    }
                    //Dates and times
                    if (validateDateTime(fields.event.start) === false){
                        message(
                            'Wrong date',
                            'Please, check date of start event.'
                        );
                        return false;
                    }
                    if (validateDateTime(fields.event.finish) === false){
                        message(
                            'Wrong date',
                            'Please, check date of finish event.'
                        );
                        return false;
                    }
                    if (validateDateTime(fields.registration.start) === false){
                        message(
                            'Wrong date',
                            'Please, check date of start registration.'
                        );
                        return false;
                    }
                    if (validateDateTime(fields.registration.start) === false){
                        message(
                            'Wrong date',
                            'Please, check date of finish registration.'
                        );
                        return false;
                    }
                    //Place
                    if (validatePlace(fields.place.map) === false){
                        message(
                            'Wrong place',
                            'Sorry, but Google Maps cannot find place of your event. Please, check address and try again.'
                        );
                        return false;
                    }
                    return true;
                },
                question    : function(fields, handle){
                    var question = pure.post.create.dialogs.question;
                    if (fields.quote.value.length < 3 && fields.miniature.value.length === 0){
                        question(
                            'Please, conform operation',
                            'You did not write a excerpt for your event. It will be generated automatically, if you do not define it. You did not define miniature for your event. If your event contains some images, it will be used as miniature.  Do you want continue without defined excerpt?',
                            handle
                        );
                        return false;
                    }
                    if (fields.quote.value.length < 3){
                        question(
                            'Please, conform operation',
                            'You did not write a excerpt for your event. It will be generated automatically, if you do not define it. Do you want continue without defined excerpt?',
                            handle
                        );
                        return false;
                    }
                    if (fields.miniature.value.length === 0){
                        question(
                            'Please, conform operation',
                            'You did not define miniature for your event. If your event contains some images, it will be used as miniature. Do you want continue without defined excerpt?',
                            handle
                        );
                        return false;
                    }
                    return true;
                },
                prepare     : function(form, fields){
                    var allFields = pure.nodes.select.all('*[form="' + form.getAttribute('id') + '"]');
                    for (var index = allFields.length - 1; index >= 0; index -= 1){
                        allFields[index].disabled = false;
                    }
                    if (fields.miniature.value.length === 0){
                        fields.miniature.setAttribute('type', 'text');
                        fields.miniature.value = 'no miniature';
                    }
                },
                proceed     : function(action, form, actionInput, fields){
                    if (pure.post.create.controls.actions.validate(fields) !== false){
                        actionInput.value = action;
                        if (pure.post.create.controls.actions.question(
                                fields,
                                function(){
                                    pure.post.create.controls.actions.prepare(form, fields);
                                    if (typeof tinyMCE !== 'undefined'){
                                        tinyMCE.triggerSave();
                                        form.submit();
                                    }
                                }
                            ) !== false){
                            pure.post.create.controls.actions.prepare(form, fields);
                            if (typeof tinyMCE !== 'undefined'){
                                tinyMCE.triggerSave();
                                form.submit();
                            }
                        }
                    }
                },
                remove      : function(action, form, actionInput, fields){
                    var isDraft = (pure.system.getInstanceByPath('pure.post.data.isDraft') === null ? null : pure.system.getInstanceByPath('pure.post.data.isDraft')),
                        message = '',
                        button  = '';
                    if (isDraft !== null){
                        if (isDraft === 'yes'){
                            message = 'Are you sure, what you want remove this event? This event is not published, nobody does not see it.';
                            button  = 'save changes'
                        }else{
                            message = 'Are you sure, what you want remove this event? If you want just hide it from all, you can turn it to a draft (press \"to draft\" and, in this case, nobody will not see your event.';
                            button  = 'to draft'
                        }
                    }
                    pure.components.dialogs.B.open({
                        title       : 'Confirm operation',
                        innerHTML   :   '<p style="font-family : \'Ubuntu\' DroidScanAttached, Verdana, Arial; font-size : 0.9em; color : rgb(140, 148, 170);">' +
                                            message +
                                        '</p>',
                        width       : 70,
                        parent      : document.body,
                        buttons     : [
                            {
                                title       : 'cancel',
                                handle      : null,
                                closeAfter  : true
                            },
                            {
                                title       : button,
                                handle      : function(){
                                    pure.post.create.controls.actions.proceed('draft', form, actionInput, fields);
                                },
                                closeAfter  : true
                            },
                            {
                                title       : 'remove',
                                handle      : function(){
                                    actionInput.value = 'remove';
                                    pure.post.create.controls.actions.prepare(form, fields);
                                    if (typeof tinyMCE !== 'undefined'){
                                        tinyMCE.triggerSave();
                                        form.submit();
                                    }
                                },
                                closeAfter  : true
                            }
                        ]
                    });
                },
                correction  : function(form, fields){
                    fields.content.setAttribute('form', form.getAttribute('id'));
                }
            }
        },
        miniature   : {
            init : function(){
                var nodes = {
                    file    : pure.nodes.select.first('input[data-create-post-engine-element="miniature"]'      ),
                    image   : pure.nodes.select.first('img[data-create-post-engine-element="miniature"]'        ),
                    load    : pure.nodes.select.first('*[data-create-post-engine-element="miniature.select"]'   ),
                    remove  : pure.nodes.select.first('*[data-create-post-engine-element="miniature.remove"]'   )
                };
                if (pure.tools.objects.isValueIn(nodes, null) === false){
                    pure.events.add(
                        nodes.load,
                        'click',
                        function(){
                            if (nodes.file.getAttribute('type') === 'text'){
                                nodes.file.setAttribute('type', 'file');
                                nodes.file.value = '';
                            }
                            pure.events.call(nodes.file, 'click');
                        }
                    );
                    pure.events.add(
                        nodes.remove,
                        'click',
                        function(){
                            pure.post.create.miniature.actions.remove(nodes.file, nodes.image, nodes.remove);
                        }
                    );
                    pure.events.add(
                        nodes.file,
                        'change',
                        function(){
                            pure.post.create.miniature.actions.select(nodes.file, nodes.image);
                        }
                    );
                }
            },
            actions : {
                select  : function(input, image){
                    var message         = pure.post.create.dialogs.info,
                        ext             = null;
                    if (typeof input.files !== 'undefined' && typeof input.value === 'string'){
                        if (input.files.length === 1){
                            ext = (input.value.match(/\.([^\.]+)$/)[1]).toLowerCase();
                            if (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg'){
                                pure.post.create.miniature.actions.preview(input.files[0], image);
                            }
                            if (input.files.length > 1){
                                message('You cannot do that', 'Sorry you can use only GIF, PNG, JPEG or JPG.');
                                return false;
                            }else{
                                //no file selected - just exit
                                return false;
                            }
                        }
                        message('You cannot do that', 'Sorry you can choose only one file.');
                        return false;
                    }
                    message('Error', 'Sorry some error with your browser. Could not get file name');
                    return false;
                },
                preview : function(file, image){
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
                remove  : function(input, image, button){
                    var noMiniatureSrc = button.getAttribute('data-create-post-engine-no_miniature_src');
                    noMiniatureSrc = (typeof noMiniatureSrc !== 'string' ? '' : noMiniatureSrc);
                    input.setAttribute('type', 'text');
                    input.value = 'no miniature';
                    image.setAttribute('src', noMiniatureSrc);
                }

            }
        },
        dialogs     : {
            info: function (title, message) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p style="font-family : \'Ubuntu\' DroidScanAttached, Verdana, Arial; font-size : 0.9em; color : rgb(140, 148, 170);">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : [
                        {
                            title       : 'OK',
                            handle      : null,
                            closeAfter  : true
                        }
                    ]
                });
            },
            question : function (title, message, handle) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p style="font-family : \'Ubuntu\' DroidScanAttached, Verdana, Arial; font-size : 0.9em; color : rgb(140, 148, 170);">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : [
                        {
                            title       : 'cancel',
                            handle      : null,
                            closeAfter  : true
                        },
                        {
                            title       : 'continue',
                            handle      : handle,
                            closeAfter  : true
                        }
                    ]
                });
            }
        },
        fullscreen : {
            ready   : false,
            init    : function(){
                if (typeof window.tinymce !== 'undefined' && pure.post.create.fullscreen.ready === false){
                    pure.post.create.fullscreen.ready = true;
                    for (var i = 0; i < tinymce.editors.length; i++) {
                        tinymce.editors[i].on('FullscreenStateChanged', function (e) {
                            var header_menu = pure.nodes.select.first('*[data-global-makeup-marks="menu-top-line-full"]');
                            if (e.state === false){
                                header_menu.style.display = '';
                            }else{
                                header_menu.style.display = 'none';
                            }
                        });
                    }
                }else{
                    if (pure.post.create.fullscreen.ready === false){
                        setTimeout(pure.post.create.fullscreen.init, 100);
                    }
                }
            }
        },
        init        : function(){
            pure.post.create.controls.  init();
            pure.post.create.miniature. init();
            pure.post.create.map.       init();
            pure.post.create.fullscreen.init();
        }
    };
    pure.system.start.add(pure.post.create.init);
}());