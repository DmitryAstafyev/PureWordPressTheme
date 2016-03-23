(function () {
    if (typeof window.pure                      !== "object") { window.pure                     = {}; }
    if (typeof window.pure.components           !== "object") { window.pure.components          = {}; }
    if (typeof window.pure.components.lockpage  !== "object") { window.pure.components.lockpage = {}; }
    "use strict";
    window.pure.components.lockpage.A = {
        show : function(background){
            var node = document.createElement('DIV');
            node.style.position     = 'fixed';
            node.style.top          = '0px';
            node.style.left         = '0px';
            node.style.width        = '100%';
            node.style.height       = '100%';
            node.style.background   = 'rgba(255,255,255,0.7)';
            node.style.zIndex       = '99999';
            document.body.appendChild(node);
            pure.templates.progressbar.A.show(node);
        },
        init : function(){
            pure.events.add(
                window,
                'beforeunload',
                pure.components.lockpage.A.show
            );
        }
    };
    pure.system.start.add(pure.components.lockpage.A.init);
}());