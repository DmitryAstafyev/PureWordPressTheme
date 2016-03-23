<?php
namespace Pure\Providers\Members{
    class friends_of_user implements \Pure\Providers\Provider{
        private function validate(&$parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['from_date'        ]));
                $result = ($result === false ? false : isset($parameters['days'             ]));
                $result = ($result === false ? false : isset($parameters['targets_array'    ]));
                $parameters['format'] = (isset($parameters['format']) === true ? $parameters['format'] : 'full');
                return $result;
            }
            return false;
        }
        public function get($parameters){
            global $wpdb;
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true){
                $users_IDs = $parameters['targets_array'];
                if (count($users_IDs)>0){
                    $users_selector         = '';
                    $not_users_selector     = '';
                    $first_enter            = false;
                    foreach($users_IDs as $users_ID){
                        $users_selector     .= ($first_enter === false ? '' : ' OR ').'(initiator_user_id='.$users_ID.' OR friend_user_id='.$users_ID.')';
                        $not_users_selector .= ($first_enter === false ? '' : ' AND ').'ID<>'.$users_ID;
                        $first_enter        = true;
                    }
                    $users_data = $wpdb->get_results(   'SELECT IDs.ID FROM '.
                                                            '(SELECT initiator_user_id as ID FROM wp_bp_friends '.
                                                            'WHERE '.$users_selector.') IDs '.
                                                            'WHERE '.$not_users_selector.' '.
                                                        'UNION '.
                                                        'SELECT IDs.ID FROM '.
                                                            '(SELECT friend_user_id as ID FROM wp_bp_friends '.
                                                            'WHERE '.$users_selector.') IDs '.
                                                            'WHERE '.$not_users_selector);
                    $result     = $Common->select($users_data, $parameters, $parameters['format']);
                }
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>