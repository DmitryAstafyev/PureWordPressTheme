<?php
namespace Pure\Templates\Layout\BuddyPress\Profile{
    class A{
        public function get($member){
            $UserData               = \Pure\Providers\Members\Initialization::instance()->getCommon();
            $member_full            = $UserData->get((int)$member->id);
            $UserData               = NULL;
            $headerClass            = \Pure\Templates\BuddyPress\Headers\Initialization::instance()->get('A');
            $avatar_id              = uniqid('avatar_id');
            $Profile                = \Pure\Templates\BuddyPress\Profile\Initialization::instance()->get('A');
            $innerHTMLProfile       = $Profile->get(
                (int)$member->id,
                (object)array(
                    'manage'=>true
                )
            );
            $innerHTMLProfile       = Initialization::instance()->html(
                'A/one_column_segment_normal',
                array(
                    array('title',      'Profile'),
                    array('content',    $innerHTMLProfile),
                )
            );
            $innerHTMLAbout         = Initialization::instance()->html(
                'A/about',
                array(
                    array('name',       $member_full->author->name     ),
                    array('info_0',       __( 'Created', 'pure' ).': '.$member_full->posts->count.' '.__( 'posts', 'pure' ).' '.__( 'and', 'pure' ).' '.$member_full->comments->count.' '.__( 'comments', 'pure' )),
                    array('info_1',       __( 'With us from', 'pure' ).' '.$member_full->author->date.' ('.$member_full->author->how_long.')'),
                    array('info_2',       __( 'Has', 'pure' ).' '.$member_full->author->friends.' '.__( 'friends and member of', 'pure' ).' '.$member_full->author->groups.' '.__( 'groups', 'pure' )),
                )
            );
            $innerHTML              = Initialization::instance()->html(
                'A/layout',
                array(
                    array('header_segment',     $headerClass->get((int)$member->id, (object)array('avatar_id'=>$avatar_id))),
                    array('profile',            $innerHTMLProfile       ),
                    array('about',              $innerHTMLAbout         ),
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