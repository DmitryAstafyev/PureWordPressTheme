<?php
    \Pure\Debug\Logs\Core::instance()->open('HEADER');
    require_once('kernel/inserts/header.php');
    \Pure\Debug\Logs\Core::instance()->close('HEADER');
//BEGIN: Content area
//========================================================================================================
    \Pure\Debug\Logs\Core::instance()->open('CONTENT');
    require_once('kernel/inserts/content.php');
    \Pure\Debug\Logs\Core::instance()->close('CONTENT');
//END: Content area
//========================================================================================================
    \Pure\Debug\Logs\Core::instance()->open('FOOTER');
    require_once('kernel/inserts/footer.php');
    require_once('kernel/inserts/footer.after.php');
    \Pure\Debug\Logs\Core::instance()->close('FOOTER');
?>
