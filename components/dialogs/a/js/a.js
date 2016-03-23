(function () {
    if (typeof window.pure                      !== "object") { window.pure                     = {}; }
    if (typeof window.pure.components           !== "object") { window.pure.components          = {}; }
    if (typeof window.pure.components.dialogs   !== "object") { window.pure.components.dialogs  = {}; }
    "use strict";
    window.pure.components.dialogs.A = {
        shadow : {
            storage : {
                data    : {},
                get     : function(id){
                    return (typeof pure.components.dialogs.A.shadow.storage.data[id] !== 'undefined' ? pure.components.dialogs.A.shadow.storage.data[id] : null);
                },
                remove  : function(id){
                    if (typeof pure.components.dialogs.A.shadow.storage.data[id] !== 'undefined'){
                        pure.components.dialogs.A.shadow.storage.data[id] = null;
                        delete pure.components.dialogs.A.shadow.storage.data[id];
                        return true;
                    }
                    return null;
                },
                set     : function(id, node){
                    if (typeof pure.components.dialogs.A.shadow.storage.data[id] === 'undefined'){
                        pure.components.dialogs.A.shadow.storage.data[id] = {
                            id      : id,
                            node    : node
                        };
                        return pure.components.dialogs.A.shadow.storage.data[id];
                    }
                    return null;
                }
            },
            open    : function(id, parent){
                var storage = pure.components.dialogs.A.shadow.storage,
                    node    = document.createElement('DIV');
                if (node !== null){
                    node.setAttribute('data-type-element', 'Pure.Components.Dialogs.A.Shadow');
                    if (storage.set(id, node) !== null){
                        parent.appendChild(node);
                        return node;
                    }
                }
                return null;
            },
            close   : function(id){
                var storage     = pure.components.dialogs.A.shadow.storage,
                    instance    = storage.get(id);
                if (instance !== null){
                    instance.node.parentNode.removeChild(instance.node);
                    return storage.remove(id);
                }
                return null;
            }
        },
        dialog : {
            validate    : function(params){
                return pure.tools.objects.validate(params, [    { name: "title",        type: "string", value: ''   },
                                                                { name: "innerHTML",    type: "string"              },
                                                                { name: "buttons",      type: "array"               },
                                                                { name: "width",        type: "number", value: 50   },
                                                                { name: "parent",       type: "node",   value: null }]);
            },
            open        : function(params){
                var id                  = pure.tools.IDs.get(pure.components.dialogs.A),
                    shadow              = pure.components.dialogs.A.shadow,
                    dialog              = null,
                    buttonsInnerHTML    = '',
                    instance            = null,
                    button              = null;
                if (pure.components.dialogs.A.dialog.validate(params) === true){
                    instance = shadow.open(id, (params.parent === null ? document.body : params.parent));
                    if (instance !== null){
                        dialog      = document.createElement('DIV');
                        dialog.setAttribute('data-type-element', 'Pure.Components.Dialogs.A.TableContainer');
                        for(var index = 0, maxIndex = params.buttons.length; index < maxIndex; index += 1){
                            if (pure.tools.objects.validate(params.buttons[index], [    { name: "title",        type: "string"                      },
                                                                                        { name: "handle",       type: "function",   value: null     },
                                                                                        { name: "closeAfter",   type: "boolean",    value: true     }]) === true){
                                params.buttons[index].id = id + '_' + index;
                                buttonsInnerHTML += '<a data-type-element="Pure.Components.Dialogs.A.Dialog.Button" data-id-element="' + params.buttons[index].id + '">' + params.buttons[index].title + '</a>';
                            }
                        }
                        dialog.innerHTML =  '<div data-type-element="Pure.Components.Dialogs.A.Container">'+
                                                '<div data-type-element="Pure.Components.Dialogs.A.Dialog" style="width: ' + params.width + '%; left:' + (100 - params.width) / 2 + '%;">'+
                                                    (params.title !== '' ? '<p data-type-element="Pure.Components.Dialogs.A.Dialog.Title">' + params.title + '</p>' : '') +
                                                    params.innerHTML +
                                                    '<div data-type-element="Pure.Components.Dialogs.A.Dialog.Buttons">' +
                                                        buttonsInnerHTML +
                                                    '</div>' +
                                                '</div>' +
                                            '</div>';
                        instance.appendChild(dialog);
                        for(var index = 0, maxIndex = params.buttons.length; index < maxIndex; index += 1){
                            if (typeof params.buttons[index].id !== 'undefined'){
                                button = pure.nodes.select.first('*[data-type-element="Pure.Components.Dialogs.A.Dialog.Button"][data-id-element="' + params.buttons[index].id + '"]');
                                if (button !== null){
                                    (function(id, node, handle, closeAfter){
                                        pure.events.add(node, 'click', function(){
                                            pure.system.runHandle(handle, null, 'pure.components.dialogs.A.dialog.open', this);
                                            if (closeAfter === true){
                                                shadow.close(id);
                                            }
                                        });
                                    }(id, button, params.buttons[index].handle, params.buttons[index].closeAfter));
                                }
                            }
                        }
                    }
                }
            }
        },
        open : function(params){
            return pure.components.dialogs.A.dialog.open(params);
        }
    };
}());