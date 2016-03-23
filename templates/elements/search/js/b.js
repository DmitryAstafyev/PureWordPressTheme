(function () {
    if (typeof window.pure              !== "object") { window.pure             = {}; }
    if (typeof window.pure.templates    !== "object") { window.pure.templates   = {}; }
    "use strict";
    window.pure.templates.advancedsearch = {
        init    : function(){
            var instances = pure.nodes.select.all('*[data-engine-advanced-search-action]:not([data-element-inited])');
            if (instances !== null) {
                Array.prototype.forEach.call(
                    instances,
                    function (instance, _index, source) {
                        var id      = instance.getAttribute('data-engine-advanced-search-action'),
                            form    = pure.nodes.select.first('form[id="' + id + '"]');
                        if (form !== null){
                            pure.events.add(
                                instance,
                                'click',
                                function(event){
                                    pure.templates.advancedsearch.submit(id, form);
                                }
                            );
                        }
                        instance.setAttribute('data-element-inited', 'true');
                    }
                );
            }
        },
        submit  : function(id, form){
            function getTermsString(inputs){
                var data = [];
                Array.prototype.forEach.call(
                    inputs,
                    function (input, _index, source) {
                        data.push(input.value);
                    }
                );
                return data.join(',');
            };
            var categories  = pure.nodes.select.all('input[data-engine-advanced-search-category="' + id + '"]'),
                tags        = pure.nodes.select.all('input[data-engine-advanced-search-tag="' + id + '"]');
            if (categories !== null && tags !== null){
                if (categories.length === 0){
                    pure.templates.advancedsearch.message('Ooops', 'You should define at least one category.');
                    return false;
                }
                if (tags.length === 0){
                    pure.templates.advancedsearch.message('Ooops', 'You should define at least one tag.');
                    return false;
                }
                form.action = form.action.replace('[categories]', getTermsString(categories   ));
                form.action = form.action.replace('[tags]',       getTermsString(tags         ));
                window.location.href = form.action;
            }
        },
        message : function (title, message) {
            pure.components.dialogs.B.open({
                title       : title,
                innerHTML   : '<p>' + message + '</p>',
                width       : 70,
                parent      : document.body,
                buttons     : [
                    {
                        title       : 'OK',
                        handle      : null,
                        closeAfter  : true
                    }
                ]
            });
        }
    };
    pure.system.start.add(pure.templates.advancedsearch.init);
}());