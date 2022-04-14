<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $parameters = $containerConfigurator->parameters();

    $parameters->set('merite.vote_activate', '%env(MERITE_VOTE)%');
    $parameters->set('merite.proposition_activate', '%env(MERITE_PROPO)%');

    $containerConfigurator->extension(
        'twig',
        [
            'form_themes' => ['bootstrap_5_layout.html.twig'],
            'paths' => [
                '%kernel.project_dir%/src/AcMarche/MeriteSportif/templates' => 'AcMarcheMeriteSportif',
            ],
            'globals' => [
                'bootcdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
                'vote_activate' => '%merite.vote_activate%',
                'proposition_activate' => '%merite.proposition_activate%',
            ],
        ]
    );
};
