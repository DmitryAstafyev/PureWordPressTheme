<?php
namespace Pure\Providers\Members{
    class talks_of_user implements \Pure\Providers\Provider{
        private function validate(&$parameters){
            if (is_array($parameters) === true){
                $result                 = true;
                $result                 = ($result === false ? false : isset($parameters['user_id']));
                $parameters['format']   = (isset($parameters['format']) === true ? $parameters['format'] : 'full');
                return $result;
            }
            return false;
        }
        public function get($parameters){
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true){
                \Pure\Components\Messenger\Chat\Initialization::instance()->attach(true);
                $Chat       = new \Pure\Components\Messenger\Chat\Provider();
                $users_data = $Chat->getRecipientsOfUser($parameters['user_id']);
                $Chat       = NULL;
                $result     = ($users_data !== false ? $Common->select($users_data, $parameters, $parameters['format'], ['thread_id']) : false);
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>