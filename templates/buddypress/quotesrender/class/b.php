<?php
namespace Pure\Templates\BuddyPress\QuotesRender{
    class B{
        private function validate(&$parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id) == 'integer'  ? true : false) : false));
                    break;
            }
            return $result;
        }
        private function quote($quote, $author, $date){
            $innerHTML = Initialization::instance()->html(
                'B/quote',
                array(
                    array('quote',  $quote  ),
                    array('name',   $author ),
                    array('date',   $date   )
                )
            );
            return $innerHTML;
        }
        public function get($parameters){
            $innerHTML = '';
            if ($this->validate($parameters, __METHOD__) === true){
                \Pure\Components\BuddyPress\Quotes\Initialization::instance()->attach();
                $Quotes = new \Pure\Components\BuddyPress\Quotes\Core();
                $quotes = $Quotes->get((object)array('user'=>(int)$parameters->user_id));
                $Quotes = NULL;
                if ($quotes !== false){
                    if (count($quotes) > 0){
                        $selected = rand(0, count($quotes) - 1);
                        $innerHTML = $this->quote(
                            $quotes[$selected]->quote,
                            $quotes[$selected]->user_name,
                            $quotes[$selected]->date_created
                        );
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>