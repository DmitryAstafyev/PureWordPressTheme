<?php
namespace Pure\Components\Demo\Minimal{
    class Core{
        private $data               = false;
        private $option             = 'PureThemeMinimalDemoData';
        private $auto_flag          = 'PureThemeMinimalDemoDataIsGenerated';
        private $post_thumbnails    = false;
        private $posts_count        = 6;
        private $categories         = false;
        private function addIcons(){
            \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
            $PostProvider   = new \Pure\Components\PostTypes\Post\Module\Core();
            $path           = Initialization::instance()->configuration->paths->images.'/icons/';
            $FileSystem     = new \Pure\Resources\FileSystem();
            $icons          = $FileSystem->getFilesList($path);
            $FileSystem     = NULL;
            if (is_array($icons) !== false){
                foreach($icons as $key=>$icon){
                    $id = $PostProvider->unsafeAddAttachment(0, (object)array('name'=>$icon, 'full'=>$path.$icon));
                    if ((int)$id > 0){
                        $this->data->icons[$key] = (int)$id;
                    }
                }
            }
            $PostProvider   = NULL;
        }
        private function addAttachments(){
            \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
            $PostProvider   = new \Pure\Components\PostTypes\Post\Module\Core();
            $path           = Initialization::instance()->configuration->paths->images.'/attachments/';
            $FileSystem     = new \Pure\Resources\FileSystem();
            $attachments    = $FileSystem->getFilesList($path);
            $FileSystem     = NULL;
            if (is_array($attachments) !== false){
                foreach($attachments as $key=>$attachment){
                    $id = $PostProvider->unsafeAddAttachment(0, (object)array('name'=>$attachment, 'full'=>$path.$attachment));
                    if ((int)$id > 0){
                        $this->data->attachments[$key] = (int)$id;
                    }
                }
            }
            $PostProvider   = NULL;
        }
        private function getPostThumbnail(){
            if ($this->post_thumbnails === false){
                $FileSystem             = new \Pure\Resources\FileSystem();
                $path                   = Initialization::instance()->configuration->paths->images.'/thumbnails/';
                $thumbnails             = $FileSystem->getFilesList($path);
                $FileSystem             = NULL;
                if (is_array($thumbnails) !== false){
                    shuffle($thumbnails);
                    $this->post_thumbnails  = (object)array(
                        'thumbnails'=>array(),
                        'index'     =>-1
                    );
                    foreach($thumbnails as $thumbnail){
                        $this->post_thumbnails->thumbnails[] = (object)array(
                            'basename'  =>$thumbnail,
                            'full'      =>$path.$thumbnail
                        );
                    }
                }
            }
            if ($this->post_thumbnails !== false){
                if ($this->post_thumbnails->index === count($this->post_thumbnails->thumbnails) - 1){
                    $this->post_thumbnails->index = 0;
                }
                $this->post_thumbnails->index ++;
                return $this->post_thumbnails->thumbnails[$this->post_thumbnails->index];
            }
            return false;
        }
        private function createCategories(){
            $this->categories = array();
            for($index = 0; $index <= 4; $index ++){
                $cat_id = wp_insert_term(
                    'Demo category '.$index,
                    'category',
                    array(
                        'description'   => '',
                        'slug'          => 'demo_category_'.$index,
                    )
                );
                if (isset($cat_id['term_id']) !== false){
                    $this->categories[] = (int)$cat_id['term_id'];
                }
            }
        }
        private function getCategory(){
            if (count($this->categories) > 0){
                return $this->categories[rand(0, count($this->categories) - 1)];
            }else{
                return false;
            }
        }
        private function addPosts(){
            \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
            $PostProvider   = new \Pure\Components\PostTypes\Post\Module\Create();
            $WordPress      = new \Pure\Components\WordPress\UserData\Data();
            $current        = $WordPress->get_current_user();
            $WordPress      = NULL;
            for($index = 0; $index <= $this->posts_count; $index ++){
                $thumbnail = $this->getPostThumbnail();
                if ($thumbnail !== false){
                    $result = $PostProvider->create_not_from_POST(
                        (object)array(
                            'author_id'                 =>$current->ID,
                            'action'                    =>'publish',
                            'post_title'                =>__('Hello, World!', 'pure'),
                            'post_content'              =>__('This photo was created by great photographer, Greg Martin. You will get a tons pleasure, if visit his <a href="http://www.artofgregmartin.com/">site</a>.', 'pure'),
                            'post_excerpt'              =>__('This is demonstration post. You can configure front-page in administration panel in section "Pure", scroll "Page editor".', 'pure'),
                            'post_visibility'           =>0,
                            'post_association'          =>'community',
                            'post_category'             =>$this->getCategory(),
                            'post_allow_comments'       =>'open',
                            'post_miniature'            =>$thumbnail->full,
                            'post_sandbox'              =>'no',
                            'post_association_object'   =>0,
                            'post_tags'                 =>array('tag_1', 'tag_2')
                        )
                    );
                    if ($result->message === 'publish'){
                        $this->data->posts[] = (int)$result->id;
                    }
                }
            }
            $PostProvider = NULL;
        }
        private function addEvents(){
            \Pure\Components\PostTypes\Events\Module\Initialization::instance()->attach();
            $EventsProvider = new \Pure\Components\PostTypes\Events\Module\Create();
            $WordPress      = new \Pure\Components\WordPress\UserData\Data();
            $current        = $WordPress->get_current_user();
            $WordPress      = NULL;
            for($index = 0; $index <= $this->posts_count; $index ++){
                $thumbnail = $this->getPostThumbnail();
                if ($thumbnail !== false){
                    $date_event_start           = new \DateTime();
                    $date_event_finish          = new \DateTime();
                    $date_registration_start    = new \DateTime();
                    $date_registration_finish   = new \DateTime();
                    $date_event_finish->modify('+30 day');
                    $date_registration_finish->modify('+15 day');
                    $result = $EventsProvider->create_not_from_POST(
                        (object)array(
                            'author_id'                         =>$current->ID,
                            'action'                            =>'publish',
                            'post_title'                        =>__('Hello, World!', 'pure'),
                            'post_content'                      =>__('This photo was created by great photographer, Greg Martin. You will get a tons pleasure, if visit his <a href="http://www.artofgregmartin.com/">site</a>.', 'pure'),
                            'post_excerpt'                      =>__('This is demonstration post. You can configure front-page in administration panel in section "Pure", scroll "Page editor".', 'pure'),
                            'post_visibility'                   =>0,
                            'post_association'                  =>'community',
                            'post_category'                     =>$this->getCategory(),
                            'post_allow_comments'               =>'open',
                            'post_miniature'                    =>$thumbnail->full,
                            'post_sandbox'                      =>'no',
                            'post_association_object'           =>0,
                            'event_start_day'                   =>(int)$date_event_start->format('d'),
                            'event_start_month'                 =>(int)$date_event_start->format('m'),
                            'event_start_year'                  =>(int)$date_event_start->format('Y'),
                            'event_start_hour'                  =>11,
                            'event_start_minute'                =>00,
                            'event_finish_day'                  =>(int)$date_event_finish->format('d'),
                            'event_finish_month'                =>(int)$date_event_finish->format('m'),
                            'event_finish_year'                 =>(int)$date_event_finish->format('Y'),
                            'event_finish_hour'                 =>11,
                            'event_finish_minute'               =>00,
                            'event_registration_start_day'      =>(int)$date_registration_start->format('d'),
                            'event_registration_start_month'    =>(int)$date_registration_start->format('m'),
                            'event_registration_start_year'     =>(int)$date_registration_start->format('Y'),
                            'event_registration_start_hour'     =>11,
                            'event_registration_start_minute'   =>00,
                            'event_registration_finish_day'     =>(int)$date_registration_finish->format('d'),
                            'event_registration_finish_month'   =>(int)$date_registration_finish->format('m'),
                            'event_registration_finish_year'    =>(int)$date_registration_finish->format('Y'),
                            'event_registration_finish_hour'    =>11,
                            'event_registration_finish_minute'  =>00,
                            'event_members_limit'               =>100,
                            'event_on_map'                      =>'Koper, Slovenia',
                            'event_place_name'                  =>'',
                            'post_tags'                         =>array('tag_1', 'tag_2')
                        )
                    );
                    if ($result->message === 'publish'){
                        $this->data->posts[] = (int)$result->id;
                    }
                }
            }
            $EventsProvider = NULL;
        }
        private function addReports(){
            \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
            $ReportsProvider    = new \Pure\Components\PostTypes\Reports\Module\Create();
            $WordPress          = new \Pure\Components\WordPress\UserData\Data();
            $current            = $WordPress->get_current_user();
            $WordPress          = NULL;
            for($index = 0; $index <= $this->posts_count; $index ++){
                $thumbnail = $this->getPostThumbnail();
                if ($thumbnail !== false){
                    $result = $ReportsProvider->create_not_from_POST(
                        (object)array(
                            'author_id'                 =>$current->ID,
                            'action'                    =>'publish',
                            'post_title'                =>__('Hello, World!', 'pure'),
                            'post_content'              =>__('This photo was created by great photographer, Greg Martin. You will get a tons pleasure, if visit his <a href="http://www.artofgregmartin.com/">site</a>.', 'pure'),
                            'post_excerpt'              =>__('This is demonstration post. You can configure front-page in administration panel in section "Pure", scroll "Page editor".', 'pure'),
                            'post_visibility'           =>0,
                            'post_association'          =>'community',
                            'post_category'             =>$this->getCategory(),
                            'post_allow_comments'       =>'open',
                            'post_miniature'            =>$thumbnail->full,
                            'post_sandbox'              =>'no',
                            'post_association_object'   =>0,
                            'report_collection'         =>0,
                            'report_on_map'             =>'Koper, Slovenia',
                            'report_place_name'         =>'',
                            'post_tags'                 =>array('tag_1', 'tag_2')
                        )
                    );
                    if ($result->message === 'publish'){
                        $this->data->posts[] = (int)$result->id;
                    }
                }
            }
            $ReportsProvider = NULL;
        }
        private function addQuestions(){
            \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
            $ReportsProvider    = new \Pure\Components\PostTypes\Questions\Module\Create();
            $WordPress          = new \Pure\Components\WordPress\UserData\Data();
            $current            = $WordPress->get_current_user();
            $WordPress          = NULL;
            for($index = 0; $index <= $this->posts_count; $index ++){
                $result = $ReportsProvider->create_not_from_POST(
                    (object)array(
                        'author_id'                 =>$current->ID,
                        'action'                    =>'publish',
                        'post_title'                =>__('Hello, World!', 'pure'),
                        'post_content'              =>__('This photo was created by great photographer, Greg Martin. You will get a tons pleasure, if visit his <a href="http://www.artofgregmartin.com/">site</a>.', 'pure'),
                        'post_excerpt'              =>'',
                        'post_visibility'           =>0,
                        'post_association'          =>'community',
                        'post_category'             =>$this->getCategory(),
                        'post_allow_comments'       =>'open',
                        'post_miniature'            =>'no miniature',
                        'post_sandbox'              =>'no',
                        'post_association_object'   =>0,
                        'post_keywords'             =>array('keyword_1', 'keyword_2')
                    )
                );
                if ($result->message === 'publish'){
                    $this->data->posts[] = (int)$result->id;
                }
            }
            $ReportsProvider = NULL;
        }
        private function addPostsForSlider(){
            \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
            $PostProvider   = new \Pure\Components\PostTypes\Post\Module\Create();
            $path           = Initialization::instance()->configuration->paths->images.'/slider/';
            $WordPress      = new \Pure\Components\WordPress\UserData\Data();
            $current        = $WordPress->get_current_user();
            $WordPress      = NULL;
            $FileSystem     = new \Pure\Resources\FileSystem();
            $thumbnails     = $FileSystem->getFilesList($path);
            $FileSystem     = NULL;
            if (is_array($thumbnails) !== false){
                foreach($thumbnails as $thumbnail){
                    $result = $PostProvider->create_not_from_POST(
                        (object)array(
                            'author_id'                 =>$current->ID,
                            'action'                    =>'publish',
                            'post_title'                =>__('Hello, World!', 'pure'),
                            'post_content'              =>__('This photo was created by great photographer, Greg Martin. You will get a tons pleasure, if visit his <a href="http://www.artofgregmartin.com/">site</a>.', 'pure'),
                            'post_excerpt'              =>__('This is demonstration post. You can configure front-page in administration panel in section "Pure", scroll "Page editor".', 'pure'),
                            'post_visibility'           =>0,
                            'post_association'          =>'community',
                            'post_category'             =>$this->getCategory(),
                            'post_allow_comments'       =>'open',
                            'post_miniature'            =>$path.$thumbnail,
                            'post_sandbox'              =>'no',
                            'post_association_object'   =>0,
                        )
                    );
                    if ($result->message === 'publish'){
                        $this->data->posts_for_slider[] = (int)$result->id;
                    }
                }
            }
        }
        private function addInserts(){
            \Pure\Components\PostTypes\Inserts\Module\Initialization::instance()->attach();
            $InsertProvider = new \Pure\Components\PostTypes\Inserts\Module\Provider();
            $post_id        = $InsertProvider->create((object)array(
                'post_title'    =>__('Hello, World!', 'pure'),
                'post_content'  =>__('This is "insert". It is type of post, which was defined by us for inserts into page, like you see here. You can easily change, remove or add new inserts; render it on your page or in sidebar. To manage content of inserts, you should go to an administration panel, section "Inserts". To define place of inserts on page, you should go to "Widgets" page (for configure footers and magic bar) or "Page editor" (for configure front-page).', 'pure'),
            ));
            $InsertProvider = NULL;
            if ($post_id !== false){
                $this->data->inserts[] = (int)$post_id;
            }
        }
        private function addBackground(){
            \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
            $PostProvider   = new \Pure\Components\PostTypes\Post\Module\Core();
            $path           = Initialization::instance()->configuration->paths->images.'/background/';
            $id             = $PostProvider->unsafeAddAttachment(0, (object)array('name'=>'1.jpg', 'full'=>$path.'1.jpg'));
            if ((int)$id > 0){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach(true);
                $Settings = new \Pure\Components\WordPress\Settings\Settings();
                $Settings->try_save_by_name(
                    'images',
                    'background',
                    $id
                );
                $Settings = NULL;
            }
        }
        private function add(){
            $this->data = false;
            if ($this->getCategory() !== false){
                $this->data = (object)array(
                    'icons'             =>array(),
                    'posts'             =>array(),
                    'attachments'       =>array(),
                    'posts_for_slider'  =>array(),
                    'inserts'           =>array()
                );
                $this->createCategories     ();
                $this->addIcons             ();
                $this->addAttachments       ();
                $this->addInserts           ();
                $this->addPostsForSlider    ();
                $this->addPosts             ();
                $this->addEvents            ();
                $this->addReports           ();
                $this->addQuestions         ();
                $this->addBackground        ();
            }
            return $this->data;
        }
        public function generate(){
            //If data was generated before
            $data   = get_option($this->option);
            if ($data !== false){
                if (is_object($data) !== false){
                    return $data;
                }
            }
            //If data doesn't exist
            $result = $this->add();
            if ($result !== false){
                update_option($this->option, $result);
            }
            return $result;
        }
        public function remove(){
            $data = $this->getDemoData();
            if ($data !== false){
                $remove = function($IDs){
                    foreach($IDs as $id){
                        $post = get_post($id);
                        if ($post !== false){
                            if ($post->post_type === 'attachment'){
                                wp_delete_attachment( $id );
                            }else{
                                $attachments = get_posts(
                                    array(
                                        'post_type'         => 'attachment',
                                        'posts_per_page'    => -1,
                                        'post_status'       => null,
                                        'post_parent'       => $id
                                    )
                                );
                                if (is_array($attachments) !== false){
                                    foreach( $attachments as $attachment ){
                                        wp_delete_attachment( $attachment->ID );
                                    }
                                }
                                $thumbnail_id = get_post_thumbnail_id($id);
                                if ($thumbnail_id !== false && is_null($thumbnail_id) === false){
                                    delete_post_thumbnail($id);
                                    wp_delete_attachment( $thumbnail_id );
                                }
                                wp_delete_post($id, true);
                            }
                        }
                    }
                };
                $remove($data->icons                );
                $remove($data->attachments          );
                $remove($data->posts                );
                $remove($data->posts_for_slider     );
                $remove($data->inserts              );
                delete_option($this->option);
            }
        }
        public function init(){
            $isGenerated = get_option($this->auto_flag);
            if ($isGenerated !== 'yes'){
                if ($this->generate() !== false){
                    update_option($this->auto_flag, 'yes');
                }
            }
        }
        public function getDemoData(){
            return get_option($this->option);
        }
        function __construct(){
        }
    }
}