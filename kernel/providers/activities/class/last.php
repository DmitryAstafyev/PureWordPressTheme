<?php
namespace Pure\Providers\Activities{
    class last implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                return $result;
            }
            return false;
        }
        public function get($parameters){
            $result             = (object)array(
                'activities'=>array(),
                'count'     =>0,
                'total'     =>0,
                'shown'     =>0
            );
            $Common     = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true) {
                $IDs = NULL;
                global $wpdb;
                $IDs = $wpdb->get_results(  'SELECT id, user_id, date_recorded ' .
                                                'FROM wp_bp_activity ' .
                                                'ORDER BY date_recorded DESC', OBJECT_K);
                if (is_null($IDs) === false) {
                    if (count($IDs) > 0){
                        $result = $Common->get_activities_by_IDs($IDs, $parameters);
                    }
                }
            }
            $Common = NULL;
            return $result;
        }
    }
}
?>