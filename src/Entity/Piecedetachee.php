<?php

namespace App\Entity;

use App\Repository\PiecedetacheeRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PiecedetacheeRepository::class)
 */
class Piecedetachee
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Marque::class, inversedBy="piecedetachees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $marque;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $model;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $prix;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="piecedetachee", orphanRemoval=true, cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="piecedetachees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vendeur;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min = 50,
     *      max = 6200,
     *      minMessage = "La cylindrée minimum est {{ limit }}cc",
     *      maxMessage = "La cylindrée max est { limit }}cc"
     * )
     */
    private $cylindreemoto;

    /**
     * @ORM\Column(type="integer")
     */
    private $usure;

    /**
     * @ORM\Column(type="integer")
     */
    private $anneemoto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmation_code;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validate;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->prix, 0, '', ' ')." €";
    }

    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setPiecedetachee($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getPiecedetachee() === $this) {
                $image->setPiecedetachee(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVendeur()
    {
        return $this->vendeur;
    }

    /**
     * @param mixed $vendeur
     */
    public function setVendeur($vendeur): void
    {
        $this->vendeur = $vendeur;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCylindreemoto(): ?string
    {
        return $this->cylindreemoto;
    }

    public function setCylindreemoto(string $cylindreemoto): self
    {
        $this->cylindreemoto = $cylindreemoto;

        return $this;
    }

    public function getUsure(): ?int
    {
        return $this->usure;
    }

    public function setUsure(int $usure): self
    {
        $this->usure = $usure;

        return $this;
    }

    public function getAnneemoto(): ?int
    {
        return $this->anneemoto;
    }

    public function setAnneemoto(int $anneemoto): self
    {
        $this->anneemoto = $anneemoto;

        return $this;
    }

    public function getConfirmationCode(): ?string
    {
        return $this->confirmation_code;
    }

    public function setConfirmationCode(?string $confirmation_code): self
    {
        $this->confirmation_code = $confirmation_code;

        return $this;
    }

    public function getValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }
}
