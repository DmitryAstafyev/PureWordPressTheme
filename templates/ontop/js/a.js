(function () {
    if (typeof window.pure          !== "object") { window.pure         = {}; }
    if (typeof window.pure.onTop    !== "object") { window.pure.onTop   = {}; }
    "use strict";
    window.pure.onTop.A = {
        init : function(){
            var instances = pure.nodes.select.all('*[data-global-makeup-marks="to-top-button"]');
            if (instances !== null){
                Array.prototype.forEach.call(
                    instances,
                    function(item, _index, source){
                        pure.events.add(
                            item,
                            'click',
                            function(){
                                jQuery("html, body").animate({ scrollTop: 0 }, 600);
                                return false;
                            }
                        );
                    }
                );
            }
        }
    };
    pure.system.start.add(pure.onTop.A.init);
}());