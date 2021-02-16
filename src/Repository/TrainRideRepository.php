<?php

namespace App\Repository;

use App\Entity\TrainRide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainRide|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainRide|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainRide[]    findAll()
 * @method TrainRide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainRideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainRide::class);
    }

    public function findByStartDateAfterGivenDate($date)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.startDate > :date')
            ->setParameter('date', $date)
            ->orderBy('t.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findImmediateNextRideStartingAfterNow()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.startDate > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('t.startDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllLongerThanGivenHours(int $hours, bool $includeExpiredCards = true): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM train_ride t
            WHERE TIMESTAMPDIFF(MINUTE, t.start_date, t.end_date) > :minutes
            ';

        if (!$includeExpiredCards) {
            $sql .= 'AND t.start_date > CURRENT_TIME()';
        }            

        $sql .= 'ORDER BY t.start_date ASC';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['minutes' => $hours * 60]);

        return $stmt->fetchAllAssociative();
    }
}
