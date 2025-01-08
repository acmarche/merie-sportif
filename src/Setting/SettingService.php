<?php

namespace AcMarche\MeriteSportif\Setting;

use AcMarche\MeriteSportif\Repository\SettingRepository;

/**
 * Use for global Twig
 */
class SettingService
{
    public function __construct(private readonly SettingRepository $settingRepository) {}

    public function mode(): string
    {
        $setting = $this->settingRepository->findOne();

        return $setting->mode;
    }

    public function year(): int
    {
        $setting = $this->settingRepository->findOne();

        return $setting->year;
    }
}