<?php
    //================================================================================
    require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->kernel.'/pure.location.php'));
    //================================================================================
?>
<?php
    \Pure\Components\Tools\DebugMarks\Initialization::instance()->attach();
    //Components:: (here components, which cannot be attached in AUTOLOAD section
    \Pure\Components\Attacher\Module\Initialization::instance()->attach();
    //Get title of page
    \Pure\Components\WordPress\PageTitle\Initialization::instance()->attach();
    $Title      = new \Pure\Components\WordPress\PageTitle\Core();
    $page_title = $Title->get();
    $Title      = NULL;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width">
    <title><?php echo $page_title; ?></title>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:300,700,300italic,400italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <?php
        $CSSLinks   = new \Pure\Resources\CSSLinks();
        $JSLinks    = new \Pure\Resources\JavaScripts();
        $CSSLinks   ->enqueue();
        $JSLinks    ->enqueue();
        $CSSLinks   = NULL;
        $JSLinks    = NULL;
        wp_head();
    ?>
</head>
<body style="opacity: 0;">
    <?php
    //Attach global settings
    \Pure\Components\GlobalSettings\Module\Initialization::instance()->attach();
    //Attach actions before header
    require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->inserts.'/header.before.php'));
    ?>
    <!--BEGIN:: Global.Content.Wrapper-->
    <div data-element-type="Global.Content.Wrapper">