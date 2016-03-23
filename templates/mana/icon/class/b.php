<?php
namespace Pure\Templates\Mana\Icon{
    class B{
        private $has_permit = false;
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->user_id  ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->object   ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->object_id) !== false ? true : false));
                $parameters->field  = (isset($parameters->field ) !== false ? $parameters->field: ''    );//Free field for communication. Can be anything
                $parameters->data   = (isset($parameters->data  ) !== false ? $parameters->data : false );//It's cache
                return $result;
            }
            return false;
        }
        private function resources(){
            if (isset(\Pure\Configuration::instance()->globals->flags->PureManaIconBResource) === false){
                \Pure\Configuration::instance()->globals->flags->PureManaIconBResource = true;
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Dialogs\B\Initialization::instance()->attach(false, 'after');
                \Pure\Templates\ProgressBar\Initialization::instance()->attach_resources_of('A', false, 'after');
                //Define request settings
                require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->requests.'/settings/request.mana.icons.requests.php'));
                $Settings = new \Pure\Requests\Mana\Icons\Requests\Settings\Initialization();
                $Settings->init((object)array());
                $Settings = NULL;
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
                $permit     = ($data->object_type == 'post' ? $this->has_permit->post : ($data->object_type == 'comment' ? $this->has_permit->comment : ($data->object_type == 'activity' ? $this->has_permit->comment : ($data->object_type == 'image' ? $this->has_permit->comment : $this->has_permit->comment))));
                if ($data !== false){
                    if ($permit !== false){
                        $innerHTML = Initialization::instance()->html(
                            'B/member',
                            array(
                                array('value',      ((int)$data->plus - (int)$data->minus)  ),
                                array('object',     $parameters->object                     ),
                                array('object_id',  $parameters->object_id                  ),
                                array('field',      $parameters->field                      ),
                            )
                        );
                    }else{
                        $innerHTML = Initialization::instance()->html(
                            'B/not_member',
                            array(
                                array('value',      ((int)$data->plus - (int)$data->minus)  ),
                                array('object',     $parameters->object                     ),
                                array('object_id',  $parameters->object_id                  ),
                            )
                        );
                    }
                }
                $this->template();
            }
            return $innerHTML;
        }
        private function template(){
            $innerHTML = '';
            if (isset(\Pure\Configuration::instance()->globals->flags->PureManaIconBTemplate) === false){
                \Pure\Configuration::instance()->globals->flags->PureManaIconBTemplate = true;
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    $innerHTML  = Initialization::instance()->html(
                        'B/member',
                        array(
                            array('value',      '[value]'       ),
                            array('object',     '[object]'      ),
                            array('object_id',  '[object_id]'   ),
                            array('field',      '[field]'       ),
                        )
                    );
                }else{
                    $innerHTML  = Initialization::instance()->html(
                        'B/not_member',
                        array(
                            array('value',      '[value]'       ),
                            array('object',     '[object]'      ),
                            array('object_id',  '[object_id]'   ),
                            array('field',      '[field]'       ),
                        )
                    );
                }
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.mana.icon.templates.B',
                    base64_encode($innerHTML),
                    false,
                    true
                );
            }
            return $innerHTML;
        }
        public function markInnerHTML($object, $object_id, $field){
            $this->resources();
            $this->template();
            return Initialization::instance()->html(
                'B/mark',
                array(
                    array('object',     $object     ),
                    array('object_id',  $object_id  ),
                    array('field',      $field      ),
                )
            );
        }
        function __construct($add_template = true, $attach_resources = true){
            $this->has_permit       = $this->hasPermit();
        }
    }
}
?>