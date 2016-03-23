(function () {
    if (typeof window.pure      !== "object") { window.pure         = {}; }
    if (typeof window.pure.post !== "object") { window.pure.post    = {}; }
    "use strict";
    window.pure.post.create = {
        controls : {
            init : function(){
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
                        categories      : pure.nodes.select.all('input[name="post_category"]'                       )
                    };
                if (nodes.form !== null && nodes.action !== null && pure.tools.objects.isValueIn(fields, null) === false){
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
                    pure.post.create.controls.actions.correction(nodes.form, fields);
                }
            },
            actions : {
                validate    : function(fields){
                    var message     = pure.post.create.dialogs.info,
                        categories  = false,
                        keywords    = pure.nodes.select.all('input[data-posteditor-fieldID="post_keywords"]');
                    if (keywords.length < 1){
                        message(
                            'Please, define field',
                            'You do not define any keyword of your question. Please, do it.'
                        );
                        return false;
                    }
                    if (fields.header.value.length < 3){
                        message(
                            'Please, define field',
                            'You do not write any title of your question. Please, do it.'
                        );
                        return false;
                    }
                    for(var index = fields.categories.length - 1; index >= 0; index -=1){
                        categories = (categories === true ? true : fields.categories[index].checked);
                    }
                    if (categories === false){
                        message(
                            'Please, define field',
                            'You should define at lease one category for your question.'
                        );
                        return false;
                    }
                    return true;
                },
                question    : function(fields, handle){
                    var question = pure.post.create.dialogs.question;
                    return true;
                },
                prepare     : function(form, fields){
                    var allFields = pure.nodes.select.all('*[form="' + form.getAttribute('id') + '"]');
                    for (var index = allFields.length - 1; index >= 0; index -= 1){
                        allFields[index].disabled = false;
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
                            message = 'Are you sure, what you want remove this question? This question is not published, nobody does not see it.';
                            button  = 'save changes'
                        }else{
                            message = 'Are you sure, what you want remove this question? If you want just hide it from all, you can turn it to a draft (press \"to draft\" and, in this case, nobody will not see your question.';
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
        init : function(){
            pure.post.create.controls.  init();
            pure.post.create.fullscreen.init();
        }
    };
    pure.system.start.add(pure.post.create.init);
}());