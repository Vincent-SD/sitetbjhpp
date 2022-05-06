<?php

namespace App\Repository;

use App\Entity\BoutadeCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BoutadeCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoutadeCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoutadeCategory[]    findAll()
 * @method BoutadeCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoutadeCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoutadeCategory::class);
    }

    // /**
    //  * @return BoutadeCategory[] Returns an array of BoutadeCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BoutadeCategory
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
