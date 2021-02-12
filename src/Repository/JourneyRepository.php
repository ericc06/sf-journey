<?php

namespace App\Repository;

use App\Entity\Journey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Journey|null find($id, $lockMode = null, $lockVersion = null)
 * @method Journey|null findOneBy(array $criteria, array $orderBy = null)
 * @method Journey[]    findAll()
 * @method Journey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JourneyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Journey::class);
    }

    public function findAllHavingMoreThanGivenNumberOfTrips(int $nbTrips): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT j.id, COUNT(t.id) as count FROM journey j 
            LEFT JOIN trip as t ON t.journey_id = j.id
            GROUP BY j.id
            HAVING count >= :nb
            ORDER BY count DESC
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['nb' => $nbTrips]);

        return $stmt->fetchAllAssociative();
    }
}
