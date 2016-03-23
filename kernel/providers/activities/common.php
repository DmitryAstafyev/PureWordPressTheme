<?php
namespace Pure\Providers\Activities{
    class Common{
        public function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['shown'            ]));
                $result = ($result === false ? false : isset($parameters['maxcount'         ]));
                return $result;
            }
            return false;
        }
        private function addData(&$activities){
            \Pure\Components\BuddyPress\URLs\Initialization::instance()->attach(true);
            $WordPress      = new \Pure\Components\WordPress\UserData\Data();
            $BuddyPress     = new \Pure\Components\BuddyPress\URLs\Core();
            foreach($activities as $key=>$activity){
                if (isset($activity->content) !== false){
                    $activities[$key]->content = stripcslashes($activity->content);
                }
                if (isset($activity->user_id) !== false){
                    if ((int)$activity->user_id > 0){
                        $activities[$key]->user = (object)array(
                            'id'    =>(int)$activity->user_id,
                            'name'  =>$WordPress->get_name((int)$activity->user_id),
                            'avatar'=>$WordPress->user_avatar_url($activity->user_id),
                            'posts' =>get_author_posts_url((int)$activity->user_id),
                            'home'  =>$BuddyPress->member($activity->user_login)
                        );
                    }else{
                        $activities[$key]->user = false;
                    }
                }else{
                    $activities[$key]->user = false;
                }
                if (isset($activity->children) !== false){
                    if (is_array($activity->children) !== false){
                        $this->addData($activities[$key]->children);
                    }
                }
            }
            $BuddyPress     = NULL;
            $WordPress      = NULL;
        }
        public function get_activities_by_IDs($IDs, $parameters){
            $result = (object)array(
                'activities'=>array(),
                'count'     =>0
            );
            if (is_array($IDs) === true){
                $_IDs = array();
                foreach($IDs as $id){
                    $_IDs[] = (int)$id->id;
                }
                $IDs = NULL;
                //Get all records
                $data = bp_activity_get_specific(
                    array (
                        'activity_ids'      =>$_IDs,
                        'max'               => 100000,
                        'display_comments'  => true
                    )
                );
                //Remove shown before
                $result->activities = (isset($data["activities"]) === true ? $data["activities"] : NULL);
                if (is_null($result->activities) === false){
                    //Remove unnecessary
                    $result->total = count($result->activities);
                    if ((int)$parameters['shown'] > 0){
                        array_splice($result->activities, 0, (int)$parameters['shown']);
                    }
                    array_splice($result->activities, (int)$parameters['maxcount'], count($result->activities) );
                    $result->shown = count($result->activities);
                    //Add data
                    $this->addData($result->activities);
                }
            }
            return $result;
        }
        public function getItemByID($item_id){
            $item = false;
            if ((int)$item_id > 0){
                global $wpdb;
                $selector   = 'SELECT * FROM wp_bp_activity WHERE id='.(int)$item_id;
                $item       = $wpdb->get_results($selector);
                $item       = (is_array($item) !== false ? (count($item) === 1 ? $item[0] : false): false);
            }
            return $item;
        }
    }
}
?>