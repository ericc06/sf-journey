<?php

namespace App\DataFixtures;

use App\Entity\Journey;
use App\Entity\Trip;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JourneyFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $rides = [
            [
                'startLocation' => 'Beauvais',
                'endLocation' => 'Strasbourg',
                'startDate' => new \DateTime('now - 3 day'),
                'endDate' => new \DateTime('now - 3 day + 1 hour'),
                'seatNumber' => 'wagon 7, seat 24',
                'meansType' => 'train',
                'meansNumber' => 'TGV AF123',
            ],
            [
                'startLocation' => 'Nice',
                'endLocation' => 'Paris',
                'startDate' => new \DateTime('now - 5 day'),
                'endDate' => new \DateTime('now - 5 day + 1 hour'),
                'seatNumber' => 'G6',
                'meansType' => 'flight',
                'meansNumber' => 'AF123',
                'baggageInfo' => 'Baggage drop at ticket counter 344',
            ],
            [
                'startLocation' => 'Paris',
                'endLocation' => 'Beauvais',
                'startDate' => new \DateTime('now - 4 day'),
                'endDate' => new \DateTime('now - 4 day + 1 hour'),
                'meansType' => 'bus',
                'meansNumber' => 'FL444',
            ],
            [
                'startLocation' => 'Beauvais',
                'endLocation' => 'Strasbourg',
                'startDate' => new \DateTime('now - 1 month - 3 day'),
                'endDate' => new \DateTime('now - 1 month - 3 day + 1 hour'),
                'seatNumber' => 'wagon 7, seat 24',
                'meansType' => 'train',
                'meansNumber' => 'TGV AF123',
            ],
            [
                'startLocation' => 'Nice',
                'endLocation' => 'Paris',
                'startDate' => new \DateTime('now - 1 month - 5 day'),
                'endDate' => new \DateTime('now - 1 month - 5 day + 1 hour'),
                'seatNumber' => 'G6',
                'meansType' => 'flight',
                'meansNumber' => 'AF123',
                'baggageInfo' => 'Baggage drop at ticket counter 344',
            ],
            [
                'startLocation' => 'Paris',
                'endLocation' => 'Beauvais',
                'startDate' => new \DateTime('now - 1 month - 4 day'),
                'endDate' => new \DateTime('now - 1 month - 4 day + 1 hour'),
                'meansType' => 'bus',
                'meansNumber' => 'FL444',
            ],
            [
                'startLocation' => 'Beauvais',
                'endLocation' => 'Strasbourg',
                'startDate' => new \DateTime('now - 2 month - 3 day'),
                'endDate' => new \DateTime('now - 2 month - 3 day + 1 hour'),
                'seatNumber' => 'wagon 7, seat 24',
                'meansType' => 'train',
                'meansNumber' => 'TGV AF123',
            ],
            [
                'startLocation' => 'Nice',
                'endLocation' => 'Paris',
                'startDate' => new \DateTime('now - 2 month - 5 day'),
                'endDate' => new \DateTime('now - 2 month - 5 day + 1 hour'),
                'seatNumber' => 'G6',
                'meansType' => 'flight',
                'meansNumber' => 'AF123',
                'baggageInfo' => 'Baggage drop at ticket counter 344',
            ],
            [
                'startLocation' => 'Paris',
                'endLocation' => 'Beauvais',
                'startDate' => new \DateTime('now - 2 month - 4 day'),
                'endDate' => new \DateTime('now - 2 month - 4 day + 1 hour'),
                'meansType' => 'bus',
                'meansNumber' => 'FL444',
            ],
        ];

        $trip = new Trip();

        foreach ($rides as $ride) {
            $rideType = $ride['meansType'];
            $rideClass = 'App\\Entity\\'.ucfirst($rideType).'Ride';
            $rideObj = new $rideClass();
            foreach ($ride as $key => $value) {
                $methodName = 'set'.ucfirst($key);
                $rideObj->$methodName($value);
            }
            $trip->addRide($rideObj);
        }

        $journey = new Journey();
        $journey->addTrip($trip);

        $manager->persist($journey);
        $manager->flush();
    }
}
