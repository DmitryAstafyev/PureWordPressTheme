<?php
namespace Pure\Plugins\Tags {
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
                                        'title_type'=> $instance['title_type'   ],
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
            $instance 				  = array();
            $instance['title'		] = strip_tags( $new_instance['title'			] );
            $instance['title_type'  ] = strip_tags( $new_instance['title_type'      ] );
            $instance['template'    ] = strip_tags( $new_instance['template'	    ] );
            return $instance;
        }
        public function form($instance)
        {
            $templates          = \Pure\Templates\Elements\Categories\Initialization    ::instance()->templates;
            $templatesTitle     = \Pure\Templates\Titles\Initialization                 ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization           ::instance()->get('A');
            $title 			    = isset( $instance[ 'title' 		] )  ? $instance[ 'title' 			] : '';
            $title_type		    = isset( $instance[ 'title_type' 	] )  ? $instance[ 'title_type' 		] : 'B';
            $template		    = isset( $instance[ 'template'      ] )  ? $instance[ 'template'	    ] : 'A';
            ?>
            <?php
            $groups->open(array(    "title" =>"Title",
                                    "group" =>"Tags",
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
                "group" =>"Tags",
                "echo"  =>true));
            ?>
            <p>Choose template of insert</p>
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
            $templatesComments  = NULL;
            $templatesTitle     = NULL;
            $groups             = NULL;
        }
    }
}
?>