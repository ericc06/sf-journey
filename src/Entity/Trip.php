<?php

namespace App\Entity;

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
     * @ORM\OneToMany(targetEntity=Card::class, mappedBy="trip", cascade={"persist", "remove"})
     * @Groups({"trip"})
     */
    private $cards;

    /**
     * @ORM\ManyToOne(targetEntity=Journey::class, inversedBy="trips")
     * @Groups({"journey_link"})
     */
    private $journey;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
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
     * @return Collection|Card[]
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setTrip($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getTrip() === $this) {
                $card->setTrip(null);
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
