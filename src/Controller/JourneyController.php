<?php

namespace App\Controller;

//require_once __DIR__.'/../vendor/autoload.php';

use App\Entity\Card;
use App\Entity\Journey;
use App\Entity\Trip;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class JourneyController extends AbstractController
{
    private $serializer;
    private $journey;
    private $cardsArray;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->journey = new Journey();
        $this->cardsArray = [];
    }

    /**
     * @Route("/journey", name="journey", methods={"GET", "POST"})
     */
    public function index(Request $request): Response
    {
        $this->setCardsArrayFromJson($request->getContent());

        $this->buildJourney();

        $jsonContent = $this->getSerializedJourney();

        /*$response = new Response();
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);*/

        $response = JsonResponse::fromJsonString($jsonContent);

        return $response;
    }

    /**
     * @Route("/journey2", name="journey2", methods={"GET", "POST"})
     */
    public function index2(Request $request): Response
    {
        //var_dump($trip); exit;
        $card0 = new Card();
        $card0->setStartLocation('Beauvais');
        $card0->setEndLocation('Strasbourg');
        $card0->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '18-Feb-2021 20:00:00'));
        $card0->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '18-Feb-2021 20:00:00'));
        $card0->setSeatNumber('W7-P64');
        $card0->setMeansType('TGV');
        $card0->setMeansNumber('AF123');

        $card1 = new Card();
        $card1->setStartLocation('Nice');
        $card1->setEndLocation('Paris');
        $card1->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Feb-2021 20:00:00'));
        $card1->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Feb-2021 20:00:00'));
        $card1->setSeatNumber('G6');
        $card1->setMeansType('plane');
        $card1->setMeansNumber('AF123');

        $card2 = new Card();
        $card2->setStartLocation('Paris');
        $card2->setEndLocation('Beauvais');
        $card2->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '17-Feb-2021 20:00:00'));
        $card2->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '17-Feb-2021 20:00:00'));
        $card2->setMeansType('bus');
        $card2->setMeansNumber('FL444');

        $card3 = new Card();
        $card3->setStartLocation('Rome');
        $card3->setEndLocation('Tunis');
        $card3->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Feb-2021 20:00:00'));
        $card3->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Feb-2021 20:00:00'));
        $card3->setSeatNumber('H3');
        $card3->setMeansType('plane');
        $card3->setMeansNumber('IT555');

        $card4 = new Card();
        $card4->setStartLocation('Milan');
        $card4->setEndLocation('Rome');
        $card4->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '15-Feb-2021 20:00:00'));
        $card4->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '15-Feb-2021 20:00:00'));
        $card4->setMeansType('bus');
        $card4->setMeansNumber('BU789');
        
        $card5 = new Card();
        $card5->setStartLocation('Nice');
        $card5->setEndLocation('Paris');
        $card5->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Mar-2021 20:00:00'));
        $card5->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '16-Mar-2021 20:00:00'));
        $card5->setSeatNumber('G6');
        $card5->setMeansType('plane');
        $card5->setMeansNumber('AF123');

        $card6 = new Card();
        $card6->setStartLocation('Paris');
        $card6->setEndLocation('Beauvais');
        $card6->setStartDate(\DateTime::createFromFormat('j-M-Y H:i:s', '17-Mar-2021 20:00:00'));
        $card6->setEndDate(\DateTime::createFromFormat('j-M-Y H:i:s', '17-Mar-2021 20:00:00'));
        $card6->setMeansType('bus');
        $card6->setMeansNumber('FL444');

        /*$trip = new Trip();
        $trip->addCard($card0);
        $trip->addCard($card1);
        $trip->addCard($card2);*/

        $this->cardsArray[] = $card0;
        $this->cardsArray[] = $card4;
        $this->cardsArray[] = $card2;
        $this->cardsArray[] = $card3;
        $this->cardsArray[] = $card1;
        $this->cardsArray[] = $card5;
        $this->cardsArray[] = $card6;

        // Storing given cards into $this->cardsStorage
        //$this->getCardsFromJson($request->getContent());

        /*$givenCards = $this->serializer->deserialize(
            $request->getContent(),
            Trip::class,
            'json'
        );*/
        //$this->cardsArray = $givenCards->getCards()->toArray();

        //var_dump($this->cardsArray); exit;

        $this->buildJourney();
        //$this->journey->addTrip($trip);

        $jsonContent = $this->serializer->serialize(
            //$givenCards->getCards(),
            $this->journey,
            'json',
            ['groups' => ['card', 'trip', 'journey']]
        );

        $response = new Response();
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    public function setCardsArrayFromJson($jsonContent): void
    {
        $givenCardsObject = $this->serializer->deserialize(
            $jsonContent,
            Trip::class,
            'json'
        );

        $givenCards = $givenCardsObject->getCards();

        foreach ($givenCards as $card) {
            $this->cardsArray[] = $card;
        }
    }

    public function buildJourney(): void
    {
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

            $this->journey = $tmp_journey->getTrips()->matching($criteria);
        } else {
            $this->journey = $tmp_journey;
        }
    }

    public function getSerializedJourney(): string
    {
        $jsonContent = $this->serializer->serialize(
            $this->journey,
            'json',
            ['groups' => ['card', 'trip', 'journey']]
        );

        return $jsonContent;
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
