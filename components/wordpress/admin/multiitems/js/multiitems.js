(function () {
    if (typeof window.pure                  !== "object") { window.pure                     = {}; }
    if (typeof window.pure.components       !== "object") { window.pure.components          = {}; }
    if (typeof window.pure.components.admin !== "object") { window.pure.components.admin    = {}; }
    "use strict";
    window.pure.components.admin.multiitems = {
        init    : function () {
            var removes         = pure.nodes.select.all('*[data-muliitems-under-control]'   ),
                adds            = pure.nodes.select.all('*[data-muliitems-add-button]'),
                id              = null,
                template        = null,
                index_template  = null,
                handles         = null;
            if (removes !== null) {
                if (typeof removes.length === "number") {
                    for (var index = removes.length - 1; index >= 0; index -= 1) {
                        if (pure.nodes.find.parentByAttr(removes[index], {name:'data-muliitems-template', value:null}) === null) {
                            id = removes[index].getAttribute('data-muliitems-under-control');
                            if (typeof id === 'string') {
                                if (id.trim() !== '') {
                                    (function (node, id) {
                                        pure.events.add(
                                            node,
                                            "click",
                                            function (event) {
                                                pure.components.admin.multiitems.actions.remove(event, id);
                                            }
                                        );
                                    }(removes[index], id));
                                    removes[index].removeAttribute('data-muliitems-under-control');
                                }
                            }
                        }
                    }
                }
            }
            if (adds !== null) {
                if (typeof adds.length === "number") {
                    for (var index = adds.length - 1; index >= 0; index -= 1) {
                        //Check is it template or not
                        if (pure.nodes.find.parentByAttr(adds[index], {name:'data-muliitems-template', value:null}) === null){
                            template = pure.nodes.find.childByAttr(adds[index].parentNode, '*', { name: 'data-muliitems-template', value: null });
                            if (template !== null) {
                                index_template  = template.     getAttribute('data-muliitems-index-template'    );
                                handles         = adds[index].  getAttribute('data-muliitems-afteradd-handles'  );
                                if (typeof handles === "string"){
                                    handles = handles.replace(/[^A-Za-z0-9_.,]/gi, '');
                                    handles = handles.split(',');
                                }else{
                                    handles = [];
                                }
                                handles = (handles instanceof Array === true ? handles : [handles]);
                                if (typeof index_template === "string") {
                                    if (index_template.trim() !== '') {
                                        (function (node, templateInnerHTML, index_template, handles) {
                                            pure.events.add(
                                                node,
                                                "click",
                                                function (event) {
                                                    pure.components.admin.multiitems.actions.add(event, templateInnerHTML, index_template, handles);
                                                }
                                            );
                                        }(adds[index], template.innerHTML, index_template, handles));
                                        template.parentNode.removeChild(template);
                                        adds[index].removeAttribute('data-muliitems-add-button'         );
                                        adds[index].removeAttribute('data-muliitems-afteradd-handles'   );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        },
        actions: {
            remove : function(event, id){
                var parent = pure.nodes.select.first('div[data-muliitems-parent-of="' + id + '"]');
                if (parent !== null) {
                    parent.parentNode.removeChild(parent);
                }
            },
            add: function (event, templateInnerHTML, index_template, handles) {
                function getHandle(handle_str){
                    var paths   = handle_str.split('.'),
                        handle  = window;
                    paths = (paths instanceof Array === true ? paths : [paths]);
                    for (var index = 0; index < paths.length; index += 1){
                        if (typeof handle[paths[index]] !== 'undefined'){
                            handle = handle[paths[index]];
                        }else{
                            return null;
                        }
                    }
                    return (typeof handle === 'function' ? handle : null);
                };
                var template        = document.createElement("DIV"),
                    index           = Math.round(Math.random() * 10000000),
                    current_index   = index_template.   replace(/\[index\]/gi, '' + index + ''),
                    innerHTML       = '',
                    remove          = null,
                    handle          = null;
                if (template !== null) {
                    //Such way of replacing, because can be something like [[index]][[index]]..[[index]].
                    innerHTML           = templateInnerHTML.replace(
                        /(\[\[index\]\]){2,}/gi,
                        function(str, p1, offset, s){
                            return str.replace(/\[\[index\]\]/i, '[[-index-]]').replace(/\[\[index\]\]/gi, '[[--index--]]').replace(/\[\[-index-\]\]/gi, '[[index]]');
                        }
                    );
                    innerHTML           = innerHTML.replace(/\[index\]/gi, '' + index + '');
                    innerHTML           = innerHTML.replace(/\[--index--\]/gi, '[index]');
                    innerHTML           = innerHTML.replace(/(data-muliitems-under-control-template)/gi, 'data-muliitems-under-control');
                    template.innerHTML  = innerHTML;
                    if (template.childNodes !== null) {
                        if (template.childNodes.length > 0) {
                            do {
                                event.target.parentNode.insertBefore(template.childNodes[0], event.target);
                            } while (template.childNodes.length > 0);
                            remove = pure.nodes.select.first('*[data-muliitems-under-control="' + current_index + '"]');
                            if (remove !== null) {
                                pure.events.add(remove, "click", function (event) { pure.components.admin.multiitems.actions.remove(event, current_index); });
                            }
                            for(var index = handles.length - 1; index >= 0; index -= 1){
                                try{
                                    handle = getHandle(handles[index]);
                                    if (handle !== null){
                                        handle.call();
                                    }
                                }catch (e){}
                            }
                        }
                    }
                }
            }
        }
    };
    pure.system.start.add(pure.components.admin.multiitems.init);
}());