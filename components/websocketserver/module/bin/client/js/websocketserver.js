(function () {
    if (typeof window.pure                              !== "object") { window.pure                             = {}; }
    if (typeof window.pure.components                   !== "object") { window.pure.components                  = {}; }
    if (typeof window.pure.components.webServerSocket   !== "object") { window.pure.components.webServerSocket  = {}; }
    "use strict";
    window.pure.components.webServerSocket.module = {
        connection : {
            webSocket   : {
                socket  : null,
                ready   : false
            },
            connect     : function(){
                var settings    = pure.system.getInstanceByPath('pure.globalsettings.webSocketServer'),
                    webSocket   = pure.components.webServerSocket.module.connection.webSocket;
                if (settings !== null){
                    if (webSocket.socket === null){
                        try{
                            webSocket.socket = new WebSocket(
                                'ws://' + settings.address + ':' + settings.port
                            );
                        }catch (exception){
                            pure.components.webServerSocket.module.connection.restart();
                            return false;
                        }
                        pure.events.add(webSocket.socket, 'open',       pure.components.webServerSocket.module.connection.events.onOpen     );
                        pure.events.add(webSocket.socket, 'close',      pure.components.webServerSocket.module.connection.events.onClose    );
                        pure.events.add(webSocket.socket, 'message',    pure.components.webServerSocket.module.connection.events.onMessage  );
                        pure.events.add(webSocket.socket, 'error',      pure.components.webServerSocket.module.connection.events.onError    );
                    }
                }
            },
            restart     : function(){
                pure.components.webServerSocket.module.connection.webSocket.socket  = null;
                pure.components.webServerSocket.module.connection.webSocket.ready   = false;
                setTimeout(pure.components.webServerSocket.module.connection.connect, 5000);
            },
            events      : {
                onOpen      : function(event){
                    //console.log('Connection successfully opened (readyState ' + event.target.readyState+')');
                    pure.components.webServerSocket.module.connection.webSocket.ready = true;
                },
                onClose     : function(event){
                    if(event.target.readyState == 2){
                        //console.log('Closing... The connection is going throught the closing handshake (readyState '+event.target.readyState+')');
                    } else if(event.target.readyState == 3){
                        //console.log('Connection closed... The connection has been closed or could not be opened (readyState '+event.target.readyState+')');
                        pure.components.webServerSocket.module.connection.restart();
                    } else{
                        //console.log('Connection closed... (unhandled readyState ' + event.target.readyState + ')');
                        pure.components.webServerSocket.module.connection.restart();
                    }
                },
                onMessage   : function(event){
                    var response = (typeof event.data === 'string' ? event.data : null);
                    if (response !== null){
                        pure.components.webServerSocket.module.actions.income.processing(response);
                        //console.log('Server says: ' + response);
                    }
                },
                onError     : function(event){
                    var webSocket = pure.components.webServerSocket.module.connection.webSocket;
                    if (webSocket.socket.readyState != WebSocket.OPEN){
                        //console.log('WebSocket server was not found...');
                        pure.components.webServerSocket.module.connection.restart();
                    }
                }
            },
            send : function(message){
                var webSocket = pure.components.webServerSocket.module.connection.webSocket;
                if (webSocket.socket !== null && webSocket.ready === true){
                    try{
                        webSocket.socket.send(message);
                    }catch(e){
                        return false;
                    }
                }
            }
        },
        actions : {
            outcome : {
                authorization   : function(){
                    var data = {
                        group   : "auth",
                        command : "authorization",
                        user_id : pure.globalsettings.webSocketServer.user_id,
                        token   : pure.globalsettings.webSocketServer.token
                    };
                    return pure.components.webServerSocket.module.connection.send(JSON.stringify(data));
                },
                wakeup          : function(){
                    var data = {
                        group   : "actions",
                        command : "wakeup",
                        user_id : pure.globalsettings.webSocketServer.user_id,
                        token   : pure.globalsettings.webSocketServer.token
                    };
                    return pure.components.webServerSocket.module.connection.send(JSON.stringify(data));
                }
            },
            income : {
                processing  : function(response){
                    var _response = null;
                    try{
                        _response = pure.convertor.UTF8.decode(
                            pure.convertor.BASE64.decode(response)
                        );
                        _response = JSON.parse(_response);
                    }catch (e){
                        return false;
                    }
                    if (_response !== null){
                        if (typeof _response.group === 'string' && typeof _response.command === 'string'){
                            switch (_response.group){
                                case 'auth':
                                    switch (_response.command){
                                        case 'require':
                                            pure.components.webServerSocket.module.actions.outcome.authorization();
                                            break;
                                    }
                                    break;
                                case 'events':
                                    switch (_response.command){
                                        case 'event':
                                            pure.appevents.Actions.call(
                                                'webSocketServerEvents',
                                                _response.event,
                                                _response,
                                                null
                                            );
                                            break;
                                    }
                                    break;
                            }
                        }
                    }
                },
                accept      : function(){

                }
            }
        },
        appevents : {
            init : function(){
                pure.appevents.Actions.listen(
                    'webSocketsServer',
                    'wakeup',
                    pure.components.webServerSocket.module.actions.outcome.wakeup,
                    'webSocketsServer_WakeUp'
                );
            }
        },
        init : function(){
            pure.components.webServerSocket.module.connection.  connect();
            pure.components.webServerSocket.module.appevents.   init();
        }
    };
    pure.system.start.add(pure.components.webServerSocket.module.init);
}());