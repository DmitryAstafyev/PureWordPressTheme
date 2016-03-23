<?php
namespace Pure\Inserts\Special\ASearchResults {
    function getTermsIDs($terms, $taxonomy){
        $IDs = array();
        foreach($terms as $term){
            $term_obj = get_term_by('name', $term, $taxonomy);
            if ($term_obj !== false){
                $IDs[] = $term_obj->term_id;
            }
        }
        return $IDs;
    };
    function getIDs(){
        $categories = explode(',',\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->categories   );
        $tags       = explode(',',\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->tags         );
        $categories = getTermsIDs($categories,  'category');
        $tags       = getTermsIDs($tags,        'post_tag');
        $Posts      = \Pure\Providers\Posts\Initialization::instance()->getCommon();
        $IDs        = $Posts->get_posts_IDs_by_category_tag($categories, $tags);
        $Posts      = NULL;
        return $IDs;
    };
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('search.results.php');
//Get data about member
    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
    $BuddyPressSettings = \Pure\Components\WordPress\Settings\Instance::instance()->settings->buddypress->properties;
//Get IDs of posts
//Layout of page
    $layoutClass = \Pure\Templates\Layout\WordPress\SearchResults\Initialization::instance()->get($BuddyPressSettings->header_template->value);
    echo $layoutClass->get(getIDs());
    $layoutClass = NULL;
    $BuddyPressSettings = NULL;
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('search.results.php');
}
?>