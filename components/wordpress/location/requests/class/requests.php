<?php
namespace Pure\Components\WordPress\Location\Requests{
    class Core{
        private $request;
        public function is(){
            return (is_null($this->request) === true ? false : true);
        }
        private function error($message){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->basic->properties;
            $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $ErrorPage  = \Pure\Templates\Pages\Error\Initialization::instance()->get($settings->error_page_template);
            $ErrorPage->message('Request error', $message, true);
            exit;
        }
        private function validate(){
            if (isset($_POST['command']) === true || isset($_GET['command']) === true){
                $command    = strtolower(esc_attr((isset($_POST['command']) === true ? $_POST['command'] : $_GET['command'])));
                $Register   = new Register();
                if (isset($Register->commands[$command]) === true){
                    $this->request              = new \stdClass();
                    $this->request->parameters  = new \stdClass();
                    $this->request->module      = $Register->commands[$command]['module'  ];
                    $this->request->class       = $Register->commands[$command]['class'   ];
                    $this->request->method      = $Register->commands[$command]['method'  ];
                    $exclusion_esc_sql          = (isset($Register->commands[$command]['exclusion_esc_sql'] ) !== false ? $Register->commands[$command]['exclusion_esc_sql' ] : array());
                    $exclusion_exist            = (isset($Register->commands[$command]['exclusion_exist']   ) !== false ? $Register->commands[$command]['exclusion_exist'   ] : array());
                    foreach($Register->commands[$command]['parameters'] as $parameter){
                        if (isset($_POST[$parameter]) !== true && isset($_GET[$parameter]) !== true){
                            if (in_array($parameter, $exclusion_exist) !== false){
                                $this->request->parameters->$parameter = false;
                            }else{
                                $this->request  = NULL;
                                $Register       = NULL;
                                $this->error(
                                    __('We get from you request with command:', 'pure').
                                    '<br/>'.
                                    '[<strong>'.$command.'</strong>]'.
                                    '<br/>'.
                                    __('but we cannot detect all necessary parameters, which are defined for this command. Missing parameter: ['.$parameter.']', 'pure')
                                );
                                return false;
                            }
                        }else{
                            if (in_array($parameter, $exclusion_esc_sql) === false){
                                $this->request->parameters->$parameter = esc_sql((isset($_POST[$parameter]) === true ? $_POST[$parameter] : $_GET[$parameter]));
                            }else{
                                $this->request->parameters->$parameter = (isset($_POST[$parameter]) === true ? $_POST[$parameter] : $_GET[$parameter]);
                            }
                        }
                    }
                    $Register = NULL;
                    return true;
                }
            }
            $this->error(
                __('We get from you URL with request. But we cannot detect command of request.', 'pure')
            );
            return false;
        }
        public function processing(){
            try{
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/'.$this->request->module));
                $method         = $this->request->method;
                $RequestClass   = new $this->request->class(false);
                $RequestClass->$method($this->request->parameters);
                $RequestClass   = NULL;
            }catch (\Exception $e){
                return NULL;
            }
        }
        function __construct(){
            $parts  = preg_split('/\//', strtolower(preg_replace('/\?.*/', '', $_SERVER["REQUEST_URI"])));
            $parts  = array_values(array_filter( $parts, function($item){ return ($item !== '' ? true : false);} ));
            if (count($parts) === 1){
                $Register   = new Register();
                if ($parts[0] === $Register->root) {
                    if ($this->validate() === true) {
                        $Register = NULL;
                        return true;
                    }
                }
                $Register   = NULL;
            }
        }
    }
}
?>