<?php

namespace App\Tests\Service;

use App\Entity\BusRide;
use App\Entity\FlightRide;
use App\Entity\TrainRide;
use App\Entity\Trip;
use App\Entity\Journey;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JourneyManagerTest extends WebTestCase
{
    private $journeyManager;

    protected function setup(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = self::$container;
        $this->journeyManager = $container->get('App\Service\JourneyManager');
    }

    public function testGetBuiltJourney()
    {
        $ridesArray = $this->getRidesArray();

        $journey = $this->journeyManager->getBuiltJourney($ridesArray);

        $this->assertInstanceOf(Journey::class, $journey);
        $this->assertObjectHasAttribute('trips', $journey);
        $this->assertObjectHasAttribute('rides', $journey->getTrips()->first());
    }

    public function testGetSerializedJourney()
    {
        $journey = $this->getTestJourney();

        $serializedJourney = $this->journeyManager->getSerializedJourney($journey);

        $this->assertStringContainsString('trips', $serializedJourney);
        $this->assertStringContainsString('wagon 7, seat 24', $serializedJourney);
    }

    public function testGetTextualJourney()
    {
        $journey = $this->getTestJourney();

        $text = $this->journeyManager->getTextualJourney($journey);

        $this->assertStringContainsString('Your journey counts 1 trip.', $text);
        $this->assertStringContainsString('Trip n°1 counts 4 travels:', $text);
        $this->assertStringContainsString('You have arrived at your final destination.', $text);
    }

    public function testAddStartRideToTrip()
    {
        $trip = $this->getTestTrip();
        $this->assertEquals(4, $trip->getRides()->count());

        $ridesArray = $this->getRidesArray();
        $this->journeyManager->setThisRidesArray($ridesArray);

        $startRide = $this->journeyManager->addStartRideToTrip($trip);

        $this->assertStringContainsString('Nice', $startRide->getStartLocation());
        $this->assertEquals(5, $trip->getRides()->count());
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
        $trip = $this->getTestTrip();

        $journey = new Journey();
        $journey->addTrip($trip);

        return $journey;
    }

    public function getTestTrip(): Trip
    {
        $ridesArray = $this->getRidesArray();

        $trip = new Trip();
        $trip->addRide($ridesArray[0]);
        $trip->addRide($ridesArray[1]);
        $trip->addRide($ridesArray[2]);
        $trip->addRide($ridesArray[3]);

        return $trip;
    }

    public function getRidesArray(): array
    {
        $ride1 = new TrainRide();
        $ride1->setStartLocation('Paris');
        $ride1->setEndLocation('Strasbourg');
        $ride1->setStartDate(new \DateTime());
        $ride1->setEndDate(new \DateTime());
        $ride1->setSeatNumber('wagon 7, seat 24');
        $ride1->setMeansType('train');
        $ride1->setMeansNumber('TGV 425');

        $ride2 = new FlightRide();
        $ride2->setStartLocation('Nice');
        $ride2->setEndLocation('Paris');
        $ride2->setStartDate(new \DateTime('now - 1 day'));
        $ride2->setEndDate(new \DateTime('now - 1 day + 2 hour'));
        $ride2->setSeatNumber('4F');
        $ride2->setMeansType('flight');
        $ride2->setMeansNumber('AF123');

        $ride3 = new FlightRide();
        $ride3->setStartLocation('Strasbourg');
        $ride3->setEndLocation('Nantes');
        $ride3->setStartDate(new \DateTime('now + 1 day'));
        $ride3->setEndDate(new \DateTime('now + 1 day + 2 hour'));
        $ride3->setSeatNumber('8C');
        $ride3->setMeansType('flight');
        $ride3->setMeansNumber('EJ987');

        $ride4 = new BusRide();
        $ride4->setStartLocation('Nantes');
        $ride4->setEndLocation('Rennes');
        $ride4->setStartDate(new \DateTime('now + 1 day + 1 hour'));
        $ride4->setEndDate(new \DateTime('now + 1 day + 2 hour'));
        $ride4->setMeansType('bus');
        $ride4->setMeansNumber('FL32');
        
        $ridesArray = [];
        $ridesArray[] = $ride1;
        $ridesArray[] = $ride2;
        $ridesArray[] = $ride3;
        $ridesArray[] = $ride4;

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
