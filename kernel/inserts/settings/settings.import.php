<?php
require_once('settings.import.minimal.php');
if (isset($_POST['pure_import_data_command']) !== false){
    $current_command = $_POST['pure_import_data_command'];
    require_once('settings.import.online.php');
}else{
    \Pure\Components\Demo\Module\Initialization::instance()->attach(true);
    $Importer = new \Pure\Components\Demo\Module\Core();
    if ($Importer->isDone() === false){
        require_once('settings.import.start.php');
    }else{
        require_once('settings.import.done.php');
    }
    $Importer = NULL;
}