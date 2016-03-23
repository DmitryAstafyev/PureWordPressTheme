<?php
namespace Pure\Templates\BuddyPress\QuotesManage{
    class A{
        private function validate(&$parameters, $method){
            $result = true;
            switch(preg_replace("/.*(?=\:\:)(\:\:)/", '', $method)){
                case 'get':
                    $result = ($result === false ? $result : (isset($parameters->user_id) === true ? (gettype($parameters->user_id  ) == 'integer'  ? true : false) : false));
                    $result = ($result === false ? $result : (isset($parameters->id     ) === true ? (gettype($parameters->id       ) == 'string'   ? true : false) : false));
                    break;
            }
            return $result;
        }
        public function innerHTMLQuote($user_id, $id, $quote){
            $innerHTML = Initialization::instance()->html(
                'A/quote_in_list',
                array(
                    array('remove',                 __('remove','pure') ),
                    array('activate',               __('activate','pure') ),
                    array('deactivate',             __('deactivate','pure') ),
                    array('quote',                  $quote->quote ),
                    array('info',                   $quote->user_name.', '.$quote->date_created),
                    array('quote_id',               $quote->id),
                    array('state',                  ((boolean)$quote->active === false ? 'deactive' : 'active')),
                    array('user_id',                $user_id),
                    array('id',                     $id),
                )
            );
            return $innerHTML;
        }
        private function innerHTMLQuotesList($parameters){
            \Pure\Components\BuddyPress\Quotes\Initialization::instance()->attach();
            $Quotes     = new \Pure\Components\BuddyPress\Quotes\Core();
            $quotes     = $Quotes->get((object)array('user'=>(int)$parameters->user_id));
            $Quotes     = NULL;
            $innerHTML  = '';
            if ($quotes !== false){
                foreach($quotes as $quote){
                    $innerHTML .= $this->innerHTMLQuote($parameters->user_id, $parameters->id, $quote);
                }
            }
            if ($innerHTML === ''){
                $innerHTML = Initialization::instance()->html(
                    'A/no_quotes',
                    array(
                        array('message',        __('You did not create any quote, yet. Just add new one.','pure') ),
                        array('instance_id',    $parameters->id ),
                    )
                );
            }
            return $innerHTML;
        }
        private function innerHTMLTemplates($parameters, $settings){
            $innerHTML = Initialization::instance()->html(
                'A/template_do_not_show',
                array(
                    array('label',  __('Do not show quotes on my page', 'pure') ),
                    array('active', ($settings->template === false ? 'checked' : '')),
                    array('id',     $parameters->id),
                )
            );
            foreach(\Pure\Templates\BuddyPress\QuotesRender\Initialization::instance()->templates as $template){
                $TemplateQuote  = \Pure\Templates\BuddyPress\QuotesRender\Initialization::instance()->get($template->key);
                if (method_exists($TemplateQuote, 'example') !== false){
                    $innerHTMLQuote = $TemplateQuote->example();
                    $innerHTML      .= Initialization::instance()->html(
                        'A/template',
                        array(
                            array('quote',                  $innerHTMLQuote ),
                            array('active',                 ($settings->template == $template->key ? 'checked' : '')),
                            array('template',               $template->key),
                            array('id',                     $parameters->id),
                        )
                    );
                }
            }
            return $innerHTML;
        }
        private function innerHTML($parameters){
            \Pure\Components\BuddyPress\PersonalSettings\Initialization::instance()->attach();
            $Settings               = new \Pure\Components\BuddyPress\PersonalSettings\User();
            $settings               = $Settings->get((object)array('user_id'=>(int)$parameters->user_id));
            $Settings               = NULL;
            $innerHTML              = '';
            if (isset($settings['quotes']) !== false){
                $settings   = $settings['quotes'];
                $innerHTML  = Initialization::instance()->html(
                    'A/wrapper',
                    array(
                        array('instance_id',            $parameters->id                 ),
                        array('group_id',               uniqid()                        ),
                        array('id',                     uniqid()                        ),
                        array('tab_name_0',             __('Your quotes','pure') ),
                        array('tab_name_1',             __('Add new','pure') ),
                        array('tab_name_2',             __('View settings','pure') ),
                        //List
                        array('quotes',                 $this->innerHTMLQuotesList($parameters) ),
                        array('remove',                 __('remove','pure') ),
                        array('activate',               __('activate','pure') ),
                        array('deactivate',             __('deactivate','pure') ),
                        //New
                        array('new_0',                  __('New quote','pure') ),
                        array('new_1',                  __('Enter below your new quote. It can be text with length not less 10 symbols and not more than 500 symbols.','pure') ),
                        //Templates
                        array('templates_0',            __('Template','pure') ),
                        array('templates_1',            __('Choose template for render quotes on your personal page.','pure') ),
                        array('templates',              $this->innerHTMLTemplates($parameters, $settings) ),
                        //Buttons
                        array('button_0',               __('add new','pure') ),
                        array('button_1',               __('save','pure') ),
                        array('button_2',               __('activate / deactivate','pure') ),
                        array('button_3',               __('cancel','pure') ),
                        //array('',            __('','pure') ),
                    )
                );
            }
            return $innerHTML;
        }
        private function resources($parameters){
            //Attach styles
            \Pure\Components\Styles\CheckBoxes\B\Initialization         ::instance()->attach();
            \Pure\Components\Dialogs\A\Initialization                   ::instance()->attach();
            \Pure\Templates\ProgressBar\Initialization                  ::instance()->get('A');
            \Pure\Templates\ProgressBar\Initialization                  ::instance()->get('B');
            //Define variables
            \Pure\Components\WordPress\Location\Requests\Initialization ::instance()->attach();
            \Pure\Components\Attacher\Module\Initialization             ::instance()->attach();
            $Requests = new \Pure\Components\WordPress\Location\Requests\Register();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.managequotes.configuration.destination',
                $Requests->url,
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.managequotes.configuration.request.activate',
                'command'.      '=templates_of_manage_quotes_state'.    '&'.
                'user_id'.      '='.$parameters->user_id.               '&'.
                'quote_id'.     '='.'[quote_id]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.managequotes.configuration.request.remove',
                'command'.      '=templates_of_manage_quotes_remove'.   '&'.
                'user_id'.      '='.$parameters->user_id.               '&'.
                'quote_id'.     '='.'[quote_id]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.managequotes.configuration.request.add',
                'command'.      '=templates_of_manage_quotes_add_new'.  '&'.
                'user_id'.      '='.$parameters->user_id.               '&'.
                'quote'.        '='.'[quote]',
                false,
                true
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.managequotes.configuration.request.settings',
                'command'.      '=templates_of_personalsettings_update'.    '&'.
                'user'.         '='.$parameters->user_id.                   '&'.
                'settings'.     '='.'[settings]',
                false,
                true
            );
            $Requests = NULL;
        }
        private function quoteTemplate(){
            $innerHTML = Initialization::instance()->html(
                'A/quote_in_list_template',
                array(
                    array('remove',                 __('remove','pure') ),
                    array('activate',               __('activate','pure') ),
                    array('deactivate',             __('deactivate','pure') ),
                )
            );
            \Pure\Components\Attacher\Module\Initialization::instance()->attach();
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.managequotes.configuration.template.quote',
                base64_encode($innerHTML),
                false,
                true
            );
            $innerHTML = Initialization::instance()->html(
                'A/no_quotes',
                array(
                    array('message',        __('You did not create any quote, yet. Just add new one.','pure') ),
                    array('instance_id',    '[instance_id]' ),
                )
            );
            \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                'pure.buddypress.managequotes.configuration.template.noQuotes',
                base64_encode($innerHTML),
                false,
                true
            );
        }
        public function get($parameters){
            $innerHTML  = '';
            if ($this->validate($parameters, __METHOD__) === true){
                $WordPress      = new \Pure\Components\WordPress\UserData\Data();
                $current        = $WordPress->get_current_user();
                $WordPress      = NULL;
                if ($current !== false){
                    if ((int)$current->ID === (int)$parameters->user_id){
                        $innerHTML = $this->innerHTML($parameters);
                        $this->resources($parameters);
                        $this->quoteTemplate();
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>