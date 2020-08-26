<?php

namespace App\Repository;

use App\Entity\Moto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Moto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Moto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Moto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Moto::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('created_at' => 'DESC'));
    }

    public function findAllStillOnSale() {
        return $this->findAllStillOnSaleQuerry()
            ->getQuery()
            ->getResult();
    }

    public function findAllAlreadySale() {
        return $this->createQueryBuilder('m')
            ->where('m.sold = true')
            ->getQuery()
            ->getResult();
    }

    private function findAllStillOnSaleQuerry(): QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->where('m.sold = false');
    }

    public function getFavoritesByUser(UserInterface $user)
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->leftJoin('m.marque','mm')
            ->addSelect('mm')
            ->innerJoin('m.likes','ml')
            ->where('ml.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
        return $queryBuilder;
    }

    public function getForSaleByUser(UserInterface $user)
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->addSelect('mm')
            ->leftJoin('m.marque','mm')
            ->where('m.vendeur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
        return $queryBuilder;
    }
    // /**
    //  * @return Moto[] Returns an array of Moto objects
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
    public function findOneBySomeField($value): ?Moto
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
