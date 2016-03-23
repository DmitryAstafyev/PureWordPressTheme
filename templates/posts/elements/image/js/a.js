(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.posts                    !== "object") { window.pure.posts                   = {}; }
    if (typeof window.pure.posts.elements           !== "object") { window.pure.posts.elements          = {}; }
    if (typeof window.pure.posts.elements.image     !== "object") { window.pure.posts.elements.image    = {}; }
    "use strict";
    window.pure.posts.elements.image.A = {
        gallery     : {
            data    : {},
            add     : function(postSRC, fullSRC, postID){
                var storage = pure.posts.elements.image.A.gallery.data;
                storage[postID] = (typeof storage[postID] !== 'undefined' ? storage[postID] : {images : [], current: 0});
                storage[postID].images.push({
                    postSRC : postSRC,
                    fullSRC : fullSRC
                });
            },
            get     : function(postID){
                var storage = pure.posts.elements.image.A.gallery.data;
                return (typeof storage[postID] !== 'undefined' ? storage[postID] : null);
            }
        },
        instances   : {
            init : function(){
                var instances   = pure.nodes.select.all('*[data-engine-image-element="Image.A.Button.Full"]:not([data-element-image-inited])'),
                    postID      = null,
                    fullSRC     = null,
                    postSRC     = null;
                if (instances !== null){
                    for(var index = 0, max_index = instances.length; index < max_index; index += 1){
                        postID  = instances[index].getAttribute('data-engine-image-postID'  );
                        fullSRC = instances[index].getAttribute('data-engine-image-fullSRC' );
                        postSRC = instances[index].getAttribute('data-engine-image-postSRC' );
                        if (postID !== null && postID !== '' && fullSRC !== null && fullSRC !== '' && postSRC !== null && postSRC !== ''){
                            (function(instance, postID, postSRC, fullSRC){
                                pure.posts.elements.image.A.gallery.add(postSRC, fullSRC, postID);
                                pure.events.add(
                                    instance,
                                    'click',
                                    function(){
                                        pure.posts.elements.image.A.actions.show(postSRC, fullSRC, postID);
                                    }
                                );
                                instance.setAttribute('data-element-image-inited', 'true');
                            }(instances[index], postID, postSRC, fullSRC));
                        }
                    }
                }
            }
        },
        templates   : {
            data    : null,
            init    : function(){
                var template    = pure.nodes.select.first('*[data-engine-image-element="Image.A.FullView"]'),
                    data        = null;
                if (template !== null && pure.posts.elements.image.A.templates.data === null){
                    data = {
                        innerHTML   : template.innerHTML,
                        attributes  : pure.nodes.attributes.get(template, ['data-engine-image-element', 'style']),
                        nodeName    : template.nodeName
                    };
                    template.parentNode.removeChild(template);
                    pure.posts.elements.image.A.templates.data = data;
                }
                return null;
            },
            render : function(postSRC, fullSRC, postID){
                var template    = pure.posts.elements.image.A.templates.data,
                    storage     = pure.posts.elements.image.A.gallery.get(postID),
                    fullview    = null,
                    nodes       = null;
                if (template !== null && storage !== null){
                    fullview            = document.createElement(template.nodeName);
                    fullview.innerHTML  = template.innerHTML;
                    pure.nodes.attributes.set(fullview, template.attributes);
                    fullview.setAttribute('data-engine-image-id', postID);
                    document.body.appendChild(fullview);
                    nodes = {
                        img         : pure.nodes.select.first(template.nodeName + '[data-engine-image-id="' + postID + '"] *[data-engine-image-element="Image.A.FullView.Image"]'      ),
                        original    : pure.nodes.select.first(template.nodeName + '[data-engine-image-id="' + postID + '"] *[data-engine-image-element="Image.A.Original.Image"]'      ),
                        close       : pure.nodes.select.first(template.nodeName + '[data-engine-image-id="' + postID + '"] *[data-engine-image-element="Image.A.FullView.Close"]'      ),
                        next        : pure.nodes.select.first(template.nodeName + '[data-engine-image-id="' + postID + '"] *[data-engine-image-element="Image.A.FullView.Next"]'       ),
                        previous    : pure.nodes.select.first(template.nodeName + '[data-engine-image-id="' + postID + '"] *[data-engine-image-element="Image.A.FullView.Previous"]'   ),
                        position    : pure.nodes.select.first(template.nodeName + '[data-engine-image-id="' + postID + '"] *[data-engine-image-element="Image.A.FullView.Position"]'   ),
                        thumbnail   : pure.nodes.select.first(template.nodeName + '[data-engine-image-id="' + postID + '"] *[data-engine-image-element="Image.A.FullView.Thumbnail"]'  )
                    };
                    if (nodes.img !== null && nodes.close !== null && nodes.thumbnail !== null && nodes.original !== null){
                        pure.events.add(
                            nodes.close,
                            'click',
                            function(){
                                fullview.parentNode.removeChild(fullview);
                            }
                        );
                        for(var index = 0, max_index = storage.images.length; index < max_index; index += 1){
                            (function(template, storage, index, parent, img, position, original){
                                var thumbnail = template.cloneNode(true);
                                thumbnail.style.backgroundImage = 'url(' + storage.images[index].postSRC + ')';
                                parent.appendChild(thumbnail);
                                pure.events.add(
                                    thumbnail,
                                    'click',
                                    function(){
                                        storage.current = index;
                                        pure.posts.elements.image.A.templates.update(storage, img, position, original);
                                    }
                                );
                            }(nodes.thumbnail, storage, index, nodes.thumbnail.parentNode, nodes.img, nodes.position, nodes.original));
                        }
                        for(var index = 0, max_index = storage.images.length; index < max_index; index += 1){
                            if (storage.images[index].postSRC === postSRC){
                                storage.current = index;
                                break;
                            }
                        }
                        nodes.thumbnail.parentNode.removeChild(nodes.thumbnail);
                        pure.posts.elements.image.A.templates.update(storage, nodes.img, nodes.position, nodes.original);
                        if (nodes.next !== null){
                            pure.events.add(
                                nodes.next,
                                'click',
                                function(){
                                    pure.posts.elements.image.A.actions.next(storage, nodes.img, nodes.position, nodes.original);
                                }
                            );
                        }
                        if (nodes.previous !== null){
                            pure.events.add(
                                nodes.previous,
                                'click',
                                function(){
                                    pure.posts.elements.image.A.actions.previous(storage, nodes.img, nodes.position, nodes.original);
                                }
                            );
                        }
                    }else{
                        fullview.parentNode.removeChild(fullview);
                    }
                }
            },
            update : function(galleryData, img, position, original){
                img.style.backgroundImage   = 'url(' + galleryData.images[galleryData.current].fullSRC + ')';
                original.src                = galleryData.images[galleryData.current].fullSRC;
                if (position !== null){
                    position.innerHTML = (galleryData.current + 1) + ' / ' + galleryData.images.length;
                }
            }
        },
        actions     : {
            show        : function(postSRC, fullSRC, postID){
                pure.posts.elements.image.A.templates.render(postSRC, fullSRC, postID);
            },
            next        : function(galleryData, img, position, original){
                galleryData.current = (galleryData.current < galleryData.images.length - 1 ? galleryData.current + 1 : 0);
                pure.posts.elements.image.A.templates.update(galleryData, img, position, original);
            },
            previous    : function(galleryData, img, position, original){
                galleryData.current = (galleryData.current > 0 ? galleryData.current - 1 : galleryData.images.length - 1);
                pure.posts.elements.image.A.templates.update(galleryData, img, position, original);
            }
        },
        init        : function(){
            pure.posts.elements.image.A.instances.init();
            pure.posts.elements.image.A.templates.init();
        }
    };
    pure.system.start.add(pure.posts.elements.image.A.init);
}());