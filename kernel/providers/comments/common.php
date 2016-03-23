<?php
namespace Pure\Providers\Comments{
    class Common{
        public function validate(&$parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['shown'            ]));
                $result = ($result === false ? false : isset($parameters['maxcount'         ]));
                if ($result !== false){
                    $parameters['make_tree']  = (isset($parameters['make_tree'] ) === false ? false : $parameters['make_tree']);
                }
                return $result;
            }
            return false;
        }
        private function defaults(&$parameters){
            $parameters                 = (is_array($parameters) === false ? array() : $parameters);
            $parameters['add_post_data' ] = (isset($parameters['add_post_data'  ]) === false ? true  : $parameters['add_post_data'  ]);
            $parameters['add_user_data' ] = (isset($parameters['add_user_data'  ]) === false ? true  : $parameters['add_user_data'  ]);
            $parameters['add_excerpt'   ] = (isset($parameters['add_excerpt'    ]) === false ? true  : $parameters['add_excerpt'    ]);
            $parameters['add_DB_fields' ] = (isset($parameters['add_DB_fields'  ]) === false ? false : $parameters['add_DB_fields'  ]);
        }
        private function clear_comment($comment_content){
            \Pure\Components\Tools\Strings\Initialization::instance()->attach(true);
            $Strings    = new \Pure\Components\Tools\Strings\Strings();
            $content    = preg_replace('/\[meme:begin\](.*)\[meme:end\]/i', '',                         $comment_content);
            $content    = preg_replace('/\[attachment:begin\](.*)\[attachment:end\]/i', '',             $content        );
            $content    = preg_replace('/\[quote:begin\](.(?!\[quote:begin\]))*\[quote:end\]/si', '',   $content        );
            $_content   = wp_strip_all_tags($content);
            $content    = $Strings->substr_utf8($_content, 0, 100);
            $content    .= (strlen($content) > 0 ? (strlen($_content) > strlen($content) ? '...' : '') : '');
            $Strings    = NULL;
            return $content;
        }
        private function add_data($comment, $parameters){
            $this->defaults($parameters);
            \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach(true);
            $BuddyPress = new \Pure\Components\BuddyPress\URLs\Core();
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $_comment   = (object)array(
                'comment'   =>(object)array(
                    'id'        =>(int)$comment->comment_ID,
                    'date'      =>(new \DateTime($comment->comment_date))->format('Y-m-d H:i'),
                    'parent'    =>(int)$comment->comment_parent,
                    'value'     =>stripcslashes($comment->comment_content)
                )
            );
            if ($parameters['add_excerpt'] !== false){
                $_comment->comment->excerpt = $this->clear_comment(stripcslashes($comment->comment_content));
            }
            if ($parameters['add_post_data'] !== false){
                $_comment->post = (object)array(
                    'id'        =>$comment->comment_post_ID,
                    'url'       =>get_permalink($comment->comment_post_ID),
                    'title'     =>get_the_title($comment->comment_post_ID),
                );
            }
            if ($parameters['add_user_data'] !== false){
                $_comment->author = (object)array(
                    'id'        =>$comment->user_id,
                    'avatar'    =>($comment->user_id !== 0 ? $WordPress->user_avatar_url((int)$comment->user_id) : false                     ),
                    'posts'     =>($comment->user_id !== 0 ? get_author_posts_url       ((int)$comment->user_id) : ''                        ),
                    'name'      =>($comment->user_id !== 0 ? $WordPress->get_name       ((int)$comment->user_id) : $comment->comment_author  ),
                    'home'      =>($comment->user_id !== 0 ? $BuddyPress->member(get_userdata($comment->user_id)->user_login)       : ''                        ),
                );
            }
            if ($parameters['add_DB_fields'] !== false){
                $_comment->fields = $comment;
            }
            $BuddyPress = NULL;
            $WordPress  = NULL;
            return $_comment;
        }
        public function processing($comments, $parameters, $count){
            $_comments = array();
            foreach ($comments as $comment){
                $_comments[] = $this->add_data($comment, $parameters);
            }
            return (object)array(
                'comments'  =>$_comments,
                'shown'     =>count($_comments),
                'total'     =>$count
            );
        }
        public function tree($comments_object){
            $Tree = new Tree();
            $tree = $Tree->make($comments_object);
            $Tree = NULL;
            return $tree;
        }
        public function create($user_id, $post_id, $parent_comment_id, $comment, $strip_tags = true){
            $Create = new Create();
            $result = $Create->add($user_id, $post_id, $parent_comment_id, $comment, $strip_tags);
            $Create = NULL;
            return $result;
        }
        public function isUserCommentAuthor($comment_id, $user_id){
            $Create = new Create();
            $result = $Create->isUserCommentAuthor($comment_id, $user_id);
            $Create = NULL;
            return $result;
        }
    }
    class Tree{
        private function children($comment_id, $comments_src){
            $children = array();
            foreach($comments_src as $comment) {
                if ($comment->comment->parent === $comment_id){
                    $children[] = $comment;
                }
            }
            return (count($children) > 0 ? $children : false);
        }
        public function make($comments_object){
            $_comments_trg  = array();
            $comments_src   = $comments_object->comments;
            foreach($comments_src as $comment){
                $_comments_trg[]    = $comment;
                $children           = $this->children($comment->comment->id, $comments_src);
                if ($children !== false){
                    $_comments_trg[count($_comments_trg) - 1]->children = $children;
                }else{
                    $_comments_trg[count($_comments_trg) - 1]->children = false;
                }
            }
            $comments_trg = array();
            foreach($_comments_trg as $comment){
                if ($comment->comment->parent === 0){
                    $comments_trg[] = $comment;
                }
            }
            return (object)array(
                'comments'  =>$comments_trg,
                'shown'     =>count($comments_trg),
                'total'     =>$comments_object->total
            );
        }
    }
    class Create {
        private function getRootCommentID($post_id, $comment_id){
            if ((int)$comment_id > 0 && (int)$post_id > 0){
                global $wpdb;
                $selector   =   'SELECT '.
                                    'wp_comments.comment_ID, '.
                                    'wp_commentmeta.meta_value AS comment_root_id, '.
                                    'wp_commentmeta.meta_key '.
                                'FROM '.
                                    'wp_comments, '.
                                    'wp_commentmeta '.
                                'WHERE '.
                                    'wp_comments.comment_post_ID = '.(int)$post_id.' '.
                                    'AND wp_comments.comment_ID = '.(int)$comment_id.' '.
                                    'AND wp_comments.comment_ID = wp_commentmeta.comment_id';
                $result     = $wpdb->get_results($selector);
                if (is_array($result) !== false){
                    return (count($result) === 0 ? (int)$comment_id : (int)$result[0]->comment_root_id);
                }
            }
            return false;
        }
        public function isUserCommentAuthor($comment_id, $user_id){
            if ((int)$comment_id > 0 && (int)$user_id > 0){
                global $wpdb;
                $selector   =   'SELECT '.
                                    '* '.
                                'FROM '.
                                    'wp_comments '.
                                'WHERE '.
                                    'user_id = '.(int)$user_id.' '.
                                    'AND comment_ID = '.(int)$comment_id;
                $result     = $wpdb->query($selector);
                return ((int)$result > 0 ? true : false);
            }
            return false;
        }
        private function isPostHasComment($post_id, $comment_id){
            if ((int)$comment_id > 0 && (int)$post_id > 0){
                global $wpdb;
                $selector   =   'SELECT '.
                                    '* '.
                                'FROM '.
                                    'wp_comments '.
                                'WHERE '.
                                    'comment_post_ID = '.(int)$post_id.' '.
                                    'AND comment_ID = '.(int)$comment_id;
                $count      = $wpdb->query($selector);
                if ((int)$count > 0){
                    return true;
                }
            }
            return false;
        }
        public function add($user_id, $post_id, $parent_comment_id, $_comment, $strip_tags = true){
            if ((int)$user_id > 0 && (int)$post_id > 0){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)($current !== false ? $current->ID : -1) === (int)$user_id) {
                    if ((int)$parent_comment_id > 0){
                        if ($this->isPostHasComment($post_id, $parent_comment_id) === false){
                            return false;
                        }else{
                            $root_comment_id = $this->getRootCommentID($post_id, $parent_comment_id);
                            if ($root_comment_id === false){
                                return false;
                            }
                        }
                    }
                    $comment = addslashes($_comment);
                    if ($strip_tags !== false){
                        $comment = wp_strip_all_tags($comment);
                    }
                    if (strlen($comment) > 1){
                        \Pure\Components\Tools\IPs\Initialization::instance()->attach(true);
                        $IPs        = new \Pure\Components\Tools\IPs\Core();
                        $created    = date("Y-m-d H:i:s");
                        $arguments  = array(
                            'comment_post_ID'       => (int)$post_id,
                            'comment_content'       => $comment,
                            'comment_type'          => '',
                            'comment_date'          => $created,
                            'comment_author'        => $current->name,
                            'comment_author_email'  => $current->user_email,
                            'comment_author_url'    => $current->user_url,
                            'user_id'               => (int)$user_id,
                            'comment_author_IP'     => $IPs->getClientIP(),
                            'comment_agent'         => $_SERVER['HTTP_USER_AGENT'],
                            'comment_approved'      => 1,
                        );
                        $IPs                            = NULL;
                        $arguments['comment_parent']    = ((int)$parent_comment_id > 0 ? (int)$parent_comment_id : 0);
                        $result                         = wp_insert_comment($arguments);
                        if ((int)$result > 0){
                            if ((int)$parent_comment_id > 0){
                                update_comment_meta((int)$result, 'comment_root', (int)$root_comment_id );
                            }
                            return (object)array(
                                'id'        =>(int)$result,
                                'parent'    =>(int)$parent_comment_id,
                                'comment'   =>stripcslashes($comment),
                                'post_id'   =>(int)$post_id,
                                'date'      =>$created
                            );
                        }
                    }
                }
            }
            return false;
        }
    }
}
?>