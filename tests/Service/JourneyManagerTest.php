<?php

namespace App\Tests\Service;

use App\Service\JourneyManager;
use App\Entity\Ride;
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
        $ridesArray = $this->getRidesArray();

        $journey = $this->journeyManager->buildJourney($ridesArray);

        $this->assertInstanceOf(Journey::class, $journey);
        $this->assertObjectHasAttribute('trips', $journey);
        $this->assertObjectHasAttribute('rides', $journey->getTrips()->first());
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

    public function testAddStartRideToTrip()
    {
        $trip = $this->getInitialTrip();
        $this->assertEquals(3, $trip->getRides()->count());

        $ridesArray = $this->getRidesArray();
        $this->journeyManager->setThisRidesArray($ridesArray);

        $startRide = $this->journeyManager->addStartRideToTrip($trip);

        $this->assertStringContainsString('Nice', $startRide->getStartLocation());
        $this->assertEquals(4, $trip->getRides()->count());
    }

    /*public function testGetRidesArrayFromJson()
    {
        $this->initProperties();

        $trip = $this->getInitialTrip();

        $jsonContent = $this->getInitialJson();

        $ridesArray = $this->journeyManager->getRidesArrayFromJson($jsonContent);

        $this->assertEquals(2, count($ridesArray));
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
        $ridesArray = $this->getRidesArray();

        $trip = new Trip();
        $trip->addRide($ridesArray[0]);
        $trip->addRide($ridesArray[1]);
        $trip->addRide($ridesArray[2]);

        return $trip;
    }

    public function getRidesArray(): array
    {
        $ride1 = new Ride();
        $ride1->setStartLocation('Paris');
        $ride1->setEndLocation('Strasbourg');
        $ride1->setStartDate(new \DateTime());
        $ride1->setEndDate(new \DateTime());
        $ride1->setSeatNumber('wagon 7, seat P64');
        $ride1->setMeansType('TGV');
        $ride1->setMeansNumber('425');

        $ride2 = new Ride();
        $ride2->setStartLocation('Nice');
        $ride2->setEndLocation('Paris');
        $ride2->setStartDate(new \DateTime('now -1 day'));
        $ride2->setEndDate(new \DateTime('now -1 day'));
        $ride2->setSeatNumber('4F');
        $ride2->setMeansType('flight');
        $ride2->setMeansNumber('AF123');

        $ride3 = new Ride();
        $ride3->setStartLocation('Strasbourg');
        $ride3->setEndLocation('Nantes');
        $ride3->setStartDate(new \DateTime('now +1 day'));
        $ride3->setEndDate(new \DateTime('now +1 day'));
        $ride3->setSeatNumber('8C');
        $ride3->setMeansType('flight');
        $ride3->setMeansNumber('EJ987');

        $ridesArray = [];
        $ridesArray[] = $ride1;
        $ridesArray[] = $ride2;
        $ridesArray[] = $ride3;

        return $ridesArray;
    }
    
    /*
    public function getInitialJson(): string
    {
        $ridesArray = $this->getRidesArray();
        
        $input = [
            "rides" => [
                $ridesArray[0]->toArray(),
                $ridesArray[1]->toArray(),
                $ridesArray[2]->toArray()
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
