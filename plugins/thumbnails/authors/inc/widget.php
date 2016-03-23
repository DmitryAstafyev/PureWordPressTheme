<?php
namespace Pure\Plugins\Thumbnails\Authors {
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
                'content'		    => (isset($instance['content'		    ]) ? $instance['content'		    ] : 'last'  ),
                'targets'		    => (isset($instance['targets'		    ]) ? $instance['targets'		    ] : ''      ),
                'template'		    => (isset($instance['template'		    ]) ? $instance['template'		    ] : 'A'     ),
                'title'		        => (isset($instance['title'		        ]) ? $instance['title'		        ] : ''      ),
                'title_type'		=> (isset($instance['title_type'		]) ? $instance['title_type'		    ] : 'B'     ),
                'maxcount'		    => (isset($instance['maxcount'		    ]) ? $instance['maxcount'		    ] : '10'    ),
                'only_with_avatar'  => (isset($instance['only_with_avatar'  ]) ? $instance['only_with_avatar'   ] : false   ),
                'top'		        => (isset($instance['top'		        ]) ? $instance['top'		        ] : true    ),
                'displayed'		    => (isset($instance['displayed'		    ]) ? $instance['displayed'		    ] : false   ),
                'profile'		    => (isset($instance['profile'		    ]) ? $instance['profile'		    ] : '#'     ),
                'days'		        => (isset($instance['days'		        ]) ? $instance['days'		        ] : '30'    ),
                'from_date'		    => (isset($instance['from_date'		    ]) ? $instance['from_date'		    ] : ''      ),
                'templates_settings'=> (isset($instance['templates_settings']) ? $instance['templates_settings' ] : array() ),
                'more'		        => (isset($instance['more'		        ]) ? $instance['more'		        ] : false   ),
                'wrapper'           => (isset($instance['wrapper'           ]) ? $instance['wrapper'            ] : false   ),
                'min_width'         => (isset($instance['min_width'         ]) ? $instance['min_width'          ] : 300     ),
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
            $instance['template'            ] = strip_tags( $new_instance['template'            ] );
            $instance['content'		        ] = strip_tags( $new_instance['content'			    ] );
            $instance['targets'		        ] = strip_tags( $new_instance['targets'			    ] );
            $instance['maxcount'	        ] = strip_tags( $new_instance['maxcount'		    ] );
            $instance['only_with_avatar'    ] = strip_tags( $new_instance['only_with_avatar'    ] );
            $instance['top'	                ] = strip_tags( $new_instance['top'		            ] );
            $instance['displayed'		    ] = strip_tags( $new_instance['displayed'		    ] );
            $instance['profile'		        ] = strip_tags( $new_instance['profile'			    ] );
            $instance['days'		        ] = strip_tags( $new_instance['days'			    ] );
            $instance['from_date'           ] = strip_tags( $new_instance['from_date'	        ] );
            $instance['more'                ] = strip_tags( $new_instance['more'	            ] );
            $instance['wrapper'             ] = (bool)strip_tags( $new_instance['wrapper'	            ] );
            $instance['min_width'           ] = (int)strip_tags( $new_instance['min_width'	        ] );
            $instance['templates_settings'  ] = $new_instance['templates_settings'  ];
            return $instance;
        }
        public function form($instance)
        {
            $templatesAuthor    = \Pure\Templates\Authors\Initialization        ::instance()->templates;
            $templatesTitle     = \Pure\Templates\Titles\Initialization         ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization   ::instance()->get('A');
            $title 			    = isset( $instance[ 'title' 		    ] )  ? $instance[ 'title' 			    ] : '';
            $title_type		    = isset( $instance[ 'title_type' 	    ] )  ? $instance[ 'title_type' 		    ] : 'B';
            $template		    = isset( $instance[ 'template' 	        ] )  ? $instance[ 'template' 		    ] : 'A';
            $content		    = isset( $instance[ 'content' 		    ] )  ? $instance[ 'content' 		    ] : 'last';
            $targets		    = isset( $instance[ 'targets' 		    ] )  ? $instance[ 'targets' 		    ] : '';
            $maxcount		    = isset( $instance[ 'maxcount' 		    ] )  ? $instance[ 'maxcount'		    ] : '10';
            $only_with_avatar   = isset( $instance[ 'only_with_avatar'  ] )  ? $instance[ 'only_with_avatar'    ] : false;
            $top		        = isset( $instance[ 'top' 		        ] )  ? $instance[ 'top'		            ] : true;
            $displayed		    = isset( $instance[ 'displayed' 	    ] )  ? $instance[ 'displayed'	        ] : false;
            $profile		    = isset( $instance[ 'profile' 		    ] )  ? $instance[ 'profile'			    ] : '#';
            $days		        = isset( $instance[ 'days' 		        ] )  ? $instance[ 'days'			    ] : '30';
            $from_date		    = isset( $instance[ 'from_date'         ] )  ? $instance[ 'from_date'	        ] : '';
            $more		        = isset( $instance[ 'more'              ] )  ? $instance[ 'more'	            ] : false;
            $wrapper		    = isset( $instance[ 'wrapper'           ] )  ? $instance[ 'wrapper'	            ] : false;
            $min_width		    = isset( $instance[ 'min_width'         ] )  ? $instance[ 'min_width'	        ] : 300;
            $templates_settings = isset( $instance[ 'templates_settings'] )  ? $instance[ 'templates_settings'  ] : array();
            ?>
            <?php
            $groups->open(array(    "title" =>"Title",
                                    "group" =>"ThumbnailsAuthors",
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
                                    "group" =>"ThumbnailsAuthors",
                                    "echo"  =>true));
            ?>
            <p>Choose template</p>
            <?php
            foreach ($templatesAuthor as $templateAuthor){
                ?>
                <p>
                    <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templateAuthor->key; ?>" <?php checked( $templateAuthor->key, $template ); ?> id="<?php echo $this->get_field_id( 'template'.$templateAuthor->key ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'template'.$templateAuthor->key ); ?>">Template <?php echo $templateAuthor->key; ?> <br />
                        <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $templateAuthor->thumbnail; ?>">
                    </label>
                    <?php
                        \Pure\Templates\Authors\Initialization::instance()->description($templateAuthor->key);
                        $settings = \Pure\Templates\Authors\Initialization::instance()->settings($templateAuthor->key);
                        if ($settings !== false){
                            $settings->show($templates_settings, true, $this->get_field_name('templates_settings'));
                            $settings = NULL;
                        }
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
                                    "group" =>"ThumbnailsAuthors",
                                    "echo"  =>true));
            ?>
            <select class="widefat" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>">
                <?php
                foreach(\Pure\Providers\Members\Initialization::instance()->instances as $provider){
                    ?>
                    <option value="<?php echo $provider->key; ?>" <?php selected( $content, $provider->key ); ?>><?php echo $provider->description; ?></option>
                    <?php
                }
                ?>
            </select>
            <p>
                <input class="checkbox" type="checkbox" <?php checked( (bool)$displayed, true ); ?> id="<?php echo $this->get_field_id( 'displayed' ); ?>" name="<?php echo $this->get_field_name( 'displayed' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'displayed' ); ?>">Associate with displayed member or group</label>
            </p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked( (bool)$only_with_avatar, true ); ?> id="<?php echo $this->get_field_id( 'only_with_avatar' ); ?>" name="<?php echo $this->get_field_name( 'only_with_avatar' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'only_with_avatar' ); ?>">Show authors with avatars only.</label>
            </p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked( (bool)$top, true ); ?> id="<?php echo $this->get_field_id( 'top' ); ?>" name="<?php echo $this->get_field_name( 'top' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'top' ); ?>">Allocate the first user. </label>
            </p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked( (bool)$more, true ); ?> id="<?php echo $this->get_field_id( 'more' ); ?>" name="<?php echo $this->get_field_name( 'more' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'more' ); ?>">Show button "more" (AJAX loader). </label>
            </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"IDs",
                                    "group" =>"ThumbnailsAuthors",
                                    "echo"  =>true));
            ?>
            <p>Define here ID of posts (or comma separated posts). Or define here ID of categories, groups and etc.</p>
            <p>
                <label for="<?php echo $this->get_field_id( 'targets' ); ?>">ID of post(s), category(s), group(s), user(s)</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'targets' ); ?>" name="<?php echo $this->get_field_name( 'targets' ); ?>" type="text" value="<?php echo esc_attr( $targets ); ?>"/>
            </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Period",
                                    "group" =>"ThumbnailsAuthors",
                                    "echo"  =>true));
            ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'from_date' ); ?>">Point of reference</label>
                <input class="widefat" type="date" placeholder="<?php echo date("m.d.y"); ?>" id="<?php echo $this->get_field_id( 'from_date' ); ?>" name="<?php echo $this->get_field_name( 'from_date' ); ?>" type="text" value="<?php echo esc_attr( $from_date ); ?>"/>
                <img alt="" data-type="Pure.Configuration.Img" src="<?php echo Initialization::instance()->configuration->urls->images.'/datescheme.png'; ?>">
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
            $groups->open(array(    "title" =>"Wrapper",
                                    "group" =>"ThumbnailsAuthors",
                                    "echo"  =>true));
            ?>
            <p>
                <input class="checkbox" type="checkbox" <?php checked( (bool)$wrapper, true ); ?> id="<?php echo $this->get_field_id( 'wrapper' ); ?>" name="<?php echo $this->get_field_name( 'wrapper' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'wrapper' ); ?>">Use wrapper for presentation content</label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'min_width' ); ?>">Minimal width of item in [px]</label>
                <input class="widefat" type="number" id="<?php echo $this->get_field_id( 'min_width' ); ?>" name="<?php echo $this->get_field_name( 'min_width' ); ?>" type="text" value="<?php echo esc_attr( $min_width ); ?>"/>
            </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Other",
                                    "group" =>"ThumbnailsAuthors",
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
            $templatesAuthor    = NULL;
            $templatesTitle     = NULL;
        }
    }
}
?>