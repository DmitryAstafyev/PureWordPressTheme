<?php
namespace Pure\Providers\Posts{
    class group implements \Pure\Providers\Provider{
        private function validate($parameters){
            if (is_array($parameters) === true){
                $result = true;
                $result = ($result === false ? false : isset($parameters['from_date'        ]));
                $result = ($result === false ? false : isset($parameters['days'             ]));
                $result = ($result === false ? false : isset($parameters['thumbnails'       ]));
                $result = ($result === false ? false : isset($parameters['targets_array'    ]));
                $parameters['selection'] = (isset($parameters['selection'])     !== false ? $parameters['selection'] : false);
                $parameters['selection'] = (is_array($parameters['selection'])  !== false ? $parameters['selection'] : false);
                /* CONTENT:: gallery, playlist, audio, embed */
                return $result;
            }
            return false;
        }
        public function get($parameters, $do_processing = true){
            global $wpdb;
            $result             = false;
            $Common             = new Common();
            if ($this->validate($parameters) === true && $Common->validate($parameters) === true){
                if (is_array($parameters['targets_array']) === true){
                    if (count($parameters['targets_array']) > 0){
                        $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                        $current    = $WordPress->get_current_user(true, false);
                        $WordPress  = NULL;
                        $notMemberIDs  = array();
                        $isMemberIDs  = array();
                        foreach($parameters['targets_array'] as $group_id){
                            if ($current !== false){
                                if (in_array((int)$group_id, $current->memberships) !== false){
                                    $isMemberIDs[] = (int)$group_id;
                                }else{
                                    $notMemberIDs[] = (int)$group_id;
                                }
                            }else{
                                $notMemberIDs[] = (int)$group_id;
                            }
                        }
                        $notMemberSelector  = '';
                        $isMemberSelector   = '';
                        if (count($notMemberIDs) > 0){
                            $notMemberSelector =    'ID IN ('.
                                                        'SELECT post_id FROM wp_pure_post_visibility '.
                                                            'WHERE '.
                                                                'association = "group" '.'AND '.
                                                                'closed = 0 '.          'AND '.
                                                                'object_id IN ('.implode(',', $notMemberIDs).'))';
                        }
                        if (count($isMemberIDs) > 0){
                            $isMemberSelector =     'ID IN ('.
                                                        'SELECT post_id FROM wp_pure_post_visibility '.
                                                            'WHERE '.
                                                                'association = "group" '.'AND '.
                                                                'object_id IN ('.implode(',', $isMemberIDs).'))';
                        }
                        if ($notMemberSelector !== '' && $isMemberSelector !== ''){
                            $visibilitySelector = '('.$notMemberSelector.' OR '.$isMemberSelector.')';
                        }else{
                            $visibilitySelector = $notMemberSelector.$isMemberSelector;
                        }
                        \Pure\Components\Tools\SQLConditions\Initialization::instance()->attach(true);
                        $SQLConditions          = new \Pure\Components\Tools\SQLConditions\Conditions();
                        $where                  = $SQLConditions->WHERE('post_date', $parameters['from_date'], $parameters['days']);
                        $thumbnails_selector    = 'AND ID IN (SELECT post_id FROM wp_postmeta WHERE meta_key="_thumbnail_id") ';
                        $selector               =   'SELECT * FROM wp_posts '.
                                                        'WHERE '.
                                                            'post_status="'.$parameters['post_status'].'" '.                    'AND '.
                                                            $Common->get_post_type($parameters['post_type']).' '.               'AND '.
                                                            $where.' '.
                                                            ($parameters['thumbnails'] === true ? $thumbnails_selector : '').   'AND '.
                                                            $visibilitySelector.' '.
                                                        'ORDER BY post_date DESC';
                        if ($parameters['selection'] !== false){
                            $selector =     'SELECT '.
                                                '* '.
                                            'FROM '.
                                                '('.$selector.') AS t_posts '.
                                            'WHERE '.
                                                $Common->get_selection_selector('t_posts', $parameters['selection']).' '.
                                            'ORDER BY '.
                                                't_posts.post_date DESC';
                        }
                        $selector               = $Common->apply_sandbox_setting($parameters, $selector);
                        $_posts                 = $wpdb->get_results(   $selector);
                        $posts                  = $wpdb->get_results(   $selector.
                                                                        ' LIMIT '.$parameters['shown'].','.$parameters['maxcount'] );
                        if ($do_processing === false){
                            $result = $posts;
                        }else {
                            $result = $Common->processing($posts, $parameters, count($_posts));
                        }
                        $SQLConditions          = NULL;
                    }
                }
            }
            $Common             = NULL;
            return $result;
        }
    }
}
?>