<?php
namespace Pure\Plugins\Thumbnails\Posts {
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
                                        'hidetitle'	        => (empty($instance['hidetitle']) ? false : true),
                                        'post_type'	        => isset($instance['post_type']) !== false ? $instance['post_type'] : 'all',
                                        'targets'	        => $instance['targets'		    ],
                                        'title'		        => $instance['title'		    ],
                                        'title_type'        => $instance['title_type'       ],
                                        'thumbnails'        => $instance['thumbnails'       ],
                                        'maxcount'	        => $instance['maxcount'		    ],
                                        'profile'	        => $instance['profile'		    ],
                                        'days'	            => $instance['days'		        ],
                                        'from_date'         => $instance['from_date'	    ],
                                        'template'          => $instance['template'	        ],
                                        'displayed'	        => $instance['displayed'	    ],
                                        'slider_template'   => $instance['slider_template'  ],
                                        'tab_template'      => $instance['tab_template'	    ],
                                        'presentation'      => $instance['presentation'     ],
                                        'more'              => $instance['more'	            ],
                                        'selection'         => (isset($instance['selection']) !== false ? $instance['selection'] : false),
                                        'tabs_columns'      => $instance['tabs_columns'     ],
                                        'wrapper_width'	    => isset($instance['wrapper_width']) !== false ? $instance['wrapper_width'] : 23,
                                        'wrapper_space'	    => isset($instance['wrapper_space']) !== false ? $instance['wrapper_space'] : 1,
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
                $ErrorMessages->show("\\Pure\\Plugins\\Thumbnails\\Posts\\Widget\\widget::: ".$e);
                $ErrorMessages = NULL;
                return NULL;
            }
        }
        public function update($new_instance, $old_instance)
        {
            $instance 				  = array();
            $instance['title'		    ] = strip_tags( $new_instance['title'			] );
            $instance['title_type'      ] = strip_tags( $new_instance['title_type'	    ] );
            $instance['content'		    ] = strip_tags( $new_instance['content'			] );
            $instance['post_type'	    ] = strip_tags( $new_instance['post_type'	    ] );
            $instance['hidetitle'	    ] = strip_tags( $new_instance['hidetitle'		] );
            $instance['targets'		    ] = strip_tags( $new_instance['targets'			] );
            $instance['maxcount'	    ] = strip_tags( $new_instance['maxcount'		] );
            $instance['thumbnails'	    ] = strip_tags( $new_instance['thumbnails'		] );
            $instance['profile'		    ] = strip_tags( $new_instance['profile'			] );
            $instance['days'		    ] = strip_tags( $new_instance['days'			] );
            $instance['from_date'       ] = strip_tags( $new_instance['from_date'	    ] );
            $instance['template'        ] = strip_tags( $new_instance['template'	    ] );
            $instance['displayed'	    ] = strip_tags( $new_instance['displayed'	    ] );
            $instance['slider_template' ] = strip_tags( $new_instance['slider_template' ] );
            $instance['tab_template'    ] = strip_tags( $new_instance['tab_template'    ] );
            $instance['presentation'    ] = strip_tags( $new_instance['presentation'    ] );
            $instance['tabs_columns'    ] = strip_tags( $new_instance['tabs_columns'    ] );
            $instance['wrapper_width'   ] = strip_tags( $new_instance['wrapper_width'   ] );
            $instance['wrapper_space'   ] = strip_tags( $new_instance['wrapper_space'   ] );
            $instance['more'            ] = strip_tags( $new_instance['more'	        ] );
            return $instance;
        }
        public function form($instance)
        {
            $templatesPosts     = \Pure\Templates\Posts\Thumbnails\Initialization   ::instance()->templates;
            $templatesSliders   = \Pure\Templates\Sliders\Initialization            ::instance()->templates;
            $templatesTabs      = \Pure\Templates\Tabs\Initialization               ::instance()->templates;
            $templatesTitle     = \Pure\Templates\Titles\Initialization             ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization       ::instance()->get('A');
            $title 			    = isset( $instance[ 'title' 		    ] )  ? $instance[ 'title' 			] : '';
            $title_type		    = isset( $instance[ 'title_type'        ] )  ? $instance[ 'title_type' 		] : 'A';
            $hidetitle		    = isset( $instance[ 'hidetitle'		    ] )  ? $instance[ 'hidetitle'		] : false;
            $content		    = isset( $instance[ 'content' 		    ] )  ? $instance[ 'content' 		] : 'last';
            $post_type		    = isset( $instance[ 'post_type' 	    ] )  ? $instance[ 'post_type' 		] : 'all';
            $targets		    = isset( $instance[ 'targets' 		    ] )  ? $instance[ 'targets' 		] : '';
            $maxcount		    = isset( $instance[ 'maxcount' 		    ] )  ? $instance[ 'maxcount'		] : '10';
            $thumbnails		    = isset( $instance[ 'thumbnails' 	    ] )  ? $instance[ 'thumbnails'		] : false;
            $profile		    = isset( $instance[ 'profile' 		    ] )  ? $instance[ 'profile'			] : '#';
            $displayed	        = isset( $instance[ 'displayed'         ] )  ? $instance[ 'displayed'	    ] : 'none';
            $days		        = isset( $instance[ 'days' 		        ] )  ? $instance[ 'days'			] : '30';
            $from_date		    = isset( $instance[ 'from_date'         ] )  ? $instance[ 'from_date'	    ] : '';
            $slider_template    = isset( $instance[ 'slider_template'   ] )  ? $instance[ 'slider_template' ] : 'A';
            $tab_template       = isset( $instance[ 'tab_template'      ] )  ? $instance[ 'tab_template'    ] : 'A';
            $template		    = isset( $instance[ 'template'          ] )  ? $instance[ 'template' 		] : 'A';
            $presentation       = isset( $instance[ 'presentation'      ] )  ? $instance[ 'presentation'    ] : 'clear';
            $tabs_columns       = isset( $instance[ 'tabs_columns'      ] )  ? $instance[ 'tabs_columns'    ] : '1';
            $wrapper_width      = isset( $instance[ 'wrapper_width'     ] )  ? $instance[ 'wrapper_width'   ] : '23';
            $wrapper_space      = isset( $instance[ 'wrapper_space'     ] )  ? $instance[ 'wrapper_space'   ] : '1';
            $more		        = isset( $instance[ 'more'              ] )  ? $instance[ 'more'	        ] : false;
            ?>
            <?php
            $groups->open(array(    "title" =>"Title",
                                    "group" =>"ThumbnailsPosts",
                                    "echo"  =>true));
            ?>
                <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title of widget</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
                    <p>For 'posts by categories' and 'posts by authors' title is name of category or author (you cannot define other title in this case).</p>
                </p>
                <p>
                    <input class="checkbox" type="checkbox" <?php checked((bool)$hidetitle, true ); ?> id="<?php echo $this->get_field_id( 'hidetitle' ); ?>" name="<?php echo $this->get_field_name( 'hidetitle' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'hidetitle' ); ?>">Hide name of category or author for modes: 'posts by category' and 'posts by authors'. </label>
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
                                    "group" =>"ThumbnailsPosts",
                                    "echo"  =>true));
            ?>
                <p>Choose template of thumbnail of post</p>
                <?php
                foreach ($templatesPosts as $templatePosts){
                    ?>
                    <p>
                        <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templatePosts->key; ?>" <?php checked( $templatePosts->key, $template ); ?> id="<?php echo $this->get_field_id( 'template'.$templatePosts->key ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'template'.$templatePosts->key ); ?>">Template <?php echo $templatePosts->key; ?> <br />
                            <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $templatePosts->thumbnail; ?>">
                        </label>
                        <?php
                        \Pure\Templates\Posts\Thumbnails\Initialization::instance()->description($templatePosts->key);
                        ?>
                    </p>
                <?php
                }
                ?>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Presentation",
                                    "group" =>"ThumbnailsPosts",
                                    "echo"  =>true));
            ?>
                <p>
                    <input class="checkbox" type="radio" value="clear" <?php checked( 'clear', $presentation ); ?> id="<?php echo $this->get_field_id( 'presentation_A' ); ?>" name="<?php echo $this->get_field_name( 'presentation' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'presentation_A' ); ?>">just thumbnails</label>
                </p>
                <p>
                    <input class="checkbox" type="radio" value="slider" <?php checked( 'slider', $presentation ); ?> id="<?php echo $this->get_field_id( 'presentation_B' ); ?>" name="<?php echo $this->get_field_name( 'presentation' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'presentation_B' ); ?>">slider</label>
                </p>
                <p>
                    <input class="checkbox" type="radio" value="tabs" <?php checked( 'tabs', $presentation ); ?> id="<?php echo $this->get_field_id( 'presentation_C' ); ?>" name="<?php echo $this->get_field_name( 'presentation' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'presentation_C' ); ?>">tabs</label>
                </p>
                <p>
                    <input class="checkbox" type="radio" value="wrapper" <?php checked( 'wrapper', $presentation ); ?> id="<?php echo $this->get_field_id( 'presentation_D' ); ?>" name="<?php echo $this->get_field_name( 'presentation' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'presentation_D' ); ?>">responsive wrapper</label>
                </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Show",
                                    "group" =>"ThumbnailsPosts",
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
            $post_types = get_post_types(
                array(
                    'public' => true,
                ),
                'names'
            );
            ?>
                <p>
                    <select class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
                        <option value="all" <?php selected( $post_type, 'all' ); ?>>all</option>
                        <?php
                        foreach($post_types as $_post_type){
                            if ($_post_type !== 'page'){
                                ?>
                                <option value="<?php echo $_post_type; ?>" <?php selected( $post_type, $_post_type ); ?>><?php echo $_post_type; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <select class="widefat" id="<?php echo $this->get_field_id( 'displayed' ); ?>" name="<?php echo $this->get_field_name( 'displayed' ); ?>">
                        <option value="none" <?php selected( $displayed, "none" ); ?>><?php echo 'do not associate'; ?></option>
                        <option value="member" <?php selected( $displayed, "member" ); ?>><?php echo 'displayed member'; ?></option>
                        <option value="post" <?php selected( $displayed, "post" ); ?>><?php echo 'displayed post'; ?></option>
                        <option value="group" <?php selected( $displayed, "group" ); ?>><?php echo 'displayed group'; ?></option>
                    </select>
                    <label for="<?php echo $this->get_field_id( 'displayed' ); ?>">Associate with:</label>
                </p>
                <p>
                    <input class="checkbox" type="checkbox" <?php checked( (bool)$thumbnails, true ); ?> id="<?php echo $this->get_field_id( 'thumbnails' ); ?>" name="<?php echo $this->get_field_name( 'thumbnails' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'thumbnails' ); ?>">Show post only with thumbnails. </label>
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
                                    "group" =>"ThumbnailsPosts",
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
            $groups->open(array(    "title" =>"Period",
                                    "group" =>"ThumbnailsPosts",
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
            $groups->open(array(    "title" =>"Slider configuration",
                                    "group" =>"ThumbnailsPosts",
                                    "echo"  =>true));
            ?>
                <p>Choose template of slider</p>
                <?php
                foreach ($templatesSliders as $templateSliders){
                    ?>
                    <p>
                        <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templateSliders->key; ?>" <?php checked( $templateSliders->key, $slider_template ); ?> id="<?php echo $this->get_field_id( 'slider_template'.$templateSliders->key ); ?>" name="<?php echo $this->get_field_name( 'slider_template' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'slider_template'.$templateSliders->key ); ?>">Template <?php echo $templateSliders->key; ?> <br />
                            <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $templateSliders->thumbnail; ?>">
                        </label>
                        <?php
                        \Pure\Templates\Sliders\Initialization::instance()->description($templateSliders->key);
                        ?>
                    </p>
                <?php
                }
                ?>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Tabs configuration",
                                    "group" =>"ThumbnailsPosts",
                                    "echo"  =>true));
            ?>
                <p>This section is actual, if you use presentation "tabs"</p>
                <p>
                    <input class="checkbox" type="radio" value="1" <?php checked( '1', $tabs_columns ); ?> id="<?php echo $this->get_field_id( 'tabs_columns_1' ); ?>" name="<?php echo $this->get_field_name( 'tabs_columns' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'tabs_columns_1' ); ?>">1 column in tab</label>
                </p>
                <p>
                    <input class="checkbox" type="radio" value="2" <?php checked( '2', $tabs_columns ); ?> id="<?php echo $this->get_field_id( 'tabs_columns_2' ); ?>" name="<?php echo $this->get_field_name( 'tabs_columns' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'tabs_columns_2' ); ?>">2 columns in tab</label>
                </p>
                <p>Choose template of Tabs</p>
                <?php
                foreach ($templatesTabs as $templateTabs){
                    ?>
                    <p>
                        <input data-type="Pure.Configuration.Input.Fader" class="checkbox" type="radio" value="<?php echo $templateTabs->key; ?>" <?php checked( $templateTabs->key, $tab_template ); ?> id="<?php echo $this->get_field_id( 'tab_template'.$templateTabs->key ); ?>" name="<?php echo $this->get_field_name( 'tab_template' ); ?>" />
                        <label for="<?php echo $this->get_field_id( 'tab_template'.$templateTabs->key ); ?>">Template <?php echo $templateTabs->key; ?> <br />
                            <img alt="" data-type="Pure.Configuration.Input.Fader" width="90%" style="margin-left: 5%;" src="<?php echo $templateTabs->thumbnail; ?>">
                        </label>
                        <?php
                        \Pure\Templates\Tabs\Initialization::instance()->description($templateTabs->key);
                        ?>
                    </p>
                <?php
                }
                ?>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Wrapper configuration",
                                    "group" =>"ThumbnailsPosts",
                                    "echo"  =>true));
            ?>
                <p>This section is actual, if you use presentation "wrapper"</p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'wrapper_width' ); ?>">Define minimal width of column in [em]</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'wrapper_width' ); ?>" name="<?php echo $this->get_field_name( 'wrapper_width' ); ?>" type="text" value="<?php echo esc_attr( $wrapper_width ); ?>"/>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'wrapper_space' ); ?>">Define space between columns in [em]</label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'wrapper_space' ); ?>" name="<?php echo $this->get_field_name( 'wrapper_space' ); ?>" type="text" value="<?php echo esc_attr( $wrapper_space ); ?>"/>
                </p>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Other",
                                    "group" =>"ThumbnailsPosts",
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
                    <p>Version: <?php echo Initialization::instance()->configuration->version; ?></p>
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