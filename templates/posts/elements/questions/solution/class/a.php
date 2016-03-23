<?php
namespace Pure\Templates\Posts\Elements\Questions\Solution{
    class A{
        private $additions;
        private $parameters;
        private $author;
        private $current;
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->object       ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->object_id    ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->question_id  ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->is_owner     ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->is_active    ) !== false ? true : false));
                if ($result !== false){
                    $this->parameters   = $parameters;
                    return true;
                }
            }
            return false;
        }
        private function resources(){
            if ($this->parameters->is_owner !== false){
                if (isset(\Pure\Configuration::instance()->globals->flags->PureQuestionsSolutionResources) === false) {
                    \Pure\Configuration::instance()->globals->flags->PureQuestionsSolutionResources = true;
                    \Pure\Templates\ProgressBar\        Initialization::instance()->get('A');
                    \Pure\Components\Dialogs\B\         Initialization::instance()->attach();
                    //Settings
                    \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
                    \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                    $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.posts.elements.questions.solution.requests.direction',
                        $Requests->url,
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.posts.elements.questions.solution.requests.set',
                        'command'.      '='.'templates_of_questions_solution_set'.  '&'.
                        'question_id'.  '='.'[question_id]'.                        '&'.
                        'object_id'.    '='.'[object_id]'.                          '&'.
                        'object_type'.  '='.'[object_type]',
                        false,
                        true
                    );
                    $Requests = NULL;
                }
            }
        }
        private function template(){
            $innerHTML = '';
            if (isset(\Pure\Configuration::instance()->globals->flags->PureQuestionsSolutionTemplate) === false) {
                \Pure\Configuration::instance()->globals->flags->PureQuestionsSolutionTemplate = true;
                if ($this->parameters->is_owner !== false){
                    $innerHTML = Initialization::instance()->html(
                        'A/owner',
                        array(
                            array('icon_current',   '[is_active]'                                                           ),
                            array('object_id',      '[object_id]'                                                           ),
                            array('question_id',    '[question_id]'                                                           ),
                            array('object_type',    '[object]'                                                              ),
                            array('label_0',        __('Is it solution for your question or not?', 'pure')           ),
                            array('label_1',        __('yes', 'pure')                                                ),
                            array('label_2',        __('no', 'pure')                                                 ),
                        )
                    );
                }else{
                    $innerHTML = Initialization::instance()->html(
                        'A/member',
                        array(
                            array('icon_current',   '[is_active]'                                                           ),
                            array('object_id',      '[object_id]'                                                           ),
                            array('question_id',    '[question_id]'                                                         ),
                            array('object_type',    '[object]'                                                              ),
                        )
                    );
                }
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.solution.template',
                    base64_encode($innerHTML),
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.solution.icons.active',
                    base64_encode(Initialization::instance()->configuration->urls->images.'/a/active.png'),
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.questions.solution.icons.inactive',
                    base64_encode(Initialization::instance()->configuration->urls->images.'/a/inactive.png'),
                    false,
                    true
                );
            }
            return $innerHTML;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false) {
                if ($parameters->is_owner !== false){
                    $innerHTML = Initialization::instance()->html(
                        'A/owner',
                        array(
                            array('icon_current',   ($parameters->is_active !== false ?
                                                    Initialization::instance()->configuration->urls->images.'/a/active.png' :
                                                    Initialization::instance()->configuration->urls->images.'/a/inactive.png')  ),
                            array('object_id',      $parameters->object_id                                                      ),
                            array('question_id',    $parameters->question_id                                                    ),
                            array('object_type',    $parameters->object                                                         ),
                            array('label_0',        __('Is it solution for your question or not?', 'pure')               ),
                            array('label_1',        __('yes', 'pure')                                                    ),
                            array('label_2',        __('no', 'pure')                                                     ),
                        )
                    );
                }else{
                    $innerHTML = Initialization::instance()->html(
                        'A/member',
                        array(
                            array('icon_current',   ($parameters->is_active !== false ?
                                                    Initialization::instance()->configuration->urls->images.'/a/active.png' :
                                                    Initialization::instance()->configuration->urls->images.'/a/inactive.png')   ),
                            array('object_id',      $parameters->object_id                                                      ),
                            array('object_type',    $parameters->object                                                         ),
                        )
                    );
                }
                $this->resources();
                $this->template();
            }
            return $innerHTML;
        }
    }
}
?>