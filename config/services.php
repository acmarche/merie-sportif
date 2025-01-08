<?php

use AcMarche\MeriteSportif\Security\LdapMerite;
use AcMarche\MeriteSportif\Token\TokenManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services
        ->load('AcMarche\MeriteSportif\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests2}']);

    if (interface_exists(LdapInterface::class)) {
        $services
            ->set(Ldap::class)
            ->args(['@Symfony\Component\Ldap\Adapter\ExtLdap\Adapter'])
            ->tag('ldap');
        $services->set(Adapter::class)->args(
            [
                [
                    'host' => '%env(ACLDAP_URL)%',
                    'port' => 636,
                    'encryption' => 'ssl',
                    'options' => [
                        'protocol_version' => 3,
                        'referrals' => false,
                    ],
                ],
            ],
        );

        $services
            ->set(LdapMerite::class)
            ->arg('$adapter', service(Adapter::class))
            ->tag('ldap'); // necessary for new LdapBadge(LdapMerite::class)
    }
    $services
        ->set(TokenManager::class)
        ->arg('$formLoginAuthenticator', service('security.authenticator.form_login.main'));
};
