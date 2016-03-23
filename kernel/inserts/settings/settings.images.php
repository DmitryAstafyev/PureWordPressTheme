<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('images');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance          ::instance()->settings->images->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance          ::instance()->settings->images->id;
$templates              = \Pure\Templates\Makeup\Footer\Layout\Initialization   ::instance()->templates;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Background & logo", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
\Pure\Components\WordPress\Media\Resources\Initialization::instance()->attach();
?>
<form method="POST" action="">
    <?php
    $images = (object)array(
        'background'    => (object)array(
            'title'     =>__('Common background', 'pure'),
            'info'      =>__('Background image for not personal pages, like search page, top of posts and etc.' , 'pure')
        ),
        'logo_dark' => (object)array(
            'title' =>__('Dark logo', 'pure'),
            'info'  =>__('This logo will be used with light menu background' , 'pure')
        ),
        'logo_light'=> (object)array(
            'title' =>__('Light logo', 'pure'),
            'info'  =>__('This logo will be used with light menu background' , 'pure')
        )
    );
    foreach($images as $key=>$image){
        $image_url = '';
        if ((int)$properties->$key->value > 0){
            $image_url = wp_get_attachment_image_src( (int)$properties->$key->value, 'full', false );
            $image_url = (is_array($image_url) !== false ? $image_url[0] : '');
        }
        $image_url  = ($image_url === '' ? \Pure\Configuration::instance()->imagesURL.'/admin/no_image.png' : $image_url);
        $id         = uniqid();
        ?>
        <p data-type="Pure.Configuration.Title"><strong><?php echo $image->title;?></strong></p>
        <p data-type="Pure.Configuration.Info"><?php echo $image->info;?></p>
        <div style="max-width: 15em;">
            <div data-element-type="Pure.Admin.Preview.Image.Container">
                <img data-element-type="Pure.Admin.Preview.Image" src="<?php echo $image_url; ?>" data-storage-id="<?php echo $id; ?>" pure-wordpress-media-images-default-src="<?php echo \Pure\Configuration::instance()->imagesURL.'/admin/no_image.png'; ?>"/>
                <div data-element-type="Pure.Admin.Preview.Image.Controls.Container">
                    <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Load" pure-wordpress-media-images-add-selector="*[data-storage-id=|<?php echo $id; ?>|]" pure-wordpress-media-images-displayed><?php echo __('load', 'pure');?></div>
                    <div data-element-type="Pure.Admin.Preview.Image.Button" data-addition-type="Remove" pure-wordpress-media-images-remove-selector="*[data-storage-id=|<?php echo $id; ?>|]" pure-wordpress-media-images-displayed><?php echo __('remove', 'pure');?></div>
                </div>
            </div>
        </div>
        <input data-element-type="Pure.Admin.Preview.Image" data-storage-id="<?php echo $id; ?>" id="<?php echo $properties->$key->name; ?>" name="<?php echo $properties->$key->name; ?>" value="<?php echo (int)$properties->$key->value; ?>" type="text"/>
        <?php
    }
    ?>
    <input type="hidden" name="update_settings" value="Y" />
    <?php
    echo $status_of_saving->message;
    ?>
    <p>
        <input type="submit" value="Save settings" class="button-primary"/>
    </p>
    <?php
    $groups->close(false);
    ?>
</form>