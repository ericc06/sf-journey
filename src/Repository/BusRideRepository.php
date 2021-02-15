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

    // /**
    //  * @return BusRide[] Returns an array of BusRide objects
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
    public function findOneBySomeField($value): ?BusRide
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
