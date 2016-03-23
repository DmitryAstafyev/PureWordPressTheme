<?php
namespace Pure\Templates\Elements\Warnings{
    class A{
        public function attach($post_id){
            if ((int)$post_id > 0){
                \Pure\Components\PostTypes\Warnings\Module\Initialization::instance()->attach();
                $Warnings = new \Pure\Components\PostTypes\Warnings\Module\Provider();
                $warnings = $Warnings->getWarningsForPost($post_id);
                $Warnings = NULL;
                if ($warnings !== false){
                    \Pure\Components\Attacher\Module\Initialization::instance()->attach();
                    \Pure\Components\Attacher\Module\Attacher::instance()->addSETTING(
                        'pure.templates.warnings.collection',
                        json_encode($warnings),
                        false,
                        true
                    );
                    \Pure\Components\Dialogs\B\Initialization::instance()->attach();
                }
            }
        }
    }
}
?>