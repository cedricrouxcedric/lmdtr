<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="{{ value }} est deja utilisé."
 * )
 * @UniqueEntity(
 *     fields={"username"},
 *     message="{{ value }} est deja utilisé."
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true )
     * @Assert\Email(message = "l'adresse '{{ value }}' n'est pas un email valide.")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit contenir minimum 8 caractères")
     */
    private $password;

    /**
     * * @Assert\EqualTo(propertyPath="password", message="Vous n'avez pas tapé le même mot de passe")
     */
    private $confirm_password;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmationCode;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validateAccount = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\OneToMany(targetEntity=Moto::class, mappedBy="vendeur", orphanRemoval=true)
     */
    private $motos;

    /**
     * @ORM\OneToMany  (targetEntity="App\Entity\Piecedetachee", mappedBy="vendeur", orphanRemoval=true)
     */
    private $piecedetachees;

    /**
     * @ORM\OneToMany(targetEntity=Commentaires::class, mappedBy="auteur", orphanRemoval=true)
     */
    private $commentaires;

    /**
     * @ORM\Column(type="boolean")
     */
    private $rgpd = false;

    /**
     * @ORM\OneToMany(targetEntity=Articles::class, mappedBy="auteur", orphanRemoval=true)
     */
    private $articles;

    public function __construct()
    {
        $this->motos = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirm_password;
    }

    /**
     * @param mixed $confirm_password
     */
    public function setConfirmPassword($confirm_password): void
    {
        $this->confirm_password = $confirm_password;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getConfirmationCode()
    {
        return $this->confirmationCode;
    }

    /**
     * @param mixed $confirmationCode
     */
    public function setConfirmationCode($confirmationCode): void
    {
        $this->confirmationCode = $confirmationCode;
    }

    /**
     * @return mixed
     */
    public function getValidateAccount()
    {
        return $this->validateAccount;
    }

    /**
     * @param mixed $validateAccount
     */
    public function setValidateAccount($validateAccount): void
    {
        $this->validateAccount = $validateAccount;
    }

    /**
     * @return mixed
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }

    /**
     * @param mixed $resetToken
     */
    public function setResetToken($resetToken): void
    {
        $this->resetToken = $resetToken;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Moto[]
     */
    public function getMotos(): Collection
    {
        return $this->motos;
    }

    public function addMoto(Moto $moto): self
    {
        if (!$this->motos->contains($moto)) {
            $this->motos[] = $moto;
            $moto->setVendeur($this);
        }

        return $this;
    }

    public function removeMoto(Moto $moto): self
    {
        if ($this->motos->contains($moto)) {
            $this->motos->removeElement($moto);
            // set the owning side to null (unless already changed)
            if ($moto->getVendeur() === $this) {
                $moto->setVendeur(null);
            }
        }
        return $this;
    }

    /**
     * @return @return Collection|Piecedetachee[]
     */
    public function getPiecedetachees()
    {
        return $this->piecedetachees;
    }

    public function addPiecedetachee(Piecedetachee $piecedetachee): self
    {
        if (!$this->piecedetachees->contains($piecedetachee)) {
            $this->piecedetachees[] = $piecedetachee;
            $piecedetachee->setVendeur($this);
        }

        return $this;
    }

    public function removePiecedetachee(Piecedetachee $piecedetachee): self
    {
        if ($this->piecedetachees->contains($piecedetachee)) {
            $this->piecedetachees->removeElement($piecedetachee);
            // set the owning side to null (unless already changed)
            if ($piecedetachee->getVendeur() === $this) {
                $piecedetachee->setVendeur(null);
            }
        }
        return $this;
    }


    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * @return Collection|Commentaires[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaires $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setAuteur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getAuteur() === $this) {
                $commentaire->setAuteur(null);
            }
        }

        return $this;
    }

    public function getRgpd(): ?bool
    {
        return $this->rgpd;
    }

    public function setRgpd(bool $rgpd): self
    {
        $this->rgpd = $rgpd;

        return $this;
    }

    /**
     * @return Collection|Articles[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setAuteur($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getAuteur() === $this) {
                $article->setAuteur(null);
            }
        }

        return $this;
    }
}
