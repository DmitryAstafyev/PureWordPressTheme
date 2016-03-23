<?php
namespace Pure\Templates\Layout\BuddyPress\AllGroups{
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
            $groups                 = new \Pure\Plugins\Thumbnails\Groups\Builder(array(
                'content'           => 'last',
                'targets'	        => '',
                'template'	        => 'J',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => 15,
                'only_with_avatar'	=> false,
                'top'	            => false,
                'profile'	        => '',
                'days'	            => 3650,
                'from_date'         => '',
                'show_content'      => false,
                'show_admin_part'   => false,
                'show_life'         => false,
                'more'              => true,
                'group'             => uniqid()
            ));
            $innerHTMLGroups        = $groups->render();
            $innerHTMLGroups        = ($innerHTMLGroups !== '' ? $innerHTMLGroups : '<p data-element-type="Pure.Social.Home.A.Message">'.__('No groups on site yet.', 'pure').'</p>');
            $groups                 = NULL;
            $innerHTMLGroups        = Initialization::instance()->html(
                'A/one_column_segment_central',
                array(
                    array('title',      __('All groups on site') ),
                    array('content',    $innerHTMLGroups                ),
                )
            );
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('groups',             $innerHTMLGroups        ),
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