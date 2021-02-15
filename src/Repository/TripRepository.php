<?php

namespace App\Repository;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function findByTripStartDateAfterGivenDate($date)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.tripStartDate > :date')
            ->setParameter('date', $date)
            ->orderBy('t.tripStartDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findImmediateNextTripStartingAfterNow()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.tripStartDate > CURRENT_DATE()')
            ->orderBy('t.tripStartDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllHavingAtLeastGivenNumberOfRides(int $nbRides): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT t.id, COUNT(c.id) as count FROM trip t 
            LEFT JOIN ride as r ON r.trip_id = t.id
            GROUP BY t.id
            HAVING count >= :nb
            ORDER BY count DESC
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['nb' => $nbRides]);

        return $stmt->fetchAllAssociative();
    }
}
