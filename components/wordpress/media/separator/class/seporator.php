<?php
namespace Pure\Components\WordPress\Media\Separator{
    class Core{
        static function init(){
            add_action('posts_where',   array( '\\Pure\\Components\\WordPress\\Media\\Separator\\Core', 'separate_media_files'));
            add_filter('pre_get_posts', array( '\\Pure\\Components\\WordPress\\Media\\Separator\\Core', 'allow_admins_see_everything_in_console'));
        }
        static function separate_media_files($where){
            global $current_user;
            if( !current_user_can( 'manage_options' ) ) {
                if( is_user_logged_in() ){
                    if( isset( $_POST['action'] ) ){
                        if( $_POST['action'] == 'query-attachments' ){
                            $where .= ' AND post_author='.$current_user->data->ID;
                        }
                    }
                }
            }
            return $where;
            //http://jeffreycarandang.com/tutorials/hide-wordpress-posts-media-uploaded-users/
        }
        static function allow_admins_see_everything_in_console($query) {
            global $pagenow;
            if( ( 'edit.php' != $pagenow && 'upload.php' != $pagenow   ) || !$query->is_admin ){
                return $query;
            }
            if( !current_user_can( 'manage_options' ) ) {
                global $user_ID;
                $query->set('author', $user_ID );
            }
            return $query;
            //http://jeffreycarandang.com/tutorials/hide-wordpress-posts-media-uploaded-users/
        }
    }
}
?>