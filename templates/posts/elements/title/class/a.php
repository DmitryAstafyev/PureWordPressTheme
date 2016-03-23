<?php
namespace Pure\Templates\Posts\Elements\Title{
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
                $PostData   = \Pure\Providers\Posts\Initialization::instance()->getCommon();
                $post       = $PostData->get($parameters->post_id, false);
                $PostData   = NULL;
                if ($post !== false){
                    if ($post->post->miniature === ''){
                        $innerHTML .=       '<!--BEGIN: Post.Title.A -->'.
                                            '<div data-post-element-type="Pure.Posts.Title.A">'.
                                                '<p data-post-element-type="Pure.Posts.Title.A">'.$post->post->title.'</p>'.
                                                '<p data-post-element-type="Pure.Posts.Title.A.Date">'.date('F j, Y, H:i', strtotime($post->post->date)).', '.__('views', 'pure').': '.$post->post->views.'</p>'.
                                            '</div>'.
                                            '<!--END: Post.Title.A -->';
                    }else{
                        $innerHTML .=       '<!--BEGIN: Post.Title.A -->'.
                                            '<div data-post-element-type="Pure.Posts.Title.A.Thumbnail">'.
                                                '<img data-post-element-type="Pure.Posts.Title.A.Thumbnail" src="'.$post->post->miniature.'"/>'.
                                                '<div data-post-element-type="Pure.Posts.Title.A">'.
                                                    '<p data-post-element-type="Pure.Posts.Title.A">'.$post->post->title.'</p>'.
                                                    '<p data-post-element-type="Pure.Posts.Title.A.Date">'.date('F j, Y, H:i', strtotime($post->post->date)).', '.__('views', 'pure').': '.$post->post->views.'</p>'.
                                                '</div>'.
                                            '</div>'.
                                            '<!--END: Post.Title.A -->';
                    }
                }
            }
            return $innerHTML;
        }
    }
}
?>