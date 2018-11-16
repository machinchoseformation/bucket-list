<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @ORM\Column(type="simple_array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateRegistered;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Wish", mappedBy="author")
     */
    private $wishesCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserWish", mappedBy="user")
     */
    private $listWishes;

    public function __construct()
    {
        $this->wishesCreated = new ArrayCollection();
        $this->listWishes = new ArrayCollection();
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
        return $this->roles;
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
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getDateRegistered(): ?\DateTimeInterface
    {
        return $this->dateRegistered;
    }

    public function setDateRegistered(\DateTimeInterface $dateRegistered): self
    {
        $this->dateRegistered = $dateRegistered;

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

    /**
     * @return Collection|Wish[]
     */
    public function getWishesCreated(): Collection
    {
        return $this->wishesCreated;
    }

    public function addWishesCreated(Wish $wishesCreated): self
    {
        if (!$this->wishesCreated->contains($wishesCreated)) {
            $this->wishesCreated[] = $wishesCreated;
            $wishesCreated->setAuthor($this);
        }

        return $this;
    }

    public function removeWishesCreated(Wish $wishesCreated): self
    {
        if ($this->wishesCreated->contains($wishesCreated)) {
            $this->wishesCreated->removeElement($wishesCreated);
            // set the owning side to null (unless already changed)
            if ($wishesCreated->getAuthor() === $this) {
                $wishesCreated->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserWish[]
     */
    public function getListWishes(): Collection
    {
        return $this->listWishes;
    }

    public function addListWish(UserWish $listWish): self
    {
        if (!$this->listWishes->contains($listWish)) {
            $this->listWishes[] = $listWish;
            $listWish->setUser($this);
        }

        return $this;
    }

    public function removeListWish(UserWish $listWish): self
    {
        if ($this->listWishes->contains($listWish)) {
            $this->listWishes->removeElement($listWish);
            // set the owning side to null (unless already changed)
            if ($listWish->getUser() === $this) {
                $listWish->setUser(null);
            }
        }

        return $this;
    }
}
