<?php

use AcMarche\MeriteSportif\Token\TokenManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {

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
