<?php

use AcMarche\MeriteSportif\Token\TokenManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('merite.vote_activate', (bool)'%env(MERITE_VOTE)%');
    $parameters->set('merite.proposition_activate', (bool)'%env(MERITE_PROPO)%');

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('AcMarche\MeriteSportif\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests2}']);


    $services->set(TokenManager::class)
        ->arg('$formLoginAuthenticator', service('security.authenticator.form_login.main'));

};
