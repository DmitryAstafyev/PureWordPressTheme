<?php
namespace Pure\Debug\Logs{
    class Core{
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        private $session_id;
        private $storage;
        function __construct(){
            $this->session_id   = uniqid();
            $this->storage      = (object)array(
                'IDs'   =>array(),
                'data'  =>array()
            );
        }
        public function open($operation){
            if (\Pure\Configuration::instance()->do_duration_logs !== false){
                $id                         = uniqid();
                $this->storage->IDs[$id]    = $operation;
                $this->storage->data[$id]   = (object)array(
                    'operation' =>$operation,
                    '_start'    =>(function_exists('microtime') === false ? time() : microtime(true)),
                    '_finish'   =>0,
                    'start'     =>date("Y-m-d H:i:s"),
                    'finish'    =>''
                );
            }
        }
        public function close($operation){
            if (\Pure\Configuration::instance()->do_duration_logs !== false){
                $id = array_search($operation, $this->storage->IDs);
                if ($id !== false){
                    unset($this->storage->IDs[$id]);
                    if (isset($this->storage->data[$id]) !== false){
                        $this->storage->data[$id]->_finish  = (function_exists('microtime') === false ? time() : microtime(true));
                        $this->storage->data[$id]->finish   = date("Y-m-d H:i:s");
                        $Provider = new Provider();
                        $Provider->add(
                            (object)array(
                                'session_id'    =>$this->session_id,
                                'operation'     =>$operation,
                                'start'         =>$this->storage->data[$id]->start,
                                'finish'        =>$this->storage->data[$id]->finish,
                                'duration'      =>((float)$this->storage->data[$id]->_finish - (float)$this->storage->data[$id]->_start),//in seconds
                            )
                        );
                        $Provider = NULL;
                        unset($this->storage->data[$id]);
                    }
                }
            }
        }
    }
    class Provider{
        public function add($parameters){
            if (\Pure\Configuration::instance()->do_duration_logs !== false){
                global $wpdb;
                $result = $wpdb->insert(
                    'wp_pure_logs',
                    array(
                        'session_id'    =>$parameters->session_id,
                        'operation'     =>$parameters->operation,
                        'start'         =>$parameters->start,
                        'finish'        =>$parameters->finish,
                        'duration'      =>(float)$parameters->duration,//in seconds
                    ),
                    array('%s', '%s', '%s', '%s', '%f')
                );
            }
        }
    }
}
?>