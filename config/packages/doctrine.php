<?php

use AcMarche\MeriteSportif\Doctrine\Rand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'mappings' => [
                    'AcMarche\MeriteSportif' => [
                        'is_bundle' => false,
                        'dir' => '%kernel.project_dir%/src/AcMarche/MeriteSportif/src/Entity',
                        'prefix' => 'AcMarche\MeriteSportif',
                        'alias' => 'AcMarche\MeriteSportif',
                    ],
                ],
                'dql' => [
                    'numeric_functions' => [
                        'Rand' => Rand::class
                    ]
                ],
            ],
        ]
    );
};
/**
 *  dql:
numeric_functions:
Rand: App\Doctrine\Rand
 */
