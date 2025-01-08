<?php

use AcMarche\MeriteSportif\Entity\User;
use AcMarche\MeriteSportif\Security\MeriteAuthenticator;
use AcMarche\MeriteSportif\Security\MeriteLdapAuthenticator;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

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

    $main = [
        'provider' => 'merite_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'login_throttling' => [
            'max_attempts' => 6, // per minute...
        ],
        'remember_me' => [
            'secret' => '%kernel.secret%',
            'lifetime' => 604800,
            'path' => '/',
            'always_remember_me' => true,
        ],
        'form_login' => [],
        'entry_point' => MeriteAuthenticator::class,
        'switch_user' => true,
    ];

    $authenticators = [MeriteAuthenticator::class];
    if (interface_exists(LdapInterface::class)) {
        $authenticators[] = MeriteLdapAuthenticator::class;
        $main['form_login_ldap'] = [
            'service' => Ldap::class,
            'check_path' => 'app_login',
        ];
    }

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
