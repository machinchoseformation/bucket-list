<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserWish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserWish|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWish|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWish[]    findAll()
 * @method UserWish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserWishRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserWish::class);
    }

    public function findUserList(User $user)
    {
        $qb = $this->createQueryBuilder('uw');
        $qb->select('uw')
            ->addSelect('w')
            ->join('uw.wish', 'w')
            ->andWhere('uw.user = :user')
            ->setParameter('user', $user)
            ->addOrderBy('uw.done', 'ASC')
            ->addOrderBy('uw.dateAdded', 'DESC')
            ;

        $query = $qb->getQuery();
        return $query->getResult();
    }

    // /**
    //  * @return UserWish[] Returns an array of UserWish objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserWish
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
