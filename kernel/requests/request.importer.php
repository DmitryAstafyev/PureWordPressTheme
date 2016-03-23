<?php
namespace Pure\Requests\Importer{
    class Core{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'progress':
                    $parameters->index = (integer)($parameters->index);
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function progress($parameters){
            if ($this->validate($parameters, __METHOD__) === true) {
                $this->sanitize($parameters, __METHOD__);
                if ((int)$parameters->index >= 0){
                    \Pure\Components\Demo\Module\Initialization::instance()->attach(true);
                    $logs = \Pure\Components\Demo\Module\Logs::get((int)$parameters->index);
                    if ($logs !== false){
                        echo json_encode($logs);
                        return true;
                    }
                }
            }
            echo 'fail';
            return false;
        }
    }
}
?>