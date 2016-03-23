<?php
namespace Pure\Plugins\HighLights {
    class Widget extends \WP_Widget {
        private $pureUpdated = false;
        public function __construct() {
            parent::__construct(
                Initialization::instance()->configuration->id,                                      // id
                Initialization::instance()->configuration->name, 		                            // name
                array( 'description' => Initialization::instance()->configuration->description )    // description
            );
        }
        private function repairURLs($URLs){
            foreach($URLs as $key=>$url){
                $URLs[$key] = \Pure\Resources\Names::instance()->repairURL($url);
            }
            return $URLs;
        }
        public function widget($args, $instance)
        {
            $title 	        = apply_filters( 'widget_title', $instance['title'] );
            $out 	        = $args['before_widget'];
            $_parameters    = array(	'title'		    => $instance['title'		],
                                        'title_type'    => $instance['title_type'   ],
                                        'template'      => $instance['template'	    ],
                                        'icons'         => $instance['icons'	    ],
                                        'titles'        => $instance['titles'	    ],
                                        'descriptions'  => $instance['descriptions' ],
                                        'post_ids'      => $instance['post_ids'	    ],
                                        'page_ids'      => $instance['page_ids'	    ],
                                        'urls'          => $this->repairURLs($instance['urls']),
            );
            try{
                $widget     = new Builder($_parameters);
                $innerHTML  = $widget->render();
                $out        .= $innerHTML;
                $out        .= $args['after_widget'];
                echo $out;
            }catch (\Exception $e){
                \Pure\Components\Tools\ErrorsRender\Initialization::instance()->attach();
                $ErrorMessages = new \Pure\Components\Tools\ErrorsRender\Render();
                $ErrorMessages->show("\\Pure\\Plugins\\HighLights\\Widget\\widget::: ".$e);
                $ErrorMessages = NULL;
                return NULL;
            }
        }
        public function update($new_instance, $old_instance)
        {
            $this->pureUpdated          = true;
            $instance 				    = array();
            $instance['title'		]   = strip_tags( $new_instance['title'			] );
            $instance['title_type'  ]   = strip_tags( $new_instance['title_type'    ] );
            $instance['template'    ]   = strip_tags( $new_instance['template'	    ] );
            $instance['icons'       ]   = array();
            $instance['titles'      ]   = array();
            $instance['descriptions']   = array();
            $instance['post_ids'    ]   = array();
            $instance['page_ids'    ]   = array();
            $instance['urls'        ]   = array();
            if ( isset ( $new_instance['icons'] ) ){
                foreach ( $new_instance['icons'] as $value ){
                    array_push($instance['icons'], $value);
                }
            }
            if ( isset ( $new_instance['titles'] ) ){
                foreach ( $new_instance['titles'] as $value ){
                    array_push($instance['titles'], $value);
                }
            }
            if ( isset ( $new_instance['descriptions'] ) ){
                foreach ( $new_instance['descriptions'] as $value ){
                    array_push($instance['descriptions'], $value);
                }
            }
            if ( isset ( $new_instance['post_ids'] ) ){
                foreach ( $new_instance['post_ids'] as $value ){
                    array_push($instance['post_ids'], $value);
                }
            }
            if ( isset ( $new_instance['page_ids'] ) ){
                foreach ( $new_instance['page_ids'] as $value ){
                    array_push($instance['page_ids'], $value);
                }
            }
            if ( isset ( $new_instance['urls'] ) ){
                foreach ( $new_instance['urls'] as $value ){
                    array_push($instance['urls'], \Pure\Resources\Names::instance()->clearURL($value));
                }
            }
            return $instance;
        }
        private function call_scripts_after_update(){
            ?>
            <script type="text/javascript">
                if(typeof pure === "object"){
                    (pure.system.getInstanceByPath("pure.components.admin.multiitems"   ) !== null ? pure.system.getInstanceByPath("pure.components.admin.multiitems"   ).init() : null);
                    (pure.system.getInstanceByPath("pure.wordpress.media.images"        ) !== null ? pure.system.getInstanceByPath("pure.wordpress.media.images"        ).init() : null);
                    (pure.system.getInstanceByPath("pure.admin.groups.D"                ) !== null ? pure.system.getInstanceByPath("pure.admin.groups.D"                ).init() : null);
                }
            </script>
            <?php
        }
        private function getPostsTitles($posts){
            $titles = array();
            foreach($posts as $post){
                $titles[$post->ID] = '['.$post->ID.']:: '.$post->post_title;
            }
            return $titles;
        }
        public function form($instance)
        {
            if (isset($this->pureUpdated) !== false){
                if ($this->pureUpdated !== false){
                    $this->call_scripts_after_update();
                }
            }
            $templates          = \Pure\Templates\HighLights\Initialization             ::instance()->templates;
            $templatesTitle     = \Pure\Templates\Titles\Initialization                 ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization           ::instance()->get('A');
            $containers         = \Pure\Templates\Admin\Groups\Initialization           ::instance()->get('D');
            $title 			    = isset( $instance[ 'title' 		] )  ? $instance[ 'title' 			] : '';
            $title_type		    = isset( $instance[ 'title_type' 	] )  ? $instance[ 'title_type' 		] : 'B';
            $template		    = isset( $instance[ 'template'      ] )  ? $instance[ 'template'	    ] : 'A';
            $icons		        = isset( $instance[ 'icons' 		] )  ? $instance[ 'icons' 		    ] : array();
            $titles		        = isset( $instance[ 'titles' 		] )  ? $instance[ 'titles' 		    ] : array();
            $descriptions       = isset( $instance[ 'descriptions'  ] )  ? $instance[ 'descriptions'    ] : array();
            $post_ids           = isset( $instance[ 'post_ids' 		] )  ? $instance[ 'post_ids' 	    ] : array();
            $page_ids	        = isset( $instance[ 'page_ids' 		] )  ? $instance[ 'page_ids' 	    ] : array();
            $urls		        = isset( $instance[ 'urls' 		    ] )  ? $instance[ 'urls' 		    ] : array();
            $urls               = $this->repairURLs($urls);
            $posts              = get_posts(
                array(
                    'numberposts'     => 1000,
                    'orderby'         => 'post_title',
                    'order'           => 'DESC',
                    'post_type'       => 'post',
                    'post_status'     => 'publish'
                )
            );
            $pages              = get_posts(
                array(
                    'numberposts'     => 1000,
                    'orderby'         => 'post_title',
                    'order'           => 'DESC',
                    'post_type'       => 'page',
                    'post_status'     => 'publish'
                )
            );
            $posts_titles = $this->getPostsTitles($posts);
            $pages_titles = $this->getPostsTitles($pages);
            ?>
            <?php
            $groups->open(array(    "title" =>"Title",
                                    "group" =>"HighLights",
                                    "echo"  =>true));
            ?>
                <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title of widget</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
                </p>
                <p>Choose template of title</p>
                <?php
                foreach ($templatesTitle as $templateTitle){
                    ?>
                    <p>
                        <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templateTitle->key; ?>" <?php checked( $templateTitle->key, $title_type ); ?> id="<?php echo $this->get_field_id( 'title_type'.$templateTitle->key ); ?>" name="<?php echo $this->get_field_name( 'title_type' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'title_type'.$templateTitle->key ); ?>">Template <?php echo $templateTitle->key; ?> <br />
                            <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $templateTitle->thumbnail; ?>">
                        </label>
                        <?php
                        \Pure\Templates\Titles\Initialization::instance()->description($templateTitle->key);
                        ?>
                    </p>
                <?php
                }
                ?>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(
                "title" =>"Template",
                "group" =>"HighLights",
                "echo"  =>true));
            ?>
            <p>Choose template of highlights</p>
            <?php
            foreach ($templates as $_template){
                ?>
                <p>
                    <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $_template->key; ?>" <?php checked( $_template->key, $template ); ?> id="<?php echo $this->get_field_id( 'template'.$_template->key ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'template'.$_template->key ); ?>">Template <?php echo $_template->key; ?> <br />
                        <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $_template->thumbnail; ?>">
                    </label>
                    <?php
                    \Pure\Templates\HighLights\Initialization::instance()->description($_template->key);
                    ?>
                </p>
            <?php
            }
            ?>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Content",
                                    "group" =>"HighLights",
                                    "echo"  =>true));
            for($index = 0; $index < count($icons); $index ++){
                if ($icons[$index] !== ''){
                    $attachment = wp_get_attachment_image_src( $icons[$index], 'thumbnail', false );
                    if (is_array($attachment) !== false){
                        $attachment_url = $attachment[0];
                    }else{
                        $attachment_url = Initialization::instance()->configuration->urls->images.'/no_image.png';
                    }
                }else{
                    $attachment_url = Initialization::instance()->configuration->urls->images.'/no_image.png';
                }
                $this->innerHTMLItem(
                    $containers,
                    $posts,
                    $pages,
                    $index,
                    $posts_titles,
                    $pages_titles,
                    (object)array(
                        'title'             =>$titles[$index],
                        'description'       =>$descriptions[$index],
                        'post_id'           =>$post_ids[$index],
                        'page_id'           =>$page_ids[$index],
                        'url'               =>$urls[$index],
                        'attachment_id'     =>$icons[$index],
                        'attachment_url'    =>$attachment_url
                    )
                );
            }
            ?>
            <a data-basic-type="Button" data-element-type="Pure.Admin.MultiItems.Add" data-muliitems-add-button data-muliitems-afteradd-handles="pure.wordpress.media.images.init">add new</a>
            <?php
            $this->innerHTMLTemplateItem($containers, $posts, $pages);
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $templates          = NULL;
            $templatesTitle     = NULL;
            $groups             = NULL;
            \Pure\Components\WordPress\Admin\Multiitems\Initialization::instance()->attach();
            \Pure\Components\WordPress\Media\Resources\Initialization::instance()->attach();
        }
        private function getScrollTitle($posts_titles, $pages_titles, $index, $data){
            $title = '';
            if ($data->post_id !== '-1' && $data->post_id !== '' && $title === ''){
                $title = (isset($posts_titles[$data->post_id]) !== false ? $posts_titles[$data->post_id] : '');
            }
            if ($data->page_id !== '-1' && $data->page_id !== '' && $title === ''){
                $title = (isset($pages_titles[$data->page_id]) !== false ? $pages_titles[$data->page_id] : '');
            }
            if ($data->url !== '' && $title === ''){
                $title = $data->url;
            }
            return ($title !== '' ? $title : 'Item');
        }
        private function innerHTMLItem($containers, $posts, $pages, $index, $posts_titles, $pages_titles, $data){
            $containers->open(
                array(
                    "title"             =>$this->getScrollTitle($posts_titles, $pages_titles, $index, $data),
                    "opened"            =>false,
                    'style_content'     =>'padding:0.5em;',
                    "container_attr"    =>'data-muliitems-parent-of="'.$this->get_field_id( 'Items' ).'['.$index.']"',
                    "remove_attr"       =>'data-muliitems-under-control="'.$this->get_field_id( 'Items' ).'['.$index.']"',
                    "remove_title"      =>__('remove', 'pure'),
                    "id"                =>$this->get_field_id( 'Items' ).'['.$index.']',
                    "on_change"         =>'pure.wordpress.media.images.init',
                    "echo"              =>true,
                )
            );
            ?>
            <p data-element-type="Pure.Admin.Title">Basic</p>
            <p>
                <label for="<?php echo $this->get_field_id( 'titles' ); ?>[<?php echo $index; ?>]">Title of item</label>
                <textarea data-element-type="Pure.Admin.TextArea" id="<?php echo $this->get_field_id( 'titles' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'titles' ); ?>[<?php echo $index; ?>]" type="text"><?php echo $data->title; ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'descriptions' ); ?>[<?php echo $index; ?>]">Description of item</label>
                <textarea data-element-type="Pure.Admin.TextArea" id="<?php echo $this->get_field_id( 'descriptions' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'descriptions' ); ?>[<?php echo $index; ?>]" type="text"><?php echo $data->description; ?></textarea>
            </p>
            <p>Choose an icon of item</p>
            <div data-element-type="Pure.Admin.Preview.Image.Container">
                <img data-element-type="Pure.Admin.Preview.Image" src="<?php echo $data->attachment_url; ?>" data-storage-id="<?php echo $index; ?>" pure-wordpress-media-images-default-src="<?php echo Initialization::instance()->configuration->urls->images.'/no_image.png' ?>"/>
                <div data-element-type="Pure.Admin.Preview.Image.Controls.Container">
                    <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Load" pure-wordpress-media-images-add-selector="*[data-storage-id=|<?php echo $index; ?>|]" pure-wordpress-media-images-displayed>load</div>
                    <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Remove" pure-wordpress-media-images-remove-selector="*[data-storage-id=|<?php echo $index; ?>|]" pure-wordpress-media-images-displayed>remove</div>
                </div>
            </div>
            <input data-element-type="Pure.Admin.Preview.Image" data-storage-id="<?php echo $index; ?>" id="<?php echo $this->get_field_id( 'icons' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'icons' ); ?>[<?php echo $index; ?>]" value="<?php echo $data->attachment_id; ?>" type="text"/>
            <p data-element-type="Pure.Admin.Title">Choose link of item</p>
            <p>
                <label for="<?php echo $this->get_field_id( 'urls' ); ?>[<?php echo $index; ?>]">Any your URL</label>
                <input data-element-type="Pure.Admin.Input" id="<?php echo $this->get_field_id( 'urls' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'urls' ); ?>[<?php echo $index; ?>]" type="text" value="<?php echo $data->url; ?>"/>
            </p>
            <label for="<?php echo $this->get_field_id( 'post_ids' ); ?>[<?php echo $index; ?>]">or link to post</label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'post_ids' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'post_ids' ); ?>[<?php echo $index; ?>]">
                <option value="-1" <?php selected( $data->post_id, '-1' ); ?>>no</option>
                <?php
                foreach($posts as $post){
                    ?>
                    <option <?php selected( $data->post_id, $post->ID ); ?> value="<?php echo $post->ID; ?>">[id: <?php echo $post->ID.'] '.$post->post_title; ?></option>
                <?php
                }
                ?>
            </select>
            <label for="<?php echo $this->get_field_id( 'page_ids' ); ?>[<?php echo $index; ?>]">or link to page</label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'page_ids' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'page_ids' ); ?>[<?php echo $index; ?>]">
                <option value="-1" <?php selected( $data->page_id, '-1' ); ?>>no</option>
                <?php
                foreach($pages as $page){
                    ?>
                    <option <?php selected( $data->page_id, $page->ID ); ?> value="<?php echo $page->ID; ?>">[id: <?php echo $page->ID.'] '.$page->post_title; ?></option>
                <?php
                }
                ?>
            </select>
            <?php
            $containers->close(
                array(
                    "echo"              =>true,
                )
            );
        }
        private function innerHTMLTemplateItem($containers, $posts, $pages){
            ?>
            <div data-muliitems-index-template="<?php echo $this->get_field_id( 'Items' );?>[[index]]"
                 data-muliitems-template>
                <?php
                $containers->open(
                    array(
                        "title"             =>"Content",
                        "opened"            =>true,
                        'style_content'     =>'padding:0.5em;',
                        "container_attr"    =>'data-muliitems-parent-of="'.$this->get_field_id( 'Items' ).'[[index]]"',
                        "remove_attr"       =>'data-muliitems-under-control-template="'.$this->get_field_id( 'Items' ).'[[index]]"',
                        "remove_title"      =>__('remove', 'pure'),
                        "id"                =>$this->get_field_id( 'Items' ).'[[index]]',
                        "on_change"         =>'pure.wordpress.media.images.init',
                        "echo"              =>true,
                    )
                );
                ?>
                <p data-element-type="Pure.Admin.Title">Basic</p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'titles' ); ?>[[index]]">Title of item</label>
                    <textarea data-element-type="Pure.Admin.TextArea" id="<?php echo $this->get_field_id( 'titles' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'titles' ); ?>[[index]]" type="text" value=""></textarea>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'descriptions' ); ?>[[index]]">Description of item</label>
                    <textarea data-element-type="Pure.Admin.TextArea" id="<?php echo $this->get_field_id( 'descriptions' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'descriptions' ); ?>[[index]]" type="text" value=""></textarea>
                </p>
                <p>Choose an icon of item</p>
                <div data-element-type="Pure.Admin.Preview.Image.Container">
                    <img data-element-type="Pure.Admin.Preview.Image" src="<?php echo Initialization::instance()->configuration->urls->images.'/no_image.png' ?>" data-storage-id="[index]" pure-wordpress-media-images-default-src="<?php echo Initialization::instance()->configuration->urls->images.'/no_image.png' ?>"/>
                    <div data-element-type="Pure.Admin.Preview.Image.Controls.Container">
                        <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Load" pure-wordpress-media-images-add-selector="*[data-storage-id=|[index]|]" pure-wordpress-media-images-displayed>load</div>
                        <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Remove" pure-wordpress-media-images-remove-selector="*[data-storage-id=|[index]|]" pure-wordpress-media-images-displayed>remove</div>
                    </div>
                </div>
                <input data-element-type="Pure.Admin.Preview.Image" data-storage-id="[index]" id="<?php echo $this->get_field_id( 'icons' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'icons' ); ?>[[index]]" value="" type="text"/>
                <p data-element-type="Pure.Admin.Title">Choose link of item</p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'urls' ); ?>[[index]]">Any your URL</label>
                    <input data-element-type="Pure.Admin.Input" id="<?php echo $this->get_field_id( 'urls' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'urls' ); ?>[[index]]" type="text" value=""/>
                </p>
                <label for="<?php echo $this->get_field_id( 'post_ids' ); ?>[[index]]">or link to post</label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'post_ids' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'post_ids' ); ?>[[index]]">
                    <option value="-1" selected>no</option>
                    <?php
                    foreach($posts as $post){
                        ?>
                        <option value="<?php echo $post->ID; ?>">[id: <?php echo $post->ID.'] '.$post->post_title; ?></option>
                    <?php
                    }
                    ?>
                </select>
                <label for="<?php echo $this->get_field_id( 'page_ids' ); ?>[[index]]">or link to page</label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'page_ids' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'page_ids' ); ?>[[index]]">
                    <option value="-1" selected>no</option>
                    <?php
                    foreach($pages as $page){
                        ?>
                        <option value="<?php echo $page->ID; ?>">[id: <?php echo $page->ID.'] '.$page->post_title; ?></option>
                    <?php
                    }
                    ?>
                </select>
                <?php
                $containers->close(
                    array(
                        "echo"              =>true,
                    )
                );
                ?>
            </div>
            <?php
        }
    }
}
?>