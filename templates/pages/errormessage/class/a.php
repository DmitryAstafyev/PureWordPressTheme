<?php
namespace Pure\Templates\Pages\ErrorMessage{
    class A{
        public function innerHTML($title, $message, $echo = false){
            \Pure\Configuration::instance()->globals->ErrorMessage = true;
            $GroupWrapper   = \Pure\Templates\Admin\Groups\Initialization::instance()->get('C');
            $innerHTML      = $GroupWrapper->open(array(
                    "title"             =>$title,
                    "group"             =>uniqid(),
                    "echo"              =>false,
                    "opened"            =>true,
                    "content_style"     =>'width:auto;padding:0.5em;',
                    "container_style"   =>'width:70%; left:15%;')
            );
            $innerHTML     .= '<p data-type-element="Pure.Templates.Pages.ErrorMessage.A">'.$message.'</p>';
            $innerHTML     .= $GroupWrapper->close(array("echo"=>false));
            $GroupWrapper   = NULL;
            if ($echo !== false){
                echo $innerHTML;
            }
            return $innerHTML;
        }
    }
}
?>