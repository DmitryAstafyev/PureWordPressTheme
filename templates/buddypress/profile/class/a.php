<?php
namespace Pure\Templates\BuddyPress\Profile{
    class A{
        private function validate(&$parameters){
            $parameters             = (is_object($parameters) === true ? $parameters : new \stdClass());
            $parameters->manage     = (isset($parameters->manage    ) === true ? (gettype($parameters->manage   ) === 'boolean' ? $parameters->manage       : false ) : false);
            $parameters->avatar_id  = (isset($parameters->avatar_id ) === true ? (gettype($parameters->avatar_id) === 'string'  ? $parameters->avatar_id    : false ) : false);
        }
        private function getDefaultValue($field){
            $value = array();
            if (isset($field->childs) !== false){
                foreach($field->childs as $child){
                    if ((int)$child->is_default_option === 1){
                        $value[] = $child->name;
                    }
                }
            }
            return $value;
        }
        private function manageField($id, $field, $value = false, $collection_type){
            //echo var_dump($field);
            //$innerHTML = '<p data-element-type="Pure.Social.Profile.A.Message.Normal">'.$field->value.'</p>';
            $innerHTML      = '';
            $locked         = (isset($field->locked) === true ? ($field->locked === true ? ' disabled="disabled" ' : '') : '');
            $value          = ($value !== false ? $value : (isset($field->value) === true ? $field->value : false));
            if ($value !== false){
                switch($field->type){
                    case 'textbox':
                        $innerHTML .= '<input '.$locked.' data-engine-id="'.$id.'" data-engine-collection-type="'.$collection_type.'" data-engine-field-id="'.$field->id.'" type="text" value="'.stripcslashes($value).'" />';
                        break;
                    case 'textarea':
                        $innerHTML .= '<textarea '.$locked.' data-engine-id="'.$id.'" data-engine-collection-type="'.$collection_type.'" data-engine-field-id="'.$field->id.'">'.stripcslashes($value).'</textarea>';
                        break;
                    case 'selectbox':
                        if (isset($field->childs) === true){
                            $value = unserialize($value);
                            $value = (is_array($value) === false ? $this->getDefaultValue($field) : $value);
                            if (is_array($value) === true){
                                $innerHTML .=   '<select '.$locked.' data-element-type="Pure.Social.Profile.A.Select">';
                                foreach($field->childs as $option){
                                    $selected   = (in_array($option->name, $value) === true  ? 'selected' : '');
                                    $innerHTML .= '<option '.$selected.' data-engine-id="'.$id.'" data-engine-collection-type="'.$collection_type.'" data-engine-field-id="'.$field->id.'">'.stripcslashes($option->name).'</option>';
                                }
                                $innerHTML .=   '</select>';
                            }
                        }
                        break;
                    case 'multiselectbox':
                        if (isset($field->childs) === true){
                            $value = unserialize($value);
                            $value = (is_array($value) === false ? $this->getDefaultValue($field) : $value);
                            if (is_array($value) === true){
                                $innerHTML .=   '<select '.$locked.' data-element-type="Pure.Social.Profile.A.Select" multiple="multiple" >';
                                foreach($field->childs as $option){
                                    $selected   = (in_array($option->name, $value) === true ? 'selected' : '');
                                    $innerHTML .= '<option '.$selected.' data-engine-id="'.$id.'" data-engine-collection-type="'.$collection_type.'" data-engine-field-id="'.$field->id.'">'.stripcslashes($option->name).'</option>';
                                }
                                $innerHTML .=   '</select>';
                            }
                        }
                        break;
                    case 'radio':
                        if (isset($field->childs) === true){
                            $value = unserialize($value);
                            $value = (is_array($value) === false ? $this->getDefaultValue($field) : $value);
                            if (is_array($value) === true){
                                $name       = $id.$field->id;
                                foreach($field->childs as $option){
                                    $checked    = (in_array($option->name, $value) === true ? 'checked' : '');
                                    $innerHTML .=   '<label>'.
                                                        '<input '.$locked.' '.$checked.' data-element-type="Pure.CommonStyles.CheckBox.A" type="radio" value="'.$option->name.'" name="'.$name.'" data-engine-id="'.$id.'" data-engine-collection-type="'.$collection_type.'" data-engine-field-id="'.$field->id.'"/>'.
                                                        '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                            '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                                            '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                                            '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                                                '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                                            '</div>'.
                                                        '</div>'.
                                                        '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                            stripcslashes($option->name).
                                                        '</p>'.
                                                    '</label>';
                                }
                            }
                        }
                        break;
                    case 'checkbox':
                        if (isset($field->childs) === true){
                            $value = unserialize($value);
                            $value = (is_array($value) === false ? $this->getDefaultValue($field) : $value);
                            if (is_array($value) === true){
                                $name       = $id.$field->id;
                                foreach($field->childs as $option){
                                    $checked    = (in_array($option->name, $value) === true ? 'checked' : '');
                                    $innerHTML .=   '<label>'.
                                                        '<input '.$locked.' '.$checked.' data-element-type="Pure.CommonStyles.CheckBox.A" type="checkbox" value="'.$option->name.'" name="'.$name.'" data-engine-id="'.$id.'" data-engine-collection-type="'.$collection_type.'" data-engine-field-id="'.$field->id.'"/>'.
                                                        '<div data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                            '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="on">on</p>'.
                                                            '<p data-element-type="Pure.CommonStyles.CheckBox.A.Label" data-addition-type="off">off</p>'.
                                                            '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider">'.
                                                                '<div data-element-type="Pure.CommonStyles.CheckBox.A.Slider.Indicator"></div>'.
                                                            '</div>'.
                                                        '</div>'.
                                                        '<p data-element-type="Pure.CommonStyles.CheckBox.A">'.
                                                            stripcslashes($option->name).
                                                        '</p>'.
                                                    '</label>';
                                }
                            }
                        }
                        break;
                    case 'datebox':
                        $innerHTML .= '<input '.$locked.' type="date" value="'.$value.'" data-engine-id="'.$id.'" data-engine-collection-type="'.$collection_type.'" data-engine-field-id="'.$field->id.'"/>';
                        break;
                    case 'url':
                        $innerHTML .= '<input '.$locked.' type="url" value="'.$value.'" data-engine-id="'.$id.'" data-engine-collection-type="'.$collection_type.'" data-engine-field-id="'.$field->id.'"/>';
                        break;
                    case 'number':
                        $innerHTML .= '<input '.$locked.' type="number" value="'.$value.'" data-engine-id="'.$id.'" data-engine-collection-type="'.$collection_type.'" data-engine-field-id="'.$field->id.'"/>';
                        break;
                }
                \Pure\Components\Styles\CheckBoxes\A\Initialization::instance()->attach();
            }
            return $innerHTML;
        }
        private function profileWithManage($user_id, $id, $profile, $_profile, $parameters){
            \Pure\Components\GlobalSettings\MIMETypes\Initialization::instance()->attach(true);
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $avatar     = $WordPress->user_avatar_url($user_id);
            $WordPress  = NULL;
            $group_mark = uniqid('Profile_Visibility_Selector');
            $innerHTML  =           '<table data-element-type="Pure.Social.Profile.A.Table" border="0">'.
                                        '<tr data-element-type="Pure.Social.Profile.A.Row">'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column" data-addition-type="Title" colspan="2"><p data-element-type="Pure.Social.Profile.A.Message.Title">'.__( 'Basic information', 'pure' ).'</p></td>'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column" data-addition-type="Title"><p data-element-type="Pure.Social.Profile.A.Message.SubTitle">'.__( 'Visibility', 'pure' ).'</p></td>'.
                                        '</tr>'.
                                        '<tr data-element-type="Pure.Social.Profile.A.Row">'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column" colspan="2" data-noborder><p data-element-type="Pure.Social.Profile.A.Message.Notice">'.__( 'Apply to all in group', 'pure' ).'</p></td>'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column" data-noborder data-addition-type="select">'.
                                            '<select data-engine-id="'.$id.'" data-engine-visibility-group="'.$group_mark.'" data-engine-basic-visibility-selector>'.
                                                '<option value="nothing" selected></option>'.
                                                '<option value="public">'.  __( 'Anybody',            'pure' ).'</option>'.
                                                '<option value="loggedin">'.__( 'Registered users',   'pure' ).'</option>'.
                                                '<option value="friends">'. __( 'Friends',            'pure' ).'</option>'.
                                                '<option value="adminsonly">'. __( 'Hidden',             'pure' ).'</option>'.
                                            '</select>'.
                                            '</td>'.
                                        '</tr>';
            foreach($profile->WordPressBasic as $field){
                $locked         = (isset($field->locked) === true ? ($field->locked === true ? ' disabled ' : '') : '');
                $visibility     = (isset($field->allow_change_visibility) === true ? ($field->allow_change_visibility === false ? ' disabled ' : '') : '');
                $popup_title    =   ($locked        !== '' ? __( 'This field cannot be changed.', 'pure' ) : '');
                $popup_title    .=  ($popup_title   !== '' ? ' ' : '');
                $popup_title    .=  ($visibility    !== '' ? __( 'Visibility of field cannot be changed.', 'pure' ) : '');
                $innerHTML .=       '<tr data-element-type="Pure.Social.Profile.A.Row">'.
                                        '<td data-element-type="Pure.Social.Profile.A.Column"><p data-element-type="Pure.Social.Profile.A.Message.Normal" '.$locked.' '.($visibility !== '' ? ' out-of-control ' : '').' title="'.$popup_title.'">'.__( $field->name, 'pure' ).'</p></td>'.
                                        '<td data-element-type="Pure.Social.Profile.A.Column">'.
                                            $this->manageField($id, $field, false, 'WordPressBasic').
                                        '</td>'.
                                        '<td data-element-type="Pure.Social.Profile.A.Column">'.
                                            '<select '.$visibility.' data-engine-id="'.$id.'" data-engine-visibility-field-id="'.$field->id.'" data-engine-visibility-group="'.$group_mark.'">'.
                                                '<option value="public" '.  ($field->visibility == 'public'     ? 'selected' : '').'>'.__( 'Anybody',            'pure' ).'</option>'.
                                                '<option value="loggedin" '.($field->visibility == 'loggedin'   ? 'selected' : '').'>'.__( 'Registered users',   'pure' ).'</option>'.
                                                '<option value="friends" '. ($field->visibility == 'friends'    ? 'selected' : '').'>'.__( 'Friends',            'pure' ).'</option>'.
                                                '<option value="adminsonly" '. ($field->visibility == 'adminsonly'    ? 'selected' : '').'>'.__( 'Hidden',             'pure' ).'</option>'.
                                            '</select>'.
                                        '</td>'.
                                    '</tr>';
            }
            foreach($_profile->structure as $collection_name=>$collection){
                $group_mark = uniqid('Profile_Visibility_Selector');
                $innerHTML .=           '<tr data-element-type="Pure.Social.Profile.A.Row">'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column" data-addition-type="Title" colspan="3">'.
                                                '<p data-element-type="Pure.Social.Profile.A.Message.Title">'.$collection->name.'</p>'.
                                                (isset($collection->description) === true ? ($collection->description !== "" ? '<p data-element-type="Pure.Social.Profile.A.Message.Description">'.$collection->description.'</p>' : '') : '').
                                            '</td>'.
                                        '</tr>'.
                                        '<tr data-element-type="Pure.Social.Profile.A.Row">'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column" colspan="2" data-noborder><p data-element-type="Pure.Social.Profile.A.Message.Notice">'.__( 'Apply to all in group', 'pure' ).'</p></td>'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column" data-noborder data-addition-type="select">'.
                                                '<select data-engine-id="'.$id.'" data-engine-visibility-group="'.$group_mark.'" data-engine-basic-visibility-selector>'.
                                                    '<option value="nothing" selected></option>'.
                                                    '<option value="public">'.  __( 'Anybody',            'pure' ).'</option>'.
                                                    '<option value="loggedin">'.__( 'Registered users',   'pure' ).'</option>'.
                                                    '<option value="friends">'. __( 'Friends',            'pure' ).'</option>'.
                                                    '<option value="adminsonly">'. __( 'Hidden',             'pure' ).'</option>'.
                                                '</select>'.
                                            '</td>'.
                                        '</tr>';
                //echo var_dump($profile);
                foreach($collection->fields as $field_name=>$field){
                    $field_value = '';
                    if (isset($profile->BuddyPressProfile[$collection_name]->fields[$field_name]) === true){
                        $field_value = $profile->BuddyPressProfile[$collection_name]->fields[$field_name]->value;
                    }
                    $locked             = (isset($field->locked) === true ? ($field->locked === true ? ' disabled ' : '') : '');
                    $visibility         = (isset($field->allow_change_visibility) === true ? ($field->allow_change_visibility === false ? ' disabled ' : '') : '');
                    $popup_title        =   ($locked        !== '' ? __( 'This field cannot be changed.', 'pure' ) : '');
                    $popup_title        .=  ($popup_title   !== '' ? ' ' : '');
                    $popup_title        .=  ($visibility    !== '' ? __( 'Visibility of field cannot be changed.', 'pure' ) : '');
                    $field_visibility   = $profile->BuddyPressProfile[$collection_name]->fields[$field_name]->visibility;
                    $innerHTML .=       '<tr data-element-type="Pure.Social.Profile.A.Row">'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column"><p data-element-type="Pure.Social.Profile.A.Message.Normal" '.$locked.' '.($visibility !== '' ? ' out-of-control ' : '').' title="'.$popup_title.'">'.$field->name.'</p></td>'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column">'.
                                                $this->manageField($id, $field, $field_value, 'BuddyPressProfile').
                                                (isset($field->description) === true ? ($field->description !== "" ? '<p data-element-type="Pure.Social.Profile.A.Message.FieldDescription">'.$field->description.'</p>' : '') : '').
                                            '</td>'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column" data-addition-type="select">'.
                                                '<select '.$visibility.' data-engine-id="'.$id.'" data-engine-visibility-field-id="'.$field->id.'" data-engine-visibility-group="'.$group_mark.'">'.
                                                    '<option value="public" '.      ($field_visibility == 'public'     ? 'selected' : '').'>'.__( 'Anybody',            'pure' ).'</option>'.
                                                    '<option value="loggedin" '.    ($field_visibility == 'loggedin'   ? 'selected' : '').'>'.__( 'Registered users',   'pure' ).'</option>'.
                                                    '<option value="friends" '.     ($field_visibility == 'friends'    ? 'selected' : '').'>'.__( 'Friends',            'pure' ).'</option>'.
                                                    '<option value="adminsonly" '.  ($field_visibility == 'adminsonly' ? 'selected' : '').'>'.__( 'Hidden',             'pure' ).'</option>'.
                                                '</select>'.
                                            '</td>'.
                                        '</tr>';
                }
            }
            $innerHTML .=           '</table>'.
                                    '<p data-element-type="Pure.Social.Profile.A.Control.Button">'.
                                        '<a data-element-type="Pure.Social.Profile.A.Control.Button"'.
                                            'data-engine-id="'.$id.'" '.
                                            'data-engine-element="user_profile_update" '.
                                            'data-engine-data-user="'.$user_id.'" '.
                                            'data-engine-data-destination="'.get_site_url().'/request/" '.
                                            'data-engine-data-progress="D"'.
                                        '>'.__( 'Update', 'pure' ).'</a>'.
                                    '</p>';
            \Pure\Resources\Compressor::instance()->JS(Initialization::instance()->configuration->paths->js.'/'.'A.admin.js');
            //Attach progress bars
            \Pure\Templates\ProgressBar\Initialization::instance()->get("A");
            \Pure\Templates\ProgressBar\Initialization::instance()->get("D");
            //Attach uploader
            \Pure\Components\Uploader\Module\Initialization::instance()->attach();
            //Attach crop library
            \Pure\Components\Crop\Module\Initialization::instance()->attach();
            //Addition dialog
            \Pure\Components\Dialogs\B\Initialization::instance()->attach();
            return $innerHTML;
        }
        private function profileWithoutManage($profile){
            $innerHTML =            '<table data-element-type="Pure.Social.Profile.A.Table" border="0">'.
                                        '<tr data-element-type="Pure.Social.Profile.A.Row">'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column" data-addition-type="Title" colspan="2"><p data-element-type="Pure.Social.Profile.A.Message.Title">'.__( 'Basic information', 'pure' ).'</p></td>'.
                                        '</tr>';
            foreach($profile->WordPressBasic as $field){
                if ($field->value !== ''){
                    $innerHTML .=       '<tr data-element-type="Pure.Social.Profile.A.Row">'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column"><p data-element-type="Pure.Social.Profile.A.Message.Normal">'.__( $field->name, 'pure' ).'</p></td>'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column"><p data-element-type="Pure.Social.Profile.A.Message.Normal">'.$field->value.'</p></td>'.
                                        '</tr>';
                }
            }
            foreach($profile->BuddyPressProfile as $collection_name=>$collection){
                $isFields   = false;
                $_innerHTML =           '<tr data-element-type="Pure.Social.Profile.A.Row">'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column" data-addition-type="Title" colspan="2"><p data-element-type="Pure.Social.Profile.A.Message.Title">'.__( $collection->name, 'pure' ).'</p></td>'.
                                        '</tr>';
                foreach($collection->fields as $field){
                    if ($field->value !== ''){
                        $isFields = true;
                        if (in_array($field->type, array('selectbox', 'multiselectbox', 'radio', 'checkbox')) === true){
                            $value = unserialize($field->value);
                            $value = (count($value) === 1 ? $value[0] : $value);
                        }else{
                            $value = $field->value;
                        }
                        $_innerHTML .=  '<tr data-element-type="Pure.Social.Profile.A.Row">'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column"><p data-element-type="Pure.Social.Profile.A.Message.Normal">'.$field->name.'</p></td>'.
                                            '<td data-element-type="Pure.Social.Profile.A.Column">';
                        if (is_array($value) === true){
                            foreach($value as $_value){
                                $_innerHTML .=  '<p data-element-type="Pure.Social.Profile.A.Message.Normal">'.$_value.'</p>';
                            }
                        }else{
                            $_innerHTML .=  '<p data-element-type="Pure.Social.Profile.A.Message.Normal">'.$value.'</p>';
                        }
                        $_innerHTML .=      '</td>'.
                                        '</tr>';
                    }
                }
                if ($isFields === true){
                    $innerHTML .= $_innerHTML;
                }
            }
            $innerHTML .=           '</table>';
            return $innerHTML;
        }
        public function get($user_id, $parameters = false){
            $innerHTML = '';
            if (gettype($user_id) === 'integer'){
                $this->validate($parameters);
                \Pure\Components\BuddyPress\Profile\Initialization::instance()->attach();
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $Profile    = new \Pure\Components\BuddyPress\Profile\Core();
                $profile    = $Profile->get((object)array(
                    'id'    =>(int)$user_id,
                    'full'  =>true
                ));
                $current    = $WordPress->get_current_user();
                if ($profile !== false){
                    $id = uniqid('Profile');
                    if ($current === false){
                        $innerHTML = $this->profileWithoutManage($profile);
                    }else{
                        if ($parameters->manage === true && (int)$current->ID === (int)$user_id ){
                            $_profile   = $Profile->getCollections(true);
                            $innerHTML = $this->profileWithManage($user_id, $id, $profile, $_profile, $parameters);
                        }else{
                            $innerHTML = $this->profileWithoutManage($profile);
                        }
                    }
                }
                $WordPress  = NULL;
                $Profile    = NULL;
            }
            return $innerHTML;
        }
    }
}
?>