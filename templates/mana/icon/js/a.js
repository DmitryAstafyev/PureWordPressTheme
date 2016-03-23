(function () {
    if (typeof window.pure              !== "object") { window.pure             = {}; }
    if (typeof window.pure.mana         !== "object") { window.pure.mana        = {}; }
    if (typeof window.pure.mana.icon    !== "object") { window.pure.mana.icon   = {}; }
    "use strict";
    window.pure.mana.icon.A = {
        icons       : {
            init : function(){
                function proceedContainer(container){
                    var attributes = {
                            object      : container.getAttribute('data-engine-mana-object'  ),
                            objectID    : container.getAttribute('data-engine-mana-objectID'),
                            field       : container.getAttribute('data-engine-mana-field'   )
                        };
                    if (attributes.object !== null && attributes.objectID !== null){
                        attributes.field = (attributes.field === '' ? null : attributes.field);
                        if (pure.mana.icon.A.canvases.size.update(attributes.object, attributes.objectID) !== false){
                            pure.mana.icon.A.canvases.      refresh (attributes.object, attributes.objectID);
                            pure.mana.icon.A.labels.update. total   (attributes.object, attributes.objectID);
                            pure.mana.icon.A.buttons.       init    (attributes.object, attributes.objectID, attributes.field, container);
                        }
                    }
                    container.setAttribute('data-engine-element-inited', 'true');
                }
                var containers  = pure.nodes.select.all('*[data-engine-mana-element="Container"]:not([data-engine-element-inited])');
                if (containers !== null){
                    for(var index = containers.length - 1; index >= 0; index -= 1){
                        proceedContainer(containers[index]);
                    }
                }
            }

        },
        buttons     : {
            isPossible  : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.mana.icons.configuration.requestURL'      ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.mana.icons.configuration.requests.set'    ) === null ? false : true));
                return result;
            },
            init : function(object, objectID, field, container){
                var minus   = pure.nodes.select.first('*[data-engine-mana-element="Container"][data-engine-mana-object="' + object + '"][data-engine-mana-objectID="' + objectID + '"] *[data-engine-mana-element="Button.Minus"]'),
                    plus    = pure.nodes.select.first('*[data-engine-mana-element="Container"][data-engine-mana-object="' + object + '"][data-engine-mana-objectID="' + objectID + '"] *[data-engine-mana-element="Button.Plus"]');
                if (minus !== null && plus !== null){
                    pure.events.add(
                        minus,
                        'click',
                        function(){
                            pure.mana.icon.A.buttons.send(object, objectID, field, -1, container);
                        }
                    );
                    pure.events.add(
                        plus,
                        'click',
                        function(){
                            pure.mana.icon.A.buttons.send(object, objectID, field, 1, container);
                        }
                    );
                }
            },
            send : function(object, objectID, field, value, container){
                var request     = null,
                    progress    = null;
                if (pure.mana.icon.A.buttons.isPossible() !== false){
                    progress    = pure.templates.progressbar.A.show(container);
                    request     = pure.mana.icons.configuration.requests.set;
                    request     = request.replace(/\[object\]/gi,       object                          );
                    request     = request.replace(/\[object_id\]/gi,    objectID                        );
                    request     = request.replace(/\[value\]/gi,        value                           );
                    request     = request.replace(/\[field\]/gi,        (field === null ? '' : field)   );
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : pure.mana.icons.configuration.requestURL,
                        request     : request,
                        onrecieve   : function (id_request, response) {
                            pure.mana.icon.A.buttons.onRecieve(id_request, response, object, objectID, value, progress);
                        },
                        onreaction  : null,
                        onerror     : function (event, id_request) {
                            pure.mana.icon.A.buttons.onError(event, id_request, progress);
                        },
                        ontimeout   : function (id_request) {
                            pure.mana.icon.A.buttons.onError(id_request, id_request, progress);
                        }
                    });
                }
            },
            onRecieve : function(id_request, response, object, objectID, value, progress){
                var message = pure.mana.icon.A.dialogs.info;
                pure.templates.progressbar.A.hide(progress);
                switch (response){
                    case 'success':
                        pure.mana.icon.A.buttons.       remove          (object, objectID);
                        pure.mana.icon.A.canvases.      updateAttributes(object, objectID, value);
                        pure.mana.icon.A.canvases.      refresh         (object, objectID);
                        pure.mana.icon.A.labels.update. values          (object, objectID, value);
                        pure.mana.icon.A.labels.update. total           (object, objectID);
                        pure.mana.icon.A.hotUpdate.     call();
                        break;
                    case 'error':
                        message('Server error', 'Sorry, but server had gotten some error during work. Try again a bit later.');
                        break;
                    case 'fail':
                        message('Server error', 'Sorry, but server had gotten some error during work. Try again a bit later.');
                        break;
                    case 'voted':
                        message('It seems you had forgotten.', 'You had voted before. You cannot vote twice.');
                        pure.mana.icon.A.buttons.remove(object, objectID);
                        break;
                }
            },
            onError : function(event, id_request, progress){
                pure.templates.progressbar.A.hide(progress);
            },
            remove : function(object, objectID){
                var minus   = pure.nodes.select.first('*[data-engine-mana-element="Container"][data-engine-mana-object="' + object + '"][data-engine-mana-objectID="' + objectID + '"] *[data-engine-mana-element="Button.Minus"]'),
                    plus    = pure.nodes.select.first('*[data-engine-mana-element="Container"][data-engine-mana-object="' + object + '"][data-engine-mana-objectID="' + objectID + '"] *[data-engine-mana-element="Button.Plus"]');
                if (minus !== null && plus !== null) {
                    minus.parentNode.removeChild(minus);
                    plus.parentNode.removeChild(plus);
                }
            }
        },
        canvases    : {
            get                 : function(object, objectID){
                var canvas = pure.nodes.select.first('*[data-engine-mana-element="Container"][data-engine-mana-object="' + object + '"][data-engine-mana-objectID="' + objectID + '"] canvas[data-engine-mana-element="Switcher"]');
                return (canvas !== null ? canvas : null);
            },
            getAttributes       : function(canvasNode){
                var attributes = {
                    plus        : canvasNode.getAttribute('data-engine-mana-plus'           ),
                    minus       : canvasNode.getAttribute('data-engine-mana-minus'          ),
                    color_plus  : canvasNode.getAttribute('data-engine-mana-color-plus'     ),
                    color_minus : canvasNode.getAttribute('data-engine-mana-color-minus'    )
                };
                attributes.color_plus   = (attributes.color_plus    !== null ? attributes.color_plus    : 'rgb(0,255,0)');
                attributes.color_minus  = (attributes.color_minus   !== null ? attributes.color_minus   : 'rgb(255,0,0)');
                return (pure.tools.objects.isValueIn(attributes, null) === false ? attributes : null);
            },
            updateAttributes    : function(object, objectID, value){
                var canvas      = pure.mana.icon.A.canvases.get(object, objectID),
                    attributes  = null;
                if (canvas !== null){
                    attributes = pure.mana.icon.A.canvases.getAttributes(canvas);
                    if (attributes !== null){
                        switch (value){
                            case -1:
                                canvas.setAttribute('data-engine-mana-minus', (parseInt(attributes.minus, 10) + 1));
                                break;
                            case 1:
                                canvas.setAttribute('data-engine-mana-plus', (parseInt(attributes.plus, 10) + 1));
                                break;
                        }
                    }
                }
            },
            rewriteAttributes    : function(object, objectID, plus, minus){
                var canvas      = pure.mana.icon.A.canvases.get(object, objectID);
                if (canvas !== null){
                    canvas.setAttribute('data-engine-mana-minus',   parseInt(minus, 10  ));
                    canvas.setAttribute('data-engine-mana-plus',    parseInt(plus, 10   ));
                }
            },
            size : {
                detect : {
                    cache   : {},
                    proceed : function(canvas){
                        function emulate(size){
                            var node    = document.createElement('div'),
                                sizePX  = null;
                            node.style.opacity  = 0.01;
                            node.style.position = 'absolute';
                            node.style.top      = '-10000px';
                            node.style.left     = '-10000px';
                            node.style.width    = size.width;
                            node.style.height   = size.height;
                            document.body.appendChild(node);
                            sizePX = pure.nodes.render.size(node);
                            document.body.removeChild(node);
                            node = null;
                            if (sizePX.width !== 0 && sizePX.height !== 0){
                                return sizePX;
                            }
                            return null;
                        };
                        var size        = {
                                width   : canvas.getAttribute('data-engine-mana-canvas-width'),
                                height  : canvas.getAttribute('data-engine-mana-canvas-height')
                            },
                            cache       = pure.mana.icon.A.canvases.size.detect.cache,
                            property    = null,
                            sizePX      = null;
                        if (pure.tools.objects.isValueIn(size, null) === false){
                            property = 'set' + size.width + '_to_' + size.height;
                            if (typeof cache[property] !== 'undefined'){
                                return cache[property];
                            }else{
                                sizePX = emulate(size);
                                if (sizePX !== null){
                                    cache[property] = sizePX;
                                    return sizePX;
                                }
                            }
                        }
                        return null;
                    }
                },
                update : function(object, objectID){
                    var canvas  = pure.mana.icon.A.canvases.get(object, objectID),
                        size    = null;
                    if (canvas !== null){
                        size = pure.mana.icon.A.canvases.size.detect.proceed(canvas);
                        if (size !== null){
                            canvas.width   = size.width;
                            canvas.height  = size.height;
                            return true;
                        }
                    }
                    return false;
                }
            },
            refresh : function(object, objectID){
                function draw(canvas, plus, minus, color_plus, color_minus){
                    var context = null,
                        width   = parseInt(canvas.width,    10),
                        height  = parseInt(canvas.height,   10),
                        x       = null,
                        y       = null,
                        r       = null,
                        s       = null;
                    if (typeof canvas.getContext !== 'undefined'){
                        context = canvas.getContext('2d');
                        x = width / 2;
                        y = height / 2;
                        s = width*0.1;
                        r = x - s;
                        //Plus
                        context.beginPath();
                        context.arc(x, y, r, 0, ((Math.PI*2)/(plus + minus)) * plus, false);
                        context.lineWidth   = s;
                        context.strokeStyle = color_plus;
                        context.stroke();
                        context.closePath();
                        //Minus
                        context.beginPath();
                        context.arc(x, y, r, ((Math.PI*2)/(plus + minus)) * plus, (Math.PI*2), false);
                        context.lineWidth   = s;
                        context.strokeStyle = color_minus;
                        context.stroke();
                        context.closePath();
                    }
                };
                var canvas  = pure.mana.icon.A.canvases.get(object, objectID),
                    data    = null;
                if (canvas !== null){
                    data = pure.mana.icon.A.canvases.getAttributes(canvas);
                    if (data !== null){
                        draw(
                            canvas,
                            parseInt(data.plus, 10),
                            parseInt(data.minus, 10),
                            data.color_plus,
                            data.color_minus
                        );
                    }
                }
            }

        },
        labels      : {
            update  : {
                total   : function(object, object_id){
                    var labels  = pure.nodes.select.all('*[data-engine-mana-element="Container"][data-engine-mana-objectID="' + object_id + '"][data-engine-mana-object="' + object + '"] *[data-engine-mana-element="Label.Total"]'),
                        value   = null,
                        property = 'data-engine-mana-value';
                    if (labels !== null){
                        for(var index = labels.length - 1; index >= 0; index -= 1){
                            value = parseInt(labels[index].innerHTML, 10);
                            if (value < 0){
                                labels[index].setAttribute(property, 'negative');
                            }else if(value === 0){
                                labels[index].setAttribute(property, 'neutral');
                            }else if(value > 0){
                                labels[index].setAttribute(property, 'positive');
                            }
                        }
                    }
                },
                values  : function(object, objectID, value){
                    function update(nodes, value){
                        for (var index = nodes.length - 1; index >= 0; index -= 1){
                            nodes[index].innerHTML = value;
                        }
                    }
                    var labels = {
                            total   : pure.nodes.select.all('*[data-engine-mana-element="Container"][data-engine-mana-objectID="' + objectID + '"][data-engine-mana-object="' + object + '"] *[data-engine-mana-element="Label.Total"]'),
                            minus   : pure.nodes.select.all('*[data-engine-mana-element="Container"][data-engine-mana-objectID="' + objectID + '"][data-engine-mana-object="' + object + '"] *[data-engine-mana-element="Label.Minus"]'),
                            plus    : pure.nodes.select.all('*[data-engine-mana-element="Container"][data-engine-mana-objectID="' + objectID + '"][data-engine-mana-object="' + object + '"] *[data-engine-mana-element="Label.Plus"]')
                        },
                        minus   = null,
                        plus    = null;
                    if (pure.tools.objects.isValueIn(labels, null) === false){
                        if (labels.total.length > 0 && labels.minus.length > 0 && labels.plus.length > 0){
                            minus   = parseInt(labels.minus[0].innerHTML, 10);
                            plus    = parseInt(labels.plus[0].innerHTML, 10);
                            minus   = (value === -1 ? minus + 1 : minus );
                            plus    = (value === 1  ? plus + 1  : plus  );
                            update(labels.total,    (plus - minus)  );
                            update(labels.minus,    minus           );
                            update(labels.plus,     plus            );
                        }
                    }
                },
                rewrite : function(object, objectID, _plus, _minus){
                    function update(nodes, value){
                        for (var index = nodes.length - 1; index >= 0; index -= 1){
                            nodes[index].innerHTML = value;
                        }
                    }
                    var labels = {
                            total   : pure.nodes.select.all('*[data-engine-mana-element="Container"][data-engine-mana-objectID="' + objectID + '"][data-engine-mana-object="' + object + '"] *[data-engine-mana-element="Label.Total"]'),
                            minus   : pure.nodes.select.all('*[data-engine-mana-element="Container"][data-engine-mana-objectID="' + objectID + '"][data-engine-mana-object="' + object + '"] *[data-engine-mana-element="Label.Minus"]'),
                            plus    : pure.nodes.select.all('*[data-engine-mana-element="Container"][data-engine-mana-objectID="' + objectID + '"][data-engine-mana-object="' + object + '"] *[data-engine-mana-element="Label.Plus"]')
                        },
                        minus   = parseInt(_minus, 10),
                        plus    = parseInt(_plus, 10);
                    if (pure.tools.objects.isValueIn(labels, null) === false){
                        if (labels.total.length > 0 && labels.minus.length > 0 && labels.plus.length > 0){
                            update(labels.total,    (plus - minus)  );
                            update(labels.minus,    minus           );
                            update(labels.plus,     plus            );
                        }
                    }
                }
            }
        },
        template    : {
            data        : null,
            init                : function(){
                function getNodeTemplate(outerHTML){
                    var node = document.createElement('div');
                    node.innerHTML = outerHTML;
                    return (node.childNodes.length === 1 ? node.childNodes[0] : null);
                }
                var template = pure.system.getInstanceByPath('pure.mana.icon.templates.A');
                if (template !== null){
                    template = pure.convertor.BASE64.decode(template);
                    template = pure.convertor.UTF8.  decode(template);
                    template = getNodeTemplate(template);
                    pure.mana.icon.A.template.data = {
                        innerHTML   : template.innerHTML,
                        attributes  : pure.nodes.attributes.get(template, ['data-engine-mana-element', 'data-engine-mana-objectID', 'data-engine-mana-object', 'data-engine-mana-field', 'style']),
                        nodeName    : template.nodeName
                    };
                    return true;
                }
                return null;
            },
            add         : function(fields, mark, field){
                var data    = pure.mana.icon.A.template.data,
                    node    = null,
                    total   = null,
                    field   = (typeof field !== 'undefined' ? field : null);
                if (data !== null){
                    total   = parseInt(fields.plus, 10) - parseInt(fields.minus, 10);
                    node    = document.createElement(data.nodeName);
                    node.setAttribute('data-engine-mana-element',   'Container'                     );
                    node.setAttribute('data-engine-mana-objectID',  fields.object_id                );
                    node.setAttribute('data-engine-mana-object',    fields.object_type              );
                    node.setAttribute('data-engine-mana-field',     (field !== null ? field : '')   );
                    pure.nodes.attributes.set(node, data.attributes);
                    node.innerHTML = data.innerHTML;
                    node.innerHTML = node.innerHTML.replace(/\[object_type\]/gi,    fields.object_type              );
                    node.innerHTML = node.innerHTML.replace(/\[object_id\]/gi,      fields.object_id                );
                    node.innerHTML = node.innerHTML.replace(/\[field\]/gi,          (field !== null ? field : '')   );
                    node.innerHTML = node.innerHTML.replace(/\[total\]/gi,          total                           );
                    node.innerHTML = node.innerHTML.replace(/\[plus\]/gi,           fields.plus                     );
                    node.innerHTML = node.innerHTML.replace(/\[minus\]/gi,          fields.minus                    );
                    mark.parentNode.insertBefore(node, mark);
                    mark.parentNode.removeChild(mark);
                }
            },
            addPackage : function(data, field){
                for(var key in data){
                    (function(fields, field){
                        var mark = pure.nodes.select.first('*[data-engine-mana-element="Mark"][data-engine-mana-objectID="' + fields.object_id + '"][data-engine-mana-object="' + fields.object_type + '"]');
                        if (mark !== null){
                            pure.mana.icon.A.template.add(fields, mark, field);
                        }
                    }(data[key], field));
                }
                pure.mana.icon.A.icons.init();
            }
        },
        add         : {
            attached    : false,
            init        : function(){
                if (pure.mana.icon.A.add.attached === false){
                    pure.mana.icon.A.add.attached = true;
                    pure.appevents.Actions.listen(
                        'pure.mana.icons',
                        'new',
                        pure.mana.icon.A.add.proceed,
                        'pure.mana.icons.new'
                    );
                }
            },
            /*
            * params = {object : string, IDs : array of {object_id : integer, user_id: integer}}
            * */
            proceed     : function(params){
                var object_type = (typeof params.object === 'string'    ? params.object : null),
                    IDs         = (typeof params.IDs    !== 'undefined' ? params.IDs    : null),
                    field       = (typeof params.field  !== 'undefined' ? params.field  : null),
                    user_ids    = [],
                    object_ids  = [];
                if (object_type !== null && IDs !== null){
                    if (IDs instanceof Array !== false){
                        for(var index = IDs.length - 1; index >= 0; index -= 1){
                            if (typeof IDs[index].object_id === 'number' && typeof IDs[index].user_id === 'number'){
                                if (IDs[index].object_id <= 0 || IDs[index].user_id <= 0){
                                    IDs.splice(index, 1);
                                }else{
                                    user_ids.   push(IDs[index].user_id     );
                                    object_ids. push(IDs[index].object_id   );
                                }
                            }else{
                                IDs.splice(index, 1);
                            }
                        }
                        if (IDs.length > 0){
                            pure.mana.icon.A.request.send(object_type, user_ids, object_ids, field);
                        }
                    }
                }
            }
        },
        request     : {
            isPossible  : function(){
                var result = true;
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.mana.icons.configuration.requestURL'      ) === null ? false : true));
                result = (result === false ? false : (pure.system.getInstanceByPath('pure.mana.icons.configuration.requests.get'    ) === null ? false : true));
                return result;
            },
            send : function(object_type, user_ids, object_ids, field){
                var request = null;
                if (pure.mana.icon.A.request.isPossible() !== false){
                    request = pure.mana.icons.configuration.requests.get;
                    request = request.replace(/\[object\]/gi,       object_type             );
                    request = request.replace(/\[user_ids\]/gi,     user_ids.join   (',')   );
                    request = request.replace(/\[object_ids\]/gi,   object_ids.join (',')   );
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : pure.mana.icons.configuration.requestURL,
                        request     : request,
                        onrecieve   : function (id_request, response) {
                            pure.mana.icon.A.request.onRecieve(id_request, response, field);
                        },
                        onreaction  : null,
                        onerror     : function (event, id_request) {
                            pure.mana.icon.A.request.onError(event, id_request);
                        },
                        ontimeout   : function (id_request) {
                            pure.mana.icon.A.request.onError(id_request, id_request);
                        }
                    });
                }
            },
            onRecieve   : function(id_request, response, field){
                var data = null;
                try{
                    data = JSON.parse(response);
                    if (typeof data === 'object'){
                        pure.mana.icon.A.template.addPackage(data, field);
                    }
                }catch(e){}
            },
            onError     : function(event, id_request){
                //Do nothing
            }
        },
        hotUpdate   : {
            inited      : false,
            init        : function(){
                if (pure.mana.icon.A.hotUpdate.inited === false){
                    pure.appevents.Actions.listen(
                        'webSocketServerEvents',
                        'mana_update',
                        pure.mana.icon.A.hotUpdate.processing,
                        'post_mana_update_handle'
                    );
                    pure.mana.icon.A.hotUpdate.inited = true;
                }
            },
            call        : function(){
                //Server notification
                pure.appevents.Actions.call('webSocketsServer','wakeup', null, null);
            },
            processing  : function(params){
                var parameters = (typeof params.parameters === 'object' ? params.parameters : null);
                if (parameters !== null){
                    if (typeof params.parameters !== 'undefined'){
                        if (typeof params.parameters.object_type    !== 'undefined' && typeof params.parameters.object_id   !== 'undefined' &&
                            typeof params.parameters.plus           !== 'undefined' && typeof params.parameters.minus       !== 'undefined'){
                            pure.mana.icon.A.canvases.      rewriteAttributes   (params.parameters.object_type, params.parameters.object_id, params.parameters.plus, params.parameters.minus);
                            pure.mana.icon.A.canvases.      refresh             (params.parameters.object_type, params.parameters.object_id);
                            pure.mana.icon.A.labels.update. rewrite             (params.parameters.object_type, params.parameters.object_id, params.parameters.plus, params.parameters.minus);
                        }
                    }
                }
            }
        },
        dialogs     : {
            info: function (title, message) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p data-post-element-type="Pure.Mana.Icon.A.Dialog">' + message + '</p>',
                    width       : 70,
                    parent      : document.body,
                    buttons     : [
                        {
                            title       : 'OK',
                            handle      : null,
                            closeAfter  : true
                        }
                    ]
                });
            }
        },
        init        : function(){
            pure.mana.icon.A.icons.     init();
            pure.mana.icon.A.template.  init();
            pure.mana.icon.A.add.       init();
            pure.mana.icon.A.hotUpdate. init();
        }
    };
    pure.system.start.add(pure.mana.icon.A.init);
}());