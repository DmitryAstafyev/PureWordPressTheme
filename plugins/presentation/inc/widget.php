<?php
namespace Pure\Plugins\Presentation {
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
            $out 	        = $args['before_widget'];
            $_parameters    = array(	'content'	        => $instance['content'		    ],
                                        'targets'	        => $instance['targets'		    ],
                                        'template'          => $instance['template'	        ],
                                        'maxcount'          => $instance['maxcount'	        ],
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
                $ErrorMessages->show("\\Pure\\Plugins\\Presentation\\Widget\\widget::: ".$e);
                $ErrorMessages = NULL;
                return NULL;
            }
        }
        public function update($new_instance, $old_instance)
        {
            $instance 				  = array();
            $instance['content'		    ] = strip_tags( $new_instance['content'			] );
            $instance['targets'		    ] = strip_tags( $new_instance['targets'			] );
            $instance['template'        ] = strip_tags( $new_instance['template'	    ] );
            $instance['maxcount'        ] = strip_tags( $new_instance['maxcount'	    ] );
            return $instance;
        }
        public function form($instance)
        {
            $groups             = \Pure\Templates\Admin\Groups\Initialization   ::instance()->get('A');
            $templates          = \Pure\Templates\Presentation\Initialization   ::instance()->templates;
            $content		    = isset( $instance[ 'content' 		    ] )  ? $instance[ 'content' 		] : 'last';
            $targets		    = isset( $instance[ 'targets' 		    ] )  ? $instance[ 'targets' 		] : '';
            $template		    = isset( $instance[ 'template'          ] )  ? $instance[ 'template' 		] : 'A';
            $maxcount		    = isset( $instance[ 'maxcount'          ] )  ? $instance[ 'maxcount' 		] : 5;
            ?>
            <?php
            $groups->open(array(    "title" =>"Template",
                                    "group" =>"Presentation",
                                    "echo"  =>true));
            ?>
                <p>Choose template of presentation</p>
                <?php
                foreach ($templates as $_template){
                    ?>
                    <p>
                        <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $_template->key; ?>" <?php checked( $_template->key, $template ); ?> id="<?php echo $this->get_field_id( 'template'.$_template->key ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'template'.$_template->key ); ?>">Template <?php echo $_template->key; ?> <br />
                            <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $_template->thumbnail; ?>">
                        </label>
                        <?php
                        \Pure\Templates\Presentation\Initialization::instance()->description($_template->key);
                        ?>
                    </p>
                <?php
                }
                ?>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Show",
                                    "group" =>"Presentation",
                                    "echo"  =>true));
            ?>
                <select class="widefat" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>">
                    <?php
                    foreach(\Pure\Providers\Posts\Initialization::instance()->instances as $provider){
                        ?>
                        <option value="<?php echo $provider->key; ?>" <?php selected( $content, $provider->key ); ?>><?php echo $provider->description; ?></option>
                    <?php
                    }
                    ?>
                </select>
                <p>
                    <label for="<?php echo $this->get_field_id( 'maxcount' ); ?>">Maximum count of records</label>
                    <input class="widefat" type="number" id="<?php echo $this->get_field_id( 'maxcount' ); ?>" name="<?php echo $this->get_field_name( 'maxcount' ); ?>" type="text" value="<?php echo esc_attr( $maxcount ); ?>"/>
                </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"IDs",
                                    "group" =>"Presentation",
                                    "echo"  =>true));
            ?>
                <p>Define here ID of category (or comma separated categories). Or define here ID of author (or comma separated authors).</p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'targets' ); ?>">ID of category or author</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'targets' ); ?>" name="<?php echo $this->get_field_name( 'targets' ); ?>" type="text" value="<?php echo esc_attr( $targets ); ?>"/>
                </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
        <?php
            $templatesPosts     = NULL;
            $templatesTitle     = NULL;
            $templatesTabs      = NULL;
            $templatesSliders   = NULL;
            $groups             = NULL;
        }
    }
}
?>