<?php
namespace Pure\Templates\Posts\Elements\Image{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post_id   ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->postSRC   ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->fullSRC   ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->align     ) !== false ? true : false));
                $parameters->caption = (isset($parameters->caption) !== false ? $parameters->caption : false);
                return $result;
            }
            return false;
        }
        private function fullViewTemplate(){
            $innerHTML = '';
            if (isset(\Pure\Configuration::instance()->globals->flags->PostElementsImageATemplateInited) === false){
                \Pure\Configuration::instance()->globals->flags->PostElementsImageATemplateInited = true;
                $gallery_id = uniqid();
                $innerHTML  =   '<div data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Container" '.
                                    'data-engine-image-element="Image.A.FullView" '.
                                    'style="display:none;" '.
                                '>'.
                                    '<label>'.
                                        '<input data-post-element-type="Pure.Posts.Elements.Image.A.Original" type="checkbox" />'.
                                        '<div data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Image" data-engine-image-element="Image.A.FullView.Image"></div>'.
                                        '<div data-post-element-type="Pure.Posts.Elements.Image.A.Original">'.
                                            '<img alt="" pure-image-inited data-post-element-type="Pure.Posts.Elements.Image.A.Original" data-engine-image-element="Image.A.Original.Image"/>'.
                                        '</div>'.
                                    '</label>'.
                                    '<a data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Close" data-engine-image-element="Image.A.FullView.Close">'.__('close','pure').'</a>'.
                                    '<a data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Previous" data-engine-image-element="Image.A.FullView.Previous"></a>'.
                                    '<a data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Next" data-engine-image-element="Image.A.FullView.Next"></a>'.
                                    '<a data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Position" data-engine-image-element="Image.A.FullView.Position">1/12</a>'.
                                    '<input data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Thumbnails" type="checkbox" id="'.$gallery_id.'"/>'.
                                    '<div data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Thumbnails.Container">'.
                                        '<label for="'.$gallery_id.'" data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Thumbnails"></label>'.
                                        '<div data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Thumbnails">'.
                                            '<div data-post-element-type="Pure.Posts.Elements.Image.A.FullView.Thumbnails.Item" '.
                                                'data-engine-image-element="Image.A.FullView.Thumbnail" '.
                                            '></div>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>';
            }
            return $innerHTML;
        }
        /*
         * $parameters->postSRC     - SRC to render in post
         * $parameters->fullSRC     - SRC of full screen
         * $parameters->align       - align of image
         * $parameters->caption     - caption of image
         */
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                //Get image data
                \Pure\Components\WordPress\Media\Attachments\Initialization::instance()->attach();
                $AttachmentTools    = new \Pure\Components\WordPress\Media\Attachments\Core();
                $attachment         = $AttachmentTools->getPostRecordByAttachmentURL($parameters->fullSRC);
                $AttachmentTools    = NULL;
                if ($attachment !== false){
                    $ManaTemplate   = \Pure\Templates\Mana\Icon\Initialization::instance()->get('A');
                    $innerHTMLMana  =  '<div data-post-element-type="Pure.Posts.Elements.Image.A.Mana.Wrapper">'.
                                            $ManaTemplate->innerHTML(
                                                (object)array(
                                                    'object'    =>'image',
                                                    'object_id' =>$attachment->ID,
                                                    'user_id'   =>$attachment->post_author,
                                                )
                                            ).
                                        '</div>';
                    $ManaTemplate   = NULL;
                }
                $innerHTML      =   '<div data-post-element-type="Pure.Posts.Elements.Image.A.Container">'.
                                        '<img pure-image-inited alt="" data-post-element-type="Pure.Posts.Elements.Image.A" src="'.$parameters->postSRC.'"/>';
                if ($parameters->caption !== false){
                    $innerHTML .=       '<p data-post-element-type="Pure.Posts.Elements.Image.A">'.$parameters->caption.'</p>';
                }
                if ($parameters->fullSRC !== false){
                    $innerHTML .=       '<a data-post-element-type="Pure.Posts.Elements.Image.A.Full" '.
                                            'data-engine-image-postSRC="'.$parameters->postSRC.'" '.
                                            'data-engine-image-fullSRC="'.$parameters->fullSRC.'" '.
                                            'data-engine-image-postID="'.$parameters->post_id.'" '.
                                            'data-engine-image-element="Image.A.Button.Full" '.
                                        '></a>'.
                                        $this->fullViewTemplate();
                }


                $innerHTML .=       $innerHTMLMana.
                                '</div>';
                \Pure\Components\Attacher\Module\Attacher::instance()->addINIT(
                    'pure.posts.elements.image.A',
                    false,
                    true
                );
            }
            return $innerHTML;
        }
    }
}
?>