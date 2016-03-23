<?php
namespace Pure\Templates\Reports\Indexes{
    class A{
        public function get($post_id){
            $innerHTML = '';
            if ((int)$post_id > 0) {
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    $innerHTML = $this->getForVote($post_id);
                }else{
                    $innerHTML = $this->getAsInformation($post_id);
                }
            }
            return $innerHTML;
        }
        public function getAsInformation($post_id){
            $innerHTML = '';
            if ((int)$post_id > 0){
                \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
                $Reports    = new \Pure\Components\PostTypes\Reports\Module\Provider();
                $indexes    = $Reports->get((int)$post_id);
                $Reports    = NULL;
                if ($indexes !== false){
                    foreach($indexes->indexes as $key=>$index){
                        $innerHTML  .= Initialization::instance()->html(
                            'A/info_line',
                            array(
                                array('name',           $index                                                          ),
                                array('current',        number_format($indexes->votes[$key], 2)                         ),
                                array('max',            $indexes->max[$key]                                             ),
                                array('rate',           ((int)$indexes->votes[$key] / (int)$indexes->max[$key])*100     ),
                                array('index',          $key                                                            ),
                                array('post_id',        $post_id                                                        ),
                                array('author_rate',    (isset($indexes->author_votes[$key]) !== false ? ((int)$indexes->author_votes[$key] / (int)$indexes->max[$key])*100 : 0)),
                                array('author_value',   (isset($indexes->author_votes[$key]) !== false ? __('Author vote is', 'pure').': '.(int)$indexes->author_votes[$key] : __('Author not voted yet', 'pure'))),
                            )
                        );
                    }
                }
            }
            return $innerHTML;
        }
        private function initVote($user_id){
            if (isset(\Pure\Configuration::instance()->globals->flags->report_variables) === false){
                \Pure\Templates\ProgressBar\Initialization                  ::instance()->get('B');
                //Define variables
                \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
                \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
                $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.reports.configuration.destination',
                    $Requests->url,
                    false,
                    true
                );
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.reports.configuration.request.vote',
                    'command'.      '=templates_of_reports_vote'.       '&'.
                    'user_id'.      '='.$user_id.                       '&'.
                    'post_id'.      '='.'[post_id]'.                    '&'.
                    'index'.        '='.'[index]'.                      '&'.
                    'value'.        '='.'[value]',
                    false,
                    true
                );
                \Pure\Configuration::instance()->globals->flags->report_variables = true;
            }
        }
        public function getForVote($post_id){
            $innerHTML = '';
            if ((int)$post_id > 0){
                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                $current    = $WordPress->get_current_user();
                $WordPress  = NULL;
                if ($current !== false){
                    \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
                    $Reports    = new \Pure\Components\PostTypes\Reports\Module\Provider();
                    $indexes    = $Reports->get((int)$post_id);
                    if ($indexes !== false){
                        foreach($indexes->indexes as $key=>$index){
                            if ($Reports->isUserVoted((int)$post_id, (int)$key, (int)$current->ID) === false){
                                $innerHTMLSegments = '';
                                for($part = $indexes->max[$key]; $part >= $indexes->min[$key]; $part -= 1){
                                    $innerHTMLSegments .= '<div data-element-type="Pure.Report.InPost.A.Index.Part" data-report-segment-value="'.$part.'"><p>'.$part.'</p></div>';
                                }
                                $innerHTML  .= Initialization::instance()->html(
                                    'A/vote_line',
                                    array(
                                        array('name',       $index                                                          ),
                                        array('current',    number_format($indexes->votes[$key], 2)                         ),
                                        array('max',        $indexes->max[$key]                                             ),
                                        array('segments',   $innerHTMLSegments                                              ),
                                        array('index',      $key                                                            ),
                                        array('post_id',    $post_id                                                        ),
                                        array('rate',       ((int)$indexes->votes[$key] / (int)$indexes->max[$key])*100     ),
                                        array('author_rate',    (isset($indexes->author_votes[$key]) !== false ? ((int)$indexes->author_votes[$key] / (int)$indexes->max[$key])*100 : 0)),
                                        array('author_value',   (isset($indexes->author_votes[$key]) !== false ? __('Author vote is', 'pure').': '.(int)$indexes->author_votes[$key] : __('Author not voted yet', 'pure'))),
                                    )
                                );
                                $this->initVote($current->ID);
                            }else{
                                $innerHTML  .= Initialization::instance()->html(
                                    'A/info_line',
                                    array(
                                        array('name',       $index                                                          ),
                                        array('current',    number_format($indexes->votes[$key], 2)                         ),
                                        array('max',        $indexes->max[$key]                                             ),
                                        array('rate',       ((int)$indexes->votes[$key] / (int)$indexes->max[$key])*100     ),
                                        array('index',      $key                                                            ),
                                        array('post_id',    $post_id                                                        ),
                                        array('author_rate',    (isset($indexes->author_votes[$key]) !== false ? ((int)$indexes->author_votes[$key] / (int)$indexes->max[$key])*100 : 0)),
                                        array('author_value',   (isset($indexes->author_votes[$key]) !== false ? __('Author vote is', 'pure').': '.(int)$indexes->author_votes[$key] : __('Author not voted yet', 'pure'))),
                                    )
                                );
                            }
                        }
                    }
                    $Reports    = NULL;
                }
            }
            return $innerHTML;
        }
    }
}
?>