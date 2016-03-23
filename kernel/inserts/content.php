<?php
namespace Pure\Inserts\Content {
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    \Pure\Components\Tools\DebugMarks\Marks::instance()->open('content.php');
    if (is_front_page() === true) {
        require_once('content.frontpage.php');
    } else if(\Pure\Configuration::instance()->globals->requests->BYSCHEME !== false){
        require_once('content.byscheme.php');
    } else {
        $TemplateLayout = \Pure\Templates\Layout\Page\Container\Initialization::instance()->get('A');
        $TemplateLayout->before_content();
        \Pure\Components\BuddyPress\Location\Initialization::instance()->attach();
        switch (\Pure\Configuration::instance()->globals->requests->type) {
            case 'BUDDY':
                \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
                $PersonalSettings   = new \Pure\Components\BuddyPress\PersonalSettings\User();
                $ErrorMessage       = \Pure\Templates\Pages\ErrorMessage\Initialization::instance()->get('B');
                switch (\Pure\Configuration::instance()->globals->requests->BUDDY) {
                    case 'member::activities':
                        $available = $PersonalSettings->availableForCurrentUser(\Pure\Configuration::instance()->globals->IDs->user_id);
                        if ($available !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/BuddyPress/member.activities.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission to see this page.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'member::profile':
                        $available = $PersonalSettings->availableForCurrentUser(\Pure\Configuration::instance()->globals->IDs->user_id);
                        if ($available !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/BuddyPress/member.profile.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission to see this page.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'member::groups':
                        $available = $PersonalSettings->availableForCurrentUser(\Pure\Configuration::instance()->globals->IDs->user_id);
                        if ($available !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/BuddyPress/member.groups.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission to see this page.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'member::friends':
                        $available = $PersonalSettings->availableForCurrentUser(\Pure\Configuration::instance()->globals->IDs->user_id);
                        if ($available !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/BuddyPress/member.friends.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission to see this page.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'groups::group':
                        require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/BuddyPress/groups.group.php'));
                        break;
                    case 'members':
                        require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/BuddyPress/members.php'));
                        break;
                    case 'groups':
                        require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/BuddyPress/groups.php'));
                        break;
                }
                $PersonalSettings = NULL;
                break;
            case 'SPECIAL':
                \Pure\Components\WordPress\Post\Visibility\Initialization::instance()->attach(true);
                $Available      = new \Pure\Components\WordPress\Post\Visibility\Availability();
                $ErrorMessage   = \Pure\Templates\Pages\ErrorMessage\Initialization::instance()->get('B');
                switch (\Pure\Configuration::instance()->globals->requests->SPECIAL->request) {
                    case 'CREATEPOST':
                        if ($Available->createPost() !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/post.edit.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission for creating a post.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'CREATEEVENT':
                        if ($Available->createPost() !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/event.edit.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission for creating an event.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'CREATEREPORT':
                        if ($Available->createPost() !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/report.edit.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission for creating an report.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'CREATEQUESTION':
                        if ($Available->createPost() !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/question.edit.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission for creating an question.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'EDITPOST':
                        if ($Available->editPost(\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->post_id) !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/post.edit.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission for editing an post.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'EDITEVENT':
                        if ($Available->editPost(\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->post_id) !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/event.edit.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission for editing an event.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'EDITREPORT':
                        if ($Available->editPost(\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->post_id) !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/report.edit.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission for editing an report.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'EDITQUESTION':
                        if ($Available->editPost(\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->post_id) !== false) {
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/question.edit.php'));
                        } else {
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('You have not permission for editing an question.', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'TOP':
                        if (in_array(\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->type, array('post', 'event', 'report', 'question')) !== false){
                            switch(\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->type){
                                case 'post':
                                    require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/post.top.posts.php'));
                                    break;
                                case 'event':
                                    require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/post.top.events.php'));
                                    break;
                                case 'report':
                                    require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/post.top.reports.php'));
                                    break;
                                case 'question':
                                    require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/post.top.questions.php'));
                                    break;
                            }
                        }else{
                            $ErrorMessage->innerHTML(
                                __('No access', 'pure'),
                                __('Incorrect type of page was defined in url', 'pure'),
                                true
                            );
                        }
                        break;
                    case 'STREAM':
                        require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/author.stream.php'));
                        break;
                    case 'SEARCH':
                        require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/search.php'));
                        break;
                    case 'ASEARCH':
                        require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/asearch.results.php'));
                        break;
                    case 'DRAFTS':
                        require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/author.drafts.php'));
                        break;
                    case 'GROUPCONTENT':
                        require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/Special/group.content.php'));
                        break;
                }
                $Available = NULL;
                break;
            case 'POST':
                \Pure\Components\WordPress\Post\Visibility\Initialization::instance()->attach(true);
                $Available      = new \Pure\Components\WordPress\Post\Visibility\Availability();
                $ErrorMessage   = \Pure\Templates\Pages\ErrorMessage\Initialization::instance()->get('A');
                if ($Available->viewPost(\Pure\Configuration::instance()->globals->requests->POST->ID) !== false) {
                    \Pure\Components\WordPress\Post\ViewsCounter\Initialization::instance()->attach();
                    $ViewsCounter = new \Pure\Components\WordPress\Post\ViewsCounter\Counter();
                    $ViewsCounter->set(\Pure\Configuration::instance()->globals->requests->POST->ID);
                    $ViewsCounter = NULL;
                    switch (\Pure\Configuration::instance()->globals->requests->POST->post_type) {
                        case 'post':
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/WordPress/post.render.php'));
                            break;
                        case 'event':
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/WordPress/event.render.php'));
                            break;
                        case 'report':
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/WordPress/report.render.php'));
                            break;
                        case 'question':
                            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/WordPress/question.render.php'));
                            break;
                    }
                } else {
                    $ErrorMessage->innerHTML(
                        __('No access', 'pure'),
                        __('You have not permission for reading this post / event.', 'pure'),
                        true
                    );
                }
                $Available = NULL;
                break;
            case 'PAGE':
                \Pure\Components\WordPress\Post\ViewsCounter\Initialization::instance()->attach();
                $ViewsCounter = new \Pure\Components\WordPress\Post\ViewsCounter\Counter();
                $ViewsCounter->set(\Pure\Configuration::instance()->globals->requests->PAGE->ID);
                $ViewsCounter = NULL;
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/WordPress/page.render.php'));
                break;
            case 'AUTHOR':
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/WordPress/author.content.php'));
                break;
            case 'CATEGORY':
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/WordPress/category.content.php'));
                break;
            case 'TAG':
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/WordPress/tag.content.php'));
                break;
            case 'SEARCH':
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts . '/WordPress/search.results.php'));
                break;
        }
        $TemplateLayout->after_content();
        $TemplateLayout = NULL;
    }
    \Pure\Components\Tools\DebugMarks\Marks::instance()->close('content.php');
}
?>