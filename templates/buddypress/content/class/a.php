<?php
namespace Pure\Templates\BuddyPress\Content{
    class A{
        private $settings = false;
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->counts->properties;
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $this->settings = $settings;
        }
        private function validate(&$parameters){
            $parameters             = (is_object($parameters) === true ? $parameters : new \stdClass());
            $parameters->avatar_id  = (isset($parameters->avatar_id ) === true ? (gettype($parameters->avatar_id) === 'string'  ? $parameters->avatar_id    : false ) : false);
        }
        private function innerHTMLLine($id, $user_id, $current){
            $innerHTML =    '<!--BEGIN: Social.Header:: Bottom line area-->'.
                            '<div data-element-type="Pure.Social.Header.A.BottomLine">'.
                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                    '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                        '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Content', 'pure' ).'</p>'.
                                    '</div>'.
                                '</div>'.
                                '<div data-element-type="Pure.Social.Header.A.LineItems.Container">'.
                                    '<div data-element-type="Pure.Social.Header.A.LineItems.SubContainer">'.
                                        '<div data-element-type="Pure.Social.Header.A.LineItems">'.
                                            '<label for="'.$id.'_All">'.
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">'.__( 'All', 'pure' ).'</a>'.
                                            '</label>'.
                                            '<label for="'.$id.'_Galleries">'.
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">'.__( 'Galleries', 'pure' ).'</a>'.
                                            '</label>'.
                                            '<label for="'.$id.'_Media">'.
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">'.__( 'Media', 'pure' ).'</a>'.
                                            '</label>'.
                                            '<label for="'.$id.'_Audio">'.
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">'.__( 'Audio', 'pure' ).'</a>'.
                                            '</label>';
            if ($current !== false){
                if ((int)$current->ID === (int)$user_id) {
                    $innerHTML .=           '<label for="' . $id . '_Drafts">' .
                                                '<a data-element-type="Pure.Social.Header.A.LineItems.Item">' . __('Drafts', 'pure') . '</a>' .
                                            '</label>';
                }
            }
            $innerHTML .=               '</div>'.
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                            '<!--END: Social.Header:: Bottom line area-->';
            return $innerHTML;
        }
        private function innerHTMLBlock($user_id, $container_id, $type){
            switch($type){
                case 'all':
                    $selection = false;
                    break;
                case 'galleries':
                    $selection = array('gallery');
                    break;
                case 'audio':
                    $selection = array('playlist', 'audio');
                    break;
                case 'media':
                    $selection = array('embed');
                    break;
                case 'drafts':
                    $selection = false;
                    break;
            }
            $wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('B');
            $posts      = new \Pure\Plugins\Thumbnails\Posts\Builder(array(
                'content'           => 'author',
                'targets'	        => $user_id,
                'template'	        => 'D',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => $this->settings->posts,
                'only_with_avatar'	=> false,
                'profile'	        => '',
                'days'	            => 3650,
                'from_date'         => '',
                'hidetitle'	        => true,
                'thumbnails'	    => false,
                'slider_template'	=> '',
                'tab_template'	    => '',
                'presentation'	    => 'clear',
                'tabs_columns'	    => 1,
                'selection'         =>$selection,
                'post_status'       =>($type !== 'drafts' ? '' : 'draft'),
                'more'              => true));
            $innerHTML  = $posts->render();
            $innerHTML  = ($innerHTML !== '' ? $wrapper->get($innerHTML, (object)array('id'=>$container_id, 'column_width'=>'28em', 'node_type'=>'article', 'space'=>'1em')) : '');
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Friends.A.Message">No posts</p>');
            $wrapper    = NULL;
            $posts      = NULL;
            return $innerHTML;
        }
        public function get($user_id, $parameters = false){
            $innerHTML = '';
            if (gettype($user_id) === 'integer'){
                $this->validate($parameters);
                $this->getSettings();
                $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                $current        = $WordPress->get_current_user();
                $WordPress      = NULL;
                $id             = uniqid('Personal_Home_Page');
                $innerHTML      = $this->innerHTMLLine($id, $user_id, $current);
                $innerHTML      .=  '<div data-element-type="Pure.Social.Home.A.TabsContent.Container">';
                $container_id   = uniqid('all');
                $innerHTML      .=      '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_All" name="'.$id.'TabsCollection" type="radio" checked '.
                                            'data-engine-positioning-B-caller="change" '.
                                            'data-engine-positioning-B-id="'.$container_id.'" '.
                                            'data-engine-positioning-B-property-name="checked" '.
                                            'data-engine-positioning-B-property-type="boolean" '.
                                            'data-engine-positioning-B-property-value="true" '.
                                            'data-engine-positioning-B-redraw-selector="*[data-element-type=|Pure.Social.Home.A.TabContent|][data-engine-tab-container-id=|'.$container_id.'|]" '.
                                        ' />'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="All" data-engine-tab-container-id="'.$container_id.'">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'All', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                $this->innerHTMLBlock($user_id, $container_id, 'all').
                                            '</div>'.
                                        '</div>';
                $container_id   = uniqid('galleries');
                $innerHTML      .=      '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_Galleries" name="'.$id.'TabsCollection" type="radio" '.
                                            'data-engine-positioning-B-caller="change" '.
                                            'data-engine-positioning-B-id="'.$container_id.'" '.
                                            'data-engine-positioning-B-property-name="checked" '.
                                            'data-engine-positioning-B-property-type="boolean" '.
                                            'data-engine-positioning-B-property-value="true" '.
                                            'data-engine-positioning-B-redraw-selector="*[data-element-type=|Pure.Social.Home.A.TabContent|][data-engine-tab-container-id=|'.$container_id.'|]" '.
                                        '/>'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="Galleries" data-engine-tab-container-id="'.$container_id.'">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Galleries', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                $this->innerHTMLBlock($user_id, $container_id, 'galleries').
                                            '</div>'.
                                        '</div>';
                $container_id   = uniqid('media');
                $innerHTML      .=      '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_Media" name="'.$id.'TabsCollection" type="radio" '.
                                            'data-engine-positioning-B-caller="change" '.
                                            'data-engine-positioning-B-id="'.$container_id.'" '.
                                            'data-engine-positioning-B-property-name="checked" '.
                                            'data-engine-positioning-B-property-type="boolean" '.
                                            'data-engine-positioning-B-property-value="true" '.
                                            'data-engine-positioning-B-redraw-selector="*[data-element-type=|Pure.Social.Home.A.TabContent|][data-engine-tab-container-id=|'.$container_id.'|]" '.
                                        '/>'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="Media" data-engine-tab-container-id="'.$container_id.'">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Media', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                $this->innerHTMLBlock($user_id, $container_id, 'media').
                                            '</div>'.
                                        '</div>';
                $container_id   = uniqid('audio');
                $innerHTML      .=      '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_Audio" name="'.$id.'TabsCollection" type="radio" '.
                                            'data-engine-positioning-B-caller="change" '.
                                            'data-engine-positioning-B-id="'.$container_id.'" '.
                                            'data-engine-positioning-B-property-name="checked" '.
                                            'data-engine-positioning-B-property-type="boolean" '.
                                            'data-engine-positioning-B-property-value="true" '.
                                            'data-engine-positioning-B-redraw-selector="*[data-element-type=|Pure.Social.Home.A.TabContent|][data-engine-tab-container-id=|'.$container_id.'|]" '.
                                        '/>'.
                                        '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="Audio" data-engine-tab-container-id="'.$container_id.'">'.
                                            '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                    '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Audio', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                '<div data-element-type="Pure.Social.Home.A.TabContent.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Home.A.TabContent.Tab.Center">'.
                                                        $this->innerHTMLBlock($user_id, $container_id, 'audio').
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>';
                if ($current !== false){
                    if ((int)$current->ID === (int)$user_id){
                        $container_id   = uniqid('drafts');
                        $innerHTML      .=  '<input data-element-type="Pure.Social.Home.A.Tabs.Switchers.Inputs" id="'.$id.'_Drafts" name="'.$id.'TabsCollection" type="radio" '.
                                                'data-engine-positioning-B-caller="change" '.
                                                'data-engine-positioning-B-id="'.$container_id.'" '.
                                                'data-engine-positioning-B-property-name="checked" '.
                                                'data-engine-positioning-B-property-type="boolean" '.
                                                'data-engine-positioning-B-property-value="true" '.
                                                'data-engine-positioning-B-redraw-selector="*[data-element-type=|Pure.Social.Home.A.TabContent|][data-engine-tab-container-id=|'.$container_id.'|]" '.
                                            '/>'.
                                            '<div data-element-type="Pure.Social.Home.A.TabContent" data-tab-type="Media" data-engine-tab-container-id="'.$container_id.'">'.
                                                '<div data-element-type="Pure.Social.Header.A.TopLine.Top">'.
                                                    '<div data-element-type="Pure.Social.Header.A.TopLine.Top.Container">'.
                                                        '<p data-element-type="Pure.Social.Header.A.TopLine.Top">'.__( 'Drafts (not published)', 'pure' ).'</p>'.
                                                    '</div>'.
                                                '</div>'.
                                                '<div data-element-type="Pure.Social.Home.A.TabContent.Tab">'.
                                                    $this->innerHTMLBlock($user_id, $container_id, 'drafts').
                                                '</div>'.
                                            '</div>';
                    }
                }
                $innerHTML      .= '</div>';
            }
            return $innerHTML;
        }
    }
}
?>