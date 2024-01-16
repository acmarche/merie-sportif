<?php

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twigConfig): void {
    $twigConfig
        ->formThemes(['bootstrap_5_layout.html.twig'])
        ->path('%kernel.project_dir%/src/AcMarche/MeriteSportif/templates', 'AcMarcheMeriteSportif')
        ->global('bootcdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css')
        ->global('merite_vote_activate', '%env(MERITE_VOTE)%')
        ->global('merite_email', '%env(MERITE_EMAIL)%')
        ->global('merite_year', '%env(MERITE_YEAR)%')
        ->global('merite_proposition_activate', '%env(MERITE_PROPO)%');
};
