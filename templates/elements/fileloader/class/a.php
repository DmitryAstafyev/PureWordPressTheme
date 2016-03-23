<?php
namespace Pure\Templates\Elements\FileLoader{
    class A{
        private $current;
        private $parameters;
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->object_id    ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->object_type  ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->is_author    ) !== false ? true : false));
                if ($result !== false){
                    $this->parameters   = $parameters;
                    $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                    $this->current      = $WordPress->get_current_user();
                    $WordPress          = NULL;
                    return true;
                }
                return $result;
            }
            return false;
        }
        private function resources(){
            if (isset(\Pure\Configuration::instance()->globals->flags->PureFileLoaderResource) === false){
                \Pure\Configuration::instance()->globals->flags->PureFileLoaderResource = true;
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.elements.fileloader.requests.direction',
                    $Requests->url,
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.elements.fileloader.requests.request',
                    'command'.      '='.'templates_of_post_attachments_request'.     '&'.
                    'object_ids'.   '='.'[object_ids]'.                              '&'.
                    'object_types'. '='.'[object_types]',
                    false,
                    true
                );
                if ($current !== false){
                    //Get settings
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->attachments->properties;
                    $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
                    //Attach styles
                    \Pure\Templates\ProgressBar\        Initialization::instance()->get('B');
                    \Pure\Components\Dialogs\B\         Initialization::instance()->attach();
                    \Pure\Components\Uploader\Module\   Initialization::instance()->attach();
                    //Settings
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.elements.fileloader.requests.commands.add',
                        'templates_of_post_attachments_add',
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.elements.fileloader.nodes.input',
                        Initialization::instance()->html(
                            'A/input',
                            array()
                        ),
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.elements.fileloader.configuration.maxSize',
                        (int)$settings->max_size_attachment,
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.elements.fileloader.requests.remove',
                        'command'.      '='.'templates_of_post_attachments_remove'.     '&'.
                        'object_id'.    '='.'[object_id]'.                              '&'.
                        'object_type'.  '='.'[object_type]'.                            '&'.
                        'user_id'.      '='.$current->ID.                               '&'.
                        'url'.          '='.'[url]',
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.elements.fileloader.configuration.user_id',
                        $current->ID,
                        false,
                        true
                    );
                }
                $Requests = NULL;
            }
        }
        private function templates(){
            $innerHTMLRemoveButton  = Initialization::instance()->html(
                'A/remove_button',
                array(
                    array('object_id',          '[object_id]'                   ),
                    array('object_type',        '[object_type]'                 ),
                    array('attachment_id',      '[attachment_id]'               ),
                    array('file_name',          '[file_name]'                   ),
                    array('url',                '[url]'                         ),
                    array('label_2',            __('remove', 'pure')     ),
                )
            );
            $innerHTMLAttachment        = Initialization::instance()->html(
                'A/attachment',
                array(
                    array('object_id',          '[object_id]'                   ),
                    array('object_type',        '[object_type]'                 ),
                    array('attachment_id',      '[attachment_id]'               ),
                    array('file_name',          '[file_name]'                   ),
                    array('url',                '[url]'                         ),
                    array('label_1',            __('download', 'pure')   ),
                    array('remove',             $innerHTMLRemoveButton          ),
                )
            );
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.elements.fileloader.templates.attachment',
                base64_encode($innerHTMLAttachment),
                false,
                true
            );
        }
        public function attach(){
            $this->templates();
            $this->resources();
        }
        public function innerHTMLContainerTemplate($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false) {
                $innerHTML = Initialization::instance()->html(
                    'A/attachments',
                    array(
                        array('attachments',    ''                                  ),
                        array('object_id',      $parameters->object_id              ),
                        array('object_type',    $parameters->object_type            ),
                        array('label_0',        __('Attachments', 'pure')    ),
                    )
                );
            }
            return $innerHTML;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false) {
                $innerHTMLAttachments = '';
                \Pure\Components\PostAttachments\Module\Initialization::instance()->attach();
                $Attachments = new \Pure\Components\PostAttachments\Module\Provider();
                $attachments = $Attachments->get($parameters->object_id, $parameters->object_type);
                $Attachments = NULL;
                foreach($attachments as $attachment){
                    $attachment_id              = uniqid();
                    if ($parameters->is_author !== false){
                        $innerHTMLRemoveButton      = Initialization::instance()->html(
                            'A/remove_button',
                            array(
                                array('object_id',          $parameters->object_id          ),
                                array('object_type',        $parameters->object_type        ),
                                array('attachment_id',      $attachment_id                  ),
                                array('file_name',          $attachment->file_name          ),
                                array('url',                $attachment->url                ),
                                array('label_2',            __('remove', 'pure')     ),
                            )
                        );
                    }else{
                        $innerHTMLRemoveButton      = '';
                    }
                    $innerHTMLAttachments .= Initialization::instance()->html(
                        'A/attachment',
                        array(
                            array('object_id',          $parameters->object_id          ),
                            array('object_type',        $parameters->object_type        ),
                            array('attachment_id',      $attachment_id                  ),
                            array('file_name',          $attachment->file_name          ),
                            array('url',                $attachment->url                ),
                            array('label_1',            __('download', 'pure')   ),
                            array('remove',             $innerHTMLRemoveButton          ),
                        )
                    );
                }
                $innerHTML = Initialization::instance()->html(
                    'A/attachments',
                    array(
                        array('attachments',    $innerHTMLAttachments               ),
                        array('object_id',      $parameters->object_id              ),
                        array('object_type',    $parameters->object_type            ),
                        array('label_0',        __('Attachments', 'pure')    ),
                    )
                );
                $this->templates();
                $this->resources();
            }
            return $innerHTML;
        }
    }
}
?>