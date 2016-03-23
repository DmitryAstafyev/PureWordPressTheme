<?php
// [BEGIN]::: Save all globalsettings ===============================================================================
\Pure\Components\WordPress\Settings\Initialization::instance()->attach();
$status_of_saving = \Pure\Components\WordPress\Settings\Instance::instance()->tryToSaveFromPOST('webSocketServer');
// [END]::: Save all globalsettings =================================================================================
\Pure\Components\WordPress\Settings\Instance::instance()->reload();
$properties             = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->webSocketServer->properties;
$prefix                 = \Pure\Components\WordPress\Settings\Instance  ::instance()->settings->webSocketServer->id;
$groups                 = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
$groups->open(
    array(
        "title"             =>__( "WebSocket server settings", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>true,
        "container_style"   =>'width:auto;padding:0.5em;',
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<form method="POST" action="">
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('What is it?', 'pure');?></strong></p>
    <p data-type="Pure.Configuration.Info"><?php echo __('WebSocket server allows communicate in real-time mode. For example, chat. With active server members will see messages immediately without any delay and addition requests to server. Also WebSocket server allow render any changes of comments in real-time mode.', 'pure');?></p>
    <p data-type="Pure.Configuration.Info"><?php echo __('You can switch off WebSocket server, in this case site will work, but chat will not be available.', 'pure');?></p>
    <p data-type="Pure.Configuration.Accent"><?php echo __('WebSocket server can work in two modes: none-thread and thread mode. Thread mode requires PHPThreads on your server. Thread mode will use automatically if PHPThreads are installed. What use? None-thread server is better for small systems - not more 500 users online at moment. And thread server is more stable for big systems with more than 500 users online. In addition, you should know, thread server "eats" much more memory than none-thread.', 'pure');?></p>
    <p data-type="Pure.Configuration.Title"><strong><?php echo __('Basic settings', 'pure');?></strong></p>
    <table>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Start mode', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->start_mode->name;?>" name="<?php echo $properties->start_mode->name; ?>">
                    <option value="launcher" <?php selected( 'launcher', $properties->start_mode->value ); ?>><?php echo __('Start and restart server automatically', 'pure' ); ?></option>
                    <option value="auto" <?php selected( 'auto', $properties->start_mode->value ); ?>><?php echo __('Start server automatically (by request)', 'pure' ); ?></option>
                    <option value="manually" <?php selected( 'manually', $properties->start_mode->value ); ?>><?php echo __('I will start server manually', 'pure' ); ?></option>
                    <option value="off" <?php selected( 'off', $properties->start_mode->value ); ?>><?php echo __('Do not use it.', 'pure' ); ?></option>
                </select>
                <p data-type="Pure.Configuration.Info"><?php echo __('Modes "Start and restart server automatically" and "Start server automatically (by request)" are different. In first case will be started launcher (not webSocketServer). This launcher starts webSocketServer and controls its heartbeat. If server stops, launcher will restart it automatically. In second case ("Start server automatically (by request)") status of webSocketServer checks only if some user makes request to site. In this case if at current moment nobody are on site, webSocketServer can be stopped (until somebody visit it).', 'pure');?></p>
                <p data-type="Pure.Configuration.Accent"><?php echo __('To launch server manually you should call in OS console next command: [php -q initialization.php] from folder "../wp-content/themes/pure/components/webSocketServer/Module/bin/thread".', 'pure');?></p>
                <p data-type="Pure.Configuration.Accent"><?php echo __('If you choose option "Do not use it" client part of webSocket will not be loaded on client side.', 'pure');?></p>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('IP adress of server', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="text" value="<?php echo $properties->address->value; ?>" id="<?php echo $properties->address->name; ?>" name="<?php echo $properties->address->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Port', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->port->value; ?>" id="<?php echo $properties->port->name; ?>" name="<?php echo $properties->port->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Backlog', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('If you do not know what does it mean, just leave it as default', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->backlog->value; ?>" id="<?php echo $properties->backlog->name; ?>" name="<?php echo $properties->backlog->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Logs', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Do logs or not.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->logs->name;?>" name="<?php echo $properties->logs->name; ?>">
                    <option value="on" <?php selected( 'on', $properties->logs->value ); ?>><?php echo __('Allow', 'pure' ); ?></option>
                    <option value="off" <?php selected( 'off', $properties->logs->value ); ?>><?php echo __('Deny', 'pure' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Logs rendering', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->logs_as_comment->name;?>" name="<?php echo $properties->logs_as_comment->name; ?>">
                    <option value="on" <?php selected( 'on', $properties->logs_as_comment->value ); ?>><?php echo __('Show as comment', 'pure' ); ?></option>
                    <option value="off" <?php selected( 'off', $properties->logs_as_comment->value ); ?>><?php echo __('Render in console only', 'pure' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Heartbeat time out in seconds', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Server should check his status periodically to be sure, that everything is ok. Here you can define duration of such period', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->heartbeat_timeout->value; ?>" id="<?php echo $properties->heartbeat_timeout->name; ?>" name="<?php echo $properties->heartbeat_timeout->name; ?>" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('Duration of server life in heartbeat iterations', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('Server can be automatically rebooted after defined count of heartbeat iterations. For example, if you set heartbeat duration in 60 seconds and this parameter in 1000 - each 1000 * 60 seconds server will be stopped (not rebooted). It can be useful for deep clearing memory usage.', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <input data-type="Pure.Configuration.Input" class="checkbox" type="number" value="<?php echo $properties->heartbeat_interations->value; ?>" id="<?php echo $properties->heartbeat_interations->name; ?>" name="<?php echo $properties->heartbeat_interations->name; ?>" />
                <p data-type="Pure.Configuration.Attention"><?php echo __('Attention. If you start server manually, in this case server will not reboot, but just switch off.', 'pure');?></p>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; min-width: 15em; vertical-align: top;">
                <p data-type="Pure.Configuration.Title"><?php echo __('PHP debug mode', 'pure');?></p>
                <p data-type="Pure.Configuration.Info"><?php echo __('This option is very useful if you launch webSocketServer manually (via [php -q initialization.php]). In this case you can switch on debug mode and see a lot information about current work of server in real-time mode.', 'pure');?></p>
                <p data-type="Pure.Configuration.Attention"><?php echo __('To launch server manually you should call in OS console next command: [php -q initialization.php] from folder "../wp-content/themes/pure/components/webSocketServer/Module/bin/thread".', 'pure');?></p>
            </td>
            <td style="width: auto; vertical-align: top;">
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->php_debug->name;?>" name="<?php echo $properties->php_debug->name; ?>">
                    <option value="on" <?php selected( 'on', $properties->php_debug->value ); ?>><?php echo __('Debug mode on', 'pure' ); ?></option>
                    <option value="off" <?php selected( 'off', $properties->php_debug->value ); ?>><?php echo __('Debug mode off', 'pure' ); ?></option>
                </select>
                <p data-type="Pure.Configuration.Info"><?php echo __('In console you can see current usage of memory by webSocketServer. This is actual only if PHP debug mode is on.', 'pure');?></p>
                <select data-type="Pure.Configuration.Input.Fader" id="<?php echo $prefix.$properties->show_memoryusage_with_heartbeat->name;?>" name="<?php echo $properties->show_memoryusage_with_heartbeat->name; ?>">
                    <option value="on" <?php selected( 'on', $properties->show_memoryusage_with_heartbeat->value ); ?>><?php echo __('Show memory usage', 'pure' ); ?></option>
                    <option value="off" <?php selected( 'off', $properties->show_memoryusage_with_heartbeat->value ); ?>><?php echo __('Do not show memory usage', 'pure' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p data-type="Pure.Configuration.Attention"><?php echo __('Attention. Your php server should support <strong>Sockets</strong> and <strong>PHPThreads</strong> (if you want to use thread server for big systems). See below instruction.', 'pure');?></p>
            </td>
        </tr>
    </table>
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
<?php
$groups->open(
    array(
        "title"             =>__( "Install PHP Sockets", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>false,
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<p data-type="Pure.Configuration.Info">Under windows you just need to uncomment the line</p>
<p data-type="Pure.Configuration.Info">;extension=php_sockets.dll</p>
<p data-type="Pure.Configuration.Info">so that it looks like</p>
<p data-type="Pure.Configuration.Info">extension=php_sockets.dll</p>
<p data-type="Pure.Configuration.Info">(without the ; at the start)</p>
<p data-type="Pure.Configuration.Info">in your php.ini (in the directory c:\program files\PHP\php.ini)</p>
<p data-type="Pure.Configuration.Info">If that line isn't in there, you probably have a custom php install. In that case you need to add the line to php.ini and download the sockets dll.</p>
<p data-type="Pure.Configuration.Info">In order to get the php_sockets.dll:</p>
<p data-type="Pure.Configuration.Info">- Download the regular php zip file from http://www.php.net/downloads.php</p>
<p data-type="Pure.Configuration.Info">- Find the dll in the ext directory</p>
<p data-type="Pure.Configuration.Info">- Extract the php_sockets.dll to the ext directory of your install</p>
<p data-type="Pure.Configuration.Info">(probably at c:\program files\PHP\ext)</p>
<p data-type="Pure.Configuration.Info">Source: <a href="http://php.net/manual/en/sockets.installation.php" target="_blank">here</a></p>
<?php
$groups->close(false);
?>
<?php
$groups->open(
    array(
        "title"             =>__( "Install PHP Threads", 'pure' ),
        "group"             =>uniqid(),
        "echo"              =>true,
        "opened"            =>false,
        "content_style"     =>'width:auto;padding:0.5em;'
    )
);
?>
<p data-type="Pure.Configuration.Info">For Wampp (Windows)</p>
<p data-type="Pure.Configuration.Info">1.  Find out what is your 'PHP Extension Build' version by using phpinfo(). You can use this - http://localhost/?phpinfo=1</p>
<p data-type="Pure.Configuration.Info">2.  Download the pthreads that matches your php version (32 bit or 64 bit) and php extension build (currently used VC11). Use this link for download - http://windows.php.net/downloads/pecl/releases/pthreads/</p>
<p data-type="Pure.Configuration.Info">3.  Extract the zip -</p>
<p data-type="Pure.Configuration.Info">Move php_pthreads.dll to the 'bin\php\ext\' directory.</p>
<p data-type="Pure.Configuration.Info">Move pthreadVC2.dll to the 'bin\php\' directory.</p>
<p data-type="Pure.Configuration.Info">Move pthreadVC2.dll to the 'bin\apache\bin' directory.</p>
<p data-type="Pure.Configuration.Info">Move pthreadVC2.dll to the 'C:\windows\system32' directory.</p>
<p data-type="Pure.Configuration.Info">4.  Open php\php.ini and add</p>
<p data-type="Pure.Configuration.Info">extension=php_pthreads.dll</p>
<p data-type="Pure.Configuration.Info">Now restart server and you are done. Thanks.</p>
<p data-type="Pure.Configuration.Info">Source: <a href="http://php.net/manual/en/pthreads.installation.php" target="_blank">here</a></p>
<?php
$groups->close(false);
?>
