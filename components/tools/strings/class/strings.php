<?php
namespace Pure\Components\Tools\Strings{
    class Strings {
        public function mb_str_replace($needle, $replacement, $haystack)
        {
            $needle_len         = mb_strlen($needle             );
            $replacement_len    = mb_strlen($replacement        );
            $pos                = mb_strpos($haystack, $needle  );
            while ($pos !== false)
            {
                $haystack   = mb_substr($haystack, 0, $pos) . $replacement
                    . mb_substr($haystack, $pos + $needle_len);
                $pos        = mb_strpos($haystack, $needle, $pos + $replacement_len);
            }
            return $haystack;
        }
        public function mb_clear($haystack)
        {
            $purestr = $haystack;
            $purestr = $this->mb_str_replace("\r\n","",    $purestr);
            $purestr = $this->mb_str_replace("\n","",      $purestr);
            $purestr = $this->mb_str_replace("\n\r","",    $purestr);
            $purestr = $this->mb_str_replace("\r","",      $purestr);
            $purestr = $this->mb_str_replace("\t","",      $purestr);
            return $purestr;
        }
        public function get_int_from_string($source){
            return preg_replace("/[^0-9]/", '', $source);
        }
        public function remove_int_from_string($source){
            return preg_replace("/[0-9]/", '', $source);
        }
        public function substr_utf8($str,$from,$len) {
            # utf8 substr
            # http://www.yeap.lv
            return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s', '$1',$str);
        }
    }
}
?>