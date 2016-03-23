(function () {
    if (typeof window.pure          !== "object") { window.pure         = {}; }
    if (typeof window.pure.tabs     !== "object") { window.pure.tabs    = {}; }
    "use strict";
    window.pure.tabs.A = {
        data: {
            storage : {
                _length : -1,
                get length(){
                    this._length += 1;
                    return this._length;
                }
            },
            set : function (id, container, line, panel, tabs, titles, attribute, sizes) {
                var current_index   = pure.tabs.A.data.storage.length,
                    id              = (typeof id === "string" ? id : "tabs_" + current_index),
                    current_record  = null;
                if (typeof pure.tabs.A.data.storage[id] !== "object") {
                    pure.tabs.A.data.storage[id] = {};
                }
                current_record              = pure.tabs.A.data.storage[id];
                current_record.id           = id;
                current_record.tabs         = tabs;
                current_record.titles       = titles;
                current_record.attribute    = attribute;
                current_record.current      = -1;
                current_record.sizes        = sizes;
                current_record.container    = container;
                current_record.panel        = panel;
                current_record.line         = line;
                current_record.width        = 0;
                current_record.width_panel  = 0;
                current_record.offset       = 0;
                return current_record;
            },
            get : function (id) {
                return (typeof pure.tabs.A.data.storage[id] === "object" ? pure.tabs.A.data.storage[id] : null);
            }
        },
        init    : function () {
            var instances       = pure.nodes.select.all("div[data-engine-element=\"Tabs.A\"]"),
                tabs_content    = null,
                tabs_titles     = null,
                tabs_items      = null,
                container       = null,
                panel           = null,
                line            = null,
                buttons         = { left: null, right: null },
                id_attribute    = "data-engine-element-id",
                id              = null,
                record          = null,
                attribute       = {name:null, value:null},
                sizes           = [];
            if (instances !== null) {
                if (typeof instances.length === "number") {
                    for (var index = instances.length - 1; index >= 0; index -= 1) {
                        attribute.name  = instances[index].getAttribute("data-engine-active-attr-name"  );
                        attribute.value = instances[index].getAttribute("data-engine-active-attr-value" );
                        if (typeof attribute.name === "string" && typeof attribute.value === "string") {
                            id = "tab.A.ID." + Math.round(Math.random() * 100000).toFixed(0).toString();
                            instances[index].setAttribute(id_attribute, id);
                            tabs_titles     = pure.nodes.select.all("div[data-engine-element=\"Tabs.A\"]["      + id_attribute + "=\"" + id + "\"] div[data-engine-element=\"Tabs.A.Title\"]"               );
                            tabs_content    = pure.nodes.select.all("div[data-engine-element=\"Tabs.A\"]["      + id_attribute + "=\"" + id + "\"] div[data-engine-element=\"Tabs.A.Tab\"]"                 );
                            tabs_items      = pure.nodes.select.all("div[data-engine-element=\"Tabs.A\"]["      + id_attribute + "=\"" + id + "\"] p[data-engine-element=\"Tabs.A.Item\"]"                  );
                            container       = pure.nodes.select.first("div[data-engine-element=\"Tabs.A\"]["    + id_attribute + "=\"" + id + "\"] div[data-engine-element=\"Tabs.A.Titles.Container\"]"    );
                            line            = pure.nodes.select.first("div[data-engine-element=\"Tabs.A\"]["    + id_attribute + "=\"" + id + "\"] div[data-engine-element=\"Tabs.A.Line\"]"                );
                            panel           = pure.nodes.select.first("div[data-engine-element=\"Tabs.A\"]["    + id_attribute + "=\"" + id + "\"] div[data-engine-element=\"Tabs.A.Novigation.Panel\"]"    );
                            buttons.left    = pure.nodes.select.first("div[data-engine-element=\"Tabs.A\"]["    + id_attribute + "=\"" + id + "\"] div[data-engine-element=\"Tabs.A.Novigation.Left\"]"     );
                            buttons.right   = pure.nodes.select.first("div[data-engine-element=\"Tabs.A\"]["    + id_attribute + "=\"" + id + "\"] div[data-engine-element=\"Tabs.A.Novigation.Right\"]"    );
                            if (tabs_content !== null && tabs_titles    !== null && tabs_items  !== null &&
                                buttons.left !== null && buttons.right  !== null && container   !== null && panel !== null) {
                                if (typeof tabs_content.length === "number" &&
                                    typeof tabs_titles. length === "number" &&
                                    typeof tabs_items.  length === "number") {
                                    if (tabs_content.length === tabs_titles.length && tabs_items.length === tabs_titles.length) {
                                        for (var sub_index = tabs_titles.length - 1; sub_index >= 0; sub_index -= 1) {
                                            sizes.unshift(pure.nodes.render.size(tabs_titles[sub_index]).width);
                                        }
                                        record = pure.tabs.A.data.set(null, container, line, panel, tabs_content, tabs_titles, attribute, sizes);
                                        pure.tabs.A.actions.resize  (record.id);
                                        pure.tabs.A.actions.init    (record.id, buttons, tabs_titles, tabs_items);
                                        pure.tabs.A.actions.toggle  (null, record.id, 0);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        },
        actions: {
            init: function (id, buttons, titles, items) {
                var data = pure.tabs.A.data.get(id);
                if (data !== null) {
                    for (var index = titles.length - 1; index >= 0; index -= 1) {
                        (function(id, index, title){
                            pure.events.add(title, "click", function (event) { pure.tabs.A.actions.toggle(event, id, index); });
                        }(id, index, titles[index]));
                    }
                    for (var index = items.length - 1; index >= 0; index -= 1) {
                        (function (id, index, item) {
                            pure.events.add(item, "click", function (event) { pure.tabs.A.actions.toggle(event, id, index); });
                        }(id, index, items[index]));
                    }
                    pure.events.add(window,         "resize",   function (event) { pure.tabs.A.actions.resize   (id);           });
                    pure.events.add(buttons.left,   "click",    function (event) { pure.tabs.A.actions.left     (event, id);    });
                    pure.events.add(buttons.right,  "click",    function (event) { pure.tabs.A.actions.right    (event, id);    });
                }
            },
            toggle: function (event, id, index) {
                function hideAll(tabs) {
                    for (var index = tabs.length - 1; index >= 0; index -= 1) {
                        tabs[index].style.display = "none";
                    }
                };
                function deactiveAll(titles, attribute) {
                    for (var index = titles.length - 1; index >= 0; index -= 1) {
                        titles[index].removeAttribute(attribute.name);
                    }
                };
                var data = pure.tabs.A.data.get(id);
                if (data !== null) {
                    if (data.current !== index) {
                        hideAll     (data.tabs);
                        deactiveAll (data.titles, data.attribute);
                        data.tabs[index].   style.display = "";
                        data.titles[index]. setAttribute(data.attribute.name, data.attribute.value);
                        data.current = index;
                        pure.tabs.A.actions.show(id, index);
                    }
                }

                event !== null ? (event.stopPropagation ? event.stopPropagation() : (event.cancelBubble = true)) : event;
            },
            right: function (event, id) {
                var data            = pure.tabs.A.data.get(id),
                    width           = 0,
                    offset          = 0,
                    needed_space    = -1;
                if (data !== null) {
                    width = data.offset + data.width - data.width_panel;
                    for (var index = 0, max_index = data.titles.length; index < max_index; index += 1){
                        offset += data.sizes[index];
                        if (offset > width) {
                            needed_space    = offset - width;
                            break;
                        }
                    }
                    if (needed_space !== -1) {
                        data.offset             += needed_space;
                        data.line.style.left    = "-" + data.offset + "px";
                    }
                }
                event.stopPropagation ? event.stopPropagation() : (event.cancelBubble = true);
            },
            left: function (event, id) {
                var data            = pure.tabs.A.data.get(id),
                    offset          = 0,
                    needed_space    = -1;
                if (data !== null) {
                    if (data.offset > 0) {
                        for (var index = 0, max_index = data.titles.length; index < max_index; index += 1) {
                            offset += data.sizes[index];
                            if (offset > data.offset) {
                                needed_space = data.offset - (offset - data.sizes[index]);
                                if (needed_space === 0 && index > 0) {
                                    needed_space = data.sizes[index - 1];
                                }
                                break;
                            }
                        }
                        if (needed_space !== -1) {
                            data.offset -= needed_space;
                            data.offset = (data.offset < 0 ? 0 : data.offset);
                            data.line.style.left = "-" + data.offset + "px";
                        }
                    }
                }
                event.stopPropagation ? event.stopPropagation() : (event.cancelBubble = true);
            },
            show : function(id, title_index){
                var data            = pure.tabs.A.data.get(id),
                    width           = 0,
                    offset          = 0,
                    updated         = false;
                if (data !== null) {
                    for (var index = 0, max_index = title_index; index < max_index; index += 1) {
                        offset += data.sizes[index];
                    }
                    if (offset < data.offset) {
                        data.offset = offset;
                        updated     = true;
                    } else if (offset + data.sizes[title_index] > data.offset + data.width - data.width_panel) {
                        data.offset += (offset + data.sizes[title_index] - (data.offset + data.width - data.width_panel));
                        updated     = true;
                    }
                    if (updated === true) {
                        data.line.style.left = "-" + data.offset + "px";
                    }
                }
            },
            resize: function (id) {
                var data = pure.tabs.A.data.get(id);
                if (data !== null) {
                    data.width          = pure.nodes.render.size(data.container).width;
                    data.width_panel    = pure.nodes.render.size(data.panel).width;
                }
            }
        }
    };
    pure.system.start.add(pure.tabs.A.init);
}());