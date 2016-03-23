(function () {
    if (typeof window.pure                  !== "object") { window.pure                 = {}; }
    if (typeof window.pure.loaderProgress   !== "object") { window.pure.loaderProgress  = {}; }
    "use strict";
    window.pure.loaderProgress.A = {
        items   : {
            stop    : false,
            data    : null,
            indexes : [],
            init    : function(){
                var items   = pure.nodes.select.all('*[data-compressor-item]'),
                    indexes = pure.loaderProgress.A.items.indexes;
                if (items !== null){
                    for(var index = items.length - 1; index >= 0; index -= 1){
                        items[index].style.display = 'none';
                        indexes.push(index);
                    }
                    indexes.sort(function() { return Math.random() - 0.6;});
                    pure.loaderProgress.A.items.data = items;
                    pure.loaderProgress.A.items.proceed();
                }
            },
            proceed : function(){
                var node = null;
                if (pure.loaderProgress.A.items.stop === false){
                    if (pure.loaderProgress.A.items.indexes.length > 0){
                        node = pure.loaderProgress.A.items.data[pure.loaderProgress.A.items.indexes[0]];
                        if (node !== null){
                            if (typeof node.style !== 'undefined'){
                                if (node.style !== null){
                                    node.style.display = '';
                                }
                            }
                        }
                        pure.loaderProgress.A.items.indexes.splice(0, 1);
                        setTimeout(
                            pure.loaderProgress.A.items.proceed,
                            300 + Math.random() * 1000
                        );
                    }
                }
            },
            finish : function(){
                var node = pure.nodes.select.first('*[data-compressor-loader]');
                pure.loaderProgress.A.items.stop = true;
                if (node !== null){
                    node.style.opacity = 0;
                    setTimeout(
                        function(){
                            node.parentNode.removeChild(node);
                        },
                        1000
                    );
                }
            }
        },
        events  : {
            init : function(){
                pure.appevents.Actions.listen(
                    'pure.compressor',
                    'finish',
                    pure.loaderProgress.A.items.finish,
                    'pure.loaderProgress.A'
                );
            }
        },
        init    : function(){
            var progress    = pure.system.getInstanceByPath('pure.compressor.progress'),
                node        = document.createElement('DIV');
            if (progress !== null){
                node.innerHTML = progress.innerHTML;
                pure.nodes.move.appendChildsTo(document.body, node.childNodes);
                pure.loaderProgress.A.items.init();
                pure.loaderProgress.A.events.init();
            }
        }
    };
    pure.loaderProgress.A.init();
}());