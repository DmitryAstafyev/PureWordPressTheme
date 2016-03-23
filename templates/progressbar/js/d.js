(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.templates                !== "object") { window.pure.templates               = {}; }
    if (typeof window.pure.templates.progressbar    !== "object") { window.pure.templates.progressbar   = {}; }
    "use strict";
    window.pure.templates.progressbar.D = {
        show : function(nodeBefore){
            var progress = document.createElement("DIV");
            if (progress !== null){
                progress.innerHTML =    '<!--BEGIN: Progress bar -->\
                                            <div data-element-type="ProgressBar.D.Reset.Float"></div>\
                                            <div data-element-type="ProgressBar.D.Container">\
                                                <div data-element-type="ProgressBar.D.Particle" data-addition-type="0"></div>\
                                                <div data-element-type="ProgressBar.D.Particle" data-addition-type="1"></div>\
                                                <div data-element-type="ProgressBar.D.Particle" data-addition-type="2"></div>\
                                                <div data-element-type="ProgressBar.D.Particle" data-addition-type="3"></div>\
                                                <div data-element-type="ProgressBar.D.Particle" data-addition-type="4"></div>\
                                                <div data-element-type="ProgressBar.D.Particle" data-addition-type="5"></div>\
                                            </div>\
                                            <!--END: Progress bar -->';
                nodeBefore.parentNode.insertBefore(progress, nodeBefore);
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
        }
    };
}());