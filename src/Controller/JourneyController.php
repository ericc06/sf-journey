<?php

namespace App\Controller;

//require_once __DIR__.'/../vendor/autoload.php';

use App\Entity\Card;
use App\Entity\Journey;
use App\Entity\Trip;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
        // Storing given cards into $this->cardsStorage
        $this->getCardsFromJson($request->getContent());

        /*$givenCards = $this->serializer->deserialize(
            $request->getContent(),
            Trip::class,
            'json'
        );*/
        //$this->cardsArray = $givenCards->getCards()->toArray();

        //var_dump($this->cardsArray); exit;

        $this->buildJourney();

        $jsonContent = $this->serializer->serialize(
            //$givenCards->getCards(),
            $this->journey,
            'json',
            ['groups' => ['card', 'trip', 'journey']]
        );

        $response = new Response();
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(209);
        //$response->setStatusCode(Response::HTTP_OK);

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

        $trip = new Trip();
        $trip->addCard($card0);
        $trip->addCard($card1);
        $trip->addCard($card2);

        $this->cardsArray[] = $card0;
        $this->cardsArray[] = $card1;
        $this->cardsArray[] = $card2;

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
        $this->journey->addTrip($trip);

        $jsonContent = $this->serializer->serialize(
            //$givenCards->getCards(),
            $this->journey,
            'json',
            ['groups' => ['card', 'trip', 'journey']]
        );

        $response = new Response();
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(209);
        //$response->setStatusCode(Response::HTTP_OK);

        return $response;
    }


    public function getCardsFromJson($jsonContent): void
    {
        $givenCardsObject = $this->serializer->deserialize(
            $jsonContent,
            Trip::class,
            'json'
        );

        $givenCards = $givenCardsObject->getCards();
        //var_dump($givenCards); exit;

        foreach ($givenCards as $card) {
            $this->cardsArray[] = $card;
        }
    }

    public function buildJourney(): void
    {

        $trip = new Trip();

        $startCard = $this->findStartCard($trip);

        $this->findNextCards($trip, $startCard);

        $this->journey->addTrip($trip);


        /*while ($this->cardsStorage->count() > 0) {
            $trip = new Trip();

            $startCard = $this->findStartCard($trip);
            //$this->transferCard($trip, $startCard);

            //$this->findNextCards($trip, $startCard);
            
        }*/
    }

    public function findStartCard(&$trip): Card
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
                /*print_r("startLoc : " . $startLoc . "<br>");
                print_r("endCard->getEndLocation : " . $endCard->getEndLocation() . "<br>");
                print_r("startDate : " . $startDate->format('Y-m-d H:i:s') . "<br>");
                print_r("endCard->getEndDate : " . $endCard->getEndDate()->format('Y-m-d H:i:s') . "<br><br>");*/
                if (
                    $card !== $endCard
                    && $startLoc === $endCard->getEndLocation()
                    && $startDate > $endCard->getEndDate()
                ) {
                    $isItStartCard = false;
                    break;
                }
            }

            // We found the (or one of the) staring card. We break the loop.
            if ($isItStartCard) {
                $startCard = $card;
                break;
            }
        }

        $trip->addCard($startCard);
        $this->unsetValue($this->cardsArray, $startCard);

        return $startCard;
    }

    function unsetValue(&$array, $value, $strict = TRUE)
    {
        if (($key = array_search($value, $array, $strict)) !== FALSE) {
            unset($array[$key]);
        }
    }

    // Transferring a card from the given cards list to the trip being build
    /*public function transferCard(&$trip, $card): void
    {
        $trip->addCard($card);
        unset($this->cardsArray[$card]);
    }*/

    public function findNextCards(&$trip, $card): void
    {
        print_r("START CARD  : " . $card->getStartLocation() . "<br>\n");
        if ($nextCard = $this->getNextCard($card)) {
            $trip->addCard($nextCard);
            $this->unsetValue($this->cardsArray, $nextCard);
            $this->findNextCards($trip, $nextCard);
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
            /*print_r("startLoc : " . $startLoc . "<br>");
                print_r("endCard->getEndLocation : " . $endCard->getEndLocation() . "<br>");
                print_r("startDate : " . $startDate->format('Y-m-d H:i:s') . "<br>");
                print_r("endCard->getEndDate : " . $endCard->getEndDate()->format('Y-m-d H:i:s') . "<br><br>");*/
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
        if ($nextCard !== null) {
            print_r("found next : " . $nextCard->getStartLocation() . "<br>\n");
        }
        return $nextCard;
    }
}
