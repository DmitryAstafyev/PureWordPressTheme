<?php
namespace Pure\Plugins\Quotes {
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
            $_parameters    = array(
                'target'		=> (isset($instance['target'		]) ? $instance['target'		    ] : ''  ),
                'title'		    => (isset($instance['title'		    ]) ? $instance['title'		    ] : ''  ),
                'title_type'    => (isset($instance['title_type'    ]) ? $instance['title_type'		] : 'B' ),
                'displayed'		=> (isset($instance['displayed'		]) ? $instance['displayed'		] : ''  ),
                'random'		=> (isset($instance['random'		]) ? $instance['random'		    ] : ''  ),
                'template'		=> (isset($instance['template'		]) ? $instance['template'		] : 'A' ),
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
                $ErrorMessages->show("\\Pure\\Plugins\\Quotes\\Widget\\widget::: ".$e);
                $ErrorMessages = NULL;
                return NULL;
            }
        }
        public function update($new_instance, $old_instance)
        {
            $instance 				  = array();
            $instance['title'		] = strip_tags( $new_instance['title'			] );
            $instance['title_type'  ] = strip_tags( $new_instance['title_type'      ] );
            $instance['target'		] = strip_tags( $new_instance['target'			] );
            $instance['template'    ] = strip_tags( $new_instance['template'	    ] );
            $instance['random'      ] = strip_tags( $new_instance['random'	        ] );
            $instance['displayed'   ] = strip_tags( $new_instance['displayed'	    ] );
            return $instance;
        }
        public function form($instance)
        {
            $templatesQuotes    = \Pure\Templates\BuddyPress\QuotesRender\Initialization::instance()->templates;
            $templatesTitle     = \Pure\Templates\Titles\Initialization                 ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization           ::instance()->get('A');
            $title 			    = isset( $instance[ 'title' 		] )  ? $instance[ 'title' 			] : '';
            $title_type		    = isset( $instance[ 'title_type' 	] )  ? $instance[ 'title_type' 		] : 'B';
            $target		        = isset( $instance[ 'target' 		] )  ? $instance[ 'target' 		    ] : '';
            $template		    = isset( $instance[ 'template'      ] )  ? $instance[ 'template'	    ] : 'A';
            $random		        = isset( $instance[ 'random'        ] )  ? $instance[ 'random'	        ] : false;
            $displayed		    = isset( $instance[ 'displayed'     ] )  ? $instance[ 'displayed'	    ] : false;
            ?>
            <?php
            $groups->open(array(    "title" =>"Title",
                                    "group" =>"Quotes",
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
                "group" =>"Quotes",
                "echo"  =>true));
            ?>
            <p>Choose template of insert</p>
            <?php
            foreach ($templatesQuotes as $templateQuote){
                ?>
                <p>
                    <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templateQuote->key; ?>" <?php checked( $templateQuote->key, $template ); ?> id="<?php echo $this->get_field_id( 'template'.$templateQuote->key ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'template'.$templateQuote->key ); ?>">Template <?php echo $templateQuote->key; ?> <br />
                        <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $templateQuote->thumbnail; ?>">
                    </label>
                    <?php
                    \Pure\Templates\BuddyPress\QuotesRender\Initialization::instance()->description($templateQuote->key);
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
                                    "group" =>"Quotes",
                                    "echo"  =>true));
            ?>
                <p>Define here ID of user (or comma separated users).</p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'target' ); ?>">ID of user or group</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'target' ); ?>" name="<?php echo $this->get_field_name( 'target' ); ?>" type="text" value="<?php echo esc_attr( $target ); ?>"/>
                </p>
                <p>
                    <input class="checkbox" type="checkbox" <?php checked( (bool)$displayed, true ); ?> id="<?php echo $this->get_field_id( 'displayed' ); ?>" name="<?php echo $this->get_field_name( 'displayed' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'displayed' ); ?>">Associate with displayed member</label>
                </p>
                <p>
                    <input class="checkbox" type="checkbox" <?php checked( (bool)$random, true ); ?> id="<?php echo $this->get_field_id( 'random' ); ?>" name="<?php echo $this->get_field_name( 'random' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'random' ); ?>">Display random quote of random user</label>
                </p>
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