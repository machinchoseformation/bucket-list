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

    public function findListWishes(
        ?int $page = 1,
        ?string $keyword = null,
        ?int $categoryId = null,
        ?string $sort = null
    )
    {
        //notre query builder
        $qb = $this->createQueryBuilder('w');

        //recherche ?
        if ($keyword){
            //ajoute une clause where
            $qb->andWhere("w.label LIKE :kw");
            $qb->setParameter('kw', '%' . $keyword . '%');
        }

        //filtre par catégorie ?
        if ($categoryId){
            //ajoute le where
            $qb->andWhere("w.category = :cat");
            $qb->setParameter("cat", $categoryId);
        }

        //on compte d'abord le nombre total de résultat avec ces where !!
        $qb->select('COUNT(w) AS tot');
        $total = $qb->getQuery()->getSingleScalarResult();

        //on change le select opéré...
        //on veut maintenant récupérer les wish et non le compte
        $qb->select('w');

        //tri
        switch ($sort){
            case "date-asc":
                $qb->orderBy('w.dateCreated', 'ASC');
                break;
            case "date-desc":
                $qb->orderBy('w.dateCreated', 'DESC');
                break;
            case "note-asc":
                $qb->orderBy('w.averageRating', 'ASC');
                break;
            case "note-desc":
                $qb->orderBy('w.averageRating', 'DESC');
                break;
        }

        //pagination
        $numPerPage = 20; //nombre de wish par page
        $offset = ($page - 1) * $numPerPage; //le # du premier wish à récupérer
        $qb->setMaxResults($numPerPage);
        $qb->setFirstResult($offset);

        $query = $qb->getQuery();
        $wishes = $query->getResult();

        return [
            "wishes" => $wishes,
            "total" => $total,
            "lastPage" => ceil($total/$numPerPage),
        ];
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
