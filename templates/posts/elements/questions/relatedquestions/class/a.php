<?php
namespace Pure\Templates\Posts\Elements\Questions\RelatedQuestions{
    class A{
        private $parameters;
        private $author;
        private $current;
        private $question;
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post_id) !== false ? true : false));
                if ($result !== false){
                    \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
                    $this->parameters   = $parameters;
                    $this->author       = get_post_field( 'post_author', $parameters->post_id);
                    $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                    $this->current      = $WordPress->get_current_user();
                    $WordPress          = NULL;
                    $Questions          = new \Pure\Components\PostTypes\Questions\Module\Provider();
                    $this->question     = $Questions->get($parameters->post_id);
                    $Questions          = NULL;
                    return ($this->question !== false ? true : false);
                }
            }
            return false;
        }
        private function resources(){
            $Attachments = \Pure\Templates\Elements\FileLoader\Initialization::instance()->get('A');
            $Attachments->attach(($this->current !== false ? ((int)$this->current->ID === (int)$this->author ? true : false) : false));
            $Attachments = NULL;
            if ($this->current !== false){
                \Pure\Templates\ProgressBar\        Initialization::instance()->get('A');
                //Settings
                \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.relatedQuestions.requests.direction',
                    $Requests->url,
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.relatedQuestions.requests.add',
                    'command'.      '='.'templates_of_questions_add_related_question'.  '&'.
                    'question_id'.  '='.'[question_id]'.                                '&'.
                    'post_url'.     '='.'[post_url]',
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.relatedQuestions.requests.remove',
                    'command'.      '='.'templates_of_questions_remove_related_question'.   '&'.
                    'question_id'.  '='.'[question_id]'.                                    '&'.
                    'post_id'.      '='.'[post_id]',
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.relatedQuestions.icons.active',
                    base64_encode(Initialization::instance()->configuration->urls->images.'/A/active.png'),
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.relatedQuestions.icons.inactive',
                    base64_encode(Initialization::instance()->configuration->urls->images.'/A/inactive.png'),
                    false,
                    true
                );
                $Requests = NULL;
            }
        }
        private function innerHTMLSolution($question_id){
            \Pure\Components\PostTypes\Questions\Module\Initialization::instance()->attach();
            $Solutions      = new \Pure\Components\PostTypes\Questions\Module\Solutions();
            $has_solution   = $Solutions->hasAnswer($question_id);
            $Solutions      = NULL;
            $innerHTML      = Initialization::instance()->html(
                'A/solution',
                array(
                    array('icon', ($has_solution === false ?
                        Initialization::instance()->configuration->urls->images.'/A/inactive.png' :
                        Initialization::instance()->configuration->urls->images.'/A/active.png')),
                )
            );
            return $innerHTML;
        }
        private function template(){
            $Mana               = \Pure\Templates\Mana\Icon\Initialization::instance()->get('B');
            $innerHTMLManaMark  = $Mana->markInnerHTML(
                'question_related_question',
                '[post_id]',
                '[question_id]'
            );
            $Mana               = NULL;
            $innerHTMLSolution  = Initialization::instance()->html(
                'A/solution',
                array(
                    array('icon', '[icon]'),
                )
            );
            $innerHTMLPost      = Initialization::instance()->html(
                'A/post_thumbnail',
                array(
                    array('mana',               $innerHTMLManaMark                  ),
                    array('solution',           $innerHTMLSolution                  ),
                    array('question_id',        '[question_id]'                     ),
                    array('post_id',            '[post_id]'                         ),
                    array('post_title',         '[post_title]'                      ),
                    array('label_0',            __('by', 'pure')             ),
                    array('post_created',       '[post_created]'                    ),
                    array('label_1',            __('attached by', 'pure')    ),
                    array('post_attached_by',   '[post_attached_by]'                ),
                    array('post_author',        '[post_author]'                     ),
                    array('post_url',           '[post_url]'                        ),
                    array('remove',             ''                                  ),
                )
            );
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.posts.elements.questions.relatedQuestions.templates.post',
                base64_encode($innerHTMLPost),
                false,
                true
            );

        }
        private function innerHTMLPostRemove($related_post){
            $innerHTML = '';
            if ($this->current !== false){
                if ((int)$this->current->ID === (int)$this->author ||
                    (int)$this->current->ID === (int)$related_post->added_by){
                    if (isset($related_post->wait_for) === false){
                        $innerHTML = Initialization::instance()->html(
                            'A/remove',
                            array(
                                array('label_0',        __('remove', 'pure') ),
                                array('label_1',        __('Waiting for confirmation of removing', 'pure') ),
                                array('question_id',    $this->parameters->post_id  ),
                                array('post_id',        $related_post->post_id      ),
                            )
                        );
                    }else{
                        if ((int)$this->current->ID === (int)$related_post->wait_for){
                            $innerHTML = Initialization::instance()->html(
                                'A/remove',
                                array(
                                    array('label_0',        __('confirm removing', 'pure')   ),
                                    array('label_1',        __('Waiting for confirmation of removing', 'pure') ),
                                    array('question_id',    $this->parameters->post_id              ),
                                    array('post_id',        $related_post->post_id                  ),
                                )
                            );
                        }else{
                            $innerHTML = Initialization::instance()->html(
                                'A/remove_wait_for',
                                array(
                                    array('label_0',        __('Waiting for confirmation of removing', 'pure') ),
                                )
                            );
                        }
                    }
                }
            }
            return $innerHTML;
        }
        private function innerHTMLPost($related_post){
            $innerHTMLPosts = '';
            $Posts          = \Pure\Providers\Posts\Initialization::instance()->getCommon();
            $post           = $Posts->get($related_post->post_id);
            $Posts          = NULL;
            if ($post !== false){
                $WordPress          = new \Pure\Components\WordPress\UserData\Data();
                $Mana               = \Pure\Templates\Mana\Icon\Initialization::instance()->get('B');
                $innerHTMLMana      = $Mana->innerHTML(
                    (object)array(
                        'user_id'   =>$related_post->added_by,
                        'object'    =>'question_related_question',
                        'object_id' =>$related_post->post_id,
                        'field'     =>$this->parameters->post_id
                    )
                );
                $Mana               = NULL;
                $innerHTMLSolution  = $this->innerHTMLSolution($related_post->post_id);
                $innerHTMLPosts     = Initialization::instance()->html(
                    'A/post_thumbnail',
                    array(
                        array('mana',               $innerHTMLMana                                      ),
                        array('solution',           $innerHTMLSolution                                  ),
                        array('question_id',        $this->parameters->post_id                          ),
                        array('post_id',            $related_post->post_id                              ),
                        array('post_title',         $post->post->title                                  ),
                        array('label_0',            __('by', 'pure')                             ),
                        array('post_created',       date('F j, Y', strtotime($post->post->date))        ),
                        array('label_1',            __('attached by', 'pure')                    ),
                        array('post_attached_by',   $WordPress->get_name((int)$related_post->added_by)  ),
                        array('post_author',        $post->author->name                                 ),
                        array('post_url',           $post->post->url                                    ),
                        array('remove',             $this->innerHTMLPostRemove($related_post)           ),
                    )
                );
                $WordPress      = NULL;
            }
            return $innerHTMLPosts;
        }
        private function innerHTMLEditor(){
            $innerHTMLEditor = '';
            if ($this->current !== false){
                //if ((int)$this->current->ID !== (int)$this->author){
                    $innerHTMLEditor = Initialization::instance()->html(
                        'A/editor',
                        array(
                            array('label_0',        __('add question', 'pure')      ),
                            array('question_id',    $this->parameters->post_id         ),
                            array('label_1',        __('Related question', 'pure')  ),
                            array('label_2',        __('Place URL to answer here. If you know some similar question, would be great attach it.', 'pure') ),
                            array('label_3',        __('attach question', 'pure')   ),
                            array('label_4',        __('cancel', 'pure')        ),
                            array('label_5',        __('URL of question', 'pure')   ),
                        )
                    );
                //}
            }
            return $innerHTMLEditor;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false) {
                $innerHTMLPosts = '';
                foreach($this->question->questions as $question){
                    $innerHTMLPosts .= $this->innerHTMLPost($question);
                }
                $innerHTML      = Initialization::instance()->html(
                    'A/wrapper',
                    array(
                        array('label_0',        __('Related questions', 'pure')          ),
                        array('label_1',        __('No questions attached yet', 'pure')  ),
                        array('posts',          $innerHTMLPosts                                 ),
                        array('editor',         $this->innerHTMLEditor()                        ),
                        array('question_id',    $this->parameters->post_id                      ),
                    )
                );
                $this->resources();
                $this->template();
            }
            return $innerHTML;
        }
    }
}
?>