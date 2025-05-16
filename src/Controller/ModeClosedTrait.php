<?php

namespace AcMarche\MeriteSportif\Controller;


use AcMarche\MeriteSportif\Entity\Setting;
use AcMarche\MeriteSportif\Setting\SettingEnum;

trait ModeClosedTrait
{
    public function itsClosed(Setting $setting)
    {
        if ($setting->mode === SettingEnum::MODE_CLOSED->value) {
            $this->addFlash('warning', 'Le mérite sportif est clôturé. Merci de votre participation.');
        }

        return $this->redirectToRoute('merite_home');
    }
}