<?php

namespace AcMarche\MeriteSportif\Entity;

use AcMarche\MeriteSportif\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public int $id;

    #[ORM\Column(length: 20)]
    public int $year = 2025;

    #[ORM\Column(length: 80)]
    public ?string $mode = null;

    #[ORM\Column()]
    public array $emails = [];

    #[ORM\Column(length: 40)]
    public string $emailFrom = "";

}
