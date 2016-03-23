<?php
namespace Pure\Templates\Layout\BuddyPress\AllMembers{
    class A{
        private $settings = false;
        private function getSettings(){
            \Pure\Components\WordPress\Settings\Initialization::instance()->attach();
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->settings->counts->properties;
            $settings       = \Pure\Components\WordPress\Settings\Instance::instance()->less($settings);
            $this->settings = $settings;
        }
        public function get(){
            $this->getSettings();
            $members    = new \Pure\Plugins\Thumbnails\Authors\Builder(array(
                    'content'           => 'last',
                    'targets'	        => '',
                    'template'	        => 'F',
                    'title'		        => '',
                    'title_type'        => '',
                    'maxcount'	        => 30,
                    'only_with_avatar'	=> false,
                    'top'	            => false,
                    'profile'	        => '',
                    'days'	            => 3650,
                    'from_date'         => '',
                    'more'              => true)
            );
            $innerHTMLMembers   = $members->render();
            $innerHTMLMembers   = ($innerHTMLMembers !== '' ? $innerHTMLMembers : '<p data-element-type="Pure.Social.Home.A.Message">'.__('No members on site yet.', 'pure').'</p>');
            $members            = NULL;
            $innerHTMLMembers   = Initialization::instance()->html(
                'A/one_column_segment_central',
                array(
                    array('title',      __('All members on site') ),
                    array('content',    $innerHTMLMembers                ),
                )
            );
            $innerHTML          = Initialization::instance()->html(
                'A/layout',
                array(
                    array('members', $innerHTMLMembers),
                )
            );
            //Attach effects
            \Pure\Components\Effects\Fader\Initialization::instance()->attach();
            \Pure\Components\LockPage\A\Initialization::instance()->attach();
            //Attach global layout
            \Pure\Templates\Layout\Page\Container\Initialization::instance()->attach_resources_of('A');
            $headerClass = NULL;
            return $innerHTML;
        }
    }
}
?>