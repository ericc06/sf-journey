<?php

namespace App\Service;

use App\Entity\Card;
use App\Entity\Journey;
use App\Entity\Trip;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class JourneyManager
{
    private $cardsArray;
    private $serializer;

    public function __construct($cardsArray = [], SerializerInterface $serializer)
    {
        $this->cardsArray = $cardsArray;
        $this->serializer = $serializer;
    }

    public function getCardsArrayFromJson($jsonContent): array
    {
        $cardsArray = [];

        $givenCardsObject = $this->serializer->deserialize(
            $jsonContent,
            Trip::class,
            'json'
        );

        $givenCards = $givenCardsObject->getCards();

        foreach ($givenCards as $card) {
            $cardsArray[] = $card;
        }
//var_dump($cardsArray); exit;
        return $cardsArray;
    }

    public function buildJourney(Array $cardsArray): Journey
    {
        $this->cardsArray = $cardsArray;

        $tmp_journey = new Journey;

        while (count($this->cardsArray) > 0) {
            $trip = new Trip();

            $startCard = $this->addStartCardToTrip($trip);

            // The starting date of the trips will be used to sort them
            // chronologocally if there are many. 
            $trip->setTripStartDate($startCard->getStartDate());

            $this->addNextCardsToTrip($trip, $startCard);

            $tmp_journey->addTrip($trip);
            //$this->journey->addTrip($trip);

            print_r('TRIP TERMINE. Cartes restantes : ' . count($this->cardsArray) . "<br>\n");
        }

        // If the journey contains many trips, we order them by starting date.
        if ($tmp_journey->getTrips()->count() > 1) {
            // ordering the trips collection by tripStartDate property
            $criteria = new Criteria();
            $criteria->orderBy(['tripStartDate' => Criteria::ASC]);

            //return $tmp_journey->getTrips()->matching($criteria);
        }
        
        return $tmp_journey;
    }

    public function getSerializedJourney($journey): string
    {
        $jsonContent = $this->serializer->serialize(
            $journey,
            'json',
            ['groups' => ['card', 'trip', 'journey']]
        );

        return $jsonContent;
    }

    public function getTextualJourney(Journey $journey): string
    {
        $trips = $journey->getTrips();

        //$nbTrips = $trip->count();

        $text = "Your journey counts " . $trips->count() . " trips.\n\n";

        foreach($trips->getIterator() as $i => $trip) {
            $cards = $trip->getCards();

            $text .= "Trip n°" . (int)($i + 1) . " counts " . $cards->count() . " travels:\n\n";

            foreach($cards->getIterator() as $j => $card) {
                //$text .= "Description of travel n°" . (int)($j + 1) . ":\n";
            
                $text .= "- On " . date_format($card->getStartDate(), 'Y-m-d H:i:s');
                $text .= " take " . $card->getMeansType() . " " . $card->getMeansNumber();
                $text .= " from " . $card->getStartLocation();
                $text .= $card->getMeansStartPoint() ? " (exact location: " . $card->getMeansStartPoint() . ") " : "";
                $text .= " to " . $card->getEndLocation() . ".";
                $text .= $card->getMeansEndPoint() ? " (exact location: " . $card->getMeansEndPoint() . ")" : "";
                $text .= $card->getSeatNumber() ? " Sit in " . $card->getSeatNumber() . "." : " No seat assignment.";
                $text .= " Arrival planned on " . date_format($card->getEndDate(), 'Y-m-d H:i:s') . " at " . $card->getMeansEndPoint() . "\n\n\n";
                $text .= $card->getBaggageInfo() ? " " . $card->getBaggageInfo() . "." : "";
            }

        }

        return $text;
    }

    public function addStartCardToTrip(&$trip): Card
    {
        $cards = $this->cardsArray;
        $startCard = null;

        foreach ($cards as $card) {
            $startLoc = $card->getStartLocation();
            $startDate = $card->getStartDate();
            // Boolean used to know the exit condition of the following 'foreach' loop.
            $isItStartCard = true;
            // Looking for a card with an end location equal to the current card starting location
            // AND with an end date older that the current card start date.
            // If we find one, the current card is not the starting cart, so we break the loop
            // and move to the next cart.
            foreach ($cards as $endCard) {
                if (
                    $card !== $endCard
                    && $startLoc === $endCard->getEndLocation()
                    && $startDate > $endCard->getEndDate()
                ) {
                    $isItStartCard = false;
                    break;
                }
            }

            // We found the (or one of the) starting card. We break the main loop.
            if ($isItStartCard) {
                $startCard = $card;
                break;
            }
        }

        $trip->addCard($startCard);
        $this->unsetValue($this->cardsArray, $startCard);

        return $startCard;
    }

    public function unsetValue(&$array, $value, $strict = true)
    {
        if (($key = array_search($value, $array, $strict)) !== false) {
            unset($array[$key]);
        }
    }

    public function addNextCardsToTrip(&$trip, $card): void
    {
        print_r('START CARD  : ' . $card->getStartLocation() . "<br>\n");
        if ($nextCard = $this->getNextCard($card)) {
            $trip->addCard($nextCard);
            $this->unsetValue($this->cardsArray, $nextCard);
            $this->addNextCardsToTrip($trip, $nextCard);
        }

        //return false;
    }

    // Provided a card (the startCard), looking for the next card of the trip, with:
    // - start location of the next card = end location of the startCart
    // - start date of the next card > end date of the startCart
    // If there are many, we chose the first one (chronologically).
    public function getNextCard($startCard): ?Card
    {
        $cards = $this->cardsArray;
        $endLoc = $startCard->getEndLocation();
        $endDate = $startCard->getEndDate();

        $nextCard = null;

        foreach ($cards as $endCard) {
            if (
                $startCard !== $endCard
                && $endLoc === $endCard->getStartLocation()
                && $endDate < $endCard->getStartDate()
            ) {
                if (!$nextCard) {
                    $nextCard = $endCard;
                } else {
                    if ($endCard->getStartDate() < $nextCard->getStartDate()) {
                        $nextCard = $endCard;
                    }
                }
                break;
            }
        }
        if (null !== $nextCard) {
            print_r('found next : ' . $nextCard->getStartLocation() . "<br>\n");
        }

        return $nextCard;
    }
}
