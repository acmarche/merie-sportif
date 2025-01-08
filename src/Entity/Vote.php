<?php

namespace AcMarche\MeriteSportif\Entity;

use Doctrine\DBAL\Types\Types;
use AcMarche\MeriteSportif\Repository\VoteRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private Categorie $categorie;
    #[ORM\ManyToOne(targetEntity: Club::class, inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private Club $club;
    #[ORM\ManyToOne(targetEntity: Candidat::class, inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private Candidat $candidat;
    #[ORM\Column(type: Types::SMALLINT)]
    private int $point;

    public function __construct(Categorie $categorie, Club $club, Candidat $candidat, int $point)
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->categorie = $categorie;
        $this->club = $club;
        $this->candidat = $candidat;
        $this->point = $point;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClub(): Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }

    public function getCategorie(): Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getCandidat(): Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }

    public function getPoint(): int
    {
        return $this->point;
    }

    public function setPoint(int $point): self
    {
        $this->point = $point;

        return $this;
    }
}
