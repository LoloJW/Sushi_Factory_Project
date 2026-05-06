<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email doit avoir un format valide")]
    private ?string $email = null;

    /** @var list<string> $roles */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire')]
    #[Assert\Length(min: 12, minMessage: 'Le mot de passe doit avoir au moins 12 caractères')]
    #[Assert\Regex(pattern: '/[A-Z]/', message: 'Le mot de passe doit contenir au moins une majuscule')]
    #[Assert\Regex(pattern: '/[a-z]/', message: 'Le mot de passe doit contenir au moins une minuscule')]
    #[Assert\Regex(pattern: '/[0-9]/', message: 'Le mot de passe doit contenir au moins un chiffre')]
    #[Assert\Regex(pattern: '/[^a-zA-Z0-9]/', message: 'Le mot de passe doit contenir au moins un caractère special')]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Assert\Length(min: 2, max: 30, minMessage: 'Le prénom doit avoir au moins 2 caractères', maxMessage: 'Le prénom doit avoir au plus 30 caractères')]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(min: 2, max: 30, minMessage: 'Le nom doit avoir au moins 2 caractères', maxMessage: 'Le nom doit avoir au plus 30 caractères')]
    private ?string $lastName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $joinedAt = null;

    #[ORM\Column]
    private ?bool $isVerified = null;

    /**
     * @var Collection<int, ReservationRoom>
     */
    #[ORM\OneToMany(targetEntity: ReservationRoom::class, mappedBy: 'user')]
    private Collection $reservationRooms;

    /**
     * @var Collection<int, Subject>
     */
    #[ORM\OneToMany(targetEntity: Subject::class, mappedBy: 'user')]
    private Collection $subjects;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user')]
    private Collection $posts;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imgProfile = null;

    #[Assert\File(
        maxSize: '2M',
        extensions: ['png', 'jpg', 'jpeg', 'webp'],
        maxSizeMessage: 'Votre avatar ne doit pas dépasser {{ limit }} {{ suffix }}.',
        extensionsMessage: 'Format invalide, uniquement png, jpg, jpeg et webp acceptés.',
    )]
    #[Vich\UploadableField(mapping: 'avatars', fileNameProperty: 'imgProfile')]
    private ?File $imgFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;
    /**
     * @var Collection<int, ReservationRoom>
     */
    #[ORM\ManyToMany(targetEntity: ReservationRoom::class, mappedBy: 'userInvites')]
    private Collection $reservationRoomsInvites;

    public function __construct()
    {
        $this->reservationRooms = new ArrayCollection();
        $this->subjects = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->reservationRoomsInvites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeImmutable $joinedAt): static
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, ReservationRoom>
     */
    public function getReservationRooms(): Collection
    {
        return $this->reservationRooms;
    }

    public function addReservationRoom(ReservationRoom $reservationRoom): static
    {
        if (!$this->reservationRooms->contains($reservationRoom)) {
            $this->reservationRooms->add($reservationRoom);
            $reservationRoom->setUser($this);
        }

        return $this;
    }

    public function removeReservationRoom(ReservationRoom $reservationRoom): static
    {
        if ($this->reservationRooms->removeElement($reservationRoom)) {
            // set the owning side to null (unless already changed)
            if ($reservationRoom->getUser() === $this) {
                $reservationRoom->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
            $subject->setUser($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        if ($this->subjects->removeElement($subject)) {
            // set the owning side to null (unless already changed)
            if ($subject->getUser() === $this) {
                $subject->setUser(null);
            }
        }

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
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    public function getImgProfile(): ?string
    {
        return $this->imgProfile;
    }

    public function setImgProfile(?string $imgProfile): static
    {
        $this->imgProfile = $imgProfile;

        return $this;
    }

    public function getImgFile(): ?File
    {
        return $this->imgFile;
    }

    public function setImgFile(?File $imgFile): static
    {
        $this->imgFile = $imgFile;

        if (null !== $imgFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, ReservationRoom>
     */
    public function getReservationRoomsInvites(): Collection
    {
        return $this->reservationRoomsInvites;
    }

    public function addReservationRoomsInvite(ReservationRoom $reservationRoomsInvite): static
    {
        if (!$this->reservationRoomsInvites->contains($reservationRoomsInvite)) {
            $this->reservationRoomsInvites->add($reservationRoomsInvite);
            $reservationRoomsInvite->addUserInvite($this);
        }

        return $this;
    }

    public function removeReservationRoomsInvite(ReservationRoom $reservationRoomsInvite): static
    {
        if ($this->reservationRoomsInvites->removeElement($reservationRoomsInvite)) {
            $reservationRoomsInvite->removeUserInvite($this);
        }

        return $this;
    }
}
