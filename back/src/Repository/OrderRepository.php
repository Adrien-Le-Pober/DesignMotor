<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }


    public function findByUser($user): array
    {
        return $this->createQueryBuilder('o')
        ->select('o.id', 'o.createdAt', 'o.products')
        ->andWhere('o.user = :user')
        ->setParameter('user', $user)
        ->orderBy('o.createdAt', 'ASC')
        ->getQuery()
        ->getResult()
    ;
    }

    //    public function findOneBySomeField($value): ?Order
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
