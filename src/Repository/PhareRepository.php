<?php

namespace App\Repository;

use App\Entity\Phare;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Phare|null find($id, $lockMode = null, $lockVersion = null)
 * @method Phare|null findOneBy(array $criteria, array $orderBy = null)
 * @method Phare[]    findAll()
 * @method Phare[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Phare::class);
    }

    // /**
    //  * @return Phare[] Returns an array of Phare objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Phare
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
