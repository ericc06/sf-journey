<?php

namespace App\Entity;

use App\Repository\BusRideRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BusRideRepository::class)
 */
class BusRide extends Ride
{
    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $meansNumber;

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
