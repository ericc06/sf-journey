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

    // /**
    //  * @return TrainRide[] Returns an array of TrainRide objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrainRide
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
