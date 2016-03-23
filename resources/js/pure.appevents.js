(function () {
    "use strict";
    if (typeof window.pure !== "object") { window.pure = {}; }
    window.pure.appevents = {
        Events : {
            data    : {},
            methods : {
                serialize   : function (source_string){
                    return source_string.replace(/\W/gim, '_');

                },
                validate    : function (group_name, event_name) {
                    var serialize   = pure.appevents.Events.methods.serialize,
                        group_name  = (typeof group_name === "string" ? serialize(group_name) : null),
                        event_name  = (typeof event_name === "string" ? serialize(event_name) : null),
                        data        = pure.appevents.Events.data;
                    if (group_name !== null && event_name !== null) {
                        return (typeof data[group_name] !== "undefined" ? (typeof data[group_name][event_name] !== "undefined" ? true : null) : null);
                    }
                    return null;
                },
                register    : function (group_name, event_name){
                    var serialize   = pure.appevents.Events.methods.serialize,
                        group_name  = (typeof group_name === "string" ? serialize(group_name) : null),
                        event_name  = (typeof event_name === "string" ? serialize(event_name) : null),
                        data        = pure.appevents.Events.data;
                    if (group_name !== null && event_name !== null) {
                        data[group_name]    = (typeof data[group_name] === "undefined" ? {} : data[group_name]);
                        data                = data[group_name];
                        data[event_name]    = (typeof data[event_name] === "undefined" ? [] : data[event_name]);
                        return true;
                    }
                    return false;
                },
                clear       : function (group_name, event_name){
                    var serialize       = pure.appevents.Events.methods.serialize,
                        group_name      = (typeof group_name === "string" ? serialize(group_name) : null),
                        event_name      = (typeof event_name === "string" ? serialize(event_name) : null),
                        handles_data    = pure.appevents.Events.methods.get(group_name, event_name),
                        data            = pure.appevents.Events.data;
                    if (handles_data !== null) {
                        if (handles_data.length === 0) {
                            data[group_name][event_name] = null;
                            delete data[group_name][event_name];
                            if (Object.keys(data[group_name]).length === 0) {
                                delete data[group_name];
                            }
                            return true;
                        }
                    }
                    return false;
                },
                add         : function (group_name, event_name, handle, handle_id, register) {
                    var serialize       = pure.appevents.Events.methods.serialize,
                        group_name      = (typeof group_name    === "string"    ? serialize(group_name) : null  ),
                        event_name      = (typeof event_name    === "string"    ? serialize(event_name) : null  ),
                        handle_id       = (typeof handle_id     === "string"    ? handle_id             : null  ),
                        handle          = (typeof handle        === "function"  ? handle                : null  ),
                        register        = (typeof register      === "boolean"   ? register              : true  ),
                        event_handles   = null,
                        data            = pure.appevents.Events.data;
                    if (register === true) {
                        pure.appevents.Events.methods.register(group_name, event_name);
                    }
                    if (pure.appevents.Events.methods.validate(group_name, event_name) === true && handle_id !== null && handle !== null) {
                        data[group_name][event_name].push({
                            handle      : handle,
                            id          : handle_id
                        });
                        return true;
                    }
                    return false;
                },
                get         : function (group_name, event_name) {
                    var serialize   = pure.appevents.Events.methods.serialize,
                        group_name  = (typeof group_name === "string" ? serialize(group_name) : null),
                        event_name  = (typeof event_name === "string" ? serialize(event_name) : null),
                        data        = pure.appevents.Events.data;
                    if (pure.appevents.Events.methods.validate(group_name, event_name) === true) {
                        return data[group_name][event_name];
                    }
                    return null;
                }
            }
        },
        Actions : {
            listen  : function (group_name, event_name, handle, handle_id) {
                return pure.appevents.Events.methods.add(group_name, event_name, handle, handle_id);
            },
            call    : function (group_name, event_name, params, callback) {
                var handles_data    = pure.appevents.Events.methods.get(group_name, event_name),
                    callback        = (typeof callback === "function" ? callback : null),
                    callback_result = null;
                if (handles_data !== null) {
                    for (var index = handles_data.length - 1; index >= 0; index -= 1) {
                        callback_result = pure.system.runHandle(handles_data[index].handle, params, "[Environment.AppEvents][Actions.call]", this);
                        if (typeof callback_result !== "undefined" && callback !== null) {
                            if (callback_result !== null) {
                                pure.system.runHandle(callback, callback_result, "[Environment.AppEvents][Actions.call]", this);
                            }
                        }
                    }
                    return true;
                }
                return false;
            },
            remove  : function (group_name, event_name, id, clear_group) {
                var handles_data    = pure.appevents.Events.methods.get(group_name, event_name),
                    id              = (typeof id            === "string"    ? id            : null  ),
                    clear_group     = (typeof clear_group   === "boolean"   ? clear_group   : false );
                if (handles_data !== null) {
                    for (var index = handles_data.length - 1; index >= 0; index -= 1) {
                        if (handles_data[index].id === id) {
                            handles_data.splice(index, 1);
                        }
                    }
                    if (clear_group === true) {
                        pure.appevents.Events.methods.clear(group_name, event_name);
                    }
                    return true;
                }
                return false;
            }
        }
    }
}());
