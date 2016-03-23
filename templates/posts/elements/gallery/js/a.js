(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.posts                    !== "object") { window.pure.posts                   = {}; }
    if (typeof window.pure.posts.elements           !== "object") { window.pure.posts.elements          = {}; }
    if (typeof window.pure.posts.elements.gallery   !== "object") { window.pure.posts.elements.gallery  = {}; }
    "use strict";
    window.pure.posts.elements.gallery.A = {
        instances   : {
            init : function(){
                var instances   = pure.nodes.select.all('*[data-engine-gallery-element="Gallery.A.Button.Full"]:not([data-element-inited])'),
                    id          = null,
                    parent      = null,
                    sliderID    = null,
                    gallery     = null,
                    template    = null;
                if (instances !== null){
                    for(var index = instances.length - 1; index >= 0; index -= 1){
                        id = instances[index].getAttribute('data-engine-gallery-id');
                        if (id !== null && id !== ''){
                            gallery = pure.system.getInstanceByPath('pure.posts.elements.gallery.data.' + id);
                            if (gallery !== null){
                                instances[index].setAttribute('data-element-inited', 'true');
                                gallery = {
                                    src     : gallery.split(','),
                                    current : 0
                                };
                                parent  = pure.nodes.find.parentByAttr(instances[index], {name : 'data-engine-element', value: 'parent'});
                                if (parent !== null){
                                    sliderID = pure.sliders.B.getID(parent);
                                    if (sliderID !== null){
                                        pure.posts.elements.gallery.A.resize.add(sliderID, parent, parseFloat(parent.getAttribute('data-engine-gallery-rate')));
                                        template = pure.posts.elements.gallery.A.templates.get(id);
                                        if (template !== null){
                                            (function(instance, galleryData, sliderID, galleryID, template){
                                                pure.events.add(
                                                    instance,
                                                    'click',
                                                    function(){
                                                        pure.posts.elements.gallery.A.actions.show(galleryData, sliderID, galleryID, template);
                                                    }
                                                );
                                            }(instances[index], gallery, sliderID, id, template));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        },
        templates   : {
            data    : null,
            get     : function(galleryID){
                var template    = pure.nodes.select.first('*[data-engine-gallery-id="' + galleryID + '"][data-engine-gallery-element="Gallery.A.FullView"]'),
                    data        = null;
                if (template !== null){
                    data = {
                        innerHTML   : template.innerHTML,
                        attributes  : pure.nodes.attributes.get(template, ['data-engine-gallery-element', 'style']),
                        nodeName    : template.nodeName
                    };
                    template.parentNode.removeChild(template);
                    return data;
                }
                return null;
            },
            render : function(galleryData, sliderID, galleryID, template){
                var fullview    = document.createElement(template.nodeName),
                    nodes       = null;
                if (fullview !== null){
                    fullview.innerHTML = template.innerHTML;
                    pure.nodes.attributes.set(fullview, template.attributes);
                    fullview.setAttribute('data-engine-gallery-id', galleryID);
                    document.body.appendChild(fullview);
                    nodes = {
                        img         : pure.nodes.select.first(template.nodeName + '[data-engine-gallery-id="' + galleryID + '"] *[data-engine-gallery-element="Gallery.A.FullView.Image"]'      ),
                        original    : pure.nodes.select.first(template.nodeName + '[data-engine-gallery-id="' + galleryID + '"] *[data-engine-gallery-element="Gallery.A.Original.Image"]'      ),
                        close       : pure.nodes.select.first(template.nodeName + '[data-engine-gallery-id="' + galleryID + '"] *[data-engine-gallery-element="Gallery.A.FullView.Close"]'      ),
                        next        : pure.nodes.select.first(template.nodeName + '[data-engine-gallery-id="' + galleryID + '"] *[data-engine-gallery-element="Gallery.A.FullView.Next"]'       ),
                        previous    : pure.nodes.select.first(template.nodeName + '[data-engine-gallery-id="' + galleryID + '"] *[data-engine-gallery-element="Gallery.A.FullView.Previous"]'   ),
                        position    : pure.nodes.select.first(template.nodeName + '[data-engine-gallery-id="' + galleryID + '"] *[data-engine-gallery-element="Gallery.A.FullView.Position"]'   ),
                        thumbnails  : pure.nodes.select.all  (template.nodeName + '[data-engine-gallery-id="' + galleryID + '"] *[data-engine-gallery-element="Gallery.A.FullView.Thumbnail"]'  )
                    };
                    if (nodes.img !== null && nodes.close !== null && nodes.original !== null){
                        pure.events.add(
                            nodes.close,
                            'click',
                            function(){
                                fullview.parentNode.removeChild(fullview);
                            }
                        );
                        galleryData.current = pure.sliders.B.getCurrentPosition(sliderID);
                        if (galleryData.current !== null){
                            pure.posts.elements.gallery.A.templates.update(galleryData, nodes.img, nodes.position, nodes.original);
                            if (nodes.next !== null){
                                pure.events.add(
                                    nodes.next,
                                    'click',
                                    function(){
                                        pure.posts.elements.gallery.A.actions.next(galleryData, nodes.img, nodes.position, nodes.original);
                                    }
                                );
                            }
                            if (nodes.previous !== null){
                                pure.events.add(
                                    nodes.previous,
                                    'click',
                                    function(){
                                        pure.posts.elements.gallery.A.actions.previous(galleryData, nodes.img, nodes.position, nodes.original);
                                    }
                                );
                            }
                            if (nodes.thumbnails !== null){
                                for(var index = nodes.thumbnails.length - 1; index >= 0; index -= 1){
                                    (function(thumbnail, index, galleryData, img, position, original){
                                        pure.events.add(
                                            thumbnail,
                                            'click',
                                            function(){
                                                galleryData.current = index;
                                                pure.posts.elements.gallery.A.templates.update(galleryData, img, position, original);
                                            }
                                        );
                                    }(nodes.thumbnails[index], index, galleryData, nodes.img, nodes.position, nodes.original));
                                }
                            }
                        }
                    }else{
                        fullview.parentNode.removeChild(fullview);
                    }
                }
            },
            update : function(galleryData, img, position, original){
                img.style.backgroundImage   = 'url('+galleryData.src[galleryData.current]+')';
                original.src                = galleryData.src[galleryData.current];
                if (position !== null){
                    position.innerHTML = (galleryData.current + 1) + ' / ' + galleryData.src.length;
                }
            }
        },
        actions     : {
            show        : function(galleryData, sliderID, galleryID, template){
                pure.posts.elements.gallery.A.templates.render(galleryData, sliderID, galleryID, template);
            },
            next        : function(galleryData, img, position, original){
                galleryData.current = (galleryData.current < galleryData.src.length - 1 ? galleryData.current + 1 : 0);
                pure.posts.elements.gallery.A.templates.update(galleryData, img, position, original);
            },
            previous    : function(galleryData, img, position, original){
                galleryData.current = (galleryData.current > 0 ? galleryData.current - 1 : galleryData.src.length - 1);
                pure.posts.elements.gallery.A.templates.update(galleryData, img, position, original);
            }
        },
        resize      : {
            data    : [],
            inited  : false,
            add     : function(sliderID, parent, rate){
                pure.posts.elements.gallery.A.resize.data.push(
                    function(){
                        var size = pure.nodes.render.size(parent);
                        if (size.width !== null && size.width !== 0 && size.height !== null && size.height !== 0){
                            parent.style.maxHeight = size.width / rate;
                            pure.sliders.B.update(sliderID);
                        }
                    }
                );
            },
            init    : function(){
                if (pure.posts.elements.gallery.A.resize.inited === false){
                    pure.events.add(window,"resize", pure.posts.elements.gallery.A.resize.resize);
                    pure.posts.elements.gallery.A.resize.inited = true;
                }
            },
            resize  : function(event){
                var handles = pure.posts.elements.gallery.A.resize.data;
                for(var index = handles.length - 1; index >= 0; index -= 1){
                    pure.system.runHandle(handles[index], null, '', event);
                }
            }
        },
        init        : function(){
            pure.posts.elements.gallery.A.instances.init();
            pure.posts.elements.gallery.A.resize.init();
        }
    };
    pure.system.start.add(pure.posts.elements.gallery.A.init);
}());