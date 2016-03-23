<?php
namespace Pure\Templates\Posts\Elements\Questions\RelatedPosts{
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
                    'pure.posts.elements.questions.relatedPosts.requests.direction',
                    $Requests->url,
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.relatedPosts.requests.add',
                    'command'.      '='.'templates_of_questions_add_related_post'.   '&'.
                    'question_id'.  '='.'[question_id]'.                            '&'.
                    'post_url'.     '='.'[post_url]',
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.relatedPosts.requests.remove',
                    'command'.      '='.'templates_of_questions_remove_related_post'.   '&'.
                    'question_id'.  '='.'[question_id]'.                                '&'.
                    'post_id'.      '='.'[post_id]',
                    false,
                    true
                );
                $Requests = NULL;
            }
        }
        private function template(){
            $Mana               = \Pure\Templates\Mana\Icon\Initialization::instance()->get('B');
            $innerHTMLManaMark  = $Mana->markInnerHTML(
                'question_related_post',
                '[post_id]',
                '[question_id]'
            );
            $Mana               = NULL;
            $Solution           = \Pure\Templates\Posts\Elements\Questions\Solution\Initialization::instance()->get('A');
            $innerHTMLSolution  = $Solution->innerHTML(
                (object)array(
                    'is_active'     =>'[is_active]',
                    'object'        =>'related_post',
                    'object_id'     =>'[object_id]',
                    'question_id'   =>'[question_id]',
                    'is_owner'      =>($this->current !== false ? ((int)$this->current->ID === (int)$this->author ? true : false) : false)
                )
            );
            $Solution           = NULL;
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
                    array('post_excerpt',       '[post_excerpt]'                    ),
                    array('post_url',           '[post_url]'                        ),
                    array('label_2',            __('read more', 'pure')      ),
                    array('remove',             ''                                  ),
                )
            );
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.posts.elements.questions.relatedPosts.templates.post',
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
                        'object'    =>'question_related_post',
                        'object_id' =>$related_post->post_id,
                        'field'     =>$this->parameters->post_id
                    )
                );
                $Mana               = NULL;
                $Solution           = \Pure\Templates\Posts\Elements\Questions\Solution\Initialization::instance()->get('A');
                $innerHTMLSolution  = $Solution->innerHTML(
                    (object)array(
                        'is_active'     =>(isset($related_post->is_active) === false ? false : $related_post->is_active),
                        'object'        =>'related_post',
                        'object_id'     =>$related_post->post_id,
                        'question_id'   =>$this->parameters->post_id,
                        'is_owner'      =>($this->current !== false ? ((int)$this->current->ID === (int)$this->author ? true : false) : false)
                    )
                );
                $Solution           = NULL;
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
                        array('post_excerpt',       $post->post->excerpt                                ),
                        array('post_url',           $post->post->url                                    ),
                        array('label_2',            __('read more', 'pure')                      ),
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
                            array('label_0',        __('add post', 'pure')      ),
                            array('question_id',    $this->parameters->post_id         ),
                            array('label_1',        __('Related post', 'pure')  ),
                            array('label_2',        __('Place URL to post here. You can attach to this question some post from site. Of course it should be about solution of problematic.', 'pure') ),
                            array('label_3',        __('attach post', 'pure')   ),
                            array('label_4',        __('cancel', 'pure')        ),
                            array('label_5',        __('URL of post', 'pure')   ),
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
                foreach($this->question->posts as $post){
                    $innerHTMLPosts .= $this->innerHTMLPost($post);
                }
                $innerHTML      = Initialization::instance()->html(
                    'A/wrapper',
                    array(
                        array('label_0',        __('Related posts', 'pure')          ),
                        array('label_1',        __('No posts attached yet', 'pure')  ),
                        array('posts',          $innerHTMLPosts                             ),
                        array('editor',         $this->innerHTMLEditor()                    ),
                        array('question_id',    $this->parameters->post_id                  ),
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