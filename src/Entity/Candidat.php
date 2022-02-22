<?php

namespace AcMarche\MeriteSportif\Entity;

use AcMarche\MeriteSportif\Repository\CandidatRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: CandidatRepository::class)]
class Candidat implements Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    #[ORM\Column(type: 'string', length: 100)]
    private ?string $nom;
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $prenom;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $palmares;
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $add_by;
    #[ORM\Column(type: 'boolean')]
    private ?bool $validate;
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $sport;
    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'candidats')]
    private ?Categorie $categorie;
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'candidat', orphanRemoval: true)]
    private Collection|array $votes;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="candidat_image", fileNameProperty="imageName", size="imageSize")
     */
    private File $imageFile;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageName = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $imageSize = null;
    /**
     * Utilisez pendant le vote
     */
    private int $position;

    public function __construct()
    {
        $this->position = 0;
        $this->validate = true;
        $this->votes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nom.' '.$this->prenom;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $imageFile
     * @throws Exception
     */
    public function setImageFile(File|UploadedFile $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getImageFile(): File
    {
        return $this->imageFile;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setCandidat($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getCandidat() === $this) {
                $vote->setCandidat(null);
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPalmares(): ?string
    {
        return $this->palmares;
    }

    public function setPalmares(?string $palmares): self
    {
        $this->palmares = $palmares;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): self
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getAddBy(): ?string
    {
        return $this->add_by;
    }

    public function setAddBy(?string $add_by): self
    {
        $this->add_by = $add_by;

        return $this;
    }

    public function getValidate(): bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }

    public function getSport(): ?string
    {
        return $this->sport;
    }

    public function setSport(?string $sport_temporaire): void
    {
        $this->sport = $sport_temporaire;
    }
}
