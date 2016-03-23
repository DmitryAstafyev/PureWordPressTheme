<?php
namespace Pure\Components\WordPress\PageBackground{
    class Core{
        public function generate(){
            if (is_front_page() !== false) {
                $Background = \Pure\Templates\Layout\Page\Background\Initialization::instance()->get('A');
                $Background->background($this->getCommonBackground(), true);
            }else if(\Pure\Configuration::instance()->globals->requests->BYSCHEME !== false){
                $Background = \Pure\Templates\Layout\Page\Background\Initialization::instance()->get('A');
                $Background->background($this->getCommonBackground(), true);
            } else {
                $Background = \Pure\Templates\Layout\Page\Background\Initialization::instance()->get('A');
                switch (\Pure\Configuration::instance()->globals->requests->type) {
                    case 'BUDDY':
                        if (in_array(\Pure\Configuration::instance()->globals->requests->BUDDY,
                                ['member::activities', 'member::profile', 'member::groups', 'member::friends', 'groups::group']) !== false){
                            $Background->background($this->getSocialBackground(), true);
                        }else{
                            switch (\Pure\Configuration::instance()->globals->requests->BUDDY) {
                                case 'members':
                                    $Background->background($this->getCommonBackground(), true);
                                    break;
                                case 'groups':
                                    $Background->background($this->getCommonBackground(), true);
                                    break;
                            }
                        }
                        break;
                    case 'SPECIAL':
                        switch (\Pure\Configuration::instance()->globals->requests->SPECIAL->request) {
                            case 'CREATEPOST':
                                //none
                                break;
                            case 'CREATEEVENT':
                                //none
                                break;
                            case 'EDITPOST':
                                //none
                                break;
                            case 'EDITEVENT':
                                //none
                                break;
                            case 'DRAFTS':
                                //none
                                break;
                            case 'TOP':
                                //$Background->background($this->getCommonBackground(), true);
                                break;
                            case 'STREAM':
                                $Background->background($this->getSocialBackground(), true);
                                break;
                            case 'SEARCH':
                                //$Background->background($this->getCommonBackground(), true);
                                break;
                            case 'ASEARCH':
                                //$Background->background($this->getCommonBackground(), true);
                                break;
                            case 'GROUPCONTENT':
                                $Background->background($this->getSocialBackground(), true);
                                break;
                        }
                        break;
                    case 'POST':
                        $Background->background($this->getSocialBackground(), true);
                        break;
                    case 'PAGE':
                        //none
                        break;
                    case 'AUTHOR':
                        $Background->background($this->getSocialBackground(), true);
                        break;
                    case 'CATEGORY':
                        //$Background->background($this->getCommonBackground(), true);
                        break;
                    case 'TAG':
                        //$Background->background($this->getCommonBackground(), true);
                        break;
                    case 'SEARCH':
                        //$Background->background($this->getCommonBackground(), true);
                        break;
                }
                $Background = NULL;
            }
        }
        private function getCommonBackground(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $parameters     = \Pure\Components\WordPress\Settings\Instance::instance()->settings->images->properties;
            $parameters     = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
            $background_url = '';
            if ((int)$parameters->background > 0){
                $background_url = wp_get_attachment_image_src( (int)$parameters->background, 'full', false );
                $background_url = (is_array($background_url) !== false ? $background_url[0] : '');
            }
            return $background_url;
        }
        private function getSocialBackground(){
            $settings           = false;
            \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
            if (\Pure\Configuration::instance()->globals->IDs->group_id !== false){
                $Settings       = new \Pure\Components\BuddyPress\PersonalSettings\Group();
                $settings       = $Settings->get((object)array('group_id'=>(int)\Pure\Configuration::instance()->globals->IDs->group_id));
            }else if (\Pure\Configuration::instance()->globals->IDs->user_id !== false){
                $Settings       = new \Pure\Components\BuddyPress\PersonalSettings\User();
                $settings       = $Settings->get((object)array('user_id'=>(int)\Pure\Configuration::instance()->globals->IDs->user_id));
            }
            $Settings           = NULL;
            $background_url     = '';
            if ($settings !== false) {
                if (isset($settings['background']->attachment_id) !== false){
                    $background_url = ((int)$settings['background']->attachment_id > 0 ? wp_get_attachment_image_src( (int)$settings['background']->attachment_id, 'full', false ) : '');
                    $background_url = (is_array($background_url) !== false ? $background_url[0] : '');
                }
            }
            return ($background_url !== '' ? $background_url : '');
        }
        public function get_background_url(){
            $background_url = '';
            if (is_front_page() !== false) {
                $background_url = $this->getCommonBackground();
            } else if(\Pure\Configuration::instance()->globals->requests->BYSCHEME !== false){
                $background_url = $this->getCommonBackground();
            } else {
                switch (\Pure\Configuration::instance()->globals->requests->type) {
                    case 'BUDDY':
                        if (in_array(\Pure\Configuration::instance()->globals->requests->BUDDY,
                                ['member::activities', 'member::profile', 'member::groups', 'member::friends', 'groups::group']) !== false){
                            $background_url = $this->getSocialBackground();
                        }else{
                            switch (\Pure\Configuration::instance()->globals->requests->BUDDY) {
                                case 'members':
                                    $background_url = $this->getCommonBackground();
                                    break;
                                case 'groups':
                                    $background_url = $this->getCommonBackground();
                                    break;
                            }
                        }
                        break;
                    case 'SPECIAL':
                        switch (\Pure\Configuration::instance()->globals->requests->SPECIAL->request) {
                            case 'CREATEPOST':
                                break;
                            case 'CREATEEVENT':
                                break;
                            case 'EDITPOST':
                                break;
                            case 'EDITEVENT':
                                break;
                            case 'DRAFTS':
                                break;
                            case 'TOP':
                                //$background_url = $this->getCommonBackground();
                                break;
                            case 'STREAM':
                                $background_url = $this->getSocialBackground();
                                break;
                            case 'SEARCH':
                                //$background_url = $this->getCommonBackground();
                                break;
                            case 'GROUPCONTENT':
                                $background_url = $this->getSocialBackground();
                                break;
                        }
                        break;
                    case 'POST':
                        $background_url = $this->getSocialBackground();
                        break;
                    case 'PAGE':
                        break;
                    case 'AUTHOR':
                        $background_url = $this->getSocialBackground();
                        break;
                    case 'CATEGORY':
                        //$background_url = $this->getCommonBackground();
                        break;
                    case 'TAG':
                        //$background_url = $this->getCommonBackground();
                        break;
                    case 'SEARCH':
                        //$background_url = $this->getCommonBackground();
                        break;
                }
            }
            return $background_url;
        }
    }
}
?>