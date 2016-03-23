<?php
namespace Pure\Templates\BuddyPress\Activities{
    class A{
        private $user;
        private $settings;
        private $ManaTemplate;
        private $manaCache = false;
        private $permissions;
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $parameters->user_id    = (isset($parameters->user_id      ) !== false ? $parameters->user_id   : false);
                $parameters->group_id   = (isset($parameters->group_id     ) !== false ? $parameters->group_id  : false);
                //Or group_id OR user_id should be defined, not both
                if (($parameters->group_id === false && $parameters->user_id === false) ||
                    ($parameters->group_id !== false && $parameters->user_id !== false)){
                    return false;
                }
                $parameters->object_id      = ($parameters->user_id !== false ? $parameters->user_id    : $parameters->group_id );
                $parameters->object_type    = ($parameters->user_id !== false ? 'activity'              : 'groups'              );
                return true;
            }
            return false;
        }
        private function getManaPermissions(){
            if ($this->user !== false){
                \Pure\Components\Relationships\Mana\Initialization::instance()->attach();
                $Mana                   = new \Pure\Components\Relationships\Mana\Provider();
                $this->user->mana       = $Mana->getUserPermissions($this->user->ID);
                $Mana                   = NULL;
            }
        }
        private function getData($parameters){
            $Activities = \Pure\Providers\Activities\Initialization::instance()->get(
                ($parameters->object_type === 'activity' ? 'of_user' : 'of_group')
            );
            $activities = $Activities->get(
                array(
                    'shown'         =>0,
                    'maxcount'      =>$this->settings->show_on_page,
                    'targets_array' =>array($parameters->object_id)
                )
            );
            $Activities = NULL;
            return $activities;
        }
        private function getEditPermissions($parameters){
            $this->permissions = (object)array(
                'root'              =>false,
                'comment'           =>false,
                'remove_activity'   =>false,
                'remove_comment'    =>false,
            );
            if ($this->user !== false){
                switch($parameters->object_type){
                    case 'activity':
                        $this->permissions->root            = ((int)$this->user->ID === (int)$parameters->object_id ? true : false);
                        $this->permissions->comment         = true;
                        $this->permissions->remove_activity = ($this->permissions->root !== false ? ($this->settings->allow_remove_activities   === 'yes' ? true : false) : false);
                        $this->permissions->remove_comment  = ($this->permissions->root !== false ? ($this->settings->allow_remove_comments     === 'yes' ? true : false) : false);
                        break;
                    case 'groups':
                        $this->permissions->root            = (array_search((int)$parameters->object_id, $this->user->memberships) !== false ? true : false);
                        $this->permissions->comment         = true;
                        $this->permissions->remove_activity = ($this->permissions->root !== false ? ($this->settings->allow_remove_activities   === 'yes' ? true : false) : false);
                        $this->permissions->remove_comment  = ($this->permissions->root !== false ? ($this->settings->allow_remove_comments     === 'yes' ? true : false) : false);
                        break;
                }
            }
        }
        private function getQuote($_quote){
            $author = '';
            $date   = '';
            $quote  = $_quote;
            $quote  = preg_replace('/\[quote:begin\]/i',    '', $quote);
            $quote  = preg_replace('/\[quote:end\]/i',      '', $quote);
            preg_match('/\[author:begin\](.*)\[author:end\]/i', $_quote, $matches);
            if (is_array($matches) !== false) {
                if (count($matches) === 2) {
                    $author = $matches[1];
                    $quote  = preg_replace('/\[author:begin\](.*)\[author:end\]/i', '', $quote);
                }
            }
            preg_match('/\[date:begin\](.*)\[date:end\]/', $_quote, $matches);
            if (is_array($matches) !== false) {
                if (count($matches) === 2) {
                    $date   = $matches[1];
                    $quote  = preg_replace('/\[date:begin\](.*)\[date:end\]/i', '', $quote);
                }
            }
            if ($quote !== '' && $author !== '' && $date !== ''){
                return  '<div data-post-element-type="Pure.BuddyPress.Activities.A.Quote">'.
                            '<p data-post-element-type="Pure.BuddyPress.Activities.A.Quote">'.nl2br(esc_html($quote)).'</p>'.
                            '<p data-post-element-type="Pure.BuddyPress.Activities.A.Quote.Author">'.$author.', '.
                                '<span data-post-element-type="Pure.BuddyPress.Activities.A.Quote.Date">'.$date.'</span>'.
                            '</p>'.
                        '</div>';
            }
            return false;
        }
        private function getActivityAction($activityValue, $activity_id){
            $value = preg_replace('/[\n\r\s]*$/', '', $activityValue);
            $value = preg_replace('/^[\n\r\s]*/', '', $value        );
            return '<p data-post-element-type="Pure.BuddyPress.Activities.A.Activity" data-engine-activity-activityID="'.$activity_id.'" data-engine-activity-element="Activity.Value">'.nl2br($value).'</p>';
        }
        private function getActivityContent($activityValue, $activity_id){
            $clear = preg_replace('/<(p)[^>]*?(\/?)>.*<(\/p)>|<(a)[^>]*?(\/?)>.*<(\/a)>|<(li)[^>]*?(\/?)>.*<(\/li)>|<(ul)[^>]*?(\/?)>.*<(\/ul)>|<(ol)[^>]*?(\/?)>.*<(\/ol)>/i', '', $activityValue);
            if (mb_strlen($clear) !== mb_strlen($activityValue)){
                $value = preg_replace('/[\n\r\s]*$/', '', $activityValue);
                $value = preg_replace('/^[\n\r\s]*/', '', $value        );
                return '<div data-post-element-type="Pure.BuddyPress.Activities.A.Activity" data-engine-activity-activityID="'.$activity_id.'" data-engine-activity-element="Activity.Value">'.$value.'</div>';
            }else{
                $value = preg_replace('/[\n\r\s]*$/', '', $activityValue);
                $value = preg_replace('/^[\n\r\s]*/', '', $value        );
                return '<p data-post-element-type="Pure.BuddyPress.Activities.A.Activity" data-engine-activity-activityID="'.$activity_id.'" data-engine-activity-element="Activity.Value">'.nl2br(esc_html($value)).'</p>';
            }
        }
        private function parseActivityContent($activityValue, $activity_id){
            preg_match_all('/\[quote:begin\](.(?!\[quote:begin\]))*\[quote:end\]/si', $activityValue, $matches);
            $_parts = preg_split('/\[quote:begin\](.(?!\[quote:begin\]))*\[quote:end\]/si', $activityValue, -1, PREG_SPLIT_OFFSET_CAPTURE);
            $parts  = array();
            foreach($_parts as $key=>$part){
                $is_empty = (preg_replace('/[\r\n\s]/', '', $part[0]) === '' ? true : false);
                if ($key === 0){
                    $parts[] = ($part[1] === 0 ? ($part[0] === '' ? false : ($is_empty === false ? $part[0] : false)) : false);
                }else{
                    $parts[] = ($part[0] === '' ? false : ($is_empty === false ? $part[0] : false));
                }
            }
            //echo var_dump($parts);
            if (is_array($matches) !== false) {
                if (count($matches) === 2) {
                    if (count($matches[0]) > 0){
                        $comment = '';
                        for($index = 0, $max_index = count($parts); $index < $max_index; $index ++){
                            $comment .= ($parts[$index] !== false ? $this->getActivityContent($parts[$index], $activity_id) : '');
                            if (isset($matches[0][$index]) !== false){
                                $quote = $this->getQuote($matches[0][$index]);
                                $comment .= ($quote !== false ? $quote : '');
                            }
                        }
                        return $comment;
                    }
                }
            }
            return $this->getActivityContent($activityValue, $activity_id);
        }
        private function parseActivity($activity){
            if ($activity->content !== ''){
                //Memes
                preg_match('/\[meme:begin\](.*)\[meme:end\]/i', $activity->content, $matches);
                if (is_array($matches) !== false){
                    if (count($matches) === 2){
                        return  '<div data-post-element-type="Pure.BuddyPress.Activities.A.Meme" data-engine-activity-activityID="'.$activity->id.'" data-engine-activity-element="Activity.Meme">'.
                                    '<img alt="" data-post-element-type="Pure.BuddyPress.Activities.A.Meme" src="'.esc_html($matches[1]).'"/>'.
                                '</div>';
                    }
                }
                //Attachment
                preg_match('/\[attachment:begin\](.*)\[attachment:end\]/i', $activity->content, $matches);
                if (is_array($matches) !== false){
                    if (count($matches) === 2){
                        return  '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Container" data-engine-activity-activityID="'.$activity->id.'" data-engine-activity-element="Activity.Attachment">'.
                                    '<img alt="" data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment" src="'.esc_html($matches[1]).'"/>'.
                                '</div>'.
                                $this->parseActivityContent(preg_replace('/\[attachment:begin\](.*)\[attachment:end\]/', '', $activity->content), $activity->id);
                    }
                }
                $this->parseActivityContent(esc_html($activity->content), $activity->id);
                //Just comment
                return $this->parseActivityContent(preg_replace('/\[attachment:begin\](.*)\[attachment:end\]/', '', $activity->content), $activity->id);
            }else{
                return $this->getActivityAction($activity->action, $activity->id);
            }
        }
        private function innerHTMLTitle($parameters, $activities){
            return  '<!--BEGIN: Activity.Title.A -->'.
                    '<div data-post-element-type="Pure.BuddyPress.Activities.Title.A">'.
                        '<p data-post-element-type="Pure.BuddyPress.Activities.Title.A">'.__('Activities', 'pure').'</p>'.
                        '<p data-post-element-type="Pure.BuddyPress.Activities.Title.A.Info">'.__('total', 'pure').': <span data-engine-activity-element="Activities.Counter.Total" data-engine-activity-objectID="'.$parameters->object_id.'">'.$activities->total.'</span></p>'.
                    '</div>'.
                    '<!--END: Activity.Title.A -->';
        }
        private function innerHTMLRootActivitiesMark($parameters){
            return  '<!--BEGIN: New sent root comments mark -->'.
                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.EditorMark" data-engine-activity-root-mark="'.$parameters->object_id.$parameters->object_type.'"></div>'.
                    '<!--END: New sent root comments mark -->';
        }
        private function innerHTMLRootEditor($parameters){
            $innerHTML = '';
            if ($this->permissions->root !== false){
                $innerHTML =    '<!--BEGIN: Activity.A.Add -->'.
                                '<div data-post-element-type="Pure.BuddyPress.Activities.A.Activity.Add">'.
                                    '<input disabled data-post-element-type="Pure.BuddyPress.Activities.A.Controls" type="checkbox" data-type-controls="add_comment"'.
                                            'id="add_post_in_activity_'.$parameters->object_id.'"'.
                                            'data-engine-activity-element="Editor.Caller" '.
                                            'data-engine-activity-objectID="'.$parameters->object_id.'" '.
                                            'data-engine-activity-objectType="'.$parameters->object_type.'" '.
                                            'data-engine-activity-activityID="0" '.
                                            'data-engine-activity-rootID="0" '.
                                            'data-engine-activity-event="change" '.
                                            'data-engine-activity-editorID="add_post_in_activity_'.$parameters->object_id.'"/>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="add_switcher">'.
                                        '<label for="add_post_in_activity_'.$parameters->object_id.'" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="write"></label>'.
                                        '<p data-post-element-type="Pure.BuddyPress.Activities.A.Activity.Count">'.__('add new one', 'pure').'</p>'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls.ResetFloat"></div>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="add_editor">'.
                                        '<p data-post-element-type="Pure.BuddyPress.Activities.A.Activity.Title">'.__('Write here', 'pure').'</p>'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.EditorMark" data-engine-activity-mark="add_post_in_activity_'.$parameters->object_id.'"></div>'.
                                    '</div>'.
                                '</div>'.
                                '<!--END: Activity.A.Add -->';
            }
            return $innerHTML;
        }
        private function innerHTMLIncludedActivities($parameters, $parent_activity, $root_id){
            $innerHTML  = '';
            $children   = $parent_activity->children;
            if ($children !== false){
                $children = array_reverse($children);
                foreach($children as $_activity){
                    $activity   = $_activity;
                    $user       = $_activity->user;
                    $innerHTML .=   '<!--BEGIN: Activity.A -->'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Wrapper" '.
                                        'data-engine-activity-objectID="'.$parameters->object_id.'" '.
                                        'data-engine-activity-objectType="'.$parameters->object_type.'" '.
                                        'data-engine-activity-activityID="'.$activity->id.'"'.
                                        'data-engine-activity-remove="remove" '.
                                    '>'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Left" data-post-activity-type="included">'.
                                            '<div data-post-element-type="Pure.BuddyPress.Activities.A.Avatar" data-post-activity-type="included" style="background-image:url('.$user->avatar.')">'.
                                            '</div>'.
                                            $this->ManaTemplate->innerHTML(
                                                (object)array(
                                                    'object'    =>'activity',
                                                    'object_id' =>$activity->id,
                                                    'user_id'   =>$user->id,
                                                    'field'     =>$parameters->object_id,
                                                    'data'      =>$this->manaCache
                                                )
                                            ).
                                        '</div>'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Right">'.
                                            '<p data-post-element-type="Pure.BuddyPress.Activities.A.Name"><a data-engine-activity-element="Activity.Author.Name" href="'.$user->home.'">'.$user->name.'</a> '.($activity->content !== '' ? __('say', 'pure') : __('do', 'pure')).'</p>'.
                                            '<p data-post-element-type="Pure.BuddyPress.Activities.A.Date" data-engine-activity-element="Activity.DateTime">'.$activity->date_recorded.'</p>'.
                                            $this->parseActivity($activity);
                    if ($this->user !== false){
                        $innerHTML .=       '<input disabled data-post-element-type="Pure.BuddyPress.Activities.A.Controls" type="checkbox" data-type-controls="editor"'.
                                                    'id="editor_id_'.$activity->id.'"'.
                                                    'data-engine-activity-element="Editor.Caller" '.
                                                    'data-engine-activity-objectID="'.$parameters->object_id.'" '.
                                                    'data-engine-activity-objectType="'.$parameters->object_type.'" '.
                                                    'data-engine-activity-activityID="'.$activity->id.'" '.
                                                    'data-engine-activity-rootID="'.$root_id.'" '.
                                                    'data-engine-activity-event="change" '.
                                                    'data-engine-activity-editorID="editor_id_'.$activity->id.'"/>'.
                                            '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="include_controls">';
                        if ($this->permissions->comment !== false){
                            $innerHTML .=       '<label for="editor_id_'.$activity->id.'" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="reply"></label>';
                        }
                        if ($this->permissions->remove_comment !== false){
                            $innerHTML .=       '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="remove" '.
                                                    'data-engine-activity-element="Activities.Remove" '.
                                                    'data-engine-activity-activityID="'.$activity->id.'" '.
                                                '></label>';
                        }
                        $innerHTML .=           '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls.ResetFloat"></div>'.
                                            '</div>'.
                                            '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="editor">'.
                                                '<div data-post-element-type="Pure.BuddyPress.Activities.A.EditorMark" data-engine-activity-mark="editor_id_'.$activity->id.'"></div>'.
                                            '</div>';
                    }
                    $innerHTML .=           '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="sub_included" data-engine-activity-included="Container" data-engine-activity-activityID="'.$activity->id.'">'.
                                                $this->innerHTMLIncludedActivities($parameters, $_activity, $root_id).
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<!--END: Activity.A -->';
                }
            }
            return $innerHTML;
        }
        private function innerHTMLActivity($parameters, $_activity){
            $activity   = $_activity;
            $user       = $_activity->user;
            $children   = $_activity->children;
            $innerHTML  =   '<!--BEGIN: Activity.A -->'.
                            '<div data-post-element-type="Pure.BuddyPress.Activities.A.Wrapper" '.
                                'data-engine-activity-objectID="'.$parameters->object_id.'" '.
                                'data-engine-activity-objectType="'.$parameters->object_type.'" '.
                                'data-engine-activity-activityID="'.$activity->id.'" '.
                                'data-engine-activity-remove="remove" '.
                                'data-engine-activity-type="root" '.
                            '>'.
                                '<div data-post-element-type="Pure.BuddyPress.Activities.A.Left">'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Avatar" style="background-image:url('.$user->avatar.')">'.
                                    '</div>'.
                                    $this->ManaTemplate->innerHTML(
                                        (object)array(
                                            'object'    =>'activity',
                                            'object_id' =>$activity->id,
                                            'user_id'   =>$user->id,
                                            'field'     =>$parameters->object_id,
                                            'data'      =>$this->manaCache
                                        )
                                    ).
                                '</div>'.
                                '<div data-post-element-type="Pure.BuddyPress.Activities.A.Right">'.
                                    '<p data-post-element-type="Pure.BuddyPress.Activities.A.Name"><a data-engine-activity-element="Activity.Author.Name" href="'.$user->home.'">'.$user->name.'</a> '.($activity->content !== '' ? __('say', 'pure') : __('do', 'pure')).'</p>'.
                                    '<p data-post-element-type="Pure.BuddyPress.Activities.A.Date" data-engine-activity-element="Activity.DateTime">'.$activity->date_recorded.'</p>'.
                                    $this->parseActivity($activity).
                                    '<input id="switcher_id_'.$activity->id.'" data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="switcher" type="checkbox"/>';
            if ($this->user !== false){
                $innerHTML .=       '<input disabled data-post-element-type="Pure.BuddyPress.Activities.A.Controls" type="checkbox" data-type-controls="editor"'.
                                            'id="editor_id_'.$activity->id.'"'.
                                            'data-engine-activity-element="Editor.Caller" '.
                                            'data-engine-activity-objectID="'.$parameters->object_id.'" '.
                                            'data-engine-activity-objectType="'.$parameters->object_type.'" '.
                                            'data-engine-activity-activityID="'.$activity->id.'" '.
                                            'data-engine-activity-rootID="0" '.
                                            'data-engine-activity-event="change" '.
                                            'data-engine-activity-editorID="editor_id_'.$activity->id.'"/>';
            }
            $innerHTML .=           '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="switcher">'.
                                        '<label for="switcher_id_'.$activity->id.'" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="switcher" '.($children !== false ? 'data-button-light' : '').' data-engine-activity-included-flag="data-button-light"></label>'.
                                        '<p data-post-element-type="Pure.BuddyPress.Activities.A.Activity.Count">'.__('include', 'pure').' <span data-engine-activity-included="Count">'.($children !== false ? count($children) : '').'</span></p>'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls.ResetFloat"></div>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="controls">'.
                                        '<label for="switcher_id_'.$activity->id.'" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="cancel"></label>';
            if ($this->user !== false){
                if ($this->permissions->comment !== false){
                    $innerHTML .=       '<label for="editor_id_'.$activity->id.'" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="reply"></label>';
                }
            }
            if ($this->permissions->remove_activity !== false){
                $innerHTML .=           '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="remove" '.
                                            'data-engine-activity-element="Activities.Remove" '.
                                            'data-engine-activity-activityID="'.$activity->id.'" '.
                                        '></label>';
            }
            $innerHTML .=               '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls.ResetFloat"></div>'.
                                    '</div>';
            if ($this->user !== false){
                $innerHTML .=       '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="editor">'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.EditorMark" data-engine-activity-mark="editor_id_'.$activity->id.'"></div>'.
                                    '</div>';
            }
            $innerHTML .=           '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="included" data-engine-activity-included="Container" data-engine-activity-activityID="'.$activity->id.'">'.
                                        $this->innerHTMLIncludedActivities($parameters, $_activity, $activity->id).
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                            '<!--END: Activity.A -->';
            return $innerHTML;
        }
        private function innerHTMLActivities($parameters, $activities){
            $innerHTML = '';
            foreach($activities->activities as $activity){
                $innerHTML .= $this->innerHTMLActivity($parameters, $activity);
            }
            return $innerHTML;
        }
        private function innerHTMLMoreMark($parameters){
            $innerHTML =    '<!--BEGIN: Activity.A.More.Mark -->'.
                            '<div data-post-element-type="Pure.BuddyPress.Activities.A.EditorMark" '.
                                'data-engine-activity-more-mark="'.$parameters->object_id.$parameters->object_type.'" '.
                            '></div>'.
                            '<!--END: Activity.A.More.Mark  -->';
            return $innerHTML;
        }
        private function innerHTMLMore($parameters, $activities){
            $innerHTML =    '<!--BEGIN: Activity.A.More -->'.
                            '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="more">'.
                                '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="more_all" '.
                                    'data-engine-activity-element="Activities.More.All" '.
                                    'data-engine-activity-more-shown="'.$activities->shown.'" '.
                                    'data-engine-activity-objectID="'.$parameters->object_id.'" '.
                                    'data-engine-activity-objectType="'.$parameters->object_type.'" '.
                                '></label>'.
                                '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="more" '.
                                    'data-engine-activity-element="Activities.More.Package" '.
                                    'data-engine-activity-more-shown="'.$activities->shown.'" '.
                                    'data-engine-activity-objectID="'.$parameters->object_id.'" '.
                                    'data-engine-activity-objectType="'.$parameters->object_type.'" '.
                                '></label>'.
                                '<p data-post-element-type="Pure.BuddyPress.Activities.A.Activity.Count">'.__('activities', 'pure').': '.
                                    '<span data-engine-activity-element="Activities.Counter.Shown" '.
                                        'data-engine-activity-objectID="'.$parameters->object_id.'" '.
                                        'data-engine-activity-objectType="'.$parameters->object_type.'" '.
                                    '>'.$activities->shown.'</span> / '.
                                    '<span data-engine-activity-element="Activities.Counter.Total" '.
                                        'data-engine-activity-objectID="'.$parameters->object_id.'" '.
                                        'data-engine-activity-objectType="'.$parameters->object_type.'" '.
                                    '>'.$activities->total.'</span>'.
                                '</p>'.
                                '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls.ResetFloat"></div>'.
                            '</div>'.
                            '<!--END: Activity.A.More -->';
            return $innerHTML;
        }
        private function makeManaCache($activities){
            $data       = array();
            $processing = function(&$data, $activities) use (&$processing){
                foreach($activities as $activity){
                    $data[$activity->id] = (object)array(
                        'object_id' =>$activity->id,
                        'user_id'   =>$activity->user_id
                    );
                    if ($activity->children !== false){
                        $processing($data, $activity->children);
                    }
                }
            };
            $processing($data, $activities);
            if (count($data) > 0){
                \Pure\Components\Relationships\Mana\Initialization::instance()->attach(true);
                $Provider           = new \Pure\Components\Relationships\Mana\Provider();
                $this->manaCache    = $Provider->fillDataWithObjects(
                    (object)array(
                        'object'=>'activity',
                        'data'   =>$data
                    )
                );
                $Provider           = NULL;
            }
            //echo var_dump($this->manaCache);
        }
        private function attachTinyMCE(){
            /*
             * It doesn't necessary, because \_WP_Editors::editor_settings attach it. But let it be here
             * just to remember
            \Pure\Components\Attacher\Module\Attacher::instance()->addJS(
                get_site_url().'/wp-includes/js/tinymce/tinymce.min.js',
                false,
                true
            );*/
            \Pure\Components\Attacher\Module\Attacher::instance()->addCSS(
                get_site_url().'/wp-includes/css/editor.min.css',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addCSS(
                get_site_url().'/wp-includes/css/dashicons.min.css',
                false,
                true
            );
            if ( ! class_exists( '_WP_Editors' ) ) {
                require( ABSPATH . WPINC . '/class-wp-editor.php' );
            }
            \_WP_Editors::editor_settings(
                'nulleditorjustforconfiguration',
                \_WP_Editors::parse_settings( 'nulleditorjustforconfiguration', array() )
            );
        }
        private function resources($parameters){
            \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
            \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.allowRemoveActivity',
                $this->permissions->remove_activity,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.allowRemoveComment',
                $this->permissions->remove_comment,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.allowAttachment',
                $this->settings->allow_attachment,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.allowMemes',
                $this->settings->allow_memes,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.maxLength',
                $this->settings->max_length,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.hotUpdate',
                $this->settings->hot_update,
                false,
                true
            );
            \Pure\Components\Dialogs\B\Initialization::instance()->attach(false, 'after');
            \Pure\Components\WordPress\Media\Resources\Initialization::instance()->attach(false, 'after');
            \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('A', false, 'after');
            \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('D', false, 'after');
            \Pure\Components\Attacher\Module\Attacher::instance()->addINIT(
                'pure.buddypress.activities.A',
                false,
                true
            );
            $this->attachTinyMCE();
            //Define request settings
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/settings/request.buddypress.activities.php'));
            $Settings = new \Pure\Requests\BuddyPress\Activities\Settings\Initialization();
            $Settings->init((object)array(
                'user_id'=>($this->user !== false ? $this->user->ID : 0)
            ));
            $Settings = NULL;
        }
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $parameters     = \Pure\Components\WordPress\Settings\Instance::instance()->settings->activities->properties;
            $this->settings = \Pure\Components\WordPress\Settings\Instance::instance()->less($parameters);
        }
        public function messageInnerHTML($message){
            return  '<div data-post-element-type="Pure.BuddyPress.Activities.A.Information">'.
                        '<p data-post-element-type="Pure.BuddyPress.Activities.A.Information">'.$message.'</p>'.
                    '</div>';
        }
        private function buildInnerHTML($parameters){
            $innerHTML      = '';
            $activities     = $this->getData($parameters);
            if ($activities !== false){
                $this->ManaTemplate = \Pure\Templates\Mana\Icon\Initialization::instance()->get('A');
                $this->makeManaCache($activities->activities);
                $this->resources($parameters);
                //$innerHTML .= $this->innerHTMLTitle             ($parameters, $activities);
                $innerHTML .= $this->innerHTMLRootEditor        ($parameters);
                $innerHTML .= $this->innerHTMLRootActivitiesMark($parameters);
                $innerHTML .= $this->innerHTMLActivities        ($parameters, $activities);
                $innerHTML .= $this->innerHTMLMoreMark          ($parameters, $activities);
                $innerHTML .= $this->innerHTMLMore              ($parameters, $activities);
                //Templates
                $Templates  = new ATemplates();
                if ($this->permissions->comment !== false){
                    $Templates->innerHTMLEditorTemplate       ($parameters->object_id, $parameters->object_type, $this->settings, $this->user);
                }
                if ($this->permissions->root !== false){
                    $Templates->innerHTMLRootEditorTemplate   ($parameters->object_id, $parameters->object_type, $this->settings, $this->user);
                }
                $Templates->innerHTMLIncludedComment      ($parameters->object_id, $parameters->object_type, $this->user, $this->ManaTemplate, $this->permissions);
                $Templates->innerHTMLRootComment          ($parameters->object_id, $parameters->object_type, $this->user, $this->ManaTemplate, $this->permissions);
                $Templates->innerQuoteNotification        ($parameters->object_id, $parameters->object_type, $this->user);
                $Templates  = NULL;
                return $innerHTML;
            }
            return $innerHTML;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $this->getSettings();
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $this->user = $WordPress->get_current_user(true);
                $WordPress  = NULL;
                $this->getEditPermissions($parameters);
                if ($this->user !== false){
                    $this->getManaPermissions();
                    if ($this->user->mana->allow_create->activity === false && $this->permissions->root !== false){
                        $this->permissions->root = false;
                        $innerHTML .= $this->messageInnerHTML(__('Sorry, but you have not enough mana to add some post here.', 'pure'));
                    }
                    if ($this->user->mana->allow_create->comment === false && $this->permissions->comment !== false){
                        $this->permissions->comment = false;
                        $innerHTML .= $this->messageInnerHTML(__('Sorry, but you have not enough mana to add comments here.', 'pure'));
                    }
                    if ($this->user->mana->allow_remove->activity === false && $this->permissions->remove_activity !== false){
                        $this->permissions->remove_activity = false;
                        $innerHTML .= $this->messageInnerHTML(__('Sorry, but you have not enough mana to remove activities.', 'pure'));
                    }
                    if ($this->user->mana->allow_remove->comment === false && $this->permissions->remove_comment !== false){
                        $this->permissions->remove_comment = false;
                        $innerHTML .= $this->messageInnerHTML(__('Sorry, but you have not enough mana to remove comments.', 'pure'));
                    }
                }
                $innerHTML .= $this->buildInnerHTML($parameters);
            }
            return $innerHTML;
        }
    }
    class ATemplates {
        public function innerHTMLRootEditorTemplate($object_id, $object_type, $settings, $user){
            $innerHTML = '';
            if ($user !== false){
                $innerHTML =    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Container" style="display: none;" '.
                                    'data-engine-activity-element="Root.Editor" '.
                                    'data-engine-activity-objectID="'.$object_id.'" '.
                                    'data-engine-activity-objectType="'.$object_type.'" '.
                                '>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment" data-switch-id="[activityID]">'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label.Container">'.
                                            '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label">'.
                                                '<p data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label">'.__('attachment', 'pure').'</p>'.
                                            '</div>'.
                                        '</div>'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Container">'.
                                            '<img alt="" data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment" data-storage-id="[activityID]"/>'.
                                        '</div>'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label.Container">'.
                                            '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label">'.
                                                '<p data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label">'.__('attachment', 'pure').'</p>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.RootEditor.Container"'.
                                        'data-engine-activity-element="Root.Editor.Container" '.
                                    '>'.
                                    '</div>'.
                                    '<label for="[editorID]" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" title="'.__('hide', 'pure').'" data-button-type="cancel"></label>'.
                                    '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" title="'.__('send', 'pure').'" data-button-type="send" data-engine-activity-element="Editor.Button.Send"></label>';
                if($settings->allow_attachment === 'on'){
                    $innerHTML .=   '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" title="'.__('image', 'pure').'" data-button-type="attachment" '.
                                        'data-engine-activity-element="Editor.Button.Attachment" '.
                                        'pure-wordpress-media-images-add-selector="img[data-storage-id=|[activityID]|]"'.
                                        'pure-wordpress-media-images-switch-selector="*[data-switch-id=|[activityID]|]"'.
                                        'data-align-direction="left">'.
                                    '</label>'.
                                    '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" title="'.__('image', 'pure').'" data-button-type="remove_attachment" '.
                                        'data-engine-activity-element="Editor.Button.Attachment" '.
                                        'pure-wordpress-media-images-remove-selector="img[data-storage-id=|[activityID]|]" '.
                                        'pure-wordpress-media-images-switch-selector="*[data-switch-id=|[activityID]|]" '.
                                        'data-switch-id="[activityID]" '.
                                        'data-align-direction="left">'.
                                    '</label>';
                }
                $innerHTML .=       '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls.ResetFloat"></div>'.
                                '</div>';
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.buddypress.activities.configuration.templates.root_editor.'.$object_id.$object_type,
                    base64_encode($innerHTML),
                    false,
                    true
                );
            }
            return $innerHTML;
        }
        public function innerHTMLEditorTemplate($object_id, $object_type, $settings, $user){
            $innerHTML = '';
            if ($user !== false){
                $innerHTML =    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Container" style="display: none;" '.
                                    'data-engine-activity-element="Editor" '.
                                    'data-engine-activity-objectID="'.$object_id.'" '.
                                    'data-engine-activity-objectType="'.$object_type.'" '.
                                '>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment" data-switch-id="[activityID]">'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label.Container">'.
                                            '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label">'.
                                                '<p data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label">'.__('attachment', 'pure').'</p>'.
                                            '</div>'.
                                        '</div>'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Container">'.
                                            '<img alt="" data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment" data-storage-id="[activityID]"/>'.
                                        '</div>'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label.Container">'.
                                            '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label">'.
                                                '<p data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Label">'.__('attachment', 'pure').'</p>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                    '<textarea data-post-element-type="Pure.BuddyPress.Activities.A.Controls.TextArea"></textarea>'.
                                    '<label for="[editorID]" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" title="'.__('hide', 'pure').'" data-button-type="cancel"></label>'.
                                    '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" title="'.__('quote', 'pure').'" data-button-type="quote" data-engine-activity-element="Editor.Button.Quote"></label>';
                if($settings->allow_memes === 'on'){
                    $innerHTML .=   '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" title="'.__('meme', 'pure').'" data-button-type="meme" data-engine-activity-element="Editor.Button.Meme"></label>';
                }
                $innerHTML .=       '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" title="'.__('send', 'pure').'" data-button-type="send" data-engine-activity-element="Editor.Button.Send"></label>';
                if($settings->allow_attachment === 'on'){
                    $innerHTML .=   '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" title="'.__('image', 'pure').'" data-button-type="attachment" '.
                                        'data-engine-activity-element="Editor.Button.Attachment" '.
                                        'pure-wordpress-media-images-add-selector="img[data-storage-id=|[activityID]|]"'.
                                        'pure-wordpress-media-images-switch-selector="*[data-switch-id=|[activityID]|]"'.
                                        'data-align-direction="left">'.
                                    '</label>'.
                                    '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" title="'.__('image', 'pure').'" data-button-type="remove_attachment" '.
                                        'data-engine-activity-element="Editor.Button.Attachment" '.
                                        'pure-wordpress-media-images-remove-selector="img[data-storage-id=|[activityID]|]" '.
                                        'pure-wordpress-media-images-switch-selector="*[data-switch-id=|[activityID]|]" '.
                                        'data-switch-id="[activityID]" '.
                                        'data-align-direction="left">'.
                                    '</label>';
                }
                $innerHTML .=       '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls.ResetFloat"></div>'.
                                '</div>';
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.buddypress.activities.configuration.templates.editor.'.$object_id.$object_type,
                    base64_encode($innerHTML),
                    false,
                    true
                );
            }
            return $innerHTML;
        }
        public function innerQuoteNotification($object_id, $object_type, $user){
            $innerHTML = '';
            if ($user !== false){
                $innerHTML =    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Quote.Notification" data-engine-activity-element="Quote.Notification" style="display: none;">'.
                                    __('Quote was saved', 'pure').
                                '</div>';
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.buddypress.activities.configuration.templates.quote.'.$object_id.$object_type,
                    base64_encode($innerHTML),
                    false,
                    true
                );
            }
            return $innerHTML;
        }
        public function innerHTMLRootComment($object_id, $object_type, $user, $manaTemplate, $permissions){
            /*
             * FIELDS
             * [name]
             * [avatar]
             * [date]
             * [content]
             * [activity_id]
             * [post_id]
             */
            $innerHTML =    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Wrapper" '.
                                'data-engine-activity-element="Activity.Root" '.
                                'data-engine-activity-activityID="[activity_id]" '.
                                'data-engine-activity-objectID="'.$object_id.'" '.
                                'data-engine-activity-objectType="'.$object_type.'" '.
                                'data-engine-activity-type="root" '.
                                'data-engine-activity-remove="remove" '.
                                'style="display:none;" '.
                            '>'.
                                '<div data-post-element-type="Pure.BuddyPress.Activities.A.Left">'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Avatar" style="background-image:url([avatar])">'.
                                    '</div>'.
                                    $manaTemplate->markInnerHTML('activity', '[activity_id]').
                                '</div>'.
                                '<div data-post-element-type="Pure.BuddyPress.Activities.A.Right">'.
                                    '<p data-post-element-type="Pure.BuddyPress.Activities.A.Name"><a data-engine-activity-element="Activity.Author.Name" href="[home]">[name]</a> '.__('say', 'pure').'</p>'.
                                    '<p data-post-element-type="Pure.BuddyPress.Activities.A.Date" data-engine-activity-element="Activity.DateTime">[date]</p>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Meme" data-engine-activity-activityID="[activity_id]" data-engine-activity-element="Activity.Meme">'.
                                        '<img alt="" data-post-element-type="Pure.BuddyPress.Activities.A.Meme" src="[meme]"/>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Container" data-engine-activity-activityID="[activity_id]" data-engine-activity-element="Activity.Attachment">'.
                                        '<img alt="" data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment" src="[attachment]"/>'.
                                    '</div>'.
                                    '<p data-post-element-type="Pure.BuddyPress.Activities.A.Activity" data-engine-activity-activityID="[activity_id]" data-engine-activity-element="Activity.Value">[content]</p>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Activity" data-engine-activity-activityID="[activity_id]" data-engine-activity-element="Activity.Container"></div>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Quote" data-engine-activity-activityID="[activity_id]" data-engine-activity-element="Activity.Quote">'.
                                        '<p data-post-element-type="Pure.BuddyPress.Activities.A.Quote">[quote]</p>'.
                                        '<p data-post-element-type="Pure.BuddyPress.Activities.A.Quote.Author">[quote_author], '.
                                            '<span data-post-element-type="Pure.BuddyPress.Activities.A.Quote.Date">[quote_date]</span>'.
                                        '</p>'.
                                    '</div>'.
                                    '<input id="switcher_id_[activity_id]" data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="switcher" type="checkbox"/>';
            if ($user !== false){
                $innerHTML .=       '<input data-post-element-type="Pure.BuddyPress.Activities.A.Controls" type="checkbox" data-type-controls="editor"'.
                                            'id="editor_id_[activity_id]"'.
                                            'data-engine-activity-element="Editor.Caller" '.
                                            'data-engine-activity-objectID="'.$object_id.'" '.
                                            'data-engine-activity-activityID="[activity_id]" '.
                                            'data-engine-activity-rootID="[root_id]" '.
                                            'data-engine-activity-objectType="'.$object_type.'" '.
                                            'data-engine-activity-event="change" '.
                                            'data-engine-activity-editorID="editor_id_[activity_id]"/>';
            }
            $innerHTML .=           '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="switcher">'.
                                        '<label for="switcher_id_[activity_id]" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="switcher" data-engine-activity-included-flag="data-button-light"></label>'.
                                        '<p data-post-element-type="Pure.BuddyPress.Activities.A.Activity.Count">'.__('include', 'pure').' <span data-engine-activity-included="Count"></span></p>'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls.ResetFloat"></div>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="controls">'.
                                        '<label for="switcher_id_[activity_id]" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="cancel"></label>';
            if ($user !== false){
                if ($permissions->comment !== false){
                    $innerHTML .=       '<label for="editor_id_[activity_id]" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="reply"></label>';
                }
            }
            if ($permissions->remove_activity !== false){
                $innerHTML .=           '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="remove" '.
                                            'data-engine-activity-element="Activities.Remove" '.
                                            'data-engine-activity-activityID="[activity_id]" '.
                                        '></label>';
            }
            $innerHTML .=               '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls.ResetFloat"></div>'.
                                    '</div>';
            if ($user !== false){
                $innerHTML .=       '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="editor">'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.EditorMark" data-engine-activity-mark="editor_id_[activity_id]"></div>'.
                                    '</div>';
            }
            $innerHTML .=           '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="included" data-engine-activity-included="Container" data-engine-activity-activityID="[activity_id]">'.
                                    '</div>'.
                                '</div>'.
                            '</div>';
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.templates.root_comment.'.$object_id.$object_type,
                base64_encode($innerHTML),
                false,
                true
            );
            return $innerHTML;
        }
        public function innerHTMLIncludedComment($object_id, $object_type, $user, $manaTemplate, $permissions){
            /*
             * FIELDS
             * [name]
             * [avatar]
             * [date]
             * [content]
             * [activity_id]
             * [post_id]
             */
            $innerHTML  =   '<div data-post-element-type="Pure.BuddyPress.Activities.A.Wrapper" '.
                                'data-engine-activity-element="Activity.Included" '.
                                'data-engine-activity-activityID="[activity_id]" '.
                                'data-engine-activity-objectID="'.$object_id.'" '.
                                'data-engine-activity-objectType="'.$object_type.'" '.
                                'data-engine-activity-remove="remove" '.
                                'style="display:none;" '.
                            '>'.
                                '<div data-post-element-type="Pure.BuddyPress.Activities.A.Left" data-post-activity-type="included">'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Avatar" data-post-activity-type="included" style="background-image:url([avatar])">'.
                                    '</div>'.
                                    $manaTemplate->markInnerHTML('activity', '[activity_id]').
                                '</div>'.
                                '<div data-post-element-type="Pure.BuddyPress.Activities.A.Right">'.
                                    '<p data-post-element-type="Pure.BuddyPress.Activities.A.Name"><a data-engine-activity-element="Activity.Author.Name" href="[home]">[name]</a> '.__('say', 'pure').'</p>'.
                                    '<p data-post-element-type="Pure.BuddyPress.Activities.A.Date" data-engine-activity-element="Activity.DateTime">[date]</p>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Meme" data-engine-activity-activityID="[activity_id]" data-engine-activity-element="Activity.Meme">'.
                                        '<img alt="" data-post-element-type="Pure.BuddyPress.Activities.A.Meme" src="[meme]"/>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment.Container" data-engine-activity-activityID="[activity_id]" data-engine-activity-element="Activity.Attachment">'.
                                        '<img alt="" data-post-element-type="Pure.BuddyPress.Activities.A.Editor.Attachment" src="[attachment]"/>'.
                                    '</div>'.
                                    '<p data-post-element-type="Pure.BuddyPress.Activities.A.Activity" data-engine-activity-activityID="[activity_id]" data-engine-activity-element="Activity.Value">[content]</p>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Activity" data-engine-activity-activityID="[activity_id]" data-engine-activity-element="Activity.Container"></div>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Quote" data-engine-activity-activityID="[activity_id]" data-engine-activity-element="Activity.Quote">'.
                                        '<p data-post-element-type="Pure.BuddyPress.Activities.A.Quote">[quote]</p>'.
                                        '<p data-post-element-type="Pure.BuddyPress.Activities.A.Quote.Author">[quote_author], '.
                                            '<span data-post-element-type="Pure.BuddyPress.Activities.A.Quote.Date">[quote_date]</span>'.
                                        '</p>'.
                                    '</div>';
            if ($user !== false){
                $innerHTML .=       '<input data-post-element-type="Pure.BuddyPress.Activities.A.Controls" type="checkbox" data-type-controls="editor"'.
                                            'id="editor_id_[activity_id]"'.
                                            'data-engine-activity-element="Editor.Caller" '.
                                            'data-engine-activity-objectID="[object_id]" '.
                                            'data-engine-activity-activityID="[activity_id]" '.
                                            'data-engine-activity-rootID="[root_id]" '.
                                            'data-engine-activity-event="change" '.
                                            'data-engine-activity-objectType="'.$object_type.'" '.
                                            'data-engine-activity-editorID="editor_id_[activity_id]"/>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="include_controls">';
                if ($permissions->comment !== false){
                    $innerHTML .=       '<label for="editor_id_[activity_id]" data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="reply"></label>';
                }
                if ($permissions->remove_comment !== false){
                    $innerHTML .=       '<label data-post-element-type="Pure.BuddyPress.Activities.A.Controls.Button" data-button-type="remove" '.
                                            'data-engine-activity-element="Activities.Remove" '.
                                            'data-engine-activity-activityID="[activity_id]" '.
                                        '></label>';
                }
                $innerHTML .=           '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls.ResetFloat"></div>'.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="editor">'.
                                        '<div data-post-element-type="Pure.BuddyPress.Activities.A.EditorMark" data-engine-activity-mark="editor_id_[activity_id]"></div>'.
                                    '</div>';
            }
            $innerHTML .=           '<div data-post-element-type="Pure.BuddyPress.Activities.A.Controls" data-type-controls="sub_included" data-engine-activity-included="Container" data-engine-activity-activityID="[activity_id]">'.
                                    '</div>'.
                                '</div>'.
                            '</div>';
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.activities.configuration.templates.comment.'.$object_id.$object_type,
                base64_encode($innerHTML),
                false,
                true
            );
            return $innerHTML;
        }
    }
}
?>