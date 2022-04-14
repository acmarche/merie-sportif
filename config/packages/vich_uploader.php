<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'vich_uploader',
        [
            'metadata' => ['type' => 'attribute'],
            'mappings' => [
                'candidat_image' => [
                    'uri_prefix' => '/images/candidats',
                    'upload_destination' => '%kernel.project_dir%/public/images/candidats',
                    'namer' => 'vich_uploader.namer_uniqid',
                    'inject_on_load' => false,
                ],
            ],
        ]
    );
};
