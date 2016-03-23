(function () {
    "use strict";
    if (typeof window.pure !== "object") { window.pure = {}; }
    window.pure.nodes = {
        select              : {
            first   : function (strSelector) {
                var resultObjectCollection = null;
                if (typeof strSelector === "string") {
                    if (typeof document.querySelector === "function") {
                        try{
                            resultObjectCollection = document.querySelector(strSelector);
                        }catch (e){
                            if (typeof console !== 'undefined'){
                                if (typeof console.log === 'function'){
                                    console.log('PURE.NODES.SELECT.FIRST::: Bad selector [' + strSelector + ']');
                                }
                            }
                        }
                    } else {
                        try { resultObjectCollection = $(strSelector).get(0); } catch (e) { }
                    }
                }
                return resultObjectCollection;
            },
            all     : function (strSelector, paramDocumentLink) {
                var resultObjectCollection = null;
                if (typeof strSelector === "string") {
                    if (typeof document.querySelectorAll === "function") {
                        try{
                            resultObjectCollection = document.querySelectorAll(strSelector);
                        }catch (e){
                            if (typeof console !== 'undefined'){
                                if (typeof console.log === 'function'){
                                    console.log('PURE.NODES.SELECT.ALL::: Bad selector [' + strSelector + ']');
                                }
                            }
                        }
                    } else {
                        try { resultObjectCollection = $(strSelector); } catch (e) { }
                    }
                }
                return resultObjectCollection;
            }
        },
        selectFromParent    : {
            select  : function (parent, strSelector, only_first) {
                var id      = (Math.random() * 100000000).toFixed(),
                    nodes   = null,
                    id_attr = "data-" + id;
                if (typeof parent.nodeName === "string") {
                    parent.setAttribute(id_attr, id);
                    if (only_first === false) {
                        nodes = pure.nodes.select.all(parent.nodeName + '[' + id_attr + '="' + id + '"] ' + strSelector);
                    } else {
                        nodes = pure.nodes.select.first(parent.nodeName + '[' + id_attr + '="' + id + '"] ' + strSelector);
                    }
                    parent.removeAttribute(id_attr);
                }
                return nodes;
            },
            first   : function (parent, strSelector) {
                return pure.nodes.selectFromParent.select(parent, strSelector, true);
            },
            all     : function (parent, strSelector) {
                return pure.nodes.selectFromParent.select(parent, strSelector, false);
            }
        },
        find                : {
            childByAttr : function (parent, nodeName, attribute) {
                var result_node = null,
                    nodeName    = nodeName.toLowerCase();
                if (typeof parent.childNodes !== "undefined") {
                    if (typeof parent.childNodes.length === "number") {
                        for (var index = parent.childNodes.length - 1; index >= 0; index -= 1) {
                            if (typeof parent.childNodes[index].nodeName === "string") {
                                if (parent.childNodes[index].nodeName.toLowerCase() === nodeName || nodeName === "*") {
                                    if (typeof parent.childNodes[index].getAttribute === "function") {
                                        if (attribute.value !== null) {
                                            if (parent.childNodes[index].getAttribute(attribute.name) === attribute.value) {
                                                return parent.childNodes[index];
                                            }
                                        } else {
                                            if (parent.childNodes[index].hasAttribute(attribute.name) === true) {
                                                return parent.childNodes[index];
                                            }
                                        }
                                    }
                                }
                            }
                            result_node = pure.nodes.find.childByAttr(parent.childNodes[index], nodeName, attribute);
                            if (result_node !== null) {
                                return result_node;
                            }
                        }
                    }
                }
                return null;
            },
            childByType : function (parent, nodeName) {
                var result_node = null,
                    nodeName    = nodeName.toLowerCase();
                if (typeof parent.childNodes !== "undefined") {
                    if (typeof parent.childNodes.length === "number") {
                        for (var index = parent.childNodes.length - 1; index >= 0; index -= 1) {
                            if (typeof parent.childNodes[index].nodeName === "string") {
                                if (parent.childNodes[index].nodeName.toLowerCase() === nodeName) {
                                    return parent.childNodes[index];
                                }
                            }
                        }
                        for (var index = parent.childNodes.length - 1; index >= 0; index -= 1) {
                            result_node = pure.nodes.find.childByType(parent.childNodes[index], nodeName);
                            if (result_node !== null) {
                                return result_node;
                            }
                        }
                    }
                }
                return null;
            },
            parentByAttr: function (child, attribute) {
                if (typeof child !== 'undefined' && typeof attribute !== 'undefined'){
                    if (typeof child.parentNode !== 'undefined') {
                        if (child.parentNode !== null) {
                            if (typeof child.parentNode.getAttribute === 'function'){
                                if (attribute.value !== null){
                                    if (child.parentNode.getAttribute(attribute.name) === attribute.value) {
                                        return child.parentNode;
                                    } else {
                                        return this.parentByAttr(child.parentNode, attribute);
                                    }
                                }else{
                                    if (child.parentNode.getAttribute(attribute.name) !== null) {
                                        return child.parentNode;
                                    } else {
                                        return this.parentByAttr(child.parentNode, attribute);
                                    }
                                }
                            }
                        }
                    }
                }
                return null;
            }
        },
        render              : {
            size                : function (node) {
                function ClientRectSize(node) {
                    var ElementHeight       = 0,
                        ElementWidth        = 0,
                        BoundingClientRect  = null;
                    if (typeof node.getBoundingClientRect !== "undefined") {
                        BoundingClientRect  = node.getBoundingClientRect();
                        ElementHeight       = BoundingClientRect.bottom - BoundingClientRect.top;
                        ElementWidth        = BoundingClientRect.right - BoundingClientRect.left;
                    }
                    return { Height: ElementHeight, Width: ElementWidth }
                };
                function OffsetSize(node) {
                    var ElementHeight   = 0,
                        ElementWidth    = 0;
                    if (typeof node.offsetHeight !== "undefined") {
                        ElementHeight   = node.offsetHeight;
                        ElementWidth    = node.offsetWidth;
                    }
                    return { Height: ElementHeight, Width: ElementWidth }
                };
                var ElementHeight       = null,
                    ElementWidth        = null,
                    ElementOutHeight    = null,
                    ElementOutWidth     = null,
                    clientRectSize      = null,
                    offsetSize          = null,
                    MarginTop           = null,
                    MarginBottom        = null, 
                    MarginRight         = null,
                    MarginLeft          = null;
                if (typeof node === "object") {
                    //определяем базовые размеры элемента тремя способами
                    clientRectSize  = ClientRectSize(node);
                    offsetSize      = OffsetSize(node);
                    //Гипотиза. Тот вариант, который дает наибольший размер и есть верный.
                    ElementHeight   = Math.max(clientRectSize["Height"], offsetSize["Height"]);
                    ElementWidth    = Math.max(clientRectSize["Width"], offsetSize["Width"]);
                    //определяем отступы
                    MarginTop       = parseInt(document.defaultView.getComputedStyle(node).marginTop,       10);
                    MarginBottom    = parseInt(document.defaultView.getComputedStyle(node).marginBottom,    10);
                    MarginRight     = parseInt(document.defaultView.getComputedStyle(node).marginRight,     10);
                    MarginLeft      = parseInt(document.defaultView.getComputedStyle(node).marginLeft,      10);
                    if (MarginTop       === null || MarginTop       === "") { MarginTop     = 0; } else { MarginTop     = parseInt(MarginTop, 10);      }
                    if (MarginBottom    === null || MarginBottom    === "") { MarginBottom  = 0; } else { MarginBottom  = parseInt(MarginBottom, 10);   }
                    if (MarginRight     === null || MarginRight     === "") { MarginRight   = 0; } else { MarginRight   = parseInt(MarginRight, 10);    }
                    if (MarginLeft      === null || MarginLeft      === "") { MarginLeft    = 0; } else { MarginLeft    = parseInt(MarginLeft, 10);     }
                    ElementOutHeight    = ElementHeight + MarginTop     + MarginBottom;
                    ElementOutWidth     = ElementWidth  + MarginRight   + MarginLeft;
                }
                return {
                    height      : ElementHeight,
                    width       : ElementWidth,
                    marginHeight: ElementOutHeight,
                    marginWidth : ElementOutWidth
                };
            },
            windowSize          : function () {
                var size = { width : null, height : null};
                if (self.innerHeight) { size.height = self.innerHeight; size.width = self.innerWidth; }
                else if (document.documentElement && document.documentElement.clientHeight) { size.height = document.documentElement.clientHeight; size.width = document.documentElement.clientWidth; }
                else if (document.body) { size.height = document.body.clientHeight; size.width = document.body.clientWidth; }
                return size;
            },
            windowScroll        : function(){
                var body    = document.body,
                    html    = document.documentElement;
                return {
                    top     : Math.max(
                        body.scrollTop,
                        html.scrollTop,
                        (typeof body.pageYOffset    === 'number' ? body.pageYOffset     : 0),
                        (typeof html.pageYOffset    === 'number' ? html.pageYOffset     : 0),
                        (typeof window.pageYOffset  === 'number' ? window.pageYOffset   : 0)
                    ),
                    height  : Math.max(
                        body.scrollHeight,
                        body.offsetHeight,
                        html.clientHeight,
                        html.scrollHeight,
                        html.offsetHeight
                    )
                };
            },
            imageSize           : function(image){
                function generateSize(image){
                    var imageObj    = new Image(),
                        size        = null;
                    imageObj.src    = image.src;
                    size            = {
                        width  : imageObj.width,
                        height : imageObj.height
                    };
                    imageObj        = null;
                    return size;
                };
                if (typeof image !== 'undefined'){
                    if(typeof image.naturalWidth === 'number'){
                        return {
                            width  : image.naturalWidth,
                            height : image.naturalHeight
                        }
                    }else{
                        return generateSize(image);
                    }
                }
                return null;
            },
            redraw              : function(node){
                if (typeof node !== 'undefined'){
                    if (typeof node.style !== 'undefined'){
                        node.style.display = 'none';
                        node.style.display = '';
                        return true;
                    }
                }
                return false;
            },
            offset              : function(node){
                //http://tympanus.net/codrops/2013/07/18/on-scroll-effect-layout/
                var offsetTop = 0, offsetLeft = 0;
                do {
                    if ( !isNaN( node.offsetTop ) ) {
                        offsetTop += node.offsetTop;
                    }
                    if ( !isNaN( node.offsetLeft ) ) {
                        offsetLeft += node.offsetLeft;
                    }
                } while( node = node.offsetParent );
                return {
                    top     : offsetTop,
                    left    : offsetLeft
                }
            },
            isDisplayed         : function(node){
                return (node.offsetParent === null ? false : true);
            },
            computedStyle       : function(node){
                if (typeof document.defaultView !== 'undefined'){
                    if (typeof document.defaultView.getComputedStyle !== 'undefined'){
                        return document.defaultView.getComputedStyle(node, null)
                    }
                }
                return null;
            }
        },
        convert     : {
            emToPx : function(em, context){
                function getElementFontSize(context) {
                    return parseFloat(getComputedStyle(context || document.documentElement).fontSize);
                };
                return em * getElementFontSize(context);
            }
        },
        attributes  : {
            get : function(node, exclude){
                var attributes  = null,
                    exclude     = (exclude instanceof Array === true ? exclude : []);
                if (typeof node.getAttribute === 'function'){
                    for(var index = exclude.length - 1; index >= 0; index -= 1){
                        exclude[index] = exclude[index].toLowerCase();
                    }
                    attributes = [];
                    for (var index = node.attributes.length - 1; index >= 0; index -= 1){
                        if (exclude.indexOf(node.attributes[index].name.toLowerCase()) === -1){
                            attributes.push({
                                name    : node.attributes[index].name,
                                value   : node.attributes[index].value
                            });
                        }
                    }
                }
                return attributes;
            },
            set : function(node, attributes){
                var attributes = (attributes instanceof Array === true ? attributes : null);
                if (typeof node.setAttribute === 'function' && attributes !== null){
                    for (var index = attributes.length - 1; index >= 0; index -= 1){
                        node.setAttribute(attributes[index].name, attributes[index].value);
                    }
                    return true;
                }
                return false;
            }
        },
        builder     : function (attributes) {
            function settingup(attributes, nodes) {
                var parent = null,
                    childs = null;
                if (typeof attributes.settingup === "object" && nodes !== null) {
                    parent = nodes[attributes.settingup.parent];
                    childs = attributes.settingup.childs;
                    for (var index = 0, max_index = childs.length; index < max_index; index += 1) {
                        if (typeof nodes[childs[index]] !== "undefined") {
                            if (typeof attributes[childs[index]].settingup === "object") {
                                parent.appendChild(nodes[childs[index]][attributes[childs[index]].settingup.parent]);
                            } else {
                                parent.appendChild(nodes[childs[index]]);
                            }
                        }
                    }
                }
            };
            function make(attribute) {
                var node = null;
                if (window.pure.tools.objects.validate(attribute, [{ name: "html", type: "string", value: null }]) === true) {
                    node = document.createElement(attribute.node);
                    node.setAttribute(attribute.name, attribute.value);
                    if (attribute.html !== null) {
                        node.innerHTML = attribute.html;
                    }
                    return node;
                } else {
                    return null;
                }
            };
            var nodes = null;
            try {
                if (window.pure.tools.objects.validate(attributes, [{ name: "node",     type: "string" },
                                                                    { name: "name",     type: "string" },
                                                                    { name: "value",    type: "string" }]) === true) {
                    return make(attributes);
                } else {
                    for (var property in attributes) {
                        if (window.pure.tools.objects.validate(attributes[property], [  { name: "node",     type: "string" },
                                                                                        { name: "name",     type: "string" },
                                                                                        { name: "value",    type: "string" }]) === true) {
                            if (nodes === null) { nodes = {}; }
                            nodes[property] = make(attributes[property]);
                            if (nodes[property] === null) {
                                return null;
                            }
                        } else {
                            if (typeof attributes[property] === "object" && property !== "settingup") {
                                if (nodes === null) { nodes = {}; }
                                nodes[property] = this.builder(attributes[property]);
                                if (nodes[property] === null) {
                                    return null;
                                }
                            }
                        }
                    }
                    settingup(attributes, nodes);
                }
            } catch (e) {
                return null;
            }
            return nodes;
        },
        insertAfter : function (newNode, referenceNode) {
            if (referenceNode.parentNode.lastChild !== referenceNode) {
                referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
            } else {
                referenceNode.parentNode.appendChild(newNode);
            }
        },
        saveRemove  : function (node) {
            if (typeof node.parentNode !== "undefined") {
                if (typeof node.parentNode.removeChild === "function") {
                    node.parentNode.removeChild(node);
                    return true;
                }
            }
            return false;
        },
        move        : {
            appendChildsTo : function(container, childs){
                for(var index = 0, maxIndex = childs.length; index < maxIndex; index += 1){
                    container.appendChild(childs[0]);
                }
            },
            insertChildsAfter : function(point, childs){
                for(var index = childs.length - 1; index >= 0; index -= 1){
                    pure.nodes.insertAfter(childs[index], point);
                }
            },
            insertChildsBefore : function(point, childs){
                for(var index = 0, maxIndex = childs.length; index < maxIndex; index += 1){
                    point.parentNode.insertBefore(childs[0], point);
                }
            }
        }
    };
}());