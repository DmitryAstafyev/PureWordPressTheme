<?php
namespace Pure\Providers\Members{
    class recipients_of_user implements \Pure\Providers\Provider{
        private function validate(&$parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['user_id']));
                $parameters['format'] = (isset($parameters['format']) === true ? $parameters['format'] : 'full');
                return $result;
            }
            return false;
        }
        public function get($parameters){
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true){
                \Pure\Components\Messenger\Mails\Initialization::instance()->attach();
                $Mails      = new \Pure\Components\Messenger\Mails\Provider();
                $users_data = $Mails->getAllRecipientsOfUser($parameters['user_id']);
                $Mails      = NULL;
                $result     = ($users_data !== false ? $Common->select($users_data, $parameters, $parameters['format']) : false);
            }
            $Common         = NULL;
            return $result;
        }
    }
}
?>