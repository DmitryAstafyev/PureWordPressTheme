(function () {
    if (typeof window.pure                      !== "object") { window.pure                     = {}; }
    if (typeof window.pure.templates            !== "object") { window.pure.templates           = {}; }
    if (typeof window.pure.templates.counter    !== "object") { window.pure.templates.counter   = {}; }
    "use strict";
    window.pure.templates.counter.A = {
        defaultTotal    : 2000,
        init            : function () {
            var instances = pure.nodes.select.all('*[data-engine-counter="counter"]');
            if (instances !== null) {
                Array.prototype.forEach.call(
                    instances,
                    function (item, index, source) {
                        var value       = parseInt(item.innerHTML, 10),
                            total       = item.getAttribute('data-engine-counter-totaltime'),
                            progress    = {
                                node    : item.getAttribute('data-engine-counter-progress-node'),
                                css     : item.getAttribute('data-engine-counter-progress-class')
                            };
                        if (progress.node !== '' && progress.css !== ''){
                            progress.node = pure.nodes.select.first(progress.node.replace(/\|/gi, '"'));
                            if (progress.node === null){
                                progress = null;
                            }else{
                                progress.node.className = progress.css;
                            }
                        }
                        value               = (typeof value === 'number' ? value : 0);
                        total               = (total !== null ? parseInt(total, 10) : pure.templates.counter.A.defaultTotal);
                        total               = (typeof total === 'number' ? total : pure.templates.counter.A.defaultTotal);
                        item.style.opacity  = 1;
                        pure.templates.counter.A.handle(item, 0, value, Math.round(total / value), progress);
                    }
                );
            }
        },
        handle          : function (node, current, max, speed, progress) {
            if (node !== null) {
                if (typeof node.innerHTML !== 'undefined') {
                    node.innerHTML = current;
                    if (current < max) {
                        setTimeout(
                            function () {
                                pure.templates.counter.A.handle(node, current + 1, max, speed, progress);
                            },
                            speed
                        );
                    }else{
                        if (progress !== null){
                            progress.node.className = '';
                        }
                    }
                }
            }
        }
    };
    pure.system.start.add(pure.templates.counter.A.init);
}());