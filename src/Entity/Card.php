<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CardRepository::class)
 */
class Card
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"card"})
     */
    private $startLocation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"card"})
     */
    private $endLocation;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"card"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"card"})
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"card"})
     */
    private $seatNumber;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"card"})
     */
    private $meansType;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"card"})
     */
    private $meansNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"card"})
     */
    private $meansStartPoint;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"card"})
     */
    private $meansEndPoint;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"card"})
     */
    private $baggageInfo;

    /**
     * @ORM\ManyToOne(targetEntity=Trip::class, inversedBy="cards", fetch="LAZY")
     * @Groups({"trip_link"})
     */
    private $trip;

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

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getSeatNumber(): ?string
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(?string $seatNumber): self
    {
        $this->seatNumber = $seatNumber;

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

    public function getMeansNumber(): ?string
    {
        return $this->meansNumber;
    }

    public function setMeansNumber(?string $meansNumber): self
    {
        $this->meansNumber = $meansNumber;

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

    public function getBaggageInfo(): ?string
    {
        return $this->baggageInfo;
    }

    public function setBaggageInfo(?string $baggageInfo): self
    {
        $this->baggageInfo = $baggageInfo;

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
        $arrayCard = [
            //"id" => $this->getId(),
            'startLocation' => $this->getStartLocation(),
            'endLocation' => $this->getEndLocation(),
            'startDate' => '2021-03-16T20:00:00+00:00',
            'endDate' => '2021-03-16T20:00:00+00:00',
            'seatNumber' => $this->getSeatNumber(),
            'meansType' => $this->getMeansType(),
            'meansNumber' => $this->getMeansNumber(),
            'meansStartPoint' => $this->getMeansStartPoint(),
            'meansEndPoint' => $this->getMeansEndPoint(),
            'baggageInfo' => $this->getBaggageInfo(),
            //"trip" => $this->getTrip(),
        ];

        return $arrayCard;
    }
}
