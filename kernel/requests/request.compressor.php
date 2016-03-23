<?php
namespace Pure\Requests\Compressor{
    class Core{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $parameters->crc32 = (string)($parameters->crc32);
                    $parameters->crc32 = explode(',',$parameters->crc32);
                    if (is_array($parameters->crc32) !== false){
                        if (count($parameters->crc32) > 0){
                            $mapper             = function($value){
                                return (int)$value;
                            };
                            $parameters->crc32  = array_map($mapper, $parameters->crc32);
                            $mapper             = NULL;
                            return true;
                        }
                    }
                    return false;
                    break;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function get($parameters){
            if ($this->validate($parameters, __METHOD__) !== false) {
                $this->sanitize($parameters, __METHOD__);
                $cached = \Pure\Resources\Compressor::instance()->getCached();
                if ($cached !== false){
                    $resources = array();
                    foreach($parameters->crc32 as $crc32){
                        $key = array_search($crc32, $cached->CRC32);
                        if ($key !== false){
                            $type               = $cached->types[$key];
                            if (isset($resources[$type]) === false){
                                $resources[$type] = array();
                            }
                            $output             = @file_get_contents($cached->files[$key]);
                            $resources[$type][] = (object)array(
                                'crc32'=>$cached->CRC32[$key],
                                'cache'=>base64_encode($output)
                            );
                        }
                    }
                    echo json_encode($resources);
                    return true;
                }
            }
            echo 'fail';
            return false;
        }
    }
}
?>