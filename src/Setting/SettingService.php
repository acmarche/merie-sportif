<?php

namespace AcMarche\MeriteSportif\Setting;

use AcMarche\MeriteSportif\Entity\Setting;
use AcMarche\MeriteSportif\Repository\SettingRepository;

/**
 * Use for global Twig
 */
class SettingService
{
    private ?Setting $setting = null;

    public function __construct(
        private readonly SettingRepository $settingRepository,
    ) {}

    public function init(): void
    {
        if (!$this->setting) {
            $this->setting = $this->settingRepository->findOne();
        }
    }

    public function mode(): string
    {
        $this->init();

        return $this->setting->mode;
    }

    public function year(): int
    {
        $this->init();

        return $this->setting->year;
    }

    public function emails(): array
    {
        $this->init();

        return $this->setting->emails;
    }

    public function emailFrom(): string
    {
        $this->init();

        return $this->setting->emailFrom;
    }

}