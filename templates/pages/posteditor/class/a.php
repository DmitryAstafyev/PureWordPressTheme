<?php
namespace Pure\Templates\Pages\PostEditor{
    class A{
        private $id;
        private $current;
        private $GroupWrapper;
        private $post = false;
        private function getPostData($post_id){
            $Provider   = \Pure\Providers\Posts\Initialization::instance()->getCommon();
            $this->post = $Provider->get($post_id);
            //echo var_dump($this->post);
            $Provider   = NULL;
            return ($this->post !== false ? true : false);
        }
        private function permissions(){
            \Pure\Components\Relationships\Mana\Initialization::instance()->attach();
            $Mana                   = new \Pure\Components\Relationships\Mana\Provider();
            $this->current->mana    = $Mana->getUserPermissions($this->current->ID);
            $Mana                   = NULL;
        }
        private function checkPermissions(){
            $innerHTML = '';
            if ($this->current->mana !== false){
                if ($this->current->mana->allow_create->post === false){
                    $innerHTML = $this->GroupWrapper->open(array(
                            "title"             =>__( "You cannot create post", 'pure' ),
                            "group"             =>uniqid(),
                            "echo"              =>false,
                            "opened"            =>true,
                            "content_style"     =>'width:auto;padding:0.5em;')
                    );
                    $innerHTML .=   '<p data-type-element="Pure.PostEditor.A.Notice">'.
                                        __( "Sorry, but you cannot create post, because you have not enough mana (rate) for it. You should have at least ", 'pure' ).
                                        '<strong style="color:green;">('.$this->current->mana->threshold->allow_create->post.')</strong>'.
                                        __( " but you have ", 'pure' ).
                                        '<strong style="color:red;">('.$this->current->mana->value.')</strong>'.
                                    '</p>';
                    $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
                }
            }
            return $innerHTML;
        }
        private function getSandboxCategory(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
            $parameters = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
            $categoryID = (int)$parameters->mana_threshold_manage_categories_sandbox;
            $parameters = NULL;
            return get_category($categoryID);
        }
        private function formBEGIN($actionURL){
            return  '<form action="'.$actionURL.'" method="post" enctype="multipart/form-data" data-create-post-engine-element="form" id="'.$this->id.'">';
        }
        private function formEND(){
            return '</form>';
        }
        private function hiddenFields($command){
            global $post_ID;
            $innerHTML =    '<input form="'.$this->id.'" data-type-element="Pure.PostEditor.A.HiddenField" name="author_id" value="'.$this->current->ID.'"/>'.
                            '<input form="'.$this->id.'" data-type-element="Pure.PostEditor.A.HiddenField" name="command" value="'.$command.'"/>'.
                            '<input form="'.$this->id.'" data-type-element="Pure.PostEditor.A.HiddenField" name="action" value="" data-create-post-engine-element="action"/>'.
                            '<input form="'.$this->id.'" data-type-element="Pure.PostEditor.A.HiddenField" name="post_ID" id="post_ID" value="'.$post_ID.'"/>'.
                            '<input form="'.$this->id.'" data-type-element="Pure.PostEditor.A.HiddenField" name="post_id" id="post_id" value="'.$post_ID.'"/>';
            return $innerHTML;
        }
        private function elementTitle(){
            if ($this->post !== false){
                $innerHTML = '<p data-type-element="Pure.PostEditor.A.Title">'.__( "Edit the post", 'pure' ).'</p>';
            }else{
                $innerHTML = '<p data-type-element="Pure.PostEditor.A.Title">'.__( "Create a new post", 'pure' ).'</p>';
            }
            return $innerHTML;
        }
        private function elementDetails(){
            $innerHTML = '';
            if ($this->post !== false){
                if ($this->post->post->post_status !== 'publish'){
                    $innerHTML = '<p data-type-element="Pure.PostEditor.A.Attention">'.__( "This post is not published yet.", 'pure' ).'</p>';
                }
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.post.data.isDraft',
                    ($this->post->post->post_status !== 'publish' ? 'yes' : 'no'),
                    false,
                    true
                );
            }
            return $innerHTML;
        }
        private function elementHEADER(){
            $innerHTML = $this->GroupWrapper->open(array(
                    "title"             =>__( "Title of your post", 'pure' ),
                    "group"             =>uniqid(),
                    "echo"              =>false,
                    "opened"            =>true,
                    "content_style"     =>'width:auto;padding:0.5em;')
            );
            $innerHTML .= '<textarea form="'.$this->id.'" data-type-element="Pure.PostEditor.A" data-type-addition="Header" name="post_title">'.($this->post !== false ? $this->post->post->post_title : '').'</textarea>';
            $innerHTML .= '<p data-type-element="Pure.PostEditor.A.Notice">'.__( "Please define title of your post. Not more 250 symbols.", 'pure' ).'</p>';
            $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
            return $innerHTML;
        }
        private function elementEDITOR(){
            wp_enqueue_script('mce-view');
            $innerHTML = $this->GroupWrapper->open(array(
                    "title"             =>__( "Your post", 'pure' ),
                    "group"             =>uniqid(),
                    "echo"              =>false,
                    "opened"            =>true,
                    "content_style"     =>'width:auto;padding:0.5em;')
            );
            ob_start();
            wp_editor(
                ($this->post !== false ? $this->post->post->post_content : ''),
                'content',
                array(
                    'wpautop'           => true,
                    'media_buttons'     => true,
                    'textarea_name'     => 'post_content',
                    'textarea_rows'     => 20,
                    'tabindex'          => null,
                    'editor_css'        => '',
                    'editor_class'      => '',
                    'teeny'             => false,
                    'dfw'               => false,
                    'tinymce'           => true,
                    'quicktags'         => true,
                    'drag_drop_upload'  => false
                )
            );
            $editor_contents = ob_get_contents();
            ob_get_clean();
            $innerHTML .= $editor_contents;
            $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
            return $innerHTML;
        }
        private function elementQUOTE(){
            $innerHTML = $this->GroupWrapper->open(array(
                    "title"             =>__( "Quote for your post", 'pure' ),
                    "group"             =>uniqid(),
                    "echo"              =>false,
                    "opened"            =>false,
                    "content_style"     =>'width:auto;padding:0.5em;')
            );
            $innerHTML .= '<textarea form="'.$this->id.'" data-type-element="Pure.PostEditor.A" data-type-addition="Quote" name="post_excerpt">'.($this->post !== false ? stripcslashes($this->post->post->post_excerpt) : '').'</textarea>';
            $innerHTML .= '<p data-type-element="Pure.PostEditor.A.Notice">'.__( "You can define short description for your post. This is not a mandatory field. If you do not define it, quote will be built by content of your post. Not more 500 symbols.", 'pure' ).'</p>';
            $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
            return $innerHTML;
        }
        private function elementMINIATURE(){
            \Pure\Components\Styles\Buttons\B\Initialization::instance()->attach();
            \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach();
            $innerHTML = $this->GroupWrapper->open(array(
                    "title"             =>__( "Miniature for your post", 'pure' ),
                    "group"             =>uniqid(),
                    "echo"              =>false,
                    "opened"            =>false,
                    "content_style"     =>'width:auto;padding:0.5em;')
            );
            $no_miniature = \Pure\Templates\Pages\PostEditor\Initialization::instance()->configuration->urls->images.'/A/no_image.png';
            $innerHTML .=   '<img alt="" data-type-element="Pure.PostEditor.A.Miniature" src="'.($this->post !== false ? ($this->post->post->miniature !== '' ? $this->post->post->miniature : $no_miniature) : $no_miniature).'" data-create-post-engine-element="miniature"/>'.
                            '<a data-element-type="Pure.CommonStyles.Button.B" data-type-direction="right" data-create-post-engine-element="miniature.select">'.__( "load", 'pure' ).'</a>'.
                            '<a data-element-type="Pure.CommonStyles.Button.B" data-type-direction="right" data-create-post-engine-element="miniature.remove" data-create-post-engine-no_miniature_src="'.$no_miniature.'">'.__( "remove", 'pure' ).'</a>'.
                            '<div data-type-element="Pure.PostEditor.A.ResetFloat"></div>'.
                            '<input form="'.$this->id.'" type="'.($this->post !== false ? 'text' : 'file').'" value="'.($this->post !== false ? 'no change' : '').'" data-type-element="Pure.PostEditor.A.FileInput" accept="'.\Pure\Components\GlobalSettings\MIMETypes\Types::$images.'" data-create-post-engine-element="miniature" name="post_miniature"/>';
            $innerHTML .=   '<p data-type-element="Pure.PostEditor.A.Notice">'.__( "You can define some image for your post. This is not a mandatory field. If your post has some images, it will be used as miniature.", 'pure' ).'</p>';
            $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
            return $innerHTML;
        }
        private function elementVISIBILITY(){
            \Pure\Components\Styles\CheckBoxes\A\Initialization::instance()->attach();
            \Pure\Components\WordPress\Post\Visibility\Initialization::instance()->attach();
            $visibility     = \Pure\Components\WordPress\Post\Visibility\Data::$visibility;
            $association    = \Pure\Components\WordPress\Post\Visibility\Data::$association;
            $GroupProvider  = \Pure\Providers\Groups\Initialization::instance()->get('users');
            $groups         = $GroupProvider->get(array(
                'from_date'         =>date('Y-m-d'),
                'days'              =>9999,
                'targets_array'     =>array($this->current->ID),
                'shown'             =>0,
                'maxcount'          =>999,
                'only_with_avatar'  =>false
            ));
            $GroupProvider  = NULL;
            $innerHTML = $this->GroupWrapper->open(array(
                    "title"             =>__( "Visibility of your post", 'pure' ),
                    "group"             =>uniqid(),
                    "echo"              =>false,
                    "opened"            =>false,
                    "content_style"     =>'width:auto;padding:0.5em;')
            );
            $innerHTML .=   '<p data-type-element="Pure.PostEditor.A.SubTitle"><strong>'.__( "First level.", 'pure' ).'</strong> '.__( "Please, define first (global) level of visibility.", 'pure' ).'</p>';
            $innerHTML .=   '<table data-type-element="Pure.PostEditor.A.Visibility">'.
                                '<tr>'.
                                    '<td>'.
                                        '<label>'.
                                            '<input form="'.$this->id.'" data-element-type="Pure.CommonStyles.CheckBox.A" '.($this->post !== false ? ($this->post->visibility->visibility == $visibility['public'] ? 'checked' : '') : 'checked').' type="radio" value="'.$visibility['public'].'" name="post_visibility"/>'.
                                            '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                                '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                                '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                                    '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                                '</div>'.
                                            '</div>'.
                                            '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                __( 'anyone', 'pure' ).
                                            '</p>'.
                                        '</label>'.
                                    '</td>'.
                                    '<td>'.
                                        '<p data-type-element="Pure.PostEditor.A.Visibility">'.__( "Your post will be available for all user. And for registered and not.", 'pure' ).'</p>'.
                                    '</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td>'.
                                        '<label>'.
                                            '<input form="'.$this->id.'" data-element-type="Pure.CommonStyles.CheckBox.A" '.($this->post !== false ? ($this->post->visibility->visibility == $visibility['private'] ? 'checked' : '') : '').' type="radio" value="'.$visibility['private'].'" name="post_visibility"/>'.
                                            '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                                '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                                '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                                    '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                                '</div>'.
                                            '</div>'.
                                            '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                __( 'registered', 'pure' ).
                                            '</p>'.
                                        '</label>'.
                                    '</td>'.
                                    '<td>'.
                                        '<p data-type-element="Pure.PostEditor.A.Visibility">'.__( "Show post only registered users.", 'pure' ).'</p>'.
                                    '</td>'.
                                '</tr>'.
                            '</table>';
            $innerHTML .=   '<p data-type-element="Pure.PostEditor.A.SubTitle"><strong>'.__( "Second level.", 'pure' ).'</strong> '.__( "You can define second level of visibility of your post.", 'pure' ).'</p>';
            $innerHTML .=   '<table data-type-element="Pure.PostEditor.A.Visibility">'.
                                '<tr>'.
                                    '<td>'.
                                        '<label>'.
                                            '<input form="'.$this->id.'" data-element-type="Pure.CommonStyles.CheckBox.A" '.($this->post !== false ? ($this->post->visibility->association == $association['community'] ? 'checked' : '') : 'checked').' type="radio" value="'.$association['community'].'" name="post_association"/>'.
                                            '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                                '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                                '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                                    '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                                '</div>'.
                                            '</div>'.
                                            '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                __( 'community', 'pure' ).
                                            '</p>'.
                                        '</label>'.
                                    '</td>'.
                                    '<td>'.
                                        '<p data-type-element="Pure.PostEditor.A.Visibility">'.__( "With this setting of visibility your post will be available for any user according a first level of visibility.", 'pure' ).'</p>'.
                                    '</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td>'.
                                        '<label>'.
                                            '<input form="'.$this->id.'" data-element-type="Pure.CommonStyles.CheckBox.A" '.($this->post !== false ? ($this->post->visibility->association == $association['friends'] ? 'checked' : '') : '').' type="radio" value="'.$association['friends'].'" name="post_association"/>'.
                                            '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                                '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                                '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                                    '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                                '</div>'.
                                            '</div>'.
                                            '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                __( 'friends', 'pure' ).
                                            '</p>'.
                                        '</label>'.
                                    '</td>'.
                                    '<td>'.
                                        '<p data-type-element="Pure.PostEditor.A.Visibility">'.__( "Use this setting of visibility to associate post with your friends. If first level of visibility is \"registered\", your post will see only your friends.", 'pure' ).'</p>'.
                                    '</td>'.
                                '</tr>';
            if ($groups !== false){
                if (count($groups->groups) > 0){
                    $innerHTML .=   '<tr>'.
                                        '<td>'.
                                            '<label>'.
                                                '<input form="'.$this->id.'" data-element-type="Pure.CommonStyles.CheckBox.A" '.($this->post !== false ? ($this->post->visibility->association == $association['group'] ? 'checked' : '') : '').' type="radio" value="'.$association['group'].'" name="post_association"/>'.
                                                '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                    '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                                    '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                                    '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                                        '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                                    '</div>'.
                                                '</div>'.
                                                '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                    __( 'group', 'pure' ).
                                                '</p>'.
                                            '</label>'.
                                        '</td>'.
                                        '<td>'.
                                            '<p data-type-element="Pure.PostEditor.A.Visibility">'.__( "Use this setting of visibility to associate post with some group, where you are a member. If first level of visibility is \"registered\", your post will see only members of selected group.", 'pure' ).'</p>'.
                                            '<select form="'.$this->id.'" data-type-element="Pure.PostEditor.A.Visibility" name="post_association_object">';
                    foreach($groups->groups as $group){
                        $innerHTML .=           '<option value="'.$group->id.'" '.($this->post !== false ? ((isset($this->post->visibility->object_id) !== false ? $this->post->visibility->object_id : 0) == $group->id ? 'selected' : '') : '').'>'.$group->name.'</option>';
                    }
                    $innerHTML .=           '</select>'.
                                        '</td>'.
                                    '</tr>';
                }
            }
            $innerHTML .=   '</table>';
            $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
            return $innerHTML;
        }
        private function elementCATEGORIES(){
            $innerHTML      = '';
            $categories     = get_terms('category', 'orderby=count&hide_empty=0');
            if (is_array($categories) !== false){
                if (count($categories) > 0){
                    $innerHTML = $this->GroupWrapper->open(array(
                            "title"             =>__( "Define category of your post", 'pure' ),
                            "group"             =>uniqid(),
                            "echo"              =>false,
                            "opened"            =>false,
                            "content_style"     =>'width:auto;padding:0.5em;')
                    );
                    if ($this->post !== false){
                        \Pure\Components\Tools\Arrays\Initialization::instance()->attach(true);
                        $ArrayTools     = new \Pure\Components\Tools\Arrays\Arrays();
                        $_categories    = $ArrayTools->make_array_by_property_of_array_objects($this->post->category->all, 'id', 'integer');
                        $ArrayTools     = NULL;
                    }else{
                        $_categories    = array();
                    }
                    $sandbox        = $this->getSandboxCategory();
                    $sandbox_id     = ($sandbox !== false ? (int)$sandbox->term_id : false);
                    $in_sandbox     = in_array($sandbox_id, $_categories);
                    foreach($categories as $category) {
                        if ($sandbox_id !== (int)$category->term_id){
                            $innerHTML .=       '<label>'.
                                                    '<input form="'.$this->id.'" data-element-type="Pure.CommonStyles.CheckBox.A" type="radio" value="'.$category->term_id.'" '.($_categories !== false ? (in_array((int)$category->term_id, $_categories) !== false ? 'checked' : '' ) : '').' name="post_category"/>'.
                                                    '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                        '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                                        '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                                        '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                                            '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                                        '</div>'.
                                                    '</div>'.
                                                    '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                        $category->name.
                                                    '</p>'.
                                                '</label>';
                        }
                    }
                    if ($this->current->mana !== false){
                        if ($this->current->mana->allow_manage->categories === false || $in_sandbox !== false){
                            if ($sandbox !== false){
                                $innerHTML .=       '<label>'.
                                                        '<input form="'.$this->id.'" data-element-type="Pure.CommonStyles.CheckBox.A" '.($this->current->mana->allow_manage->categories === false ? 'disabled' : '').' type="checkbox" checked value="yes" name="post_sandbox"/>'.
                                                        '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                            '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                                            '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                                            '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                                                '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                                            '</div>'.
                                                        '</div>'.
                                                        '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                            $sandbox->name.
                                                        '</p>'.
                                                    '</label>';
                                if ($this->current->mana->allow_manage->categories === false){
                                    $innerHTML .=   '<p data-type-element="Pure.PostEditor.A.Attention">'.
                                                        __( "You have not enough mana (rate). So you can (and should) define any category, but it will be placed into \"sandbox\" until you have not enough mana (rate). Should be at least  ", 'pure' ).
                                                        '<strong style="color:green;">('.$this->current->mana->threshold->allow_manage->categories.')</strong>'.
                                                        __( " but you have ", 'pure' ).
                                                        '<strong style="color:red;">('.$this->current->mana->value.')</strong>.'.
                                                        __( " If you are updating post, updated post will be placed into sandbox too.", 'pure' ).
                                                    '</p>';
                                }
                            }
                        }
                    }
                    $innerHTML .=   '<p data-type-element="Pure.PostEditor.A.Notice">'.__( "It will be better, if you define category of your post.", 'pure' ).'</p>';
                    $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
                }
            }
            return $innerHTML;
        }
        private function elementSETTINGS(){
            $allow_manage = true;
            if ($this->current->mana !== false) {
                if ($this->current->mana->allow_manage->comment === false) {
                    $allow_manage = false;
                }
            }
            $innerHTML = $this->GroupWrapper->open(array(
                    "title"             =>__( "Comments", 'pure' ),
                    "group"             =>uniqid(),
                    "echo"              =>false,
                    "opened"            =>false,
                    "content_style"     =>'width:auto;padding:0.5em;')
            );
            $innerHTML .=   '<label>'.
                                    '<input form="'.$this->id.'" data-element-type="Pure.CommonStyles.CheckBox.A" '.($this->post !== false ? ($this->post->post->comment_status === 'open' ? 'checked' : ''): 'checked').' type="radio" value="open" name="post_allow_comments" '.($allow_manage === false ? 'disabled' : '').'/>'.
                                    '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                        '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                        '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                        '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                            '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                        'allow comments from registered users'.
                                    '</p>'.
                                '</label>';
            $innerHTML .=   '<label>'.
                                    '<input form="'.$this->id.'" data-element-type="Pure.CommonStyles.CheckBox.A" '.($this->post !== false ? ($this->post->post->comment_status === 'closed' ? 'checked' : ''): '').' type="radio" value="closed" name="post_allow_comments" '.($allow_manage === false ? 'disabled' : '').'/>'.
                                    '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                        '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                        '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                        '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                            '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                        'deny any comments'.
                                    '</p>'.
                                '</label>';
            if ($allow_manage === false){
                $innerHTML .=   '<p data-type-element="Pure.PostEditor.A.Attention">'.
                                    __( "You have not enough mana (rate). So you cannot manage comments for now. Should be at least  ", 'pure' ).
                                    '<strong style="color:green;">('.$this->current->mana->threshold->allow_manage->comment.')</strong>'.
                                    __( " but you have ", 'pure' ).
                                    '<strong style="color:red;">('.$this->current->mana->value.')</strong>'.
                                '</p>';
            }else{
                $innerHTML .=   '<p data-type-element="Pure.PostEditor.A.Notice">'.__( "You can allow or deny comments to your post.", 'pure' ).'</p>';
            }
            $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
            return $innerHTML;
        }
        private function elementTAGS(){
            global $post_ID;
            $innerHTML  = $this->GroupWrapper->open(array(
                    "title"             =>__( "Tags for post", 'pure' ),
                    "group"             =>uniqid(),
                    "echo"              =>false,
                    "opened"            =>false,
                    "content_style"     =>'width:auto;padding:0.5em;')
            );
            $template   = \Pure\Templates\Posts\Elements\TermsEditor\Initialization::instance()->get('A');
            $innerHTML .= $template->innerHTML(
                $post_ID,
                'post_tag',
                $this->id,
                'post_tags',
                'data-posteditor-fieldID="post_tags"'
            );
            $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
            return $innerHTML;
        }
        private function elementWARNINGS(){
            global $post_ID;
            $innerHTML  = $this->GroupWrapper->open(array(
                    "title"             =>__( "Warnings", 'pure' ),
                    "group"             =>uniqid(),
                    "echo"              =>false,
                    "opened"            =>false,
                    "content_style"     =>'width:auto;padding:0.5em;')
            );
            $template   = \Pure\Templates\Elements\TermsSelector\Initialization::instance()->get('B');
            $innerHTML .= $template->innerHTML(
                $post_ID,
                'warning_mark',
                $this->id,
                'post_warnings',
                'data-posteditor-fieldID="post_warnings"'
            );
            $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
            return $innerHTML;
        }
        private function elementCONTROLS(){
            \Pure\Components\Styles\Buttons\C\Initialization::instance()->attach();
            if ($this->post !== false){
                $innerHTML = '';
                if ($this->post->post->post_status !== 'publish'){
                    $innerHTML .=   '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" data-create-post-engine-element="button.publish">'.__( "publish", 'pure' ).'</a>';
                }else{
                    $innerHTML .=   '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" data-create-post-engine-element="button.draft">'.__( "to draft", 'pure' ).'</a>';
                }
                $innerHTML .=       '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" data-create-post-engine-element="button.update">'.__( "update", 'pure' ).'</a>'.
                                    '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" data-create-post-engine-element="button.remove">'.__( "remove", 'pure' ).'</a>'.
                                    '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" data-create-post-engine-element="button.preview">'.__( "preview", 'pure' ).'</a>';
            }else{
                $innerHTML =    '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" data-create-post-engine-element="button.publish">'.__( "publish", 'pure' ).'</a>'.
                                '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" data-create-post-engine-element="button.preview">'.__( "preview", 'pure' ).'</a>'.
                                '<a data-element-type="Pure.CommonStyles.Button.C" data-addition-type="right" data-create-post-engine-element="button.draft">'.__( "save as draft", 'pure' ).'</a>';
            }
            return $innerHTML;
        }
        private function errorInnerHTML($error){
            switch($error){
                case 'post not found':
                    $innerHTML = $this->GroupWrapper->open(array(
                            "title"             =>__( "Post not found", 'pure' ),
                            "group"             =>uniqid(),
                            "echo"              =>false,
                            "opened"            =>true,
                            "content_style"     =>'width:auto;padding:0.5em;')
                    );
                    $innerHTML .=   '<p data-type-element="Pure.PostEditor.A.Attention">'.
                                        __( "Sorry, but post, which you are required, was not found. Please check url and try again.", 'pure' ).
                                    '</p>';
                    $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
                    return $innerHTML;
                    break;
                case 'access deny':
                    $innerHTML = $this->GroupWrapper->open(array(
                            "title"             =>__( "You should register", 'pure' ),
                            "group"             =>uniqid(),
                            "echo"              =>false,
                            "opened"            =>true,
                            "content_style"     =>'width:auto;padding:0.5em;')
                    );
                    $innerHTML .=   '<p data-type-element="Pure.PostEditor.A.Attention">'.
                        __( "Sorry, but to create post you should register first.", 'pure' ).
                        '</p>';
                    $innerHTML .= $this->GroupWrapper->close(array("echo"=>false));
                    return $innerHTML;
                    break;
            }
        }
        private function innerHTML($createURL, $command){
            $innerHTML = $this->checkPermissions();
            if ($innerHTML === ''){
                $innerHTML  = $this->formBEGIN          ($createURL);
                $innerHTML .= $this->formEND            ();
                $innerHTML .= $this->hiddenFields       ($command);
                $innerHTML .= $this->elementHEADER      ();
                $innerHTML .= $this->elementEDITOR      ();
                $innerHTML .= $this->elementQUOTE       ();
                $innerHTML .= $this->elementMINIATURE   ();
                $innerHTML .= $this->elementVISIBILITY  ();
                $innerHTML .= $this->elementCATEGORIES  ();
                $innerHTML .= $this->elementTAGS        ();
                $innerHTML .= $this->elementWARNINGS    ();
                $innerHTML .= $this->elementSETTINGS    ();
                $innerHTML .= $this->elementDetails     ();
                $innerHTML .= $this->elementCONTROLS    ();
            }
            return $innerHTML;
        }
        private function setPostID(){
            global $post_ID;
            if ($this->post !== false){
                $post_ID = $this->post->post->ID;
                return true;
            }else{
                \Pure\Components\PostTypes\Post\Module\Initialization::instance()->attach();
                $Provider   = new \Pure\Components\PostTypes\Post\Module\Core();
                $post_ID    = $Provider->addEmptyDraft('post');
                $Provider   = NULL;
                return ((int)$post_ID > 0 ? true : false);
            }
            return false;
        }
        public function get($post_id = false){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            \Pure\Components\Dialogs\B\Initialization::instance()->attach();
            $this->GroupWrapper = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
            if ($current !== false){
                $this->id           = uniqid('PostEditor');
                $this->current      = $current;
                $this->permissions();
                if ($post_id === false){
                    \Pure\Components\WordPress\Location\Requests\Initialization::instance()->attach();
                    $Data       = new \Pure\Components\WordPress\Location\Requests\Register();
                    $createURL  = $Data->url;
                    $command    = 'templates_of_posts_create_post';
                    $Data       = NULL;
                    if ($this->setPostID() !== false){
                        return $this->innerHTML($createURL, $command);
                    }else{
                        return $this->errorInnerHTML('Sorry, some error on server. Cannot create draft for new post.');
                    }
                }else{
                    if ($this->getPostData($post_id) !== false){
                        if ($this->post->post->post_type === 'post'){
                            $Data       = new \Pure\Components\WordPress\Location\Requests\Register();
                            $createURL  = $Data->url;
                            $command    = 'templates_of_posts_update_post';
                            $Data       = NULL;
                            if ($this->setPostID() !== false){
                                return $this->innerHTML($createURL, $command);
                            }else{
                                return $this->errorInnerHTML('Sorry, some error on server. Cannot detect ID of current post.');
                            }
                        }else{
                            return $this->errorInnerHTML('post not found');
                        }
                    }else{
                        return $this->errorInnerHTML('post not found');
                    }
                }
            }else{
                return $this->errorInnerHTML('access deny');
            }
        }
    }
}
?>