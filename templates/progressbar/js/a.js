(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.templates                !== "object") { window.pure.templates               = {}; }
    if (typeof window.pure.templates.progressbar    !== "object") { window.pure.templates.progressbar   = {}; }
    "use strict";
    window.pure.templates.progressbar.A = {
        show : function(injectionNode, containerStyleString, particleStyleString, labelStyleString, label){
            var progress                = document.createElement("DIV"),
                containerStyleString    = (typeof containerStyleString  === 'string' ? containerStyleString : ''),
                particleStyleString     = (typeof particleStyleString   === 'string' ? particleStyleString  : ''),
                labelStyleString        = (typeof labelStyleString      === 'string' ? labelStyleString     : ''),
                label                   = (typeof label                 === 'string' ? label                : '');
            if (progress !== null){
                progress.innerHTML =    '<!--BEGIN: Progress bar -->\
                                        <div data-element-type="ProgressBar.A.Container" style="' + containerStyleString + '">\
                                            <p data-element-type="ProgressBar.A.Label" ' + (labelStyleString !== '' ? 'style="' + labelStyleString + '"' : '') + '>' + label + '</p>\
                                            <div data-element-type="ProgressBar.A.Particle" data-addition-type="0" ' + (particleStyleString !== '' ? 'style="' + particleStyleString + '"' : '') + '></div>\
                                            <div data-element-type="ProgressBar.A.Particle" data-addition-type="1" ' + (particleStyleString !== '' ? 'style="' + particleStyleString + '"' : '') + '></div>\
                                            <div data-element-type="ProgressBar.A.Particle" data-addition-type="2" ' + (particleStyleString !== '' ? 'style="' + particleStyleString + '"' : '') + '></div>\
                                            <div data-element-type="ProgressBar.A.Particle" data-addition-type="3" ' + (particleStyleString !== '' ? 'style="' + particleStyleString + '"' : '') + '></div>\
                                            <div data-element-type="ProgressBar.A.Particle" data-addition-type="4" ' + (particleStyleString !== '' ? 'style="' + particleStyleString + '"' : '') + '></div>\
                                            <div data-element-type="ProgressBar.A.Particle" data-addition-type="5" ' + (particleStyleString !== '' ? 'style="' + particleStyleString + '"' : '') + '></div>\
                                        </div>\
                                        <!--END: Progress bar -->';
                injectionNode.appendChild(progress);
                return progress;
            }
            return null;
        },
        hide : function(progress){
            if (typeof progress.parentNode !== 'undefined'){
                if (progress.parentNode !== null){
                    if (typeof progress.parentNode.removeChild === 'function'){
                        progress.parentNode.removeChild(progress);
                    }
                }
            }
        },
        wrapper : {
            data        : {},
            isBusy      : function(id){
                var data = pure.templates.progressbar.A.wrapper.data;
                return (typeof data[id] !== 'undefined' ? true : false);
            },
            //node = NODE || SELECTOR
            busy        : function(id, node, containerStyleString, particleStyleString, labelStyleString, label){
                var data                    = pure.templates.progressbar.A.wrapper.data,
                    node                    = (typeof node === 'string' ? pure.nodes.select.first(node) : node),
                    containerStyleString    = (typeof containerStyleString  === 'string' ? containerStyleString : ''),
                    particleStyleString     = (typeof particleStyleString   === 'string' ? particleStyleString  : ''),
                    labelStyleString        = (typeof labelStyleString      === 'string' ? labelStyleString     : ''),
                    label                   = (typeof label                 === 'string' ? label                : '');
                if (typeof data[id] === 'undefined' && node !== null){
                    data[id] = pure.templates.progressbar.A.show(node, containerStyleString, particleStyleString, labelStyleString, label);
                }
            },
            clear       : function(id){
                var data = pure.templates.progressbar.A.wrapper.data;
                if (typeof data[id] !== 'undefined'){
                    pure.templates.progressbar.A.hide(data[id]);
                    data[id] = null;
                    delete data[id];
                }
            }
        }
    };
}());