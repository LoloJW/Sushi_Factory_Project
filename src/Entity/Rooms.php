<?php

namespace App\Entity;

use App\Repository\RoomsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: RoomsRepository::class)]
#[UniqueEntity(fields: ['roomNumber'],message: 'Cette salle existe deja')]
class Rooms
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Entrez un numéro de salle.')]
    #[Assert\Positive(message: 'Le numéro de salle doit etre positif.')]
    #[ORM\Column(unique: true)]
    private ?int $roomNumber = null;

    #[Assert\Range(min: 1, max: 100, notInRangeMessage: 'La capacité doit etre comprise entre {{ min }} et {{ max }} personnes.')]
    #[Assert\NotNull(message: 'Entrez la capacité maximale de la salle.')]
    #[ORM\Column]
    private ?int $capacity = null;

    #[Assert\NotNull(message: 'Entrez si la salle possède un projecteur.')]
    #[ORM\Column]
    private ?bool $projector = null;

    #[Assert\NotNull(message: 'Entrez si la salle possède un tableau.')]
    #[ORM\Column]
    private ?bool $whiteboard = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoomNumber(): ?int
    {
        return $this->roomNumber;
    }

    public function setRoomNumber(int $roomNumber): static
    {
        $this->roomNumber = $roomNumber;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function isProjector(): ?bool
    {
        return $this->projector;
    }

    public function setProjector(?bool $projector): static
    {
        $this->projector = $projector;

        return $this;
    }

    public function isWhiteboard(): ?bool
    {
        return $this->whiteboard;
    }

    public function setWhiteboard(?bool $whiteboard): static
    {
        $this->whiteboard = $whiteboard;

        return $this;
    }
    /**
     * @var Collection<int, ReservationRoom>
    */
    #[ORM\OneToMany(targetEntity: ReservationRoom::class, mappedBy: 'room', orphanRemoval: true)]
    private Collection $reservationRooms;
    
    public function __construct()
    {
        $this->reservationRooms = new ArrayCollection();
    }
    public function getReservationRooms(): Collection
    {
        return $this->reservationRooms;
    }
}

