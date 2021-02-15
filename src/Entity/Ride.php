<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\MappedSuperclass
 * @ORM\DiscriminatorColumn(name="meansType", type="string")
 * @ORM\DiscriminatorMap({
 * "train"="App\Entity\TrainRide",
 * "flight"="App\Entity\FlightRide",
 * "bus"="App\Entity\BusRide"
 * })
 */
abstract class Ride
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ride"})
     */
    protected $startLocation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ride"})
     */
    protected $endLocation;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"ride"})
     */
    protected $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"ride"})
     */
    protected $endDate;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"ride"})
     */
    protected $meansType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"ride"})
     */
    protected $meansStartPoint;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"ride"})
     */
    protected $meansEndPoint;

    /**
     * @ORM\ManyToOne(targetEntity=Trip::class, inversedBy="rides", fetch="LAZY")
     * @ORM\JoinColumn(name="trip_id", referencedColumnName="id")
     * @Groups({"trip_link"})
     */
    protected $trip;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartLocation(): ?string
    {
        return $this->startLocation;
    }

    public function setStartLocation(string $startLocation): self
    {
        $this->startLocation = $startLocation;

        return $this;
    }

    public function getEndLocation(): ?string
    {
        return $this->endLocation;
    }

    public function setEndLocation(string $endLocation): self
    {
        $this->endLocation = $endLocation;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getMeansType(): ?string
    {
        return $this->meansType;
    }

    public function setMeansType(string $meansType): self
    {
        $this->meansType = $meansType;

        return $this;
    }

    public function getMeansStartPoint(): ?string
    {
        return $this->meansStartPoint;
    }

    public function setMeansStartPoint(?string $meansStartPoint): self
    {
        $this->meansStartPoint = $meansStartPoint;

        return $this;
    }

    public function getMeansEndPoint(): ?string
    {
        return $this->meansEndPoint;
    }

    public function setMeansEndPoint(?string $meansEndPoint): self
    {
        $this->meansEndPoint = $meansEndPoint;

        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): self
    {
        $this->trip = $trip;

        return $this;
    }

    public function toArray(): array
    {
        $arrayRide = [
            //"id" => $this->getId(),
            'startLocation' => $this->getStartLocation(),
            'endLocation' => $this->getEndLocation(),
            'startDate' => '2021-03-16T20:00:00+00:00',
            'endDate' => '2021-03-16T20:00:00+00:00',
            //'seatNumber' => $this->getSeatNumber(),
            'meansType' => $this->getMeansType(),
            //'meansNumber' => $this->getMeansNumber(),
            'meansStartPoint' => $this->getMeansStartPoint(),
            'meansEndPoint' => $this->getMeansEndPoint(),
            //'baggageInfo' => $this->getBaggageInfo(),
            //"trip" => $this->getTrip(),
        ];

        return $arrayRide;
    }
}
