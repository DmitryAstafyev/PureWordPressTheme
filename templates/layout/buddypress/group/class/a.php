<?php
namespace Pure\Templates\Layout\BuddyPress\Group{
    class A{
        private $members = false;
        private function getMembers($group_id){
            $members            = new \Pure\Plugins\Thumbnails\Authors\Builder(array(
                    'content'           => 'users_of_group',
                    'targets'	        => (int)$group_id,
                    'template'	        => 'F',
                    'title'		        => '',
                    'title_type'        => '',
                    'maxcount'	        => 10,
                    'only_with_avatar'	=> false,
                    'top'	            => false,
                    'profile'	        => '',
                    'days'	            => 3650,
                    'from_date'         => '',
                    'more'              => true)
            );
            $innerHTMLMembers   = $members->render();
            $members            = NULL;
            $innerHTMLMembers   = Initialization::instance()->html(
                'A/one_column_segment_central',
                array(
                    array('title',      __('Members', 'pure')  ),
                    array('content',    $innerHTMLMembers  ),
                )
            );
            return $innerHTMLMembers;
        }
        private function getModerators($group_id){
            $members            = new \Pure\Plugins\Thumbnails\Authors\Builder(array(
                    'content'           => 'moderators_of_group',
                    'targets'	        => (int)$group_id,
                    'template'	        => 'F',
                    'title'		        => '',
                    'title_type'        => '',
                    'maxcount'	        => 10,
                    'only_with_avatar'	=> false,
                    'top'	            => false,
                    'profile'	        => '',
                    'days'	            => 3650,
                    'from_date'         => '',
                    'more'              => true)
            );
            $innerHTMLMembers   = $members->render();
            $members            = NULL;
            if ($innerHTMLMembers !== ''){
                $innerHTMLMembers   = Initialization::instance()->html(
                    'A/one_column_segment_central',
                    array(
                        array('title',      __('Moderators', 'pure')  ),
                        array('content',    $innerHTMLMembers  ),
                    )
                );
            }
            return $innerHTMLMembers;
        }
        public function getQuote($group_id){
            if ($this->members === false){
                \Pure\Components\BuddyPress\Groups\Initialization::instance()->attach();
                $GroupData      = new \Pure\Components\BuddyPress\Groups\Core();
                $group          = $GroupData->get((object)array('id'=>$group_id));
                $GroupData      = NULL;
                $this->members  = $group->members;
            }
            $innerHTML      = '';
            $QuoteTemplate  = \Pure\Templates\BuddyPress\QuotesRender\Initialization::instance()->get('B');
            foreach($this->members as $key=>$value){
                if ($value !== false){
                    $innerHTML              = $QuoteTemplate->get((object)array('user_id'=>$value));
                    $this->members[$key]    = false;
                    if ($innerHTML !== ''){
                        break;
                    }
                }
            }
            $QuoteTemplate  = NULL;
            if ($innerHTML !== ''){
                $innerHTML    = Initialization::instance()->html(
                    'A/quote',
                    array(
                        array('content',   $innerHTML),
                    )
                );
            }
            return $innerHTML;
        }
        public function get($group_id){
            $group = groups_get_group(
                array(
                    'group_id'=>$group_id
                )
            );
            $headerClass            = \Pure\Templates\BuddyPress\Headers\Initialization::instance()->get('AGroup');
            $Activities             = \Pure\Templates\BuddyPress\Activities\Initialization::instance()->get('A');
            $activities             = $Activities->innerHTML(
                (object)array(
                    'group_id'=>$group->id
                )
            );
            $Activities             = NULL;
            $innerHTMLActivities    = Initialization::instance()->html(
                'A/one_column_segment_normal',
                array(
                    array('title',      ''          ),
                    array('content',    $activities ),
                )
            );
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('header_segment',     $headerClass->get($group)       ),
                    array('activities_segment', $innerHTMLActivities            ),
                    array('quote_0',            $this->getQuote($group_id)      ),
                    array('members',            $this->getMembers($group_id)    ),
                    array('quote_1',            $this->getQuote($group_id)      ),
                    array('moderators',         $this->getModerators($group_id) ),
                )
            );
            //Attach effects
            \Pure\Components\Effects\Fader\Initialization::instance()->attach();
            \Pure\Components\LockPage\A\Initialization::instance()->attach();
            //Attach global layout
            \Pure\Templates\Layout\Page\Container\Initialization::instance()->attach_resources_of('A');
            return $innerHTML;
        }
    }
}
?>