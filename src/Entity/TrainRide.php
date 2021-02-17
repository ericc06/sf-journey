<?php

namespace App\Entity;

use App\Repository\TrainRideRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TrainRideRepository::class)
 */
class TrainRide extends Ride
{
    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"ride"})
     */
    private $seatNumber;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"ride"})
     */
    private $meansNumber;

    public function getSeatNumber(): ?string
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(?string $seatNumber): self
    {
        $this->seatNumber = $seatNumber;

        return $this;
    }

    public function getMeansNumber(): ?string
    {
        return $this->meansNumber;
    }

    public function setMeansNumber(?string $meansNumber): self
    {
        $this->meansNumber = $meansNumber;

        return $this;
    }
}
