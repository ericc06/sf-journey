<?php

namespace App\Repository;

use App\Entity\BusRide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BusRide|null find($id, $lockMode = null, $lockVersion = null)
 * @method BusRide|null findOneBy(array $criteria, array $orderBy = null)
 * @method BusRide[]    findAll()
 * @method BusRide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusRideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BusRide::class);
    }

    public function findByStartDateAfterGivenDate($date)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.startDate > :date')
            ->setParameter('date', $date)
            ->orderBy('b.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findImmediateNextRideStartingAfterNow()
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.startDate > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('b.startDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllLongerThanGivenHours(int $hours, bool $includeExpiredCards = true): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM bus_ride b
            WHERE TIMESTAMPDIFF(MINUTE, b.start_date, b.end_date) > :minutes
            ';

        if (!$includeExpiredCards) {
            $sql .= 'AND b.start_date > CURRENT_TIME()';
        }

        $sql .= 'ORDER BY b.start_date ASC';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['minutes' => $hours * 60]);

        return $stmt->fetchAllAssociative();
    }
}
