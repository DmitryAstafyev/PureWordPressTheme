(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components               !== "object") { window.pure.components              = {}; }
    "use strict";
    window.pure.components.responsive = {
        scheme: {
            getComments : function(node, comments){
                var childs      = (typeof node.childNodes !== "undefined" ? (typeof node.childNodes.length === "number" ? node.childNodes : null) : null),
                    comments    = (comments instanceof Array ? comments : []);
                if (childs !== null) {
                    for (var index = childs.length - 1; index >= 0; index -= 1) {
                        if (childs[index].nodeType == 8) {
                            comments.push(childs[index]);
                        }
                        if (typeof childs[index].childNodes !== 'undefined'){
                            if (childs[index].childNodes !== null){
                                pure.components.responsive.scheme.getComments(childs[index], comments);
                            }
                        }
                    }
                }
                return comments;
            },
            getURL      : function () {
                function gethref(str) {
                    var url         = null,
                        variants    = [ {reg : new RegExp('".*?"', "gi"), symbol: '"'}, {reg : new RegExp("'.*?'", "gi"), symbol: "'"}];
                    for (var index = variants.length - 1; index >= 0; index -= 1) {
                        url = str.match(variants[index].reg);
                        if (url !== null) {
                            return (url[0].replace(new RegExp(variants[index].symbol, "gi"), "")).toLowerCase();
                        }
                    }
                    return null;
                };
                var comments    = window.pure.components.responsive.scheme.getComments(document.head),
                    value       = "",
                    url         = "";
                for (var index = comments.length - 1; index >= 0; index -= 1) {
                    value = (typeof comments[index].value === "string" ? comments[index].value : (typeof comments[index].textContent === "string" ? comments[index].textContent : null));
                    if (value !== null) {
                        value = value.toLowerCase();
                        if (value.indexOf("#responsive scheme") !== -1) {
                            url = gethref(value);
                            if (url !== null) {
                                if (url.indexOf("http://") === -1 && url.indexOf("https://") === -1) {
                                    return window.location.protocol + "//" + window.location.host + "/" + url;
                                }else{
                                    return url;
                                }
                            }
                        }
                    }
                }
                return null;
            },
            get         : function () {
                var url     = null,
                    request = null;
                if (typeof pure.tools === "object") {
                    url = pure.components.responsive.scheme.getURL();
                    if (url !== null) {
                        request = pure.tools.request.send({
                            url         : url,
                            type        : "GET",
                            onrecieve   : pure.components.responsive.rules.processing
                        });
                    }
                }
            }
        },
        events: {
            timers              : {},
            init                : function (triggers, id) {
                pure.events.add(
                    window,
                    "resize",
                    function (event) {
                        pure.components.responsive.events.processing(event, triggers, id);
                    }
                );
            },
            nodes               : {
                checkNode   : function (target) {
                    if (target.node === null) {
                        return false;
                    }
                    if (typeof target.node.parentNode !== "object") {
                        return false;
                    }
                    return true;
                },
                setNode     : function (target) {
                    if (pure.components.responsive.events.nodes.checkNode(target) === false) {
                        target.node = pure.nodes.select.first(target.selector);
                        if (pure.components.responsive.events.nodes.checkNode(target) === false) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        return true;
                    }
                }
            },
            processing          : function (event, triggers, id) {
                function launch(trigger, cache) {
                    function launchHandles      (handles, type){
                        if (handles.last !== type) {
                            handles.last = type;
                            for (var index = handles[type].length - 1; index >= 0; index -= 1) {
                                if (typeof handles[type][index].css     === "function") { handles[type][index].css  (); }
                                if (typeof handles[type][index].attr    === "function") { handles[type][index].attr (); }
                            }
                        }
                    };
                    function prepareWindowTarget(trigger, cache) {
                        if (typeof cache[trigger.target.id + "_" + "size"] !== "object") {
                            cache[trigger.target.id + "_" + "size"] = pure.nodes.render.windowSize();
                        }
                    };
                    function prepareNodeTarget  (trigger, cache) {
                        if (pure.components.responsive.events.nodes.setNode(trigger.target) === true) {
                            if (typeof cache[trigger.target.id + "_" + "size"] !== "object") {
                                cache[trigger.target.id + "_" + "size"] = pure.nodes.render.size(trigger.target.node);
                            }

                        }
                    };
                    if (trigger.target.selector === "window") {
                        prepareWindowTarget (trigger, cache);
                    } else {
                        prepareNodeTarget   (trigger, cache);
                    }
                    switch (trigger.condition) {
                        case "less":
                            if (trigger.value > cache[trigger.target.id + "_" + "size"][trigger.property]) {
                                launchHandles(trigger.handles, "on");
                            } else {
                                launchHandles(trigger.handles, "off");
                            }
                            break;
                        case "more":
                            if (trigger.value < cache[trigger.target.id + "_" + "size"][trigger.property]) {
                                launchHandles(trigger.handles, "on");
                            } else {
                                launchHandles(trigger.handles, "off");
                            }
                            break;
                        case "equally":
                            if (trigger.value === cache[trigger.target.id + "_" + "size"][trigger.property]) {
                                launchHandles(trigger.handles, "on");
                            } else {
                                launchHandles(trigger.handles, "off");
                            }
                            break;
                    }
                };
                var cache = {};
                for (var index = triggers.length - 1; index >= 0; index -= 1) {
                    launch(triggers[index], cache);
                }
                pure.appevents.Actions.call(
                    'pure.positioning',
                    'update',
                    null,
                    null
                );
            },
            generate    : function (triggers) {
                pure.components.responsive.events.processing(null, triggers);
            }
        },
        rules: {
            triggers    : {
                validate: function (trigger) {
                    function validate(trigger) {
                        if (pure.tools.objects.validate(trigger, [  { name: "id",           type: "string" },
                                                                    { name: "property",     type: "string" },
                                                                    { name: "target",       type: "string" },
                                                                    { name: "condition",    type: "string" },
                                                                    { name: "value",        type: "string" }]) === true) {
                            trigger.condition   = trigger.condition.toLowerCase();
                            trigger.property    = trigger.property.toLowerCase();
                            if (trigger.condition === "less" || trigger.condition === "more" || trigger.condition === "equally") {
                                if (trigger.property === "width" || trigger.property === "height") {
                                    trigger.target = {
                                        selector: trigger.target.toLowerCase(),
                                        id      : trigger.target.replace(/\W/gi, ''),
                                        node    : null
                                    };
                                    return true;
                                }
                            }
                        }
                        return false;
                    };
                    function parseValue(trigger) {
                        function getEMtoPX(size_in_EM) {
                            var tempNode    = document.createElement("DIV"),
                                size        = null;
                            tempNode.style.opacity  = 0.01;
                            tempNode.style.position = "absolute";
                            tempNode.style.width    = size_in_EM;
                            tempNode.style.height   = size_in_EM;
                            document.body.appendChild(tempNode);
                            size                    = pure.nodes.render.size(tempNode);
                            document.body.removeChild(tempNode);
                            tempNode                = null;
                            return (typeof size.width === "number" ? size.width : null);
                        };
                        var value = null;
                        trigger.value = trigger.value.toLowerCase();
                        if (trigger.value.indexOf("em") !== -1) {
                            value           = getEMtoPX(trigger.value);
                            trigger.value   = (value !== null ? value.toString() : "");
                        }
                        trigger.value   = trigger.value.replace(/[^0-9]/gi, '');
                        value           = parseInt(trigger.value);
                        trigger.value   = (typeof value === "number" ? (value >= 0 ? value : null) : null);
                        return trigger.value;
                    };
                    for (var index = trigger.length - 1; index >= 0; index -= 1) {
                        if (validate(trigger[index]) === true) {
                            if (parseValue(trigger[index]) === null) {
                                trigger.splice(index, 1);
                            }
                        } else {
                            trigger.splice(index, 1);
                        }
                    }
                    return (trigger.length > 0 ? true : false);
                },
                build   : function (triggers, rules) {
                    function make(trigger, rules) {
                        trigger.handles = { on: [], off: [] };
                        for (var index = rules.length - 1; index >= 0; index -= 1) {
                            if (rules[index].trigger === trigger.id) {
                                trigger.handles.on. push(rules[index].handles.on);
                                trigger.handles.off.push(rules[index].handles.off);
                            }
                        }

                    };
                    for (var index = triggers.length - 1; index >= 0; index -= 1) {
                        make(triggers[index], rules);
                    }
                }
            },
            handles     : {
                nodes   : {
                    checkNode   : function (target) {
                        if (typeof target.node.length !== "number") {
                            return false;
                        }
                        if (target.node.length === 0) {
                            return false;
                        }
                        if (target.node.length > 1) {
                            for (var index = target.node.length - 1; index >= 0; index -= 1) {
                                if (typeof target.node[index].parentNode !== "object") {
                                    return false;
                                }
                            }
                        } else {
                            if (typeof target.node[0].parentNode !== "object") {
                                return false;
                            }
                        }
                        return true;
                    },
                    setNode     : function (target) {
                        if (pure.components.responsive.rules.handles.nodes.checkNode(target) === false) {
                            target.node = pure.nodes.select.all(target.selector);
                            if (pure.components.responsive.rules.handles.nodes.checkNode(target) === false) {
                                return false;
                            } else {
                                return true;
                            }
                        } else {
                            return true;
                        }
                    }
                },
                validate: function (trigger, rule) {
                    function validate(rule) {
                        if (pure.tools.objects.validate(rule, [ { name: "id",       type: "string" },
                                                                { name: "trigger",  type: "string" },
                                                                { name: "target",   type: "string" },
                                                                { name: "actions",  type: "object" }]) === true) {
                            if (pure.tools.objects.validate(rule.actions, [ { name: "on",   type: "object", value: null },
                                                                            { name: "off",  type: "object", value: null }]) === true) {
                                if (rule.actions.on === null && rule.actions.off === null) {
                                    return false;
                                } else {
                                    rule.target = {
                                        selector: rule.target,
                                        node    : null
                                    };
                                    return true;
                                }
                            }

                        }
                        return false;
                    };
                    function isTriggerDefined(rule, trigger) {
                        for (var index = trigger.length - 1; index >= 0; index -= 1) {
                            if (rule.trigger === trigger[index].id) {
                                return true;
                            }
                        }
                        return false;
                    };
                    for (var index = rule.length - 1; index >= 0; index -= 1) {
                        if (validate(rule[index]) === true) {
                            if (isTriggerDefined(rule[index], trigger) === false) {
                                rule.splice(index, 1);
                            }
                        } else {
                            rule.splice(index, 1);
                        }
                    }
                    return (rule.length > 0 ? true : false);
                },
                build   : function (rule) {
                    function build(rule) {
                        function prepare(action) {
                            if (action !== null) {
                                action.css  = (typeof action.css    !== "undefined" ? (action.css   instanceof Array ? action.css   : [action.css]  ) : null);
                                action.attr = (typeof action.attr   !== "undefined" ? (action.attr  instanceof Array ? action.attr  : [action.attr] ) : null);
                                if (action.css !== null) {
                                    for (var index = action.css.length - 1; index >= 0; index -= 1) {
                                        if (pure.tools.objects.validate(action.css[index], [{ name: "property", type: "string"              },
                                                                                            { name: "value",    type: "string", value: null }]) === false) {
                                            action.css.splice(index, 1);
                                        }
                                    }
                                }
                                if (action.attr !== null) {
                                    for (var index = action.attr.length - 1; index >= 0; index -= 1) {
                                        if (pure.tools.objects.validate(action.attr[index], [   { name: "name",     type: "string"              },
                                                                                                { name: "value",    type: "string", value: null },
                                                                                                { name: "remove",   type: "string", value: null }]) === false) {
                                            action.attr.splice(index, 1);
                                        }
                                    }
                                }
                            }
                        };
                        function make(rule) {
                            function setHandleFromAction(action, target) {
                                var handles = {css : null, attr: null};
                                if (action.css !== null) {
                                    (function (actions, handles, target) {
                                        handles.css = function () {
                                            var value = null;
                                            if (pure.components.responsive.rules.handles.nodes.setNode(target) === true) {
                                                for (var index = actions.length - 1; index >= 0; index -= 1) {
                                                    value = (actions[index].value !== null ? actions[index].value : "");
                                                    for (var node_index = target.node.length - 1; node_index >= 0; node_index -= 1) {
                                                        if (typeof target.node[node_index].style[actions[index].property] !== "undefined") {
                                                            if (target.node[node_index].style[actions[index].property] !== value) {
                                                                target.node[node_index].style[actions[index].property] = value;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        };
                                    }(action.css, handles, target));
                                }
                                if (action.attr !== null) {
                                    (function (actions, handles, target) {
                                        handles.attr = function () {
                                            if (pure.components.responsive.rules.handles.nodes.setNode(target) === true) {
                                                for (var index = actions.length - 1; index >= 0; index -= 1) {
                                                    for (var node_index = target.node.length - 1; node_index >= 0; node_index -= 1) {
                                                        if (actions[index].remove === null) {
                                                            target.node[node_index].setAttribute(actions[index].name, actions[index].value);
                                                        } else {
                                                            target.node[node_index].removeAttribute(actions[index].name);
                                                        }
                                                    }
                                                }
                                            }
                                        };
                                    }(action.attr, handles, target));
                                }
                                return handles;
                            };
                            var target = {
                                    selector: rule.target.selector,
                                    node    : pure.nodes.select.all(rule.target.selector)
                                };
                            rule.handles        = {};
                            rule.handles.on     = setHandleFromAction(rule.actions.on, target);
                            rule.handles.off    = setHandleFromAction(rule.actions.off, target);
                        };
                        prepare(rule.actions.on   );
                        prepare(rule.actions.off  );
                        return make(rule);
                    };
                    for (var index = rule.length - 1; index >= 0; index -= 1) {
                        build(rule[index]);
                    }
                }
            },
            processing  : function(id, response, event){
                var id          = (typeof id        === "string" ? id       : null),
                    response    = (typeof response  === "string" ? response : null),
                    xmlObject   = null;
                if (id !== null && response !== null) {
                    xmlObject = pure.tools.xml.fromString(response, false);
                    if (xmlObject !== null) {
                        if (typeof xmlObject.trigger !== "undefined" && typeof xmlObject.rule !== "undefined") {
                            xmlObject.trigger   = (xmlObject.trigger    instanceof Array ? xmlObject.trigger   : [xmlObject.trigger]);
                            xmlObject.rule      = (xmlObject.rule       instanceof Array ? xmlObject.rule      : [xmlObject.rule   ]);
                            if (pure.components.responsive.rules.triggers.validate(xmlObject.trigger) === true) {
                                if (pure.components.responsive.rules.handles.validate(xmlObject.trigger, xmlObject.rule) === true) {
                                    pure.components.responsive.rules.handles.  build(xmlObject.rule                    );
                                    pure.components.responsive.rules.triggers. build(xmlObject.trigger, xmlObject.rule );
                                    pure.components.responsive.events.init     (xmlObject.trigger, id);
                                    pure.components.responsive.events.generate (xmlObject.trigger);
                                    //Here we deny do update by POSITIONING COMPONENT if WINDOW RESIZE is. We do to refuse from double updating.
                                    //Here [pure.components.responsive.events.processing] we call updating manually.
                                    pure.appevents.Actions.call(
                                        'pure.positioning',
                                        'manual.window.resize.update',
                                        null,
                                        null
                                    );
                                }
                            }
                        }
                    }
                }
            }
        },
        init: function () {
            pure.components.responsive.scheme.get();
        }
    };
    pure.system.start.add(pure.components.responsive.init);
}());