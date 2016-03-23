<?php
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Import advanced demo data", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<form method="POST" action="">
    <p data-type="Pure.Configuration.Info"><?php echo __('You can import advanced demo data on your site. To do it you have to place demo data into folder [wp-content/uploads/demo]. This folder should consists such files like [titles.xml], [authors.xml] and etc.', 'pure');?></p>
    <p data-type="Pure.Configuration.Accent"><?php echo __('Pure template importer does not use any DB import procedures. We do not modify DB, at least directly. To add demo data on your site we use emulate mode - we create everything step by step like real user. This is safe mode. And it takes much time. Whole procedure of import will take about 5-10 minutes.', 'pure');?></p>
    <p data-type="Pure.Configuration.Info"><?php echo __('You have two ways of import:', 'pure');?></p>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Safe mode', 'pure');?></strong></p>
    <ol data-type="Pure.Configuration.List">
        <li><p><?php echo __('Be sure - all files from [demo.zip] are in folder [wp-content/uploads/demo]', 'pure');?></p></li>
        <li><p><?php echo __('Stop any outside connections on your server (for this site for sure).', 'pure');?></p></li>
        <li><p><?php echo __('Go to the folder [wp-content/themes/pure/components/demo/Module/bin]', 'pure');?></p></li>
        <li><p><?php echo __('Using write/read rights for folders [../wp-content/uploads] and [PHP temp upload folder] (scripts will add images on your server) run next command [php -q launcher.php]', 'pure');?></p></li>
    </ol>
    <p data-type="Pure.Configuration.Accent"><?php echo __('For sure we recommend use this way. After you run import, you will see logs of process in console. If you have some problems with import, you should open file [launcher.php] and turn on DEBUG mode according comments in script. Copy logs data from console and send us, please. We will help you, you will help other people. ', 'pure');?></p>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Online mode', 'pure');?></strong></p>
    <ol data-type="Pure.Configuration.List">
        <li><p><?php echo __('Be sure - all files from [demo.zip] are in folder [wp-content/uploads/demo]', 'pure');?></p></li>
        <li><p><?php echo __('At the time of import, set rights to ../wp-content/uploads to 777 for standard user of your server (import script will be run by standard user of your server). In unix OS - use CHMOD; in windows - set permission via properties of folder (section "security"). It is necessary only during import.', 'pure');?></p></li>
        <li><p><?php echo __('At the time of import, set rights to your PHP upload temp folder (you can see path to this folder in your php.ini - [upload_tmp_dir]) to 777 for standard user of your server. Use commands like in step (2). It is necessary only during import.', 'pure');?></p></li>
        <li><p><?php echo __('After you press button "Start import in online mode", you will see area with logs information', 'pure');?></p></li>
        <li><p><?php echo __('Procedure of import will take much time. About 5-10 minutes.', 'pure');?></p></li>
        <li><p><?php echo __('Do not close browser and do not update page while procedure of import is going.', 'pure');?></p></li>
        <li><p><?php echo __('After finishing of import, restore rights to ../wp-content/uploads and PHP upload temp folder to standard.', 'pure');?></p></li>
        <li><p><?php echo __('If something is going wrong, you will see information about errors in logs.', 'pure');?></p></li>
    </ol>
    <input type="hidden" name="pure_import_data_command" value="online" />
    <?php
    $groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('B');
    $groups->open(
        array(
            "title"             =>__( "Start import in online mode", 'pure' ),
            "group"             =>uniqid(),
            "echo"              =>true,
            "opened"            =>false,
            "container_style"   =>'width:auto;',
            "content_style"     =>'width:auto;padding:0.5em;'
        )
    );
    ?>
    <p data-type="Pure.Configuration.Info"><?php echo __('Do not worry, if you forget set correct permissions or copy necessary files. Before start procedure, script will check everything and will not start import, if something is not ready for it.', 'pure');?></p>
    <p>
        <input type="submit" value="<?php echo __('Start import in online mode', 'pure');?>" class="button-primary"/>
    </p>
    <?php
    $groups->close(false);
    ?>
    <?php
    $groups->close(false);
    ?>
</form>