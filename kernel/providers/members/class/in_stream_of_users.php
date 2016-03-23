<?php
namespace Pure\Providers\Members{
    class in_stream_of_users implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['from_date'        ]));
                $result = ($result === false ? false : isset($parameters['days'             ]));
                $result = ($result === false ? false : isset($parameters['targets_array'    ]));
                return $result;
            }
            return false;
        }
        public function get($parameters){
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true){
                $users_IDs      = $parameters['targets_array'];
                $users_data     = array();
                if (count($users_IDs) > 0){
                    \Pure\Components\Stream\Module\Initialization::instance()->attach();
                    $Stream     = new \Pure\Components\Stream\Module\Provider();
                    $in_stream  = array();
                    foreach($users_IDs as $user_id){
                        $IDs = $Stream->get_users_IDs_in_stream($user_id);
                        if (is_array($IDs) !== false){
                            foreach($IDs as $ID){
                                if (in_array((int)$ID, $in_stream) === false){
                                    $in_stream[] = $ID;
                                }
                            }
                        }
                    }
                    $Stream     = NULL;
                    foreach($in_stream as $user_id){
                        $record             = new \stdClass();
                        $record->ID         = $user_id;
                        $users_data[]       = $record;
                    }
                }
                $result         = $Common->select($users_data, $parameters);
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>