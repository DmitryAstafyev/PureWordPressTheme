<?php
namespace Pure\Components\webSocketServer\Module{
    class Starter{
        private function initSettings(){
            require_once(\Pure\Configuration::instance()->dir(\Pure\Components\webSocketServer\Module\Initialization::instance()->configuration->paths->bin.'/common/settings.php'));
            $Settings = new \Pure\Components\webSocketServer\Common\Settings('', false);
            $settings = $Settings->get();
            $Settings = NULL;
            foreach($settings as $key=>$value){
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'window.pure.globalsettings.webSocketServer.'.$key,
                    $value,
                    false,
                    true
                );
            }
        }
        private function initClientModule(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->webSocketServer->properties;
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            if ($settings->start_mode !== 'off'){
                \Pure\Components\Attacher\Module\Attacher::instance()->addJS(
                    \Pure\Components\webSocketServer\Module\Initialization::instance()->configuration->urls->bin.'/client/js/webSocketServer.js',
                    false,
                    true
                );
            }
        }
        private function initSecurityToken(){
            \Pure\Components\Token\Module\Initialization::instance()->attach();
            $Token = new \Pure\Components\Token\Module\Token();
            $token = $Token->update();
            $Token = NULL;
            if ($token !== false){
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'window.pure.globalsettings.webSocketServer.token',
                    $token->token,
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'window.pure.globalsettings.webSocketServer.user_id',
                    $token->user_id,
                    false,
                    true
                );
            }
        }
        private function isWorking(){
            global $wpdb;
            $result     = false;
            $selector   = 'SELECT * FROM '.\Pure\DataBase\TablesNames::instance()->websockets_state;
            $instances  = $wpdb->get_results($selector);
            if (count($instances) > 0){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->webSocketServer->properties;
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                $checkpoint = strtotime("now");
                foreach($instances as $instance){
                    $pulse  = strtotime($instance->pulse);
                    $result = ($result === true ? true : ($checkpoint - $pulse > (((int)$settings->heartbeat_timeout) * 2) ? false : true));
                    //echo var_dump(($checkpoint - $pulse));
                }
            }
            return $result;
        }
        public function reset(){
            global $wpdb;
            try{
                $wpdb->query(   'DELETE FROM '.\Pure\DataBase\TablesNames::instance()->websockets_state);
                $wpdb->query(   'INSERT INTO '.
                                    \Pure\DataBase\TablesNames::instance()->websockets_state.' '.
                                'SET '.
                                    'uniqid = "'.   uniqid().           '",'.
                                    'pulse = "'.    date("Y-m-d H:i:s").'";'
                );
                return true;
            }catch (\Exception $e){
                return false;
            }
        }
        private function run() {
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->webSocketServer->properties;
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            if ($settings->start_mode === 'auto' || $settings->start_mode === 'launcher'){
                if ($this->isWorking() === false){
                    \Pure\Components\Tools\System\Initialization::instance()->attach();
                    $System     = new \Pure\Components\Tools\System\Core();
                    $file_name  = ($settings->start_mode === 'auto' ? 'initialization.php' : 'launcher.php');
                    $prefix     = (class_exists('\Worker') !== false ? '' : 'none');
                    if (substr(php_uname(), 0, 7) == "Windows"){
                        $command = 'php -q '.addslashes(\Pure\Configuration::instance()->dir(Initialization::instance()->instance()->configuration->paths->bin.'/'.$prefix.'thread/'.$file_name));
                    }else {
                        $command = 'php -q '.\Pure\Configuration::instance()->dir(Initialization::instance()->instance()->configuration->paths->bin.'/'.$prefix.'thread/'.$file_name);
                    }
                    $this->reset();
                    $System->run($command);
                    $System = NULL;
                }
            }
        }
        public function proceed(){
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                $this->initSettings();
                $this->initClientModule();
                $this->initSecurityToken();
                $this->run();
            }
        }
    }
    $starter = new Starter();
    $starter->proceed();
    $starter = NULL;
}
?>