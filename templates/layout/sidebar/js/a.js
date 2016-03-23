(function () {
    if (typeof window.pure                  !== "object") { window.pure                 = {}; }
    if (typeof window.pure.layout           !== "object") { window.pure.layout          = {}; }
    if (typeof window.pure.layout.sidebar   !== "object") { window.pure.layout.sidebar  = {}; }
    "use strict";
    window.pure.layout.sidebar.A = {
        init : function(){
            var switcher = pure.nodes.select.first('*[id="Pure.GlobalLayout.SideBar.Switcher"]');
            if (switcher !== null){
                pure.events.add(
                    switcher,
                    'change',
                    function(){
                        setTimeout(
                            function(){
                                pure.appevents.Actions.call(
                                    'global.layout.sidebar',
                                    'update',
                                    switcher.checked,
                                    null
                                );
                            },
                            50
                        );
                    }
                );
            }
        }
    };
    pure.system.start.add(pure.layout.sidebar.A.init);
}());