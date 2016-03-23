<?php
namespace Pure\Plugins\Search {
    class Widget extends \WP_Widget {
        public function __construct() {
            parent::__construct(
                Initialization::instance()->configuration->id,                                      // id
                Initialization::instance()->configuration->name, 		                            // name
                array( 'description' => Initialization::instance()->configuration->description )    // description
            );
        }
        public function widget($args, $instance)
        {
            $title 	        = apply_filters( 'widget_title', $instance['title'] );
            $out 	        = $args['before_widget'];
            $_parameters    = array(	'title'		=> $instance['title'		],
                                        'background'=> $instance['background'   ],
                                        'template'  => $instance['template'	    ]
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
                $ErrorMessages->show("\\Pure\\Plugins\\Tags\\Widget\\widget::: ".$e);
                $ErrorMessages = NULL;
                return NULL;
            }
        }
        public function update($new_instance, $old_instance)
        {
            $this->pureUpdated          = true;
            $instance 				    = array();
            $instance['title'		]   = strip_tags( $new_instance['title'			] );
            $instance['background'  ]   = strip_tags( $new_instance['background'      ] );
            $instance['template'    ]   = strip_tags( $new_instance['template'	    ] );
            return $instance;
        }
        private function call_scripts_after_update(){
            ?>
            <script type="text/javascript">
                if(typeof pure === "object"){
                    (pure.system.getInstanceByPath("pure.wordpress.media.images"        ) !== null ? pure.system.getInstanceByPath("pure.wordpress.media.images"        ).init() : null);
                }
            </script>
        <?php
        }
        public function form($instance)
        {
            if (isset($this->pureUpdated) !== false){
                if ($this->pureUpdated !== false){
                    $this->call_scripts_after_update();
                }
            }
            $templates          = \Pure\Templates\Elements\Search\Initialization        ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization           ::instance()->get('A');
            $title 			    = isset( $instance[ 'title' 		] )  ? $instance[ 'title' 			] : '';
            $background		    = isset( $instance[ 'background' 	] )  ? $instance[ 'background' 		] : '';
            $template		    = isset( $instance[ 'template'      ] )  ? $instance[ 'template'	    ] : 'A';
            ?>
            <?php
            $groups->open(array(    "title" =>"Title",
                                    "group" =>"Search",
                                    "echo"  =>true));
            ?>
                <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title of widget</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
                </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(
                "title" =>"Template",
                "group" =>"Search",
                "echo"  =>true));
            ?>
            <p>Choose template</p>
            <?php
            foreach ($templates as $_template){
                ?>
                <p>
                    <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $_template->key; ?>" <?php checked( $_template->key, $template ); ?> id="<?php echo $this->get_field_id( 'template'.$_template->key ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'template'.$_template->key ); ?>">Template <?php echo $_template->key; ?> <br />
                        <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $_template->thumbnail; ?>">
                    </label>
                    <?php
                    \Pure\Templates\Elements\Categories\Initialization::instance()->description($_template->key);
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
                "title" =>"Background",
                "group" =>"Search",
                "echo"  =>true));
            $storage_id = uniqid();
            if ($background !== ''){
                $attachment = wp_get_attachment_image_src( $background, 'thumbnail', false );
                if (is_array($attachment) !== false){
                    $attachment_url = $attachment[0];
                }else{
                    $attachment_url = Initialization::instance()->configuration->urls->images.'/no_image.png';
                }
            }else{
                $attachment_url = Initialization::instance()->configuration->urls->images.'/no_image.png';
            }
            ?>
            <p>Choose background</p>
            <div data-element-type="Pure.Admin.Preview.Image.Container">
                <img data-element-type="Pure.Admin.Preview.Image" src="<?php echo $attachment_url; ?>" data-storage-id="<?php echo $storage_id; ?>" pure-wordpress-media-images-default-src="<?php echo Initialization::instance()->configuration->urls->images.'/no_image.png' ?>"/>
                <div data-element-type="Pure.Admin.Preview.Image.Controls.Container">
                    <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Load" pure-wordpress-media-images-add-selector="*[data-storage-id=|<?php echo $storage_id; ?>|]">load</div>
                    <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Remove" pure-wordpress-media-images-remove-selector="*[data-storage-id=|<?php echo $storage_id; ?>|]">remove</div>
                </div>
            </div>
            <input data-element-type="Pure.Admin.Preview.Image" data-storage-id="<?php echo $storage_id; ?>" id="<?php echo $this->get_field_id( 'background' ); ?>" name="<?php echo $this->get_field_name( 'background' ); ?>" value="<?php echo $background; ?>" type="text"/>
            <?php
            $groups->close(array("echo"=>true));
            ?>
        <?php
            $templatesComments  = NULL;
            $templatesTitle     = NULL;
            $groups             = NULL;
            \Pure\Components\WordPress\Media\Resources\Initialization::instance()->attach();
        }
    }
}
?>