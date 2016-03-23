(function () {
    if (typeof window.pure                  !== "object") { window.pure                 = {}; }
    if (typeof window.pure.components       !== "object") { window.pure.components      = {}; }
    if (typeof window.pure.components.more  !== "object") { window.pure.components.more = {}; }
    "use strict";
    window.pure.components.more.A = {
        init : function(){
            var buttons = pure.nodes.select.all('*[data-type-use="Pure.Components.More"]:not([data-type-more-inited])');
            if (buttons !== null){
                for (var index = buttons.length - 1; index >= 0; index -= 1){
                    (function (button) {
                        pure.events.add(button, "click",
                            function (event) { pure.components.more.A.actions.send(event, button); });
                        button.setAttribute('data-type-more-inited', 'true');
                    }(buttons[index]));
                }
            }
            pure.components.more.A.More.init();
        },
        actions : {
            send : function(event, button){
                function getParameters(button){
                    var group       = button.getAttribute('data-type-more-group'        ),
                        maximum     = button.getAttribute('data-type-more-max'          ),
                        settings    = button.getAttribute('data-type-more-settings'     ),
                        post_type   = button.getAttribute('data-type-more-post_type'    ),
                        progress    = button.getAttribute('data-type-more-progress'     ),
                        template    = button.getAttribute('data-type-more-template'     );
                    if (group !== null && maximum !== null && template !== null && settings !== null){
                        if (group !== '' && maximum !== '' && template !== '' && settings !== ''){
                            progress    = (progress === null ? template : (progress === '' ? template   : progress  ));
                            return {
                                group       : group,
                                maximum     : maximum,
                                template    : template,
                                settings    : settings,
                                progress    : progress,
                                post_type   : post_type
                            };
                        }
                    }
                    return null;
                };
                var progress    = null,
                    parameters  = getParameters(button),
                    records     = null,
                    request     = null,
                    settings    = null;
                if ((parseInt(button.style.opacity, 10) === 1 || button.style.opacity === '') &&
                    parameters !== null && pure.tools.objects.is('pure.templates.progressbar.' + parameters.progress) !== false){
                    settings = pure.system.getInstanceByPath(parameters.settings);
                    if (settings !== null){
                        progress = pure.templates.progressbar[parameters.progress].show(button);
                        button.style.opacity = 0.4;
                        if (progress !== null && typeof settings.params[parameters.group] === "string" ){
                            records = pure.nodes.select.all('*[data-type-more-group="' + parameters.group + '"]:not([data-type-use="Pure.Components.More"]):not([data-type-use="Pure.Components.More.Shown"])');
                            if (records !== null){
                                request = settings.params[parameters.group];
                                request = request.replace(/\[count\]/gi,    records.length      );
                                request = request.replace(/\[maximum\]/gi,  parameters.maximum  );
                                if (parameters.post_type !== null){
                                    request = request.replace(/\[post_type\]/gi,  parameters.post_type  );
                                }
                                pure.tools.request.send({
                                    type        : 'POST',
                                    url         : settings.url,
                                    request     : request,
                                    onrecieve   : function (id_request, response) { pure.components.more.A.actions.receive(id_request, response, button, progress, parameters.progress, parameters.group); },
                                    onreaction  : null,
                                    onerror     : function (id_request) { pure.components.more.A.actions.error(id_request, button, progress, parameters.progress); },
                                    ontimeout   : function (id_request) { pure.components.more.A.actions.error(id_request, button, progress, parameters.progress); }
                                });
                            }
                        }
                    }
                }
            },
            receive : function(id_request, response, button, progress, progress_template, group){
                var node = null;
                if (response !== 'error'){
                    node            = document.createElement("DIV");
                    node.innerHTML  = pure.tools.scripts.removeFromInnerHTML(response);
                    for (var index = node.childNodes.length - 1; index >= 0; index -= 1){
                        button.parentNode.insertBefore(node.childNodes[0], button);
                    }
                    node = null;
                    pure.tools.scripts.attachScripts(pure.tools.scripts.getFromInnerHTML(response));
                    pure.components.more.A.status.update(group);
                    pure.components.attacher.module.findAndAttach(response);
                    pure.appevents.Actions.call('pure.more', 'done', group, null);
                }
                pure.templates.progressbar[progress_template].hide(progress, button);
                button.style.opacity = '';
            },
            error : function(id_request, button, progress, progress_template){
                var node = document.createElement("P");
                node.innerHTML = 'Some error on server is. Sorry.';
                node.setAttribute('data-type-element', 'Author.Thumbnail.D.More.Error');
                button.parentNode.insertBefore(node, button);
                setTimeout(function(){
                    node.parentNode.removeChild(node);
                    pure.templates.progressbar[progress_template].hide(progress);
                    button.style.opacity = '';
                }, 5000);
            },
            reset : function(id){
                function remove(records){
                    for(var index = records.length - 1; index >= 0; index -= 1){
                        if (typeof records[index].parentNode !== 'undefined'){
                            if (typeof records[index].parentNode.removeChild === 'function'){
                                records[index].parentNode.removeChild(records[index]);
                            }
                        }
                    }
                };
                var records = pure.nodes.select.all('*[data-type-more-group="' + id + '"]:not([data-type-use="Pure.Components.More"]):not([data-type-use="Pure.Components.More.Shown"])'),
                    button  = pure.nodes.select.first('*[data-type-use="Pure.Components.More"][data-type-more-group="' + id + '"]');
                if (records !== null && button !== null){
                    remove(records);
                    pure.components.more.A.actions.send(null, button);
                }
            }
        },
        status : {
            update : function(group){
                var label   = pure.nodes.select.first('*[data-type-more-group="' + group + '"][data-type-use="Pure.Components.More.Shown"]'),
                    records = null;
                if (label !== null){
                    records = pure.nodes.select.all('*[data-type-more-group="' + group + '"]:not([data-type-use="Pure.Components.More"]):not([data-type-use="Pure.Components.More.Shown"])');
                    if (records !== null){
                        label.innerHTML = records.length;
                    }
                }

            }
        },
        More : {
            initialized : false,
            init        : function(){
                if (pure.components.more.A.More.initialized === false){
                    pure.appevents.Events.methods.register('pure.more', 'done');
                    pure.appevents.Actions.listen('pure.more', 'done', function(){ pure.components.more.A.init(); }, 'pure.components.more.A.init');
                    pure.components.more.A.More.initialized = true;
                }
            }
        }
    };
    (function(){
        pure.appevents.Actions.listen('pure.more', 'reset', pure.components.more.A.actions.reset, 'pure.more.listener.reset');
        pure.system.start.add(pure.components.more.A.init);
    }());
}());