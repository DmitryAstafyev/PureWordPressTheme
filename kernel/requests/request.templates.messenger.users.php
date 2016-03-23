<?php
namespace Pure\Requests\Templates\Messenger{
    class Users{
        private function validate(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'getFriends':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->shown      = (integer  )($parameters->shown    );
                    $parameters->maxcount   = (integer  )($parameters->maxcount );
                    return true;
                case 'getGroups':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->shown      = (integer  )($parameters->shown    );
                    $parameters->maxcount   = (integer  )($parameters->maxcount );
                    return true;
                case 'getRecipients':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->shown      = (integer  )($parameters->shown    );
                    $parameters->maxcount   = (integer  )($parameters->maxcount );
                    return true;
                case 'getTalks':
                    $parameters->user_id    = (integer  )($parameters->user_id  );
                    $parameters->shown      = (integer  )($parameters->shown    );
                    $parameters->maxcount   = (integer  )($parameters->maxcount );
                    return true;
            }
            return false;
        }
        private function sanitize(&$parameters, $method){
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
            }
        }
        public function getFriends($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user_id){
                    $Provider   = \Pure\Providers\Members\Initialization::instance()->get('friends_of_user');
                    $users      = $Provider->get(array(
                        'shown'             =>$parameters->shown,
                        'only_with_avatar'  =>false,
                        'maxcount'          =>$parameters->maxcount,
                        'profile'           =>'',
                        'from_date'         =>date("Y-m-d"),
                        'days'              =>9999,
                        'targets_array'     =>array($parameters->user_id),
                        'format'            =>'name_avatar_id'
                    ));
                    echo json_encode($users);
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
        public function getGroups($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user_id){
                    $ProviderGroups     = \Pure\Providers\Groups\Initialization::instance()->get('users');
                    $groups             = $ProviderGroups->get(array(
                        'shown'                     =>$parameters->shown,
                        'only_with_avatar'          =>false,
                        'maxcount'                  =>$parameters->maxcount,
                        'profile'                   =>'',
                        'from_date'                 =>date("Y-m-d"),
                        'days'                      =>9999,
                        'targets_array'             =>array($parameters->user_id),
                        'load_all_members'          =>false,
                        'load_membership_requests'  =>false,
                    ));
                    $ProviderMembers    = \Pure\Providers\Members\Initialization::instance()->get('users_of_group');
                    foreach($groups->groups as $key=>$group){
                        $members        = $ProviderMembers->get(array(
                            'shown'             =>$parameters->shown,
                            'only_with_avatar'  =>false,
                            'maxcount'          =>$parameters->maxcount,
                            'profile'           =>'',
                            'from_date'         =>date("Y-m-d"),
                            'days'              =>9999,
                            'targets_array'     =>array($group->id),
                            'format'            =>'name_avatar_id'
                        ));
                        $groups->groups[$key]->members = $members;
                    }
                    echo json_encode($groups);
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
        public function getRecipients($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user_id){
                    $Provider   = \Pure\Providers\Members\Initialization::instance()->get('recipients_of_user');
                    $users      = $Provider->get(array(
                        'shown'             =>$parameters->shown,
                        'only_with_avatar'  =>false,
                        'maxcount'          =>$parameters->maxcount,
                        'profile'           =>'',
                        'user_id'           =>$parameters->user_id,
                        'format'            =>'name_avatar_id'
                    ));
                    echo json_encode($users);
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
        public function getTalks($parameters){
            if ($this->validate($parameters, __METHOD__) !== false){
                $this->sanitize($parameters, __METHOD__);
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ((int)$current->ID === (int)$parameters->user_id){
                    $Provider   = \Pure\Providers\Members\Initialization::instance()->get('talks_of_user');
                    $users      = $Provider->get(array(
                        'shown'             =>$parameters->shown,
                        'only_with_avatar'  =>false,
                        'maxcount'          =>$parameters->maxcount,
                        'profile'           =>'',
                        'user_id'           =>$parameters->user_id,
                        'format'            =>'name_avatar_id'
                    ));
                    echo json_encode($users);
                    return true;
                }
            }
            echo 'no access';
            return false;
        }
    }
}
?>