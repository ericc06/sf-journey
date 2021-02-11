<?php

namespace App\Tests\Service;

use App\Service\JourneyManager;
use App\Entity\Card;
use App\Entity\Trip;
use App\Entity\Journey;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class JourneyManagerTest extends TestCase
{
    private $serializer;
    private $journeyManager;

    protected function setup(): void
    {
        parent::setUp();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
        $this->journeyManager = new JourneyManager($this->serializer);
    }

    public function testBuildJourney()
    {
        $cardsArray = $this->getCardsArray();

        $journey = $this->journeyManager->buildJourney($cardsArray);

        $this->assertInstanceOf(Journey::class, $journey);
        $this->assertObjectHasAttribute('trips', $journey);
        $this->assertObjectHasAttribute('cards', $journey->getTrips()->first());
    }

    public function testGetSerializedJourney()
    {
        $journey = $this->getTestJourney();

        $serializedJourney = $this->journeyManager->getSerializedJourney($journey);

        $this->assertStringContainsString('trips', $serializedJourney);
        $this->assertStringContainsString('wagon 7, seat P64', $serializedJourney);
    }
    
    public function testGetTextualJourney()
    {
        $journey = $this->getTestJourney();

        $text = $this->journeyManager->getTextualJourney($journey);

        $this->assertStringContainsString('Your journey counts 1 trip.', $text);
        $this->assertStringContainsString('Trip n°1 counts 3 travels.', $text);
        $this->assertStringContainsString('You have arrived at your final destination.', $text);
    }

    public function testAddStartCardToTrip()
    {
        $trip = $this->getInitialTrip();
        $this->assertEquals(3, $trip->getCards()->count());

        $cardsArray = $this->getCardsArray();
        $this->journeyManager->setThisCardsArray($cardsArray);

        $startCard = $this->journeyManager->addStartCardToTrip($trip);

        $this->assertStringContainsString('Nice', $startCard->getStartLocation());
        $this->assertEquals(4, $trip->getCards()->count());
    }

    /*public function testGetCardsArrayFromJson()
    {
        $this->initProperties();

        $trip = $this->getInitialTrip();

        $jsonContent = $this->getInitialJson();

        $cardsArray = $this->journeyManager->getCardsArrayFromJson($jsonContent);

        $this->assertEquals(2, count($cardsArray));
    }*/

    public function getTestJourney(): Journey
    {
        $trip = $this->getInitialTrip();

        $journey = new Journey();
        $journey->addTrip($trip);

        return $journey;
    }

    public function getInitialTrip(): Trip
    {
        $cardsArray = $this->getCardsArray();

        $trip = new Trip();
        $trip->addCard($cardsArray[0]);
        $trip->addCard($cardsArray[1]);
        $trip->addCard($cardsArray[2]);

        return $trip;
    }

    public function getCardsArray(): array
    {
        $card1 = new Card();
        $card1->setStartLocation('Paris');
        $card1->setEndLocation('Strasbourg');
        $card1->setStartDate(new \DateTime());
        $card1->setEndDate(new \DateTime());
        $card1->setSeatNumber('wagon 7, seat P64');
        $card1->setMeansType('TGV');
        $card1->setMeansNumber('425');

        $card2 = new Card();
        $card2->setStartLocation('Nice');
        $card2->setEndLocation('Paris');
        $card2->setStartDate(new \DateTime('now -1 day'));
        $card2->setEndDate(new \DateTime('now -1 day'));
        $card2->setSeatNumber('4F');
        $card2->setMeansType('flight');
        $card2->setMeansNumber('AF123');

        $card3 = new Card();
        $card3->setStartLocation('Strasbourg');
        $card3->setEndLocation('Nantes');
        $card3->setStartDate(new \DateTime('now +1 day'));
        $card3->setEndDate(new \DateTime('now +1 day'));
        $card3->setSeatNumber('8C');
        $card3->setMeansType('flight');
        $card3->setMeansNumber('EJ987');

        $cardsArray = [];
        $cardsArray[] = $card1;
        $cardsArray[] = $card2;
        $cardsArray[] = $card3;

        return $cardsArray;
    }
    
    /*
    public function getInitialJson(): string
    {
        $cardsArray = $this->getCardsArray();
        
        $input = [
            "cards" => [
                $cardsArray[0]->toArray(),
                $cardsArray[1]->toArray(),
                $cardsArray[2]->toArray()
            ]
        ];

        $json = json_encode($input);

        return $json;
    }
    */

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->journeyManager = null;
    }
}
