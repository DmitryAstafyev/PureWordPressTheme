<?php
    wp_footer();
    $LoginForm          = \Pure\Templates\Authorization\Login\Initialization::instance()->get('A', 'after');
    $LoginForm->innerHTML(
        (object)array(
            'echo'=>true
        )
    );
    $LoginForm          = NULL;
    $ResetForm          = \Pure\Templates\Authorization\Reset\Initialization::instance()->get('A', 'after');
    $ResetForm->innerHTML(
        (object)array(
            'echo'=>true
        )
    );
    $ResetForm          = NULL;
    $RegistrationForm   = \Pure\Templates\Authorization\Registration\Initialization::instance()->get('A', 'after');
    $RegistrationForm->innerHTML(
        (object)array(
            'echo'=>true
        )
    );
    $RegistrationForm   = NULL;
    \Pure\Resources\Compressor                              ::instance()->init();
    \Pure\Components\Attacher\Module\Attacher               ::instance()->publishAfterLoadCommand(true);
?>
</body>
</html>