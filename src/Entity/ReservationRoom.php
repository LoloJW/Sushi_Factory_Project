<?php

namespace App\Entity;

use App\Enum\ReservationType;
use App\Repository\ReservationRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRoomRepository::class)]
class ReservationRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservationRooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rooms $room = null;

    #[ORM\Column(enumType: ReservationType::class)]
    private ?ReservationType $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $reservedFor = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $timeStart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $timeEnd = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Subject $subject = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'reservationRoomsInvites')]
    private Collection $userInvites;

    public function __construct()
    {
        $this->userInvites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRoom(): ?Rooms
    {
        return $this->room;
    }

    public function setRoom(?Rooms $room): static
    {
        $this->room = $room;
        return $this;
    }

    public function getType(): ?ReservationType
    {
        return $this->type;
    }

    public function setType(ReservationType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getReservedFor(): ?\DateTime
    {
        return $this->reservedFor;
    }

    public function setReservedFor(\DateTime $reservedFor): static
    {
        $this->reservedFor = $reservedFor;

        return $this;
    }

    public function getTimeStart(): ?\DateTime
    {
        return $this->timeStart;
    }

    public function setTimeStart(\DateTime $timeStart): static
    {
        $this->timeStart = $timeStart;

        return $this;
    }

    public function getTimeEnd(): ?\DateTime
    {
        return $this->timeEnd;
    }

    public function setTimeEnd(\DateTime $timeEnd): static
    {
        $this->timeEnd = $timeEnd;

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

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        // unset the owning side of the relation if necessary
        if ($subject === null && $this->subject !== null) {
            $this->subject->setReservation(null);
        }

        // set the owning side of the relation if necessary
        if ($subject !== null && $subject->getReservation() !== $this) {
            $subject->setReservation($this);
        }

        $this->subject = $subject;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserInvites(): Collection
    {
        return $this->userInvites;
    }

    public function addUserInvite(User $userInvite): static
    {
        if (!$this->userInvites->contains($userInvite)) {
            $this->userInvites->add($userInvite);
        }

        return $this;
    }

    public function removeUserInvite(User $userInvite): static
    {
        $this->userInvites->removeElement($userInvite);

        return $this;
    }
}
