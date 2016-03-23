<?php
namespace Pure\Templates\Layout\BuddyPress\Member{
    class A{
        public function get($member){
            $UserData               = \Pure\Providers\Members\Initialization::instance()->getCommon();
            $member_full            = $UserData->get((int)$member->id);
            $UserData               = NULL;
            $headerClass            = \Pure\Templates\BuddyPress\Headers\Initialization::instance()->get('A');
            $activitiesClass        = \Pure\Templates\BuddyPress\Activities\Initialization::instance()->get('A');
            $friends                = new \Pure\Plugins\Thumbnails\Authors\Builder(array(
                'content'           => 'friends_of_user',
                'targets'	        => (int)$member->id,
                'template'	        => 'F',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => 5,
                'only_with_avatar'	=> false,
                'top'	            => false,
                'profile'	        => '',
                'days'	            => 3650,
                'from_date'         => '',
                'more'              => true)
            );
            $innerHTMLFriends       = $friends->render();
            $innerHTMLFriends       = ($innerHTMLFriends !== '' ? $innerHTMLFriends : '<p data-element-type="Pure.Social.Home.A.Message">'.$member_full->author->name.' '.__('has not any friend here yet.', 'pure').'</p>');
            $friends                = NULL;
            $groups     = new \Pure\Plugins\Thumbnails\Groups\Builder(array(
                'content'           => 'users',
                'targets'	        => (int)$member->id,
                'template'	        => 'J',
                'title'		        => '',
                'title_type'        => '',
                'maxcount'	        => 5,
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
            $innerHTMLGroups        = ($innerHTMLGroups !== '' ? $innerHTMLGroups : '<p data-element-type="Pure.Social.Home.A.Message">'.$member_full->author->name.' '.__('are not a member of any group yet.', 'pure').'</p>');
            $groups                 = NULL;
            $ManaSummary            = \Pure\Templates\Mana\Summary\Initialization::instance()->get('A');
            $innerHTMLMana          = $ManaSummary->innerHTML((int)$member->id);
            $ManaSummary            = NULL;
            $innerHTMLActivities    = $activitiesClass->innerHTML(
                (object)array(
                    'user_id'=>(int)$member->id
                )
            );
            $avatar_id              = uniqid('avatar_id');
            $innerHTMLActivities    = Initialization::instance()->html(
                'A/one_column_segment_normal',
                array(
                    array('title',      ''                      ),
                    array('content',    $innerHTMLActivities    ),
                )
            );
            $innerHTMLPostCount     = Initialization::instance()->html(
                'A/about',
                array(
                    array('name',       $member_full->author->name     ),
                    array('info',       __( 'Created', 'pure' ).': '.$member_full->posts->count.' '.__( 'posts', 'pure' ).' '.__( 'and', 'pure' ).' '.$member_full->comments->count.' '.__( 'comments', 'pure' )),
                )
            );
            $innerHTMLFriends    = Initialization::instance()->html(
                'A/one_column_segment_central',
                array(
                    array('title',      __('Friends', 'pure')  ),
                    array('content',    $innerHTMLFriends  ),
                )
            );
            $innerHTMLHistory     = Initialization::instance()->html(
                'A/about',
                array(
                    array('name',       $member_full->author->name     ),
                    array('info',       __( 'With us from', 'pure' ).' '.$member_full->author->date.' ('.$member_full->author->how_long.')'),
                )
            );
            $innerHTMLGroups        = Initialization::instance()->html(
                'A/one_column_segment_central',
                array(
                    array('title',      __('Groups', 'pure')  ),
                    array('content',    $innerHTMLGroups  ),
                )
            );
            $innerHTMLMana          = Initialization::instance()->html(
                'A/one_column_segment_central',
                array(
                    array('title',      __('Karma', 'pure')     ),
                    array('content',    $innerHTMLMana          ),
                )
            );
            $innerHTMLSocial        = Initialization::instance()->html(
                'A/about',
                array(
                    array('name',       $member_full->author->name     ),
                    array('info',       __( 'Has', 'pure' ).' '.$member_full->author->friends.' '.__( 'friends and member of', 'pure' ).' '.$member_full->author->groups.' '.__( 'groups', 'pure' )),
                )
            );
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('header_segment',     $headerClass->get((int)$member->id, (object)array('avatar_id'=>$avatar_id))),
                    array('activities_segment', $innerHTMLActivities    ),
                    array('history',            $innerHTMLHistory       ),
                    array('friends',            $innerHTMLFriends       ),
                    array('post_count',         $innerHTMLPostCount     ),
                    array('groups',             $innerHTMLGroups        ),
                    array('mana',               $innerHTMLMana          ),
                    array('social',             $innerHTMLSocial        ),
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