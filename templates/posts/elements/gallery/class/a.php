<?php
namespace Pure\Templates\Posts\Elements\Gallery{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset       ($parameters->items ) !== false ? true : false));
                $result = ($result === false ? false : (is_object   ($parameters->items ) !== false ? true : false));
                $result = ($result === false ? false : (isset       ($parameters->items->large      ) !== false ? true : false));
                $result = ($result === false ? false : (isset       ($parameters->items->thumbnail  ) !== false ? true : false));
                $result = ($result === false ? false : (is_array    ($parameters->items->large      ) !== false ? true : false));
                $result = ($result === false ? false : (is_array    ($parameters->items->thumbnail  ) !== false ? true : false));
                $result = ($result === false ? false : (isset       ($parameters->name  ) !== false ? true : false));
                $parameters->rate = (isset($parameters->rate) !== false ? $parameters->rate : 16 / 9);
                return $result;
            }
            return false;
        }
        /*
         * $parameters->items   - items of gallery (URLs of images)
         * $parameters->name    - name of gallery
         */
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                $gallery_id = uniqid('gallery');
                $Slider     = \Pure\Templates\Sliders\Initialization::instance()->get('B');
                $innerHTML  =   '<!--BEGIN: Gallery.A -->'.
                                '<article data-post-element-type="Pure.Posts.Elements.Gallery.A.Container" data-engine-element="parent" data-engine-gallery-rate="'.$parameters->rate.'">'.
                                    '<div data-post-element-type="Pure.Posts.Elements.Gallery.A">';
                $_data      = (object)array('items'=>array());
                $SRCs       = array();
                foreach($parameters->items->large as $image){
                    $_data->items[] =   '<div data-post-element-type="Pure.Posts.Elements.Gallery.A.Item" style="background-image:url('.$image->src.')">'.
                                        '</div>';
                    $SRCs[]         = $image->src;
                }
                \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                    'pure.posts.elements.gallery.data.'.$gallery_id,
                    implode(",", $SRCs),
                    false,
                    true
                );
                $innerHTML .= $Slider->get(
                    $_data,
                    (object)array(
                        'windowresize'=>false
                    )
                );
                $Slider     = NULL;
                $innerHTML .=       '</div>'.
                                    '<div data-post-element-type="Pure.Posts.Elements.Gallery.A.Controls">'.
                                        '<div data-post-element-type="Pure.Posts.Elements.Gallery.A.Controls.Left" data-engine-type="Slider.B.Button.Left">'.
                                        '</div>'.
                                        '<div data-post-element-type="Pure.Posts.Elements.Gallery.A.Controls.Right" data-engine-type="Slider.B.Button.Right">'.
                                        '</div>'.
                                        '<a data-post-element-type="Pure.Posts.Elements.Gallery.A.Full" data-engine-gallery-id="'.$gallery_id.'" data-engine-gallery-element="Gallery.A.Button.Full"></a>'.
                                    '</div>'.
                                    '<p data-post-element-type="Pure.Posts.Elements.Gallery.A.Info">'.$parameters->name.'</p>'.
                                '</article>'.
                                '<!--END: Gallery.A -->'.
                                '<div data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Container" data-engine-gallery-id="'.$gallery_id.'" data-engine-gallery-element="Gallery.A.FullView" style="display:none;">'.
                                    '<label>'.
                                        '<input data-post-element-type="Pure.Posts.Elements.Gallery.A.Original" type="checkbox" />'.
                                        '<div data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Image" data-engine-gallery-element="Gallery.A.FullView.Image"></div>'.
                                        '<div data-post-element-type="Pure.Posts.Elements.Gallery.A.Original">'.
                                            '<img alt="" data-post-element-type="Pure.Posts.Elements.Gallery.A.Original" data-engine-gallery-element="Gallery.A.Original.Image"/>'.
                                        '</div>'.
                                    '</label>'.
                                    '<a data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Close" data-engine-gallery-element="Gallery.A.FullView.Close">'.__('close','pure').'</a>'.
                                    '<a data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Previous" data-engine-gallery-element="Gallery.A.FullView.Previous"></a>'.
                                    '<a data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Next" data-engine-gallery-element="Gallery.A.FullView.Next"></a>'.
                                    '<a data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Position" data-engine-gallery-element="Gallery.A.FullView.Position">1/12</a>'.
                                    '<input data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Thumbnails" type="checkbox" id="'.$gallery_id.'"/>'.
                                    '<div data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Thumbnails.Container">'.
                                        '<label for="'.$gallery_id.'" data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Thumbnails"></label>'.
                                        '<div data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Thumbnails">';
                foreach($parameters->items->thumbnail as $image){
                    $innerHTML .=           '<div data-post-element-type="Pure.Posts.Elements.Gallery.A.FullView.Thumbnails.Item" style="background-image: url('.$image->src.')" data-engine-gallery-element="Gallery.A.FullView.Thumbnail"></div>';
                }
                $innerHTML .=           '</div>'.
                                    '</div>'.
                                '</div>';
                \Pure\Components\Attacher\Module\Attacher::instance()->addINIT(
                    'pure.posts.elements.gallery.A',
                    false,
                    true
                );
            }
            return $innerHTML;
        }
    }
}
?>