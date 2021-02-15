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

    // /**
    //  * @return FlightRide[] Returns an array of FlightRide objects
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
    public function findOneBySomeField($value): ?FlightRide
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
