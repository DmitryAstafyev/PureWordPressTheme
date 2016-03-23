<?php
namespace Pure\Templates\Posts\Elements\Questions\Additions{
    class A{
        private $additions;
        private $parameters;
        private $author;
        private $current;
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post_id) !== false ? true : false));
                if ($result !== false){
                    $this->parameters   = $parameters;
                    $this->author       = get_post_field( 'post_author', $parameters->post_id);
                    $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                    $this->current      = $WordPress->get_current_user();
                    $WordPress          = NULL;
                    return true;
                }
            }
            return false;
        }
        private function attachTinyMCE(){
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
        private function resources(){
            $Attachments = \Pure\Templates\Elements\FileLoader\Initialization::instance()->get('A');
            $Attachments->attach(($this->current !== false ? ((int)$this->current->ID === (int)$this->author ? true : false) : false));
            $Attachments = NULL;
            if ($this->current !== false){
                if (isset(\Pure\Configuration::instance()->globals->flags->PureQuestionsAdditionsResources) === false){
                    \Pure\Configuration::instance()->globals->flags->PureQuestionsAdditionsResources = true;
                    \Pure\Templates\ProgressBar\        Initialization::instance()->get('A');
                    \Pure\Components\Dialogs\B\         Initialization::instance()->attach();
                    $this->attachTinyMCE();
                    //Settings
                    \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
                    \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                    $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.posts.elements.questions.additions.requests.direction',
                        $Requests->url,
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.posts.elements.questions.additions.requests.update',
                        'command'.      '='.'templates_of_questions_update_addition'.   '&'.
                        'post_id'.      '='.$this->parameters->post_id.                 '&'.
                        'addition_id'.  '='.'[addition_id]'.                            '&'.
                        'author_id'.    '='.$this->current->ID.                         '&'.
                        'content'.      '='.'[content]',
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.posts.elements.questions.additions.requests.remove',
                        'command'.      '='.'templates_of_questions_remove_addition'.   '&'.
                        'addition_id'.  '='.'[addition_id]',
                        false,
                        true
                    );
                    $Requests = NULL;
                }
            }
        }
        private function parseContent($activityValue){
            $value = preg_replace('/[\n\r\s]*$/', '', $activityValue);
            $value = preg_replace('/^[\n\r\s]*/', '', $value        );
            return $value;
        }
        private function getAdditions(){
            \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
            $Additions          = new \Pure\Components\PostTypes\Questions\Module\Additions();
            $this->additions    = $Additions->get($this->parameters->post_id);
            $Additions          = NULL;
            return (is_array($this->additions) === true ? true : false);
        }
        private function innerHTMLAddition($addition){
            if (is_array($addition->attachments) === false){
                $addition->attachments = array();
            }
            $Attachments            = \Pure\Templates\Elements\FileLoader\Initialization::instance()->get('A');
            $innerHTMLAttachments   = $Attachments->innerHTML(
                (object)array(
                    'object_id'     =>$addition->ID,
                    'object_type'   =>'addition',
                    'is_author'     => ($this->current !== false ? ((int)$this->current->ID === (int)$this->author ? true : false) : false)
                )
            );
            $Attachments            = NULL;
            $innerHTMLEditor = '';
            if ($this->current !== false){
                if ((int)$this->current->ID === (int)$this->author){
                    $innerHTMLEditor = Initialization::instance()->html(
                        'A/modify_editor',
                        array(
                            array('addition_id',    $addition->ID                                           ),
                            array('label_0',        __('edit', 'pure')                               ),
                            array('label_1',        __('Modify addition', 'pure')                    ),
                            array('label_2',        __('Update addition to your question', 'pure')   ),
                            array('label_3',        __('save', 'pure')                               ),
                            array('label_4',        __('cancel', 'pure')                             ),
                            array('label_5',        __('remove', 'pure')                             ),
                        )
                    );
                }
            }
            $innerHTML  = Initialization::instance()->html(
                'A/addition',
                array(
                    array('addition_id',    $addition->ID                                   ),
                    array('question_id',    $this->parameters->post_id                      ),
                    array('added_label',    __('added', 'pure')                      ),
                    array('added',          date('F j, Y', strtotime($addition->post_date)) ),
                    array('content',        $this->parseContent($addition->post_content)    ),
                    array('attachments',    $innerHTMLAttachments                           ),
                    array('editor',         $innerHTMLEditor                                ),
                )
            );
            return $innerHTML;
        }
        private function templates(){
            $innerHTMLEditor        = '';
            $innerHTMLAttachments   = '';
            if ($this->current !== false){
                if ((int)$this->current->ID === (int)$this->author){
                    if (isset(\Pure\Configuration::instance()->globals->flags->PureQuestionsAdditionsTemplate) === false){
                        \Pure\Configuration::instance()->globals->flags->PureQuestionsAdditionsTemplate = true;
                        $innerHTMLEditor = Initialization::instance()->html(
                            'A/modify_editor',
                            array(
                                array('addition_id',    '[addition_id]'                                         ),
                                array('label_0',        __('edit', 'pure')                               ),
                                array('label_1',        __('Modify addition', 'pure')                    ),
                                array('label_2',        __('Update addition to your question', 'pure')   ),
                                array('label_3',        __('save', 'pure')                               ),
                                array('label_4',        __('cancel', 'pure')                             ),
                                array('label_5',        __('remove', 'pure')                             ),
                            )
                        );
                        $Attachments            = \Pure\Templates\Elements\FileLoader\Initialization::instance()->get('A');
                        $innerHTMLAttachments   = $Attachments->innerHTMLContainerTemplate(
                            (object)array(
                                'object_id'     =>'[addition_id]',
                                'object_type'   =>'addition',
                                'is_author'     => ($this->current !== false ? ((int)$this->current->ID === (int)$this->author ? true : false) : false)
                            )
                        );
                    }
                }
            }
            $innerHTML  = Initialization::instance()->html(
                'A/addition',
                array(
                    array('addition_id',    '[addition_id]'             ),
                    array('question_id',    '[question_id]'             ),
                    array('added_label',    __('added', 'pure')  ),
                    array('added',          '[date]'                    ),
                    array('content',        '[content]'                 ),
                    array('attachments',    $innerHTMLAttachments       ),
                    array('editor',         $innerHTMLEditor            ),
                )
            );
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.posts.elements.questions.additions.templates.addition',
                base64_encode($innerHTML),
                false,
                true
            );
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false) {
                if ($this->getAdditions() !== false){
                    $innerHTMLAdditions = '';
                    if (count($this->additions) > 0){
                        foreach($this->additions as $addition){
                            $innerHTMLAdditions .= $this->innerHTMLAddition($addition);
                        }
                    }
                    $innerHTMLEditor = '';
                    if ($this->current !== false){
                        if ((int)$this->current->ID === (int)$this->author){
                            $innerHTMLEditor = Initialization::instance()->html(
                                'A/editor',
                                array(
                                    array('addition_id',    $parameters->post_id                                ),
                                    array('label_0',        __('write addition', 'pure')                 ),
                                    array('label_1',        __('Addition', 'pure')                       ),
                                    array('label_2',        __('New addition to your question', 'pure')  ),
                                )
                            );
                        }
                    }
                    $innerHTML = Initialization::instance()->html(
                        'A/wrapper',
                        array(
                            array('question_id',    $parameters->post_id            ),
                            array('label_0',        __('additions', 'pure')  ),
                            array('additions',      $innerHTMLAdditions             ),
                            array('editor',         $innerHTMLEditor                ),
                        )
                    );
                    $this->resources();
                    $this->templates();
                }
            }
            return $innerHTML;
        }
    }
}
?>