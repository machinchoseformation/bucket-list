<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\WishRepository")
 */
class Wish
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @Assert\NotBlank(message="Veuillez renseigner votre idée !")
     * @Assert\Length(
     *     min="5",
     *     minMessage="5 caractères minimum svp !",
     *     max="100",
     *     maxMessage="100 caractères max svp !"
     * )
     * @ORM\Column(type="string", length=100)
     */
    private $label;

    /**
     * @Assert\Length(
     *     min="5",
     *     minMessage="5 caractères minimum svp !",
     *     max="10000",
     *     maxMessage="10 000 caractères max svp !"
     * )
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * J'utilise cette propriété dans le WishType
     * Contient un UploadedFile
     * Cette propriété n'est pas sauvegardé par Doctrine
     * @Assert\Image(
     *     maxSize="10M",
     *     maxSizeMessage="Maximum 10 megabytes SVP !",
     *     mimeTypesMessage="Veuillez charger une image !!"
     * )
     */
    private $imageFile;

    /**
     * @return mixed
     */
    public function getImageFile(): ?UploadedFile
    {
        return $this->imageFile;
    }

    /**
     * @param mixed $imageFile
     */
    public function setImageFile(UploadedFile $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateUpdated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="wishes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OrderBy({"dateCreated" = "DESC"})
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="wish", orphanRemoval=true, cascade={"persist"})
     */
    private $comments;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $averageRating;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="wishesCreated")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserWish", mappedBy="wish")
     */
    private $listWishes;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateAverageRating()
    {
        $average = null;

        if (count($this->getComments()) > 0){
            $total = 0;
            foreach($this->getComments() as $c){
                $total += $c->getRating();
            }
            $average = $total / count($this->getComments());
        }

        $this->setAverageRating($average);
    }
    

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->listWishes = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?\DateTimeInterface $dateUpdated): self
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setWish($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getWish() === $this) {
                $comment->setWish(null);
            }
        }

        return $this;
    }

    public function getAverageRating(): ?float
    {
        return round($this->averageRating, 1);
    }

    public function setAverageRating(?float $averageRating): self
    {
        $this->averageRating = $averageRating;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

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
            $listWish->setWish($this);
        }

        return $this;
    }

    public function removeListWish(UserWish $listWish): self
    {
        if ($this->listWishes->contains($listWish)) {
            $this->listWishes->removeElement($listWish);
            // set the owning side to null (unless already changed)
            if ($listWish->getWish() === $this) {
                $listWish->setWish(null);
            }
        }

        return $this;
    }
}
