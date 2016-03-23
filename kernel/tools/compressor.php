<?php
namespace Pure\Resources{
    class CompressorFiles{
        private $folder;
        private $site_url;
        private $ClearString;
        private $Properties;
        private function checkFolder($folder){
            if (file_exists(\Pure\Configuration::instance()->dir($folder)) === false){
                mkdir($folder);
            }
        }
        public function create($parent_file, $type){
            $getFilename = function($parent_file, $type){
                $sourceURL  = function($source_file){
                    $source_url = preg_replace('/\\\/', '/', $source_file);
                    $source_url = strtolower($source_url);
                    $source_url = preg_replace('/.*wp-content\//u', $this->site_url.'/wp-content/', $source_url);
                    return $source_url;
                };
                do{
                    $output_filename = uniqid().'.'.$type;
                    if (file_exists(\Pure\Configuration::instance()->dir($this->folder.'/'.$output_filename)) === false){
                        return (object)array(
                            'full'          =>$this->folder.'/'.$output_filename,
                            'base'          =>$output_filename,
                            'url'           =>$this->site_url.'/wp-content/CompressorFiles/'.$output_filename,
                            'source_url'    =>$sourceURL($parent_file)
                        );
                    }
                }while(true);
            };
            //Read content
            $output     = @file_get_contents($parent_file);
            //Prepare content
            if (method_exists($this->ClearString, $type) !== false){
                $output = $this->ClearString->$type($output);
            }
            //Set properties
            if (method_exists($this->Properties, $type) !== false){
                $output = $this->Properties->$type($output);
            }
            if (trim($output) !== ''){
                //Get new file name
                $filename   = $getFilename($parent_file, $type);
                //Write new file
                $result     = @file_put_contents($filename->full, $output);
                return ($result !== false ? $filename : false);
            }else{
                return false;
            }
        }
        function __construct(){
            $this->folder       = $_SERVER['DOCUMENT_ROOT'].'/wp-content/CompressorFiles';
            $this->site_url     = site_url();
            $this->ClearString  = new ClearString();
            $this->Properties   = new Properties();
            $this->checkFolder($this->folder);
        }
    }
    class ClearString{
        public function clearCoding($text){
            $text = preg_replace('/([^\pL\pN\pP\pS\pZ])|([\xC2\xA0])/u',    ' ', $text);
            $text = iconv('utf-8', 'utf-8//IGNORE', $text);
            //$result = preg_replace('/[^A-Za-z0-9_\[\]\-\=\"\'\:\.\,\{\}\:\;\%\@&\(\)\s\*\~\+\>\<\/\!]/',    '', $result);
            return $text;
        }
        public function clearSpaces($text){
            $text = preg_replace('/\r\n/',      ' ', $text);
            $text = preg_replace('/\n/',        ' ', $text);
            $text = preg_replace('/\t/',        ' ', $text);
            $text = preg_replace('/\s{2,}/',    ' ', $text);
            return $text;
        }
        public function css($text){
            $text = $this->clearCoding($text);
            $text = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!',   ' ', $text);
            $text = preg_replace('/﻿@charset.*;/u',                  ' ', $text);
            $text = $this->clearSpaces($text);
            return $text;
        }
        public function js($text){
            require_once('JSMin.php');
            $text = JSMin::minify($text);
            //$text = $this->clearCoding($text);
            //$text = $this->clearSpaces($text);
            return $text;
        }
    }
    class Properties{
        public function css($text){
            $text = preg_replace('/\%site_url\%/', site_url(), $text);
            return $text;
        }
    }
}
?>