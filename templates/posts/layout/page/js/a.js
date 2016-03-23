(function () {
    if (typeof window.pure                      !== "object") { window.pure                         = {}; }
    if (typeof window.pure.posts                !== "object") { window.pure.posts                   = {}; }
    if (typeof window.pure.posts.layout         !== "object") { window.pure.posts.layout            = {}; }
    if (typeof window.pure.posts.layout.page    !== "object") { window.pure.posts.layout.page       = {}; }
    "use strict";
    window.pure.posts.layout.page.A = {
        resize   : {
            handles : [],
            inited  : false,
            init    : function(){
                if (pure.posts.layout.page.A.resize.inited === false){
                    pure.events.add(window,"resize", pure.posts.layout.page.A.resize.resize);
                    pure.posts.layout.page.A.resize.inited = true;
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
            pure.posts.layout.page.A.resize.init();
        }
    };
    pure.system.start.add(pure.posts.layout.page.A.init);
}());