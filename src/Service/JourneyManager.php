<?php

namespace App\Service;

use App\Entity\Journey;
use App\Entity\Ride;
use App\Entity\Trip;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class JourneyManager
{
    private $serializer;
    private $ridesArray;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $discriminator = new ClassDiscriminatorFromClassMetadata($classMetadataFactory);

        $this->serializer = new Serializer(
            [
                new DateTimeNormalizer(),
                new ObjectNormalizer($classMetadataFactory, null, null, null, $discriminator),
                new GetSetMethodNormalizer(),
            ],
            ['json' => new JsonEncoder()]
        );
        $this->ridesArray = [];
        $this->em = $em;
    }

    public function getRidesArrayFromJson($jsonContent): array
    {
        $ridesArray = [];
        $decodedJson = json_decode($jsonContent);

        foreach ($decodedJson as $obj) {
            $ride = $this->getRideInstance($obj);

            $ridesArray[] = $ride;
        }

        return $ridesArray;
    }

    public function getRideInstance($obj): Ride
    {
        $type = ucfirst($obj->meansType);

        $rideClassName = 'App\\Entity\\'.$type.'Ride';

        $ride = new $rideClassName();

        $objReflection = new \ReflectionObject($obj);
        $objProperties = $objReflection->getProperties();

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($objProperties as $property) {
            $propertyName = $property->getName();

            // Is the current value a date?
            $value = ($date = strtotime($obj->$propertyName))
                ? \DateTime::createFromFormat('U', $date)
                : $obj->$propertyName;

            $propertyAccessor->setValue($ride, $propertyName, $value);
        }

        return $ride;
    }

    public function getBuiltJourney(array $ridesArray): Journey
    {
        $this->ridesArray = $ridesArray;

        $tmpJourney = new Journey();

        while (count($this->ridesArray) > 0) {
            $trip = new Trip();

            $startRide = $this->addStartRideToTrip($trip);

            // The starting date of the trips will be used to sort them
            // chronologocally if there are many.
            $trip->setTripStartDate($startRide->getStartDate());

            $this->addNextRidesToTrip($trip, $startRide);

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

    public function addStartRideToTrip(&$trip): Ride
    {
        $rides = $this->ridesArray;
        $startRide = null;

        foreach ($rides as $ride) {
            $startLoc = $ride->getStartLocation();
            $startDate = $ride->getStartDate();
            // Boolean used to know the exit condition of the following 'foreach' loop.
            $isStartRide = true;
            // Looking for a ride with an end location equal to the current ride starting location
            // AND with an end date older that the current ride start date.
            // If we find one, the current ride is not the starting cart, so we break the loop
            // and move to the next cart.
            foreach ($rides as $endRide) {
                if (
                    $ride !== $endRide
                    && $startLoc === $endRide->getEndLocation()
                    && $startDate > $endRide->getEndDate()
                ) {
                    $isStartRide = false;
                    break;
                }
            }

            // We found the (or one of the) starting ride. We break the main loop.
            if ($isStartRide) {
                $startRide = $ride;
                break;
            }
        }

        $trip->addRide($startRide);
        $this->unsetValue($this->ridesArray, $startRide);

        return $startRide;
    }

    public function unsetValue(&$array, $value, $strict = true): void
    {
        if (($key = array_search($value, $array, $strict)) !== false) {
            unset($array[$key]);
        }
    }

    public function addNextRidesToTrip(&$trip, $ride): void
    {
        if ($nextRide = $this->getNextRide($ride)) {
            $trip->addRide($nextRide);

            $this->unsetValue($this->ridesArray, $nextRide);
            $this->addNextRidesToTrip($trip, $nextRide);
        }
    }

    // Provided a ride (the startRide), looking for the next ride of the trip, with:
    // - start location of the next ride = end location of the startCart
    // - start date of the next ride > end date of the startCart
    // If there are many, we choose the first one (chronologically).
    public function getNextRide($startRide): ?Ride
    {
        $rides = $this->ridesArray;
        $endLoc = $startRide->getEndLocation();
        $endDate = $startRide->getEndDate();

        $nextRide = null;

        foreach ($rides as $endRide) {
            if (
                $startRide !== $endRide
                && $endLoc === $endRide->getStartLocation()
                && $endDate < $endRide->getStartDate()
            ) {
                if (!$nextRide) {
                    $nextRide = $endRide;
                } else {
                    if ($endRide->getStartDate() < $nextRide->getStartDate()) {
                        $nextRide = $endRide;
                    }
                }
                break;
            }
        }

        return $nextRide;
    }

    public function getSerializedJourney($journey): string
    {
        $jsonContent = $this->serializer->serialize(
            $journey,
            'json',
            ['groups' => ['ride', 'trip', 'journey']]
        );

        return $jsonContent;
    }

    public function getTextualJourney(Journey $journey): string
    {
        $trips = $journey->getTrips();
        $nbTrips = $trips->count();

        $text = 'Your journey counts '.$nbTrips.' trip'.($nbTrips > 1 ? 's.' : '.')."\n\n\n";

        foreach ($trips->getIterator() as $i => $trip) {
            $rides = $trip->getRides();
            $nbRides = $rides->count();

            $text .= 'Trip n°'.(int) ($i + 1).' counts ';
            $text .= $nbRides.' travel'.($nbRides > 1 ? 's:' : ':')."\n\n";

            foreach ($rides->getIterator() as $j => $ride) {
                //$text .= "Description of travel n°" . (int)($j + 1) . ":\n";
                $text .= '- On '.date_format($ride->getStartDate(), 'Y-m-d H:i:s');
                $text .= ' take '.$ride->getMeansType();
                $text .= $ride->getMeansNumber() ? ' '.$ride->getMeansNumber() : '';
                $text .= ' from '.$ride->getStartLocation();
                $text .= $ride->getMeansStartPoint() ? ' ('.$ride->getMeansStartPoint().')' : '';
                $text .= ' to '.$ride->getEndLocation();
                $text .= $ride->getMeansEndPoint() ? ' ('.$ride->getMeansEndPoint().').' : '.';
                if (method_exists($ride, 'getSeatNumber')) {
                    $text .= $ride->getSeatNumber() ? ' Sit in '.$ride->getSeatNumber().'.' : ' No seat assignment.';
                }
                if (null !== $ride->getEndDate()) {
                    $text .= ' Arrival planned on '.date_format($ride->getEndDate(), 'Y-m-d H:i:s');
                    $text .= $ride->getMeansEndPoint() ? ' at '.$ride->getMeansEndPoint().'.' : '.';
                }
                if (method_exists($ride, 'getBaggageInfo')) {
                    $text .= $ride->getBaggageInfo() ? ' '.$ride->getBaggageInfo().'.' : '';
                }
                $text .= "\n\n";
            }

            $text .= "You have arrived at your final destination.\n\n\n";
        }

        return $text;
    }

    public function persistJourney(Journey $journey): Journey
    {
        $this->em->persist($journey);
        $this->em->flush();

        return $journey;
    }

    // Useful to unit test some of the above methods
    public function setThisRidesArray(array $ridesArray): void
    {
        $this->ridesArray = $ridesArray;
    }
}
