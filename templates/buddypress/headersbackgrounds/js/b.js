(function () {
    if (typeof window.pure                              !== "object") { window.pure                             = {}; }
    if (typeof window.pure.buddypress                   !== "object") { window.pure.buddypress                  = {}; }
    if (typeof window.pure.buddypress.background        !== "object") { window.pure.buddypress.background       = {}; }
    "use strict";
    window.pure.buddypress.background.B = {
        init    : function () {
            pure.buddypress.background.B.initialize.init();
        },
        initialize  : {
            init : function(){
                var instances = pure.nodes.select.all('div[data-element-type="Pure.Social.Header.Background.B.Container"]:not([data-type-element-inited])');
                if (instances !== null) {
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        (function(node){
                            var id      = pure.tools.IDs.get('Pure.Social.Header.Background.B.'),
                                columns = null,
                                rows    = [];
                            node.setAttribute('data-engine-id', id);
                            columns = pure.nodes.select.all('div[data-element-type="Pure.Social.Header.Background.B.Container"][data-engine-id="' + id + '"] div[data-element-type="Pure.Social.Header.Background.B.Images.Column"]');
                            if (columns !== null){
                                for(var index = columns.length - 1; index >=0; index -= 1){
                                    columns[index].setAttribute('data-engine-id', id);
                                    (function(rows, node){
                                        rows.push({
                                            node    : node,
                                            count   : node.childNodes.length,
                                            current : 0
                                        });
                                    }(rows, columns[index]));
                                }
                            }
                            if (rows.length > 0){
                                pure.buddypress.background.B.Actions.onTimer(rows);
                            }
                            node.setAttribute('data-type-element-inited', 'true');
                        }(instances[index]));
                    }
                }
            }
        },
        Actions     : {
            configuration : {
                step        : 10,
                duration    : 5000
            },
            onTimer : function(rows){
                var configuration   = pure.buddypress.background.B.Actions.configuration,
                    activeColumn    = Math.round(Math.random() * (rows.length - 1)),
                    activeRow       = Math.round(Math.random() * (rows[activeColumn].count - 1)),
                    nextAction      = Math.round(Math.random() * configuration.duration),
                    position        = activeRow * configuration.step;
                rows[activeColumn].node.style.top = -position + 'em';
                setTimeout(function () {
                    pure.buddypress.background.B.Actions.onTimer(rows);
                }, nextAction);
            }
        }
    };
    pure.system.start.add(pure.buddypress.background.B.init);
}());