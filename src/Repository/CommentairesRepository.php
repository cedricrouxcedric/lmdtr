<?php

namespace App\Repository;

use App\Entity\Articles;
use App\Entity\User;
use App\Entity\Commentaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commentaires|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaires|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaires[]    findAll()
 * @method Commentaires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaires::class);
    }

    public function findComAndDataByUser(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->addSelect('a')
            ->addSelect('t')
            ->leftJoin('c.articles','a')
            ->leftJoin('a.themes','t')
            ->where('c.auteur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
        return $queryBuilder;
    }

//    }
//    /**
//
//      * @return Commentaires[] Returns an array of Commentaires objects
//      */
//    *
//    public function findByExampleField($value)
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }


    /*
    public function findOneBySomeField($value): ?Commentaires
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
