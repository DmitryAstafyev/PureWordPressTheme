(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components               !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.uploader      !== "object") { window.pure.components.uploader     = {}; }
    "use strict";
    window.pure.components.uploader.module = {
        ways : {
            upload : function(file, destination, handles, id, field, fields){
                var fileReader  = pure.components.uploader.module.ways.viaFileReader.isSupport(),
                    formData    = pure.components.uploader.module.ways.viaFormData.isSupport();
                fileReader = (typeof field === 'string' ? false : fileReader);
                fileReader = (fields instanceof Array   ? false : fileReader);
                if (formData === true){
                    return pure.components.uploader.module.ways.viaFormData.   upload(file, destination, handles, id, field, fields);
                }else if(fileReader === true){
                    return pure.components.uploader.module.ways.viaFileReader. upload(file, destination, handles, id);
                }else{
                    return pure.components.uploader.module.ways.viaIFrame.     upload(file, destination, handles, id, field, fields);
                }
            },
            validate        : function(file, destination, handles, id){
                if (typeof file !== 'undefined' && typeof destination === 'string'){
                    return true;
                }else{
                    return false;
                }
            },
            viaFileReader   : {
                isSupport : function(){
                    return (window.FileReader === undefined ? false : true);
                },
                upload : function(file, destination, handles, id){
                    var id          = (typeof id            === 'string' ? id           : pure.tools.IDs.get('pure.uploader.request.id')),
                        handles     = (typeof handles       === 'object' ? handles      : null),
                        XMLRequest  = null,
                        fileReader  = null;
                    if (pure.components.uploader.module.ways.validate(file, destination, handles, id) === true) {
                        fileReader = new FileReader();
                        pure.events.add(
                            fileReader,
                            'load',
                            function(){
                                var XMLRequest  = new XMLHttpRequest(),
                                    boundary    = 'pureuploader',
                                    bodyRequest = '';
                                pure.components.uploader.module.ways.common.attachEvents(XMLRequest, handles, id, 'pure.components.uploader.module.ways.viaFileReader.upload');
                                XMLRequest.open('POST', destination);
                                XMLRequest.setRequestHeader('Content-type',     'multipart/form-data; boundary="' + boundary + '"');
                                XMLRequest.setRequestHeader('Cache-Control',    'no-cache');
                                bodyRequest = "--" + boundary + "\r\n";
                                bodyRequest += "Content-Disposition: form-data; name='superfile'; filename='" + unescape( encodeURIComponent(file.name)) + "'\r\n";
                                bodyRequest += "Content-Type: application/octet-stream\r\n\r\n";
                                bodyRequest += fileReader.result + "\r\n";
                                bodyRequest += "--" + boundary + "--";
                                if(XMLRequest.sendAsBinary) {
                                    XMLRequest.sendAsBinary(bodyRequest);
                                } else {
                                    XMLRequest.send(bodyRequest);
                                }
                            });
                        fileReader.readAsBinaryString(file);
                        return true;
                    }
                    return null;
                }
            },
            viaFormData     : {
                isSupport   : function(){
                    return (window.FormData === undefined ? false : true);
                },
                upload      : function(file, destination, handles, id, field, fields){
                    var id          = (typeof id            === 'string' ? id           : pure.tools.IDs.get('pure.uploader.request.id')),
                        field       = (typeof field         === 'string' ? field        : 'file'),
                        handles     = (typeof handles       === 'object' ? handles      : null),
                        fields      = (fields instanceof Array ? fields                 : null),
                        XMLRequest  = null,
                        formData    = null;
                    if (pure.components.uploader.module.ways.validate(file, destination, handles, id) === true){
                        XMLRequest = new XMLHttpRequest();
                        pure.components.uploader.module.ways.common.attachEvents(XMLRequest, handles, id, 'pure.components.uploader.module.ways.viaFormData.upload');
                        formData = new FormData();
                        formData.append(field, file);
                        if (fields !== null){
                            for(var index = fields.length - 1; index >= 0; index -= 1){
                                if (typeof fields[index].name === 'string' && typeof fields[index].value === 'string'){
                                    formData.append(fields[index].name, fields[index].value);
                                }
                            }
                        }
                        XMLRequest.open('POST', destination);
                        XMLRequest.send(formData);
                        return true;
                    }
                    return null;
                }
            },
            viaIFrame       : {
                upload      : function(file, destination, handles, id, field, fields) {

                }
            },
            common : {
                attachEvents : function(XMLRequest, handles, id, from){
                    if (XMLRequest.upload) {
                        if (typeof handles.progress === 'function'){
                            pure.events.add(XMLRequest.upload, 'progress',
                                function(event){
                                    pure.system.runHandle(
                                        handles.progress,
                                        {
                                            event   : event,
                                            id      : id,
                                            loaded  : (typeof event.loaded  !== 'undefined' ? event.loaded  : null),
                                            total   : (typeof event.total   !== 'undefined' ? event.total   : null)
                                        },
                                        from,
                                        this
                                    );
                                });
                        }
                        if (typeof handles.load === 'function'){
                            pure.events.add(XMLRequest.upload, 'load',
                                function(event){
                                    pure.system.runHandle(
                                        handles.load,
                                        {
                                            event   : event,
                                            id      : id
                                        },
                                        from,
                                        this
                                    );
                                });
                        }
                        if (typeof handles.error === 'function'){
                            pure.events.add(XMLRequest.upload, 'error',
                                function(event){
                                    pure.system.runHandle(
                                        handles.error,
                                        {
                                            event   : event,
                                            id      : id
                                        },
                                        from,
                                        this
                                    );
                                });
                        }
                        if (typeof handles.timeout === 'function'){
                            pure.events.add(XMLRequest, 'timeout',
                                function(event){
                                    pure.system.runHandle(
                                        handles.timeout,
                                        {
                                            event   : event,
                                            id      : id
                                        },
                                        from,
                                        this
                                    );
                                });
                        }
                        if (typeof handles.ready === 'function'){
                            pure.events.add(XMLRequest, 'readystatechange',
                                function(event){
                                    if (typeof event.target === "object") {
                                        if (typeof event.target.readyState === "number") {
                                            if (event.target.readyState === 4) {
                                                if (typeof id === "string") {
                                                    if (event.target.status === 200) {
                                                        pure.system.runHandle(
                                                            handles.ready,
                                                            {
                                                                event       : event,
                                                                id          : id,
                                                                response    : event.target.responseText
                                                            },
                                                            from,
                                                            this
                                                        );
                                                    } else {
                                                        if (typeof handles.error === 'function'){
                                                            pure.system.runHandle(
                                                                handles.error,
                                                                {
                                                                    event       : event,
                                                                    id          : id
                                                                },
                                                                from,
                                                                this
                                                            );
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                        }
                    }
                }
            }
        },
        /*
         handles : {
             ready      : function,
             error      : function,
             timeout    : function,
             load       : function, //fire before [ready]
             progress   : function,
         }
        */
        upload : function(file, destination, handles, id, field, fields){
            return pure.components.uploader.module.ways.upload(file, destination, handles, id, field, fields);
        }
    };
}());