<?php

namespace App\Repository;

use App\Entity\Wish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Wish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wish[]    findAll()
 * @method Wish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Wish::class);
    }

    public function findListWishes()
    {
        //version en DQL
        /*
        $dql = "SELECT w 
                FROM App\Entity\Wish w 
                WHERE w.dateCreated >= 2018
                ORDER BY w.label DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        */

        //version en QueryBuilder
        $qb = $this->createQueryBuilder('w');
        $qb->select('w')
            ->andWhere('w.dateCreated >= 2018')
            ->addOrderBy('w.label', 'DESC');

        $keyword = "Ipsum";
        if ($keyword){
            $qb->andWhere("w.label LIKE :kw");
            $qb->setParameter('kw', '%' . $keyword . '%');
        }

        $query = $qb->getQuery();

        $wishes = $query->getResult();
        return $wishes;
    }

    // /**
    //  * @return Wish[] Returns an array of Wish objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Wish
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
