<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function findByStartLocation($location)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.startLocation LIKE :loc')
            ->setParameter('loc', $location)
            ->orderBy('c.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByStartDateAfterGivenDate($date)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate > :date')
            ->setParameter('date', $date)
            ->orderBy('c.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findImmediateNextCardStartingAfterNow()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('c.startDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllLongerThanGivenHours(int $hours, bool $includeExpiredCards = true): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM card c
            WHERE TIMESTAMPDIFF(MINUTE, c.start_date, c.end_date) > :minutes
            ';

        if (!$includeExpiredCards) {
            $sql .= 'AND c.start_date > CURRENT_TIME()';
        }            

        $sql .= 'ORDER BY c.start_date ASC';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['minutes' => $hours * 60]);

        return $stmt->fetchAllAssociative();
    }
}
