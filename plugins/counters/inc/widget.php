<?php
namespace Pure\Plugins\Counters {
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
            foreach($URLs as $key=>$url){
                $URLs[$key] = \Pure\Resources\Names::instance()->repairURL($url);
            }
            return $URLs;
        }
        public function widget($args, $instance)
        {
            $out 	        = $args['before_widget'];
            $_parameters    = array(	'offset'        => $instance['offset'       ],
                                        'template'      => $instance['template'	    ],
                                        'background'    => $instance['background'   ],
                                        'icons'         => $instance['icons'	    ],
                                        'titles'        => $instance['titles'	    ],
                                        'counts'        => $instance['counts'       ],
                                        'urls'          => $this->repairURLs($instance['urls']),
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
                $ErrorMessages->show("\\Pure\\Plugins\\Counters\\Widget\\widget::: ".$e);
                $ErrorMessages = NULL;
                return NULL;
            }
        }
        public function update($new_instance, $old_instance)
        {
            $this->pureUpdated          = true;
            $instance 				    = array();
            $instance['offset'		]   = strip_tags( $new_instance['offset'    ] );
            $instance['template'    ]   = strip_tags( $new_instance['template'  ] );
            $instance['background'  ]   = strip_tags( $new_instance['background'] );
            $instance['icons'       ]   = array();
            $instance['titles'      ]   = array();
            $instance['counts'      ]   = array();
            $instance['urls'        ]   = array();
            if ( isset ( $new_instance['icons'] ) ){
                foreach ( $new_instance['icons'] as $value ){
                    array_push($instance['icons'], $value);
                }
            }
            if ( isset ( $new_instance['titles'] ) ){
                foreach ( $new_instance['titles'] as $value ){
                    array_push($instance['titles'], $value);
                }
            }
            if ( isset ( $new_instance['counts'] ) ){
                foreach ( $new_instance['counts'] as $value ){
                    array_push($instance['counts'], $value);
                }
            }
            if ( isset ( $new_instance['urls'] ) ){
                foreach ( $new_instance['urls'] as $value ){
                    array_push($instance['urls'], \Pure\Resources\Names::instance()->clearURL($value));
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
            if (isset($this->pureUpdated) !== false){
                if ($this->pureUpdated !== false){
                    $this->call_scripts_after_update();
                }
            }
            $templates          = \Pure\Templates\Elements\CounterWrapper\Initialization    ::instance()->templates;
            $groups             = \Pure\Templates\Admin\Groups\Initialization               ::instance()->get('A');
            $containers         = \Pure\Templates\Admin\Groups\Initialization               ::instance()->get('D');
            $offset 		    = isset( $instance[ 'offset' 		] )  ? $instance[ 'offset' 			] : '50';
            $template		    = isset( $instance[ 'template'      ] )  ? $instance[ 'template'	    ] : 'A';
            $background		    = isset( $instance[ 'background'    ] )  ? $instance[ 'background'	    ] : '';
            $icons		        = isset( $instance[ 'icons' 		] )  ? $instance[ 'icons' 		    ] : array();
            $titles		        = isset( $instance[ 'titles' 		] )  ? $instance[ 'titles' 		    ] : array();
            $counts             = isset( $instance[ 'counts'        ] )  ? $instance[ 'counts'          ] : array();
            $urls		        = isset( $instance[ 'urls' 		    ] )  ? $instance[ 'urls' 		    ] : array();
            $urls               = $this->repairURLs($urls);
            ?>
            <?php
            $groups->open(array(
                "title" =>"Template",
                "group" =>"Counters",
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
                    \Pure\Templates\Elements\CounterWrapper\Initialization::instance()->description($_template->key);
                    ?>
                </p>
            <?php
            }
            ?>
            <?php
            $groups->close(array("echo"=>true));
            ?>
            <?php
            $groups->open(array(    "title" =>"Position of counters",
                "group" =>"Counters",
                "echo"  =>true));
            ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'offset' ); ?>">Define offset of counters on background image (in %)</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" type="text" value="<?php echo esc_attr( $offset ); ?>"/>
            </p>
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
            $groups->open(array(    "title" =>"Content",
                                    "group" =>"Counters",
                                    "echo"  =>true));
            for($index = 0; $index < count($icons); $index ++){
                if ($icons[$index] !== ''){
                    $attachment = wp_get_attachment_image_src( $icons[$index], 'thumbnail', false );
                    if (is_array($attachment) !== false){
                        $attachment_url = $attachment[0];
                    }else{
                        $attachment_url = Initialization::instance()->configuration->urls->images.'/no_image.png';
                    }
                }else{
                    $attachment_url = Initialization::instance()->configuration->urls->images.'/no_image.png';
                }
                $this->innerHTMLItem(
                    $containers,
                    $index,
                    (object)array(
                        'title'             =>$titles[$index],
                        'count'             =>$counts[$index],
                        'url'               =>$urls[$index],
                        'attachment_id'     =>$icons[$index],
                        'attachment_url'    =>$attachment_url
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
                    "title"             =>$data->title,
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
            <p data-element-type="Pure.Admin.Title">Basic</p>
            <p>
                <label for="<?php echo $this->get_field_id( 'titles' ); ?>[<?php echo $index; ?>]">Title of item</label>
                <textarea data-element-type="Pure.Admin.TextArea" id="<?php echo $this->get_field_id( 'titles' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'titles' ); ?>[<?php echo $index; ?>]" type="text"><?php echo $data->title; ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'counts' ); ?>[<?php echo $index; ?>]">Number</label>
                <input class="widefat" type="number" id="<?php echo $this->get_field_id( 'counts' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'counts' ); ?>[<?php echo $index; ?>]" value="<?php echo $data->count; ?>"/>
            </p>
            <p>Choose an icon of item</p>
            <div data-element-type="Pure.Admin.Preview.Image.Container">
                <img data-element-type="Pure.Admin.Preview.Image" src="<?php echo $data->attachment_url; ?>" data-storage-id="<?php echo $index; ?>" pure-wordpress-media-images-default-src="<?php echo Initialization::instance()->configuration->urls->images.'/no_image.png' ?>"/>
                <div data-element-type="Pure.Admin.Preview.Image.Controls.Container">
                    <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Load" pure-wordpress-media-images-add-selector="*[data-storage-id=|<?php echo $index; ?>|]" pure-wordpress-media-images-displayed>load</div>
                    <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Remove" pure-wordpress-media-images-remove-selector="*[data-storage-id=|<?php echo $index; ?>|]" pure-wordpress-media-images-displayed>remove</div>
                </div>
            </div>
            <input data-element-type="Pure.Admin.Preview.Image" data-storage-id="<?php echo $index; ?>" id="<?php echo $this->get_field_id( 'icons' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'icons' ); ?>[<?php echo $index; ?>]" value="<?php echo $data->attachment_id; ?>" type="text"/>
            <p data-element-type="Pure.Admin.Title">Choose link of item</p>
            <p>
                <label for="<?php echo $this->get_field_id( 'urls' ); ?>[<?php echo $index; ?>]">Any your URL</label>
                <input data-element-type="Pure.Admin.Input" id="<?php echo $this->get_field_id( 'urls' ); ?>[<?php echo $index; ?>]" name="<?php echo $this->get_field_name( 'urls' ); ?>[<?php echo $index; ?>]" type="text" value="<?php echo $data->url; ?>"/>
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
                        "title"             =>"Content",
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
                <p data-element-type="Pure.Admin.Title">Basic</p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'titles' ); ?>[[index]]">Title of item</label>
                    <textarea data-element-type="Pure.Admin.TextArea" id="<?php echo $this->get_field_id( 'titles' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'titles' ); ?>[[index]]" type="text" value=""></textarea>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'counts' ); ?>[[index]]">Number</label>
                    <input class="widefat" type="number"  id="<?php echo $this->get_field_id( 'counts' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'counts' ); ?>[[index]]" type="text" value=""/>
                </p>
                <p>Choose an icon of item</p>
                <div data-element-type="Pure.Admin.Preview.Image.Container">
                    <img data-element-type="Pure.Admin.Preview.Image" src="<?php echo Initialization::instance()->configuration->urls->images.'/no_image.png' ?>" data-storage-id="[index]" pure-wordpress-media-images-default-src="<?php echo Initialization::instance()->configuration->urls->images.'/no_image.png' ?>"/>
                    <div data-element-type="Pure.Admin.Preview.Image.Controls.Container">
                        <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Load" pure-wordpress-media-images-add-selector="*[data-storage-id=|[index]|]" pure-wordpress-media-images-displayed>load</div>
                        <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Remove" pure-wordpress-media-images-remove-selector="*[data-storage-id=|[index]|]" pure-wordpress-media-images-displayed>remove</div>
                    </div>
                </div>
                <input data-element-type="Pure.Admin.Preview.Image" data-storage-id="[index]" id="<?php echo $this->get_field_id( 'icons' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'icons' ); ?>[[index]]" value="" type="text"/>
                <p data-element-type="Pure.Admin.Title">Choose link of item</p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'urls' ); ?>[[index]]">Any your URL</label>
                    <input data-element-type="Pure.Admin.Input" id="<?php echo $this->get_field_id( 'urls' ); ?>[[index]]" name="<?php echo $this->get_field_name( 'urls' ); ?>[[index]]" type="text" value=""/>
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