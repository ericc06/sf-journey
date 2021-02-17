<?php

namespace App\Entity;

use App\Repository\FlightRideRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FlightRideRepository::class)
 */
class FlightRide extends Ride
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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"ride"})
     */
    private $baggageInfo;

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

    public function getBaggageInfo(): ?string
    {
        return $this->baggageInfo;
    }

    public function setBaggageInfo(?string $baggageInfo): self
    {
        $this->baggageInfo = $baggageInfo;

        return $this;
    }
}
