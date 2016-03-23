<?php
namespace Pure\Templates\Posts\Elements\GoogleMap{
    class A{
        private function validate(&$parameters){
            if (is_object($parameters) !== false){
                $result = true;
                $result = ($result === false ? false : (isset($parameters->post_id  ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->on_map   ) !== false ? true : false));
                $result = ($result === false ? false : (isset($parameters->place    ) !== false ? true : false));
                return $result;
            }
            return false;
        }
        public function innerHTML($parameters){
            $innerHTML = '';
            if ($this->validate($parameters) !== false){
                if ($parameters->on_map !== ''){
                    \Pure\Components\Maps\Google\Initialization::instance()->attach(false, 'after');
                    $innerHTML .=   '<!--BEGIN: Post.GoogleMap.A -->'.
                                    '<div data-post-element-type="Pure.Posts.MapContainer.A" '.
                                        'data-google-maps-engine-element="map" '.
                                        'data-google-maps-engine-id="'.$parameters->post_id.'" '.
                                        'data-google-maps-engine-address="'.$parameters->on_map.'" '.
                                        'data-google-maps-engine-lat="-34.397" '.
                                        'data-google-maps-engine-lng="150.644" '.
                                    '></div>'.
                                    '<p data-post-element-type="Pure.Posts.Place.A">'.($parameters->place !== '' ? $parameters->place : $parameters->on_map).'</p>'.
                                    '<!--END: Post.GoogleMap.A -->';
                }
            }
            return $innerHTML;
        }
    }
}
?>