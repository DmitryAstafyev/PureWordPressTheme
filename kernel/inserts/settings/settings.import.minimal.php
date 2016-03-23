<?php
$message = false;
if (isset($_POST['pure_basic_import_data_command']) !== false){
    \Pure\Components\Demo\Minimal\Initialization::instance()->attach();
    $Demo = new \Pure\Components\Demo\Minimal\Core();
    switch($_POST['pure_basic_import_data_command']){
        case 'generate':
            $Demo->generate();
            $message = __('Basic demo data generated.', 'pure');
            break;
        case 'remove':
            $Demo->remove();
            $message = __('Basic demo data removed.', 'pure');
            break;
    }
    $Demo = NULL;
}
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Basic demo data", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<form method="POST" action="">
    <p data-type="Pure.Configuration.Info"><?php echo __('Basic demo data (or minimal demo data) is a several posts only. Basic demo data was added automaticaly, when you activated (first time) Pure template. You can remove whole basic data or restore it here.', 'pure');?></p>
    <?php
    \Pure\Components\Demo\Minimal\Initialization::instance()->attach();
    $Demo = new \Pure\Components\Demo\Minimal\Core();
    $data = $Demo->getDemoData();
    $Demo = NULL;
    if ($data === false){
        //Add demo data
        ?>
        <p data-type="Pure.Configuration.Accent"><?php echo __('Several demo posts (events, reports, questions, inserts and single posts) will be generated with attachments (images). This operation will take some time. Maybe about 2-3 minutes. Be sure, that configuration of your PHP and your server allows run scripts for a long time.', 'pure');?></p>
        <input type="hidden" name="pure_basic_import_data_command" value="generate" />
        <p style="margin: 1rem 0">
            <input type="submit" value="<?php echo __('Generate basic (minimal) demo data', 'pure');?>" class="button-primary"/>
        </p>
        <?php
    }else{
        //Remove demo data
        ?>
        <p data-type="Pure.Configuration.Accent"><?php echo __('All basic demo posts (events, reports, questions, inserts and single posts) will be removed with all attachments (include files).', 'pure');?></p>
        <input type="hidden" name="pure_basic_import_data_command" value="remove" />
        <p style="margin: 1rem 0">
            <input type="submit" value="<?php echo __('Remove basic (minimal) demo data', 'pure');?>" class="button-primary"/>
        </p>
    <?php
    }
    ?>
    <?php
    if ($message !== false){
        ?>
        <p data-type="Pure.Configuration.Accent"><?php echo $message;?></p>
        <?php
    }
    $groups->close(false);
    ?>
</form>