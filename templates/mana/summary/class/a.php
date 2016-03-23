<?php
namespace Pure\Templates\Mana\Summary{
    class A{
        private function getData($mana, $settings){
            $data = (object)array(
                'values'    =>(object)array(
                    'total'     =>0,
                    'zero'      =>0,
                    'sandbox'   =>(int)$settings->mana_threshold_manage_categories,
                    'user'      =>(int)$mana,
                ),
                'left'      =>(object)array(
                    'dark'      =>0,
                    'gray'      =>0,
                    'light'     =>0,
                    'user'      =>0,
                ),
                'width'     =>(object)array(
                    'dark'      =>0,
                    'gray'      =>0,
                    'light'     =>0,
                ),
                'settings'  =>(object)array(
                    'offset'        =>0.2,
                    'min_offset'    =>50,
                )
            );
            if ($data->values->user > 0){
                if ($data->values->user > $data->values->sandbox){
                    $data->values->total    =   $data->settings->min_offset +
                                                $data->values->user +
                                                ($data->settings->offset * $data->values->user > $data->settings->offset ? $data->settings->offset * $data->values->user : $data->settings->min_offset);
                }else{
                    $data->values->total    =   $data->settings->min_offset +
                                                $data->values->sandbox +
                                                ($data->settings->offset * $data->values->sandbox > $data->settings->offset ? $data->settings->offset * $data->values->sandbox : $data->settings->min_offset);
                }
                $data->values->zero         =   $data->settings->min_offset;
            }else{
                $data->values->total        =   ($data->settings->offset * (-$data->values->user)   > $data->settings->offset ? $data->settings->offset * (-$data->values->user)    : $data->settings->min_offset) +
                                                ($data->settings->offset * $data->values->sandbox   > $data->settings->offset ? $data->settings->offset * $data->values->sandbox    : $data->settings->min_offset) +
                                                (-$data->values->user)  +
                                                $data->values->sandbox;
                $data->values->zero         =   ($data->settings->offset * (-$data->values->user)   > $data->settings->offset ? $data->settings->offset * (-$data->values->user)    : $data->settings->min_offset) +
                                                (-$data->values->user);
            }
            $data->left->dark   = 0;
            $data->left->gray   = (($data->values->zero / $data->values->total) * 100).'%';
            $data->left->light  = ((($data->values->zero + $data->values->sandbox)  / $data->values->total) * 100).'%';
            $data->left->user   = ((($data->values->zero + $data->values->user)  / $data->values->total) * 100).'%';
            $data->width->dark  = (($data->values->zero / $data->values->total) * 100).'%';
            $data->width->gray  = (($data->values->sandbox / $data->values->total) * 100).'%';
            $data->width->light = ((($data->values->total - $data->values->sandbox - $data->values->zero) / $data->values->total) * 100).'%';
            return $data;
        }
        private function resources($id, $mana, $settings){
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.mana.summary.data.'.$id.'.sandbox',
                (int)$settings->mana_threshold_manage_categories,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.mana.summary.data.'.$id.'.wallet',
                (int)$mana,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.mana.summary.data.'.$id.'.offset',
                0.2,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.mana.summary.data.'.$id.'.min_offset',
                50,
                false,
                true
            );
        }
        private function resourcesGive($user_id, $current){
            \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
            \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.mana.summary.configuration.destination',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.mana.summary.configuration.request',
                'command'.      '=templates_of_mana_summary_give'.  '&'.
                'source'.       '='.$current->ID.                   '&'.
                'target'.       '='.$user_id.                       '&'.
                'value'.        '='.'[value]',
                false,
                true
            );
            $Requests = NULL;
            \Pure\Components\Dialogs\B\Initialization::instance()->attach();
            \Pure\Templates\ProgressBar\Initialization::instance()->get('A');
        }
        private function innerHTMLGive($user_id, $settings, $id){
            $innerHTML  = '';
            $WordPress  = new \Pure\Components\WordPress\UserData\Data();
            $current    = $WordPress->get_current_user();
            $WordPress  = NULL;
            if ($current !== false){
                if ((int)$current->ID !== (int)$user_id){
                    \Pure\Components\Relationships\Mana\Initialization::instance()->attach();
                    $Wallet = new \Pure\Components\Relationships\Mana\Wallet();
                    $wallet = $Wallet->get($current->ID);
                    $Wallet = NULL;
                    if ((int)$wallet > (int)$settings->mana_threshold_manage_categories){
                        $innerHTML  = Initialization::instance()->html(
                            'A/give',
                            array(
                                array('label_0', __('add karma to user', 'pure')),
                                array('label_1', __('how many karma you want give, maximum is ', 'pure').
                                    $settings->mana_maximum_gift),
                                array('label_2', __('You have ', 'pure').
                                    '<span data-engine-mana-summary-total="'.$id.'">'.
                                    $wallet.
                                    '</span> '.
                                    __('karma, but you can use only', 'pure').' '.
                                    '<span data-engine-mana-summary-available="'.$id.'">'.
                                    ((int)$wallet - (int)$settings->mana_threshold_manage_categories).
                                    '</span>. '.
                                    __('It means, that minimal value karma should be at least', 'pure').' '.
                                    $settings->mana_threshold_manage_categories.
                                    '.'),
                                array('label_3', __('give', 'pure')),
                                array('label_4', __('cancel', 'pure')),
                                array('id',     $id),
                                array('min',    1),
                                array('max',    $settings->mana_maximum_gift),
                            )
                        );
                        $this->resourcesGive($user_id, $current);
                    }
                }
            }
            return $innerHTML;
        }
        public function innerHTML($user_id){
            $innerHTML = '';
            if ((int)$user_id > 0){
                \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->settings->mana->properties;
                $settings   = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                \Pure\Components\Relationships\Mana\Initialization::instance()->attach();
                $Mana       = new \Pure\Components\Relationships\Mana\Provider();
                $mana       = $Mana->getForUser($user_id);
                $Mana       = NULL;
                if ($mana !== false && (int)$settings->mana_threshold_manage_categories_sandbox > 0){
                    $mana       = $mana->value;
                    $sandbox    = get_category((int)$settings->mana_threshold_manage_categories_sandbox);
                    if ($sandbox !== false){
                        $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                        $data       = $this->getData($mana, $settings);
                        $id         = uniqid();
                        $innerHTML  = Initialization::instance()->html(
                            'A/wrapper',
                            array(
                                array('sandbox',        $sandbox->name                              ),
                                array('member_left',    $data->left->user                           ),
                                array('name',           $WordPress->get_name((int)$user_id)         ),
                                array('dark_width',     $data->width->dark                          ),
                                array('gray_width',     $data->width->gray                          ),
                                array('light_width',    $data->width->light                         ),
                                array('dark_left',      $data->left->dark                           ),
                                array('gray_left',      $data->left->gray                           ),
                                array('light_left',     $data->left->light                          ),
                                array('sandbox_value',  $data->values->sandbox                      ),
                                array('user_value',     $data->values->user                         ),
                                array('give',           $this->innerHTMLGive($user_id, $settings, $id)   ),
                                array('id',             $id                                         ),
                            )
                        );
                        $WordPress  = NULL;
                        $this->resources($id, $mana, $settings);
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>