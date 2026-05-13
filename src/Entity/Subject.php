<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'subject', cascade: ['persist', 'remove'])]
    private ?ReservationRoom $reservation = null;

    #[ORM\ManyToOne(inversedBy: 'subjects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $private = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Titre obligatoire.')]
    private ?string $title = null;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'subject')]
    private Collection $posts;

    /**
     * @var Collection<int, UserLike>
     */
    #[ORM\OneToMany(targetEntity: UserLike::class, mappedBy: 'subject')]
    private Collection $userLikes;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Gedmo\Slug(fields: ['title'])]
    #[ORM\Column(length: 255, unique : true)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->userLikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservation(): ?ReservationRoom
    {
        return $this->reservation;
    }

    public function setReservation(?ReservationRoom $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): static
    {
        $this->private = $private;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setSubject($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getSubject() === $this) {
                $post->setSubject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserLike>
     */
    public function getUserLikes(): Collection
    {
        return $this->userLikes;
    }

    public function addUserLike(UserLike $userLike): static
    {
        if (!$this->userLikes->contains($userLike)) {
            $this->userLikes->add($userLike);
            $userLike->setSubject($this);
        }

        return $this;
    }

    public function removeUserLike(UserLike $userLike): static
    {
        if ($this->userLikes->removeElement($userLike)) {
            // set the owning side to null (unless already changed)
            if ($userLike->getSubject() === $this) {
                $userLike->setSubject(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
