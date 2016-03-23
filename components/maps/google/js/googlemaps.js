(function () {
    if (typeof window.pure                  !== "object") { window.pure                 = {}; }
    if (typeof window.pure.components       !== "object") { window.pure.components      = {}; }
    if (typeof window.pure.components.maps  !== "object") { window.pure.components.maps = {}; }
    "use strict";
    window.pure.components.maps.google = {
        maps        : {
            storage         : {},
            add             : function(id, map){
                var storage = pure.components.maps.google.maps.storage;
                if (typeof storage[id] === 'undefined'){
                    storage[id] = map;
                }
            },
            get             : function(id){
                var storage = pure.components.maps.google.maps.storage;
                return (typeof storage[id] !== 'undefined' ? storage[id] : null);
            },
            addMarker       : function(id, marker){
                var storage = pure.components.maps.google.maps.storage;
                if (typeof storage[id] !== 'undefined'){
                    storage[id].markers = (typeof storage[id].markers === 'undefined' ? [] : storage[id].markers);
                    storage[id].markers.push(marker);
                }
            },
            clearMarkers    : function(id){
                var storage = pure.components.maps.google.maps.storage;
                if (typeof storage[id] !== 'undefined'){
                    if (typeof storage[id].markers !== 'undefined'){
                        for(var index = storage[id].markers.length - 1; index >= 0; index -= 1){
                            storage[id].markers[index].setMap(null);
                        }
                    }
                }
            }
        },
        actions     : {
            search  : function(id, address, callbacks){
                var map         = pure.components.maps.google.maps.get(id),
                    geocoder    = null,
                    callbacks   = (typeof callbacks === 'object' ? callbacks : null);;
                if (map !== null){
                    geocoder = new google.maps.Geocoder();
                    geocoder.geocode( { 'address': address}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var marker = null;
                            pure.components.maps.google.maps.clearMarkers(id);
                            map.setCenter(results[0].geometry.location);
                            marker = new google.maps.Marker({
                                map     : map,
                                position: results[0].geometry.location
                            });
                            pure.components.maps.google.maps.addMarker(id, marker);
                            if (callbacks !== null){
                                if (typeof callbacks.success === 'function'){
                                    pure.system.runHandle(
                                        callbacks.success,
                                        status,
                                        'pure.components.maps.google.actions.search',
                                        this
                                    );
                                }
                            }
                        }else{
                            if (typeof callbacks.fail === 'function'){
                                pure.system.runHandle(
                                    callbacks.fail,
                                    status,
                                    'pure.components.maps.google.actions.search',
                                    this
                                );
                            }
                        }
                    });
                }
            }
        },
        containers  : {
            init : function(){
                var maps = pure.nodes.select.all('*[data-google-maps-engine-element="map"]:not([data-engine-element-inited])');
                if (maps !== null){
                    for (var index = maps.length - 1; index >= 0; index -= 1){
                        (function(container){
                            var attributes  = {
                                    id  : container.getAttribute('data-google-maps-engine-id'),
                                    lat : container.getAttribute('data-google-maps-engine-lat'),
                                    lng : container.getAttribute('data-google-maps-engine-lng'),
                                    adr : container.getAttribute('data-google-maps-engine-address')
                                },
                                mapOptions  = null,
                                map         = null;
                            if (pure.tools.objects.isValueIn(attributes, null) === false){
                                mapOptions = {
                                    zoom    : (attributes.adr !== '' ? 13 : 8),
                                    center  : new google.maps.LatLng(
                                        parseFloat(attributes.lat),
                                        parseFloat(attributes.lng)
                                    )
                                };
                                map = new google.maps.Map(container, mapOptions);
                                pure.components.maps.google.maps.add(attributes.id, map);
                                if (attributes.adr !== ''){
                                    pure.components.maps.google.search(attributes.id, attributes.adr);
                                }
                            }
                            container.setAttribute('data-engine-element-inited', 'true');
                        }(maps[index]));
                    }
                }
            }
        },
        search  : function(id, address, callbacks){
            var callbacks = (typeof callbacks === 'object' ? callbacks : null);
            pure.components.maps.google.actions.search(id, address, callbacks);
        },
        init    : function(){
            if (pure.system.getInstanceByPath('google.maps.Map') === null){
                setTimeout(pure.components.maps.google.init, 100);
            }else{
                pure.components.maps.google.containers.init();
            }
        }
    };
    //This module is called by google.map.init as callback
}());