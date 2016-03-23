<?php
namespace Pure\Requests\Plugins\Thumbnails\Posts{
    class More{
        private function validate(&$parameters, $method){
            switch($method){
                case 'get':
                    $parameters->count              = (integer  )($parameters->count                );
                    $parameters->maximum            = (integer  )($parameters->maximum              );
                    $parameters->template           = (string   )($parameters->template             );
                    $parameters->content            = (string   )($parameters->content              );
                    $parameters->targets            = (string   )($parameters->targets              );
                    $parameters->profile            = (string   )($parameters->profile              );
                    $parameters->days               = (integer  )($parameters->days                 );
                    $parameters->from_date          = (string   )($parameters->from_date            );
                    $parameters->only_with_avatar   = (boolean  )($parameters->only_with_avatar     );
                    $parameters->thumbnails         = (boolean  )($parameters->thumbnails           );
                    $parameters->slider_template    = (string   )($parameters->slider_template      );
                    $parameters->tab_template       = (string   )($parameters->tab_template         );
                    $parameters->presentation       = (string   )($parameters->presentation         );
                    $parameters->tabs_columns       = (integer  )($parameters->tabs_columns         );
                    $parameters->group              = (string   )($parameters->group                );
                    $parameters->post_type          = (string   )($parameters->post_type            );
                    $parameters->sandbox            = (string   )($parameters->sandbox              );
                    break;
            }
        }
        public function get($parameters){
            $this->validate($parameters, 'get');
            require_once(\Pure\Configuration::instance()->dir(\Pure\Configuration::instance()->plugins.'/Thumbnails/Posts/inc/kernel.php'));
            $_parameters    = array(	'content'           => $parameters->content,
                                        'targets'	        => $parameters->targets,
                                        'template'	        => $parameters->template,
                                        'title'		        => '',
                                        'title_type'        => '',
                                        'maxcount'	        => $parameters->maximum,
                                        'only_with_avatar'	=> $parameters->only_with_avatar,
                                        'profile'	        => $parameters->profile,
                                        'days'	            => $parameters->days,
                                        'from_date'         => $parameters->from_date,
                                        'hidetitle'	        => true,
                                        'thumbnails'	    => $parameters->thumbnails,
                                        'slider_template'	=> $parameters->slider_template,
                                        'tab_template'	    => $parameters->tab_template,
                                        'presentation'	    => ($parameters->presentation === 'wrapper' ? 'clear' : $parameters->presentation),
                                        'tabs_columns'	    => $parameters->tabs_columns,
                                        'more'              => false,
                                        'group'             => $parameters->group,
                                        'shown'             => $parameters->count,
                                        'post_type'         => $parameters->post_type,
                                        'sandbox'           => $parameters->sandbox,
            );
            try{
                $widget     = new \Pure\Plugins\Thumbnails\Posts\Builder($_parameters);
                $innerHTML  = $widget->render();
                echo $innerHTML;
            }catch (\Exception $e){
                return 'error';
            }
        }
    }
}
?>