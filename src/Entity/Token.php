<?php

namespace AcMarche\MeriteSportif\Entity;

use Doctrine\DBAL\Types\Types;
use DateTimeImmutable;
use AcMarche\MeriteSportif\Repository\TokenRepository;
use Stringable;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Table(name: 'token')]
class Token implements Stringable
{
    use TimestampableTrait;
    use UuidTrait;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    #[Assert\NotBlank]
    protected string $value;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    protected DateTimeInterface $expire_at;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'token')]
    protected User $user;

    public function __construct()
    {
        $this->value = bin2hex(random_bytes(20));
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getExpireAt(): DateTimeInterface
    {
        return $this->expire_at;
    }

    public function setExpireAt(DateTimeInterface $expire_at): self
    {
        $this->expire_at = $expire_at;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
