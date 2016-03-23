(function () {
    if (typeof window.pure                      !== "object") { window.pure                     = {}; }
    if (typeof window.pure.templates            !== "object") { window.pure.templates           = {}; }
    if (typeof window.pure.templates.warnings   !== "object") { window.pure.templates.warnings  = {}; }
    "use strict";
    window.pure.templates.warnings.A = {
        init        : function () {
            var collection  = pure.system.getInstanceByPath('pure.templates.warnings.collection'),
                message     = '';
            if (collection !== null){
                try{
                    collection = JSON.parse(collection);
                    if (collection instanceof Array){
                        for(var index = collection.length - 1; index >=0; index -= 1){
                            message += '<h1 data-element-type="Pure.Elements.Warnings.A">'   + collection[index].title   + '</h1>';
                            message += '<p data-element-type="Pure.Elements.Warnings.A">'    + collection[index].content + '</p>';
                        }
                        pure.components.dialogs.B.open({
                            title       : 'Warning',
                            innerHTML   : message,
                            width       : 70,
                            parent      : document.body,
                            buttons     : [
                                {
                                    title       : 'BACK',
                                    handle      : function(){
                                        if (typeof window.history !== 'undefined'){
                                            if (window.history.length <= 1){
                                                window.location.href = pure.globalsettings.domain;
                                            }else{
                                                window.history.back();
                                            }
                                        }else{
                                            window.location.href = pure.globalsettings.domain;
                                        }
                                    },
                                    closeAfter  : true
                                },
                                {
                                    title       : 'CONTINUE',
                                    handle      : null,
                                    closeAfter  : true
                                },
                            ]
                        });
                    }
                }catch (e){}
            }
        }
    };
    pure.system.start.add(pure.templates.warnings.A.init);
}());