<?php
namespace Pure\Templates\Comments\Post{
    class A{
        private $user;
        private $settings;
        private $ManaTemplate;
        private $manaCache = false;
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post_id  ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->post     ) !== false ? true : false));
                return $result;
            }
            return false;
        }
        private function permissions(){
            if ($this->user !== false){
                \Pure\Components\Relationships\Mana\Initialization::instance()->attach();
                $Mana                   = new \Pure\Components\Relationships\Mana\Provider();
                $this->user->mana       = $Mana->getUserPermissions($this->user->ID);
                $Mana                   = NULL;
            }
        }
        private function getData($parameters){
            $Comments = \Pure\Providers\Comments\Initialization::instance()->get('last_in_posts');
            $comments = $Comments->get(
                array(
                    'from_date'     =>date('Y-m-d'),
                    'days'          =>9999,
                    'shown'         =>0,
                    'maxcount'      =>$this->settings->show_on_page,
                    'targets_array' =>array($parameters->post_id),
                    'add_post_data' =>false,
                    'add_user_data' =>true,
                    'add_excerpt'   =>false,
                    'add_DB_fields' =>false,
                    'make_tree'     =>true,
                )
            );
            $Comments = NULL;
            return $comments;
        }
        private function getQuote($_quote){
            $author = '';
            $date   = '';
            $quote  = $_quote;
            $quote  = preg_replace('/\[quote:begin\]/i',    '', $quote);
            $quote  = preg_replace('/\[quote:end\]/i',      '', $quote);
            preg_match('/\[author:begin\](.*)\[author:end\]/i', $_quote, $matches);
            if (is_array($matches) !== false) {
                if (count($matches) === 2) {
                    $author = $matches[1];
                    $quote  = preg_replace('/\[author:begin\](.*)\[author:end\]/i', '', $quote);
                }
            }
            preg_match('/\[date:begin\](.*)\[date:end\]/', $_quote, $matches);
            if (is_array($matches) !== false) {
                if (count($matches) === 2) {
                    $date   = $matches[1];
                    $quote  = preg_replace('/\[date:begin\](.*)\[date:end\]/i', '', $quote);
                }
            }
            if ($quote !== '' && $author !== '' && $date !== ''){
                return  '<div data-post-element-type="Pure.Posts.Comment.A.Quote">'.
                            '<p data-post-element-type="Pure.Posts.Comment.A.Quote">'.nl2br(esc_html($quote)).'</p>'.
                            '<p data-post-element-type="Pure.Posts.Comment.A.Quote.Author">'.$author.', '.
                                '<span data-post-element-type="Pure.Posts.Comment.A.Quote.Date">'.$date.'</span>'.
                            '</p>'.
                        '</div>';
            }
            return false;
        }
        private function getComment($commentValue, $comment_id){
            $comment = preg_replace('/[\n\r\s]*$/', '', $commentValue   );
            $comment = preg_replace('/^[\n\r\s]*/', '', $comment        );
            return '<p data-post-element-type="Pure.Posts.Comment.A.Comment" data-engine-comment-commentID="'.$comment_id.'" data-engine-comment-element="Comment.Value">'.nl2br(esc_html($comment)).'</p>';
        }
        private function parseCommentValue($commentValue, $comment_id){
            preg_match_all('/\[quote:begin\](.(?!\[quote:begin\]))*\[quote:end\]/si', $commentValue, $matches);
            $_parts = preg_split('/\[quote:begin\](.(?!\[quote:begin\]))*\[quote:end\]/si', $commentValue, -1, PREG_SPLIT_OFFSET_CAPTURE);
            $parts  = array();
            foreach($_parts as $key=>$part){
                $is_empty = (preg_replace('/[\r\n\s]/', '', $part[0]) === '' ? true : false);
                if ($key === 0){
                    $parts[] = ($part[1] === 0 ? ($part[0] === '' ? false : ($is_empty === false ? $part[0] : false)) : false);
                }else{
                    $parts[] = ($part[0] === '' ? false : ($is_empty === false ? $part[0] : false));
                }
            }
            //echo var_dump($parts);
            if (is_array($matches) !== false) {
                if (count($matches) === 2) {
                    if (count($matches[0]) > 0){
                        $comment = '';
                        for($index = 0, $max_index = count($parts); $index < $max_index; $index ++){
                            $comment .= ($parts[$index] !== false ? $this->getComment($parts[$index], $comment_id) : '');
                            if (isset($matches[0][$index]) !== false){
                                $quote = $this->getQuote($matches[0][$index]);
                                $comment .= ($quote !== false ? $quote : '');
                            }
                        }
                        return $comment;
                    }
                }
            }
            return $this->getComment($commentValue, $comment_id);
        }
        private function parseComment($commentValue, $comment_id){
            //Memes
            preg_match('/\[meme:begin\](.*)\[meme:end\]/i', $commentValue, $matches);
            if (is_array($matches) !== false){
                if (count($matches) === 2){
                    return  '<div data-post-element-type="Pure.Posts.Comment.A.Meme" data-engine-comment-commentID="'.$comment_id.'" data-engine-comment-element="Comment.Meme">'.
                                '<img alt="" data-post-element-type="Pure.Posts.Comment.A.Meme" src="'.esc_html($matches[1]).'"/>'.
                            '</div>';
                }
            }
            //Attachment
            preg_match('/\[attachment:begin\](.*)\[attachment:end\]/i', $commentValue, $matches);
            if (is_array($matches) !== false){
                if (count($matches) === 2){
                    return  '<div data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment.Container" data-engine-comment-commentID="'.$comment_id.'" data-engine-comment-element="Comment.Attachment">'.
                                '<img alt="" data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment" src="'.esc_html($matches[1]).'"/>'.
                            '</div>'.
                            $this->parseCommentValue(preg_replace('/\[attachment:begin\](.*)\[attachment:end\]/', '', $commentValue), $comment_id);
                }
            }
            $this->parseCommentValue(esc_html($commentValue), $comment_id);
            //Just comment
            return $this->parseCommentValue(preg_replace('/\[attachment:begin\](.*)\[attachment:end\]/', '', $commentValue), $comment_id);
        }
        private function innerHTMLTitle($parameters, $comments){
            return  '<!--BEGIN: Comment.Title.A -->'.
                    '<div data-post-element-type="Pure.Posts.Comments.Title.A">'.
                        '<p data-post-element-type="Pure.Posts.Comments.Title.A">'.__('Comments', 'pure').'</p>'.
                        '<p data-post-element-type="Pure.Posts.Comments.Title.A.Info">'.__('total', 'pure').': <span data-engine-comment-element="Comments.Counter.Total" data-engine-comment-postID="'.$parameters->post_id.'">'.$comments->total.'</span></p>'.
                    '</div>'.
                    '<!--END: Comment.Title.A -->';
        }
        private function innerHTMLRootCommentsMark($parameters){
            return  '<!--BEGIN: New sent root comments mark -->'.
                    '<div data-post-element-type="Pure.Posts.Comment.A.EditorMark" data-engine-comment-root-mark="'.$parameters->post_id.'"></div>'.
                    '<!--END: New sent root comments mark -->';
        }
        private function innerHTMLRootEditor($parameters){
            $innerHTML = '';
            if ($this->user !== false){
                $innerHTML =    '<!--BEGIN: Comment.A.Add -->'.
                                '<div data-post-element-type="Pure.Posts.Comment.A.Comment.Add">'.
                                    '<input disabled data-post-element-type="Pure.Posts.Comment.A.Controls" type="checkbox" data-type-controls="add_comment"'.
                                            'id="add_comment_to_post_'.$parameters->post_id.'"'.
                                            'data-engine-comment-element="Editor.Caller" '.
                                            'data-engine-comment-postID="'.$parameters->post_id.'" '.
                                            'data-engine-comment-commentID="0" '.
                                            'data-engine-comment-event="change" '.
                                            'data-engine-comment-editorID="add_comment_to_post_'.$parameters->post_id.'"/>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="add_switcher">'.
                                        '<label for="add_comment_to_post_'.$parameters->post_id.'" data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="write"></label>'.
                                        '<p data-post-element-type="Pure.Posts.Comment.A.Comment.Count">'.__('write your comment', 'pure').'</p>'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.Controls.ResetFloat"></div>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="add_editor">'.
                                        '<p data-post-element-type="Pure.Posts.Comment.A.Comment.Title">'.__('Your comment', 'pure').'</p>'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.EditorMark" data-engine-comment-mark="add_comment_to_post_'.$parameters->post_id.'"></div>'.
                                    '</div>'.
                                '</div>'.
                                '<!--END: Comment.A.Add -->';
            }
            return $innerHTML;
        }
        private function innerHTMLIncludedComments($parameters, $parent_comment){
            $innerHTML  = '';
            $children   = $parent_comment->children;
            if ($children !== false){
                $children = array_reverse($children);
                foreach($children as $_comment){
                    $comment    = $_comment->comment;
                    $author     = $_comment->author;
                    $innerHTML .=   '<!--BEGIN: Comment.A -->'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Wrapper" data-engine-comment-postID="'.$parameters->post_id.'" data-engine-comment-commentID="'.$comment->id.'">'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.Left" data-post-comment-type="included">'.
                                            '<div data-post-element-type="Pure.Posts.Comment.A.Avatar" data-post-comment-type="included" style="'.($author->avatar !== false ? 'background-image:url('.$author->avatar.')' : '').'">'.
                                            '</div>';
                    if ((int)$author->id > 0 && (int)$comment->id > 0){
                        $innerHTML .=           $this->ManaTemplate->innerHTML(
                                                    (object)array(
                                                        'object'    =>'comment',
                                                        'object_id' =>$comment->id,
                                                        'user_id'   =>$author->id,
                                                        'data'      =>$this->manaCache
                                                    )
                                                );
                    }
                    $innerHTML .=       '</div>'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.Right">'.
                                            '<p data-post-element-type="Pure.Posts.Comment.A.Name"><a data-engine-comment-element="Comment.Author.Name" href="'.$author->home.'">'.$author->name.'</a> '.__('say', 'pure').'</p>'.
                                            '<p data-post-element-type="Pure.Posts.Comment.A.Date" data-engine-comment-element="Comment.DateTime">'.$comment->date.'</p>'.
                                            $this->parseComment($comment->value, $comment->id);
                    if ($this->user !== false){
                        $innerHTML .=       '<input disabled data-post-element-type="Pure.Posts.Comment.A.Controls" type="checkbox" data-type-controls="editor"'.
                                                    'id="editor_id_'.$comment->id.'"'.
                                                    'data-engine-comment-element="Editor.Caller" '.
                                                    'data-engine-comment-postID="'.$parameters->post_id.'" '.
                                                    'data-engine-comment-commentID="'.$comment->id.'" '.
                                                    'data-engine-comment-event="change" '.
                                                    'data-engine-comment-editorID="editor_id_'.$comment->id.'"/>'.
                                            '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="include_controls">'.
                                                '<label for="editor_id_'.$comment->id.'" data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="reply"></label>'.
                                                '<div data-post-element-type="Pure.Posts.Comment.A.Controls.ResetFloat"></div>'.
                                            '</div>'.
                                            '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="editor">'.
                                                '<div data-post-element-type="Pure.Posts.Comment.A.EditorMark" data-engine-comment-mark="editor_id_'.$comment->id.'"></div>'.
                                            '</div>';
                    }
                    $innerHTML .=           '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="sub_included" data-engine-comment-included="Container" data-engine-comment-commentID="'.$comment->id.'">'.
                                                $this->innerHTMLIncludedComments($parameters, $_comment).
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<!--END: Comment.A -->';
                }
            }
            return $innerHTML;
        }
        private function innerHTMLComment($parameters, $_comment){
            $comment    = $_comment->comment;
            $author     = $_comment->author;
            $children   = $_comment->children;
            $innerHTML  =   '<!--BEGIN: Comment.A -->'.
                            '<div data-post-element-type="Pure.Posts.Comment.A.Wrapper" data-engine-comment-postID="'.$parameters->post_id.'" data-engine-comment-commentID="'.$comment->id.'" data-engine-comment-type="root">'.
                                '<div data-post-element-type="Pure.Posts.Comment.A.Left">'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Avatar" style="'.($author->avatar !== false ? 'background-image:url('.$author->avatar.')' : '').'">'.
                                    '</div>';
            if ((int)$author->id > 0 && (int)$comment->id > 0){
                $innerHTML .=           $this->ManaTemplate->innerHTML(
                                            (object)array(
                                                'object'    =>'comment',
                                                'object_id' =>$comment->id,
                                                'user_id'   =>$author->id,
                                                'data'      =>$this->manaCache
                                            )
                                        );
            }
            $innerHTML .=       '</div>'.
                                '<div data-post-element-type="Pure.Posts.Comment.A.Right">'.
                                    '<p data-post-element-type="Pure.Posts.Comment.A.Name"><a data-engine-comment-element="Comment.Author.Name" href="'.$author->home.'">'.$author->name.'</a> '.__('say', 'pure').'</p>'.
                                    '<p data-post-element-type="Pure.Posts.Comment.A.Date" data-engine-comment-element="Comment.DateTime">'.$comment->date.'</p>'.
                                    $this->parseComment($comment->value, $comment->id).
                                    '<input id="switcher_id_'.$comment->id.'" data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="switcher" type="checkbox"/>';
            if ($this->user !== false){
                $innerHTML .=       '<input disabled data-post-element-type="Pure.Posts.Comment.A.Controls" type="checkbox" data-type-controls="editor"'.
                                            'id="editor_id_'.$comment->id.'"'.
                                            'data-engine-comment-element="Editor.Caller" '.
                                            'data-engine-comment-postID="'.$parameters->post_id.'" '.
                                            'data-engine-comment-commentID="'.$comment->id.'" '.
                                            'data-engine-comment-event="change" '.
                                            'data-engine-comment-editorID="editor_id_'.$comment->id.'"/>';
            }
            $innerHTML .=           '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="switcher">'.
                                        '<label for="switcher_id_'.$comment->id.'" data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="switcher" '.($children !== false ? 'data-button-light' : '').' data-engine-comment-included-flag="data-button-light"></label>'.
                                        '<p data-post-element-type="Pure.Posts.Comment.A.Comment.Count">'.__('include', 'pure').' <span data-engine-comment-included="Count">'.($children !== false ? count($children) : '').'</span></p>'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.Controls.ResetFloat"></div>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="controls">'.
                                        '<label for="switcher_id_'.$comment->id.'" data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="cancel"></label>';
            if ($this->user !== false){
                $innerHTML .=           '<label for="editor_id_'.$comment->id.'" data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="reply"></label>';
            }
            $innerHTML .=               '<div data-post-element-type="Pure.Posts.Comment.A.Controls.ResetFloat"></div>'.
                                    '</div>';
            if ($this->user !== false){
                $innerHTML .=       '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="editor">'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.EditorMark" data-engine-comment-mark="editor_id_'.$comment->id.'"></div>'.
                                    '</div>';
            }
            $innerHTML .=           '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="included" data-engine-comment-included="Container" data-engine-comment-commentID="'.$comment->id.'">'.
                                        $this->innerHTMLIncludedComments($parameters, $_comment).
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                            '<!--END: Comment.A -->';
            return $innerHTML;
        }
        private function innerHTMLComments($parameters, $comments){
            $innerHTML = '';
            foreach($comments->comments as $comment){
                $innerHTML .= $this->innerHTMLComment($parameters, $comment);
            }
            return $innerHTML;
        }
        private function innerHTMLMoreMark($parameters){
            $innerHTML =    '<!--BEGIN: Comment.A.More.Mark -->'.
                            '<div data-post-element-type="Pure.Posts.Comment.A.EditorMark" data-engine-comment-more-mark="'.$parameters->post_id.'"></div>'.
                            '<!--END: Comment.A.More.Mark  -->';
            return $innerHTML;
        }
        private function innerHTMLMore($parameters, $comments){
            $innerHTML =    '<!--BEGIN: Comment.A.More -->'.
                            '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="more">'.
                                '<label data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="more_all" '.
                                    'data-engine-comment-element="Comments.More.All" '.
                                    'data-engine-comment-more-shown="'.$comments->shown.'" '.
                                    'data-engine-comment-postID="'.$parameters->post_id.'" ></label>'.
                                '<label data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="more" '.
                                    'data-engine-comment-element="Comments.More.Package" '.
                                    'data-engine-comment-more-shown="'.$comments->shown.'" '.
                                    'data-engine-comment-postID="'.$parameters->post_id.'" ></label>'.
                                '<p data-post-element-type="Pure.Posts.Comment.A.Comment.Count">'.__('comments', 'pure').': '.
                                    '<span data-engine-comment-element="Comments.Counter.Shown" data-engine-comment-postID="'.$parameters->post_id.'">'.$comments->shown.'</span> / '.
                                    '<span data-engine-comment-element="Comments.Counter.Total" data-engine-comment-postID="'.$parameters->post_id.'">'.$comments->total.'</span>'.
                                '</p>'.
                                '<div data-post-element-type="Pure.Posts.Comment.A.Controls.ResetFloat"></div>'.
                            '</div>'.
                            '<!--END: Comment.A.More -->';
            return $innerHTML;
        }
        private function makeManaCache($comments){
            $data       = array();
            $processing = function(&$data, $comments) use (&$processing){
                foreach($comments as $comment){
                    if ((int)$comment->author->id > 0 && (int)$comment->comment->id > 0){
                        $data[$comment->comment->id] = (object)array(
                            'object_id' =>$comment->comment->id,
                            'user_id'   =>$comment->author->id
                        );
                    }
                    if ($comment->children !== false){
                        $processing($data, $comment->children);
                    }
                }
            };
            $processing($data, $comments->comments);
            if (count($data) > 0){
                \Pure\Components\Relationships\Mana\Initialization::instance()->attach(true);
                $Provider           = new \Pure\Components\Relationships\Mana\Provider();
                $this->manaCache    = $Provider->fillDataWithObjects(
                    (object)array(
                        'object'=>'comment',
                        'data'   =>$data
                    )
                );
                $Provider           = NULL;
            }
        }
        private function resources($parameters){
            \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
            \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.allowAttachment',
                $this->settings->allow_attachment,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.allowMemes',
                $this->settings->allow_memes,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.maxLength',
                $this->settings->max_length,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.hotUpdate',
                $this->settings->hot_update,
                false,
                true
            );
            \Pure\Components\Dialogs\B\Initialization::instance()->attach(false, 'after');
            \Pure\Components\WordPress\Media\Resources\Initialization::instance()->attach(false, 'after');
            \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('A', false, 'after');
            \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('D', false, 'after');
            \Pure\Components\Attacher\Module\Attacher::instance()->addINIT(
                'pure.comments.posts.A',
                false,
                true
            );
            //Define request settings
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/settings/request.comments.requests.php'));
            $Settings = new \Pure\Requests\Comments\Requests\Settings\Initialization();
            $Settings->init((object)array(
                    'user_id'=>($this->user !== false ? $this->user->ID : 0),
                    'post_id'=>$parameters->post_id
                )
            );
            $Settings = NULL;
        }
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $parameters     = \Pure\Components\WordPress\Settings\Instance::instance()->settings->comments->properties;
            $this->settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
        }
        public function messageInnerHTML($message){
            return  '<div data-post-element-type="Pure.Posts.Comment.A.Information">'.
                        '<p data-post-element-type="Pure.Posts.Comment.A.Information">'.$message.'</p>'.
                    '</div>';
        }
        private function buildInnerHTML($parameters){
            $innerHTML  = '';
            $comments   = $this->getData($parameters);
            if ($comments !== false){
                $this->ManaTemplate = \Pure\Templates\Mana\Icon\Initialization::instance()->get('A');
                $this->makeManaCache($comments);
                $this->resources($parameters);
                $innerHTML .= $this->innerHTMLTitle             ($parameters, $comments);
                $innerHTML .= $this->innerHTMLRootEditor        ($parameters);
                $innerHTML .= $this->innerHTMLRootCommentsMark  ($parameters);
                $innerHTML .= $this->innerHTMLComments          ($parameters, $comments);
                $innerHTML .= $this->innerHTMLMoreMark          ($parameters, $comments);
                $innerHTML .= $this->innerHTMLMore              ($parameters, $comments);
                //Templates
                $Templates  = new ATemplates();
                $Templates->innerHTMLEditorTemplate   ($parameters->post_id, $this->settings, $this->user);
                $Templates->innerHTMLIncludedComment  ($parameters->post_id, $this->user, $this->ManaTemplate);
                $Templates->innerHTMLRootComment      ($parameters->post_id, $this->user, $this->ManaTemplate);
                $Templates->innerQuoteNotification    ($parameters->post_id, $this->user);
                $Templates  = NULL;
            }
            return $innerHTML;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $this->getSettings();
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $this->user = $WordPress->get_current_user();
                $WordPress  = NULL;
                $this->permissions();
                if ($parameters->post->comment_status === 'open'){
                    if ($this->user === false){
                        $innerHTML .= $this->messageInnerHTML(__('To leave some comments you should login or register.', 'pure'));
                    }else{
                        if ($this->user->mana->allow_create->comment === false){
                            //show comments in read mode only, like for unregistered users
                            $this->user = false;
                            $innerHTML .= $this->messageInnerHTML(__('Sorry, but you have not enough mana to leave comments.', 'pure'));
                        }
                    }
                    $innerHTML .= $this->buildInnerHTML($parameters);
                }else{
                    if ($this->user !== false){
                        if ((int)$parameters->post->post_author === (int)$this->user->ID || $this->user->role->is_admin === true){
                            //show comments in read mode only, like for unregistered users
                            $this->user = false;
                            $innerHTML .= $this->messageInnerHTML(__('Comments for this post are denied. But you are an author (or administrator) and you can see it, but cannot add new.', 'pure'));
                            $innerHTML .= $this->buildInnerHTML($parameters);
                        }else{
                            //commend denied
                            $innerHTML .= $this->messageInnerHTML(__('Comments for this post are denied.', 'pure'));
                        }
                    }else{
                        //commend denied
                        $innerHTML .= $this->messageInnerHTML(__('Comments for this post are denied.', 'pure'));
                    }
                }
            }
            return $innerHTML;
        }
    }
    class ATemplates {
        public function innerHTMLEditorTemplate($post_id, $settings, $user){
            $innerHTML = '';
            if ($user !== false){
                $innerHTML =    '<div data-post-element-type="Pure.Posts.Comment.A.Editor.Container" style="display: none;" '.
                                        'data-engine-comment-element="Editor" '.
                                        'data-engine-comment-postID="'.$post_id.'">'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment" data-switch-id="[commentID]">'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment.Label.Container">'.
                                            '<div data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment.Label">'.
                                                '<p data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment.Label">'.__('attachment', 'pure').'</p>'.
                                            '</div>'.
                                        '</div>'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment.Container">'.
                                            '<img alt="" data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment" data-storage-id="[commentID]"/>'.
                                        '</div>'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment.Label.Container">'.
                                            '<div data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment.Label">'.
                                                '<p data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment.Label">'.__('attachment', 'pure').'</p>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<textarea data-post-element-type="Pure.Posts.Comment.A.Controls.TextArea"></textarea>'.
                                    '<label for="[editorID]" data-post-element-type="Pure.Posts.Comment.A.Controls.Button" title="'.__('hide', 'pure').'" data-button-type="cancel"></label>'.
                                    '<label data-post-element-type="Pure.Posts.Comment.A.Controls.Button" title="'.__('quote', 'pure').'" data-button-type="quote" data-engine-comment-element="Editor.Button.Quote"></label>';
                if($settings->allow_memes === 'on'){
                    $innerHTML .=   '<label data-post-element-type="Pure.Posts.Comment.A.Controls.Button" title="'.__('meme', 'pure').'" data-button-type="meme" data-engine-comment-element="Editor.Button.Meme"></label>';
                }
                $innerHTML .=       '<label data-post-element-type="Pure.Posts.Comment.A.Controls.Button" title="'.__('send', 'pure').'" data-button-type="send" data-engine-comment-element="Editor.Button.Send"></label>';
                if($settings->allow_attachment === 'on'){
                    $innerHTML .=   '<label data-post-element-type="Pure.Posts.Comment.A.Controls.Button" title="'.__('image', 'pure').'" data-button-type="attachment" '.
                                        'data-engine-comment-element="Editor.Button.Attachment" '.
                                        'pure-wordpress-media-images-add-selector="img[data-storage-id=|[commentID]|]"'.
                                        'pure-wordpress-media-images-switch-selector="*[data-switch-id=|[commentID]|]"'.
                                        'data-align-direction="left">'.
                                    '</label>'.
                                    '<label data-post-element-type="Pure.Posts.Comment.A.Controls.Button" title="'.__('image', 'pure').'" data-button-type="remove_attachment" '.
                                        'data-engine-comment-element="Editor.Button.Attachment" '.
                                        'pure-wordpress-media-images-remove-selector="img[data-storage-id=|[commentID]|]" '.
                                        'pure-wordpress-media-images-switch-selector="*[data-switch-id=|[commentID]|]" '.
                                        'data-switch-id="[commentID]" '.
                                        'data-align-direction="left">'.
                                    '</label>';
                }
                $innerHTML .=       '<div data-post-element-type="Pure.Posts.Comment.A.Controls.ResetFloat"></div>'.
                                '</div>';
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.comments.posts.configuration.templates.editor.'.$post_id,
                    base64_encode($innerHTML),
                    false,
                    true
                );
            }
            return $innerHTML;
        }
        public function innerQuoteNotification($post_id, $user){
            $innerHTML = '';
            if ($user !== false){
                $innerHTML =    '<div data-post-element-type="Pure.Posts.Comment.A.Quote.Notification" data-engine-comment-element="Quote.Notification" style="display: none;">'.
                                    __('Quote was saved', 'pure').
                                '</div>';
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.comments.posts.configuration.templates.quote.'.$post_id,
                    base64_encode($innerHTML),
                    false,
                    true
                );
            }
            return $innerHTML;
        }
        public function innerHTMLRootComment($post_id, $user, $manaTemplate){
            /*
             * FIELDS
             * [name]
             * [avatar]
             * [date]
             * [comment]
             * [comment_id]
             * [post_id]
             */
            $innerHTML =    '<div data-post-element-type="Pure.Posts.Comment.A.Wrapper" data-engine-comment-element="Comment.Root" data-engine-comment-postID="'.$post_id.'" data-engine-comment-type="root" style="display:none;">'.
                                '<div data-post-element-type="Pure.Posts.Comment.A.Left">'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Avatar" style="background-image:url([avatar])">'.
                                    '</div>'.
                                    $manaTemplate->markInnerHTML('comment', '[comment_id]').
                                '</div>'.
                                '<div data-post-element-type="Pure.Posts.Comment.A.Right">'.
                                    '<p data-post-element-type="Pure.Posts.Comment.A.Name"><a data-engine-comment-element="Comment.Author.Name" href="[home]">[name]</a> '.__('say', 'pure').'</p>'.
                                    '<p data-post-element-type="Pure.Posts.Comment.A.Date" data-engine-comment-element="Comment.DateTime">[date]</p>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Meme" data-engine-comment-commentID="[comment_id]" data-engine-comment-element="Comment.Meme">'.
                                        '<img alt="" data-post-element-type="Pure.Posts.Comment.A.Meme" src="[meme]"/>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment.Container" data-engine-comment-commentID="[comment_id]" data-engine-comment-element="Comment.Attachment">'.
                                        '<img alt="" data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment" src="[attachment]"/>'.
                                    '</div>'.
                                    '<p data-post-element-type="Pure.Posts.Comment.A.Comment" data-engine-comment-commentID="[comment_id]" data-engine-comment-element="Comment.Value">[comment]</p>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Quote" data-engine-comment-commentID="[comment_id]" data-engine-comment-element="Comment.Quote">'.
                                        '<p data-post-element-type="Pure.Posts.Comment.A.Quote">[quote]</p>'.
                                        '<p data-post-element-type="Pure.Posts.Comment.A.Quote.Author">[quote_author], '.
                                            '<span data-post-element-type="Pure.Posts.Comment.A.Quote.Date">[quote_date]</span>'.
                                        '</p>'.
                                    '</div>'.
                                    '<input id="switcher_id_[comment_id]" data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="switcher" type="checkbox"/>';
            if ($user !== false){
                $innerHTML .=       '<input data-post-element-type="Pure.Posts.Comment.A.Controls" type="checkbox" data-type-controls="editor"'.
                                            'id="editor_id_[comment_id]"'.
                                            'data-engine-comment-element="Editor.Caller" '.
                                            'data-engine-comment-postID="[post_id]" '.
                                            'data-engine-comment-commentID="[comment_id]" '.
                                            'data-engine-comment-event="change" '.
                                            'data-engine-comment-editorID="editor_id_[comment_id]"/>';
            }
            $innerHTML .=           '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="switcher">'.
                                        '<label for="switcher_id_[comment_id]" data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="switcher" data-engine-comment-included-flag="data-button-light"></label>'.
                                        '<p data-post-element-type="Pure.Posts.Comment.A.Comment.Count">'.__('include', 'pure').' <span data-engine-comment-included="Count"></span></p>'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.Controls.ResetFloat"></div>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="controls">'.
                                        '<label for="switcher_id_[comment_id]" data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="cancel"></label>';
            if ($user !== false){
                $innerHTML .=           '<label for="editor_id_[comment_id]" data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="reply"></label>';
            }
            $innerHTML .=               '<div data-post-element-type="Pure.Posts.Comment.A.Controls.ResetFloat"></div>'.
                                    '</div>';
            if ($user !== false){
                $innerHTML .=       '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="editor">'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.EditorMark" data-engine-comment-mark="editor_id_[comment_id]"></div>'.
                                    '</div>';
            }
            $innerHTML .=           '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="included" data-engine-comment-included="Container" data-engine-comment-commentID="[comment_id]">'.
                                    '</div>'.
                                '</div>'.
                            '</div>';
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.templates.comment.'.$post_id,
                base64_encode($innerHTML),
                false,
                true
            );
            return $innerHTML;
        }
        public function innerHTMLIncludedComment($post_id, $user, $manaTemplate){
            /*
             * FIELDS
             * [name]
             * [avatar]
             * [date]
             * [comment]
             * [comment_id]
             * [post_id]
             */
            $innerHTML  =   '<div data-post-element-type="Pure.Posts.Comment.A.Wrapper" data-engine-comment-element="Comment.Included" data-engine-comment-postID="'.$post_id.'" style="display:none;">'.
                                '<div data-post-element-type="Pure.Posts.Comment.A.Left" data-post-comment-type="included">'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Avatar" data-post-comment-type="included" style="background-image:url([avatar])">'.
                                    '</div>'.
                                    $manaTemplate->markInnerHTML('comment', '[comment_id]').
                                '</div>'.
                                '<div data-post-element-type="Pure.Posts.Comment.A.Right">'.
                                    '<p data-post-element-type="Pure.Posts.Comment.A.Name"><a data-engine-comment-element="Comment.Author.Name" href="[home]">[name]</a> '.__('say', 'pure').'</p>'.
                                    '<p data-post-element-type="Pure.Posts.Comment.A.Date" data-engine-comment-element="Comment.DateTime">[date]</p>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Meme" data-engine-comment-commentID="[comment_id]" data-engine-comment-element="Comment.Meme">'.
                                        '<img alt="" data-post-element-type="Pure.Posts.Comment.A.Meme" src="[meme]"/>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment.Container" data-engine-comment-commentID="[comment_id]" data-engine-comment-element="Comment.Attachment">'.
                                        '<img alt="" data-post-element-type="Pure.Posts.Comment.A.Editor.Attachment" src="[attachment]"/>'.
                                    '</div>'.
                                    '<p data-post-element-type="Pure.Posts.Comment.A.Comment" data-engine-comment-commentID="[comment_id]" data-engine-comment-element="Comment.Value">[comment]</p>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Quote" data-engine-comment-commentID="[comment_id]" data-engine-comment-element="Comment.Quote">'.
                                        '<p data-post-element-type="Pure.Posts.Comment.A.Quote">[quote]</p>'.
                                        '<p data-post-element-type="Pure.Posts.Comment.A.Quote.Author">[quote_author], '.
                                            '<span data-post-element-type="Pure.Posts.Comment.A.Quote.Date">[quote_date]</span>'.
                                        '</p>'.
                                    '</div>';
            if ($user !== false){
                $innerHTML .=       '<input data-post-element-type="Pure.Posts.Comment.A.Controls" type="checkbox" data-type-controls="editor"'.
                                            'id="editor_id_[comment_id]"'.
                                            'data-engine-comment-element="Editor.Caller" '.
                                            'data-engine-comment-postID="[post_id]" '.
                                            'data-engine-comment-commentID="[comment_id]" '.
                                            'data-engine-comment-event="change" '.
                                            'data-engine-comment-editorID="editor_id_[comment_id]"/>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="include_controls">'.
                                        '<label for="editor_id_[comment_id]" data-post-element-type="Pure.Posts.Comment.A.Controls.Button" data-button-type="reply"></label>'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.Controls.ResetFloat"></div>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="editor">'.
                                        '<div data-post-element-type="Pure.Posts.Comment.A.EditorMark" data-engine-comment-mark="editor_id_[comment_id]"></div>'.
                                    '</div>';
            }
            $innerHTML .=           '<div data-post-element-type="Pure.Posts.Comment.A.Controls" data-type-controls="sub_included" data-engine-comment-included="Container" data-engine-comment-commentID="[comment_id]">'.
                                    '</div>'.
                                '</div>'.
                            '</div>';
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.comments.posts.configuration.templates.included_comment.'.$post_id,
                base64_encode($innerHTML),
                false,
                true
            );
            return $innerHTML;
        }
    }
}
?>