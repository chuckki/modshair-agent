<?php

namespace App\Repository;

use App\Entity\EndCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EndCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method EndCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method EndCustomer[]    findAll()
 * @method EndCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EndCustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EndCustomer::class);
    }

    // /**
    //  * @return EndCustomer[] Returns an array of EndCustomer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EndCustomer
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return EndCustomer[]
     */
    public function findAllMatching(string $query, int $limit = 5)
    {
        return $this->createQueryBuilder('ec')
            ->andWhere('ec.customernumber LIKE :query')
            ->orWhere(('ec.firma LIKE :query'))
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
