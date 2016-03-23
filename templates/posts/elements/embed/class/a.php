<?php
namespace Pure\Templates\Posts\Elements\Embed{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->innerHTML    ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->url          ) !== false ? true : false));
                return $result;
            }
            return false;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $innerHTML  = '';
                $innerHTML .=   '<!--BEGIN:Embed.A -->'.
                                '<article data-post-element-type="Pure.Posts.Elements.Embed.A.Container" data-engine-post-embed="container">'.
                                    '<div data-post-element-type="Pure.Posts.Elements.Embed.A.Container">'.
                                        $parameters->innerHTML.
                                    '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Elements.Embed.A.OriginalURL">'.
                                        '<p data-post-element-type="Pure.Posts.Elements.Embed.A.OriginalURL">'.__( 'Direct link', 'pure' ).'</p>'.
                                        '<a data-post-element-type="Pure.Posts.Elements.Embed.A.OriginalURL" target="_blank" href="'.$parameters->url.'"></a>'.
                                    '</div>'.
                                '</article>'.
                                '<!--END: Embed.A -->';
                \Pure\Components\Attacher\Module\Attacher::instance()->addINIT(
                    'pure.posts.elements.embed.A',
                    false,
                    true
                );
            }
            return $innerHTML;
        }
    }
}
?>