(function () {
    if (typeof window.pure          !== "object") { window.pure         = {}; }
    if (typeof window.pure.tools    !== "object") { window.pure.tools   = {}; }
    "use strict";
    window.pure.tools = {
        selections  : {
            clear   : function () {
                if (window.getSelection) {
                    window.getSelection().removeAllRanges();
                } else if (document.selection) {
                    document.selection.empty();
                }

            },
            deny    : function (node) {
                pure.events.add(node, "selectstart", function (event) {
                    return false;
                });
                pure.events.add(node, "mousedown", function (event) {
                    return false;
                });
            }
        },
        request     : {
            count   : 0,
            get id(){
                pure.tools.request.count += 1;
                return "request" + pure.tools.request.count;
            },
            sendWithFields  : function(params, fields){
                var regExp = null;
                if (typeof fields === 'object'){
                    for(var key in fields){
                        regExp          = new RegExp('\\[' + key + '\\]', 'gi');
                        params.request  = params.request.replace(regExp, fields[key]);
                    }
                }
                return pure.tools.request.send(params);
            },
            send    : function (params) {
                ///     <summary>Send asyn request by defined URL. [value] - default value.</summary>
                ///     <param name="params" type="Object">
                ///         {type           : string,               &#13;&#10;
                ///          url            : string,               &#13;&#10;                  
                ///          request        : string,               &#13;&#10;               
                ///          onrecieve      : function,     [null]  &#13;&#10;              
                ///          onreaction     : function,     [null]  &#13;&#10;              
                ///          onerror        : function,     [null]  &#13;&#10;               
                ///          ontimeout      : function,     [null]  &#13;&#10; 
                ///          forcedtimeout  : number (ms)   [20000] &#13;&#10;                           
                ///         }
                ///     </param>reaction
                ///     <returns type="boolean" mayBeNull="true">Null - if error. True - if is OK.</returns>
                var httpRequest     = null,
                    id              = (typeof params["id"]              === "string"    ? params["id"]              : null),
                    type            = (typeof params["type"]            === "string"    ? params["type"]            : null),
                    url             = (typeof params["url"]             === "string"    ? params["url"]             : null),
                    request         = (typeof params["request"]         === "string"    ? params["request"]         : ""),
                    onReceive       = (typeof params["onrecieve"]       === "function"  ? params["onrecieve"]       : null),
                    onReaction      = (typeof params["onreaction"]      === "function"  ? params["onreaction"]      : null),
                    onError         = (typeof params["onerror"]         === "function"  ? params["onerror"]         : null),
                    onTimeout       = (typeof params["ontimeout"]       === "function"  ? params["ontimeout"]       : null),
                    forcedTimeout   = (typeof params["forcedtimeout"]   === "number"    ? params["forcedtimeout"]   : 20000);
                if (type !== null && url !== null) {
                    if (typeof pure.events === "object") {
                        try {
                            httpRequest = new XMLHttpRequest();
                            if (httpRequest !== null) {
                                id          = pure.tools.request.id;
                                pure.events.add(httpRequest,
                                                "readystatechange",
                                                function (event) {
                                                    pure.tools.request.receive(event, id, onReaction, onReceive, onError);
                                                }
                                );
                                if (onTimeout !== null) {
                                    pure.events.add(httpRequest,
                                                    "timeout",
                                                    function (event) {
                                                        pure.tools.request.timeOut(event, id, onTimeout);
                                                    }
                                    );
                                }
                                httpRequest.open(type, url, true);
                                httpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                httpRequest.send(request);
                                return id;
                            } else {
                                return null;
                            }
                        } catch (e) {
                            return null;
                        }
                    }
                }
                return null;
            },
            receive : function (event, id, onReaction, onReceive, onError) {
                if (typeof event === "object") {
                    if (typeof event.target === "object") {
                        if (typeof event.target.readyState === "number") {
                            if (event.target.readyState === 4) {
                                if (typeof id === "string") {
                                    if (event.target.status === 200) {
                                        if (typeof onReceive === "function") {
                                            onReceive(id, event.target.responseText, event);
                                        }
                                    } else {
                                        if (typeof onError === "function") {
                                            onError(event, id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            },
            timeOut : function (event, id, onTimeout) {
                if (typeof event === "object") {
                    if (typeof onTimeout === "function") {
                        onTimeout(event, id);
                    }
                }
            }
        },
        xml         : {
            fromString: function (sourceString, includeRoot) {
                function make(nodes, resultObject) {
                    function setValue(fields, name, value) {
                        if (!fields[name]) {
                            fields[name] = value;
                            return value;
                        } else {
                            if (fields[name] instanceof Array !== true) {
                                fields[name] = [fields[name]];
                            }
                            fields[name].push(value);
                            return fields[name][fields[name].length - 1];
                        }
                    };
                    var nodes           = (typeof nodes !== "undefined" ? nodes : null),
                        resultObject    = (typeof resultObject  === "object"    ? resultObject  : null);
                    if (nodes !== null && resultObject !== null) {
                        if (typeof nodes.nodeType === "number") {
                            for (var index = nodes.childNodes.length - 1; index >= 0; index -= 1) {
                                if (nodes.childNodes[index].childNodes.length === 0) {
                                    if (typeof nodes.childNodes[index].tagName === "string") {
                                        setValue(resultObject, nodes.childNodes[index].tagName, nodes.childNodes[index].nodeValue);
                                    }
                                } else if (nodes.childNodes[index].childNodes.length === 1 && nodes.childNodes[index].firstChild.nodeType === 3){
                                    if (typeof nodes.childNodes[index].tagName === "string") {
                                        setValue(resultObject, nodes.childNodes[index].tagName, nodes.childNodes[index].firstChild.nodeValue);
                                    }
                                } else {
                                    make(   nodes.childNodes[index],
                                            setValue(resultObject, nodes.childNodes[index].tagName, {})
                                    );
                                }
                            }
                            return true;
                        }
                    }
                    return false;
                };
                var sourceString        = (typeof sourceString  === "string"    ? sourceString  : null  ),
                    includeRoot         = (typeof includeRoot   === "boolean"   ? includeRoot   : false ),
                    parsedObject        = null,
                    resultObject        = {},
                    microsoftXMLObject  = null;
                if (sourceString !== null) {
                    try {
                        if (window.DOMParser){
                            parsedObject = (new DOMParser()).parseFromString(sourceString, "application/xml");
                        }else if (window.ActiveXObject){
                            microsoftXMLObject          = new ActiveXObject("Microsoft.XMLDOM");
                            microsoftXMLObject.async    = false;
                            parsedObject                = microsoftXMLObject.loadXML(sourceString);
                            microsoftXMLObject          = null;
                        }else{
                            return null;
                        }
                        parsedObject = (parsedObject.nodeType   === 9       ? parsedObject = parsedObject.firstChild : parsedObject);//9 == DOCUMENT_NODE
                        parsedObject = (includeRoot             === true    ? parsedObject = parsedObject.firstChild : parsedObject);
                        if (make(parsedObject, resultObject) === true) {
                            return resultObject;
                        }
                    } catch (error) {
                        return null;
                    }
                    return null;
                }
                return null;
            }
        },
        objects     : {
            /*
            properties = [{name: string, type: string || [string]}, ...]
            or
            properties = {name: string, type: string || [string]}
            */
            validate    : function (targetObject, properties) {
                var targetObject    = (typeof targetObject  === "object"    ? targetObject  : null),
                    properties      = (typeof properties    !== "undefined" ? properties    : null),
                    types           = null,
                    status          = null,
                    values_check    = null;
                if (targetObject !== null && properties !== null) {
                    properties = (properties instanceof Array === true ? properties : [properties]);
                    for (var index = properties.length - 1; index >= 0; index -= 1) {
                        if (typeof properties[index].name === "string" && typeof properties[index].type !== "undefined") {
                            if (typeof targetObject[properties[index].name] !== "undefined") {
                                properties[index].type = (typeof properties[index].type === "string" ? [properties[index].type] : properties[index].type);
                                if (properties[index].type instanceof Array) {
                                    status = false;
                                    for (var indexTypes = properties[index].type.length - 1; indexTypes >= 0; indexTypes -= 1) {
                                        if (properties[index].type[indexTypes]          === "node") {
                                            if (targetObject[properties[index].name] !== null){
                                                if (typeof targetObject[properties[index].name] === "object"){
                                                    status = (typeof targetObject[properties[index].name].parentNode === "object" ? true : status);
                                                }
                                            }
                                        } else if (properties[index].type[indexTypes]   === "array") {
                                            status = (targetObject[properties[index].name] instanceof Array === true ? true : status);
                                        } else {
                                            status = (typeof targetObject[properties[index].name] === properties[index].type[indexTypes] ? true : status);
                                        }
                                    }
                                    if (status === false) {
                                        if (typeof properties[index].value !== "undefined") {
                                            targetObject[properties[index].name] = properties[index].value;
                                        } else {
                                            return false;
                                        }
                                    } else {
                                        if (typeof properties[index].values !== "undefined") {
                                            if (properties[index].values instanceof Array) {
                                                values_check = false;
                                                for (var indexValues = properties[index].values.length - 1; indexValues >= 0; indexValues -= 1) {
                                                    if (targetObject[properties[index].name] === properties[index].values[indexValues]) {
                                                        values_check = true;
                                                        break;
                                                    }
                                                }
                                                if (values_check === false) {
                                                    return false;
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                if (typeof properties[index].value !== "undefined") {
                                    targetObject[properties[index].name] = properties[index].value;
                                } else {
                                    return false;
                                }
                            }
                        }
                    }
                    return true;
                }
                return null;
            },
            is          : function (path) {
                var variable    = window,
                    path        = (typeof path === 'string' ? path : null);
                if (path !== null) {
                    path = path.split('.');
                    for (var index = 0, max_index = path.length; index < max_index; index += 1) {
                        if (typeof variable[path[index]] !== "undefined") {
                            variable = variable[path[index]];
                        } else {
                            return false;
                        }
                    }
                    return true;
                }
                return false;
            },
            copy        : function (targetObject, sourceObject) {
                var targetObject = (typeof targetObject === "object" ? (targetObject === null ? {} : targetObject) : {}),
                    sourceObject = (typeof sourceObject === "object" ? sourceObject : null  );
                if (sourceObject !== null) {
                    for (var key in sourceObject) {
                        if (sourceObject.hasOwnProperty(key)) {
                            if (sourceObject[key] instanceof Array) {
                                targetObject[key] = [];
                                for (var index = 0, max_index = sourceObject[key].length; index < max_index; index += 1) {
                                    targetObject[key].push(sourceObject[key][index]);
                                }
                            } else if (typeof sourceObject[key] === "object" && sourceObject[key] !== null && typeof sourceObject[key] !== "function") {
                                targetObject[key] = {};
                                targetObject[key] = pure.tools.objects.copy(targetObject[key], sourceObject[key]);
                            } else {
                                targetObject[key] = sourceObject[key];
                            }

                        }
                    }
                    return targetObject;
                }
                return null;
            },
            isValueIn   : function (targetObject, value, deep){
                var deep    = (typeof deep === 'boolean' ? deep : false),
                    inDeep  = false;
                for(var key in targetObject){
                    if (targetObject[key] === value){
                        return true;
                    }else{
                        if (deep !== false){
                            if (typeof targetObject[key] === 'object'){
                                if (typeof targetObject[key].getAttribute === 'undefined'){
                                    inDeep = pure.tools.objects.isValueIn(targetObject[key], value, deep);
                                    if (inDeep === true){
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
                return false;
            }
        },
        arrays      : {
            copy: function (source) {
                var target = [];
                if (source instanceof Array === true) {
                    for (var index = 0, max_index = source.length; index < max_index; index += 1) {
                        if (typeof source[index] === 'object'){
                            target.push(pure.tools.objects.copy(null, source[index]));
                        }else{
                            target.push(source[index]);
                        }
                    }
                }
                return target;
            }
        },
        scripts     : {
            removeFromInnerHTML : function(innerHTML){
                return innerHTML.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, '');
            },
            getFromInnerHTML : function(innerHTML){
                return innerHTML.match(/<script[^>]*>([\s\S]*?)<\/script>/gi);
            },
            attachScripts : function(scripts){
                if (scripts instanceof Array === true){
                    if (scripts.length > 0){
                        for (var index = 0, max_index = scripts.length; index < max_index; index += 1){
                            (function(innerScript){
                                var script = document.createElement("SCRIPT");
                                script.setAttribute('type', 'text/javascript');
                                script.innerHTML = innerScript.replace(/<([\s\S]*?)(script)([\s\S]*?)>/gi, '');
                                document.head.appendChild(script);
                                script = null;
                            }(scripts[index]));
                        }
                    }
                }
            }
        },
        IDs         : {
            index : 0,
            get id(){
                pure.tools.IDs.index += 1;
                return pure.tools.IDs.index;
            },
            get : function(prefix){
                var id          = Math.round(Math.random()*100000 + Math.random()*100000).toString(),
                    prefix      = (typeof prefix === "string" ? prefix : ''),
                    timestamp   = (new Date().getTime()).toString();
                return (prefix + '_' + pure.tools.IDs.id + '_' + timestamp + id).replace(/\W/g, '_');
            },
            getGlobalParentID : function(node){
                var parent  = pure.nodes.find.parentByAttr(node, {name:'data-engine-element', value: 'parent'}),
                    id      = null;
                if (parent !== null){
                    id = parent.getAttribute('data-engine-element-ID');
                    id = (typeof id === 'string' ? (id !== '' ? id.replace(/\W/, '_') : null) : null);
                }
                return id;
            }
        },
        date        : {
            YYYYMMDDHHMMSSToObject : function(strDate){
                var match   = /(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/.exec(strDate),
                    args    = [];
                if (match === null){
                    match = /(\d{4})-(\d{2})-(\d{2})/.exec(strDate);
                }
                if (match !== null){
                    if (match.length > 1){
                        for (var index = 1, max_index = match.length; index < max_index; index += 1){
                            args.push(parseInt(match[index],  10) - (index === 2 ? 1 : 0));
                        }
                        return new (Function.prototype.bind.apply(
                            Date, [null].concat(args)
                        ));
                    }
                }
                return null;
            }
        },
        validate    : {
            email : function(email){
                //http://stackoverflow.com/questions/46155/validate-email-address-in-javascript
                var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
                return re.test(email);
            }
        }
    };
}());