<?php

namespace App\Repository;

use App\Entity\Piecedetachee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Piecedetachee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Piecedetachee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Piecedetachee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PiecedetacheeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Piecedetachee::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('created_at' => 'DESC'));
    }

    // /**
    //  * @return Piecedetachee[] Returns an array of Piecedetachee objects
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
    public function findOneBySomeField($value): ?Piecedetachee
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
