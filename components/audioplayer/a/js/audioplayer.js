(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.components               !== "object") { window.pure.components              = {}; }
    if (typeof window.pure.components.audioplayer   !== "object") { window.pure.components.audioplayer  = {}; }
    "use strict";
    /*
    CONTROLS
    ELEMENT                 ATTRIBUTE                           VALUE                               INFO
    container of player     data-type-use                       Pure.Components.AudioPlayer.JCrop
                            data-type-AudioPlayer-playlist                                          link to array with playlist
                            data-type-AudioPlayer-ID                                                ID of player (can be undefined, but its necessary for volume control
                            data-AudioPlayer-handle-volume                                          handle for set volume
                            data-AudioPlayer-handle-volume-param                                    param for volume handle
                            data-AudioPlayer-handle-progress                                        handle for set progress
                            data-AudioPlayer-handle-progress-param                                  param for progress handle
    button PREVIOUS         data-type-AudioPlayer               previous
    button PLAY / PAUSE     data-type-AudioPlayer               playpause
                            data-type-AudioPlayer-state         play || pause
    button NEXT             data-type-AudioPlayer               next
    current played time     data-type-AudioPlayer               current
    total time of track     data-type-AudioPlayer               total
    container of volume     data-type-AudioPlayer               volume
    container of progress   data-type-AudioPlayer               progress

    */
    window.pure.components.audioplayer.A = {
        Storage : {
            data    : {},
            set     : function(id, nodes, playlist, handles){
                var data = pure.components.audioplayer.A.Storage.data;
                if (typeof data[id] !== 'object'){
                    data[id] = {
                        id          : id,
                        nodes       : {
                            container   : nodes.container,
                            buttons     : {
                                previous    : nodes.buttons.previous,
                                playpause   : nodes.buttons.playpause,
                                next        : nodes.buttons.next
                            },
                            time        : {
                                current     : nodes.time.current,
                                total       : nodes.time.total
                            },
                            addition    : {
                                volume      : nodes.addition.volume,
                                progress    : nodes.addition.progress
                            }
                        },
                        handles     : {
                            setVolume           : handles.setVolume,
                            setVolumeParam      : handles.setVolumeParam,
                            setProgress         : handles.setProgress,
                            setProgressParam    : handles.setProgressParam,
                            setGhost            : handles.setGhost,
                            setGhostParam       : handles.setGhostParam,
                            listener            : {}
                        },
                        playlist    : playlist,
                        canplay     : false,
                        current     : 0,
                        volume      : 0.5,
                        time        : {
                            current     : 0,
                            total       : 0
                        }
                    };
                    return data[id];
                }
                return false;
            },
            get     : function(id){
                return (typeof pure.components.audioplayer.A.Storage.data[id] === 'object' ? pure.components.audioplayer.A.Storage.data[id] : null);
            }
        },
        init : function(){
            function initInstance(instance) {
                function getHandles(instance){
                    var handles = {
                        setVolume           : instance.getAttribute('data-AudioPlayer-handle-volume'            ),
                        setVolumeParam      : instance.getAttribute('data-AudioPlayer-handle-volume-param'      ),
                        setProgress         : instance.getAttribute('data-AudioPlayer-handle-progress'          ),
                        setProgressParam    : instance.getAttribute('data-AudioPlayer-handle-progress-param'    ),
                        setGhost            : instance.getAttribute('data-AudioPlayer-handle-ghost'             ),
                        setGhostParam       : instance.getAttribute('data-AudioPlayer-handle-ghost-param'       )
                    };
                    handles.setVolume           = (typeof handles.setVolume         === 'string' ? (handles.setVolume           !== '' ? handles.setVolume          : null) : null);
                    handles.setVolumeParam      = (typeof handles.setVolumeParam    === 'string' ? (handles.setVolumeParam      !== '' ? handles.setVolumeParam     : null) : null);
                    handles.setProgress         = (typeof handles.setProgress       === 'string' ? (handles.setProgress         !== '' ? handles.setProgress        : null) : null);
                    handles.setProgressParam    = (typeof handles.setProgressParam  === 'string' ? (handles.setProgressParam    !== '' ? handles.setProgressParam   : null) : null);
                    handles.setGhost            = (typeof handles.setGhost          === 'string' ? (handles.setGhost            !== '' ? handles.setGhost           : null) : null);
                    handles.setGhostParam       = (typeof handles.setGhostParam     === 'string' ? (handles.setGhostParam       !== '' ? handles.setGhostParam      : null) : null);
                    handles.setVolume           = (handles.setVolume    === null ? null : (typeof pure.system.getInstanceByPath(handles.setVolume)      === 'function' ? pure.system.getInstanceByPath(handles.setVolume)   : handles.setVolume));
                    handles.setProgress         = (handles.setProgress  === null ? null : (typeof pure.system.getInstanceByPath(handles.setProgress)    === 'function' ? pure.system.getInstanceByPath(handles.setProgress) : handles.setProgress));
                    handles.setGhost            = (handles.setGhost     === null ? null : (typeof pure.system.getInstanceByPath(handles.setGhost)       === 'function' ? pure.system.getInstanceByPath(handles.setGhost)    : handles.setGhost));
                    return handles;
                };
                function getPlaylist(instance){
                    var playlist_data = instance.getAttribute('data-type-AudioPlayer-playlist');
                    if (typeof playlist_data === 'string' && playlist_data !== ''){
                        playlist_data = pure.system.getInstanceByPath(playlist_data);
                        return (playlist_data !== null ? (playlist_data instanceof Array === true ? playlist_data : false) : false);
                    }
                    return false;
                };
                function getID(instance){
                    var id          = instance.getAttribute('data-type-AudioPlayer-ID'),
                        parentID    = null;
                    if (typeof id === 'string' && id !== ''){
                        return id;
                    }
                    parentID = pure.tools.IDs.getGlobalParentID(instance);
                    return (parentID !== null ? parentID : pure.tools.IDs.get('Pure.Components.AudioPlayer.A'));
                };
                var id          = getID(instance),
                    nodes       = {
                        container   : null,
                        buttons     : {
                            previous    : null,
                            playpause   : null,
                            next        : null
                        },
                        time        : {
                            current     : null,
                            total       : null,
                            buffered    : null
                        },
                        addition    : {
                            volume      : null,
                            progress    : null
                        }
                    },
                    handles     = getHandles(instance),
                    playlist    = getPlaylist(instance),
                    dataset     = null;
                if (playlist !== null){
                    instance.setAttribute('data-type-AudioPlayer-ID', id);
                    nodes.container         = instance;
                    //Get basic nodes
                    nodes.buttons.previous  = pure.nodes.select.first('*[data-type-AudioPlayer-ID="' + id + '"] *[data-type-AudioPlayer="previous"]'    );
                    nodes.buttons.playpause = pure.nodes.select.first('*[data-type-AudioPlayer-ID="' + id + '"] *[data-type-AudioPlayer="playpause"]'   );
                    nodes.buttons.next      = pure.nodes.select.first('*[data-type-AudioPlayer-ID="' + id + '"] *[data-type-AudioPlayer="next"]'        );
                    if (nodes.buttons.playpause !== null){
                        //Get addition nodes
                        nodes.time.current      = pure.nodes.select.first('*[data-type-AudioPlayer-ID="' + id + '"] *[data-type-AudioPlayer="current"]' );
                        nodes.time.total        = pure.nodes.select.first('*[data-type-AudioPlayer-ID="' + id + '"] *[data-type-AudioPlayer="total"]'   );
                        nodes.addition.volume   = pure.nodes.select.first('*[data-type-AudioPlayer-ID="' + id + '"] *[data-type-AudioPlayer="volume"]'  );
                        nodes.addition.progress = pure.nodes.select.first('*[data-type-AudioPlayer-ID="' + id + '"] *[data-type-AudioPlayer="progress"]');
                        //Attach events
                        pure.components.audioplayer.A.Events.attach(id, nodes);
                        //Save data
                        dataset = pure.components.audioplayer.A.Storage.set(id, nodes, playlist, handles);
                        if (dataset !== null){
                            //Update volume
                            pure.components.audioplayer.A.Players.actions.volume.update(instance);
                            //Set volume in player
                            pure.components.audioplayer.A.Events.handles.volume(dataset);
                            //Mark as initialized
                            instance.setAttribute('data-type-component-inited', 'true');
                            return true;
                        }
                    }
                }
                instance.setAttribute('data-type-component-inited', 'fail');
                return false;
            }
            var instances = pure.nodes.select.all('*[data-type-use="Pure.Components.AudioPlayer.A"]:not([data-type-component-inited])');
            if (instances !== null){
                pure.components.audioplayer.A.Build.init();
                for (var index = instances.length - 1; index >= 0; index -= 1){
                    initInstance(instances[index]);
                }
            }
            pure.components.audioplayer.A.More.init();
        },
        Players : {
            state   : {
                loaded      : {
                    src     : null,
                    play    : false
                },
                state       : {id: null, player: null, current: null, playing: false, src: null},
                set         : function(player, id, current, src){
                    pure.components.audioplayer.A.Players.state.state = {
                        id      : id,
                        player  : player,
                        current : current,
                        src     : src
                    };
                },
                get         : function(){
                    return pure.components.audioplayer.A.Players.state.state;
                },
                play : function(){
                    pure.components.audioplayer.A.Players.state.state.playing = true;
                },
                stop : function(){
                    pure.components.audioplayer.A.Players.state.state.playing = false;
                },
                setOnLoad      : function(src){
                    pure.components.audioplayer.A.Players.state.loaded = {
                        src :src,
                        play:false
                    };
                },
                getOnLoad  : function(){
                    return pure.components.audioplayer.A.Players.state.loaded;
                },
                setOnLoadPlay  : function(play){
                    pure.components.audioplayer.A.Players.state.loaded.play = play;
                }
            },
            actions : {
                load        : function(instance){
                    function resetBuffered(instance){
                        instance.time.buffered = 0;
                        pure.components.audioplayer.A.Events.handles.buffered(instance);
                    };
                    var instance = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance));
                    if (instance !== null){
                        if (typeof instance.playlist[instance.current] === 'object') {
                            if (typeof instance.playlist[instance.current].src  === 'string' &&
                                typeof instance.playlist[instance.current].type === 'string') {
                                if (pure.components.audioplayer.A.Players.html5.isSupport(instance.playlist[instance.current].type) === true){
                                    pure.components.audioplayer.A.Players.html5.load(instance);
                                    pure.components.audioplayer.A.Players.state.set('html5', instance.id, instance.current, instance.playlist[instance.current].src);
                                }else{
                                    pure.components.audioplayer.A.Players.flash.load(instance);
                                    pure.components.audioplayer.A.Players.state.set('flash', instance.id, instance.current, instance.playlist[instance.current].src);
                                }
                                pure.components.audioplayer.A.Players.state.setOnLoad(instance.playlist[instance.current].src);
                                pure.components.audioplayer.A.Players.actions.volume.update(instance);
                                resetBuffered(instance);
                                return true;
                            }
                        }
                    }
                    return false;
                },
                onLoad      : function(src){
                    var loaded  = pure.components.audioplayer.A.Players.state.getOnLoad(),
                        state   = pure.components.audioplayer.A.Players.state.get();
                    if (loaded.src === src && state !== null){
                        if (loaded.play === true){
                            pure.components.audioplayer.A.Players[state.player].play();
                        }
                    }
                },
                stop        : function(instance){
                    var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance)),
                        state       = pure.components.audioplayer.A.Players.state.get();
                    if (instance !== null && state.id !== null){
                        if (instance.id === state.id){
                            if (typeof instance.playlist[instance.current] === 'object') {
                                if (typeof instance.playlist[instance.current].src  === 'string' &&
                                    typeof instance.playlist[instance.current].type === 'string') {
                                    instance.playlist[instance.current].position    = instance.time.current;
                                    instance.time.current                           = 0;
                                    pure.components.audioplayer.A.Players[state.player].stop();
                                    pure.components.audioplayer.A.Players.state.stop();
                                    pure.components.audioplayer.A.Render.stop(instance);
                                }
                            }
                        }
                    }
                },
                play        : function(instance){
                    var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance)),
                        state       = pure.components.audioplayer.A.Players.state.get();
                    if (instance !== null && state.id !== null){
                        if (instance.id === state.id){
                            instance.playlist[instance.current].position = (typeof instance.playlist[instance.current].position === 'number' ? instance.playlist[instance.current].position : 0);
                            pure.components.audioplayer.A.Players.actions.volume.update(instance);
                            pure.components.audioplayer.A.Players.state.setOnLoadPlay(true);
                            pure.components.audioplayer.A.Players[state.player].play(instance.playlist[instance.current].position);
                            pure.components.audioplayer.A.Players.state.play();
                            pure.components.audioplayer.A.Render.play(instance);
                        }
                    }
                },
                switcher    : function(instance, listed){
                    var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance)),
                        listed      = (typeof listed === 'boolean' ? listed : false),
                        state       = pure.components.audioplayer.A.Players.state.get();
                    if (instance !== null && state !== null){
                        if (state.player !== null){
                            if ((instance.id !== state.id ||
                                (instance.id === state.id && instance.current !== state.current))&&
                                (state.playing !== false)){
                                pure.components.audioplayer.A.Players.actions.stop(state.id);
                            }
                            if (instance.id === state.id && instance.current === state.current){
                                if (state.playing === true){
                                    pure.components.audioplayer.A.Players.actions.stop(state.id);
                                    return false;//paused
                                }else{
                                    pure.components.audioplayer.A.Players.actions.play(state.id);
                                    return true;//playing
                                }
                            }
                        }
                        if (pure.components.audioplayer.A.Players.actions.load(instance) === true){
                            if (instance.canplay === true || listed === false){
                                pure.components.audioplayer.A.Players.actions.play(instance);
                                return true;//playing
                            }
                            return false;//paused
                        }
                    }
                    return null;//error
                },
                volume : {
                    update  : function(instance){
                        var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance)),
                            state       = pure.components.audioplayer.A.Players.state.get();
                        if (instance!== null && state.player !== null){
                            pure.components.audioplayer.A.Players[state.player].volume.set(instance.volume);
                        }
                    },
                    set     : function(instance, value){
                        var instance = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance));
                        if (instance!== null){
                            instance.volume = parseFloat(value);
                            pure.components.audioplayer.A.Players.actions.volume.update(instance);
                        }
                    },
                    get     : function(instance){
                        var instance = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance));
                        if (instance!== null){
                            return instance.volume;
                        }
                        return null;
                    }
                },
                position : {
                    update  : function(instance){
                        var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance)),
                            state       = pure.components.audioplayer.A.Players.state.get();
                        if (instance!== null && state.player !== null){
                            if (state.playing === true){
                                pure.components.audioplayer.A.Players[state.player].position.set(instance.time.current, instance.volume);
                            }
                            pure.components.audioplayer.A.Render.updateCurrentTime(instance);
                        }
                    },
                    set     : function(instance, value){
                        var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance)),
                            value       = parseFloat(value);
                        if (instance!== null){
                            if (value >= 0 && value <= 1) {
                                instance.time.current                           = Math.floor(instance.time.total * value);
                                instance.playlist[instance.current].position    = instance.time.current;
                                pure.components.audioplayer.A.Players.actions.position.update(instance);
                            }
                        }
                    },
                    get     : function(instance){
                        var instance = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance));
                        if (instance!== null){
                            return instance.time.current;
                        }
                        return null;
                    }
                }
            },
            html5   :{
                instance    : null,
                events      : {
                    init            : function(){
                        var player = pure.components.audioplayer.A.Players.html5.get();
                        if (player !== null) {
                            pure.events.add(player, 'canplay',          pure.components.audioplayer.A.Players.html5.events.onLoad           );
                            pure.events.add(player, 'timeupdate',       pure.components.audioplayer.A.Players.html5.events.timeupdate       );
                            pure.events.add(player, 'durationchange',   pure.components.audioplayer.A.Players.html5.events.durationchange   );
                            pure.events.add(player, 'progress',         pure.components.audioplayer.A.Players.html5.events.buffered         );
                            pure.events.add(player, 'ended',            pure.components.audioplayer.A.Players.html5.events.ended            );
                            //pure.events.add(player, 'play',             function(event){ pure.components.audioplayer.JCrop.Events.actions.player.play           (id, event);});
                            //pure.events.add(player, 'pause',            function(event){ pure.components.audioplayer.JCrop.Events.actions.player.pause          (id, event);});
                            //pure.events.add(player, 'ended',            function(event){ pure.components.audioplayer.JCrop.Events.actions.player.ended          (id, event);});
                        }
                    },
                    onLoad          : function(event){
                        var player  = pure.components.audioplayer.A.Players.html5.get(),
                            src     = null;
                        if (player !== null) {
                            src = player.getAttribute('src');
                            if (typeof src === 'string'){
                                pure.components.audioplayer.A.Players.actions.onLoad(src);
                            }
                        }
                    },
                    timeupdate      : function(event){
                        var instance    = null,
                            state       = pure.components.audioplayer.A.Players.state.get(),
                            player      = pure.components.audioplayer.A.Players.html5.get();
                        if (state !== null && player !== null){
                            instance = pure.components.audioplayer.A.Storage.get(state.id);
                            if (instance !== null){
                                instance.time.current = (typeof player.currentTime === 'number' ? (isNaN(player.currentTime) === false ? player.currentTime : 0) : 0);
                                pure.components.audioplayer.A.Render.updateCurrentTime(instance);
                            }
                        }
                    },
                    durationchange  : function(event){
                        var instance    = null,
                            state       = pure.components.audioplayer.A.Players.state.get(),
                            player      = pure.components.audioplayer.A.Players.html5.get();
                        if (state !== null && player !== null){
                            instance = pure.components.audioplayer.A.Storage.get(state.id);
                            if (instance !== null){
                                instance.time.total = (typeof player.duration === 'number' ? (isNaN(player.duration) === false ? player.duration : 0) : 0);
                                pure.components.audioplayer.A.Render.updateTotalTime(instance);
                            }
                        }
                    },
                    ended           : function(event){
                        var instance    = null,
                            state       = pure.components.audioplayer.A.Players.state.get(),
                            player      = pure.components.audioplayer.A.Players.html5.get();
                        if (state !== null && player !== null){
                            instance = pure.components.audioplayer.A.Storage.get(state.id);
                            if (instance !== null){
                                pure.components.audioplayer.A.Players.actions.stop(instance);
                                pure.components.audioplayer.A.Players.actions.position.set(instance, 0);
                            }
                        }
                    },
                    buffered        : function(){
                        var instance    = null,
                            state       = pure.components.audioplayer.A.Players.state.get(),
                            player      = pure.components.audioplayer.A.Players.html5.get(),
                            buffered    = null;
                        if (state !== null && player !== null){
                            instance = pure.components.audioplayer.A.Storage.get(state.id);
                            if (instance !== null){
                                try{
                                    buffered                = player.buffered.end(player.buffered.length - 1);
                                    instance.time.buffered  = (typeof buffered === 'number' ? (isNaN(buffered) === false ? buffered : 0) : 0);
                                }catch (e){
                                    instance.time.buffered  = 0;
                                }
                                pure.components.audioplayer.A.Events.handles.buffered(instance);
                            }
                        }
                    }
                },
                get         : function(){
                    if (pure.components.audioplayer.A.Players.html5.instance === null){
                        pure.components.audioplayer.A.Players.html5.instance = pure.nodes.select.first('AUDIO[id="PureComponentsFlashAudioPlayerAHTML5"]');
                        pure.components.audioplayer.A.Players.html5.events.init();
                    }
                    return pure.components.audioplayer.A.Players.html5.instance;
                },
                isSupport   : function(mimeType){
                    var player = pure.components.audioplayer.A.Players.html5.get(),
                        result = false;
                    if (player !== null){
                        result = (typeof player.canPlayType === 'function'  ? player.canPlayType(mimeType)      : false         );
                        result = (typeof result             === 'undefined' ? false                             : result        );
                        result = (typeof result             === 'string'    ? (result !== '' ? true : false)    : result        );
                        result = (typeof result             === 'boolean'   ? result                            : false         );
                    }
                    return result;
                },
                load        : function(instance){
                    var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance)),
                        player      = pure.components.audioplayer.A.Players.html5.get();
                    if (instance !== null && player !== null){
                        player.setAttribute('src', instance.playlist[instance.current].src);
                        player.load();
                    }
                },
                play        : function(position){
                    var player = pure.components.audioplayer.A.Players.html5.get();
                    if (player !== null){
                        try{
                            player.currentTime = parseInt(position);
                        }catch (e){ }
                        player.play();
                    }
                },
                stop        : function(){
                    var player = pure.components.audioplayer.A.Players.html5.get();
                    if (player !== null){
                        player.pause();
                    }
                },
                volume      : {
                    set : function(value){
                        var player = pure.components.audioplayer.A.Players.html5.get();
                        if (player !== null){
                            player.volume = parseFloat(value);
                        }
                    },
                    get : function(){
                        var player = pure.components.audioplayer.A.Players.html5.get();
                        if (player !== null){
                            return parseFloat(player.volume);
                        }
                    }
                },
                position    : {
                    set : function(value){
                        var player = pure.components.audioplayer.A.Players.html5.get();
                        if (player !== null){
                            try{
                                player.currentTime = parseInt(value);
                            }catch (e){ }
                        }
                    },
                    get : function(){
                        var player = pure.components.audioplayer.A.Players.html5.get();
                        if (player !== null){
                            return player.currentTime;
                        }
                    }
                }
            },
            flash   : {
                instance    : null,
                events      : {
                    onLoad          : function(src){
                        var player  = pure.components.audioplayer.A.Players.flash.get();
                        if (player !== null) {
                            if (typeof src === 'string'){
                                pure.components.audioplayer.A.Players.actions.onLoad(src);
                            }
                        }
                    },
                    timeupdate      : function(current, peak){
                        var instance    = null,
                            state       = pure.components.audioplayer.A.Players.state.get(),
                            current     = (typeof current !== 'undefined' ? parseInt(current) : null);
                        if (state !== null && current !== null){
                            instance = pure.components.audioplayer.A.Storage.get(state.id);
                            if (instance !== null){
                                current                 = (typeof current === 'number' ? (isNaN(current) === false ? current : 0) : 0);
                                instance.time.current   = current;
                                instance.peak           = peak;
                                pure.components.audioplayer.A.Render.updateCurrentTime  (instance);
                                pure.components.audioplayer.A.Events.handles.peak       (instance);
                            }
                        }
                    },
                    durationchange  : function(total){
                        var instance    = null,
                            state       = pure.components.audioplayer.A.Players.state.get(),
                            total       = (typeof total !== 'undefined' ? parseInt(total) : null);
                        if (state !== null && total !== null){
                            instance = pure.components.audioplayer.A.Storage.get(state.id);
                            if (instance !== null){
                                instance.time.total = (typeof total === 'number' ? (isNaN(total) === false ? total : 0) : 0);
                                pure.components.audioplayer.A.Render.updateTotalTime(instance);
                            }
                        }
                    },
                    ended           : function(src){
                        var instance    = null,
                            state       = pure.components.audioplayer.A.Players.state.get(),
                            player      = pure.components.audioplayer.A.Players.flash.get();
                        if (state !== null && player !== null){
                            instance = pure.components.audioplayer.A.Storage.get(state.id);
                            if (instance !== null){
                                pure.components.audioplayer.A.Players.actions.stop(instance);
                                pure.components.audioplayer.A.Players.actions.position.set(instance, 0);
                            }
                        }
                    },
                    buffered        : function(buffered){
                        var instance    = null,
                            state       = pure.components.audioplayer.A.Players.state.get();
                        if (state !== null){
                            instance = pure.components.audioplayer.A.Storage.get(state.id);
                            if (instance !== null){
                                buffered                = (typeof buffered === 'number' ? (isNaN(buffered) === false ? parseFloat(buffered) : 0) : 0);
                                instance.time.buffered  = Math.floor(buffered * instance.time.total);
                                pure.components.audioplayer.A.Events.handles.buffered(instance);
                            }
                        }
                    }
                },
                get         : function(){
                    function setHandles(player){
                        if (player !== null) {
                            player.setEventHandle('onLoad',             'pure.components.audioplayer.A.Players.flash.events.onLoad'         );
                            player.setEventHandle('onTrackEnded',       'pure.components.audioplayer.A.Players.flash.events.ended'          );
                            player.setEventHandle('onLoadingProgress',  'pure.components.audioplayer.A.Players.flash.events.buffered'       );
                            player.setEventHandle('updateCurrentTime',  'pure.components.audioplayer.A.Players.flash.events.timeupdate'     );
                            player.setEventHandle('updateDuration',     'pure.components.audioplayer.A.Players.flash.events.durationchange' );
                        }
                    };
                    if (pure.components.audioplayer.A.Players.flash.instance === null){
                        if (typeof document["PureComponentsFlashAudioPlayerAFlash"] !== 'undefined'){
                            pure.components.audioplayer.A.Players.flash.instance = document["PureComponentsFlashAudioPlayerAFlash"];
                        }
                        if (typeof window["PureComponentsFlashAudioPlayerAFlash"]   !== 'undefined' &&
                            pure.components.audioplayer.A.Players.flash.instance    === null){
                            pure.components.audioplayer.A.Players.flash.instance = window["PureComponentsFlashAudioPlayerAFlash"];
                        }
                        setHandles(pure.components.audioplayer.A.Players.flash.instance);
                    }
                    return pure.components.audioplayer.A.Players.flash.instance;
                },
                load        : function(instance){
                    var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance)),
                        player      = pure.components.audioplayer.A.Players.flash.get();
                    if (instance !== null && player !== null){
                        try{
                            var n = player.load(instance.playlist[instance.current].src);
                        }catch (e){
                        }
                    }
                },
                play        : function(position){
                    var player = pure.components.audioplayer.A.Players.flash.get();
                    if (player !== null){
                        player.play(position);
                    }
                },
                stop        : function(){
                    var player = pure.components.audioplayer.A.Players.flash.get();
                    if (player !== null){
                        player.stop();
                    }
                },
                volume : {
                    set : function(value){
                        var player = pure.components.audioplayer.A.Players.flash.get();
                        if (player !== null){
                            player.setVolume(parseFloat(value));
                        }
                    },
                    get : function(){
                        var player = pure.components.audioplayer.A.Players.flash.get();
                        if (player !== null){
                            return parseFloat(player.getVolume());
                        }
                    }
                },
                position : {
                    set : function(value, volume){
                        var player = pure.components.audioplayer.A.Players.flash.get();
                        if (player !== null){
                            player.setPosition(parseFloat(value), parseFloat(volume));
                        }
                    },
                    get : function(){
                        var player = pure.components.audioplayer.A.Players.flash.get();
                        if (player !== null){
                            return player.getPosition();
                        }
                    }
                }
            }
        },
        Render : {
            play                : function(instance){
                var instance = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance));
                if (instance !== null){
                    instance.nodes.buttons.playpause.setAttribute('data-type-AudioPlayer-state','pause');
                }
            },
            stop                : function(instance){
                var instance = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance));
                if (instance !== null){
                    instance.nodes.buttons.playpause.setAttribute('data-type-AudioPlayer-state','play');
                }
            },
            updateCurrentTime   : function(instance){
                var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance)),
                    time        = null,
                    opacity     = null;
                if (instance !== null) {
                    time        = pure.components.audioplayer.A.Helpers.parseTime(instance.time.current);
                    opacity     = parseInt(time.seconds) / 2;
                    opacity     = ((opacity - Math.floor(opacity)) === 0 ? 0 : 1);
                    instance.nodes.time.current.innerHTML = (time.hours === '00' ? '' : time.hours + ':') + time.minutes + '<span style="opacity: ' + opacity + '">:</span>' + time.seconds;
                    pure.components.audioplayer.A.Events.handles.progress(instance);
                }
            },
            updateTotalTime     : function(instance){
                var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance)),
                    time        = null;
                if (instance !== null) {
                    time        = pure.components.audioplayer.A.Helpers.parseTime(instance.time.total);
                    instance.nodes.time.total.innerHTML = (time.hours === '00' ? '' : time.hours + ':') + time.minutes + ':' + time.seconds;
                    pure.components.audioplayer.A.Events.handles.progress(instance);
                }
            }
        },
        Events : {
            attach      : function(id, nodes){
                pure.events.add(nodes.buttons.playpause,    'click', function(event){ pure.components.audioplayer.A.Events.actions.controls.playpause   (id, event);});
                if (nodes.buttons.previous !== null){
                    pure.events.add(nodes.buttons.previous, 'click', function(event){ pure.components.audioplayer.A.Events.actions.controls.previous    (id, event);});
                }
                if (nodes.buttons.next !== null){
                    pure.events.add(nodes.buttons.next,     'click', function(event){ pure.components.audioplayer.A.Events.actions.controls.next        (id, event);});
                }
            },
            actions     : {
                controls : {
                    next        : function(id, event){
                        var instance    = pure.components.audioplayer.A.Storage.get(id);
                        if (instance !== null){
                            if (instance.current <= instance.playlist.length - 1){
                                instance.current = (instance.current === instance.playlist.length - 1 ? -1 : instance.current);
                                if (typeof instance.playlist[instance.current + 1] === 'object'){
                                    if (typeof instance.playlist[instance.current + 1].src === 'string'){
                                        instance.current += 1;
                                        pure.components.audioplayer.A.Players.actions.switcher(instance, true);
                                    }
                                }
                            }
                        }
                    },
                    playpause   : function(id, event){
                        var instance = pure.components.audioplayer.A.Storage.get(id);
                        if (instance !== null){
                            instance.canplay = pure.components.audioplayer.A.Players.actions.switcher(instance);
                        }
                    },
                    previous    : function(id, event){
                        var instance    = pure.components.audioplayer.A.Storage.get(id);
                        if (instance !== null){
                            if (instance.current >= 0){
                                if (typeof instance.playlist[instance.current - 1] === 'object'){
                                    if (typeof instance.playlist[instance.current - 1].src === 'string'){
                                        instance.current -= 1;
                                        pure.components.audioplayer.A.Players.actions.switcher(instance, true);
                                    }
                                }
                            }
                        }
                    }
                },
                player : {
                    ended           : function(id, event){
                        var instance = pure.components.audioplayer.A.Storage.get(id);
                        if (instance !== null){
                            pure.components.audioplayer.A.Events.actions.controls.next(id, null);
                        }
                    }
                }
            },
            handles : {
                waiting     : {
                    init : function(instance, handle_name){
                        if (typeof instance.handles.listener[handle_name] !== 'boolean'){
                            pure.appevents.Actions.listen('pure.components.slider', 'ready',
                                function () {
                                    pure.components.audioplayer.A.Events.handles[handle_name](instance.id);
                                    pure.appevents.Actions.remove('pure.components.slider', 'ready', instance.id + '_' + handle_name, false);
                                },
                                instance.id + '_' + handle_name
                            );
                            instance.handles.listener[handle_name] = true;
                        }
                    }
                },
                getHandle   : function(handle){
                    if (typeof  handle === 'function'){
                        return handle;
                    }else{
                        return (typeof pure.system.getInstanceByPath(handle) === 'function' ? pure.system.getInstanceByPath(handle) : handle);
                    }
                },
                volume      : function(instance){
                    var instance = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance));
                    if (instance !== null){
                        if (instance.handles.setVolume !== null){
                            instance.handles.setVolume = pure.components.audioplayer.A.Events.handles.getHandle(instance.handles.setVolume);
                            if (typeof instance.handles.setVolume === 'function'){
                                instance.handles.setVolume(instance.handles.setVolumeParam, instance.volume);
                            }else{
                                pure.components.audioplayer.A.Events.handles.waiting.init(instance, 'volume');
                            }
                        }
                    }
                },
                progress    : function(instance){
                    var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance));
                    if (instance !== null){
                        if (instance.handles.setProgress !== null){
                            instance.handles.setProgress = pure.components.audioplayer.A.Events.handles.getHandle(instance.handles.setProgress);
                            if (typeof instance.handles.setProgress === 'function'){
                                instance.handles.setProgress(instance.handles.setProgressParam, instance.time.current / instance.time.total);
                            }else{
                                pure.components.audioplayer.A.Events.handles.waiting.init(instance, 'progress');
                            }
                        }
                    }
                },
                buffered    : function(instance){
                    var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance));
                    if (instance !== null){
                        if (instance.handles.setGhost !== null){
                            instance.handles.setGhost = pure.components.audioplayer.A.Events.handles.getHandle(instance.handles.setGhost);
                            if (typeof instance.handles.setGhost === 'function'){
                                instance.handles.setGhost(instance.handles.setGhostParam + '_progress', 'buffered', instance.time.buffered / instance.time.total);
                            }else{
                                pure.components.audioplayer.A.Events.handles.waiting.init(instance, 'buffered');
                            }
                        }
                    }
                },
                peak        : function(instance){
                    var instance    = (typeof instance === 'object' ? instance : pure.components.audioplayer.A.Storage.get(instance));
                    if (instance !== null){
                        if (instance.handles.setGhost !== null){
                            instance.handles.setGhost = pure.components.audioplayer.A.Events.handles.getHandle(instance.handles.setGhost);
                            if (typeof instance.handles.setGhost === 'function'){
                                instance.handles.setGhost(instance.handles.setGhostParam + '_volume', 'peak', instance.peak);
                            }else{
                                pure.components.audioplayer.A.Events.handles.waiting.init(instance, 'peak');
                            }
                        }
                    }
                }
            }
        },
        Handles : {
            volume      : function(id, value){
                var instance = pure.components.audioplayer.A.Storage.get(id);
                if (instance !== null){
                    pure.components.audioplayer.A.Players.actions.volume.set(instance, parseFloat(value));
                }
            },
            position    : function(id, value){
                var instance = pure.components.audioplayer.A.Storage.get(id);
                if (instance !== null){
                    pure.components.audioplayer.A.Players.actions.position.set(instance, parseFloat(value));
                }
            }
        },
        Helpers : {
            parseTime : function(milliseconds){
                var minutes     = null,
                    seconds     = null,
                    hours       = null,
                    ms          = null;
                hours   = Math.floor(milliseconds / (60 * 60));
                minutes = Math.floor((milliseconds - hours * 60*60) / 60);
                seconds = Math.ceil(milliseconds - hours * 60*60 - minutes * 60);
                hours   = (hours    > 9 ? hours     : '0' + hours   );
                minutes = (minutes  > 9 ? minutes   : '0' + minutes );
                seconds = (seconds  > 9 ? seconds   : '0' + seconds );
                return {
                    hours           : hours,
                    minutes         : minutes,
                    seconds         : seconds,
                    milliseconds    : ms
                };
            }
        },
        Build : {
            initialized : false,
            init        : function(){
                var players = null,
                    src     = pure.system.getInstanceByPath('pure.settings.components.audioplayer_A');
                if (pure.components.audioplayer.A.Build.initialized === false && src !== null){
                    players                 = document.createElement('DIV');
                    players.style.position  = 'absolute';
                    players.style.width     = '0px';
                    players.style.height    = '0px';
                    players.style.overflow  = 'hidden';
                    players.style.opacity   = 0.01;
                    players.innerHTML       =  '<!--BEGIN:: Mini flash audioplayer for pure.components.audioplayer.A [used only if <audio> HTML5 does not work]  -->\
                                            <object\
                                            id="PureComponentsFlashAudioPlayerAFlash" width="0" height="0"\
                                            classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"\
                                            codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab"\
                                            style="position:absolute;opacity: 0.01;">\
                                                <param name="movie" value="' + src + '" />\
                                                <param name="allowScriptAccess" value="sameDomain" />\
                                                <embed\
                                                src="' + src + '"\
                                                name="PureComponentsFlashAudioPlayerAFlash" align="middle"\
                                                play="true" loop="false" quality="high" allowScriptAccess="sameDomain"\
                                                width="0" height="0" scale="exactfit"\
                                                type="application/x-shockwave-flash"\
                                                pluginspage="http://www.macromedia.com/go/getflashplayer">\
                                                </embed>\
                                            </object>\
                                            <!--JCrop-->\
                                            <!--JCrop-->\
                                            <audio id="PureComponentsFlashAudioPlayerAHTML5"></audio>';
                    document.body.insertBefore(players, document.body.firstChild);
                    pure.components.audioplayer.A.Build.initialized = true;
                }
            }
        },
        More : {
            initialized : false,
            init        : function(){
                if (pure.components.audioplayer.A.More.initialized === false){
                    pure.appevents.Actions.listen('pure.more',          'done', function(){ pure.components.audioplayer.A.init(); }, 'pure.components.audioplayer.A.init');
                    pure.appevents.Actions.listen('pure.positioning',   'new',  function(){ pure.components.audioplayer.A.init(); }, 'pure.components.audioplayer.A.init');
                    pure.components.audioplayer.A.More.initialized = true;
                }
            }
        }
    };
    pure.system.start.add(pure.components.audioplayer.A.init);
}());