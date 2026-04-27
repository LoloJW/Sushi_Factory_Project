<?php

namespace App\Entity;

use App\Repository\ReservationEquipmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationEquipmentRepository::class)]
class ReservationEquipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipment $equipment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $rentedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $returnAt = null;
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

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): static
    {
        $this->equipment = $equipment;
        return $this;
    }

    public function getRentedAt(): ?\DateTimeImmutable
    {
        return $this->rentedAt;
    }

    public function setRentedAt(\DateTimeImmutable $rentedAt): static
    {
        $this->rentedAt = $rentedAt;
        return $this;
    }

    public function getReturnAt(): ?\DateTimeImmutable
    {
        return $this->returnAt;
    }

    public function setReturnAt(?\DateTimeImmutable $returnAt): static
    {
        $this->returnAt = $returnAt;
        return $this;
    }
}