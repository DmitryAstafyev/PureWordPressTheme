<?php
namespace Pure\Templates\Posts\Elements\Events\TimeAndDate{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post_id) !== false ? true : false));
                return $result;
            }
            return false;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                \Pure\Components\PostTypes\Events\Module\Initialization::instance()->attach();
                $EventProvider  = new \Pure\Components\PostTypes\Events\Module\Provider();
                $event          = $EventProvider->get($parameters->post_id);
                $EventProvider  = NULL;
                if ($event !== false){
                    \Pure\Components\Tools\Dates\Initialization::instance()->attach(true);
                    $Dates                  = new \Pure\Components\Tools\Dates\Dates();
                    $duration_event         = $Dates->between($event->start, $event->finish);
                    $duration_registration  = $Dates->between($event->registration_start, $event->registration_finish);
                    $Dates                  = NULL;
                    $innerHTML .=   '<!--BEGIN: Post.TimeAndDate.A -->'.
                                    '<div data-post-element-type="Pure.Posts.TimeAndDate.A">'.
                                        '<table data-post-element-type="Pure.Posts.TimeAndDate.A">'.
                                            '<tr>'.
                                                '<td colspan="4" data-post-element-type="Pure.Posts.TimeAndDate.A.Title">'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Title">'.__( "Event", 'pure' ).'</p>'.
                                                '</td>'.
                                            '</tr>'.
                                            '<tr>'.
                                                '<td>'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Field">'.__( "start at", 'pure' ).'</p>'.
                                                '</td>'.
                                                '<td>'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Field">'.$event->start.'</p>'.
                                                '</td>'.
                                                '<td rowspan="2">'.
                                                    '<div data-post-element-type="Pure.Posts.TimeAndDate.A.Brace"></div>'.
                                                '</td>'.
                                                '<td rowspan="2">'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Field">'.$duration_event.'</p>'.
                                                '</td>'.
                                            '</tr>'.
                                            '<tr>'.
                                                '<td>'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Field">'.__( "and finish", 'pure' ).'</p>'.
                                                '</td>'.
                                                '<td>'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Field">'.$event->finish.'</p>'.
                                                '</td>'.
                                            '</tr>'.
                                            '<tr>'.
                                                '<td colspan="4" data-post-element-type="Pure.Posts.TimeAndDate.A.Title">'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Title">'.__( "Registration", 'pure' ).'</p>'.
                                                '</td>'.
                                            '</tr>'.
                                            '<tr>'.
                                                '<td>'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Field">'.__( "start at", 'pure' ).'</p>'.
                                                '</td>'.
                                                '<td>'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Field">'.$event->registration_start.'</p>'.
                                                '</td>'.
                                                '<td rowspan="2">'.
                                                    '<div data-post-element-type="Pure.Posts.TimeAndDate.A.Brace"></div>'.
                                                '</td>'.
                                                '<td rowspan="2">'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Field">'.$duration_registration.'</p>'.
                                                '</td>'.
                                            '</tr>'.
                                            '<tr>'.
                                                '<td>'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Field">'.__( "and finish", 'pure' ).'</p>'.
                                                '</td>'.
                                                '<td>'.
                                                    '<p data-post-element-type="Pure.Posts.TimeAndDate.A.Field">'.$event->registration_finish.'</p>'.
                                                '</td>'.
                                            '</tr>'.
                                        '</table>'.
                                    '</div>'.
                                    '<!--END: Post.TimeAndDate.A -->';
                }
            }
            return $innerHTML;
        }
    }
}
?>