<?php
namespace Pure\Templates\Posts\Elements\Tools{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post_id)     !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->user_id)     !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->object_type) !== false ? true : false));
                return $result;
            }
            return false;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user_id){
                        \Pure\Components\WordPress\Location\Special\Initialization::instance()->attach(true);
                        $URLs       = new \Pure\Components\WordPress\Location\Special\Register();
                        $innerHTML  =   '<a data-post-element-type="Pure.Posts.Tools.A.EditButton" '.
                                            'data-engine-lockpage-event="click" '.
                                            'data-engine-lockpage-background="rgba(255,255,255,0.8)" '.
                                            'href="'.$URLs->getURL($parameters->object_type, array('post_id'=>(int)$parameters->post_id)).'" '.
                                        '></a>';
                        $URLs = NULL;
                        \Pure\Components\LockPage\A\Initialization::instance()->attach();
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>