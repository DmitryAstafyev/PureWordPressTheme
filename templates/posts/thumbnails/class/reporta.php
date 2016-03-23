<?php
namespace Pure\Templates\Posts\Thumbnails{
    class ReportA{
        private function validate(&$parameters){
            $parameters = (is_null  ($parameters) === false ? $parameters : new \stdClass());
            $parameters = (is_object($parameters) === true  ? $parameters : new \stdClass());
            $parameters->attribute          = (isset($parameters->attribute         ) === true  ? $parameters->attribute        : new \stdClass()   );
            $parameters->attribute->name    = (isset($parameters->attribute->name   ) === true  ? $parameters->attribute->name  : ''                );
            $parameters->attribute->value   = (isset($parameters->attribute->value  ) === true  ? $parameters->attribute->value : ''                );
        }
        private function innerHTMLIndexes($data){
            \Pure\Components\PostTypes\Reports\Module\Initialization::instance()->attach();
            $Reports            = new \Pure\Components\PostTypes\Reports\Module\Provider();
            $indexes            = $Reports->get((int)$data->post->id);
            $Reports            = NULL;
            $innerHTMLIndexes   = '';
            if ($indexes !== false) {
                foreach($indexes->indexes as $key=>$index){
                    $innerHTMLIndexes  .= Initialization::instance()->html(
                        'ReportA/line',
                        array(
                            array('name',           $index                                                          ),
                            array('current',        number_format($indexes->votes[$key], 2)                         ),
                            array('max',            $indexes->max[$key]                                             ),
                            array('rate',           ((int)$indexes->votes[$key] / (int)$indexes->max[$key])*100     ),
                        )
                    );
                }
            }
            return $innerHTMLIndexes;
        }
        private function innerHTML_miniature_excerpt($data, $parameters, $attribute_str){
            $innerHTMLIndexes   = $this->innerHTMLIndexes($data);
            $innerHTML          = Initialization::instance()->html(
                'ReportA/with_excerpt_miniature',
                array(
                    array('attribute',  $attribute_str                                              ),
                    array('title',      $data->post->title                                          ),
                    array('label_by',   __('by', 'pure')                                     ),
                    array('author',     $data->author->name                                         ),
                    array('day',        date('d', strtotime($data->post->date))                     ),
                    array('month',      date('F', strtotime($data->post->date))                     ),
                    array('year',       date('Y', strtotime($data->post->date))                     ),
                    array('comments',   $data->post->comments                                       ),
                    array('views',      $data->post->views                                          ),
                    array('karma',      ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)),
                    array('excerpt',    $data->post->excerpt                                        ),
                    array('post_url',   $data->post->url                                            ),
                    array('miniature',  $data->post->miniature                                      ),
                    array('indexes',    $innerHTMLIndexes                                           ),
                    array('more',       __('read more', 'pure')                              ),
                )
            );
            return $innerHTML;
        }
        private function innerHTML_miniature($data, $parameters, $attribute_str){
            $innerHTMLIndexes   = $this->innerHTMLIndexes($data);
            $innerHTML          = Initialization::instance()->html(
                'ReportA/with_miniature',
                array(
                    array('attribute',  $attribute_str                                              ),
                    array('title',      $data->post->title                                          ),
                    array('label_by',   __('by', 'pure')                                     ),
                    array('author',     $data->author->name                                         ),
                    array('day',        date('d', strtotime($data->post->date))                     ),
                    array('month',      date('F', strtotime($data->post->date))                     ),
                    array('year',       date('Y', strtotime($data->post->date))                     ),
                    array('comments',   $data->post->comments                                       ),
                    array('views',      $data->post->views                                          ),
                    array('karma',      ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)),
                    array('post_url',   $data->post->url                                            ),
                    array('miniature',  $data->post->miniature                                      ),
                    array('indexes',    $innerHTMLIndexes                                           ),
                    array('more',       __('read more', 'pure')                              ),
                )
            );
            return $innerHTML;
        }
        private function innerHTML_excerpt($data, $parameters, $attribute_str){
            $innerHTMLIndexes   = $this->innerHTMLIndexes($data);
            $innerHTML          = Initialization::instance()->html(
                'ReportA/with_excerpt',
                array(
                    array('attribute',  $attribute_str                                              ),
                    array('title',      $data->post->title                                          ),
                    array('label_by',   __('by', 'pure')                                     ),
                    array('author',     $data->author->name                                         ),
                    array('day',        date('d', strtotime($data->post->date))                     ),
                    array('month',      date('F', strtotime($data->post->date))                     ),
                    array('year',       date('Y', strtotime($data->post->date))                     ),
                    array('comments',   $data->post->comments                                       ),
                    array('views',      $data->post->views                                          ),
                    array('karma',      ($data->post->karma >= 0 ? ($data->post->karma === 0 ? '' : '+').$data->post->karma : '-'.$data->post->karma)),
                    array('excerpt',    $data->post->excerpt                                        ),
                    array('post_url',   $data->post->url                                            ),
                    array('indexes',    $innerHTMLIndexes                                           ),
                    array('more',       __('read more', 'pure')                              ),
                )
            );
            return $innerHTML;
        }
        public function get($data, $parameters = NULL){
            $this->validate($parameters);
            $attribute_str  = ($parameters->attribute->name !== '' ? $parameters->attribute->name.'="'.$parameters->attribute->value.'" ' : '');
            $innerHTML      = '';
            if ($data->post->post_type === 'report'){
                switch($data->post->type){
                    case 'post_without_miniature':
                        $innerHTML = $this->innerHTML_excerpt($data, $parameters, $attribute_str);
                        break;
                    case 'post_with_miniature':
                        if ($data->post->excerpt !== false && $data->post->excerpt !== '') {
                            $innerHTML = $this->innerHTML_miniature_excerpt($data, $parameters, $attribute_str);
                        }else{
                            $innerHTML = $this->innerHTML_miniature($data, $parameters, $attribute_str);
                        }
                        break;
                }
            }
            return $innerHTML;
        }
        private function resources_more(){
            \Pure\Resources\Compressor::instance()->CSS(
                \Pure\Templates\Posts\Thumbnails\Initialization::instance()->configuration->paths->css.'/'.'D.more.css'
            );
            $innerHTML =   \Pure\Templates\ProgressBar\Initialization::instance()->get_resources_of('D');
            return $innerHTML;
        }
        public function more($parameters){
            $innerHTML =    $this->resources_more($parameters).
                            '<div data-type-element="Pure.Posts.Thumbnail.D.More" '.
                                'data-type-more-group="'.   $parameters['group'].'" '.
                                'data-type-more-max="'.     $parameters['maxcount'].'" '.
                                'data-type-more-template="'.$parameters['template'].'" '.
                                'data-type-more-post_type="'.$parameters['post_type'].'" '.
                                'data-type-more-settings="'.$parameters['more_settings'].'" '.
                                'data-type-more-progress="D" '.
                                'data-type-use="Pure.Components.More">'.
                                '<p data-type-element="Pure.Posts.Thumbnail.D.More">'.__('more', 'pure').'</p>'.
                            '</div>'.
                            '<p data-element-type="Pure.Posts.Thumbnail.D.More.Info">'.
                                '<span data-element-type="Pure.Posts.Thumbnail.D.More.Info" data-type-use="Pure.Components.More.Shown" data-type-more-group="'.$parameters['group'].'">'.$parameters['shown'].'</span> / '.
                                '<span data-element-type="Pure.Posts.Thumbnail.D.More.Info">'.$parameters['total'].'</span>'.
                            '</p>'.
                            '<div data-type-element="Pure.Posts.Thumbnail.D.Reset"></div>';
            return $innerHTML;
        }
    }
}
?>