<?php
namespace Pure\Components\Tools\Cache{
    class Keys {
        static private function parseArguments(&$cacheKey, $arguments){
            foreach($arguments as $argument){
                if (is_array($argument) !== false || is_object($argument) !== false){
                    Keys::parseArguments($cacheKey, $argument);
                }else {
                    switch (gettype($argument)) {
                        case 'boolean':
                            $cacheKey .= ($argument === false ? '_F_' : '_T_');
                            break;
                        case 'integer':
                            $cacheKey .= (string)$argument;
                            break;
                        case 'double':
                            $cacheKey .= (string)$argument;
                            break;
                        case 'string':
                            $cacheKey .= preg_replace('/\W/', '_', $argument);
                            break;
                    }
                }
            }
        }
        static function primitiveKey($method, $arguments){
            $cacheKey = preg_replace('/\W/','_', $method);
            Keys::parseArguments($cacheKey, $arguments);
            return $cacheKey;
        }
    }
    class Cache{
        static function get($method, $arguments){
            if (\Pure\Configuration::instance()->use_cache !== false){
                $key = Keys::primitiveKey($method, $arguments);
                return (object)array(
                    'value' =>wp_cache_get($key),
                    'key'   =>$key
                );
            }else{
                return (object)array(
                    'value' =>false,
                    'key'   =>false
                );
            }
        }
        static function set($key, $value){
            if (\Pure\Configuration::instance()->use_cache !== false){
                return wp_cache_set($key, $value);
            }else{
                return false;
            }
        }
    }
}
?>