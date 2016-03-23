<?php
namespace Pure\Templates\Mana\Icon{
    class A{
        private $resources_flag = false;
        private $template_flag  = false;
        private $has_permit     = false;
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->user_id  ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->object   ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->object_id) !== false ? true : false));
                $parameters->field  = (isset($parameters->field ) !== false ? $parameters->field: false );//Free field for communication. Can be anything
                $parameters->data   = (isset($parameters->data  ) !== false ? $parameters->data : false );//It's cache
                return $result;
            }
            return false;
        }
        private function resources(){
            if ($this->resources_flag === false){
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addINIT(
                    'pure.mana.icon.A',
                    false,
                    true
                );
                \Pure\Components\Dialogs\B\Initialization::instance()->attach(false, 'after');
                \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('A', false, 'after');
                //Define request settings
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/settings/request.mana.icons.requests.php'));
                $Settings = new \Pure\Requests\Mana\Icons\Requests\Settings\Initialization();
                $Settings->init((object)array());
                $Settings = NULL;
                $this->resources_flag = true;
            }
        }
        private function getData($parameters){
            $loadFromDataBase = function($parameters){
                \Pure\Components\Relationships\Mana\Initialization::instance()->attach(true);
                $Provider   = new \Pure\Components\Relationships\Mana\Provider();
                $data       = $Provider->getForObjects(
                    (object)array(
                        'IDs'   =>array($parameters->object_id),
                        'object'=>$parameters->object
                    )
                );
                if ($data !== false){
                    if (count($data) !== 1){
                        $data = $Provider->getDefault(
                            $parameters->user_id,
                            $parameters->object,
                            $parameters->object_id
                        );
                    }else{
                        $data = $data[0];
                    }
                }
                $Provider   = NULL;
                return $data;
            };
            if ($parameters->data !== false){
                if (isset($parameters->data[$parameters->object_id]) !== false){
                    $data = $parameters->data[$parameters->object_id];
                }else{
                    $data = $loadFromDataBase($parameters);
                }
            }else{
                $data = $loadFromDataBase($parameters);
            }
            return $data;
        }
        private function hasPermit(){
            \Pure\Components\Relationships\Mana\Initialization::instance()->attach(true);
            $ManaProvider   = new \Pure\Components\Relationships\Mana\Provider();
            $allows         = (object)array(
                'post'      =>$ManaProvider->hasPermit('allow_vote', 'post'     ),
                'comment'   =>$ManaProvider->hasPermit('allow_vote', 'comment'  )
            );
            $ManaProvider   = NULL;
            return $allows;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $this->resources();
                $data       = $this->getData($parameters);
                $id         = uniqid();
                $permit     = ($data->object_type == 'post' ? $this->has_permit->post : ($data->object_type == 'comment' ? $this->has_permit->comment : ($data->object_type == 'activity' ? $this->has_permit->comment : ($data->object_type == 'image' ? $this->has_permit->comment : true))));
                if ($data !== false){
                    $innerHTML .=       '<!--BEGIN: Mana.Icon.A -->'.
                                        '<div data-post-element-type="Pure.Mana.Icon.A.Container" '.
                                            'data-engine-mana-element="Container" '.
                                            'data-engine-mana-objectID="'.$data->object_id.'" '.
                                            'data-engine-mana-object="'.$data->object_type.'" '.
                                            'data-engine-mana-field="'.($parameters->field !== false ? $parameters->field : '').'" '.
                                        '>';
                    if ($permit !== false){
                        $innerHTML .=       '<label for="mana.A.'.$id.'">'.
                                                '<input data-post-element-type="Pure.Mana.Icon.A.Switcher" type="checkbox" name="Pure.Mana.Icon.A.Switcher" id="mana.A.'.$id.'"/>';
                    }
                    $innerHTML .=               '<div data-post-element-type="Pure.Mana.Icon.A.Switcher">'.
                                                    '<canvas data-post-element-type="Pure.Mana.Icon.A.Switcher" '.
                                                            'data-engine-mana-element="Switcher" '.
                                                            'data-engine-mana-plus="'.(int)$data->plus.'" '.
                                                            'data-engine-mana-minus="'.(int)$data->minus.'" '.
                                                            'data-engine-mana-canvas-width="3em" '.
                                                            'data-engine-mana-canvas-height="3em" '.
                                                            'data-engine-mana-color-plus="rgb(50, 150, 0)" '.
                                                            'data-engine-mana-color-minus="rgb(150, 50, 0)" '.
                                                            'width="64" height="64">'.
                                                    '</canvas>'.
                                                    '<p data-post-element-type="Pure.Mana.Icon.A.Switcher" data-engine-mana-element="Label.Total">'.((int)$data->plus - (int)$data->minus).'</p>'.
                                                '</div>';
                    if ($permit !== false){
                        $innerHTML .=           '<div data-post-element-type="Pure.Mana.Icon.A.Controls">'.
                                                    '<a data-post-element-type="Pure.Mana.Icon.A.Controls.Button" data-button-type="Minus" data-engine-mana-element="Button.Minus"></a>'.
                                                    '<div data-post-element-type="Pure.Mana.Icon.A.Controls.Labels">'.
                                                        '<p data-post-element-type="Pure.Mana.Icon.A.Controls.Labels.Total" data-engine-mana-element="Label.Total">'.((int)$data->plus - (int)$data->minus).'</p>'.
                                                        '<p data-post-element-type="Pure.Mana.Icon.A.Controls.Labels.Plus" data-engine-mana-element="Label.Plus">'.(int)$data->plus.'</p>'.
                                                        '<p data-post-element-type="Pure.Mana.Icon.A.Controls.Labels.Minus" data-engine-mana-element="Label.Minus">'.(int)$data->minus.'</p>'.
                                                    '</div>'.
                                                    '<a data-post-element-type="Pure.Mana.Icon.A.Controls.Button" data-button-type="Plus" data-engine-mana-element="Button.Plus"></a>'.
                                                '</div>';
                    }
                    if ($permit !== false){
                        $innerHTML .=       '</label>';
                    }
                    $innerHTML .=       '</div>'.
                                        '<!--END: Mana.Icon.A -->';
                    $innerHTML .=       $this->templateInnerHTML();
                }
            }
            return $innerHTML;
        }
        private function templateInnerHTML(){
            $innerHTML = '';
            if ($this->template_flag === false){
                $Template   = new ATemplate();
                $innerHTML  = $Template->innerHTML(
                    (object)array(
                        'has_permit'=>$this->has_permit->comment
                    )
                );
                $Template   = NULL;
                $this->template_flag = true;
            }
            return $innerHTML;
        }
        public function markInnerHTML($object, $object_id){
            $Template   = new ATemplate();
            $innerHTML  = $Template->mark($object, $object_id);
            $Template   = NULL;
            return $innerHTML;
        }
        function __construct($add_template = true, $attach_resources = true){
            $this->has_permit       = $this->hasPermit();
            $this->template_flag    = ($add_template        !== false ? false : true);
            $this->resources_flag   = ($attach_resources    !== false ? false : true);
        }
    }
    class ATemplate{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->has_permit   ) !== false ? true : false));
                return $result;
            }
            return false;
        }
        public function mark($object, $object_id){
            return  '<div data-post-element-type="Pure.Mana.Icon.A.Mark" '.
                        'data-engine-mana-element="Mark" '.
                        'data-engine-mana-objectID="'.$object_id.'" '.
                        'data-engine-mana-object="'.$object.'">'.
                    '</div>';
        }
        /*
         * FIELDS
         * [object_id]
         * [object_type]
         * [plus]
         * [minus]
         * [total]
         * [field]
         * */
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $innerHTML .=       '<div data-post-element-type="Pure.Mana.Icon.A.Container" '.
                                            'data-engine-mana-element="Container.Template" '.
                                            'data-engine-mana-objectID="[object_id]" '.
                                            'data-engine-mana-object="[object_type]" '.
                                            'data-engine-mana-field="[field]" '.
                                            'style="display:none;">';
                if ($parameters->has_permit !== false){
                    $innerHTML .=       '<label for="mana.A.[object_id]">'.
                                            '<input data-post-element-type="Pure.Mana.Icon.A.Switcher" type="checkbox" name="Pure.Mana.Icon.A.Switcher" id="mana.A.[object_id]"/>';
                }
                $innerHTML .=               '<div data-post-element-type="Pure.Mana.Icon.A.Switcher">'.
                                                '<canvas data-post-element-type="Pure.Mana.Icon.A.Switcher" '.
                                                        'data-engine-mana-element="Switcher" '.
                                                        'data-engine-mana-plus="[plus]" '.
                                                        'data-engine-mana-minus="[minus]" '.
                                                        'data-engine-mana-canvas-width="3em" '.
                                                        'data-engine-mana-canvas-height="3em" '.
                                                        'data-engine-mana-color-plus="rgb(50, 150, 0)" '.
                                                        'data-engine-mana-color-minus="rgb(150, 50, 0)" '.
                                                        'width="64" height="64">'.
                                                '</canvas>'.
                                                '<p data-post-element-type="Pure.Mana.Icon.A.Switcher" data-engine-mana-element="Label.Total">[total]</p>'.
                                            '</div>';
                if ($parameters->has_permit !== false){
                    $innerHTML .=           '<div data-post-element-type="Pure.Mana.Icon.A.Controls">'.
                                                '<a data-post-element-type="Pure.Mana.Icon.A.Controls.Button" data-button-type="Minus" data-engine-mana-element="Button.Minus"></a>'.
                                                '<div data-post-element-type="Pure.Mana.Icon.A.Controls.Labels">'.
                                                    '<p data-post-element-type="Pure.Mana.Icon.A.Controls.Labels.Total" data-engine-mana-element="Label.Total">[total]</p>'.
                                                    '<p data-post-element-type="Pure.Mana.Icon.A.Controls.Labels.Plus" data-engine-mana-element="Label.Plus">[plus]</p>'.
                                                    '<p data-post-element-type="Pure.Mana.Icon.A.Controls.Labels.Minus" data-engine-mana-element="Label.Minus">[minus]</p>'.
                                                '</div>'.
                                                '<a data-post-element-type="Pure.Mana.Icon.A.Controls.Button" data-button-type="Plus" data-engine-mana-element="Button.Plus"></a>'.
                                            '</div>';
                }
                if ($parameters->has_permit !== false){
                    $innerHTML .=       '</label>';
                }
                $innerHTML .=       '</div>';
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.mana.icon.templates.A',
                    base64_encode($innerHTML),
                    false,
                    true
                );
            }
            return $innerHTML;
        }
    }
}
?>