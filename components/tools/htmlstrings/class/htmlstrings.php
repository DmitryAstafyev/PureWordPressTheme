<?php
namespace Pure\Components\Tools\HTMLStrings{
    class HTMLParser {
        public function render_innocuous_tag($source, $tag){
            $result = preg_replace('/<\s*'.$tag.'\b[^>]*>/si',  '&lt;&#47;'.$tag.'&gt;', $source);
            $result = preg_replace('/<\s*\/\s*'.$tag.'\s*>/si', '&lt;&#47;'.$tag.'&gt;', $result);
            return $result;
        }
        public function remove_tags_from_string_without_tags($source, $allowed_tags = array(), $exclusions_tags = array()){
            //Step 1. Get exclusions
            $exclusions = array();
            foreach($exclusions_tags as $exclusions_tag){
                $result = preg_match_all('/<\s*'.$exclusions_tag.'\b[^>]*>(.*?)<\s*\/\s*'.$exclusions_tag.'\s*>/i', $source, $matches);
                if ((int)$result > 0){
                    foreach($matches[0] as $key=>$match){
                        $exclusions[] = (object)array(
                            'full'      =>$matches[0][$key],
                            'cleared'   =>$this->render_innocuous_tag($matches[0][$key], 'script'),
                            'id'        =>uniqid().uniqid()
                        );
                    }
                }
            }
            //Step 2. Remove from source all exclusions
            $cleared_source = $source;
            foreach($exclusions as $exclusion){
                $cleared_source = str_replace($exclusion->full, $exclusion->id, $cleared_source);
            }
            //Step 3. Clear source
            $cleared_source = $this->remove_tags_from_string($cleared_source, $allowed_tags);
            //Step 4. Back all exclusions
            foreach($exclusions as $exclusion){
                $cleared_source = str_replace($exclusion->id, $exclusion->cleared, $cleared_source);
            }
            return $cleared_source;
        }
        public function remove_tags_from_string($source, $allowed_tags = array()){
            $_allowed_tags  = '';
            $_map           = function($item, $key) use (&$_allowed_tags){
                $_allowed_tags .= '<'.strtolower($item).'>';
            };
            if (@array_walk($allowed_tags, $_map) !== false){
                $result     = strip_tags($source, $_allowed_tags);
            }else{
                $result     = '';
            }
            return $result;
        }
        public function remove_attributes_except($source, $allowed = array(), $validate_attribute = false){
            $HTMLValidator  = new HTMLAttributesValidator();
            $allowed        = array_map('strtolower', $allowed);
            $callback       = function($match) use ($allowed, $validate_attribute, $HTMLValidator){
                $_match     = $match[0];
                $_match     = preg_replace('/\s*=\s*/i', '=', $_match);
                $_callback  = function($matches) use ($allowed, $validate_attribute, $HTMLValidator){
                    $result     = '';
                    $is_allow   = in_array(strtolower($matches[1]), $allowed);
                    if ($is_allow !== false){
                        if ($validate_attribute !== false && method_exists($HTMLValidator, $matches[1]) !== false){
                            $validate_method    = $matches[1];
                            $attribute_callback = function($matches) use($HTMLValidator, $validate_method){
                                $result = $HTMLValidator->$validate_method($matches[2]);
                                return ($result !== false ? $matches[1].$result.$matches[3] : '"" ');
                            };
                            $result = preg_replace_callback('/(["\'])(.*?)(["\'])/i', $attribute_callback, $matches[0]);
                        }else{
                            $result = $matches[0];
                        }
                    }
                    return $result;
                };
                $_match     = preg_replace_callback('/\s([a-z][a-z0-9_-]*)=(["\'].*?["\'])/i', $_callback, $_match);
                $_callback  = function($matches) use ($allowed){
                    return (in_array(strtolower($matches[1]),$allowed) !== false ? $matches[0] : '');
                };
                $_match     = preg_replace_callback('/\s([a-z][a-z0-9_-]*)=(["\'].*?["\'])/i', $_callback, $_match);
                return $_match;
            };
            $HTMLValidator  = NULL;
            return preg_replace_callback('/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i', $callback, $source);
        }
        public function get_attribute($source_string, $attribute){
            preg_match('/'.$attribute.'="(.*?)"/i', $source_string, $matches);
            if (is_string($matches[0]) === false){
                preg_match("/".$attribute."='(.*?)'/i", $source_string, $matches);
            }
            if (is_string($matches[0]) !== false){
                return mb_substr($matches[0], mb_strlen($attribute.'="'), mb_strlen($matches[0]) - mb_strlen($attribute.'="') - 1);
            }
            return NULL;
        }
    }
    class HTMLAttributesValidator{
        public function href($href){
            return filter_var($href, FILTER_VALIDATE_URL);
        }
        public function target($target){
            if (strpos($target, '_blank'    ) !== -1 ){ return '_blank';    }
            if (strpos($target, '_self'     ) !== -1 ){ return '_self';     }
            if (strpos($target, '_parent'   ) !== -1 ){ return '_parent';   }
            if (strpos($target, '_top'      ) !== -1 ){ return '_top';      }
            return '_blank';
        }
    }
}
?>