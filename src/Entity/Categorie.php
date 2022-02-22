<?php

namespace AcMarche\MeriteSportif\Entity;

use AcMarche\MeriteSportif\Repository\CategorieRepository;
use Stringable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table]
#[ORM\UniqueConstraint(columns: ['ordre'])]
#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[UniqueEntity(fields: ['ordre'], message: 'Une catégorie a déjà cet ordre')]
class Categorie implements Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\Column(type: 'string', length: 100)]
    private $nom;
    #[ORM\Column(type: 'text')]
    private $description;
    #[ORM\Column(type: 'smallint', nullable: false, unique: true)]
    private $ordre;
    #[ORM\OneToMany(targetEntity: Candidat::class, mappedBy: 'categorie')]
    private $candidats;
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'categorie', orphanRemoval: true)]
    private $votes;
    private bool $complete = false;
    private int $proposition = 0;
    public function __construct()
    {
        $this->candidats = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }
    public function __toString(): string
    {
        return (string) $this->nom;
    }
    public function isComplete(): bool
    {
        return $this->complete;
    }
    public function setComplete(bool $complete): void
    {
        $this->complete = $complete;
    }
    public function getProposition(): int
    {
        return $this->proposition;
    }
    public function setProposition(int $proposition): void
    {
        $this->proposition = $proposition;
    }
    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): ArrayCollection
    {
        return $this->votes;
    }
    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setCategorie($this);
        }

        return $this;
    }
    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getCategorie() === $this) {
                $vote->setCategorie(null);
            }
        }

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
    /**
     * @return Collection|Candidat[]
     */
    public function getCandidats(): ArrayCollection
    {
        return $this->candidats;
    }
    public function addCandidat(Candidat $candidat): self
    {
        if (!$this->candidats->contains($candidat)) {
            $this->candidats[] = $candidat;
            $candidat->setCategorie($this);
        }

        return $this;
    }
    public function removeCandidat(Candidat $candidat): self
    {
        if ($this->candidats->contains($candidat)) {
            $this->candidats->removeElement($candidat);
            // set the owning side to null (unless already changed)
            if ($candidat->getCategorie() === $this) {
                $candidat->setCategorie(null);
            }
        }

        return $this;
    }
    public function getOrdre()
    {
        return $this->ordre;
    }
    public function setOrdre($ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }
}
