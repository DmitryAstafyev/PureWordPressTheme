<?php
namespace Pure\Templates\Tag\Content{
    class A{
        private function validate(&$parameters){
            $parameters = (is_object($parameters) === true ? $parameters : new \stdClass());
        }
        private function innerHTMLLine($id){
            $innerHTML =    '<!--BEGIN: Social.Header:: Bottom line area-->'.
                            '<div data-element-type="Pure.Social.Header.A.BottomLine">'.
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
                                            '</label>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                            '<!--END: Social.Header:: Bottom line area-->';
            return $innerHTML;
        }
        private function innerHTMLBlock($tag_id, $container_id, $type){
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
            }
            $wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('B');
            $posts      = new \Pure\Plugins\Thumbnails\Posts\Builder(array(
                'content'           => 'tag',
                'targets'	        => $tag_id,
                'template'	        => 'D',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => 3,
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
                'more'              => true));
            $innerHTML  = $posts->render();
            $innerHTML  = ($innerHTML !== '' ? $wrapper->get($innerHTML, (object)array('id'=>$container_id, 'column_width'=>'28em', 'node_type'=>'article', 'space'=>'1em')) : '');
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Friends.A.Message">No posts</p>');
            $wrapper    = NULL;
            $posts      = NULL;
            return $innerHTML;
        }
        public function get($tag_id, $parameters = false){
            $innerHTML = '';
            if ((int)$tag_id > 0){
                $this->validate($parameters);
                $id             = uniqid('Tag_Home_Page');
                $innerHTML      = $this->innerHTMLLine($id);
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
                                                $this->innerHTMLBlock($tag_id, $container_id, 'all').
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
                                                $this->innerHTMLBlock($tag_id, $container_id, 'galleries').
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
                                                $this->innerHTMLBlock($tag_id, $container_id, 'media').
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
                                                        $this->innerHTMLBlock($tag_id, $container_id, 'audio').
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>';
            }
            return $innerHTML;
        }
    }
}
?>