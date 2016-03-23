<?php
namespace Pure\Components\Demo\Module{
    class Core{
        private $authors;
        private $groups;
        private $tags;
        private $categories;
        private $posts;
        private $option = 'pure_demo_export_history';
        private function addAuthors(){
            $References = new References();
            $authors    = $References->get('authors');
            if ($authors !== false){
                if (isset($authors->author) !== false){
                    Logs::log('start', 'members');
                    $authors = $authors->author;
                    \Pure\Components\WordPress\Authorization\Initialization::instance()->attach();
                    $Authorization  = new \Pure\Components\WordPress\Authorization\Core();
                    $ids            = array();
                    $IDs            = array();
                    foreach($authors as $author){
                        $id = $Authorization->addUserWithoutConfirmation(
                            (object)array(
                                'login'     =>$author->login,
                                'password'  =>$author->password,
                                'email'     =>$author->login.'@purethemedemo.com',
                                'first_name'=>$author->first_name,
                                'last_name' =>$author->last_name,
                            )
                        );
                        if ($id !== false){
                            Logs::log('member ID#'.$id.' created', 'members');
                            $ids[]  = (int)$id;
                            $IDs[]  = (object)array(
                                'record_id' =>(int)$author->id,
                                'user_id'   =>(int)$id,
                                'avatar'    =>false,
                            );
                            if (($avatar_file = $References->hasUserAvatar((int)$author->id)) !== false){
                                $tmp_dir    = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
                                $size       = getimagesize($avatar_file->full);
                                if (is_array($size) !== false){
                                    if ($size[0] > 0 && $size[1] > 0){
                                        if (copy($avatar_file->full, $tmp_dir.'/'.$avatar_file->name) !== false){
                                            $_FILES['file'] = array(
                                                'name'      =>$avatar_file->full,
                                                'type'      =>$size['mime'],
                                                'size'      =>filesize($avatar_file->full),
                                                'tmp_name'  =>$tmp_dir.'/'.$avatar_file->name,
                                                'error'     =>'',
                                            );
                                            \Pure\Components\BuddyPress\Profile\Initialization::instance()->attach();
                                            $BuddyPressProfile = new \Pure\Components\BuddyPress\Profile\Core();
                                            if ($BuddyPressProfile->doSetAvatar((object)array(
                                                    'id'    =>$id,
                                                    'files' =>$_FILES,
                                                    'field' =>'file',
                                                    'crop'  =>(object)array(
                                                        'x'=>0,
                                                        'y'=>0,
                                                        'h'=>$size[1],
                                                        'w'=>$size[0]
                                                    ))) === true){
                                                $IDs[count($IDs) - 1]->avatar = true;
                                                Logs::log('avatar for member ID#'.$id.' attached', 'members');
                                            }else{
                                                Logs::log('fail set avatar for member ID#'.$id, 'members');
                                            }
                                            $BuddyPressProfile = NULL;
                                        }else{
                                            Logs::log('fail copy avatar for member ID#'.$id.' to temp folder '.$tmp_dir, 'members');
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (count($IDs) > 0){
                        $this->authors  = (object)array(
                            'list'=>$ids,
                            'full'=>$IDs
                        );
                        $Authorization  = NULL;
                        $References     = NULL;
                        Logs::log('created '.count($IDs).' members', 'members');
                        return true;
                    }
                }
            }
            $References = NULL;
            return false;
        }
        private function addGroups(){
            $References = new References();
            $groups     = $References->get('groups');
            if ($groups !== false) {
                if (isset($groups->group) !== false) {
                    Logs::log('start', 'groups');
                    $groups = $groups->group;
                    \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                    $Groups = new \Pure\Components\BuddyPress\Groups\Core();
                    $ids    = array();
                    $IDs    = array();
                    foreach($groups as $group){
                        $creator    = $this->authors->list[rand(0, (count($this->authors->list) - 1))];
                        $result     = $Groups->unsafeCreate(
                            (object)array(
                                'user_id'       =>$creator,
                                'name'          =>$group->name,
                                'description'   =>$group->description,
                                'visibility'    =>'public',
                                'invitations'   =>'members',
                            )
                        );
                        if (is_object($result) !== false){
                            Logs::log('group ID#'.$result->id.' created', 'groups');
                            $ids[]  = (int)$result->id;
                            $IDs[]  = (object)array(
                                'record_id' =>(int)$group->id,
                                'group_id'  =>(int)$result->id,
                                'creator'   =>$creator,
                                'avatar'    =>false,
                            );
                            if (($avatar_file = $References->hasGroupAvatar((int)$group->id)) !== false){
                                $tmp_dir    = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
                                $size       = getimagesize($avatar_file->full);
                                if (is_array($size) !== false){
                                    if ($size[0] > 0 && $size[1] > 0){
                                        if (copy($avatar_file->full, $tmp_dir.'/'.$avatar_file->name) !== false){
                                            $_FILES['file'] = array(
                                                'name'      =>$avatar_file->full,
                                                'type'      =>$size['mime'],
                                                'size'      =>filesize($avatar_file->full),
                                                'tmp_name'  =>$tmp_dir.'/'.$avatar_file->name,
                                                'error'     =>'',
                                            );
                                            \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                                            $BuddyPressGroups   = new \Pure\Components\BuddyPress\Groups\Core();
                                            if ($BuddyPressGroups->setAvatar((object)array(
                                                    'id'    =>(int)$result->id,
                                                    'files' =>$_FILES,
                                                    'field' =>'file',
                                                    'crop'  =>(object)array(
                                                        'x'=>0,
                                                        'y'=>0,
                                                        'h'=>$size[1],
                                                        'w'=>$size[0]
                                                    ))) === true){
                                                $IDs[count($IDs) - 1]->avatar = true;
                                                Logs::log('avatar for group ID#'.$result->id.' attached', 'groups');
                                            }
                                            $BuddyPressGroups   = NULL;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (count($IDs) > 0){
                        $this->groups = (object)array(
                            'list'=>$ids,
                            'full'=>$IDs
                        );
                        $Groups         = NULL;
                        $References     = NULL;
                        Logs::log('created '.count($IDs).' groups', 'groups');
                        return true;
                    }

                }
            }
            $Groups         = NULL;
            $References     = NULL;
            return false;
        }
        private function addMemberships(){
            Logs::log('start', 'memberships');
            foreach($this->groups->full as $group){
                Logs::log('do memberships for group ID#'.$group->group_id, 'memberships');
                $count  = rand(round(count($this->authors->list) / 2), count($this->authors->list) - 1);
                $inside = array();
                do{
                    $author = $this->authors->full[rand(0, count($this->authors->list) - 1)];
                    if (in_array((int)$author->user_id, $inside) === false){
                        if ((int)$author->user_id !== (int)$group->creator){
                            Logs::log('join member ID#'.$author->user_id.' to group ID#'.$group->group_id, 'memberships');
                            groups_join_group((int)$group->group_id, $author->user_id);
                        }
                    }
                    $count --;
                }while($count > 0);
            }
            Logs::log('finish', 'memberships');
        }
        private function addFriendships(){
            Logs::log('start', 'friendships');
            foreach($this->authors->full as $author){
                Logs::log('looking friends for member ID#'.$author->user_id, 'friendships');
                $count      = rand(round(count($this->authors->list) / 2), count($this->authors->list) - 1);
                $already    = array();
                do{
                    $friend = $this->authors->full[rand(0, count($this->authors->list) - 1)];
                    if (in_array((int)$friend->user_id, $already) === false){
                        if ((int)$friend->user_id !== (int)$author->user_id){
                            Logs::log('member ID#'.$author->user_id.' get friend ID#'.$friend->user_id, 'friendships');
                            friends_add_friend( (int)$author->user_id, (int)$friend->user_id, true );
                        }
                    }
                    $count --;
                }while($count > 0);
            }
            Logs::log('finish', 'friendships');
        }
        private function addCategories(){
            $References = new References();
            $categories = $References->get('categories');
            $References = NULL;
            if ($categories !== false) {
                if (isset($categories->category) !== false) {
                    Logs::log('start', 'categories');
                    if (function_exists('wp_create_category') === false){
                        require_once(\Pure\Configuration::instance()->dir(ABSPATH.'/wp-admin/includes/taxonomy.php'));
                    }
                    $categories = $categories->category;
                    $IDs        = array();
                    foreach($categories as $category){
                        $id = wp_create_category($category);
                        if ((int)$id > 0){
                            Logs::log('add category ID#'.$id, 'categories');
                            $IDs[] = $id;
                        }
                    }
                    if (count($IDs) > 0){
                        $this->categories = $IDs;
                        Logs::log('created '.count($IDs).' categories', 'categories');
                        return true;
                    }
                }
            }
            return false;
        }
        private function addTags(){
            $References = new References();
            $tags       = $References->get('tags');
            $References = NULL;
            if ($tags !== false) {
                if (isset($tags->tag) !== false) {
                    Logs::log('start', 'tags');
                    if (function_exists('wp_insert_term') === false){
                        require_once(\Pure\Configuration::instance()->dir(ABSPATH.'/wp-admin/includes/taxonomy.php'));
                    }
                    $tags   = $tags->tag;
                    $IDs    = array();
                    foreach($tags as $tag){
                        $id = wp_insert_term(
                            $tag,
                            'post_tag',
                            array()
                        );
                        if (is_wp_error( $id ) === false){
                            if (isset($id['term_id']) !== false){
                                if ((int)$id['term_id'] > 0){
                                    Logs::log('add tag ID#'.$id['term_id'], 'tags');
                                    $IDs[] = $id['term_id'];
                                }
                            }
                        }
                    }
                    if (count($IDs) > 0){
                        Logs::log('created '.count($IDs).' tags', 'tags');
                        $this->tags = $IDs;
                        return true;
                    }
                }
            }
            return false;
        }
        private function addPosts(){
            $PostsGenerator = new PostGenerator($this->authors, $this->tags, $this->categories);
            $this->posts    = $PostsGenerator->posts();
            $PostsGenerator = NULL;
        }
        private function save(){
            $data = (object)array(
                'authors'   =>$this->authors,
                'groups'    =>$this->groups,
                'categories'=>$this->categories,
                'tags'      =>$this->tags,
                'posts'     =>$this->posts
            );
            update_option( $this->option, $data );
        }
        public function isDone(){
            if (get_option($this->option) !== false){
                Logs::log('Cannot start import, because import was before. Check logs in table (your server database) WP_OPTIONS, option ['.$this->option.']', 'core');
                return true;
            }
            return false;
        }
        public function isCan(){
            $tmp_dir    = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
            $result     = @file_put_contents($tmp_dir.'/text.txt','data');
            if ($result !== false){
                @unlink($tmp_dir.'/text.txt');
                $path       = substr(__DIR__, 0, (stripos(__DIR__, 'wp-content') - 1));
                $result     = @file_put_contents($path.'/wp-content/uploads/text.txt','data');
                if ($result !== false){
                    @unlink($path.'/wp-content/uploads/text.txt');
                    return true;
                }
                return '../wp-content/uploads/';
            }
            return $tmp_dir;
        }
        public function import($logs = false, $warning_reports = false){
            if ($warning_reports === false){
                error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
            }else{
                error_reporting(E_ALL);
                ini_set('display_errors',1);
                ini_set('display_startup_errors',1);
            }
            Logs::$logs = $logs;
            Logs::clear();
            if ($this->isDone() === false){
                $permissions = $this->isCan();
                if ($permissions === true){
                    $References = new References();
                    if (($result = $References->validate()) === true) {
                        if ($this->addAuthors() !== false){
                            if ($this->addGroups() !== false){
                                $this->addMemberships();
                                $this->addFriendships();
                                $this->addCategories();
                                $this->addTags();
                                $this->addPosts();
                                $this->save();
                                Logs::log('finished', 'core');
                            }
                        }
                    }else{
                        $References = NULL;
                        return $result;
                    }
                }else{
                    Logs::log('Cannot start import. Have not permissions to write into: ['.$permissions.']', 'core');
                }
            }
        }
    }
    class PostGenerator{
        private $authors;
        private $tags;
        private $categories;
        private $posts_ids;
        private $PostProvider;
        function __construct($authors, $tags, $categories){
            $this->authors      = $authors;
            $this->tags         = $tags;
            $this->categories   = $categories;
            $this->posts_ids    = array();
            \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
            $this->PostProvider = new \Pure\Components\PostTypes\Post\Module\Core();
            //Attach resources
            require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/image.php')   );
            require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/file.php')    );
            require_once( \Pure\Configuration::instance()->dir(ABSPATH.'wp-admin/includes/media.php')   );
        }
        private function attachment($post_id, $image){
            $attachment_id = $this->PostProvider->unsafeAddAttachment($post_id, $image);
            if ($attachment_id === false){
                Logs::log('fail make attachment for post ID#'.$post_id.'. Unknown error.', 'posts');
            }else if ((int)$attachment_id > 0 ){
                $this->posts_ids[] = (object)array(
                    'type'  =>'attachment',
                    'parent'=>(int)$post_id,
                    'id'    =>(int)$attachment_id
                );
                Logs::log('created attachment ID#'.$attachment_id.' for post ID#'.$post_id, 'posts');
                return (int)$attachment_id;
            }else{
                Logs::log('fail make attachment for post ID#'.$post_id.', WP_ERROR:: ['.$attachment_id.']', 'posts');
            }
            return false;
        }
        private function post($post_title){
            //$author     = (object)array('user_id'=>1);
            $author     = $this->authors->full[rand(0, count($this->authors->list) - 1)];
            $category   = $this->categories[rand(0, count($this->categories) - 1)];
            $tags       = array();
            for($index = 0, $max_index = rand(1, round((count($this->tags) - 1) / 2)); $index < $max_index; $index ++){
                $tag = $this->tags[rand(0, count($this->tags) - 1)];
                if (in_array($tag, $tags) === false){
                    $tags[] = $tag;
                }
            }
            $References = new References();
            $images     = $References->getImages();
            $texts      = $References->get('texts');
            $embeds     = $References->get('embeds');
            $References = NULL;
            if ($texts !== false && $embeds !== false) {
                if (isset($texts->text) !== false && isset($embeds->embed) !== false) {
                    $texts      = $texts->text;
                    $embeds     = $embeds->embed;
                    $post_id    = $this->PostProvider->unsafeAddEmptyDraft($author->user_id, 'post');
                    if ($post_id !== false){
                        $lucky = rand(0, 90);
                        if ($lucky >= 0 && $lucky <= 30){
                            //Images in post
                            $post_type = 'images';
                        }else if($lucky > 30 && $lucky <= 60){
                            //Gallery in post
                            $post_type = 'gallery';
                        }else{
                            //Embed in post
                            $post_type = 'embed';
                        }
                        if ($post_type === 'images' || $post_type === 'gallery' || true){
                            //Generate images
                            $_images    = array();
                            for($index = 0, $max_index = rand(2, (count($images) > 5 ? 5 : count($images))); $index < $max_index; $index ++){
                                $image = $images[rand(0, count($images) - 1)];
                                if (in_array($image, $_images) === false){
                                    $_images[] = $image;
                                }
                            }
                            $attachments = array();
                            foreach($_images as $image){
                                $attachment_id = $this->attachment($post_id, $image);
                                if ($attachment_id !== false){
                                    $attachments[] = $attachment_id;
                                }
                            }
                        }
                        $post_content = '';
                        switch($post_type){
                            case 'images':
                                foreach($attachments as $attachment){
                                    $attachment_url = wp_get_attachment_image_src( (int)$attachment, 'full', false );
                                    $attachment_url = (is_array($attachment_url) !== false ? $attachment_url[0] : '');
                                    if ($attachment_url !== ''){
                                        $imageHTML  = '[caption id="attachment_'.$attachment.'" align="aligncenter" width="1024"]<a href="'.$attachment_url.'"><img class="wp-image-9 size-large" src="'.$attachment_url.'" alt="#" width="1024" height="570" /></a> Demo image[/caption]';
                                    }else{
                                        $imageHTML  = '';
                                    }
                                    $post_content .=    $texts[rand(0, count($texts) - 1)]->value.
                                                        PHP_EOL.PHP_EOL.$imageHTML.PHP_EOL.PHP_EOL;
                                }
                                break;
                            case 'gallery':
                                $post_content .=    $texts[rand(0, count($texts) - 1)]->value.
                                                    PHP_EOL.PHP_EOL.'[gallery ids="'.implode(',', $attachments).'"]'.PHP_EOL.PHP_EOL.
                                                    $texts[rand(0, count($texts) - 1)]->value;
                                break;
                            case 'embed':
                                $_embeds = array();
                                for($index = 0, $max_index = rand(1, round((count($embeds) - 1) / 2)); $index < $max_index; $index ++){
                                    $embed = $embeds[rand(0, count($embeds) - 1)];
                                    if (in_array($embed, $_embeds) === false){
                                        $_embeds[] = $embed;
                                    }
                                }
                                for($index = count($_embeds) - 1; $index >= 0; $index --){
                                    $post_content .=    $texts[rand(0, count($texts) - 1)]->value.
                                                        PHP_EOL.PHP_EOL.'[embed]'.$_embeds[$index].'[/embed]'.PHP_EOL.PHP_EOL;
                                }
                                break;
                        }
                        $post_excerpt = '';
                        switch($post_type){
                            case 'images':
                                $post_excerpt = $texts[rand(0, count($texts) - 1)]->value;
                                if (strlen($post_excerpt) > 300){
                                    $post_excerpt = substr($post_excerpt, 0, 300);
                                }
                                break;
                            case 'gallery':
                                if (rand(0,100) > 50){
                                    $post_excerpt = $texts[rand(0, count($texts) - 1)]->value;
                                    if (strlen($post_excerpt) > 300){
                                        $post_excerpt = substr($post_excerpt, 0, 300);
                                    }
                                }
                                break;
                            case 'embed':
                                $post_excerpt = '';
                                break;
                        }
                        $arguments  = array(
                            'comment_status'    => 'open',
                            'post_category'     => array($category),
                            'post_content'      => $post_content,
                            'post_excerpt'      => $post_excerpt,
                            'post_title'        => $post_title,
                            'post_type'         => 'post',
                            'post_status'       => 'publish',
                            'ID'                => $post_id
                        );
                        //Save post
                        $post_id = wp_update_post($arguments);
                        if ((int)$post_id > 0){
                            Logs::log('post ID#'.$post_id.' was saved', 'posts');
                            //Add thumbnail
                            if ($post_excerpt !== ''){
                                $thumbnail_id = $this->attachment($post_id, $images[rand(0, count($images) - 1)]);
                                if ($thumbnail_id !== false){
                                    set_post_thumbnail($post_id, $thumbnail_id);
                                    Logs::log('thumbnail for post ID#'.$post_id.' was set', 'posts');
                                }
                            }
                            $this->posts_ids[] = (object)array(
                                'type'  =>'post',
                                'parent'=>0,
                                'id'    =>(int)$post_id
                            );
                            return (int)$post_id;
                        }
                    }
                }
            }
            return false;
        }
        public function posts(){
            $References = new References();
            $titles     = $References->get('titles');
            $References = NULL;
            if ($titles !== false) {
                if (isset($titles->title) !== false) {
                    $titles = $titles->title;
                    Logs::log('start', 'posts');
                    foreach($titles as $title){
                        $this->post($title);
                    }
                    Logs::log('created '.count($this->posts_ids).' posts', 'posts');
                }
            }
            return $this->posts_ids;
        }
    }
    class References{
        private $directory;
        private $files = array(
            'authors'       =>'authors.xml',
            'groups'        =>'groups.xml',
            'texts'         =>'texts.xml',
            'titles'        =>'titles.xml',
            'tags'          =>'tags.xml',
            'categories'    =>'categories.xml',
            'embeds'        =>'embeds.xml',
        );
        function __construct(){
            $this->directory = ABSPATH.'wp-content/uploads/demo/';
        }
        public function validate(){
            //Check support
            Logs::log('validate PHP functions [START]', 'references');
            if (function_exists('simplexml_load_file') === false || function_exists('libxml_disable_entity_loader') === false){
                Logs::log('validate PHP functions [ERROR]:: Import does not possible. Function [simplexml_load_file] is not available.', 'references');
                return __('Import does not possible. Function [simplexml_load_file] is not available.');
            }
            Logs::log('validate PHP functions [OK]', 'references');
            //Check directory
            Logs::log('validate data [START]', 'references');
            if (file_exists(\Pure\Configuration::instance()->dir($this->directory)) === false){
                Logs::log('validate data [ERROR]:: Cannot find DEMO directory. DEMO directory should be: [wp-content/uploads/demo]', 'references');
                return __('Cannot find DEMO directory. DEMO directory should be: [wp-content/uploads/demo]');
            }
            //Check files
            foreach($this->files as $file){
                if (file_exists(\Pure\Configuration::instance()->dir($this->directory.$file)) === false){
                    Logs::log('validate data [ERROR]:: File ['.$file.'] was not found in [wp-content/uploads/demo]', 'references');
                    return __('File ['.$file.'] was not found in [wp-content/uploads/demo]');
                }
            }
            Logs::log('validate data [OK]', 'references');
            return true;
        }
        public function hasUserAvatar($id){
            if (file_exists(\Pure\Configuration::instance()->dir($this->directory.'avatars/'.$id.'.jpg')) !== false){
                return (object)array(
                    'name'=>$id.'.jpg',
                    'full'=>$this->directory.'avatars/'.$id.'.jpg'
                );
            }
            return false;
        }
        public function hasGroupAvatar($id){
            if (file_exists(\Pure\Configuration::instance()->dir($this->directory.'groups/'.$id.'.jpg')) !== false){
                return (object)array(
                    'name'=>$id.'.jpg',
                    'full'=>$this->directory.'groups/'.$id.'.jpg'
                );
            }
            return false;
        }
        public function get($file_name){
            if (isset($this->files[$file_name]) !== false){
                libxml_disable_entity_loader(false);
                return simplexml_load_file($this->directory.$this->files[$file_name]);
            }
            return false;
        }
        public function getImages(){
            if (file_exists(\Pure\Configuration::instance()->dir($this->directory.'images')) !== false){
                $FileSystem = new \Pure\Resources\FileSystem();
                $files      = $FileSystem->getFilesList($this->directory.'images');
                $FileSystem = NULL;
                foreach($files as $key=>$file){
                    $files[$key] = (object)array(
                        'name'=>$file,
                        'full'=>$this->directory.'images/'.$file
                    );
                }
                return $files;
            }
            return false;
        }
    }
    class Logs{
        public static $logs     = false;
        public static $option   = 'pure_export_demo_logs';
        private static $history;
        public static function clear(){
            Logs::$history = array();
            update_option( Logs::$option, Logs::$history );
        }
        public static function log($message, $author){
            if ($message !== '' && $author !== ''){
                if (is_array(Logs::$history) === false){
                    Logs::$history = array();
                }
                Logs::$history[] = (object)array(
                    'index' =>count(Logs::$history),
                    'log'   =>$message,
                    'author'=>$author,
                    'time'  =>date(date("Y-m-d H:i:s"))
                );
                update_option( Logs::$option, Logs::$history );
                if (Logs::$logs !== false){
                    echo "[".date("H:i:s")."][".$author."]:: ".$message."\r\n";
                }
            }
        }
        public static function get($offset){
            $data = get_option(Logs::$option);
            if ($data !== false){
                if (is_array($data) !== false){
                    if ($offset < count($data)){
                        return array_slice($data, $offset);
                    }else{
                        return array();
                    }
                }
            }
            return false;
        }
    }
}