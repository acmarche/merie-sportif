<?php

namespace AcMarche\MeriteSportif\Entity;

use AcMarche\MeriteSportif\Repository\UserRepository;
use Stringable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table]
#[ORM\UniqueConstraint(columns: ['username'])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: "Un utilisateur a déjà ce nom d'utilisateur")]
class User implements UserInterface, Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $username;
    #[ORM\Column(type: 'json')]
    private array $roles = [];
    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private string $password;
    #[ORM\Column(type: 'string', length: 100)]
    private $nom;
    #[ORM\OneToOne(targetEntity: Token::class, mappedBy: 'user', cascade: ['remove'])]
    private ?Token $token = null;
    #[ORM\OneToOne(targetEntity: Club::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private $club;
    public function __toString(): string
    {
        return (string) $this->username;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUsername(): ?string
    {
        return $this->username;
    }
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    public function getUserIdentifier(): string
    {
        return $this->username;
    }
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_MERITE';

        return array_unique($roles);
    }
    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }
    public function removeRole(string $role): self
    {
        if (in_array($role, $this->roles, true)) {
            $index = array_search($role, $this->roles);
            unset($this->roles[$index]);
        }

        return $this;
    }
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getSalt(): void
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
    public function getToken(): ?Token
    {
        return $this->token;
    }
    public function setToken(?Token $token): self
    {
        $this->token = $token;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $token === null ? null : $this;
        if ($newUser !== $token->getUser()) {
            $token->setUser($newUser);
        }

        return $this;
    }
    public function getClub(): ?Club
    {
        return $this->club;
    }
    public function setClub(Club $club): self
    {
        $this->club = $club;

        // set the owning side of the relation if necessary
        if ($this !== $club->getUser()) {
            $club->setUser($this);
        }

        return $this;
    }
}