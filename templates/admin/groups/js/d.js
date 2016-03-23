(function () {
    if (typeof window.pure              !== "object") { window.pure                 = {}; }
    if (typeof window.pure.admin        !== "object") { window.pure.admin           = {}; }
    if (typeof window.pure.admin.groups !== "object") { window.pure.admin.groups    = {}; }
    "use strict";
    window.pure.admin.groups.D = {
        init : function(){
            var instances = pure.nodes.select.all('input[data-engine-admin-group-handle]');
            if (instances !== null){
                Array.prototype.forEach.call(
                    instances,
                    function(item, _index, sources){
                        var handle = item.getAttribute('data-engine-admin-group-handle');
                        if (handle !== null && handle !== ''){
                            handle = pure.system.getInstanceByPath(handle);
                            if (handle !== null){
                                pure.events.add(
                                    item,
                                    'change',
                                    function(){
                                        setTimeout(handle, 50);
                                    }
                                );
                            }
                        }
                    }
                );
            }
        }
    };
    pure.system.start.add(pure.admin.groups.D.init);
}());