<?php

namespace App\Repository;

use App\Entity\Articles;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('created_at' => 'DESC'));
    }

    public function createdByOrderByDate(User $user)
    {
        $query = $this->createQueryBuilder('a')
            ->addSelect('at')
            ->addSelect('ac')
            ->leftJoin('a.commentaires','ac')
            ->leftJoin('a.themes','at')
            ->where('a.auteur = :user')
            ->setParameter('user', $user)
            ->orderBy('a.created_at','desc')
            ->getQuery()
            ->execute();
        return $query;
    }

    // /**
    //  * @return Articles[] Returns an array of Articles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Articles
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
