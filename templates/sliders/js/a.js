(function () {
    if (typeof window.pure          !== "object") { window.pure         = {}; }
    if (typeof window.pure.sliders  !== "object") { window.pure.sliders = {}; }
    "use strict";
    window.pure.sliders.A = {
        data: {
            storage : { },
            set : function (id, nodes, items, point) {
                var id              = (typeof id === "string" ? id : pure.tools.IDs.get("slider.A.ID.")),
                    current_record  = null;
                if (typeof pure.sliders.A.data.storage[id] !== "object") {
                    pure.sliders.A.data.storage[id] = {};
                }
                current_record          = pure.sliders.A.data.storage[id];
                current_record.id       = id;
                current_record.nodes    = nodes;
                current_record.items    = items;
                current_record.point    = point;
                current_record.state    = { last_controls_width : null};
                current_record.current  = { first: 0, last: -1, step : 0 };
                return current_record;
            },
            get : function (id) {
                return (typeof pure.sliders.A.data.storage[id] === "object" ? pure.sliders.A.data.storage[id] : null);
            }
        },
        init    : function () {
            function set(instance) {
                var id              = null,
                    id_attribute    = "data-engine-element-id",
                    nodes           = {
                        buttons     : { left: null, right: null },
                        container   : null,
                        content     : null,
                        items       : null,
                        points      : null,
                        controls    : null
                    },
                    items           = {
                        sizes       : [],
                        width       : 0
                    },
                    point           = {
                        node        : null,
                        nodeName    : null,
                        attributes  : [],
                        active: {
                            name    : null,
                            value   : null
                        },
                        innerHTML   : null
                    };
                id                  = pure.tools.IDs.get("slider.A.ID.");
                instance.setAttribute(id_attribute, id);
                nodes.container     = instance;
                nodes.content       = pure.nodes.select.first   ("div[data-engine-element=\"Slider.A\"][" + id_attribute + "=\"" + id + "\"] div[data-engine-type=\"Slider.A.Content\"]"        );
                nodes.points        = pure.nodes.select.first   ("div[data-engine-element=\"Slider.A\"][" + id_attribute + "=\"" + id + "\"] div[data-engine-type=\"Slider.A.Points\"]"         );
                nodes.controls      = pure.nodes.select.first   ("div[data-engine-element=\"Slider.A\"][" + id_attribute + "=\"" + id + "\"] div[data-engine-type=\"Slider.A.Controls\"]"       );
                nodes.items         = pure.nodes.select.all     ("div[data-engine-element=\"Slider.A\"][" + id_attribute + "=\"" + id + "\"] div[data-engine-type=\"Slider.A.Item\"]"           );
                nodes.buttons.left  = pure.nodes.select.first   ("div[data-engine-element=\"Slider.A\"][" + id_attribute + "=\"" + id + "\"] div[data-engine-type=\"Slider.A.Button.Left\"]"    );
                nodes.buttons.right = pure.nodes.select.first   ("div[data-engine-element=\"Slider.A\"][" + id_attribute + "=\"" + id + "\"] div[data-engine-type=\"Slider.A.Button.Right\"]"   );
                point.node          = pure.nodes.select.first   ("div[data-engine-element=\"Slider.A\"][" + id_attribute + "=\"" + id + "\"] div[data-engine-type=\"Slider.A.Point\"]"          );
                if (nodes.content       !== null && nodes.items         !== null && nodes.points    !== null &&
                    nodes.buttons.left  !== null && nodes.buttons.right !== null && point.node      !== null &&
                    nodes.controls      !== null) {
                    if (typeof nodes.items.length === "number") {
                        point.active.name   = nodes.points.getAttribute("data-engine-active-attr-name"    );
                        point.active.value  = nodes.points.getAttribute("data-engine-active-attr-value"   );
                        if (typeof point.active.name === "string" && typeof point.active.value === "string") {
                            //Get attributes of point
                            for (var attr_index = point.node.attributes.length - 1; attr_index >= 0; attr_index -= 1) {
                                if (typeof point.node.attributes[attr_index].nodeName   === "string" &&
                                    typeof point.node.attributes[attr_index].value      === "string") {
                                    point.attributes.push({
                                        name    : point.node.attributes[attr_index].nodeName,
                                        value   : point.node.attributes[attr_index].value
                                    });
                                }
                            }
                            //Save node type of point
                            point.nodeName  = point.node.nodeName;
                            //Save innerHTML of point
                            point.innerHTML = point.node.innerHTML;
                            //Remove all points
                            point.node      = null;
                            delete point.node;
                            nodes.points.innerHTML = "";
                            //Get sizes of items
                            for (var item_index = 0, max_item_index = nodes.items.length; item_index < max_item_index; item_index += 1) {
                                items.sizes.unshift(pure.nodes.render.size(nodes.items[item_index]).width);
                            }
                            //Save data and return instance
                            return pure.sliders.A.data.set(id, nodes, items, point);
                        }
                    }
                }
                return null;
            };
            var instances   = pure.nodes.select.all("div[data-engine-element=\"Slider.A\"]"),
                record      = null;
            if (instances !== null) {
                if (typeof instances.length === "number") {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        record = set(instances[index]);
                        if (record !== null) {
                            pure.sliders.A.actions.init(record.id, null);
                            pure.sliders.A.actions.resize(record.id);
                        }
                    }
                }
            }
        },
        actions: {
            init    : function (id) {
                var data = pure.sliders.A.data.get(id);
                if (data !== null) {
                    pure.events.add(window,                     "resize",   function (event) { pure.sliders.A.actions.resize    (id);           });
                    pure.events.add(data.nodes.buttons.left,    "click",    function (event) { pure.sliders.A.actions.left      (event, id);    });
                    pure.events.add(data.nodes.buttons.right,   "click",    function (event) { pure.sliders.A.actions.right     (event, id);    });
                }
            },
            right   : function (event, id) {
                var data = pure.sliders.A.data.get(id);
                if (data !== null) {
                    if (data.current.last < data.items.sizes.length) {
                        if ((data.current.last + data.current.step) < data.items.sizes.length) {
                            data.current.first += data.current.step;
                        } else {
                            data.current.first += data.items.sizes.length - data.current.last;
                        }
                        pure.sliders.A.actions.show(id);
                    }
                }
                pure.tools.selections.clear();
                event.stopPropagation ? event.stopPropagation() : (event.cancelBubble = true);
            },
            left    : function (event, id) {
                var data = pure.sliders.A.data.get(id);
                if (data !== null) {
                    if (data.current.first > 0) {
                        if (data.current.last === data.items.sizes.length) {
                            if (Math.floor(data.items.sizes.length / data.current.step) * data.current.step < data.items.sizes.length) {
                                data.current.first -= (data.items.sizes.length - Math.floor(data.items.sizes.length / data.current.step) * data.current.step);
                            } else {
                                data.current.first -= data.current.step;
                            }
                        } else {
                            data.current.first -= data.current.step;
                        }
                        data.current.first = (data.current.first < 0 ? 0 : data.current.first);
                        pure.sliders.A.actions.show(id);
                    }
                }
                pure.tools.selections.clear();
                event.stopPropagation ? event.stopPropagation() : (event.cancelBubble = true);
            },
            resize  : function (id) {
                var data = pure.sliders.A.data.get(id);
                if (data !== null) {
                    pure.sliders.A.actions.show(id);
                }
            },
            show    : function (id) {
                var data        = pure.sliders.A.data.get(id),
                    width       = 0,
                    points      = {count : 1, current : -1},
                    point       = null,
                    position    = {offset : null, before : null};
                if (data !== null) {
                    //Get size of container
                    data.items.width = pure.nodes.render.size(data.nodes.container).width;
                    //Get invissible size before first
                    position.before = 0;
                    for (var index = 0; index < data.current.first; index += 1) {
                        position.before += data.items.sizes[index];
                    }
                    //Get vissible count
                    for (var index = data.current.first; index < data.items.sizes.length; index += 1) {
                        width += data.items.sizes[index];
                        if (width > data.items.width) {
                            data.current.last   = index;
                            width               = (data.current.first !== data.current.last ? width - data.items.sizes[index] : width);
                            break;
                        }
                        if (index === data.items.sizes.length - 1){
                            data.current.last   = data.items.sizes.length;
                        }
                    }
                    //Set container's position
                    position.offset = (data.items.width - width) / 2;
                    data.nodes.content.style.left = -(position.before - position.offset) + "px";
                    //Set size of container
                    //data.nodes.content.style.width = width + "px";
                    //Show / hide items
                    for (var index = 0; index < data.items.sizes.length; index += 1) {
                        if (index >= data.current.first && index < data.current.last) {
                            data.nodes.items[index].style.opacity = 1;
                        } else {
                            data.nodes.items[index].style.opacity = 0;
                        }
                    }
                    //Update points
                    width               = 0;
                    data.current.step   = -1;
                    for (var index = 0; index < data.items.sizes.length; index += 1) {
                        width += data.items.sizes[index];
                        if (width > data.items.width) {
                            width               = data.items.sizes[index];
                            points.count        += 1;
                            data.current.step   = (data.current.step !== -1 ? data.current.step : index);
                        }
                        if (points.current === -1 && index === data.current.first) {
                            points.current = points.count - 1;
                        }
                    }
                    points.current = (data.items.sizes.length === data.current.last ? (points.current + 1 < points.count ? points.current + 1 : points.current) : points.current);
                    //Remove old points
                    data.nodes.points.innerHTML = "";
                    for (var index = 0; index < points.count; index += 1) {
                        point = document.createElement(data.point.nodeName);
                        for (var attr_index = data.point.attributes.length - 1; attr_index >= 0; attr_index -= 1) {
                            point.setAttribute(data.point.attributes[attr_index].name, data.point.attributes[attr_index].value);
                        }
                        point.innerHTML = data.point.innerHTML;
                        if (points.current === index) {
                            point.setAttribute(data.point.active.name, data.point.active.value);
                        }
                        data.nodes.points.appendChild(point);
                        point = null;
                    }
                    if (points.count === 1) {
                        data.nodes.controls.style.display = "none";
                    } else {
                        data.nodes.controls.style.display = "";
                    }
                    //Update controls's container
                    window.pure.sliders.A.actions.controls.toggle(id);
                    /*
                    data.width      = pure.nodes.render.size(data.content).     width;
                    data.step_width = pure.nodes.render.size(data.container).   width * 0.8;
                    */
                }
            },
            controls: {
                toggle: function (id) {
                    var data            = pure.sliders.A.data.get(id),
                        controls_width  = null;
                    if (data !== null) {
                        //Get size of controls's container
                        controls_width = pure.nodes.render.size(data.nodes.controls).width;
                        if (data.items.width * 0.8 < controls_width) {
                            data.nodes.points.style.display = "none";
                            data.state.last_controls_width  = controls_width;
                        } else {
                            if (data.state.last_controls_width !== null) {
                                if (data.state.last_controls_width < data.items.width) {
                                    data.nodes.points.style.display = "";
                                    data.state.last_controls_width  = null;
                                }
                            }
                        }
                    }
                }
            }
        }
    };
    pure.system.start.add(pure.sliders.A.init);
}());