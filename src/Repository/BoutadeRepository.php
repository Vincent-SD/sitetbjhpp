<?php

namespace App\Repository;

use App\Entity\Boutade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Boutade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Boutade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Boutade[]    findAll()
 * @method Boutade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoutadeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boutade::class);
    }

    public function countBoutades():int{
        return $this->createQueryBuilder('boutade')
            ->select('count(boutade.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Boutade[] Returns an array of Boutade objects
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
    public function findOneBySomeField($value): ?Boutade
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
