<?php
namespace Pure\Templates\Posts\Elements\Author{
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
                    $innerHTML .=       '<!--BEGIN: Post.Author.A -->'.
                                        '<div data-post-element-type="Pure.Posts.Author.A.Icon" style="background-image:url('.$post->author->avatar.');"></div>'.
                                        '<a data-post-element-type="Pure.Posts.Author.A.Name" href="'.$post->author->profile.'">'.$post->author->name.'</a>'.
                                        '<p data-post-element-type="Pure.Posts.Author.A.Date">'.(new \DateTime($post->post->post_date))->format('F j, Y').'</p>';
                    foreach($post->category->all as $category){
                        $innerHTML .=   '<a data-post-element-type="Pure.Posts.Author.A.Category" href="'.$category->url.'">'.$category->name.'</a>';
                    }
                    $innerHTML .=       '<!--END: Post.Author.A -->';
                }
            }
            return $innerHTML;
        }
    }
}
?>