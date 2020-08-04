<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\MotoRepository")
 *
 */
class Moto
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="100", maxMessage="Le nom saisie {{ value }} est trop long")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie", inversedBy="motos")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     */
    private $categorie;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     */
    private $year;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     */
    private $kilometrage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $a2 = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Marque", inversedBy="moto")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     */
    private $marque;
    /**
     * @ORM\Column(type="string")
     */
    private $model;

    /**
     * @ORM\Column(type="integer")
     */
    private $din;

    /**
     * @ORM\Column(type="integer")
     */
    private $fisc;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="moto", orphanRemoval=true, cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\Column(type="integer")
     */
    private $cylindree;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="motos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vendeur;

    public function __construct()
    {
        $this->images = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->prix, 0, '', ' ')." €";
    }

     /**
     * @param int $prix
     * @return $this
     */
    public function setPrix(int $prix): self
    {
        $this->prix = $prix;
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

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getKilometrage(): ?int
    {
        return $this->kilometrage;
    }

    public function setKilometrage(int $kilometrage): self
    {
        $this->kilometrage = $kilometrage;

        return $this;
    }

    public function getA2(): ?bool
    {
        return $this->a2;
    }

    public function setA2(bool $a2): self
    {
        $this->a2 = $a2;

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

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model): void
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getDin()
    {
        return $this->din;
    }

    /**
     * @param mixed $din
     */
    public function setDin($din): void
    {
        $this->din = $din;
    }

    /**
     * @return mixed
     */
    public function getFisc()
    {
        return $this->fisc;
    }

    /**
     * @param mixed $fisc
     */
    public function setFisc($fisc): void
    {
        $this->fisc = $fisc;
    }

    /**
     * @return mixed
     */
    public function getCylindree()
    {
        return $this->cylindree;
    }

    /**
     * @param mixed $cylindree
     */
    public function setCylindree($cylindree): void
    {
        $this->cylindree = $cylindree;
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
            $image->setMoto($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getMoto() === $this) {
                $image->setMoto(null);
            }
        }
        return $this;
    }

    public function getVendeur(): ?User
    {
        return $this->vendeur;
    }

    public function setVendeur(?User $vendeur): self
    {
        $this->vendeur = $vendeur;

        return $this;
    }
}
