<?php
//Attach scripts
\Pure\Components\GlobalSettings\Module\Initialization::instance()->attach();
\Pure\Components\Demo\Module\Initialization::instance()->attach();
\Pure\Components\Demo\Module\Logs::clear();
$Importer   = new \Pure\Components\Demo\Module\Core();
$test       = $Importer->isCan();
$Importer   = NULL;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "Import demo data", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
if ($test === true){
?>
    <form method="POST" action="">
        <p data-type="Pure.Configuration.Title"><strong><?php echo __('Online mode of import was started', 'pure');?></strong></p>
        <p data-type="Pure.Configuration.Attention"><?php echo __('Do not close browser and do not update page while procedure of import is going. Procedure of import will take much time. About 5-10 minutes.', 'pure');?></p>
        <div data-type="Pure.Configuration.Logs">
            <textarea data-type="Pure.Configuration.Logs" id="PureImportDataLogs"></textarea>
        </div>
    </form>
    <?php
    //Start import
    \Pure\Components\Tools\System\Initialization::instance()->attach();
    $System = new \Pure\Components\Tools\System\Core();
    if (substr(php_uname(), 0, 7) == "Windows"){
    $path       = \Pure\Configuration::instance()->dir(ABSPATH.'/wp-content/themes/pure/components/demo/module/bin/');
    $command    = 'php -q '.$path.'launcher.php';
    }else {
    $path       = \Pure\Configuration::instance()->dir(ABSPATH.'/wp-content/themes/pure/components/demo/Module/bin/');
    $command    = 'php -q '.$path.'launcher.php';
    }
    $System->run($command);
    $System = NULL;
    ?>
<?php
}else{
    ?>
    <form method="POST" action="">
        <p data-type="Pure.Configuration.Title"><strong><?php echo __('Online mode of import cannot be started', 'pure');?></strong></p>
        <p data-type="Pure.Configuration.Attention"><?php echo __('Sorry, but you cannot start import, because server script has not access for write to folder: ', 'pure').$test;?></p>
        <div data-type="Pure.Configuration.Logs">
            <textarea data-type="Pure.Configuration.Logs" id="PureImportDataLogs"></textarea>
        </div>
    </form>
    <?php
}
$groups->close(false);
?>
