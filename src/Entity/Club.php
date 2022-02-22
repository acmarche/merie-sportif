<?php

namespace AcMarche\MeriteSportif\Entity;

use AcMarche\MeriteSportif\Repository\ClubRepository;
use Stringable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table]
#[ORM\UniqueConstraint(columns: ['email'])]
#[ORM\Entity(repositoryClass: ClubRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Un club a déjà cette adresse email')]
class Club implements Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\Column(type: 'string', length: 130)]
    private $nom;
    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private $email;
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'club', orphanRemoval: true)]
    private $votes;
    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'club', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $user;
    private bool $voteIsComplete = false;
    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }
    public function __toString(): string
    {
        return (string) $this->nom;
    }
    public function getToken(): ?string
    {
        if (($user = $this->getUser()) === null) {
            return null;
        }

        if (($token = $this->getUser()->getToken()) === null) {
            return null;
        }

        return $token->getValue();
    }
    public function isVoteIsComplete(): bool
    {
        return $this->voteIsComplete;
    }
    public function setVoteIsComplete(bool $voteIsComplete): void
    {
        $this->voteIsComplete = $voteIsComplete;
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
            $vote->setClub($this);
        }

        return $this;
    }
    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getClub() === $this) {
                $vote->setClub(null);
            }
        }

        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
