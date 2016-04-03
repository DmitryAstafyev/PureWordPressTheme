<?php
namespace Pure\Components\webSocketServer\Module {
    class Resources{
        public  $paths;
        private $parameters;
        private $classes = array(
            //Common resources
            'Settings'          =>array('type'=>'common', 'file'=>'settings.php'                        ),
            'Logs'              =>array('type'=>'common', 'file'=>'logs.php'                            ),
            'Encoding'          =>array('type'=>'common', 'file'=>'encoding.php'                        ),
            'Pulse'             =>array('type'=>'common', 'file'=>'pulse.php'                           ),
            //Module resources
            'Heartbeat'         =>array('type'=>'bin', 'file'=>'nonethread/heartbeat.class.php'         ),
            'Connection'        =>array('type'=>'bin', 'file'=>'nonethread/connection.class.php'        ),
            'Connections'       =>array('type'=>'bin', 'file'=>'nonethread/connections.class.php'       ),
            'Server'            =>array('type'=>'bin', 'file'=>'nonethread/server.class.php'            ),
            //Jobs of connection
            'GetRequire'        => array('type'=>'bin', 'file'=>'nonethread/jobs/auth.require.get.php'  ),
            'SendRequire'       => array('type'=>'bin', 'file'=>'nonethread/jobs/auth.require.send.php' ),
            //Core resources
            'Token'             =>array('type'=>'components', 'file'=>'token/module/class/token.php'    ),
            //Specific
            'WordPress'         =>false
        );
        public function validate(&$parameters){
            $parameters                     = (is_object($parameters) === true ? $parameters : new \stdClass());
            $parameters->uniqid             = (isset($parameters->uniqid    ) === true ? (gettype($parameters->uniqid   ) === "string"  ? $parameters->uniqid           : ''    ) : ''      );
            $parameters->caller             = (isset($parameters->caller    ) === true ? (gettype($parameters->caller   ) === "string"  ? '['.$parameters->caller.']'   : ''    ) : ''      );
            $parameters->classes            = (isset($parameters->classes   ) === true ? (gettype($parameters->classes  ) === "array"   ? $parameters->classes          : []    ) : []      );
            $parameters->settings           = (isset($parameters->settings  ) === true ? (gettype($parameters->settings ) === "object"  ? $parameters->settings         : false ) : false   );
            return $parameters;
        }
        function __construct($parameters = NULL){
            $this->parameters   = $this->validate($parameters);
            $this->paths        = (object)array(
                'basic'         =>\Pure\Components\webSocketServer\Paths::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'wp-content') - 1))),
                'module'        =>__DIR__,
                'common'        =>\Pure\Components\webSocketServer\Paths::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'websocketserver') - 1)).'/websocketserver/module/bin/common'),
                'bin'           =>\Pure\Components\webSocketServer\Paths::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'websocketserver') - 1)).'/websocketserver/module/bin'),
                'components'    =>\Pure\Components\webSocketServer\Paths::instance()->dir(substr(__DIR__, 0, (stripos(__DIR__, 'wp-content') - 1)).'/wp-content/themes/pure/components'),
            );
            $this->classes['Heartbeat'          ]['file'] = \Pure\Components\webSocketServer\Paths::instance()->dir($this->classes['Heartbeat'         ]['file']);
            $this->classes['Connection'         ]['file'] = \Pure\Components\webSocketServer\Paths::instance()->dir($this->classes['Connection'        ]['file']);
            $this->classes['Connections'        ]['file'] = \Pure\Components\webSocketServer\Paths::instance()->dir($this->classes['Connections'       ]['file']);
            $this->classes['Server'             ]['file'] = \Pure\Components\webSocketServer\Paths::instance()->dir($this->classes['Server'            ]['file']);
            $this->classes['GetRequire'         ]['file'] = \Pure\Components\webSocketServer\Paths::instance()->dir($this->classes['GetRequire'        ]['file']);
            $this->classes['SendRequire'        ]['file'] = \Pure\Components\webSocketServer\Paths::instance()->dir($this->classes['SendRequire'       ]['file']);
            $this->classes['Token'              ]['file'] = \Pure\Components\webSocketServer\Paths::instance()->dir($this->classes['Token'             ]['file']);
        }
        public function attach(){
            foreach($this->classes as $class_name=>$class_source){
                if (isset($webSocketServerResources[$class_name]) === false){
                    if (isset($this->parameters->classes[$class_name]) !== false){
                        if ($this->parameters->classes[$class_name] !== false){
                            if ($class_source !== false){
                                if (class_exists(__NAMESPACE__.'\\'.$class_name) === false){
                                    require_once(\Pure\Components\webSocketServer\Paths::instance()->dir($this->paths->$class_source['type'].'/'.$class_source['file']));
                                    $this->log('['.$this->parameters->uniqid.']'.$this->parameters->caller.'[RESOURCES]:: Attached ...\\'.$class_source['file']);
                                    //echo "\r\n"."\r\n".$this->paths->$class_source['type'].'\\'.$class_source['file']."\r\n"."\r\n";
                                }
                            }else{
                                switch($class_name){
                                    case 'WordPress':
                                        if (class_exists('\wpdb') === false){
                                            $this->wordpress();
                                            $this->log('['.$this->parameters->uniqid.']'.$this->parameters->caller.'[RESOURCES]:: Attached WordPress Core');
                                        }
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }
        private function wordpress(){
            /* For WordPress 3.9 or less can be necessary include also [wp-config.php]
             * require_once($this->paths->basic.'/wp-config.php');
             */
            require_once(\Pure\Components\webSocketServer\Paths::instance()->dir($this->paths->basic.'/wp-load.php')             );
            require_once(\Pure\Components\webSocketServer\Paths::instance()->dir($this->paths->basic.'/wp-includes/wp-db.php')   );
        }
        private function log($message, $status = ""){
            if (class_exists('\Pure\Components\webSocketServer\Common\Logs'         ) !== false &&
                (class_exists('\Pure\Components\webSocketServer\Common\Settings'    ) !== false ||
                $this->parameters->settings !== false)){
                if (isset($this->logs_instance) === false){
                    $this->logs_instance = new \Pure\Components\webSocketServer\Common\Logs($this->parameters->uniqid, $this->parameters->settings);
                }
                $this->logs_instance->log($message, $status);
            }
        }
    }
}
?>