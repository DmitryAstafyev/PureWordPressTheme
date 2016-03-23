(function () {
    if (typeof window.pure              !== "object") { window.pure             = {}; }
    if (typeof window.pure.positioning  !== "object") { window.pure.positioning = {}; }
    "use strict";
    window.pure.positioning.A = {
        minWidth    : 300,//px
        init        : function(){
            var instances = pure.nodes.select.all('*[data-engine-positioning-A-container]:not([data-engine-element-inited])');
            if (instances !== null){
                Array.prototype.forEach.call(
                    instances,
                    function(item, _index, source){
                        var id          = pure.tools.IDs.get('highlights'),
                            items       = null,
                            parent      = {
                                attrs       : pure.nodes.attributes.get(item),
                                nodeName    : item.nodeName
                            },
                            minWidth    = item.getAttribute('data-engine-positioning-A-minWidth');
                        item.setAttribute('data-engine-positioning-A-ID', id);
                        minWidth    = (minWidth === null ? pure.positioning.A.minWidth : minWidth);
                        items       = pure.nodes.select.all('*[data-engine-positioning-A-ID="' + id + '"] *[data-engine-positioning-A-item]');
                        if (items !== null){
                            item.parentNode.setAttribute('data-engine-positioning-A-parent-ID', id);
                            if (items.length > 0){
                                pure.events.add(
                                    window,
                                    'resize',
                                    function(){
                                        pure.positioning.A.resize.build(items, parent, id);
                                    }
                                );
                                pure.positioning.A.resize.build(items, parent, id);
                            }
                        }
                        item.setAttribute('data-engine-element-inited', 'true');
                    }
                );
            }
        },
        resize      : {
            history : {
                data    : {},
                get     : function(id){
                    var data = pure.positioning.A.resize.history.data;
                    return (typeof data[id] !== 'undefined' ? data[id] : -1);
                },
                set     : function(id, value){
                    pure.positioning.A.resize.history.data[id] = value;
                }
            },
            build   : function(items, parent, id){
                function markParents(items){
                    for(var index = items.length - 1; index >= 0; index -= 1){
                        items[index].parentNode.setAttribute('data-engine-positioning-A-to-remove', 'true');
                    }
                };
                function removeParents(){
                    var parents = pure.nodes.select.all('*[data-engine-positioning-A-to-remove]');
                    for(var index = parents.length - 1; index >= 0; index -= 1){
                        parents[index].parentNode.removeChild(parents[index]);
                    }
                };
                function createRowsNodes(parent, rows){
                    var node    = null,
                        nodes   = [];
                    for (var index = 0; index < rows; index += 1){
                        node = document.createElement(parent.nodeName);
                        pure.nodes.attributes.set(node, parent.attrs);
                        nodes.push(node);
                    }
                    return nodes;
                };
                function getColumnsInRows(count, max_rows, max_columns){
                    function normalize(rows){
                        var rows_numbers = [];
                        for (var index = 0, max_index = rows.length; index < max_index; index += 1){
                            for (var _index = 0, _max_index = rows[index]; _index < _max_index; _index += 1){
                                rows_numbers.push(index);
                            }
                        }
                        return rows_numbers;
                    };
                    var rows    = [],
                        last    = 0;
                    if (max_rows === 1){
                        rows.push(max_columns);
                        return {
                            numbers : normalize(rows),
                            size    : 100 / max_columns
                        };
                    }else{
                        if (count % max_rows === 0){
                            for (var index = max_rows - 1; index >= 0; index -= 1){
                                rows.push(count / max_rows);
                            }
                            return {
                                numbers : normalize(rows),
                                size    : 100 / (count / max_rows)
                            };
                        }else{
                            for (var index = max_rows - 1; index >= 0; index -= 1){
                                rows.push(max_columns);
                            }
                            rows[rows.length - 1]   = count % max_columns;
                            last                    = rows[rows.length - 1] + rows[rows.length - 2];
                            rows[rows.length - 2]   = Math.ceil(last / 2);
                            rows[rows.length - 1]   = last - rows[rows.length - 2];
                            return {
                                numbers : normalize(rows),
                                size    : 100 / (rows[0])
                            };
                        }
                    }
                };
                function setSize(items, width){
                    for(var index = items.length - 1; index >=0; index -= 1){
                        items[index].style.width = width + '%';
                    }
                };
                function appendRows(rows, container){
                    for(var index = 0, max_index = rows.length; index < max_index; index += 1){
                        container.appendChild(rows[index]);
                    }
                };
                var count           = items.length,
                    min             = pure.positioning.A.minWidth,
                    width           = null,
                    max_columns     = 0,
                    max_rows        = 0,
                    history         = pure.positioning.A.resize.history,
                    rows_nodes      = null,
                    rows_numbers    = null,
                    container = pure.nodes.select.first('*[data-engine-positioning-A-parent-ID="' + id + '"]');
                if (pure.nodes.render.isDisplayed(container) !== false){
                    width       = pure.nodes.render.size(container).width;
                    max_columns = Math.floor(width / min);
                    max_columns = (max_columns === 0 ? 1 : max_columns);
                    max_columns = (max_columns > count ? count : max_columns);
                    if (max_columns !== history.get(id)){
                        markParents(items);
                        history.set(id, max_columns);
                        max_rows        = Math.ceil(count / max_columns);
                        rows_nodes      = createRowsNodes(parent, max_rows);
                        rows_numbers    = getColumnsInRows(count, max_rows, max_columns);
                        for (var index = 0; index < count; index += 1){
                            rows_nodes[rows_numbers.numbers[index]].appendChild(items[index]);
                        }
                        setSize(items, rows_numbers.size);
                        appendRows(rows_nodes, container);
                        removeParents();
                    }
                }
            }
        }
    };
    pure.system.start.add(pure.positioning.A.init);
}());