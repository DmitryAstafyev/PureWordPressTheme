<?php
namespace Pure\Plugins\Thumbnails\Gallery {
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
            $_parameters    = array(	'content'	        => $instance['content'		    ],
                                        'tragets'	        => $instance['targets'		    ],
                                        'title'		        => $instance['title'		    ],
                                        'title_type'        => $instance['title_type'       ],
                                        'maxcount'	        => $instance['maxcount'		    ],
                                        'profile'	        => $instance['profile'		    ],
                                        'days'	            => $instance['days'		        ],
                                        'from_date'         => $instance['from_date'	    ],
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
                $ErrorMessages->show("\\Pure\\Plugins\\Thumbnails\\Authors\\Widget\\widget::: ".$e);
                $ErrorMessages = NULL;
                return NULL;
            }
        }
        public function update($new_instance, $old_instance)
        {
            $instance 				          = array();
            $instance['title'		        ] = strip_tags( $new_instance['title'			    ] );
            $instance['title_type'          ] = strip_tags( $new_instance['title_type'          ] );
            $instance['content'		        ] = strip_tags( $new_instance['content'			    ] );
            $instance['targets'		        ] = strip_tags( $new_instance['targets'			    ] );
            $instance['maxcount'	        ] = strip_tags( $new_instance['maxcount'		    ] );
            $instance['profile'		        ] = strip_tags( $new_instance['profile'			    ] );
            $instance['days'		        ] = strip_tags( $new_instance['days'			    ] );
            $instance['from_date'           ] = strip_tags( $new_instance['from_date'	        ] );
            $instance['template'            ] = strip_tags( $new_instance['template'	        ] );
            return $instance;
        }
        public function form($instance)
        {
            $templatesGalleries = \Pure\Templates\Galleries\Initialization      ::instance()->templates;
            $templatesTitle     = \Pure\Templates\Titles\Initialization         ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization   ::instance()->get('A');
            $title 			    = isset( $instance[ 'title' 		    ] )  ? $instance[ 'title' 			    ] : '';
            $title_type		    = isset( $instance[ 'title_type' 	    ] )  ? $instance[ 'title_type' 		    ] : 'B';
            $content		    = isset( $instance[ 'content' 		    ] )  ? $instance[ 'content' 		    ] : 'last';
            $targets		    = isset( $instance[ 'targets' 		    ] )  ? $instance[ 'targets' 		    ] : '';
            $maxcount		    = isset( $instance[ 'maxcount' 		    ] )  ? $instance[ 'maxcount'		    ] : '10';
            $profile		    = isset( $instance[ 'profile' 		    ] )  ? $instance[ 'profile'			    ] : '#';
            $days		        = isset( $instance[ 'days' 		        ] )  ? $instance[ 'days'			    ] : '30';
            $from_date		    = isset( $instance[ 'from_date'         ] )  ? $instance[ 'from_date'	        ] : '';
            $template		    = isset( $instance[ 'template' 	        ] )  ? $instance[ 'template' 		    ] : 'G';
            ?>
            <?php
            $groups->open(array(    "title" =>"Title",
                                    "group" =>"ThumbnailsGallery",
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
            $groups->open(array(    "title" =>"Template",
                                    "group" =>"ThumbnailsGallery",
                                    "echo"  =>true));
            ?>
                <p>Choose template of thumbnail of gallery</p>
                <?php
                foreach ($templatesGalleries as $templateGalleries){
                    ?>
                    <p>
                        <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templateGalleries->key; ?>" <?php checked( $templateGalleries->key, $template ); ?> id="<?php echo $this->get_field_id( 'template'.$templateGalleries->key ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'template'.$templateGalleries->key ); ?>">Template <?php echo $templateGalleries->key; ?> <br />
                            <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $templateGalleries->thumbnail; ?>">
                        </label>
                        <?php
                        \Pure\Templates\Galleries\Initialization::instance()->description($templateGalleries->key);
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
                                    "group" =>"ThumbnailsGallery",
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
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"IDs",
                                    "group" =>"ThumbnailsGallery",
                                    "echo"  =>true));
            ?>
                <p>Define here ID of author (or comma separated authors). Or define here ID of categories (or comma separated categories).</p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'targets' ); ?>">ID of post(s) or category(s)</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'targets' ); ?>" name="<?php echo $this->get_field_name( 'targets' ); ?>" type="text" value="<?php echo esc_attr( $targets ); ?>"/>
                </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Period",
                                    "group" =>"ThumbnailsGallery",
                                    "echo"  =>true));
            ?>
                <p>
                    <label for="<?php echo $this->get_field_id( 'from_date' ); ?>">Point of reference</label>
                    <input class="widefat" type="date" placeholder="<?php echo date("m.d.y"); ?>" id="<?php echo $this->get_field_id( 'from_date' ); ?>" name="<?php echo $this->get_field_name( 'from_date' ); ?>" type="text" value="<?php echo esc_attr( $from_date ); ?>"/>
                    <img alt="" data-type="Pure.Configuration.Img" src="<?php echo Initialization::instance()->configuration->urls->images.'\datescheme.png'; ?>">
                    <p>From this date will be considered time for posts. Empty - current date. Format: dd/mm/yyyy or dd.mm.yyyy</p>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'days' ); ?>">Number of days</label>
                    <input class="widefat" type="number" id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="text" value="<?php echo esc_attr( $days ); ?>"/>
                <p>Number of days for post query. For example, 30 means, what will be considered only posts written within 30 days.</p>
                </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Other",
                                    "group" =>"ThumbnailsGallery",
                                    "echo"  =>true));
            ?>
                <p>
                    <label for="<?php echo $this->get_field_id( 'maxcount' ); ?>">Maximum count of records</label>
                    <input class="widefat" type="number" id="<?php echo $this->get_field_id( 'maxcount' ); ?>" name="<?php echo $this->get_field_name( 'maxcount' ); ?>" type="text" value="<?php echo esc_attr( $maxcount ); ?>"/>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'profile' ); ?>">Template for url of author profile</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'profile' ); ?>" name="<?php echo $this->get_field_name( 'profile' ); ?>" type="text" value="<?php echo esc_attr( $profile ); ?>"/>
                    <p>Define here template for url to author's profile. Use [login] to define place of author login in url. For example: "http://mysite.com/users/[login]/profile"</p>
                </p>
                <p>Version: <?php echo Initialization::instance()->configuration->version; ?></p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
        <?php
            $groups             = NULL;
            $templatesTitle     = NULL;
            $templatesGalleries = NULL;
        }
    }
}
?>