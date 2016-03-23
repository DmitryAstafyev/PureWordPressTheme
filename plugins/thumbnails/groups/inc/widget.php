<?php
namespace Pure\Plugins\Thumbnails\Groups {
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
                                        'template'	        => $instance['template'		    ],
                                        'targets'	        => $instance['targets'		    ],
                                        'title'		        => $instance['title'		    ],
                                        'title_type'        => $instance['title_type'       ],
                                        'maxcount'	        => $instance['maxcount'		    ],
                                        'only_with_avatar'	=> $instance['only_with_avatar' ],
                                        'top'	            => $instance['top'		        ],
                                        'days'	            => $instance['days'		        ],
                                        'displayed'	        => $instance['displayed'	    ],
                                        'from_date'         => $instance['from_date'	    ],
                                        'show_content'      => $instance['show_content'	    ],
                                        'show_admin_part'   => $instance['show_admin_part'  ],
                                        'show_life'         => $instance['show_life'        ],
                                        'show_opened'       => (isset($instance['show_opened']) !== false ? $instance['show_opened'] : false),
                                        'more'              => $instance['more'	            ]
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
                $ErrorMessages->show("\\Pure\\Plugins\\Thumbnails\\Groups\\Widget\\widget::: ".$e);
                $ErrorMessages = NULL;
                return NULL;
            }
        }
        public function update($new_instance, $old_instance)
        {
            $instance 				          = array();
            $instance['title'		        ] = strip_tags( $new_instance['title'			    ] );
            $instance['template'		    ] = strip_tags( $new_instance['template'			] );
            $instance['title_type'          ] = strip_tags( $new_instance['title_type'          ] );
            $instance['content'		        ] = strip_tags( $new_instance['content'			    ] );
            $instance['targets'		        ] = strip_tags( $new_instance['targets'			    ] );
            $instance['maxcount'	        ] = strip_tags( $new_instance['maxcount'		    ] );
            $instance['only_with_avatar'    ] = strip_tags( $new_instance['only_with_avatar'    ] );
            $instance['top'	                ] = strip_tags( $new_instance['top'		            ] );
            $instance['displayed'		    ] = strip_tags( $new_instance['displayed'		    ] );
            $instance['days'		        ] = strip_tags( $new_instance['days'			    ] );
            $instance['from_date'           ] = strip_tags( $new_instance['from_date'	        ] );
            $instance['show_content'        ] = strip_tags( $new_instance['show_content'	    ] );
            $instance['show_admin_part'     ] = strip_tags( $new_instance['show_admin_part'	    ] );
            $instance['show_life'           ] = strip_tags( $new_instance['show_life'	        ] );
            $instance['more'                ] = strip_tags( $new_instance['more'	            ] );
            //$instance['Backgrounds'         ] = $new_instance['Backgrounds'];
            return $instance;
        }
        public function form($instance)
        {
            //echo var_dump($instance);
            $templatesGroups    = \Pure\Templates\Groups\Initialization         ::instance()->templates;
            $templatesTitle     = \Pure\Templates\Titles\Initialization         ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization   ::instance()->get('A');
            $title 			    = isset( $instance[ 'title' 		    ] )  ? $instance[ 'title' 			    ] : '';
            $template 			= isset( $instance[ 'template' 		    ] )  ? $instance[ 'template' 			] : 'A';
            $title_type		    = isset( $instance[ 'title_type' 	    ] )  ? $instance[ 'title_type' 		    ] : 'B';
            $content		    = isset( $instance[ 'content' 		    ] )  ? $instance[ 'content' 		    ] : 'last';
            $targets		    = isset( $instance[ 'targets' 		    ] )  ? $instance[ 'targets' 		    ] : '';
            $maxcount		    = isset( $instance[ 'maxcount' 		    ] )  ? $instance[ 'maxcount'		    ] : '10';
            $top		        = isset( $instance[ 'top' 		        ] )  ? $instance[ 'top'		            ] : true;
            $only_with_avatar   = isset( $instance[ 'only_with_avatar'  ] )  ? $instance[ 'only_with_avatar'    ] : false;
            $displayed		    = isset( $instance[ 'displayed'         ] )  ? $instance[ 'displayed'	        ] : 'none';
            $days		        = isset( $instance[ 'days' 		        ] )  ? $instance[ 'days'			    ] : '30';
            $from_date		    = isset( $instance[ 'from_date'         ] )  ? $instance[ 'from_date'	        ] : '';
            $show_content	    = isset( $instance[ 'show_content'      ] )  ? $instance[ 'show_content'	    ] : false;
            $show_admin_part    = isset( $instance[ 'show_admin_part'   ] )  ? $instance[ 'show_admin_part'	    ] : false;
            $show_life          = isset( $instance[ 'show_life'         ] )  ? $instance[ 'show_life'	        ] : false;
            $more		        = isset( $instance[ 'more'              ] )  ? $instance[ 'more'	            ] : false;


/*
 //Worked example
            $globalsettings = \Pure\Templates\Backgrounds\Initialization::instance()->globalsettings('JCrop');
            $globalsettings->show($instance['Backgrounds'], true, $this->get_field_name( 'Backgrounds' ));
            $globalsettings = NULL;
*/
            ?>
            <?php
            $groups->open(array(    "title" =>"Title",
                                    "group" =>"ThumbnailsGroups",
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
            foreach ($templatesGroups as $templateGroups){
                ?>
                <p>
                    <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templateGroups->key; ?>" <?php checked( $templateGroups->key, $template ); ?> id="<?php echo $this->get_field_id( 'template'.$templateGroups->key ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'template'.$templateGroups->key ); ?>">Template <?php echo $templateGroups->key; ?> <br />
                        <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $templateGroups->thumbnail; ?>">
                    </label>
                    <?php
                    \Pure\Templates\Groups\Initialization::instance()->description($templateGroups->key);
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
                                    "group" =>"ThumbnailsGroups",
                                    "echo"  =>true));
            ?>
                <select class="widefat" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>">
                    <?php
                    foreach(\Pure\Providers\Groups\Initialization::instance()->instances as $provider){
                        ?>
                        <option value="<?php echo $provider->key; ?>" <?php selected( $content, $provider->key ); ?>><?php echo $provider->description; ?></option>
                    <?php
                    }
                    ?>
                </select>
                <p>
                    <select class="widefat" id="<?php echo $this->get_field_id( 'displayed' ); ?>" name="<?php echo $this->get_field_name( 'displayed' ); ?>">
                        <option value="none" <?php selected( $displayed, "none" ); ?>><?php echo 'do not associate'; ?></option>
                        <option value="member" <?php selected( $displayed, "member" ); ?>><?php echo 'displayed member'; ?></option>
                        <option value="group" <?php selected( $displayed, "group" ); ?>><?php echo 'displayed group'; ?></option>
                    </select>
                    <label for="<?php echo $this->get_field_id( 'displayed' ); ?>">Associate with:</label>
                </p>
                <p>
                    <input class="checkbox" type="checkbox" <?php checked( (bool)$only_with_avatar, true ); ?> id="<?php echo $this->get_field_id( 'only_with_avatar' ); ?>" name="<?php echo $this->get_field_name( 'only_with_avatar' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'only_with_avatar' ); ?>">Show members with avatars only.</label>
                </p>
                <p>
                    <input class="checkbox" type="checkbox" <?php checked( (bool)$top, true ); ?> id="<?php echo $this->get_field_id( 'top' ); ?>" name="<?php echo $this->get_field_name( 'top' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'top' ); ?>">Allocate the first group. </label>
                </p>
                <p>
                    <input class="checkbox" type="checkbox" <?php checked( (bool)$show_content, true ); ?> id="<?php echo $this->get_field_id( 'show_content' ); ?>" name="<?php echo $this->get_field_name( 'show_content' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'show_content' ); ?>">Show tab with group's content (posts). Template should support this option.</label>
                </p>
                <p>
                    <input class="checkbox" type="checkbox" <?php checked( (bool)$show_admin_part, true ); ?> id="<?php echo $this->get_field_id( 'show_admin_part' ); ?>" name="<?php echo $this->get_field_name( 'show_admin_part' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'show_admin_part' ); ?>">Show tab with group's configuration (only if user is administrator). Template should support this option.</label>
                </p>
                <p>
                    <input class="checkbox" type="checkbox" <?php checked( (bool)$show_life, true ); ?> id="<?php echo $this->get_field_id( 'show_life' ); ?>" name="<?php echo $this->get_field_name( 'show_life' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'show_life' ); ?>">Show tab with group's activities. Template should support this option.</label>
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
                                    "group" =>"ThumbnailsGroups",
                                    "echo"  =>true));
            ?>
                <p>Define here ID of user (or comma separated users). Or define here ID of group (or comma separated groups).</p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'targets' ); ?>">ID of user or group</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'targets' ); ?>" name="<?php echo $this->get_field_name( 'targets' ); ?>" type="text" value="<?php echo esc_attr( $targets ); ?>"/>
                </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Period",
                                    "group" =>"ThumbnailsGroups",
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
                                    "group" =>"ThumbnailsGroups",
                                    "echo"  =>true));
            ?>
                <p>
                    <label for="<?php echo $this->get_field_id( 'maxcount' ); ?>">Maximum count of records</label>
                    <input class="widefat" type="number" id="<?php echo $this->get_field_id( 'maxcount' ); ?>" name="<?php echo $this->get_field_name( 'maxcount' ); ?>" type="text" value="<?php echo esc_attr( $maxcount ); ?>"/>
                </p>
                <p>Version: <?php echo Initialization::instance()->configuration->version; ?></p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
        <?php
            $groups             = NULL;
            $templatesTitle     = NULL;
            $templatesGroups    = NULL;
        }
    }
}
?>