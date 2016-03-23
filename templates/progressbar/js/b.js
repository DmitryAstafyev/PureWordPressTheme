(function () {
    if (typeof window.pure                          !== "object") { window.pure                         = {}; }
    if (typeof window.pure.templates                !== "object") { window.pure.templates               = {}; }
    if (typeof window.pure.templates.progressbar    !== "object") { window.pure.templates.progressbar   = {}; }
    "use strict";
    window.pure.templates.progressbar.B = {
        show            : function(injectionNode){
            var progress = document.createElement("DIV");
            if (progress !== null){
                progress.innerHTML =    '<!--BEGIN: Progress bar -->\
                                        <div align="left" data-element-type="ProgressBar.B.Container">\
                                            <div data-element-type="ProgressBar.B.Particle" data-addition-type="0"></div>\
                                            <div data-element-type="ProgressBar.B.Particle" data-addition-type="1"></div>\
                                            <div data-element-type="ProgressBar.B.Particle" data-addition-type="2"></div>\
                                            <div data-element-type="ProgressBar.B.Particle" data-addition-type="3"></div>\
                                            <div data-element-type="ProgressBar.B.Particle" data-addition-type="4"></div>\
                                            <div data-element-type="ProgressBar.B.Particle" data-addition-type="5"></div>\
                                        </div>\
                                        <!--END: Progress bar -->';
                injectionNode.appendChild(progress);
                return progress;
            }
            return null;
        },
        hide            : function(progress){
            progress.parentNode.removeChild(progress);
        },
        hideByParent    : function(parent){
            var progress = pure.nodes.find.childByAttr(parent, 'div', {name :'data-element-type', value:'ProgressBar.B.Container'});
            if (progress !== null){
                if (typeof progress.parentNode !== 'undefined'){
                    if (progress.parentNode !== null){
                        if (typeof progress.parentNode.removeChild === 'function'){
                            progress.parentNode.removeChild(progress);
                        }
                    }
                }
            }
        }
    };
}());