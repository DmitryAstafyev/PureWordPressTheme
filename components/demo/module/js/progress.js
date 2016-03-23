(function () {
    if (typeof window.pure              !== "object") { window.pure             = {}; }
    if (typeof window.pure.components   !== "object") { window.pure.components  = {}; }
    "use strict";
    window.pure.components.importer = {
        init : function(){
            var console = pure.nodes.select.first('textarea[id="PureImportDataLogs"]');
            if (console !== null){
                pure.components.importer.logs.console = console;
                pure.components.importer.progress.step();
            }
        },
        progress : {
            history : [],
            step    : function(){
                setTimeout(
                    pure.components.importer.progress.handle,
                    500
                );
            },
            handle  : function(){
                var request     = 'command=demo_importer_progress&index=[index]',
                    destination = pure.system.getInstanceByPath('pure.globalsettings.requestURL');
                if (request !== null && destination !== null){
                    request = request.replace(/\[index\]/, pure.components.importer.progress.history.length);
                    pure.tools.request.send({
                        type        : 'POST',
                        url         : destination,
                        request     : request,
                        onrecieve   : function (id_request, response) {
                            pure.components.importer.progress.onrecieve(id_request, response);
                        },
                        onreaction  : null,
                        onerror     : function (event, id_request) {
                            pure.components.importer.progress.onerror(event, id_request);
                        },
                        ontimeout   : function (event, id_request) {
                            pure.components.importer.progress.onerror(event, id_request);
                        }
                    });
                }
            },
            onrecieve   : function(id_request, response){
                var data    = null,
                    finish  = false;
                if (response !== 'fail'){
                    try{
                        data = JSON.parse(response);
                        if (typeof data.length === 'number'){
                            for(var index = 0, max_index = data.length; index < max_index; index += 1){
                                pure.components.importer.logs.add(
                                    '['+ data[index].time +']' +
                                    '['+ data[index].author +']::' +
                                    data[index].log
                                );
                                pure.components.importer.progress.history.push(data[index]);
                                if (data[index].log == 'finished'){
                                    finish = true;
                                }
                            }
                            if (finish === false){
                                pure.components.importer.progress.step();
                            }
                        }
                    }catch (e){
                        pure.components.importer.logs.add('Server returned wrong data. Import scripts MAYBE stopped. See logs in table wp_options, record [pure_export_demo_logs]');
                    }
                }else{
                    pure.components.importer.logs.add('Server returned error. Import scripts stopped or was not started. See logs in table wp_options, record [pure_export_demo_logs]');
                }
            },
            onerror     : function(event, id_request){
                pure.components.importer.logs.add('Server does not respond. Import scripts stopped. See logs in table wp_options, record [pure_export_demo_logs]');
            }
        },
        logs : {
            console : null,
            add     : function(message){
                pure.components.importer.logs.console.value     = pure.components.importer.logs.console.value + message + '\r\n';
                pure.components.importer.logs.console.scrollTop = pure.components.importer.logs.console.scrollHeight;
            }
        }
    };
    pure.system.start.add(pure.components.importer.init);
}());