<?php
$path = substr(__DIR__, 0, (stripos(__DIR__, 'wp-content') - 1));
require_once($path.'/wp-load.php'             );
require_once($path.'/wp-includes\wp-db.php'   );
\Pure\Components\Demo\Module\Initialization::instance()->attach(true);
$Importer = new \Pure\Components\Demo\Module\Core();
$Importer->import(true, false);
$Importer = NULL;
//To see debug information use $Importer->import(true, true) <-- second parameter in [true]
?>