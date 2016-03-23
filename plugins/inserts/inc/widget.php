<?php
namespace Pure\Plugins\Inserts {
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
            $_parameters    = array(	'target'	=> $instance['target'		],
                                        'title'		=> $instance['title'		],
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
                $ErrorMessages->show("\\Pure\\Plugins\\Inserts\\Widget\\widget::: ".$e);
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
            return $instance;
        }
        public function form($instance)
        {
            $templatesInserts   = \Pure\Templates\Inserts\Initialization                ::instance()->templates;
            $templatesTitle     = \Pure\Templates\Titles\Initialization                 ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization           ::instance()->get('A');
            $title 			    = isset( $instance[ 'title' 		] )  ? $instance[ 'title' 			] : '';
            $title_type		    = isset( $instance[ 'title_type' 	] )  ? $instance[ 'title_type' 		] : 'B';
            $target		        = isset( $instance[ 'target' 		] )  ? $instance[ 'target' 		    ] : '';
            $template		    = isset( $instance[ 'template'      ] )  ? $instance[ 'template'	    ] : 'A';
            ?>
            <?php
            $groups->open(array(    "title" =>"Title",
                                    "group" =>"Inserts",
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
                "group" =>"Inserts",
                "echo"  =>true));
            ?>
            <p>Choose template of insert</p>
            <?php
            foreach ($templatesInserts as $templateInsert){
                ?>
                <p>
                    <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templateInsert->key; ?>" <?php checked( $templateInsert->key, $template ); ?> id="<?php echo $this->get_field_id( 'template'.$templateInsert->key ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'template'.$templateInsert->key ); ?>">Template <?php echo $templateInsert->key; ?> <br />
                        <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $templateInsert->thumbnail; ?>">
                    </label>
                    <?php
                    \Pure\Templates\Inserts\Initialization::instance()->description($templateInsert->key);
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
                                    "group" =>"Inserts",
                                    "echo"  =>true));
            $inserts = get_posts(
                array(
                    'numberposts'     => 1000,
                    'orderby'         => 'post_date',
                    'order'           => 'DESC',
                    'post_type'       => 'insert',
                    'post_status'     => 'publish'
                )
            );
            ?>
                <select class="widefat" id="<?php echo $this->get_field_id( 'target' ); ?>" name="<?php echo $this->get_field_name( 'target' ); ?>">
                    <?php
                    foreach($inserts as $insert){
                        ?>
                        <option value="<?php echo $insert->ID; ?>" <?php selected( $target, $insert->ID ); ?>><?php echo $insert->post_title; ?></option>
                    <?php
                    }
                    ?>
                </select>
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