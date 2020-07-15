<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MarqueRepository")
 */
class Marque
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="100", maxMessage="Le nom saisie {{ value }} est trop long")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Moto", mappedBy="marque")
     */
    private $moto;

    public function __construct()
    {
        $this->moto = new ArrayCollection();
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
    /**
     * @return Collection|Moto[]
     */
    public function getMoto(): Collection
    {
        return $this->moto;
    }

    public function addMoto(Moto $moto): self
    {
        if (!$this->moto->contains($moto)) {
            $this->moto[] = $moto;
            $moto->setCategorie($this);
        }

        return $this;
    }

    public function removeMoto(Moto $moto): self
    {
        if ($this->moto->contains($moto)) {
            $this->moto->removeElement($moto);
            // set the owning side to null (unless already changed)
            if ($moto->getCategorie() === $this) {
                $moto->setCategorie(null);
            }
        }

        return $this;
    }
}
