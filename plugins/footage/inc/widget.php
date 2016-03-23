<?php
namespace Pure\Plugins\Footage {
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
            if (is_array($URLs) !== false){
                foreach($URLs as $key=>$url){
                    $URLs[$key] = \Pure\Resources\Names::instance()->repairURL($url);
                }
            }else{
                $URLs = \Pure\Resources\Names::instance()->repairURL($URLs);
            }
            return $URLs;
        }
        public function widget($args, $instance)
        {
            $title 	        = apply_filters( 'widget_title', $instance['title'] );
            $out 	        = $args['before_widget'];
            $_parameters    = array(	'title'		        => $instance['title'		    ],
                                        'description'       => $instance['description'      ],
                                        'link'              => $this->repairURLs($instance['link']),
                                        'link_label'        => $instance['link_label'       ],
                                        'alt_background'    => $instance['alt_background'   ],
                                        'types'             => $instance['types'	        ],
                                        'srcs'              => $this->repairURLs($instance['srcs']),
                                        'template'          => $instance['template'	        ]
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
                $ErrorMessages->show("\\Pure\\Plugins\\Footage\\Widget\\widget::: ".$e);
                $ErrorMessages = NULL;
                return NULL;
            }
        }
        public function update($new_instance, $old_instance)
        {
            $this->pureUpdated              = true;
            $instance 				        = array();
            $instance['title'		    ]   = strip_tags( $new_instance['title'			    ] );
            $instance['description'     ]   = strip_tags( $new_instance['description'       ] );
            $instance['link'            ]   = strip_tags( $new_instance['link'	            ] );
            $instance['link'            ]   = \Pure\Resources\Names::instance()->clearURL($instance['link'            ] );
            $instance['link_label'      ]   = strip_tags( $new_instance['link_label'        ] );
            $instance['alt_background'  ]   = strip_tags( $new_instance['alt_background'    ] );
            $instance['template'        ]   = strip_tags( $new_instance['template'	        ] );
            $instance['types'           ]   = array();
            $instance['srcs'            ]   = array();
            if ( isset ( $new_instance['types'] ) ){
                foreach ( $new_instance['types'] as $value ){
                    array_push($instance['types'], $value);
                }
            }
            if ( isset ( $new_instance['srcs'] ) ){
                foreach ( $new_instance['srcs'] as $value ){
                    array_push($instance['srcs'], \Pure\Resources\Names::instance()->clearURL($value));
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
        public function form($instance)
        {
            if ($this->pureUpdated !== false){
                $this->call_scripts_after_update();
            }
            $templates          = \Pure\Templates\Footage\Initialization                ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization           ::instance()->get('A');
            $containers         = \Pure\Templates\Admin\Groups\Initialization           ::instance()->get('D');
            $title 			    = isset( $instance[ 'title' 		    ] )  ? $instance[ 'title' 			] : '';
            $description	    = isset( $instance[ 'description' 	    ] )  ? $instance[ 'description'     ] : '';
            $link		        = isset( $instance[ 'link' 	            ] )  ? $instance[ 'link' 		    ] : '';
            $link               = $this->repairURLs($link);
            $link_label		    = isset( $instance[ 'link_label' 	    ] )  ? $instance[ 'link_label' 		] : '';
            $alt_background	    = isset( $instance[ 'alt_background' 	] )  ? $instance[ 'alt_background'  ] : '';
            $template		    = isset( $instance[ 'template'          ] )  ? $instance[ 'template'	    ] : 'A';
            $types		        = isset( $instance[ 'types' 		    ] )  ? $instance[ 'types' 		    ] : array();
            $srcs               = isset( $instance[ 'srcs'              ] )  ? $instance[ 'srcs'            ] : array();
            $srcs               = $this->repairURLs($srcs);
            ?>
            <?php
            $groups->open(array(    "title" =>"Text block",
                                    "group" =>"Footage",
                                    "echo"  =>true));
            ?>
                <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title of text block on footage</label>
                    <textarea data-element-type="Pure.Admin.TextArea" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>"><?php echo esc_attr( $title ); ?></textarea>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'description' ); ?>">Text block on footage</label>
                    <textarea data-element-type="Pure.Admin.TextArea" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo esc_attr( $description ); ?></textarea>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'link' ); ?>">If you want show some link, define here url</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>"/>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'link_label' ); ?>">Caption for url</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'link_label' ); ?>" name="<?php echo $this->get_field_name( 'link_label' ); ?>" type="text" value="<?php echo esc_attr( $link_label ); ?>"/>
                </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(
                "title" =>"Template",
                "group" =>"Footage",
                "echo"  =>true));
            ?>
            <p>Choose template of footage</p>
            <?php
            foreach ($templates as $_template){
                ?>
                <p>
                    <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $_template->key; ?>" <?php checked( $_template->key, $template ); ?> id="<?php echo $this->get_field_id( 'template'.$_template->key ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'template'.$_template->key ); ?>">Template <?php echo $_template->key; ?> <br />
                        <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $_template->thumbnail; ?>">
                    </label>
                    <?php
                    \Pure\Templates\Footage\Initialization::instance()->description($_template->key);
                    ?>
                </p>
            <?php
            }
            ?>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Footage",
                                    "group" =>"Footage",
                                    "echo"  =>true));
            if ($alt_background !== ''){
                $attachment = wp_get_attachment_image_src( $alt_background, 'thumbnail', false );
                if (is_array($attachment) !== false){
                    $attachment_url = $attachment[0];
                }else{
                    $attachment_url = Initialization::instance()->configuration->urls->images.'/no_image.png';
                }
            }else{
                $attachment_url = Initialization::instance()->configuration->urls->images.'/no_image.png';
            }
            $storage_id = uniqid();
            ?>
            <p>Choose an alternative image for footage (use same size as video)</p>
            <div data-element-type="Pure.Admin.Preview.Image.Container">
                <img data-element-type="Pure.Admin.Preview.Image" src="<?php echo $attachment_url; ?>" data-storage-id="<?php echo $storage_id; ?>" pure-wordpress-media-images-default-src="<?php echo Initialization::instance()->configuration->urls->images.'/no_image.png' ?>"/>
                <div data-element-type="Pure.Admin.Preview.Image.Controls.Container">
                    <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Load" pure-wordpress-media-images-add-selector="*[data-storage-id=|<?php echo $storage_id; ?>|]">load</div>
                    <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Remove" pure-wordpress-media-images-remove-selector="*[data-storage-id=|<?php echo $storage_id; ?>|]">remove</div>
                </div>
            </div>
            <input data-element-type="Pure.Admin.Preview.Image" data-storage-id="<?php echo $storage_id; ?>" id="<?php echo $this->get_field_id( 'alt_background' ); ?>" name="<?php echo $this->get_field_name( 'alt_background' ); ?>" value="<?php echo $alt_background; ?>" type="text"/>
            <?php
            for($index = 0; $index < count($srcs); $index ++){
                $this->innerHTMLItem(
                    $containers,
                    $index,
                    (object)array(
                        'type'  =>$types[$index],
                        'src'   =>$srcs[$index],
                    )
                );
            }
            ?>
            <a data-basic-type="Button" data-element-type="Pure.Admin.MultiItems.Add" data-muliitems-add-button data-muliitems-afteradd-handles="pure.wordpress.media.images.init">add new</a>
            <?php
            $this->innerHTMLTemplateItem($containers);
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $templates          = NULL;
            $templatesTitle     = NULL;
            $groups             = NULL;
            \Pure\Components\WordPress\Admin\Multiitems\Initialization::instance()->attach();
            \Pure\Components\WordPress\Media\Resources\Initialization::instance()->attach();
        }
        private function innerHTMLItem($containers, $index, $data){
            $containers->open(
                array(
                    "title"             =>($data->type !== '' ? $data->type : 'source'),
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
            <p>
                <label for="<?php echo $this->get_field_id( 'types' ); ?>[<?php echo $index; ?>]">Type (for example: "video/webm" or "video/mp4")</label>
                <input data-element-type="Pure.Admin.Input" id="<?php echo $this->get_field_id( 'types' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'types' ); ?>[<?php echo $index; ?>]" type="text" value="<?php echo $data->type; ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'srcs' ); ?>[<?php echo $index; ?>]">URL of source (of defined type)</label>
                <input data-element-type="Pure.Admin.Input" id="<?php echo $this->get_field_id( 'srcs' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'srcs' ); ?>[<?php echo $index; ?>]" type="text" value="<?php echo $data->src; ?>"/>
            </p>
            <?php
            $containers->close(
                array(
                    "echo"              =>true,
                )
            );
        }
        private function innerHTMLTemplateItem($containers){
            ?>
            <div data-muliitems-index-template="<?php echo $this->get_field_id( 'Items' );?>[[index]]"
                 data-muliitems-template>
                <?php
                $containers->open(
                    array(
                        "title"             =>"Source",
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
                <p>
                    <label for="<?php echo $this->get_field_id( 'types' ); ?>[[index]]">Type (for example: "video/webm" or "video/mp4")</label>
                    <input data-element-type="Pure.Admin.Input" id="<?php echo $this->get_field_id( 'types' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'types' ); ?>[[index]]" type="text" value=""/>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'srcs' ); ?>[[index]]">URL of source (of defined type)</label>
                    <input data-element-type="Pure.Admin.Input" id="<?php echo $this->get_field_id( 'srcs' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'srcs' ); ?>[[index]]" type="text" value=""/>
                </p>
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