(function () {
    "use strict";
    if (typeof window.pure !== "object") { window.pure = {}; }
    window.pure.events = {
        add     : null,
        remove  : null,
        init                : function () {
            if (typeof window.addEventListener === "function") {
                pure.events.add = function (element, eventName, handle) {
                    if (typeof element !== "undefined" && typeof eventName === "string" && typeof handle !== "undefined") {
                        if (element !== null && typeof handle === 'function') {
                            element.addEventListener(eventName, function(event){ pure.events.handle(event, handle);}, false);
                        }
                    }
                };
                pure.events.remove = function (element, eventName, handle) {
                    if (typeof element !== "undefined" && typeof eventName === "string" && typeof handle !== "undefined") {
                        if (element !== null && typeof handle === 'function') {
                            element.removeEventListener(eventName, handle, false);
                        }
                    }
                };
            } else if (typeof document.attachEvent === "function") {
                pure.events.add = function (element, eventName, handle) {
                    if (typeof element !== "undefined" && typeof eventName === "string" && typeof handle !== "undefined") {
                        if (element !== null && typeof handle === 'function') {
                            element.attachEvent(("on" + eventName), function(event){ pure.events.handle(event, handle);});
                        }
                    }
                };
                pure.events.remove = function (element, eventName, handle) {
                    if (typeof element !== "undefined" && typeof eventName === "string" && typeof handle !== "undefined") {
                        if (element !== null && typeof handle === 'function') {
                            element.detachEvent(("on" + eventName), handle);
                        }
                    }
                };
            } else {
                pure.events.add = function (element, eventName, handle) {
                    if (typeof element !== "undefined" && typeof eventName === "string" && typeof handle !== "undefined") {
                        if (element !== null && typeof handle === 'function') {
                            element[("on" + eventName)] = function(event){ pure.events.handle(event, handle);};
                        }
                    }
                };
                pure.events.remove = function (element, eventName, handle) {
                    if (typeof element !== "undefined" && typeof eventName === "string" && typeof handle !== "undefined") {
                        if (element !== null && typeof handle === 'function') {
                            element[("on" + eventName)] = null;
                        }
                    }
                };
            };
        },
        unificationEvent    : function (event) {
            function UnificationStop(event) {
                if (typeof event.preventDefault !== "undefined") {
                    event.preventDefault = event.preventDefault;
                } else {
                    event.preventDefault = function () { try { this.returnValue = false; } catch (e) { } };
                }
                if (typeof event.stopPropagation !== "undefined") {
                    event.stopPropagation = event.stopPropagation;
                } else {
                    event.stopPropagation = function () { try { this.cancelBubble = true; } catch (e) { } };
                }
                return event;
            };
            function UnificationTarget(event) {
                if (typeof event.target === "undefined") {
                    if (typeof event.srcElement !== "undefined") {
                        event.target = event.srcElement;
                    } else {
                        event.target = null;
                    }
                }
                if (event.target !== null) {
                    if (typeof event.relatedTarget === "undefined") {
                        if (typeof event.fromElement !== "undefined") {
                            if (event.fromElement === event.target) {
                                event.relatedTarget = event.toElement;
                            } else {
                                event.relatedTarget = event.fromElement;
                            }
                        } else {
                            event.relatedTarget = null;
                            event.fromElement = null;
                        }
                    }
                }
                return event;
            };
            function UnificationCoordinate(event) {
                if (typeof event.clientX !== "undefined") {
                    if (typeof event.pageX === "undefined") {
                        event._pageX = null;
                        event._pageY = null;
                    }
                    if (event.pageX === null && event.clientX !== null) {
                        var DocumentLink    = document.documentElement,
                            BodyLink        = document.body;
                        event._pageX = event.clientX + (DocumentLink && DocumentLink.scrollLeft || BodyLink && BodyLink.scrollLeft  || 0) - (DocumentLink.clientLeft    || 0);
                        event._pageY = event.clientY + (DocumentLink && DocumentLink.scrollTop  || BodyLink && BodyLink.scrollTop   || 0) - (DocumentLink.clientTop     || 0);
                    } else {
                        event._pageX = event.pageX;
                        event._pageY = event.pageY;
                    }
                } else {
                    event._pageX = null;
                    event._pageY = null;
                }
                event._clientX = (typeof event.clientX !== "undefined" ? event.clientX : null);
                event._clientY = (typeof event.clientY !== "undefined" ? event.clientY : null);
                event._offsetX = (typeof event.offsetX !== "undefined" ? event.offsetX : (typeof event.layerX !== "undefined" ? event.layerX : null));
                event._offsetY = (typeof event.offsetY !== "undefined" ? event.offsetY : (typeof event.layerY !== "undefined" ? event.layerY : null));
                return event;
            };
            function UnificationButtons(event) {
                if (typeof event.which === "undefined" && typeof event.button !== "undefined") {
                    event.which = (event.button & 1 ? 1 : (event.button & 2 ? 3 : (event.button & 4 ? 2 : 0)));
                }
                return event;
            };
            if (typeof event.UnificationFlag !== "boolean") {
                //Унифицируем событие
                event = UnificationStop         (event);
                event = UnificationTarget       (event);
                event = UnificationCoordinate   (event);
                event = UnificationButtons      (event);
                //Отмечаем как унифицированное
                event.UnificationFlag = true;
            }
            return event;
        },
        handle              : function(event, handle){
            handle.call(this, pure.events.unificationEvent(event));
        },
        call                : function (element, eventName) {
            function extend(destination, source) {
                for (var property in source)
                    destination[property] = source[property];
                return destination;
            }
            var oEvent          = null,
                eventType       = null,
                evt             = null,
                eventMatchers   = {
                    'HTMLEvents'    : /^(?:load|unload|abort|error|select|change|submit|reset|focus|blur|resize|scroll)$/,
                    'MouseEvents'   : /^(?:click|dblclick|mouse(?:down|up|over|move|out))$/
                },
                defaultOptions  = {
                    type            : eventName,
                    canBubble       :true,
                    cancelable      :true,
                    view            :element.ownerDocument.defaultView,
                    detail          :1,
                    screenX         :0,
                    screenY         :0,
                    clientX         :0,
                    clientY         :0,
                    pointerX        : 0,
                    pointerY        : 0,
                    ctrlKey         :false,
                    altKey          :false,
                    shiftKey        :false,
                    metaKey         : false,
                    button          : 0,
                    relatedTarget   :null
                },
                options         = extend(defaultOptions, arguments[2] || {});
            for (var name in eventMatchers) {
                if (eventMatchers[name].test(eventName)) { eventType = name; break; }
            }
            if (!eventType){
                throw new SyntaxError('Only HTMLEvents and MouseEvents interfaces are supported');
            }
            if (document.createEvent) {
                oEvent = document.createEvent(eventType);
                if (eventType == 'HTMLEvents') {
                    oEvent.initEvent(eventName, options.bubbles, options.cancelable);
                } else {
                    oEvent.initMouseEvent(
                        options.type,       options.canBubble,  options.cancelable, options.view,
                        options.detail,     options.screenX,    options.screenY,    options.clientX,
                        options.clientY,    options.ctrlKey,    options.altKey,     options.shiftKey,
                        options.metaKey,    options.button,     options.relatedTarget
                    );
                }
                element.dispatchEvent(oEvent);
            } else {
                options.clientX = options.pointerX;
                options.clientY = options.pointerY;
                evt             = document.createEventObject();
                oEvent          = extend(evt, options);
                element.fireEvent('on' + eventName, oEvent);
            }
            return element;
        },
        stop : function(event){
            if (typeof event.preventDefault === 'function'){
                event.preventDefault();
            }
            if (typeof event.stopPropagation === 'function'){
                event.stopPropagation();
            }
        }
    };
    window.pure.events.init();
}());
