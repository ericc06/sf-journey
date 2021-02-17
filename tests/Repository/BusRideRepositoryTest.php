<?php

namespace App\Tests\Service;

use App\Entity\BusRide;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BusRideRepositoryTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindByStartDateAfterGivenDate()
    {
        $date = new \DateTime('now - 1 month - 10 day');

        $busRides = $this->entityManager
            ->getRepository(BusRide::class)
            ->findByStartDateAfterGivenDate($date)
        ;

        $this->assertSame(2, sizeof($busRides));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
