(function () {
    if (typeof window.pure                      !== "object") { window.pure                     = {}; }
    if (typeof window.pure.components           !== "object") { window.pure.components          = {}; }
    if (typeof window.pure.components.dialogs   !== "object") { window.pure.components.dialogs  = {}; }
    "use strict";
    if (typeof window.pure.components.dialogs.B !== 'undefined'){
        return false;
    }
    window.pure.components.dialogs.B = {
        shadow : {
            storage : {
                data    : {},
                get     : function(id){
                    return (typeof pure.components.dialogs.B.shadow.storage.data[id] !== 'undefined' ? pure.components.dialogs.B.shadow.storage.data[id] : null);
                },
                remove  : function(id){
                    if (typeof pure.components.dialogs.B.shadow.storage.data[id] !== 'undefined'){
                        pure.components.dialogs.B.shadow.storage.data[id] = null;
                        delete pure.components.dialogs.B.shadow.storage.data[id];
                        return true;
                    }
                    return null;
                },
                set     : function(id, node){
                    if (typeof pure.components.dialogs.B.shadow.storage.data[id] === 'undefined'){
                        pure.components.dialogs.B.shadow.storage.data[id] = {
                            id      : id,
                            node    : node
                        };
                        return pure.components.dialogs.B.shadow.storage.data[id];
                    }
                    return null;
                }
            },
            open    : function(id, parent){
                var storage = pure.components.dialogs.B.shadow.storage,
                    node    = document.createElement('DIV');
                if (node !== null){
                    node.setAttribute('data-type-element', 'Pure.Components.Dialogs.B.Shadow');
                    if (storage.set(id, node) !== null){
                        parent.appendChild(node);
                        return node;
                    }
                }
                return null;
            },
            close   : function(id){
                var storage     = pure.components.dialogs.B.shadow.storage,
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
                return pure.tools.objects.validate(params, [    { name: "title",        type: "string",     value: ''       },
                                                                { name: "innerHTML",    type: "string"                      },
                                                                { name: "buttons",      type: "array"                       },
                                                                { name: "afterInit",    type: "function",   value: null     },
                                                                { name: "width",        type: "number",     value: 50       },
                                                                { name: "fullHeight",   type: "boolean",    value: false    },
                                                                { name: "parent",       type: "node",       value: null     }]);
            },
            open        : function(params){
                var id                  = pure.tools.IDs.get('pure.components.dialogs.B'),
                    shadow              = pure.components.dialogs.B.shadow,
                    dialog              = null,
                    buttonsInnerHTML    = '',
                    instance            = null,
                    button              = null;
                if (pure.components.dialogs.B.dialog.validate(params) === true){
                    instance = shadow.open(id, (params.parent === null ? document.body : params.parent));
                    if (instance !== null){
                        dialog      = document.createElement('DIV');
                        dialog.setAttribute('data-type-element', 'Pure.Components.Dialogs.B.TableContainer');
                        for(var index = 0, maxIndex = params.buttons.length; index < maxIndex; index += 1){
                            if (pure.tools.objects.validate(params.buttons[index], [    { name: "title",        type: "string"                      },
                                                                                        { name: "handle",       type: "function",   value: null     },
                                                                                        { name: "closeAfter",   type: "boolean",    value: true     }]) === true){
                                params.buttons[index].id = id + '_' + index;
                                buttonsInnerHTML += '<div data-type-element="Pure.Components.Dialogs.B.Dialog.Button">' +
                                                        '<a data-type-element="Pure.Components.Dialogs.B.Dialog.Button" data-id-element="' + params.buttons[index].id + '">' + params.buttons[index].title + '</a>' +
                                                    '</div>';
                            }
                        }
                        if (params.fullHeight === false){
                            dialog.innerHTML =  '<div data-type-element="Pure.Components.Dialogs.B.Container">'+
                                                    '<div data-type-element="Pure.Components.Dialogs.B.Dialog" style="width: ' + params.width + '%; left:' + (100 - params.width) / 2 + '%;">'+
                                                        (params.title !== '' ? '<p data-type-element="Pure.Components.Dialogs.B.Dialog.Title">' + params.title + '</p>' : '') +
                                                        params.innerHTML +
                                                        '<div data-type-element="Pure.Components.Dialogs.B.Dialog.Buttons">' +
                                                            buttonsInnerHTML +
                                                        '</div>' +
                                                    '</div>' +
                                                '</div>';
                        }else{
                            dialog.innerHTML =  '<div data-type-element="Pure.Components.Dialogs.B.Container">'+
                                                    '<div data-type-element="Pure.Components.Dialogs.B.Dialog" style="width: ' + params.width + '%; left:' + (100 - params.width) / 2 + '%;height:90%;">'+
                                                        (params.title !== '' ? '<p data-type-element="Pure.Components.Dialogs.B.Dialog.Title">' + params.title + '</p>' : '') +
                                                        '<div data-type-element="Pure.Components.Dialogs.B.Dialog.FullContainer">' +
                                                            params.innerHTML +
                                                        '</div>' +
                                                        '<div data-type-element="Pure.Components.Dialogs.B.Dialog.Buttons" data-addition-type="fullheight">' +
                                                            buttonsInnerHTML +
                                                        '</div>' +
                                                    '</div>' +
                                                '</div>';
                        }
                        instance.appendChild(dialog);
                        for(var index = 0, maxIndex = params.buttons.length; index < maxIndex; index += 1){
                            if (typeof params.buttons[index].id !== 'undefined'){
                                button = pure.nodes.select.first('*[data-type-element="Pure.Components.Dialogs.B.Dialog.Button"][data-id-element="' + params.buttons[index].id + '"]');
                                if (button !== null){
                                    (function(id, node, handle, closeAfter){
                                        pure.events.add(node, 'click', function(){
                                            if (handle !== null){
                                                pure.system.runHandle(handle, null, 'pure.components.dialogs.B.dialog.open', this);
                                            }
                                            if (closeAfter === true){
                                                shadow.close(id);
                                            }
                                        });
                                    }(id, button, params.buttons[index].handle, params.buttons[index].closeAfter));
                                }
                            }
                        }
                        if (params.afterInit !== null){
                            pure.system.runHandle(
                                params.afterInit,
                                null,
                                'pure.components.dialogs.B.dialog.open',
                                this
                            );
                        }
                        return id;
                    }
                }
            }
        },
        open    : function(params){
            return pure.components.dialogs.B.dialog.open(params);
        },
        close   : function(id){
            pure.components.dialogs.B.shadow.close(id);
        }
    };
}());