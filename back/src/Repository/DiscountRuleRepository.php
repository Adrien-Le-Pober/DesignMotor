<?php

namespace App\Repository;

use App\Entity\DiscountRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DiscountRule>
 */
class DiscountRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscountRule::class);
    }

    public function findInfos(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.id', 'd.name', 'd.description')
            ->getQuery()
            ->getResult();
    }
}
