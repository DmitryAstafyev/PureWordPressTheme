<?php
namespace Pure\Templates\BuddyPress\QuotesRender{
    class A{
        public $name = 'Simple quote text';
        private function validate(&$parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id) == 'integer'  ? true : false) : false));
                    break;
            }
            return $result;
        }
        private function quote($quote, $author, $date, $quote_id, $current, $parameters){
            $innerHTML  = '';
            if ($current !== false){
                if ((int)$current->ID !== (int)$parameters->user_id){
                    \Pure\Components\BuddyPress\Quotes\Initialization::instance()->attach();
                    $Quotes         = new \Pure\Components\BuddyPress\Quotes\Core();
                    $is_attached    = $Quotes->isAttached(
                        (object)array(
                            'user_id'   =>(int)$current->ID,
                            'quote_id'  =>(int)$quote_id
                        )
                    );
                    $Quotes         = NULL;
                    $innerHTML      = Initialization::instance()->html(
                        'A/quote_with_controls',
                        array(
                            array('title_attach',   __('Attach to my page', 'pure')     ),
                            array('title_detach',   __('Remove from my page', 'pure')     ),
                            array('state',          ($is_attached === false ? 'detached' : 'attached')   ),
                            array('linked_id',      ($is_attached === false ? '' : $is_attached)   ),
                            array('quote_id',       $quote_id   ),
                            array('quote',          $quote      ),
                            array('name',           $author     ),
                            array('date',           $date       )
                        )
                    );
                }
            }
            if ($innerHTML === ''){
                $innerHTML = Initialization::instance()->html(
                    'A/quote',
                    array(
                        array('quote',  $quote  ),
                        array('name',   $author ),
                        array('date',   $date   )
                    )
                );
            }
            return $innerHTML;
        }
        private function resources($parameters, $current){
            if ($current !== false) {
                if ((int)$current->ID !== (int)$parameters->user_id) {
                    //Attach styles
                    \Pure\Templates\ProgressBar\Initialization                  ::instance()->get('A');
                    \Pure\Components\Dialogs\A\Initialization                   ::instance()->attach();
                    //Define variables
                    \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
                    \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
                    $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.buddypress.quoterender.configuration.destination',
                        $Requests->url,
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.buddypress.quoterender.configuration.request.detach',
                        'command'.      '=templates_of_manage_quotes_remove'.   '&'.
                        'user_id'.      '='.$current->ID.               '&'.
                        'quote_id'.     '='.'[quote_id]',
                        false,
                        true
                    );
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.buddypress.quoterender.configuration.request.import',
                        'command'.      '=templates_of_manage_quotes_import'.   '&'.
                        'user_id'.      '='.$current->ID.               '&'.
                        'quote_id'.     '='.'[quote_id]',
                        false,
                        true
                    );
                    $Requests = NULL;
                }
            }
        }
        public function get($parameters){
            $innerHTML = '';
            if ($this->validate($parameters, __METHOD__) === true){
                \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
                $Settings   = new \Pure\Components\BuddyPress\PersonalSettings\User();
                $settings   = $Settings->get((object)array('user_id'=>(int)$parameters->user_id));
                $Settings   = NULL;
                if ($settings !== false) {
                    if ($settings['quotes']->template === 'A') {
                        \Pure\Components\BuddyPress\Quotes\Initialization::instance()->attach();
                        $Quotes = new \Pure\Components\BuddyPress\Quotes\Core();
                        $quotes = $Quotes->get((object)array('user'=>(int)$parameters->user_id));
                        $Quotes = NULL;
                        $items  = array();
                        if ($quotes !== false){
                            if (count($quotes) > 0){
                                $WordPress  = new \Pure\Components\WordPress\UserData\Data();
                                $current    = $WordPress->get_current_user();
                                $WordPress  = NULL;
                                foreach($quotes as $quote){
                                    if ((boolean)$quote->active === true){
                                        $items[] = $this->quote(
                                            $quote->quote,
                                            $quote->user_name,
                                            date('F j, Y, G:i', strtotime($quote->date_created)),
                                            $quote->id,
                                            $current,
                                            $parameters
                                        );
                                    }
                                }
                                $this->resources($parameters, $current);
                            }
                        }
                        if (count($items) > 0){
                            if (count($items) > 1){
                                $Slider             = \Pure\Templates\Sliders\Initialization::instance()->get('B');
                                $innerHTMLQuotes    = $Slider->get(
                                    (object)array( 'items'         =>$items ),
                                    (object)array( 'windowresize'  =>true   )
                                );
                                $innerHTMLControls  = Initialization::instance()->html(
                                    'A/controls',
                                    array(
                                        array('title_next',             __('Next', 'pure')                   ),
                                        array('button_mark',            'data-engine-type="Slider.B.Button.Right"'  ),
                                    )
                                );
                                $innerHTML          = Initialization::instance()->html(
                                    'A/container',
                                    array(
                                        array('quotes',         $innerHTMLQuotes                        ),
                                        array('controls',       $innerHTMLControls                      ),
                                        array('container_mark', 'data-engine-element="parent"'          ),
                                    )
                                );
                            }else{
                                $innerHTML .= $items[0];
                            }
                        }
                    }
                }
            }
            return $innerHTML;
        }
        public function example(){
            return  $this->quote(
                'Happiness is a mental or emotional state of well-being characterized by positive or pleasant emotions ranging from contentment to intense joy.',
                'Author',
                date("Y-m-d"),
                0,
                false,
                false
            );
        }
    }
}
?>