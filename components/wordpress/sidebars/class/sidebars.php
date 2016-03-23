<?php
namespace Pure\Components\WordPress\Sidebars{
    class Core{
        private function unregisterDefaultWidgets() {
            //WordPress
            unregister_widget('WP_Widget_Pages'                     );
            unregister_widget('WP_Widget_Calendar'                  );
            unregister_widget('WP_Widget_Archives'                  );
            unregister_widget('WP_Widget_Links'                     );
            unregister_widget('WP_Widget_Meta'                      );
            unregister_widget('WP_Widget_Search'                    );
            unregister_widget('WP_Widget_Text'                      );
            unregister_widget('WP_Widget_Categories'                );
            unregister_widget('WP_Widget_Recent_Posts'              );
            unregister_widget('WP_Widget_Recent_Comments'           );
            unregister_widget('WP_Widget_RSS'                       );
            unregister_widget('WP_Widget_Tag_Cloud'                 );
            unregister_widget('WP_Nav_Menu_Widget'                  );
            unregister_widget('Twenty_Eleven_Ephemera_Widget'       );
            //BuddyPress
            unregister_widget('BP_Blogs_Recent_Posts_Widget'        );
            unregister_widget('BP_Core_Login_Widget'                );
            unregister_widget('BP_Core_Friends_Widget'              );
            unregister_widget('BP_Groups_Widget'                    );
            unregister_widget('BP_Core_Members_Widget'              );
            unregister_widget('BP_Core_Whos_Online_Widget'          );
            unregister_widget('BP_Core_Recently_Active_Widget'      );
            unregister_widget('BP_Messages_Sitewide_Notices_Widget' );
        }
        public function init(){
            $this->unregisterDefaultWidgets();
            $this->registration();
            $this->setDefaultIfNeed();
        }
        private function registration(){
            $sidebars = new Registration();
            $this->registerSidebars($sidebars->sidebars);
            $sidebars = NULL;
        }
        public function resetSidebars(){
            $sidebars = new Registration();
            $sidebars = $sidebars->sidebars;
            foreach($sidebars as $sidebar){
                unregister_sidebar($sidebar);
            }
            $sidebars = NULL;
        }
        public function registerSidebars($sidebars){
            foreach($sidebars as $sidebar){
                register_sidebar($sidebar);
            }
        }
        private function setDefaultIfNeed(){
            $Content            = new Content();
            $insert_id          = $Content->getInsert();
            $Content            = NULL;
            $sidebars           = new Registration();
            $sets               = $sidebars->getSets(($insert_id !== false ? $insert_id : 0));
            $sidebars           = $sidebars->sidebars;
            $active_sidebars    = get_option( 'sidebars_widgets' );
            $is_empty           = array();
            foreach($sidebars as $sidebar){
                $is_empty[$sidebar['id']] = (empty($active_sidebars[$sidebar['id']]) === false ? false : true);
            }
            foreach($sets as $sidebar_id=>$widgets){
                foreach($widgets as $widget){
                    if ($is_empty[$sidebar_id] !== false){
                        $this->addWidgetToSidebar($sidebar_id, $widget['id'], $widget['settings']);
                    }
                }
            }
            $sidebars           = NULL;
        }
        private function getWidgetClassByID($widget_id){
            if ( empty ( $GLOBALS['wp_widget_factory'] ) === false ){
                foreach($GLOBALS['wp_widget_factory'] as $class=>$widget){
                    foreach($widget as $inside_widget){
                        if (isset($inside_widget->id_base) !== false){
                            if ($inside_widget->id_base == $widget_id){
                                return get_class($inside_widget);
                            }
                        }
                    }
                }
            }
            return false;
        }
        public function getWidgetsFromSideBar($sidebar_id){
            $active_sidebars    = get_option( 'sidebars_widgets' );
            $result             = array();
            if (isset($active_sidebars[$sidebar_id]) !== false){
                if (empty($active_sidebars[$sidebar_id]) === false){
                    foreach($active_sidebars[$sidebar_id] as $widget){
                        $widget_id = preg_replace('/-\d*$/','',$widget);
                        preg_match('/-(\d*)$/',$widget, $widget_index);
                        if (count($widget_index) === 2){
                            $widget_index       = (int)$widget_index[1];
                            $widget_settings    = get_option( 'widget_'.$widget_id);
                            if (is_array($widget_settings) !== false){
                                if (isset($widget_settings[$widget_index]) !== false){
                                    $widget_class           = $this->getWidgetClassByID($widget_id);
                                    if ($widget_class !== false){
                                        $widget_settings    = $widget_settings[$widget_index];
                                        $result[]           = (object)array(
                                            'id'            =>$widget_id,
                                            'index'         =>$widget_index,
                                            'settings'      =>$widget_settings,
                                            'class'         =>$widget_class
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $result;
        }
        public function addWidgetToSidebar($sidebar_id, $widget_id, $widget_settings){
            //Get widget config
            $widgets_settings   = get_option('widget_'.$widget_id);
            $widgets_settings 	= ($widgets_settings === false ? array() : $widgets_settings);
            //Get sidebars collection
            $sidebars_settings  = get_option('sidebars_widgets');
            //Get name of widget class
            $widget_class       = $this->getWidgetClassByID($widget_id);
            if (is_array($widgets_settings) !== false && is_array($sidebars_settings)   !== false &&
                $widget_class               !== false && $widget_class                  !== ''){
                //Define last index in widget settings
                /*Indexes before 2 are used by core*/
                $instance_index = 2;
                if (count($widgets_settings) > 0){
                    foreach($widgets_settings as $key=>$setting){
                        $instance_index = ((int)$key > 0 ? (int)$key : $instance_index);
                    }
                    $instance_index ++;
                }
                $instance_index = ($instance_index < 2 ? 2 : $instance_index);
                //Add new widget
                $widgets_settings[$instance_index] = $widget_settings;
                //Check and define (if necessary) sidebar setting
                if (isset($sidebars_settings[$sidebar_id]) === false){
                    $sidebars_settings[$sidebar_id] = array();
                }
                //Add link to widget in sidebar
                $sidebars_settings[$sidebar_id][] = $widget_id.'-'.$instance_index;
                //Save data
                update_option( 'widget_'.$widget_id,    $widgets_settings   );
                update_option( 'sidebars_widgets',      $sidebars_settings  );
                return true;
            }
            return false;
        }
    }
    class Render{
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        public function make($sidebar_id){
            if ( function_exists('dynamic_sidebar') ){
                dynamic_sidebar($sidebar_id);
            }
        }
        public function innerHTMLSidebarControls($sidebar_id, $displayed_name){
            ob_start();
            wp_list_widget_controls( $sidebar_id, $displayed_name );
            $innerHTML = ob_get_contents();
            ob_get_clean();
            $innerHTML =    '<div class="'.esc_attr( 'widgets-holder-wrap closed').'">'.
                                $innerHTML.
                            '</div>';
            return $innerHTML;
        }
        public function innerHTMLSidebar($sidebar_id){
            ob_start();
            dynamic_sidebar($sidebar_id);
            $innerHTML = ob_get_contents();
            ob_get_clean();
            return $innerHTML;
        }
        public function by_location($group, $page = false){
            $sidebars   = new Registration();
            $sidebar_id = false;
            if (isset($sidebars->positions[$group]) !== false){
                if ($page !== false){
                    if (gettype($sidebars->positions[$group]) !== 'string'){
                        $sidebar_id = (isset($sidebars->positions[$group][$page]) !== false ? $sidebars->positions[$group][$page] : '');
                    }
                }else{
                    $sidebar_id = (gettype($sidebars->positions[$group]) === 'string' ? $sidebars->positions[$group] : false);
                }
            }
            $sidebars = NULL;
            if ($sidebar_id !== false){
                dynamic_sidebar($sidebar_id);
                return true;
            }
            return false;
        }
    }
    class SaveLoadState{
        private $option_field = 'pure_theme_sidebars_widgets_restore_data';
        /* Get widget ID from string like [puretheme_authors_thumbnails-2]
         * Will get from [puretheme_authors_thumbnails-2] ID [puretheme_authors_thumbnails]
         * */
        private function getWidgetID($widget_instance_str){
            return preg_replace('/-\d{1,}$/', '', $widget_instance_str);
        }
        public function load(){
            //Step 1. Get data
            $data = get_option($this->option_field);
            if ($data !== false){
                //Step 2. Validate data
                if (isset($data->widgets_IDs        ) !== false &&
                    isset($data->widgets_settings   ) !== false &&
                    isset($data->sidebars           ) !== false){
                    //Step 3. Restore sidebars settings
                    update_option('sidebars_widgets', $data->sidebars);
                    //Step 4. Restore widgets settings
                    foreach($data->widgets_settings as $widget_id=>$widget_settings){
                        update_option('widget_'.$widget_id, $widget_settings);
                    }
                    return true;
                }
            }
            return false;
        }
        public function save(){
            //Step 1. Get sidebars settings
            $data = (object)array(
                'widgets_IDs'       =>array(),
                'widgets_settings'  =>array(),
                'sidebars'          =>get_option('sidebars_widgets')
            );
            //Step 2. Get IDs of all used widgets
            foreach($data->sidebars as $sidebar_id=>$sidebar_widgets){
                foreach($sidebar_widgets as $key=>$widget_instance_str){
                    $widget_id = $this->getWidgetID($widget_instance_str);
                    if (in_array($widget_id, $data->widgets_IDs) === false){
                        $data->widgets_IDs[] = $widget_id;
                    }
                }
            }
            //Step 3. Get settings of all used widgets
            foreach($data->widgets_IDs as $widget_id){
                $widget_settings = get_option('widget_'.$widget_id);
                if ($widget_settings !== false){
                    $data->widgets_settings[$widget_id] = $widget_settings;
                }
            }
            //Step 4. Save data
            update_option($this->option_field, $data);
        }
    }
    class Content{
        private $option = 'pure_sidebars_default_insert';
        public function getInsert(){
            $post_id = get_option($this->option);
            if ($post_id === false){
                \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
                $PostProvider   = new \Pure\Components\PostTypes\Post\Module\Core();
                $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                $current        = $WordPress->get_current_user();
                $WordPress      = NULL;
                $post_id        = $PostProvider->unsafeAddEmptyDraft($current->ID, 'insert');
                if ($post_id !== false){
                    $arguments  = array(
                        'comment_status'    => 'open',
                        'post_content'      => __('This is "insert". It is type of post, which was defined by us for inserts into page, like you see here. You can easily change, remove or add new inserts; render it on your page or in sidebar. To manage content of inserts, you should go to an administration panel, section "Inserts". To define place of inserts on page, you should go to "Widgets" page (for configure footers and magic bar) or "Page editor" (for configure front-page).', 'pure'),
                        'post_excerpt'      => '',
                        'post_title'        => __('Hello, World!', 'pure'),
                        'post_type'         => 'insert',
                        'post_status'       => 'publish',
                        'ID'                => $post_id
                    );
                    //Save post
                    $post_id = wp_update_post($arguments);
                }
                $PostProvider = NULL;
                if ((int)$post_id > 0){
                    update_option($this->option, (int)$post_id);
                }
            }
            return ((int)$post_id > 0 ? (int)$post_id : false);
        }

    }
}
?>