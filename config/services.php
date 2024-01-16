<?php

use AcMarche\MeriteSportif\Token\TokenManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('merite.vote_activate', '%env(MERITE_VOTE)%');
    $parameters->set('merite.proposition_activate', '%env(MERITE_PROPO)%');
    $parameters->set('merite.year', '%env(MERITE_YEAR)%');
    $parameters->set('merite.email', '%env(MERITE_YEAR)%');

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
