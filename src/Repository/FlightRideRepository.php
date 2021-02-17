<?php

namespace App\Repository;

use App\Entity\FlightRide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FlightRide|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlightRide|null findOneBy(array $criteria, array $orderBy = null)
 * @method FlightRide[]    findAll()
 * @method FlightRide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightRideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlightRide::class);
    }

    public function findByStartDateAfterGivenDate($date)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.startDate > :date')
            ->setParameter('date', $date)
            ->orderBy('f.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findImmediateNextRideStartingAfterNow()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.startDate > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('f.startDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllLongerThanGivenHours(int $hours, bool $includeExpiredCards = true): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT *, TIMESTAMPDIFF(MINUTE, f.start_date, f.end_date) / 60 as duration FROM flight_ride f
            HAVING duration > :nbHours
            ';

        if (!$includeExpiredCards) {
            $sql .= 'AND f.start_date > CURRENT_TIME()';
        }

        $sql .= 'ORDER BY f.start_date ASC';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['nbHours' => $hours]);

        return $stmt->fetchAllAssociative();
    }
}
