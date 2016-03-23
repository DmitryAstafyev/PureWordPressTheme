(function () {
    if (typeof window.pure              !== "object") { window.pure                 = {}; }
    if (typeof window.pure.mana         !== "object") { window.pure.mana            = {}; }
    if (typeof window.pure.mana.summary !== "object") { window.pure.mana.summary    = {}; }
    "use strict";
    window.pure.mana.summary.A = {
        init        : function(){
            var instances = pure.nodes.select.all('input[data-engine-mana-summary-value]:not([data-element-inited])');
            if (instances !== null){
                Array.prototype.forEach.call(
                    instances,
                    function(instance, _index, source){
                        var id      = instance.getAttribute('data-engine-mana-summary-value'),
                            button  = pure.nodes.select.first('*[data-engine-mana-summary-give="' + id + '"]');
                        if (button !== false){
                            pure.events.add(
                                button,
                                'click',
                                function () {
                                    pure.mana.summary.A.action.send(id, instance, button);
                                }
                            );
                        }
                        instance.setAttribute('data-element-inited', 'true');
                    }
                );
            }
        },
        action      : {
            send : function(id, input, button){
                var destination = pure.system.getInstanceByPath('pure.mana.summary.configuration.destination'  ),
                    request     = pure.system.getInstanceByPath('pure.mana.summary.configuration.request'      ),
                    max         = parseInt(input.max, 10),
                    min         = parseInt(input.min, 10),
                    value       = parseInt(input.value, 10);
                if (destination !== null && request !== null) {
                    if (pure.templates.progressbar.A.wrapper.isBusy(id) === false){
                        try{
                            value = parseInt(value, 10);
                        }catch (e){
                            return false;
                        }
                        if (value <= max && value >= min){
                            pure.templates.progressbar.A.wrapper.busy(id, button);
                            pure.tools.request.sendWithFields({
                                    type        : 'POST',
                                    url         : destination,
                                    request     : request,
                                    onrecieve   : function (id_request, response) {
                                        pure.mana.summary.A.action.receive(response, id, input, button);
                                    },
                                    onreaction  : null,
                                    onerror     : function (event, id_request) {
                                        pure.mana.summary.A.action.error(event, id, input, button);
                                    },
                                    ontimeout   : function (event, id_request) {
                                        pure.mana.summary.A.action.error(event, id, input, button);
                                    }
                                },
                                {
                                    value: value
                                }
                            );
                        }else{
                            pure.mana.summary.A.dialogs.info(
                                'Check value',
                                'Incorrect value of karma.'
                            );
                        }
                    }
                }
            },
            receive : function(response, id, input, button){
                function error(){
                    pure.mana.summary.A.dialogs.info(
                        'Unsuccessful operation',
                        'Sorry, but operation is unsuccessful. Possible reason - you do not have enough karama.'
                    );
                };
                var wallets = null;
                pure.templates.progressbar.A.wrapper.clear(id);
                try{
                    wallets = JSON.parse(response);
                    if (typeof wallets.source !== 'undefined' && typeof wallets.target !== 'undefined'){
                        pure.mana.summary.A.update(id, wallets);
                        input.value = '';
                        return true;
                    }
                    error();
                }catch (e){
                    error();
                }
            },
            error   : function(event, id, input, button){
                pure.templates.progressbar.A.wrapper.clear(id);
                pure.mana.summary.A.dialogs.info(
                    'Unsuccessful operation',
                    'Sorry, but operation is unsuccessful. Some error was with connection or on server side.'
                );
            }
        },
        update      : function(id, wallets){
            function calculate(data, wallets){
                var fields = {
                    values : {
                        total   : 0,
                        zero    : 0,
                        sandbox : parseInt(data.sandbox, 10),
                        user    : parseInt(wallets.target, 10)
                    },
                    left : {
                        dark    : 0,
                        gray    : 0,
                        light   : 0,
                        user    : 0
                    },
                    width : {
                        dark    : 0,
                        gray    : 0,
                        light   : 0
                    },
                    settings : {
                        offset      : parseFloat(data.offset),
                        min_offset  : parseInt(data.min_offset, 10)
                    }
                };
                if (fields.values.user > 0){
                    if (fields.values.user > fields.values.sandbox){
                        fields.values.total    =    fields.settings.min_offset +
                                                    fields.values.user +
                                                    (fields.settings.offset * fields.values.user > fields.settings.offset ? fields.settings.offset * fields.values.user : fields.settings.min_offset);
                    }else{
                        fields.values.total    =    fields.settings.min_offset +
                                                    fields.values.sandbox +
                                                    (fields.settings.offset * fields.values.sandbox > fields.settings.offset ? fields.settings.offset * fields.values.sandbox : fields.settings.min_offset);
                    }
                    fields.values.zero         =    fields.settings.min_offset;
                }else{
                    fields.values.total        =    (fields.settings.offset * (-fields.values.user)   > fields.settings.offset ? fields.settings.offset * (-fields.values.user)    : fields.settings.min_offset) +
                                                    (fields.settings.offset * fields.values.sandbox   > fields.settings.offset ? fields.settings.offset * fields.values.sandbox    : fields.settings.min_offset) +
                                                    (-fields.values.user)  +
                                                    fields.values.sandbox;
                    fields.values.zero         =    (fields.settings.offset * (-fields.values.user)   > fields.settings.offset ? fields.settings.offset * (-fields.values.user)    : fields.settings.min_offset) +
                                                    (-fields.values.user);
                }
                fields.left.dark   = 0;
                fields.left.gray   = ((fields.values.zero / fields.values.total) * 100) + '%';
                fields.left.light  = (((fields.values.zero + fields.values.sandbox)  / fields.values.total) * 100) + '%';
                fields.left.user   = (((fields.values.zero + fields.values.user)  / fields.values.total) * 100) + '%';
                fields.width.dark  = ((fields.values.zero / fields.values.total) * 100) + '%';
                fields.width.gray  = ((fields.values.sandbox / fields.values.total) * 100) + '%';
                fields.width.light = (((fields.values.total - fields.values.sandbox - fields.values.zero) / fields.values.total) * 100) + '%';
                return fields;
            };
            function updateStyle(id, type, propery, value){
                var nodes = pure.nodes.select.all('*[data-engine-mana-summary-' + type + '="' + id + '"]');
                if (nodes !== null){
                    Array.prototype.forEach.call(
                        nodes,
                        function(node, index, source){
                            node.style[propery] = value;
                        }
                    );
                }
            };
            function updateValue(id, type, value){
                var nodes = pure.nodes.select.all('*[data-engine-mana-summary-' + type + '="' + id + '"]');
                if (nodes !== null){
                    Array.prototype.forEach.call(
                        nodes,
                        function(node, index, source){
                            node.innerHTML = value;
                        }
                    );
                }
            }
            var data    = pure.system.getInstanceByPath('pure.mana.summary.data.' + id),
                fields  = null;
            if (data !== false){
                fields = calculate(data, wallets);
                updateStyle(id, 'dark_left',    'left',     fields.left.dark);
                updateStyle(id, 'gray_left',    'left',     fields.left.gray);
                updateStyle(id, 'light_left',   'left',     fields.left.light);
                updateStyle(id, 'member_left',  'left',     fields.left.user);
                updateStyle(id, 'dark_width',   'width',    fields.width.dark);
                updateStyle(id, 'gray_width',   'width',    fields.width.gray);
                updateStyle(id, 'light_width',  'width',    fields.width.light);
                updateValue(id, 'member_value', fields.values.user);
                updateValue(id, 'total',        wallets.source);
                updateValue(id, 'available',    wallets.source - fields.values.sandbox);
            }
        },
        dialogs     : {
            info: function (title, message) {
                pure.components.dialogs.B.open({
                    title       : title,
                    innerHTML   : '<p>' + message + '</p>',
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
        }
    };
    pure.system.start.add(pure.mana.summary.A.init);
}());