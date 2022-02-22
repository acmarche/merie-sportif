<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'twig',
        [
            'form_themes' => ['bootstrap_5_layout.html.twig'],
            'paths' => [
                '%kernel.project_dir%/src/AcMarche/MeriteSportif/templates' => 'AcMarcheMeriteSportif',
            ],
            'globals' => [
             //   'vote_activate' => '%merite.vote_activate%',
             //   'proposition_activate' => '%merite.proposition_activate%',
            ],
        ]
    );
};
