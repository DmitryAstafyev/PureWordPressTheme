<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
if (isset($_POST['collection_name']) !== false){
    \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
    $Provider = new \Pure\Components\PostTypes\Reports\Module\Provider();
    if ($Provider->updateCollection(
        (object)array(
            'names'     =>$_POST['collection_name'  ],
            'indexes'   =>$_POST['index_name'       ],
            'maximums'  =>$_POST['index_max'        ],
        )
    ) !== false){
        $status_of_saving = '<div id="message" class="updated">Settings saved</div>';
    }else{
        $status_of_saving = '<div id="message" class="error">Sorry. Some error during saving settings.</div>';
    }
    $Provider = NULL;
}else{
    $status_of_saving = '';
}
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->reports->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->reports->id;
$collections            = @unserialize(base64_decode($properties->collections->value));
$containers             = \Pure\Templates\Admin\Groups\Initialization::instance()->get('D');
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Collection of indexes for reports", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
//FUNCTIONS
//Template of Index
$innerHTMLIndexTemplate = function($containers, $collection_index){
    ?>
    <div data-muliitems-index-template="collections[<?php echo $collection_index;?>][[index]]"
         data-muliitems-template>
    <?php
    $containers->open(
        array(
            "title"             =>__('New index', 'pure'),
            "opened"            =>false,
            'style_content'     =>'padding:0.5em;',
            "container_attr"    =>'data-muliitems-parent-of="collections['.$collection_index.'][[index]]"',
            "remove_attr"       =>'data-muliitems-under-control="collections['.$collection_index.'][[index]]"',
            "remove_title"      =>__('remove', 'pure'),
            "id"                =>'collections['.$collection_index.'][[index]]',
            "echo"              =>true,
        )
    );
    ?>
    <p>
        <label for="index_name[<?php echo $collection_index; ?>][[index]]">Name of index</label>
        <input data-element-type="Pure.Admin.Input" id="index_name[<?php echo $collection_index; ?>][[index]]" name="index_name[<?php echo $collection_index; ?>][[index]]" type="text" value=""/>
    </p>
    <p>
        <label for="index_max[<?php echo $collection_index; ?>][[index]]">Max value of index (from 2 to 10)</label>
        <input data-element-type="Pure.Admin.Input" id="index_max[<?php echo $collection_index; ?>][[index]]" name="index_max[<?php echo $collection_index; ?>][[index]]" type="number" min="2" max="10" value="10"/>
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
};
//Index
$innerHTMLIndex = function($containers, $collection_index, $index_index, $index_data){
    $containers->open(
        array(
            "title"             =>$index_data->name,
            "opened"            =>false,
            'style_content'     =>'padding:0.5em;',
            "container_attr"    =>'data-muliitems-parent-of="collections['.$collection_index.']['.$index_index.']"',
            "remove_attr"       =>'data-muliitems-under-control="collections['.$collection_index.']['.$index_index.']"',
            "remove_title"      =>__('remove', 'pure'),
            "id"                =>'collections['.$collection_index.']['.$index_index.']',
            "echo"              =>true,
        )
    );
    ?>
    <p>
        <label for="index_name[<?php echo $collection_index; ?>][<?php echo $index_index; ?>]">Name of index</label>
        <input data-element-type="Pure.Admin.Input" id="index_name[<?php echo $collection_index; ?>][<?php echo $index_index; ?>]" name="index_name[<?php echo $collection_index; ?>][<?php echo $index_index; ?>]" type="text" value="<?php echo $index_data->name; ?>"/>
    </p>
    <p>
        <label for="index_max[<?php echo $collection_index; ?>][<?php echo $index_index; ?>]">Max value of index (from 2 to 10)</label>
        <input data-element-type="Pure.Admin.Input" id="index_max[<?php echo $collection_index; ?>][<?php echo $index_index; ?>]" name="index_max[<?php echo $collection_index; ?>][<?php echo $index_index; ?>]" type="number" min="2" max="10" value="<?php echo $index_data->max; ?>"/>
    </p>
    <?php
    $containers->close(
        array(
            "echo"              =>true,
        )
    );
};
//Template of collection
$innerHTMLCollectionTemplate = function($containers, $innerHTMLIndexTemplate){
?>
    <div data-muliitems-index-template="collections[[index]]"
        data-muliitems-template>
    <?php
    $containers->open(
        array(
            "title"             =>__('New collection', 'pure'),
            "opened"            =>false,
            'style_content'     =>'padding:0.5em;',
            "container_attr"    =>'data-muliitems-parent-of="collections[[index]]"',
            "remove_attr"       =>'data-muliitems-under-control="collections[[index]]"',
            "remove_title"      =>__('remove', 'pure'),
            "id"                =>'collections[[index]]',
            "echo"              =>true,
        )
    );
    ?>
    <p>
        <label for="collection_name[[index]]">Name of collection</label>
        <input data-element-type="Pure.Admin.Input" id="collection_name[[index]]" name="collection_name[[index]]" type="text" value=""/>
    </p>
    <p data-element-type="Pure.Admin.Title">Indexes</p>
    <p>&nbsp;</p>
    <a data-basic-type="Button" data-element-type="Pure.Admin.MultiItems.Add" data-muliitems-add-button id="template">add new index</a>
    <?php
    $innerHTMLIndexTemplate($containers, '[index]');
    $containers->close(
        array(
            "echo"              =>true,
        )
    );
    ?>
    </div>
    <?php
};
//Collection
$innerHTMLCollection = function($containers, $collection, $collection_index, $innerHTMLIndex, $innerHTMLIndexTemplate){
    $containers->open(
        array(
            "title"             =>$collection->name,
            "opened"            =>false,
            'style_content'     =>'padding:0.5em;',
            "container_attr"    =>'data-muliitems-parent-of="collections['.$collection_index.']"',
            "remove_attr"       =>'data-muliitems-under-control="collections['.$collection_index.']"',
            "remove_title"      =>__('remove', 'pure'),
            "id"                =>'collections['.$collection_index.']',
            "echo"              =>true,
        )
    );
    ?>
    <p>
        <label for="collection_name[<?php echo $collection_index; ?>]">Name of collection</label>
        <input data-element-type="Pure.Admin.Input" id="collection_name[<?php echo $collection_index; ?>]" name="collection_name[<?php echo $collection_index; ?>]" type="text" value="<?php echo $collection->name; ?>"/>
    </p>
    <p data-element-type="Pure.Admin.Title">Indexes</p>
    <?php
    foreach($collection->indexes as $key=>$_index){
        $innerHTMLIndex(
            $containers,
            $collection_index,
            $key,
            $_index
        );
    }
    ?>
    <a data-basic-type="Button" data-element-type="Pure.Admin.MultiItems.Add" data-muliitems-add-button>add new index</a>
    <?php
    $innerHTMLIndexTemplate($containers, $collection_index);
    $containers->close(
        array(
            "echo"              =>true,
        )
    );
};
?>
<form method="POST" action="">
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Where it is actual?', 'pure');?></strong></p>
    <p data-type="Pure.Configuration.Info"><?php echo __('Here you can manage collection of indexes for reports. For example, you can define indexes for movie or restaurant.', 'pure');?></p>
    <?php
    foreach($collections as $index=>$collection){
        $innerHTMLCollection(
            $containers,
            $collection,
            $index,
            $innerHTMLIndex,
            $innerHTMLIndexTemplate
        );
    }
    ?>
    <a data-basic-type="Button" data-element-type="Pure.Admin.MultiItems.Add" data-muliitems-add-button data-muliitems-afteradd-handles="pure.components.admin.multiitems.init">add new collection</a>
    <p>&nbsp;</p>
    <?php
    $innerHTMLCollectionTemplate($containers, $innerHTMLIndexTemplate);
    ?>
    <input type="hidden" name="update_settings" value="Y" />
    <?php
        echo $status_of_saving;
    ?>
    <p>
        <input type="submit" value="Save settings" class="button-primary"/>
    </p>
    <?php
    $groups->close(false);
    \Pure\Components\WordPress\Admin\Multiitems\Initialization::instance()->attach();
    ?>
</form>