<?php
namespace Pure\Components\Responsive{
    class Core{
        public function getScheme($echo = false){
            $innerHTML = '';
            if (is_front_page() === true) {
                $innerHTML = $this->innerHTMLDefineScheme('front.xml');
            } else {
                switch (\Pure\Configuration::instance()->globals->requests->type) {
                    case 'BUDDY':
                        switch (\Pure\Configuration::instance()->globals->requests->BUDDY) {
                            case 'member::activities':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                            case 'member::groups':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                            case 'member::friends':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                            case 'groups::group':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                            case 'members':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                            case 'groups':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                        }
                        break;
                    case 'SPECIAL':
                        switch (\Pure\Configuration::instance()->globals->requests->SPECIAL->request) {
                            case 'CREATEPOST':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                            case 'CREATEEVENT':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                            case 'EDITPOST':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                            case 'EDITEVENT':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                            case 'TOP':
                                $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                                break;
                        }
                        break;
                    case 'POST':
                        switch (\Pure\Configuration::instance()->globals->requests->POST->post_type) {
                            case 'post':
                                $innerHTML = $this->innerHTMLDefineScheme('post.xml');
                                break;
                            case 'event':
                                $innerHTML = $this->innerHTMLDefineScheme('post.xml');
                                break;
                        }
                        break;
                    case 'PAGE':
                        $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                        break;
                    case 'AUTHOR':
                        $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                        break;
                    case 'CATEGORY':
                        $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                        break;
                    case 'TAG':
                        $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                        break;
                    case 'SEARCH':
                        $innerHTML = $this->innerHTMLDefineScheme('standard.xml');
                        break;
                }
            }
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
        private function innerHTMLDefineScheme($scheme){
            return '<!--#responsive scheme="'.Initialization::instance()->configuration->urls->resources.'/'.$scheme.'" -->';
        }
    }
}
?>