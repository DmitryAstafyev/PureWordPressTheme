<?php
namespace Pure\Providers\Activities{
    class of_user implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['targets_array'    ]));
                return $result;
            }
            return false;
        }
        public function get($parameters){
            $result     = (object)array(
                'activities'=>array(),
                'count'     =>0,
                'total'     =>0,
                'shown'     =>0
            );
            $Common     = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true) {
                $IDs = NULL;
                if (is_array($parameters['targets_array']) === true) {
                    global $wpdb;
                    $IDs = $wpdb->get_results(  'SELECT id, user_id, date_recorded ' .
                                                    'FROM wp_bp_activity ' .
                                                        'WHERE ' .
                                                            'user_id IN (' . implode(',', $parameters['targets_array']) . ') ' .    'AND ' .
                                                            'mptt_left=0 ' .                                                        'AND ' .
                                                            'mptt_right=0 ' .                                                       'AND ' .
                                                            'hide_sitewide=0 ' .                                                    'AND ' .
                                                            'NOT secondary_item_id IS NULL ' .
                                                'UNION ' .
                                                'SELECT id, user_id, date_recorded ' .
                                                    'FROM wp_bp_activity ' .
                                                        'WHERE id IN ' .
                                                            '(SELECT item_id ' .
                                                                'FROM wp_bp_activity ' .
                                                                    'WHERE ' .
                                                                        'user_id IN (' . implode(',', $parameters['targets_array']) . ') ' .    'AND ' .
                                                                        '(mptt_left<>0 OR mptt_right<>0) ' .                                    'AND ' .
                                                                        'secondary_item_id<>0 ' .                                               'AND ' .
                                                                        'hide_sitewide=0 ' .                                                    'AND ' .
                                                                        'NOT secondary_item_id IS NULL) ' .
                                                'UNION ' .
                                                'SELECT id, user_id, date_recorded ' .
                                                    'FROM wp_bp_activity ' .
                                                        'WHERE ' .
                                                            'user_id IN (' . implode(',', $parameters['targets_array']) . ') ' .    'AND ' .
                                                            'mptt_left<>0 ' .                                                       'AND ' .
                                                            'mptt_right<>0 ' .                                                      'AND ' .
                                                            'hide_sitewide=0 ' .                                                    'AND ' .
                                                            'secondary_item_id=0 ' .                                                'AND ' .
                                                            'item_id<>0 ' .
                                                        'ORDER BY date_recorded DESC', OBJECT_K);
                }
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