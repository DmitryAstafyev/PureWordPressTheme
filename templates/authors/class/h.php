<?php
namespace Pure\Templates\Authors{
    class H{
        private function validate(&$parameters){
            $content    = array('posts', 'groups', 'friends', 'all');
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute              = (isset($parameters->attribute             ) === true  ? $parameters->attribute            : new \stdClass()   );
            $parameters->attribute->name        = (isset($parameters->attribute->name       ) === true  ? $parameters->attribute->name      : ''                );
            $parameters->attribute->value       = (isset($parameters->attribute->value      ) === true  ? $parameters->attribute->value     : ''                );
            $parameters->templates_settings     = (isset($parameters->templates_settings    ) === true  ? $parameters->templates_settings   : array()           );
            $parameters->templates_settings     = (is_array($parameters->templates_settings ) === true  ? $parameters->templates_settings   : array()           );
            $parameters->templates_settings['addition_information'] = (isset($parameters->templates_settings['addition_information'] ) === true ? $parameters->templates_settings['addition_information']  : $content[0] );
            $parameters->templates_settings['addition_information'] = (in_array($parameters->templates_settings['addition_information'] , $content) === true ? $parameters->templates_settings['addition_information']  : $content[0] );
        }
        private function getHistory($data, $style = ''){
            return  '<p data-element-type="Pure.Social.Friends.A.Message.Normal" '.$style.'>'.__( 'With us from', 'pure' ).' '.$data->author->date.' ('.$data->author->how_long.')</p>'.
                    ($data->friendship->created !== false ? '<p data-element-type="Pure.Social.Friends.A.Message.Normal" '.$style.'>'.($data->friendship->accepted === true ? __( 'Friendship', 'pure' ) : __( 'Request', 'pure' )).' '.__( 'from', 'pure' ).' '.$data->friendship->created.' ('.$data->friendship->how_long.')</p>' : '').
                    '<p data-element-type="Pure.Social.Friends.A.Message.Normal" '.$style.'>'.__( 'Created', 'pure' ).': '.$data->posts->count.' '.__( 'posts', 'pure' ).' '.__( 'and', 'pure' ).' '.$data->comments->count.' '.__( 'comments', 'pure' ).'</p>'.
                    '<p data-element-type="Pure.Social.Friends.A.Message.Normal" '.$style.'>'.__( 'Has', 'pure' ).' '.$data->author->friends.' '.__( 'friends and member of', 'pure' ).' '.$data->author->groups.' '.__( 'groups', 'pure' ).'</p>';
        }
        private function innerHTMLSummary($data, $parameters, $current_user){
            $innerHTML =    '<p data-element-type="Pure.Social.Friends.A.Title">'.__( 'History', 'pure' ).'</p>'.
                            $this->getHistory($data);
            /*
            echo var_dump($data->author->login);
            echo '<br/>';
            echo var_dump($data->friendship);*/
            if ($current_user !== false){
                if ((int)$current_user->ID !== (int)$data->author->id){
                    $innerHTML .=   '<p data-element-type="Pure.Social.Friends.A.Title">'.__( 'Friendship status', 'pure' ).'</p>';
                    if ($data->friendship->created !== false){
                        if ($data->friendship->accepted === true){
                            $innerHTML .=   '<table data-element-type="Pure.Social.Friends.A.Content.Table" border="0">'.
                                                '<tr data-element-type="Pure.Social.Friends.A.Content.Row">'.
                                                    '<td data-element-type="Pure.Social.Friends.A.Content.Column"><a data-element-type="Pure.Social.Friends.A.Content.Button" data-engine-friendID="'.$data->author->id.'" data-engine-friendship-action="remove" data-type-addition="Basic">'.__( 'Remove', 'pure' ).'</a></td>'.
                                                    '<td data-element-type="Pure.Social.Friends.A.Content.Column"><p data-element-type="Pure.Social.Friends.A.Message.Normal">'.__( 'You can cancel your friendship with this user. To do it, just press "Remove". In this case this user will be removed from your friend\'s list.', 'pure' ).'</p></td>'.
                                                '</tr>'.
                                            '</table>';
                        }else{
                            if ($data->friendship->is_initiator !== false){
                                $innerHTML .=   '<table data-element-type="Pure.Social.Friends.A.Content.Table" border="0">'.
                                                    '<tr data-element-type="Pure.Social.Friends.A.Content.Row">'.
                                                        '<td data-element-type="Pure.Social.Friends.A.Content.Column"><a data-element-type="Pure.Social.Friends.A.Content.Button" data-engine-friendID="'.$data->author->id.'" data-engine-friendship-action="request" data-type-addition="Basic">'.__( 'Accept friendship', 'pure' ).'</a></td>'.
                                                        '<td data-element-type="Pure.Social.Friends.A.Content.Column"><p data-element-type="Pure.Social.Friends.A.Message.Normal">'.__( 'You and this user are not friends. But this user requested friendship with you. You can accept it and you with this user will be a friends. Or you can deny it. It is your choose.', 'pure' ).'</p></td>'.
                                                    '</tr>'.
                                                '</table>';
                            }else{
                                $innerHTML .=   '<table data-element-type="Pure.Social.Friends.A.Content.Table" border="0">'.
                                                    '<tr data-element-type="Pure.Social.Friends.A.Content.Row">'.
                                                        '<td data-element-type="Pure.Social.Friends.A.Content.Column"><a data-element-type="Pure.Social.Friends.A.Content.Button" data-engine-friendID="'.$data->author->id.'" data-type-addition="Basic">'.__( 'Cancel request', 'pure' ).'</a></td>'.
                                                        '<td data-element-type="Pure.Social.Friends.A.Content.Column"><p data-element-type="Pure.Social.Friends.A.Message.Normal">'.__( 'You and this user are not friends. But you requested friendship with him. Now you can wait for an admission from this user or you can cancel your request.', 'pure' ).'</p></td>'.
                                                    '</tr>'.
                                                '</table>';
                            }
                        }
                    }else{
                        $innerHTML .=   '<table data-element-type="Pure.Social.Friends.A.Content.Table" border="0">'.
                                            '<tr data-element-type="Pure.Social.Friends.A.Content.Row">'.
                                                '<td data-element-type="Pure.Social.Friends.A.Content.Column"><a data-element-type="Pure.Social.Friends.A.Content.Button" data-engine-friendID="'.$data->author->id.'" data-type-addition="Basic">'.__( 'Add', 'pure' ).'</a></td>'.
                                                '<td data-element-type="Pure.Social.Friends.A.Content.Column"><p data-element-type="Pure.Social.Friends.A.Message.Normal">'.__( 'You and this user are not friends. You can request a friendship with this user. After you should wait for an admission from this user.', 'pure' ).'</p></td>'.
                                            '</tr>'.
                                        '</table>';
                    }
                }
            }
            $Profile = \Pure\Templates\BuddyPress\Profile\Initialization::instance()->get('A');
            $profile = $Profile->get((int)$data->author->id);
            $Profile = NULL;
            if ($profile !== ''){
                $innerHTML .=   '<p data-element-type="Pure.Social.Friends.A.Title">'.__( 'Profile', 'pure' ).'</p>';
                $innerHTML .=   $profile;
            }
            return $innerHTML;
        }
        private function innerHTMLPosts($data, $parameters){
            $wrapper    = \Pure\Templates\Positioning\Initialization::instance()->get('B');
            $posts      = new \Pure\Plugins\Thumbnails\Posts\Builder(array(
                'content'           => 'author',
                'targets'	        => $data->author->id,
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
                'slider_template'	=> 'A',
                'tab_template'	    => 'A',
                'presentation'	    => 'clear',
                'tabs_columns'	    => 1,
                'more'              => true));
            $innerHTML  = $posts->render();
            $innerHTML  = ($innerHTML !== '' ? $wrapper->get($innerHTML, (object)array('column_width'=>'28em', 'node_type'=>'article', 'space'=>'1em')) : '');
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Friends.A.Message">No posts</p>');
            $wrapper    = NULL;
            $posts      = NULL;
            return $innerHTML;
        }
        private function innerHTMLFriends($data, $parameters){
            $friends    = new \Pure\Plugins\Thumbnails\Authors\Builder(array(
                'content'           => 'friends_of_user',
                'targets'	        => $data->author->id,
                'template'	        => 'F',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => 10,
                'only_with_avatar'	=> false,
                'top'	            => false,
                'profile'	        => '',
                'days'	            => 3650,
                'from_date'         => '',
                'more'              => true));
            $innerHTML  = $friends->render();
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Friends.A.Message">No friends</p>');
            $friends    = NULL;
            return $innerHTML;
        }
        private function innerHTMLGroups($data, $parameters){
            $groups     = new \Pure\Plugins\Thumbnails\Groups\Builder(array(
                'content'           => 'users',
                'targets'	        => $data->author->id,
                'template'	        => 'H',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => 10,
                'only_with_avatar'	=> false,
                'top'	            => false,
                'profile'	        => '',
                'days'	            => 3650,
                'from_date'         => '',
                'show_content'      => false,
                'show_admin_part'   => false,
                'show_life'         => false,
                'more'              => true));
            $innerHTML  = $groups->render();
            $innerHTML  = ($innerHTML !== '' ? $innerHTML : '<p data-element-type="Pure.Social.Friends.A.Message">No groups</p>');
            $groups     = NULL;
            return $innerHTML;
        }
        private function getInfo($data){
            $innerHTML =    '<a data-element-type="Pure.Social.Friends.A.Statistic"><span data-statistic-type="count">'.$data->posts->count.'</span><br /><span data-statistic-type="type">'.__( 'posts', 'pure' ).'</span></a>'.
                            '<a data-element-type="Pure.Social.Friends.A.Statistic"><span data-statistic-type="count">'.$data->comments->count.'</span><br /><span data-statistic-type="type">'.__( 'comments', 'pure' ).'</span></a>'.
                            '<a data-element-type="Pure.Social.Friends.A.Statistic"><span data-statistic-type="count">'.$data->author->friends.'</span><br /><span data-statistic-type="type">'.__( 'friends', 'pure' ).'</span></a>'.
                            '<a data-element-type="Pure.Social.Friends.A.Statistic"><span data-statistic-type="count">'.$data->author->groups.'</span><br /><span data-statistic-type="type">'.__( 'groups', 'pure' ).'</span></a>';
            return $innerHTML;
        }
        public function top($data, $parameters = NULL){
            return $this->simple($data, $parameters);
        }
        public function simple($data, $parameters = NULL){
            $this->validate($parameters);
            $this->resources();
            $WordPress      = new \Pure\Components\WordPress\UserData\Data();
            $current_user   = $WordPress->get_current_user();
            $id             = uniqid('Friends_Template_H');
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML      =   '<!--BEGIN: Member item -->'.
                                '<div data-element-type="Pure.Social.Friends.A.Container" data-engine-element="dialog_parent" '.$attribute_str.'>'.
                                    '<input data-element-type="Pure.Social.Friends.A.Switchers.Inputs" type="checkbox" id="'.$id.'" />'.
                                    '<div data-element-type="Pure.Social.Friends.A.Avatar" style="background-image:url('.$data->author->avatar.')">'.
                                    '</div>'.
                                    '<div data-element-type="Pure.Social.Friends.A.NameLine">'.
                                        '<div data-element-type="Pure.Social.Friends.A.NameLine.Container">'.
                                            '<div data-element-type="Pure.Social.Friends.A.NameLine.Cell.Name">'.
                                                '<a data-element-type="Pure.Social.Friends.A.Name">'.$data->author->name.'</a>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Friends.A.NameLine.Cell.Space">'.
                                            '</div>';
            if ($current_user !== false){
                if ((int)$current_user->ID !== (int)$data->author->id){
                    $innerHTML      .=      '<div data-element-type="Pure.Social.Friends.A.NameLine.Cell.Item" data-order-type="details">'.
                                                '<a data-element-type="Pure.Social.Friends.A.Button" data-button-type="message"'.
                                                    'data-messenger-engine-button="open" '.
                                                    'data-messenger-engine-switchTo="mails" '.
                                                    'data-messenger-engine-recipient-id="'.$data->author->id.'" '.
                                                    'data-messenger-engine-recipient-avatar="'.$data->author->avatar.'" '.
                                                    'data-messenger-engine-recipient-name="'.$data->author->name.'" '.
                                                '></a>'.
                                            '</div>';
                }
            }
            $innerHTML      .=              '<div data-element-type="Pure.Social.Friends.A.NameLine.Cell.Item" data-order-type="details">'.
                                                '<a data-element-type="Pure.Social.Friends.A.Button" data-button-type="view" href="'.$data->author->urls->member.'"></a>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Friends.A.NameLine.Cell.Item">'.
                                                '<label for="'.$id.'">'.
                                                    '<a data-element-type="Pure.Social.Friends.A.Button" data-button-type="more"></a>'.
                                                '</label>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<div data-element-type="Pure.Social.Friends.A.Statistic">'.
                                        $this->getInfo($data).
                                    '</div>'.
                                    '<div data-element-type="Pure.Social.Friends.A.Tabs.Container">'.
                                        '<div data-element-type="Pure.Social.Friends.A.Tabs.SubContainer">'.
                                            '<div data-element-type="Pure.Social.Friends.A.Tabs">';
            $labels = (object)array(
                'summary'=>                 '<label for="'.$id.'Summary">'.
                                                '<a data-element-type="Pure.Social.Friends.A.Tab">'.__( 'Summary', 'pure' ).'</a>'.
                                            '</label>',
                'posts'=>                   '<label for="'.$id.'Posts">'.
                                                '<a data-element-type="Pure.Social.Friends.A.Tab">'.__( 'Posts', 'pure' ).'</a>'.
                                            '</label>',
                'friends'=>                 '<label for="'.$id.'Friends">'.
                                                '<a data-element-type="Pure.Social.Friends.A.Tab">'.__( 'Friends', 'pure' ).'</a>'.
                                            '</label>',
                'groups'=>                  '<label for="'.$id.'Groups">'.
                                                '<a data-element-type="Pure.Social.Friends.A.Tab">'.__( 'Groups', 'pure' ).'</a>'.
                                            '</label>',
            );
            switch($parameters->templates_settings['addition_information']){
                case 'friends':
                    $innerHTML .= $labels->summary.$labels->friends;
                    break;
                case 'groups':
                    $innerHTML .= $labels->summary.$labels->groups;
                    break;
                case 'posts':
                    $innerHTML .= $labels->summary.$labels->posts;
                    break;
                case 'all':
                    $innerHTML .= $labels->summary.$labels->posts.$labels->friends.$labels->groups;
                    break;
            }
            $innerHTML .=                   '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<div data-element-type="Pure.Social.Friends.A.TabsContent.Container">';
            $content = (object)array(
                'summary'=>             '<input data-element-type="Pure.Social.Friends.A.Tabs.Switchers.Inputs" id="'.$id.'Summary" name="'.$id.'TabsCollection" type="radio" checked />'.
                                        '<div data-element-type="Pure.Social.Friends.A.TabContent" data-tab-type="Summary">'.
                                            '<div data-element-type="Pure.Social.Friends.A.TabContent.Title">'.
                                                '<div data-element-type="Pure.Social.Friends.A.TabContent.Title.Container">'.
                                                    '<p data-element-type="Pure.Social.Friends.A.TabContent.Title">'.__( 'Summary', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Friends.A.TabContent.Tab">'.
                                                $this->innerHTMLSummary($data, $parameters, $current_user).
                                            '</div>'.
                                        '</div>',
                'posts'=>               '<input data-element-type="Pure.Social.Friends.A.Tabs.Switchers.Inputs" data-engine-type="Switcher" data-type-addition="Posts" id="'.$id.'Posts" name="'.$id.'TabsCollection" type="radio" data-engine-container-id="'.$id.'Posts" />'.
                                        '<div data-element-type="Pure.Social.Friends.A.TabContent" data-tab-type="Posts" data-engine-container-id="'.$id.'Posts">'.
                                            '<div data-element-type="Pure.Social.Friends.A.TabContent.Title">'.
                                                '<div data-element-type="Pure.Social.Friends.A.TabContent.Title.Container">'.
                                                    '<p data-element-type="Pure.Social.Friends.A.TabContent.Title">'.__( 'Posts', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Friends.A.TabContent.Tab">'.
                                                $this->innerHTMLPosts($data, $parameters).
                                            '</div>'.
                                        '</div>',
                'friends'=>             '<input data-element-type="Pure.Social.Friends.A.Tabs.Switchers.Inputs" id="'.$id.'Friends" name="'.$id.'TabsCollection" type="radio" />'.
                                        '<div data-element-type="Pure.Social.Friends.A.TabContent" data-tab-type="Friends">'.
                                            '<div data-element-type="Pure.Social.Friends.A.TabContent.Title">'.
                                                '<div data-element-type="Pure.Social.Friends.A.TabContent.Title.Container">'.
                                                    '<p data-element-type="Pure.Social.Friends.A.TabContent.Title">'.__( 'Friends', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Friends.A.TabContent.Tab">'.
                                                '<div data-element-type="Pure.Social.Friends.A.TabContent.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Friends.A.TabContent.Tab.Center">'.
                                                        $this->innerHTMLFriends($data, $parameters).
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>',
                'groups'=>              '<input data-element-type="Pure.Social.Friends.A.Tabs.Switchers.Inputs" id="'.$id.'Groups" name="'.$id.'TabsCollection" type="radio" />'.
                                        '<div data-element-type="Pure.Social.Friends.A.TabContent" data-tab-type="Groups">'.
                                            '<div data-element-type="Pure.Social.Friends.A.TabContent.Title">'.
                                                '<div data-element-type="Pure.Social.Friends.A.TabContent.Title.Container">'.
                                                    '<p data-element-type="Pure.Social.Friends.A.TabContent.Title">'.__( 'Groups', 'pure' ).'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<div data-element-type="Pure.Social.Friends.A.TabContent.Tab">'.
                                                '<div data-element-type="Pure.Social.Friends.A.TabContent.Tab.Wrapper">'.
                                                    '<div data-element-type="Pure.Social.Friends.A.TabContent.Tab.Center">'.
                                                        $this->innerHTMLGroups($data, $parameters).
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>',
            );
            switch($parameters->templates_settings['addition_information']){
                case 'friends':
                    $innerHTML .= $content->summary.$content->friends;
                    break;
                case 'groups':
                    $innerHTML .= $content->summary.$content->groups;
                    break;
                case 'posts':
                    $innerHTML .= $content->summary.$content->posts;
                    break;
                case 'all':
                    $innerHTML .= $content->summary.$content->posts.$content->friends.$content->groups;
                    break;
            }
            $innerHTML .=           '</div>'.
                                '</div>'.
                                '<!--END: Member item -->';
            $WordPress  = NULL;
            return $innerHTML;
        }
        private function resources(){
            if (isset(\Pure\Configuration::instance()->globals->PureRequestsAuthorsTemplateHResources) === false){
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addINIT(
                    'pure.authors.H',
                    false,
                    true
                );
                \Pure\Components\Dialogs\A\Initialization::instance()->attach(false, 'after');
                \Pure\Components\Dialogs\B\Initialization::instance()->attach(false, 'after');
                \Pure\Templates\ProgressBar\Initialization::instance()->get('B');
                \Pure\Configuration::instance()->globals->PureRequestsAuthorsTemplateHResources = true;
            }
        }
        private function resources_more(){
            \Pure\Resources\Compressor::instance()->CSS(
                \Pure\Templates\Authors\Initialization::instance()->configuration->paths->css.'/'.'H.more.css');
            \Pure\Templates\ProgressBar\Initialization::instance()->get('D');
        }
        public function more($parameters){
            $this->resources_more($parameters);
            $innerHTML =    '<div data-type-element="Author.Thumbnail.H.More" '.
                                    'data-type-more-group="'.   $parameters['group'].'" '.
                                    'data-type-more-max="'.     $parameters['maxcount'].'" '.
                                    'data-type-more-template="'.$parameters['template'].'" '.
                                    'data-type-more-progress="D" '.
                                    'data-type-more-settings="'.$parameters['more_settings'].'" '.
                                    'data-type-use="Pure.Components.More">'.
                                '<p data-type-element="Author.Thumbnail.H.More">more</p>'.
                            '</div>'.
                            '<p data-element-type="Author.Thumbnail.H.More.Info">'.
                                '<span data-element-type="Author.Thumbnail.H.More.Info" data-type-use="Pure.Components.More.Shown" data-type-more-group="'.$parameters['group'].'">'.$parameters['shown'].'</span> / '.
                                '<span data-element-type="Author.Thumbnail.H.More.Info">'.$parameters['total'].'</span>'.
                            '</p>'.
                            '<div data-type-element="Author.Thumbnail.H.Reset"></div>';
            return $innerHTML;
        }
    }
}
?>