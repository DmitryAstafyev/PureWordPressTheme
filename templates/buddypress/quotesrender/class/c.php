<?php
namespace Pure\Templates\BuddyPress\QuotesRender{
    class C{
        private function validate(&$parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id) == 'integer'  ? true : false) : false));
                    break;
            }
            return $result;
        }
        public function get($parameters){
            $innerHTML = '';
            if ($this->validate($parameters, __METHOD__) === true){
                \Pure\Components\BuddyPress\Quotes\Initialization::instance()->attach();
                $Quotes = new \Pure\Components\BuddyPress\Quotes\Core();
                $quotes = $Quotes->getRandomOfUser((int)$parameters->user_id, 1);
                $Quotes = NULL;
                if ($quotes !== false){
                    if (count($quotes) > 0){
                        $WordPress = new \Pure\Components\WordPress\UserData\Data();
                        $innerHTML = Initialization::instance()->html(
                            'C/quote',
                            array(
                                array('quote',      $quotes[0]->quote                                   ),
                                array('name',       $quotes[0]->user_name                               ),
                                array('date',       $quotes[0]->date_created                            ),
                                array('avatar',     $WordPress->user_avatar_url($quotes[0]->user_id)    ),
                                array('how_long',   $WordPress->how_long_on_site($quotes[0]->user_id)   ),
                                array('label_0',    __('with us', 'pure')                        ),
                            )
                        );
                        $WordPress = NULL;
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>