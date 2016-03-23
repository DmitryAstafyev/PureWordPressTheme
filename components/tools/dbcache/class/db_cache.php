<?php
namespace Pure\Components\Tools\DBCache{
    class Groups {
        static $post        = 'post';
        static $comment     = 'comment';
        static $activity    = 'activity';
        static $author      = 'author';
        static $group       = 'group';
    }
    class Keys {
        static private function parseArguments(&$cacheKey, $arguments){
            foreach($arguments as $argument_key=>$argument){
                if (is_array($argument) !== false || is_object($argument) !== false){
                    Keys::parseArguments($cacheKey, $argument);
                }else {
                    //Exclude time marks, because it always updated from current time
                    //Also block dynamic IDs like "group"
                    if (in_array($argument_key, ['from_date', 'days', 'group']) === false) {
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
        }
        static function primitiveKey($method, $arguments, $target_IDs){
            $cacheKey = preg_replace('/\W/','_', $method).implode('_', $target_IDs);
            Keys::parseArguments($cacheKey, $arguments);
            //\Pure\Debug\Logs\Core::instance()->open($cacheKey);
            //\Pure\Debug\Logs\Core::instance()->close($cacheKey);
            return crc32($cacheKey);
        }
    }
    class Cache{
        static function get($group, $method, $target_IDs, $arguments){
            if (\Pure\Configuration::instance()->use_db_cache !== false){
                if (is_array($target_IDs) !== false){
                    if (count($target_IDs) > 0){
                        $cache_key = Keys::primitiveKey($method, $arguments, $target_IDs);
                        return (object)array(
                            'value' =>Cache::getValue($group, $cache_key),
                            'key'   =>$cache_key
                        );
                    }
                }
            }
            return (object)array(
                'value' =>false,
                'key'   =>false
            );
        }
        static function set($group, $cache_key, $target_IDs, $cache_value){
            if (\Pure\Configuration::instance()->use_db_cache !== false){
                if ($cache_key !== false){
                    if (is_array($target_IDs) !== false) {
                        if (count($target_IDs) > 0) {
                            return Cache::setValue($group, $cache_key, $target_IDs, $cache_value);
                        }
                    }
                }
            }
            return false;
        }
        static public function reset($group, $target){
            if (\Pure\Configuration::instance()->use_db_cache !== false){
                if ((int)$target > 0){
                    global $wpdb;
                    $selector = 'DELETE FROM wp_pure_db_cache WHERE cache_group="'.$group.'" AND targets LIKE "%i:'.(int)$target.';%"';
                    return $wpdb->query($selector);
                }
            }
            return false;
        }
        static private function getValue($group, $cache_key){
            global $wpdb;
            $selector   =   'SELECT '.
                                'cache_value '.
                            'FROM '.
                                \Pure\DataBase\TablesNames::instance()->db_cache.' '.
                            'WHERE '.
                                'cache_group = "'.$group.'" AND cache_key = "'.$cache_key.'"';
            $cache_value    = $wpdb->get_results($selector);
            if (is_array($cache_value) !== false){
                if (count($cache_value) === 1){
                    return Cache::decode($cache_value[0]->cache_value);
                }
            }
            return false;
        }
        static private function setValue($group, $cache_key, $target_IDs, $cache_value){
            global $wpdb;
            $cached_value   = Cache::getValue($group, $cache_key);
            $cache_value    = Cache::encode($cache_value);
            if ($cached_value === false){
                return $wpdb->insert(
                    \Pure\DataBase\TablesNames::instance()->db_cache,
                    array(  'cache_group'   => $group,
                            'cache_key'     => (int)$cache_key,
                            'targets'       => serialize($target_IDs),
                            'cache_value'   => $cache_value),
                    array( '%s', '%d', '%s', '%s' )
                );
            }else{
                return $wpdb->update(
                    \Pure\DataBase\TablesNames::instance()->db_cache,
                    array( 'targets'        => serialize($target_IDs),  'cache_value'   => $cache_value ),
                    array( 'cache_group'    => $group,                  'cache_key'     => (int)$cache_key ),
                    array( '%s', '%s' ),
                    array( '%s', '%d' )
                );
            }
        }
        static private function decode($cache_value){
            $cache_value = base64_decode($cache_value);
            $cache_value = unserialize  ($cache_value);
            return $cache_value;
        }
        static private function encode($cache_value){
            $cache_value = serialize    ($cache_value);
            $cache_value = base64_encode($cache_value);
            return $cache_value;
        }
    }
}
?>