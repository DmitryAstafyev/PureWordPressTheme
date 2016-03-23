<?php
namespace Pure\Templates\Posts\Elements\Questions\Answers{
    class A{
        private $answers;
        private $parameters;
        private $author;
        private $current;
        private $settings;
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
                    \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
                    $this->settings     = \Pure\Components\WordPress\Settings\Instance::instance()->settings->comments->properties;
                    $this->settings     = \Pure\Components\WordPress\Settings\Instance::instance()->less($this->settings);
                    return true;
                }
            }
            return false;
        }
        private function getAnswers(){
            $Comments       = \Pure\Providers\Comments\Initialization::instance()->get('last_in_posts');
            $this->answers  = $Comments->get(
                array(
                    'from_date'     =>date('Y-m-d'),
                    'days'          =>9999,
                    'shown'         =>0,
                    'maxcount'      =>$this->settings->show_on_page,
                    'targets_array' =>array($this->parameters->post_id),
                    'add_post_data' =>false,
                    'add_user_data' =>true,
                    'add_excerpt'   =>false,
                    'add_DB_fields' =>false,
                    'make_tree'     =>true,
                )
            );
            $Comments = NULL;
            return ($this->answers !== false ? true : false);
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
            if (isset(\Pure\Configuration::instance()->globals->flags->PureQuestionsAnswersResources) === false){
                \Pure\Configuration::instance()->globals->flags->PureQuestionsAnswersResources = true;
                \Pure\Templates\ProgressBar\        Initialization::instance()->get('A');
                \Pure\Components\Dialogs\B\         Initialization::instance()->attach();
                //Settings
                \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.answers.requests.direction',
                    $Requests->url,
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.answers.requests.more',
                    'command'.      '='.'templates_of_questions_get_more_answer'.   '&'.
                    'post_id'.      '='.$this->parameters->post_id.                 '&'.
                    'shown'.        '='.'[shown]'.                                  '&'.
                    'all'.          '='.'[all]',
                    false,
                    true
                );
                $Requests = NULL;
                if ($this->current !== false){
                    $this->attachTinyMCE();
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.posts.elements.questions.answers.requests.update',
                        'command'.      '='.'templates_of_questions_update_answer'. '&'.
                        'post_id'.      '='.$this->parameters->post_id.             '&'.
                        'comment_id'.   '='.'[comment_id]'.                         '&'.
                        'parent_id'.    '='.'[parent_id]'.                          '&'.
                        'author_id'.    '='.$this->current->ID.                     '&'.
                        'content'.      '='.'[content]',
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.posts.elements.questions.answers.configuration.user_id',
                        $this->current->ID,
                        false,
                        true
                    );
                }
            }
        }
        private function parseContent($activityValue){
            $value = preg_replace('/[\n\r\s]*$/', '', $activityValue);
            $value = preg_replace('/^[\n\r\s]*/', '', $value        );
            return $value;
        }
        private function innerHTMLRootEditor($answer){
            $innerHTML  = '';
            if ($this->current !== false){
                $innerHTML  = Initialization::instance()->html(
                    'A/root_editor',
                    array(
                        array('answer_id',      $answer->comment->id                            ),
                        array('question_id',    $this->parameters->post_id                      ),
                    )
                );
            }
            return $innerHTML;
        }
        private function innerHTMLIncludedEditor($answer){
            $innerHTML  = '';
            if ($this->current !== false){
                $innerHTMLAttachmentButton = '';
                if ((int)$this->current->ID === (int)$answer->author->id){
                    $innerHTMLAttachmentButton  = Initialization::instance()->html(
                        'A/attachment_button',
                        array(
                            array('answer_id',      $answer->comment->id        ),
                            array('question_id',    $this->parameters->post_id  ),
                        )
                    );
                }
                $innerHTMLReplyButton  = Initialization::instance()->html(
                    'A/reply_button',
                    array(
                        array('answer_id',      $answer->comment->id        ),
                        array('question_id',    $this->parameters->post_id  ),
                        array('root_mark',      ''                          ),
                    )
                );
                $innerHTML  = Initialization::instance()->html(
                    'A/included_editor',
                    array(
                        array('answer_id',          $answer->comment->id                        ),
                        array('question_id',        $this->parameters->post_id                  ),
                        array('attachment_button',  $innerHTMLAttachmentButton                  ),
                        array('edit_button',        $this->innerHTMLEditButton($answer, false)  ),
                        array('reply_button',       $innerHTMLReplyButton                       ),
                    )
                );
            }
            return $innerHTML;
        }
        private function isSolution($answer_id){
            \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
            $Solutions  = new \Pure\Components\PostTypes\Questions\Module\Solutions();
            $result     = $Solutions->isAnswerSolution($this->parameters->post_id, $answer_id);
            $Solutions  = NULL;
            return $result;
        }
        private function innerHTMLEditButton($answer, $root = false){
            $innerHTML = '';
            if ($this->current !== false){
                if ((int)$this->current->ID === (int)$answer->author->id) {
                    $innerHTML  = Initialization::instance()->html(
                        'A/edit_button',
                        array(
                            array('answer_id',      $answer->comment->id        ),
                            array('question_id',    $this->parameters->post_id  ),
                        )
                    );
                }
            }
            return $innerHTML;
        }
        private function innerHTMLAnswer($answer, $root = false){
            if ($root !== false){
                $innerHTMLIncluded          = '';
                if (is_array($answer->children) !== false){
                    foreach ($answer->children as $included_answer){
                        $innerHTMLIncluded .= $this->innerHTMLAnswer($included_answer, false);
                    }
                }
                $innerHTMLAttachmentButton  = '';
                $innerHTMLReplyButton       = '';
                if ($this->current !== false){
                    if ((int)$this->current->ID === (int)$answer->author->id){
                        $innerHTMLAttachmentButton  = Initialization::instance()->html(
                            'A/attachment_button',
                            array(
                                array('answer_id',      $answer->comment->id        ),
                                array('question_id',    $this->parameters->post_id  ),
                            )
                        );
                    }
                    $innerHTMLReplyButton  = Initialization::instance()->html(
                        'A/reply_button',
                        array(
                            array('answer_id',      $answer->comment->id        ),
                            array('question_id',    $this->parameters->post_id  ),
                            array('root_mark',      'editor_'                   ),
                        )
                    );
                }
                $Mana               = \Pure\Templates\Mana\Icon\Initialization::instance()->get('B');
                $innerHTMLMana      = $Mana->innerHTML(
                    (object)array(
                        'user_id'   =>$answer->author->id,
                        'object'    =>'comment',
                        'object_id' =>$answer->comment->id,
                        'field'     =>$this->parameters->post_id
                    )
                );
                $Mana               = NULL;
                $Solution           = \Pure\Templates\Posts\Elements\Questions\Solution\Initialization::instance()->get('A');
                $innerHTMLSolution  = $Solution->innerHTML(
                    (object)array(
                        'is_active'     =>$this->isSolution($answer->comment->id),
                        'object'        =>'answer',
                        'object_id'     =>$answer->comment->id,
                        'question_id'   =>$this->parameters->post_id,
                        'is_owner'      =>($this->current !== false ? ((int)$this->current->ID === (int)$this->author ? true : false) : false)
                    )
                );
                $Solution               = NULL;
                $Attachments            = \Pure\Templates\Elements\FileLoader\Initialization::instance()->get('A');
                $innerHTMLAttachments   = $Attachments->innerHTML(
                    (object)array(
                        'object_id'     =>$answer->comment->id,
                        'object_type'   =>'comment',
                        'is_author'     => ($this->current !== false ? ((int)$this->current->ID === (int)$answer->author->id ? true : false) : false)
                    )
                );
                $Attachments            = NULL;
                $innerHTML              = Initialization::instance()->html(
                    'A/root',
                    array(
                        array('mana',               $innerHTMLMana                                      ),
                        array('solution',           $innerHTMLSolution                                  ),
                        array('answer_id',          $answer->comment->id                                ),
                        array('question_id',        $this->parameters->post_id                          ),
                        array('label_by',           __('by', 'pure')                             ),
                        array('created',            date('H:i, F j, Y', strtotime($answer->comment->date))   ),
                        array('content',            $this->parseContent($answer->comment->value)        ),
                        array('avatar',             $answer->author->avatar                             ),
                        array('label_included',     __('included', 'pure')                       ),
                        array('count_included',     (is_array($answer->children) !== false ? count($answer->children) : 0)  ),
                        array('author',             $answer->author->name                               ),
                        array('author_url',         $answer->author->home                               ),
                        array('included',           $innerHTMLIncluded                                  ),
                        array('editor',             $this->innerHTMLRootEditor($answer)                 ),
                        array('attachment_button',  $innerHTMLAttachmentButton                          ),
                        array('attachments',        $innerHTMLAttachments                               ),
                        array('edit_button',        $this->innerHTMLEditButton($answer, true)           ),
                        array('reply_button',       $innerHTMLReplyButton                               ),
                    )
                );
            }else{
                $Mana                   = \Pure\Templates\Mana\Icon\Initialization::instance()->get('A');
                $innerHTMLMana          = $Mana->innerHTML(
                    (object)array(
                        'user_id'   =>$answer->author->id,
                        'object'    =>'comment',
                        'object_id' =>$answer->comment->id,
                        'field'     =>$this->parameters->post_id
                    )
                );
                $Mana                   = NULL;
                $Attachments            = \Pure\Templates\Elements\FileLoader\Initialization::instance()->get('A');
                $innerHTMLAttachments   = $Attachments->innerHTML(
                    (object)array(
                        'object_id'     =>$answer->comment->id,
                        'object_type'   =>'comment',
                        'is_author'     => ($this->current !== false ? ((int)$this->current->ID === (int)$answer->author->id ? true : false) : false)
                    )
                );
                $Attachments            = NULL;
                $innerHTMLIncluded      = '';
                if (is_array($answer->children) !== false){
                    foreach ($answer->children as $included_answer){
                        $innerHTMLIncluded .= $this->innerHTMLAnswer($included_answer, false);
                    }
                }
                $innerHTML              = Initialization::instance()->html(
                    'A/included',
                    array(
                        array('mana',           $innerHTMLMana                                      ),
                        array('answer_id',      $answer->comment->id                                ),
                        array('question_id',    $this->parameters->post_id                          ),
                        array('label_by',       __('by', 'pure')                             ),
                        array('created',        date('H:i, F j, Y', strtotime($answer->comment->date))   ),
                        array('content',        $this->parseContent($answer->comment->value)        ),
                        array('avatar',         $answer->author->avatar                             ),
                        array('author',         $answer->author->name                               ),
                        array('author_url',     $answer->author->home                               ),
                        array('editor',         $this->innerHTMLIncludedEditor($answer)             ),
                        array('attachments',    $innerHTMLAttachments                               ),
                        array('included',       $innerHTMLIncluded                                  ),
                    )
                );
            }
            return $innerHTML;
        }
        private function innerHTMLAnswers(){
            $innerHTML = '';
            foreach($this->answers->comments as $answer){
                $innerHTML .= $this->innerHTMLAnswer($answer, true);
            }
            return $innerHTML;
        }
        private function templates(){
            if (isset(\Pure\Configuration::instance()->globals->flags->PureQuestionsAnswersTemplates) === false) {
                $Attachments            = \Pure\Templates\Elements\FileLoader\Initialization::instance()->get('A');
                $innerHTMLAttachments   = $Attachments->innerHTMLContainerTemplate(
                    (object)array(
                        'object_id'     =>'[answer_id]',
                        'object_type'   =>'comment',
                        'is_author'     => ($this->current !== false ? ((int)$this->current->ID === (int)$this->author ? true : false) : false)
                    )
                );
                \Pure\Configuration::instance()->globals->flags->PureQuestionsAnswersTemplates = true;
                $Mana               = \Pure\Templates\Mana\Icon\Initialization::instance()->get('B');
                $innerHTMLManaMarkB = $Mana->markInnerHTML(
                    'comment',
                    '[answer_id]',
                    '[question_id]'
                );
                $Mana               = \Pure\Templates\Mana\Icon\Initialization::instance()->get('A');
                $innerHTMLManaMarkA = $Mana->markInnerHTML(
                    'comment',
                    '[answer_id]',
                    '[question_id]'
                );
                $Mana                   = NULL;
                $Solution               = \Pure\Templates\Posts\Elements\Questions\Solution\Initialization::instance()->get('A');
                $innerHTMLSolution      = $Solution->innerHTML(
                    (object)array(
                        'is_active'     =>'[is_active]',
                        'object'        =>'answer',
                        'object_id'     =>'[answer_id]',
                        'question_id'   =>'[question_id]',
                        'is_owner'      =>($this->current !== false ? ((int)$this->current->ID === (int)$this->author ? true : false) : false)
                    )
                );
                $Solution                   = NULL;
                $innerHTMLEditButton        = '';
                $innerHTMLAttachmentButton  = '';
                $innerHTMLRootEditor        = '';
                $innerHTMLReplyButtonRoot   = '';
                $innerHTMLReplyButtonInc    = '';
                if ($this->current !== false){
                    $innerHTMLReplyButtonRoot  = Initialization::instance()->html(
                        'A/reply_button',
                        array(
                            array('answer_id',      '[answer_id]'                   ),
                            array('question_id',    '[question_id]'                 ),
                            array('root_mark',      'editor_'                       ),
                        )
                    );
                    $innerHTMLReplyButtonInc  = Initialization::instance()->html(
                        'A/reply_button',
                        array(
                            array('answer_id',      '[answer_id]'                   ),
                            array('question_id',    '[question_id]'                 ),
                            array('root_mark',      ''                              ),
                        )
                    );
                    $innerHTMLRootEditor        = Initialization::instance()->html(
                        'A/root_editor',
                        array(
                            array('answer_id',          '[answer_id]'                   ),
                            array('question_id',        '[question_id]'                 ),
                        )
                    );
                    $innerHTMLAttachmentButton  = Initialization::instance()->html(
                        'A/attachment_button',
                        array(
                            array('answer_id',          '[answer_id]'                   ),
                            array('question_id',        '[question_id]'                 ),
                        )
                    );
                    $innerHTMLEditButton        = Initialization::instance()->html(
                        'A/edit_button',
                        array(
                            array('answer_id',      '[answer_id]'                           ),
                            array('question_id',    '[question_id]'                         ),
                        )
                    );
                }
                $innerHTMLRoot      = Initialization::instance()->html(
                    'A/root',
                    array(
                        array('mana',               $innerHTMLManaMarkB                 ),
                        array('solution',           $innerHTMLSolution                  ),
                        array('answer_id',          '[answer_id]'                       ),
                        array('question_id',        '[question_id]'                     ),
                        array('label_by',           __('by', 'pure')             ),
                        array('created',            '[created]'                         ),
                        array('content',            '[content]'                         ),
                        array('avatar',             '[avatar]'                          ),
                        array('label_included',     __('included', 'pure')       ),
                        array('count_included',     ''                  ),
                        array('author',             '[author]'                          ),
                        array('author_url',         '[author_url]'                      ),
                        array('included',           ''                                  ),
                        array('editor',             $innerHTMLRootEditor                ),
                        array('attachment_button',  $innerHTMLAttachmentButton          ),
                        array('attachments',        $innerHTMLAttachments               ),
                        array('edit_button',        $innerHTMLEditButton                ),
                        array('reply_button',       $innerHTMLReplyButtonRoot           ),
                    )
                );
                $innerHTMLIncludedEditor  = Initialization::instance()->html(
                    'A/included_editor',
                    array(
                        array('answer_id',          '[answer_id]'                       ),
                        array('question_id',        '[question_id]'                     ),
                        array('attachment_button',  $innerHTMLAttachmentButton          ),
                        array('reply_button',       $innerHTMLReplyButtonInc            ),
                    )
                );
                $innerHTMLIncluded      = Initialization::instance()->html(
                    'A/included',
                    array(
                        array('mana',               $innerHTMLManaMarkA                 ),
                        array('answer_id',          '[answer_id]'                       ),
                        array('question_id',        '[question_id]'                     ),
                        array('label_by',           __('by', 'pure')             ),
                        array('created',            '[created]'                         ),
                        array('content',            '[content]'                         ),
                        array('avatar',             '[avatar]'                          ),
                        array('author',             '[author]'                          ),
                        array('author_url',         '[author_url]'                      ),
                        array('editor',             $innerHTMLIncludedEditor            ),
                        array('attachments',        $innerHTMLAttachments               ),
                        array('edit_button',        $innerHTMLEditButton                ),
                        array('included',           ''                                  ),
                    )
                );
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.answers.templates.root',
                    base64_encode($innerHTMLRoot),
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.answers.templates.included',
                    base64_encode($innerHTMLIncluded),
                    false,
                    true
                );
            }
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false) {
                $this->resources();
                $this->templates();
                if ($this->getAnswers() !== false){
                    $innerHTMLEditor = '';
                    if ($this->current !== false){
                        if ((int)$this->current->ID !== (int)$this->author){
                            $innerHTMLEditor = Initialization::instance()->html(
                                'A/editor',
                                array(
                                    array('question_id',    $parameters->post_id                                ),
                                    array('label_0',        __('write answer', 'pure')                   ),
                                    array('label_1',        __('Answer', 'pure')                         ),
                                    array('label_2',        __('Notice, you can add only one answer to one question.', 'pure')    ),
                                )
                            );
                        }
                    }
                    $innerHTMLAnswers   = $this->innerHTMLAnswers();
                    $innerHTML          = Initialization::instance()->html(
                        'A/wrapper',
                        array(
                            array('question_id',    $parameters->post_id            ),
                            array('label_0',        __('answers', 'pure')    ),
                            array('label_1',        __('answers', 'pure')    ),
                            array('editor',         $innerHTMLEditor                ),
                            array('answers',        $innerHTMLAnswers               ),
                            array('shown',          $this->answers->shown           ),
                            array('total',          $this->answers->total           ),
                        )
                    );
                }
            }
            return $innerHTML;
        }
    }
}
?>