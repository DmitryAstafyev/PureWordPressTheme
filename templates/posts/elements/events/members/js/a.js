(function () {
    if (typeof window.pure                                  !== "object") { window.pure                                 = {}; }
    if (typeof window.pure.posts                            !== "object") { window.pure.posts                           = {}; }
    if (typeof window.pure.posts.elements                   !== "object") { window.pure.posts.elements                  = {}; }
    if (typeof window.pure.posts.elements.events            !== "object") { window.pure.posts.elements.events           = {}; }
    if (typeof window.pure.posts.elements.events.members    !== "object") { window.pure.posts.elements.events.members   = {}; }
    "use strict";
    window.pure.posts.elements.events.members.A = {
        request : {
            isPossible      : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.events.actions.configuration.requestURL'      ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.events.actions.configuration.requests.action' ) === null ? false : true));
                return result;
            },
            getAttributes   : function(button){
                var attributes = {
                        action  : button.getAttribute('data-event-members-engine-action'),
                        eventID : button.getAttribute('data-event-members-engine-eventID'),
                        userID  : button.getAttribute('data-event-members-engine-userID')
                    };
                if (pure.tools.objects.isValueIn(attributes, null) === false){
                    return attributes;
                }
                return null;
            },
            send            : function(button){
                var attributes  = pure.posts.elements.events.members.A.request.getAttributes(button),
                    request     = null,
                    progress    = null;
                if (attributes !== null){
                    if (pure.posts.elements.events.members.A.request.isPossible() !== false){
                        progress    = pure.templates.progressbar.B.show(button);
                        request     = pure.events.actions.configuration.requests.action;
                        request     = request.replace(/\[action\]/gi,   attributes.action   );
                        request     = request.replace(/\[event_id\]/gi, attributes.eventID  );
                        pure.tools.request.send({
                            type        : 'POST',
                            url         : pure.events.actions.configuration.requestURL,
                            request     : request,
                            onrecieve   : function (id_request, response) {
                                pure.posts.elements.events.members.A.request.onRecieve(id_request, response, progress);
                            },
                            onreaction  : null,
                            onerror     : function (event, id_request) {
                                pure.posts.elements.events.members.A.request.onError(event, id_request, progress);
                            },
                            ontimeout   : function (id_request) {
                                pure.posts.elements.events.members.A.request.onError(id_request, id_request, progress);
                            }
                        });
                    }
                }
            },
            onRecieve   : function(id_request, response, progress){
                var message = pure.posts.elements.events.members.A.dialogs.info;
                pure.templates.progressbar.B.hide(progress);
                switch (response){
                    case 'success':
                        message(
                            'Success',
                            'Operation is done. Press "OK" to reload page. It\' necessary to apply changes.',
                            function(){
                                location.reload();
                            }
                        );
                        break;
                    case 'registration is closed':
                        message('You cannot do it', 'Sorry, but registration is closed.');
                        break;
                    case 'fail':
                        message('Error', 'Sorry, but during saving some error was. Try again a bit later.');
                        break;
                    case 'no access':
                        message('Error', 'Server says - you have not necessary permissions to do it.');
                        break;
                }
            },
            onError     : function(event, id_request, progress){
                pure.templates.progressbar.B.hide(progress);
                message('Error', 'Sorry, but there are some unknown error. Try again a bit later.');
            }
        },
        dialogs     : {
            info: function (title, message, handle) {
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
        },
        init    : function(){
            var instances   = pure.nodes.select.all('*[data-event-members-engine-element="button"]:not([data-element-inited])');
            if (instances !== null) {
                for(var index = instances.length - 1; index >= 0; index -= 1){
                    (function(instance){
                        instance.setAttribute('data-element-inited', 'true');
                        pure.events.add(
                            instance,
                            'click',
                            function(){
                                pure.posts.elements.events.members.A.request.send(instance);
                            }
                        );
                    }(instances[index]));
                }
            }
        }
    };
    pure.system.start.add(pure.posts.elements.events.members.A.init);
}());