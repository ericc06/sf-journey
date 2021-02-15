<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TripRepository::class)
 */
class Trip
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"trip"})
     */
    private $tripStartDate;

    /**
     * @ORM\OneToMany(targetEntity=Ride::class, mappedBy="Trip", cascade={"persist", "remove"})
     * @Groups({"trip"})
     */
    private $rides;

    /**
     * @ORM\ManyToOne(targetEntity=Journey::class, inversedBy="trips", fetch="LAZY")
     * @Groups({"journey_link"})
     */
    private $journey;

    public function __construct()
    {
        $this->rides = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTripStartDate(): ?\DateTimeInterface
    {
        return $this->tripStartDate;
    }

    public function setTripStartDate(?\DateTimeInterface $tripStartDate): self
    {
        $this->tripStartDate = $tripStartDate;

        return $this;
    }

    /**
     * @return Collection|Ride[]
     */
    public function getRides(): Collection
    {
        return $this->rides;
    }

    public function addRide(Ride $ride): self
    {
        if (!$this->rides->contains($ride)) {
            $this->rides[] = $ride;
            $ride->setTrip($this);
        }

        return $this;
    }

    public function removeRide(Ride $ride): self
    {
        if ($this->rides->removeElement($ride)) {
            // set the owning side to null (unless already changed)
            if ($ride->getTrip() === $this) {
                $ride->setTrip(null);
            }
        }

        return $this;
    }

    public function getJourney(): ?Journey
    {
        return $this->journey;
    }

    public function setJourney(?Journey $journey): self
    {
        $this->journey = $journey;

        return $this;
    }
}
