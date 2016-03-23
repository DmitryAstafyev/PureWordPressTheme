<?php
namespace Pure\Components\WordPress\Location\Special {
    class Register{
        public $root        = 'SPECIAL';
        public $requests    = array(
            //Post
            'CREATEPOST'        => array(
                'parameters'    => array()
            ),
            'EDITPOST'          => array(
                'parameters'    => array('post_id')
            ),
            //Event
            'CREATEEVENT'       => array(
                'parameters'    => array()
            ),
            'EDITEVENT'         => array(
                'parameters'    => array('post_id')
            ),
            //Report
            'CREATEREPORT'      => array(
                'parameters'    => array()
            ),
            'EDITREPORT'        => array(
                'parameters'    => array('post_id')
            ),
            //Question
            'CREATEQUESTION'    => array(
                'parameters'    => array()
            ),
            'EDITQUESTION'      => array(
                'parameters'    => array('post_id')
            ),
            //Top posts
            'TOP'               => array(
                'parameters'    => array('type')
            ),
            //Stream
            'STREAM'            => array(
                'parameters'    => array('user_id')
            ),
            //Drafts
            'DRAFTS'            => array(
                'parameters'    => array('user_id')
            ),
            //Group content
            'GROUPCONTENT'             => array(
                'parameters'    => array('group_id')
            ),
            //Search page
            'SEARCH'            => array(
                'parameters'    => array()
            ),
            //Advanced search page
            'ASEARCH'            => array(
                'parameters'    => array('categories', 'tags')
            ),
            //Page by defined scheme
            'BYSCHEME'          => array(
                'parameters'    => array('scheme')
            ),
        );
        public function getURL($type, $parameters){
            //localhost/special/editpost?post_id=178
            $url = '';
            if (isset($this->requests[$type]) !== false){
                foreach($this->requests[$type]['parameters'] as $param){
                    if (isset($parameters[$param]) === false){
                        return '';
                    }
                }
                $url    = get_site_url().'/'.$this->root.'/'.$type.(count($this->requests[$type]['parameters']) > 0 ? '?' : '');
                $index  = 0;
                foreach($this->requests[$type]['parameters'] as $param){
                    $index ++;
                    $url .= $param.'='.$parameters[$param].($index < count($this->requests[$type]['parameters']) ? '&' : '');
                }
            }
            return $url;
        }
    }
}
?>