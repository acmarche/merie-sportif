<?php

namespace AcMarche\MeriteSportif\Setting;

enum SettingEnum: string
{
    case MODE_VOTE = 'Vote';
    case MODE_PROPOSITION = 'Proposition';
    case MODE_CLOSED = 'Clôturé';
    case EMAILS = 'emails';

    public static function modes(): array
    {
        return [
            'Proposition' => 'Proposition',
            'Vote' => 'Vote',
            'Clôturé' => 'Clôturé',
        ];
    }
}
