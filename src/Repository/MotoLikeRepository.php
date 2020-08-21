<?php

namespace App\Repository;

use App\Entity\MotoLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MotoLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method MotoLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method MotoLike[]    findAll()
 * @method MotoLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotoLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MotoLike::class);
    }

    // /**
    //  * @return MotoLike[] Returns an array of MotoLike objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MotoLike
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
