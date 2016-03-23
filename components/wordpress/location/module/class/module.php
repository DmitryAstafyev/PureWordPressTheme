<?php
namespace Pure\Components\WordPress\Location\Module{
    class Core{
        private function message404($message){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->basic->properties;
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $ErrorPage  = \Pure\Templates\Pages\Error\Initialization::instance()->get($settings->error_page_template);
            $ErrorPage->message('Oops, 404', $message, true);
            exit;
        }
        public function force404(){
            $this->message404('We are sorry, but this page was not found. Please, check URL and try again.');
            exit;
        }
        private function proceed404(){
            if (is_404() === true){
                //Check for request
                \Pure\Components\WordPress\Location\Requests\Initialization::instance()->attach();
                $Requests = new \Pure\Components\WordPress\Location\Requests\Core();
                if ($Requests->is() === true){
                    \Pure\Configuration::instance()->globals->requests->AJAX = true;
                    \Pure\Configuration::instance()->globals->requests->type = 'AJAX';
                    global $wp_query;
                    status_header(200);
                    $wp_query->is_page  = true;
                    $wp_query->is_404   = false;
                    //echo var_dump($_POST);
                    $Requests->processing();
                    $Requests = NULL;
                    exit;
                }
                $Requests = NULL;
                //Check for special here too
                if ($this->proceedSPECIAL() === false){
                    //Here should be real 404
                    $this->message404('We are sorry, but this page was not found. Please, check URL and try again.');
                    exit;//[!] EXIT SHOULD BE HERE. OR SECURITY TOKENS WILL BE GENERATED TWICE.
                }else{
                    //Prevent 404
                    global $wp_query;
                    status_header(200);
                    $wp_query->is_page  = true;
                    $wp_query->is_404   = false;
                }
            }
            return false;
        }
        private function proceedSPECIAL(){
            \Pure\Components\WordPress\Location\Special\Initialization::instance()->attach();
            $Location = new \Pure\Components\WordPress\Location\Special\Core();
            if ($Location->is() !== false){
                \Pure\Configuration::instance()->globals->requests->SPECIAL = $Location->getRequest();
                \Pure\Configuration::instance()->globals->requests->type    = 'SPECIAL';
                //Grab parameters like IDs
                if (isset(\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters) !== false){
                    foreach(\Pure\Configuration::instance()->globals->IDs as $key=>$value){
                        if (isset(\Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->$key) !== false){
                            \Pure\Configuration::instance()->globals->IDs->$key = \Pure\Configuration::instance()->globals->requests->SPECIAL->parameters->$key;
                        }
                    }
                }
                if (\Pure\Configuration::instance()->globals->requests->SPECIAL->request === 'BYSCHEME'){
                    \Pure\Configuration::instance()->globals->requests->BYSCHEME = true;
                }
                return true;
            }
            $Location = NULL;
            return false;
        }
        public function proceedWP_LOGIN(){
            global $pagenow;
            if( 'wp-login.php' == $pagenow ) {
                header('location:'.site_url());
            }
            return false;
        }
        public function proceedADMIN(){
            if (is_admin() !== false){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current === false){
                    $this->message404('We are sorry, but this page was not found. Please, check URL and try again.');
                    exit;
                }else{
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->basic->properties;
                    $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    if ($settings->console_access === 'no'){
                        if (defined( 'DOING_AJAX' )){
                            if (DOING_AJAX){
                                return false;
                            }
                        }
                        if (current_user_can('administrator') === false){
                            $this->message404('We are sorry, but this page was not found. Please, check URL and try again.');
                            exit;
                        }
                    }
                }
            }
            return false;
        }
        private function proceedBUDDY(){
            \Pure\Components\BuddyPress\Location\Initialization::instance()->attach();
            $BuddyPress             = new \Pure\Components\BuddyPress\Location\Core();
            if ($BuddyPress->is() !== false){
                \Pure\Configuration::instance()->globals->requests->BUDDY   = $BuddyPress->getTypePage();
                \Pure\Configuration::instance()->globals->requests->type    = 'BUDDY';
                if (in_array(\Pure\Configuration::instance()->globals->requests->BUDDY, array(
                        'member::activities', 'member::profile', 'member::friends', 'member::groups'
                    )) !== false){
                    \Pure\Configuration::instance()->globals->IDs->user_id  = (int)$BuddyPress->getID();
                }else if (in_array(\Pure\Configuration::instance()->globals->requests->BUDDY, array(
                        'groups::group'
                    )) !== false){
                    \Pure\Configuration::instance()->globals->IDs->group_id = (int)$BuddyPress->getID();
                }else if (in_array(\Pure\Configuration::instance()->globals->requests->BUDDY, array(
                        'groups', 'members'
                    )) !== false){
                    //Do nothing - no IDs
                }
                $BuddyPress         = NULL;
                return true;
            };
            $BuddyPress             = NULL;
            return false;
        }
        private function proceedArchive(){
            if (is_archive() !== false){
            }
            return false;
        }
        private function proceedPOST(){
            $post = get_post();
            if ($post !== false && is_category() === false && is_tag() === false && is_archive() === false){
                if (is_null($post) === false){
                    if (is_page() === false){
                        \Pure\Configuration::instance()->globals->requests->POST    = $post;
                        \Pure\Configuration::instance()->globals->requests->type    = 'POST';
                        \Pure\Configuration::instance()->globals->IDs->user_id      = (int)$post->post_author;
                        \Pure\Configuration::instance()->globals->IDs->post_id      = (int)$post->ID;
                        return true;
                    }
                }
            }
            return false;
        }
        private function proceedPAGE(){
            $page = get_post();
            if ($page !== false && is_category() === false && is_tag() === false){
                if (is_null($page) === false){
                    if (is_page() !== false){
                        \Pure\Configuration::instance()->globals->requests->PAGE    = $page;
                        \Pure\Configuration::instance()->globals->requests->type    = 'PAGE';
                        \Pure\Configuration::instance()->globals->IDs->user_id      = (int)$page->post_author;
                        \Pure\Configuration::instance()->globals->IDs->post_id      = (int)$page->ID;
                        return true;
                    }
                }
            }
            return false;
        }
        private function proceedAUTHOR(){
            global $wp_query;
            $displayed_author = $wp_query->get_queried_object();
            if ($displayed_author instanceof \WP_User !== false){
                \Pure\Configuration::instance()->globals->requests->AUTHOR  = $displayed_author;
                \Pure\Configuration::instance()->globals->requests->type    = 'AUTHOR';
                \Pure\Configuration::instance()->globals->IDs->user_id      = (int)$displayed_author->ID;
                return true;
            }
            return false;
        }
        private function proceedCATEGORY(){
            if (is_category() !== false) {
                $category   = get_query_var('cat');
                $category   = get_category($category);
                if ($category !== false){
                    \Pure\Configuration::instance()->globals->requests->CATEGORY    = $category;
                    \Pure\Configuration::instance()->globals->requests->type        = 'CATEGORY';
                    \Pure\Configuration::instance()->globals->IDs->category_id      = (int)$category->cat_ID;
                    return true;
                }
            }
            return false;
        }
        private function proceedTAG(){
            if (is_tag() !== false){
                $tag_id = get_query_var('tag_id');
                $tag    = get_terms( 'post_tag', 'include=' . $tag_id );
                if (is_array($tag) !== false){
                    \Pure\Configuration::instance()->globals->requests->TAG     = $tag[0];
                    \Pure\Configuration::instance()->globals->requests->type    = 'TAG';
                    \Pure\Configuration::instance()->globals->IDs->tag_id       = (int)$tag_id;
                    return true;
                }
            }
            return false;
        }
        private function proceedSEARCH(){
            if (is_search() !== false){
                global $query_string;
                $query_args     = explode("&", $query_string);
                $search_query   = array();
                foreach($query_args as $key => $string) {
                    $query_split                    = explode("=", $string);
                    if (isset($query_split[0]) !== false && isset($query_split[1]) !== false){
                        $search_query[$query_split[0]]  = urldecode($query_split[1]);
                    }
                }
                if (count($search_query) > 0){
                    $query = new \WP_Query($search_query);
                    if ($query !== false && is_null($query) === false){
                        if (isset($query->posts) !== false){
                            \Pure\Configuration::instance()->globals->requests->SEARCH  = $query;
                            \Pure\Configuration::instance()->globals->requests->type    = 'SEARCH';
                            return true;
                        }
                    }
                }
                $this->force404();
            }
            return false;
        }
        public function init(){
            \Pure\Configuration::instance()->globals->requests = (object)array(
                'AJAX'      =>false,
                'SPECIAL'   =>false,
                'BYSCHEME'  =>false,//This is sub-type of SPECIAL
                'BUDDY'     =>false,
                'POST'      =>false,
                'PAGE'      =>false,
                'AUTHOR'    =>false,
                'CATEGORY'  =>false,
                'TAG'       =>false,
                'SEARCH'    =>false,
                'type'      =>false
            );
            \Pure\Configuration::instance()->globals->IDs = (object)array(
                'user_id'       =>false,
                'group_id'      =>false,
                'post_id'       =>false,
                'category_id'   =>false,
                'tag_id'        =>false,
            );
        }
        private function registration(){
            $Recorder = new Recorder();
            switch(\Pure\Configuration::instance()->globals->requests->type){
                case 'POST':
                    $Recorder->register(
                        'post',
                        \Pure\Configuration::instance()->globals->requests->POST->ID
                    );
                    break;
                case 'PAGE':
                    $Recorder->register(
                        'page',
                        \Pure\Configuration::instance()->globals->requests->PAGE->ID
                    );
                    break;
                case 'BUDDY':
                    \Pure\Components\BuddyPress\Location\Initialization::instance()->attach(true);
                    $BuddyPress         = new \Pure\Components\BuddyPress\Location\Core();
                    $currentObjectID    = $BuddyPress->getID();
                    $BuddyPress         = NULL;
                    $Recorder->register(
                        \Pure\Configuration::instance()->globals->requests->BUDDY,
                        $currentObjectID
                    );
                    break;
            }
            $Recorder = NULL;
        }
        public function proceed(){
            /* Pay your attention. Special page can be in 404 area and outside of this area
             * That's why we have to check it and in [proceed404] and here.
             * */
            $this->init();
            $result = $this->proceed404();
            $result = ($result === false ? $this->proceedSPECIAL()  : $result);
            $result = ($result === false ? $this->proceedBUDDY()    : $result);
            $result = ($result === false ? $this->proceedAUTHOR()   : $result);
            $result = ($result === false ? $this->proceedSEARCH()   : $result);
            $result = ($result === false ? $this->proceedPOST()     : $result);
            $result = ($result === false ? $this->proceedPAGE()     : $result);
            $result = ($result === false ? $this->proceedCATEGORY() : $result);
            $result = ($result === false ? $this->proceedTAG()      : $result);
            if ($result === false){
                $this->force404();
            }
            $this->registration();
            //================================================================
            \Pure\Events\WordPressEvents::Pure_BEFORE_LOAD_PAGE();
            //================================================================
            return $result;
        }
        function __construct(){
        }
    }
    class Recorder{
        private $WordPressOptionKey = 'PureUserLocationClearingLastTime';
        private $ClearingPeriod     = 1;//in days
        private function clearing(){
            $last       = get_option($this->WordPressOptionKey);
            $current    = date_create(date("Y-m-d H:i:s"));
            $last       = ($last !== false ? date_create($last) : date_create('2000-01-01'));
            $interval   = date_diff($current, $last);
            if ((int)$interval->days > $this->ClearingPeriod){
                update_option($this->WordPressOptionKey, date("Y-m-d H:i:s"));
                global $wpdb;
                $selector   =   'DELETE '.
                                'FROM '.
                                    \Pure\DataBase\TablesNames::instance()->locations.' '.
                                'WHERE '.
                                    'DATEDIFF( '.
                                        'NOW(), '.
                                        'entrance '.
                                    ') > 1';
                $wpdb->query($selector);
            }
        }
        private function isExist($user_id){
            global $wpdb;
            $selector   =   'SELECT '.
                                '* '.
                            'FROM '.
                                \Pure\DataBase\TablesNames::instance()->locations.' '.
                            'WHERE '.
                                'user_id = '.(int)$user_id;
            $result     = $wpdb->query($selector);
            if ($result !== false){
                return ((int)$result > 0 ? true : false);
            }
        }
        public function whereUserIs($user_id){
            global $wpdb;
            $selector   =   'SELECT '.
                                '* '.
                            'FROM '.
                                \Pure\DataBase\TablesNames::instance()->locations.' '.
                            'WHERE '.
                                'user_id = '.(int)$user_id;
            $result     = $wpdb->get_results($selector);
            if (is_array($result) !== false){
                if (count($result) === 1){
                    return $result[0]->object_type;
                }
            }
            return false;
        }
        private function insert($user_id, $object_type, $object_id){
            global $wpdb;
            $created    = date("Y-m-d H:i:s");
            $result     = $wpdb->insert(
                \Pure\DataBase\TablesNames::instance()->locations,
                array(
                    'user_id'       =>(int)$user_id,
                    'object_type'   =>$object_type,
                    'object_id'     =>(int)$object_id,
                    'entrance'      =>$created,
                ),
                array('%d', '%s', '%d', '%s')
            );
            return $result;
        }
        private function update($user_id, $object_type, $object_id){
            global $wpdb;
            $created    = date("Y-m-d H:i:s");
            $result     = $wpdb->update(
                \Pure\DataBase\TablesNames::instance()->locations,
                array( 'object_type'    => $object_type, 'object_id' => (int)$object_id, 'entrance' => $created),
                array( 'user_id'        => (int)$user_id    ),
                array( '%s', '%d'       ),
                array( '%s', '%d','%s'  )
            );
            return $result;
        }
        private function add($user_id, $object_type, $object_id){
            if ($this->isExist($user_id) === false){
                $this->insert($user_id, $object_type, $object_id);
            }else{
                $this->update($user_id, $object_type, $object_id);
            }
        }
        public function register($object_type, $object_id){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                $this->add($current->ID, $object_type, $object_id);
                $this->clearing();
            }
        }
        private function clearRecordsForObject($object_type, $object_id){
            global $wpdb;
            $current    = date("Y-m-d H:i:s");
            $selector   =   'DELETE '.
                            'FROM '.
                                \Pure\DataBase\TablesNames::instance()->locations.' '.
                            'WHERE '.
                                'id IN ( '.
                                    'SELECT '.
                                        'in_post.id AS id '.
                                    'FROM '.
                                        '( '.
                                            'SELECT '.
                                                '* '.
                                            'FROM '.
                                                \Pure\DataBase\TablesNames::instance()->locations.' '.
                                            'WHERE '.
                                                'object_id = '.(int)$object_id.' '.
                                            'AND object_type = "'.$object_type.'" '.
                                        ') AS in_post '.
                                    'WHERE '.
                                        'TIMESTAMPDIFF( '.
                                            'HOUR, '.
                                            'in_post.entrance, '.
                                            '"'.$current.'" '.
                                        ') >= 1 '.
                                    ')';
            $wpdb->query($selector);
        }
        public function getUsersByObject($object_type, $object_id){
            if ((int)$object_id > 0){
                $this->clearRecordsForObject($object_type, $object_id);
                global $wpdb;
                $current    = date("Y-m-d H:i:s");
                $selector   =   'SELECT '.
                                    'in_post.user_id AS id '.
                                'FROM '.
                                    '( '.
                                        'SELECT '.
                                            '* '.
                                        'FROM '.
                                            \Pure\DataBase\TablesNames::instance()->locations.' '.
                                        'WHERE '.
                                            'object_id = '.(int)$object_id.' '.
                                            'AND object_type = "'.$object_type.'" '.
                                    ') AS in_post '.
                                'WHERE '.
                                    'TIMESTAMPDIFF(HOUR, in_post.entrance, "'.$current.'") < 1';
                $result     = $wpdb->get_results($selector);
                if (is_array($result) !== false){
                    return (count($result) > 0 ? $result : false);
                }
            }
            return false;
        }
    }
}
?>