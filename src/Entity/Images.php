<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 */
class Images
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
     * @ORM\ManyToOne(targetEntity=Moto::class, inversedBy="images")
     * @ORM\JoinColumn (name="moto",referencedColumnName="id", onDelete="CASCADE")
     */
    private $moto;

    /**
     * @ORM\ManyToOne(targetEntity=Piecedetachee::class, inversedBy="images")
     */
    private $piecedetachee;

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

    public function getMoto(): ?Moto
    {
        return $this->moto;
    }

    public function setMoto(?Moto $moto): self
    {
        $this->moto = $moto;

        return $this;
    }

    public function getPiecedetachee(): ?Piecedetachee
    {
        return $this->piecedetachee;
    }

    public function setPiecedetachee(?Piecedetachee $piecedetachee): self
    {
        $this->piecedetachee = $piecedetachee;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
