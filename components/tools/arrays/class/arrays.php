<?php
namespace Pure\Components\Tools\Arrays{
    class Arrays{
        public function make_array_by_property_of_array_objects($objects_array, $property_name, $to_type = false){
            $result = false;
            if (is_array($objects_array) === true && is_string($property_name) === true){
                $result = array();
                foreach($objects_array as $object){
                    if (isset($object->$property_name) === true){
                        if ($to_type !== false){
                            settype($object->$property_name, $to_type);
                        }
                        $result[] = $object->$property_name;
                    }
                }
            }
            return $result;
        }
    }
}
?>