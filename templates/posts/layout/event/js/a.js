(function () {
    if (typeof window.pure                      !== "object") { window.pure                         = {}; }
    if (typeof window.pure.posts                !== "object") { window.pure.posts                   = {}; }
    if (typeof window.pure.posts.layout         !== "object") { window.pure.posts.layout            = {}; }
    if (typeof window.pure.posts.layout.event   !== "object") { window.pure.posts.layout.event      = {}; }
    "use strict";
    window.pure.posts.layout.event.A = {
        resize   : {
            handles : [],
            inited  : false,
            init    : function(){
                if (pure.posts.layout.event.A.resize.inited === false){
                    pure.events.add(window,"resize", pure.posts.layout.event.A.resize.resize);
                    pure.posts.layout.event.A.resize.inited = true;
                }
            },
            resize  : function(event){
                pure.appevents.Actions.call(
                    'pure.positioning',
                    'resize',
                    null,
                    null
                );
            }
        },
        init : function(){
            pure.posts.layout.event.A.resize.init();
        }
    };
    pure.system.start.add(pure.posts.layout.event.A.init);
}());