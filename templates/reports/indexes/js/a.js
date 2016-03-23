(function () {
    if (typeof window.pure          !== "object") { window.pure         = {}; }
    if (typeof window.pure.reports  !== "object") { window.pure.reports = {}; }
    "use strict";
    window.pure.reports.A = {
        init        : function(){
            pure.reports.A.initialize.votes();
            pure.reports.A.hotUpdate.init();
        },
        initialize  : {
            votes   : function(){
                var instances = pure.nodes.select.all('*[data-report-index-vote]:not([data-element-inited])');
                if (instances !== null){
                    Array.prototype.forEach.call(
                        instances,
                        function(instance, _index, source){
                            var index       = instance.getAttribute('data-report-index-vote'),
                                object      = instance.getAttribute('data-report-object'    ),
                                segments    = null;
                            if (index !== null && object !== null){
                                segments = pure.nodes.select.all('*[data-report-index-vote="' + index + '"][data-report-object="' + object + '"] *[data-report-segment-value]');
                                if (segments !== null){
                                    Array.prototype.forEach.call(
                                        segments,
                                        function(segment, _index, source){
                                            var value = segment.getAttribute('data-report-segment-value');
                                            if (value !== null){
                                                pure.events.add(
                                                    segment,
                                                    'click',
                                                    function(){
                                                        pure.reports.A.actions.vote.send(object, index, value);
                                                    }
                                                );
                                            }
                                        }
                                    );
                                }
                            }
                            instance.setAttribute('data-element-inited', 'true');
                        }
                    );
                }
            }
        },
        actions : {
            vote : {
                progress    : {
                    data    : {},
                    isFree  : function(object_id, index){
                        var data = pure.reports.A.actions.vote.progress.data;
                        return (typeof data[object_id + '-' + index] !== 'undefined' ? true : false);
                    },
                    set     : function(object_id, index){
                        var data = pure.reports.A.actions.vote.progress.data,
                            node = pure.nodes.select.first('*[data-report-index-vote="' + index + '"][data-report-object="' + object_id + '"]');
                        if (node !== null){
                            data[object_id + '-' + index] = pure.templates.progressbar.B.show(node);
                        }
                    },
                    clear   : function(object_id, index){
                        var data = pure.reports.A.actions.vote.progress.data;
                        if (typeof data[object_id + '-' + index] !== 'undefined'){
                            pure.templates.progressbar.B.hide(data[object_id + '-' + index]);
                            data[object_id + '-' + index] = null;
                            delete data[object_id + '-' + index];
                        }
                    }
                },
                send        : function(object_id, index, value){
                    var request     = pure.system.getInstanceByPath('pure.reports.configuration.request.vote'   ),
                        destination = pure.system.getInstanceByPath('pure.reports.configuration.destination'    );
                    if (request !== null && destination !== null){
                        if (pure.reports.A.actions.vote.progress.isFree(object_id, index) === false){
                            pure.reports.A.actions.vote.progress.set(object_id, index);
                            request     = request.replace(/\[post_id\]/,    object_id   );
                            request     = request.replace(/\[index\]/,      index       );
                            request     = request.replace(/\[value\]/,      value       );
                            pure.tools.request.send({
                                type        : 'POST',
                                url         : destination,
                                request     : request,
                                onrecieve   : function (id_request, response) {
                                    pure.reports.A.actions.vote.receive(id_request, response, object_id, index);
                                },
                                onreaction  : null,
                                onerror     : function (event, id_request) {
                                    pure.reports.A.actions.vote.error(event, id_request, object_id, index);
                                },
                                ontimeout   : function (event, id_request) {
                                    pure.reports.A.actions.vote.error(event, id_request, object_id, index);
                                }
                            });
                        }
                    }
                },
                receive : function(id_request, response, object_id, index){
                    var value = null;
                    pure.reports.A.actions.vote.progress.clear(object_id, index);
                    if (response !== 'access_error' && response !== 'voted' && response !== 'error'){
                        value = parseInt(response, 10);
                        if (typeof value === 'number'){
                            pure.reports.A.actions.updateValue(object_id, index, value, false);
                            pure.reports.A.hotUpdate.call();
                        }
                    }
                },
                error   : function(event, id_request, object_id, index){
                    pure.reports.A.actions.vote.progress.clear(object_id, index);
                }
            },
            updateValue : function(object_id, index, value, no_remove){
                var node    = pure.nodes.select.first('*[data-report-index-value="' + index + '"][data-report-object="' + object_id + '"]'),
                    vote    = pure.nodes.select.first('*[data-report-index-vote="' + index + '"][data-report-object="' + object_id + '"]'),
                    max     = null,
                    label   = pure.nodes.select.first('*[data-report-index-label="' + index + '"][data-report-object="' + object_id + '"]');
                if (vote !== null && no_remove === false){
                    vote.parentNode.removeChild(vote);
                }
                if (node !== null ){
                    max = node.getAttribute('data-report-index-max');
                    if (max !== null){
                        node.style.width = ((value / max) * 100) + '%';
                        if (label !== null){
                            label.innerHTML = value.toFixed(2);
                        }
                    }
                }
            }
        },
        hotUpdate : {
            inited      : false,
            init        : function(){
                if (pure.reports.A.hotUpdate.inited === false){
                    pure.appevents.Actions.listen(
                        'webSocketServerEvents',
                        'new_index_value',
                        pure.reports.A.hotUpdate.processing,
                        'update_value_of_index_in_report'
                    );
                    pure.reports.A.hotUpdate.inited = true;
                }
            },
            call        : function(){
                //Server notification
                pure.appevents.Actions.call('webSocketsServer','wakeup', null, null);
            },
            processing : function(params){
                var parameters = (typeof params.parameters === 'object' ? params.parameters : null);
                if (parameters !== null){
                    if (typeof params.parameters !== 'undefined'){
                        if (typeof params.parameters.object_id      !== 'undefined' &&
                            typeof params.parameters.index          !== 'undefined' &&
                            typeof params.parameters.vote           !== 'undefined' ){
                            pure.reports.A.actions.updateValue(
                                params.parameters.object_id,
                                params.parameters.index,
                                params.parameters.vote,
                                true
                            );
                        }
                    }
                }
            }
        }
    };
    pure.system.start.add(pure.reports.A.init);
}());