<?php

namespace App\Service;

use App\Entity\Card;
use App\Entity\Journey;
use App\Entity\Trip;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Serializer\SerializerInterface;

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

        return $cardsArray;
    }

    public function buildJourney(array $cardsArray): Journey
    {
        $this->cardsArray = $cardsArray;

        $tmpJourney = new Journey();

        while (count($this->cardsArray) > 0) {
            $trip = new Trip();

            $startCard = $this->addStartCardToTrip($trip);

            // The starting date of the trips will be used to sort them
            // chronologocally if there are many.
            $trip->setTripStartDate($startCard->getStartDate());

            $this->addNextCardsToTrip($trip, $startCard);

            $tmpJourney->addTrip($trip);
        }

        // If the journey contains many trips, we order them by starting date.
        if ($tmpJourney->getTrips()->count() > 1) {
            $criteria = new Criteria();
            $criteria->orderBy(['tripStartDate' => Criteria::ASC]);

            $reindexedJourney = $tmpJourney->getTrips()->matching($criteria)->getValues();

            $resultJourney = new Journey();
            foreach ($reindexedJourney as $trip) {
                $resultJourney->addTrip($trip);
            }

            return $resultJourney;
        }

        return $tmpJourney;
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
        $nbTrips = $trips->count();

        $text = 'Your journey counts '.$nbTrips.' trip'.($nbTrips > 1 ? 's.' : '.')."\n\n";

        foreach ($trips->getIterator() as $i => $trip) {
            $cards = $trip->getCards();
            $nbCards = $cards->count();

            $text .= 'Trip n°'.(int) ($i + 1).' counts ';
            $text .= $nbCards.' travel'.($nbCards > 1 ? 's.' : '.')."\n\n";

            foreach ($cards->getIterator() as $j => $card) {
                //$text .= "Description of travel n°" . (int)($j + 1) . ":\n";

                $text .= '- On '.date_format($card->getStartDate(), 'Y-m-d H:i:s');
                $text .= ' take '.$card->getMeansType();
                $text .= $card->getMeansNumber() ? ' '.$card->getMeansNumber() : '';
                $text .= ' from '.$card->getStartLocation();
                $text .= $card->getMeansStartPoint() ? ' ('.$card->getMeansStartPoint().')' : '';
                $text .= ' to '.$card->getEndLocation();
                $text .= $card->getMeansEndPoint() ? ' (exact location: '.$card->getMeansEndPoint().').' : '.';
                $text .= $card->getSeatNumber() ? ' Sit in '.$card->getSeatNumber().'.' : ' No seat assignment.';
                $text .= ' Arrival planned on '.date_format($card->getEndDate(), 'Y-m-d H:i:s');
                $text .= $card->getMeansEndPoint() ? ' at '.$card->getMeansEndPoint().'.' : '.';
                $text .= $card->getBaggageInfo() ? ' '.$card->getBaggageInfo().".\n\n" : "\n\n";
            }

            $text .= "You have arrived at your final destination.\n";
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
        if ($nextCard = $this->getNextCard($card)) {
            $trip->addCard($nextCard);
            $this->unsetValue($this->cardsArray, $nextCard);
            $this->addNextCardsToTrip($trip, $nextCard);
        }
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

        return $nextCard;
    }
}
