    </div>
    <!--END:: Global.Content.Wrapper-->
<?php
    if (is_front_page() === true) {
        //All magic is going in template
    } else if(\Pure\Configuration::instance()->globals->requests->BYSCHEME !== false){
        //All magic is going in template
    } else {
        $isErrorMessage = (isset(\Pure\Configuration::instance()->globals->ErrorMessage) === false ? false : \Pure\Configuration::instance()->globals->ErrorMessage);
        $TemplateLayout = \Pure\Templates\Layout\Page\Container\Initialization::instance()->get('A');
        $TemplateLayout->before_footer();
        if ($isErrorMessage === false){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/footer.sidebar.php'));
        }
        $TemplateLayout->after_footer();
        $TemplateLayout->before_sidebar();
        if ($isErrorMessage === false){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/content.sidebar.php'));
        }
        $TemplateLayout->after_sidebar();
        $TemplateLayout = NULL;
    }
?>