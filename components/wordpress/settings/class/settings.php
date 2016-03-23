<?php
namespace Pure\Components\WordPress\Settings{
    class Instance{
        public $settings = NULL;
        static private $self;
        static function instance(){
            if (!self::$self){
                self::$self = new self();
            }
            return self::$self;
        }
        function __construct(){
            $Settings       = new Settings();
            $this->settings = $Settings->load();
            $Settings       = NULL;
        }
        public function reload(){
            $Settings       = new Settings();
            $this->settings = $Settings->load();
            $Settings       = NULL;
        }
        public function tryToSaveFromPOST($section){
            $Settings       = new Settings();
            $result         = $Settings->tryToSaveFromPOST($section);
            $Settings       = NULL;
            return $result;
        }
        public function less($properties){
            $Settings       = new Settings();
            $properties     = $Settings->less($properties);
            $Settings       = NULL;
            return $properties;
        }
        public function initDefaults(){
            //Sandbox
            $settings = $this->less($this->settings->mana->properties);
            if ($settings->mana_threshold_manage_categories_sandbox === -1){
                $cat_id = wp_insert_term(
                    __('Sandbox', 'pure'),
                    'category',
                    array(
                        'description'   => __('Here is all posts of new members', 'pure'),
                        'slug'          => 'sandbox',
                    )
                );
                if (isset($cat_id['term_id']) !== false){
                    $cat_id = (int)$cat_id['term_id'];
                }else{
                    $cat_id = 0;
                }
                if ((int)$cat_id > 0){
                    $Settings = new Settings();
                    $Settings->try_save_by_name(
                        'mana',
                        'mana_threshold_manage_categories_sandbox',
                        $cat_id
                    );
                    $Settings = NULL;
                }
            }
            //Smiles
            update_option('use_smilies', '');
        }
    }
    class Settings{
        private $settings = NULL;
        private function validate($properties, $field){
            $_settings  = $this->defaults();
            foreach($_settings->$field->properties as $key=>$property){
                if (isset($properties->$key) === false){
                    return false;
                }
            }
            return true;
        }
        public function less($properties){
            foreach($properties as $key=>$property){
                if ($property !== false){
                    if (is_object($property) !== false){
                        $properties->$key = $property->value;
                    }
                }
            }
            return $properties;
        }
        public function load(){
            $_settings  = $this->defaults();
            $settings   = $this->defaults();
            foreach($settings as $key=>$section){
                $section->properties = get_option($section->id);
                $section->properties = ($section->properties                        === false ? $_settings->$key->properties : $section->properties);
                $section->properties = ($this->validate($section->properties, $key) === false ? $_settings->$key->properties : $section->properties);
            }
            //echo "<p>==========INFO!======".var_dump($_settings)."==========================</p>";
            $this->settings = $settings;
            return $this->settings;
        }
        public function save(){
            if(is_null($this->settings) === false){
                foreach($this->settings as $section){
                    update_option($section->id, $section->properties);
                }
            }
        }
        public function tryToSaveFromPOST($section){
            $_result = (object)array(
                'saved'     =>false,
                'message'   =>'',
            );
            if (isset($_POST["update_settings"]) === true){
                $result = $this->parse_and_save($section, $_POST);
                if ($result === true){
                    $_result->message   = '<div id="message" class="updated">Settings saved</div>';
                    $_result->saved     = true;
                } else if ($result === false){
                    $_result->message   = '<div id="message" class="error">Sorry. Some error during saving settings.</div>';
                    $_result->saved     = false;
                } else if (is_null($result) !== false){
                    $_result->message   = '';
                    $_result->saved     = false;
                }
            }
            return $_result;
        }
        public function parse_and_save($section, $data){
            $this->load();
            $settings   = $this->defaults();
            $no_fields  = true;
            if (isset($settings->$section) === true){
                $settings   = $settings->$section;
                $templates  = true;
                foreach($settings->properties as $property){
                    if (is_object($property) === true){
                        if (isset($data[$property->name]) !== false){
                            $property->value    = esc_attr($data[$property->name]);
                            $no_fields          = false;
                        }
                    }else{
                        if ($property === false){
                            $templates = true;
                        }
                    }
                }
                if ($no_fields === false){
                    if ($templates === true){
                        $TemplatesGlobalSettings = new \Pure\Templates\GlobalSettings();
                        $templatesGlobalSettings = $TemplatesGlobalSettings->get_from_POST($data);
                        if ($templatesGlobalSettings !== false){
                            foreach($templatesGlobalSettings as $key=>$templatesGlobalSetting){
                                if (isset($settings->properties->$key) === true){
                                    if ($settings->properties->$key === false){
                                        $settings->properties->$key = $templatesGlobalSetting;
                                    }
                                }
                            }
                        }
                    }
                    $this->settings->$section = $settings;
                    $this->save();
                    return true;
                }
                return NULL;
            }
            return false;
        }
        public function try_save_by_name($section, $name, $value){
            $this->load();
            if (isset($this->settings->$section) !== false){
                $settings = $this->settings->$section;
                if (isset($settings->properties->$name) !== false){
                    $settings->properties->$name->value = esc_attr($value);
                    $this->settings->$section           = $settings;
                    $this->save();
                    return true;
                }
            }
            return false;
        }
        private function defaults(){
            $Defaults   = new Defaults();
            $settings   = $Defaults->get();
            $Defaults   = NULL;
            $index      = 0;
            foreach($settings as $setting){
                foreach($setting->properties as $key=>$field){
                    if ($field !== false){
                        $field->name                = 'POSTFieldNameOfSettings_'.$index;
                        $setting->properties->$key  = $field;
                        $index++;
                    }
                }
            }
            return $settings;
        }
    }
}
?>