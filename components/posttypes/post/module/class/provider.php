<?php
namespace Pure\Components\PostTypes\Post\Module{
    class Core{
        public function addEmptyDraft($post_type){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                global $wpdb;
                $current_time   = date("Y-m-d H:i:s");
                $result         = $wpdb->insert(
                    $wpdb->posts,
                    array(
                        'post_author'           =>(int)$current->ID,
                        'post_date'             =>$current_time,
                        'post_date_gmt'         =>'0000-00-00 00:00:00',
                        'post_content'          =>'',
                        'post_title'            =>'Auto Draft',
                        'post_excerpt'          =>'',
                        'post_status'           =>'auto-draft',
                        'comment_status'        =>'open',
                        'ping_status'           =>'open',
                        'post_password'         =>'',
                        'post_name'             =>'',
                        'to_ping'               =>'',
                        'pinged'                =>'',
                        'post_modified'         =>$current_time,
                        'post_modified_gmt'     =>'0000-00-00 00:00:00',
                        'post_content_filtered' =>'',
                        'post_parent'           =>0,
                        'guid'                  =>'',
                        'menu_order'            =>0,
                        'post_type'             =>$post_type,
                        'post_mime_type'        =>'',
                        'comment_count'         =>0
                    ),
                    array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d')
                );
                if ($result !== false && is_null($result) === false){
                    $post_ID = (int)$wpdb->insert_id;
                    $wpdb->update( $wpdb->posts, array( 'guid' => get_permalink( $post_ID ) ), array( 'ID' => $post_ID ) );
                    return $post_ID;
                }
            }
            return false;
        }
        public function unsafeAddEmptyDraft($user_id, $post_type){
            if ((int)$user_id > 0){
                global $wpdb;
                $current_time   = date("Y-m-d H:i:s");
                $result         = $wpdb->insert(
                    $wpdb->posts,
                    array(
                        'post_author'           =>(int)$user_id,
                        'post_date'             =>$current_time,
                        'post_date_gmt'         =>'0000-00-00 00:00:00',
                        'post_content'          =>'',
                        'post_title'            =>'Auto Draft',
                        'post_excerpt'          =>'',
                        'post_status'           =>'auto-draft',
                        'comment_status'        =>'open',
                        'ping_status'           =>'open',
                        'post_password'         =>'',
                        'post_name'             =>'',
                        'to_ping'               =>'',
                        'pinged'                =>'',
                        'post_modified'         =>$current_time,
                        'post_modified_gmt'     =>'0000-00-00 00:00:00',
                        'post_content_filtered' =>'',
                        'post_parent'           =>0,
                        'guid'                  =>'',
                        'menu_order'            =>0,
                        'post_type'             =>$post_type,
                        'post_mime_type'        =>'',
                        'comment_count'         =>0
                    ),
                    array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d')
                );
                if ($result !== false && is_null($result) === false){
                    $post_ID = (int)$wpdb->insert_id;
                    $wpdb->update( $wpdb->posts, array( 'guid' => get_permalink( $post_ID ) ), array( 'ID' => $post_ID ) );
                    return (int)$post_ID;
                }
            }
            return false;
        }
        /*
        $post_id    = int || 0 (don't assign to post)
        $image      = (object)array('name'=>filename, 'full'=> filename with path to file)
        */
        public function unsafeAddAttachment($post_id, $image){
            $tmp_dir    = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
            $size       = getimagesize($image->full);
            if (is_array($size) !== false){
                if ($size[0] > 0 && $size[1] > 0){
                    if (copy($image->full, $tmp_dir.'/'.$image->name) !== false){
                        $file           = array(
                            'name'      =>$image->name,
                            'type'      =>$size['mime'],
                            'size'      =>filesize($image->full),
                            'tmp_name'  =>$tmp_dir.'/'.$image->name,
                        );
                        if (function_exists('media_handle_sideload') === false){
                            //Attach resources
                            require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/image.php')   );
                            require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/file.php')    );
                            require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/media.php')   );
                        }
                        $attachment_id  = media_handle_sideload( $file, $post_id, $image->name );
                        if (is_wp_error( $attachment_id ) === false){
                            return (int)$attachment_id;
                        }else{
                            return $attachment_id->get_error_message();
                        }
                    }
                }
            }
            return false;
        }
    }
    class Create{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create_from_POST':
                case 'create_not_from_POST':
                    \Pure\Components\WordPress\Post\Visibility\Initialization::instance()->attach(true);
                    $parameters->post_id                    = (integer  )(isset($parameters->post_id) === false ? -1 : $parameters->post_id);
                    $parameters->author_id                  = (integer  )($parameters->author_id                );
                    $parameters->action                     = (string   )($parameters->action                   );
                    $parameters->post_title                 = (string   )($parameters->post_title               );
                    $parameters->post_content               = (string   )($parameters->post_content             );
                    $parameters->post_excerpt               = (string   )($parameters->post_excerpt             );
                    $parameters->post_visibility            = (integer  )($parameters->post_visibility          );
                    $parameters->post_association           = (string   )($parameters->post_association         );
                    $parameters->post_category              = (integer  )($parameters->post_category            );
                    $parameters->post_allow_comments        = (string   )($parameters->post_allow_comments      );
                    $parameters->post_miniature             = ($parameters->post_miniature          !== false ? (string)($parameters->post_miniature            ) : 'miniature' );
                    $parameters->post_sandbox               = ($parameters->post_sandbox            !== false ? (string)($parameters->post_sandbox              ) : 'no'        );
                    $parameters->post_association_object    = ($parameters->post_association_object !== false ? (integer)($parameters->post_association_object  ) : 0           );
                    //Possible values
                    if (in_array($parameters->action,               array('publish', 'draft', 'preview', 'update'   )               ) === false){ return false; }
                    if (array_search($parameters->post_visibility,  \Pure\Components\WordPress\Post\Visibility\Data::$visibility    ) === false){ return false; }
                    if (array_search($parameters->post_association, \Pure\Components\WordPress\Post\Visibility\Data::$association   ) === false){ return false; }
                    if (in_array($parameters->post_sandbox,         array('yes', 'no'                               )               ) === false){ return false; }
                    if (in_array($parameters->post_allow_comments,  array('open', 'closed'                          )               ) === false){ return false; }
                    if (isset($parameters->post_tags) !== false){
                        if (is_array($parameters->post_tags) !== false){
                            foreach($parameters->post_tags as $key=>$tag){
                                $parameters->post_tags[$key] = mb_strtolower((string)$tag);
                            }
                        }else{
                            $parameters->post_tags = array();
                        }
                    }else{
                        $parameters->post_tags = array();
                    }
                    if (isset($parameters->post_warnings) !== false){
                        if (is_array($parameters->post_warnings) !== false){
                            foreach($parameters->post_warnings as $key=>$tag){
                                $parameters->post_warnings[$key] = mb_strtolower((string)$tag);
                            }
                        }else{
                            $parameters->post_warnings = array();
                        }
                    }else{
                        $parameters->post_warnings = array();
                    }
                    return true;
                    break;
                case 'update':
                    $parameters->post_id                    = (integer  )($parameters->post_id                  );
                    $parameters->action                     = (string   )($parameters->action                   );
                    return true;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'create_from_POST':
                    $parameters->post_title     = wp_strip_all_tags($parameters->post_title     );
                    $parameters->post_excerpt   = wp_strip_all_tags($parameters->post_excerpt   );
                    return true;
            }
        }
        private function getSandboxCategoryID(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
            $categoryID = (int)$parameters->mana_threshold_manage_categories_sandbox;
            $parameters = NULL;
            return (int)$categoryID;
        }
        public function create_from_POST($parameters, $update = false){
            $result_object = (object)array(
                'message'   =>false,
                'id'        =>false
            );
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                if ($parameters->post_id !== -1){
                    $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                    $current    = $WordPress->get_current_user();
                    $WordPress  = NULL;
                    if ((int)($current !== false ? $current->ID : -1) === (int)$parameters->author_id) {
                        if (strlen($parameters->post_title) > 3){
                            if (strlen($parameters->post_content) > 0){
                                $arguments  = array(
                                    'comment_status'    => $parameters->post_allow_comments,
                                    'post_author'       => $parameters->author_id,
                                    'post_category'     => ($parameters->post_sandbox === 'yes' ? array($parameters->post_category, $this->getSandboxCategoryID()) : array($parameters->post_category)),
                                    'post_content'      => $parameters->post_content,
                                    'post_excerpt'      => $parameters->post_excerpt,
                                    'post_title'        => $parameters->post_title,
                                    'post_type'         => 'post',
                                    'ID'                => $parameters->post_id
                                );
                                if ($update !== false){
                                    if ($parameters->action !== 'update'){
                                        $arguments['post_status'] = ($parameters->action === 'publish' ? 'publish' : 'draft');
                                    }
                                    $post_id                    = wp_update_post($arguments);
                                }else{
                                    $arguments['post_status']   = ($parameters->action === 'publish' ? 'publish' : 'draft');
                                    $post_id                    = wp_update_post($arguments);
                                }
                                if ((int)$post_id > 0){
                                    //Tags
                                    \Pure\Components\WordPress\Terms\Module\Initialization::instance()->attach();
                                    $Terms = new \Pure\Components\WordPress\Terms\Module\Provider();
                                    $Terms->update($parameters->post_tags, 'post_tag');
                                    $Terms->attach(
                                        (int)$post_id,
                                        $parameters->post_tags,
                                        'post_tag',
                                        true
                                    );
                                    $Terms->attach(
                                        (int)$post_id,
                                        $parameters->post_warnings,
                                        'warning_mark',
                                        true
                                    );
                                    $Terms = NULL;
                                    //Visibility
                                    $Visibility = new \Pure\Components\WordPress\Post\Visibility\Provider();
                                    $Visibility->set(
                                        (int)$post_id,
                                        (object)array(
                                            'visibility'    =>$parameters->post_visibility,
                                            'association'   =>$parameters->post_association,
                                        ),
                                        (int)$parameters->post_association_object
                                    );
                                    //Miniature
                                    if ($parameters->post_miniature === 'no miniature'){
                                        delete_post_thumbnail($post_id);
                                    }else if($parameters->post_miniature === 'miniature'){
                                        if (isset($_FILES['post_miniature']) !== false){
                                            require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/image.php')   );
                                            require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/file.php')    );
                                            require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/media.php')   );
                                            $attachment_id = media_handle_upload( 'post_miniature', $post_id );
                                            if (is_wp_error( $attachment_id ) === false) {
                                                if (set_post_thumbnail($post_id, $attachment_id) === false){
                                                    //Error: with setting attachment
                                                    $result_object->message = 'thumbnail error';
                                                    return $result_object;
                                                }
                                            } else {
                                                //Error: with loading attachment
                                                $result_object->message = 'thumbnail error';
                                                return $result_object;
                                            }
                                        }
                                    }else if((int)$parameters->post_miniature > 0){
                                        if (set_post_thumbnail($post_id, (int)$parameters->post_miniature) === false){
                                            //Error: with setting attachment
                                            $result_object->message = 'thumbnail error';
                                            return $result_object;
                                        }
                                    }
                                    if ($parameters->action === 'publish' || $parameters->action === 'update'){
                                        $result_object->message = 'publish';
                                        $result_object->id      = $post_id;
                                        return $result_object;
                                    }else{
                                        $result_object->message = 'drafted';
                                        $result_object->id      = $current->ID;
                                        return $result_object;
                                    }
                                }else{
                                    //Error: during saving
                                    $result_object->message = 'error during saving';
                                    return $result_object;
                                }
                            }else{
                                //Error: no content
                                $result_object->message = 'no content';
                                return $result_object;
                            }
                        }else{
                            //Error: no title
                            $result_object->message = 'no title';
                            return $result_object;
                        }
                    }
                }
            }
            //Error: no access
            $result_object->message = 'no access';
            return $result_object;
        }
        public function create_not_from_POST($parameters, $update = false){
            $result_object = (object)array(
                'message'   =>false,
                'id'        =>false
            );
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false) {
                    $PostProvider           = new Core();
                    $parameters->post_id    = $PostProvider->unsafeAddEmptyDraft($current->ID, 'post');
                    $PostProvider           = NULL;
                    if ((int)$parameters->post_id > 0) {
                        if ($parameters->post_miniature !== 'no miniature'){
                            if (file_exists(\Pure\Configuration::instance()->dir($parameters->post_miniature)) !== false){
                                \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
                                $PostProvider   = new \Pure\Components\PostTypes\Post\Module\Core();
                                $attachment_id  = $PostProvider->unsafeAddAttachment(
                                    0,
                                    (object)array(
                                        'name'=>pathinfo($parameters->post_miniature)['basename'],
                                        'full'=>$parameters->post_miniature
                                    )
                                );
                                $PostProvider = NULL;
                                if ((int)$attachment_id > 0){
                                    $parameters->post_miniature = $attachment_id;
                                }else{
                                    $parameters->post_miniature = 'no miniature';
                                }
                            }else{
                                $parameters->post_miniature = 'no miniature';
                            }
                        }
                        return $this->create_from_POST($parameters, $update);
                    }
                }
            }
            //Error: no access
            $result_object->message = 'no access';
            return $result_object;
        }
        public function update($parameters){
            $result_object = (object)array(
                'message'   =>false,
                'id'        =>false
            );
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $Posts = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                if ($Posts->get($parameters->post_id, true) !== false){
                    if ($parameters->action === 'remove'){
                        $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                        $current    = $WordPress->get_current_user();
                        $WordPress  = NULL;
                        $post       = get_post($parameters->post_id);
                        if ($post !== false && is_null($post) === false){
                            if ((int)($current !== false ? $current->ID : -1) === (int)$post->post_author) {
                                wp_delete_post($parameters->post_id, true);
                                $result_object->message = 'removed';
                                $result_object->id      = $current->ID;
                                return $result_object;
                            }
                        }
                    }else{
                        return $this->create_from_POST($parameters, true);
                    }
                }else{
                    //Error: post was not found
                    $result_object->message = 'no post';
                    return $result_object;
                }
                $Posts = NULL;
            }
            //Error: no access
            $result_object->message = 'no access';
            return $result_object;
        }
    }
}
?>