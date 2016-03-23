<?php
namespace Pure\Providers\Members{
    class users implements \Pure\Providers\Provider{
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
                    foreach($users_IDs as $user_ID){
                        $record             = new \stdClass();
                        $record->ID         = $user_ID;
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