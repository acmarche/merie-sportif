<?php

use AcMarche\MeriteSportif\Entity\User;
use AcMarche\MeriteSportif\Security\MeriteAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;


return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            User::class => [
                'algorithm' => 'auto',
            ],
        ],
    ]);

    $containerConfigurator->extension(
        'security',
        [
            'providers' => [
                'merite_user_provider' => [
                    'entity' => [
                        'class' => User::class,
                        'property' => 'username',
                    ],
                ],
            ],
        ]
    );

    $authenticators = [MeriteAuthenticator::class];

    $main = [
        'provider' => 'merite_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'login_throttling' => [
            'max_attempts' => 6, // per minute...
        ],
        'form_login' => [],
        'entry_point' => MeriteAuthenticator::class,
        'switch_user' => true,
    ];

    $main['custom_authenticator'] = $authenticators;

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => $main,
            ],
        ]
    );
    
};
