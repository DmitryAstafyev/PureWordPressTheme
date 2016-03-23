<?php
$ProcessingAJAXRequest = (isset(\Pure\Configuration::instance()->globals->requests->AJAX) === true ? \Pure\Configuration::instance()->globals->requests->AJAX : false);
if (isset(\Pure\Configuration::instance()->globals->PureComponentsAttacher) === false && $ProcessingAJAXRequest === false){
    \Pure\Configuration::instance()->globals->PureComponentsAttacher = true;
    \Pure\Components\WordPress\Location\Requests\Initialization::instance()->attach();
    $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
    ?>
    <!--BEGIN:: Global settings -->
    <script type="text/javascript">
        (function () {
            if (typeof window.pure                              !== "object") { window.pure                             = {}; }
            if (typeof window.pure.globalsettings               !== "object") { window.pure.globalsettings              = {}; }
            "use strict";
            window.pure.globalsettings = {
                domain          : "<?php echo get_site_url(); ?>",
                requestURL      : "<?php echo $Requests->url; ?>"
            };
        }());
    </script>
    <!--END:: Global settings  -->
<?php
    $Requests = NULL;
}
?>
