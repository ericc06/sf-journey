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
        $qb = $this->createQueryBuilder('c')
            ->where('TIMEDIFF(c.endDate, c.startDate) > :hours')
            ->setParameter('hours', $hours)
            ->orderBy('c.startDate', 'ASC');

        if (!$includeExpiredCards) {
            $qb->andWhere('c.startDate >= :now')
                ->setParameter('now', new \DateTime());
        }

        $query = $qb->getQuery();

        return $query->execute();
    }
}
