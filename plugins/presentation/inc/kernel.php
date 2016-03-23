<?php
namespace Pure\Plugins\Presentation {
    class Builder{
        private $parameters;
        function __construct($parameters){
            $this->parameters = $this->validate($parameters);
            if (is_bool($this->parameters) == true){
                throw new \Exception("Pure\Plugins\Presentation\Builder\__construct::: cannot validate settings of widget", E_USER_WARNING);
            }
        }
        private function validate($parameters){
            if (isset($parameters['content'])                                                           === false ||
                \Pure\Providers\Posts\Initialization::instance()->is_available($parameters['content'])  === false){
                return false;
            }
            $parameters['targets'	] = (isset($parameters['targets'	]) == false ? NULL 	: (strlen($parameters['targets']) === 0 ? NULL : $parameters['targets'] ));
            $parameters['maxcount'	] = (isset($parameters['maxcount'	]) == false ? NULL 	: $parameters['maxcount'	]);
            if (is_null($parameters['maxcount']) == false){
                $parameters['maxcount'] = (integer)$parameters['maxcount'];
                $parameters['maxcount'] = (is_numeric($parameters['maxcount']) == false ? 100 : $parameters['maxcount']);
            }
            if (is_null($parameters['targets']) == false){
                $targets	    = preg_split('/,/', $parameters['targets']);
                $targets_array  = array();
                $strTargets     = '';
                for ($index = count($targets) - 1; $index >= 0; $index --){
                    $targets[$index] = (integer)$targets[$index];
                    $targets[$index] = (is_numeric($targets[$index]) == false ? NULL : $targets[$index]);
                    if (is_null($targets[$index]) == false){
                        if (strlen($strTargets) > 0){
                            $strTargets = $strTargets.','.((string)$targets[$index]);
                        }else{
                            $strTargets = (string)$targets[$index];
                        }
                        array_push($targets_array, (integer)$targets[$index]);
                    }
                }
                $parameters['targets']          = $strTargets;
                $parameters['targets_array']    = $targets_array;
            }else{
                $parameters['targets_array'] = array();
            }
            $parameters['shown'         ] = 0;
            $parameters['profile'       ] = '';
            $parameters['post_type'     ] = array('post', 'event');
            $parameters['post_status'   ] = 'publish';
            $parameters['from_date'     ] = date("Y-m-d H:i:s");
            $parameters['days'          ] = 3650;
            $parameters['thumbnails'    ] = true;
            $parameters['selection'     ] = false;
            return $parameters;
        }
        public function render(){
            $innerHTML  = '';
            $provider   = \Pure\Providers\Posts\Initialization::instance()->get($this->parameters['content']);
            if ($provider !== false) {
                $posts      = $provider->get($this->parameters);
                $provider   = NULL;
                if (isset($this->parameters['template']) === true && $posts !== false) {
                    $PostTemplate   = \Pure\Templates\Presentation\Initialization::instance()->get($this->parameters['template']);
                    if (is_null($PostTemplate) === false) {
                        $innerHTML  = $PostTemplate->get($posts->posts);
                    }
                    $PostTemplate   = NULL;
                }
            }
            return $innerHTML;
        }
    }
}
?>