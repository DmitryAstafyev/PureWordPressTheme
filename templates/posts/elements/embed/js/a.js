(function () {
    if (typeof window.pure                      !== "object") { window.pure                         = {}; }
    if (typeof window.pure.posts                !== "object") { window.pure.posts                   = {}; }
    if (typeof window.pure.posts.elements       !== "object") { window.pure.posts.elements          = {}; }
    if (typeof window.pure.posts.elements.embed !== "object") { window.pure.posts.elements.embed    = {}; }
    "use strict";
    window.pure.posts.elements.embed.A = {
        resize   : {
            handles : [],
            inited  : false,
            init    : function(){
                if (pure.posts.elements.embed.A.resize.inited === false){
                    pure.events.add(window,"resize", pure.posts.elements.embed.A.resize.resize);
                    pure.posts.elements.embed.A.resize.inited = true;
                }
            },
            add     : function(parent){
                var handles = pure.posts.elements.embed.A.resize.handles,
                    frame   = pure.nodes.find.childByType(parent, 'IFRAME');
                if (frame !== null){
                    if (frame.getAttribute('width') !== '' && frame.getAttribute('width') !== null){
                        handles.push(
                            function(){
                                var size = pure.nodes.render.size(parent);
                                if (size.width !== null){
                                    if (size.width > 0){
                                        frame.setAttribute('width', Math.round(size.width));
                                    }
                                }
                            }
                        );
                    }
                }
            },
            resize  : function(event){
                var handles = pure.posts.elements.embed.A.resize.handles;
                for(var index = handles.length - 1; index >= 0; index -= 1){
                    pure.system.runHandle(handles[index], null, '', event);
                }
            }
        },
        init : function(){
            var instances   = pure.nodes.select.all('*[data-engine-post-embed="container"]:not([data-element-inited])');
            if (instances !== null) {
                for(var index = instances.length - 1; index >= 0; index -= 1){
                    (function(instance){
                        instance.setAttribute('data-element-inited', 'true');
                        pure.posts.elements.embed.A.resize.add(instance);
                        pure.posts.elements.embed.A.resize.init();
                    }(instances[index]));
                }
                pure.posts.elements.embed.A.resize.resize(null);
            }

        }

    };
    pure.system.start.add(pure.posts.elements.embed.A.init);
}());