<?php

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twigConfig): void {
    $twigConfig
        ->formThemes(['bootstrap_5_layout.html.twig'])
        ->path('%kernel.project_dir%/src/AcMarche/MeriteSportif/templates', 'AcMarcheMeriteSportif')
        ->global('bootcdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css')
        ->global('vote_activate', '%merite.vote_activate%')
        ->global('merite.email', '%merite.email%')
        ->global('merite.year', '%merite.year%')
        ->global('proposition_activate', '%merite.proposition_activate%');
};
