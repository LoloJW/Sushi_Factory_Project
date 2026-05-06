<?php

namespace App\Entity;

use App\Repository\RoomsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomsRepository::class)]
class Rooms
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $roomNumber = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?bool $projector = null;

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

    public function setProjector(bool $projector): static
    {
        $this->projector = $projector;

        return $this;
    }

    public function isWhiteboard(): ?bool
    {
        return $this->whiteboard;
    }

    public function setWhiteboard(bool $whiteboard): static
    {
        $this->whiteboard = $whiteboard;

        return $this;
    }
}
