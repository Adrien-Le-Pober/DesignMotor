<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicle>
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    //    /**
    //     * @return Vehicle[] Returns an array of Vehicle objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findElectricCars(): array
    {
        return $this->createQueryBuilder('v')
            ->select(
                'v.id',
                'v.power',
                'v.space',
                'b.name AS brandName',
                'c.name AS colorName',
                'mo.name AS modelName',
                'm.name AS motorizationName',
                't.name AS typeName'
            )
            ->leftJoin('v.brand', 'b')
            ->leftJoin('v.color', 'c')
            ->leftJoin('v.model', 'mo')
            ->leftJoin('v.motorization', 'm')
            ->andWhere('m.name = :motorization')
            ->leftJoin('v.type', 't')
            ->andWhere('t.name = :type')
            ->setParameter('motorization', 'Electric')
            ->setParameter('type', 'Car')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPetrolCars(): array
    {
        return $this->createQueryBuilder('v')
            ->select(
                'v.id',
                'v.power',
                'v.space',
                'b.name AS brandName',
                'c.name AS colorName',
                'mo.name AS modelName',
                'm.name AS motorizationName',
                't.name AS typeName'
            )
            ->leftJoin('v.brand', 'b')
            ->leftJoin('v.color', 'c')
            ->leftJoin('v.model', 'mo')
            ->leftJoin('v.motorization', 'm')
            ->andWhere('m.name = :motorization')
            ->leftJoin('v.type', 't')
            ->andWhere('t.name = :type')
            ->setParameter('motorization', 'Petrol')
            ->setParameter('type', 'Car')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findElectricScooters(): array
    {
        return $this->createQueryBuilder('v')
            ->select(
                'v.id',
                'v.power',
                'b.name AS brandName',
                'c.name AS colorName',
                'mo.name AS modelName',
                'm.name AS motorizationName',
                't.name AS typeName'
            )
            ->leftJoin('v.brand', 'b')
            ->leftJoin('v.color', 'c')
            ->leftJoin('v.model', 'mo')
            ->leftJoin('v.motorization', 'm')
            ->andWhere('m.name = :motorization')
            ->leftJoin('v.type', 't')
            ->andWhere('t.name = :type')
            ->setParameter('motorization', 'Electric')
            ->setParameter('type', 'Scooter')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPetrolScooters(): array
    {
        return $this->createQueryBuilder('v')
            ->select(
                'v.id',
                'v.power',
                'b.name AS brandName',
                'c.name AS colorName',
                'mo.name AS modelName',
                'm.name AS motorizationName',
                't.name AS typeName'
            )
            ->leftJoin('v.brand', 'b')
            ->leftJoin('v.color', 'c')
            ->leftJoin('v.model', 'mo')
            ->leftJoin('v.motorization', 'm')
            ->andWhere('m.name = :motorization')
            ->leftJoin('v.type', 't')
            ->andWhere('t.name = :type')
            ->setParameter('motorization', 'Petrol')
            ->setParameter('type', 'Scooter')
            ->getQuery()
            ->getResult()
        ;
    }

    //    public function findOneBySomeField($value): ?Vehicle
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
