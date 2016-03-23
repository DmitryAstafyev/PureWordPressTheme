<?php
namespace Pure\Components\BuddyPress\Friendship{
    class Core{
        private function validate($parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'isFriends':
                    $result = ($result === false ? $result : (isset($parameters->memberIDA) === true ? (gettype($parameters->memberIDA) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->memberIDB) === true ? (gettype($parameters->memberIDB) == 'integer'  ? true : false) : false));
                    break;
            }
            return $result;
        }
        public function isFriends($parameters){
            if ($this->validate($parameters, __METHOD__) === true){
                global $wpdb;
                $result = $wpdb->get_results(   'SELECT * '.
                                                    'FROM wp_bp_friends '.
                                                        'WHERE '.
                                                            '(initiator_user_id='.$parameters->memberIDA.' AND friend_user_id='.$parameters->memberIDB.') OR '.
                                                            '(initiator_user_id='.$parameters->memberIDB.' AND friend_user_id='.$parameters->memberIDA.');');
                if (count($result) > 0){
                    if (isset($result[0]) === true){
                        return (object)array(
                            'created'       =>$result[0]->date_created,
                            'accepted'      =>((int)$result[0]->is_confirmed === 0 ? false : true),
                            'initiator'     =>(int)$result[0]->initiator_user_id,
                            'friend'        =>(int)$result[0]->friend_user_id,
                            'id'            =>(int)$result[0]->id,
                        );
                    }
                }
            }
            return false;
        }

        function __construct(){
        }
    }
}
?>