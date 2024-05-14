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

    private function findVehicles(
        string $motorization,
        string $type,
        array $filters,
        array $specialFields
    ): array {
        $qb = $this->createQueryBuilder('v');

        $defaultSelectFields = [
            'v.id',
            'v.power',
            'v.imagePath',
            'v.price',
            'b.name AS brandName',
            'c.name AS colorName',
            'mo.name AS modelName',
            'm.name AS motorizationName',
            't.name AS typeName'
        ];

        $selectFields = array_unique(array_merge($defaultSelectFields, $specialFields));

        $qb->select($selectFields)
            ->leftJoin('v.brand', 'b')
            ->leftJoin('v.color', 'c')
            ->leftJoin('v.model', 'mo')
            ->leftJoin('v.motorization', 'm')
            ->andWhere('m.name = :motorization')
            ->leftJoin('v.type', 't')
            ->andWhere('t.name = :type')
            ->setParameter('motorization', $motorization)
            ->setParameter('type', $type);

        foreach ($filters as $key => $value) {
            if ($key === 'brand') {
                $qb->andWhere('b.name = :brand')
                    ->setParameter('brand', $value);
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function findElectricCars(array $filters): array
    {
        return $this->findVehicles('Electric', 'Car', $filters, ['v.space']);
    }

    public function findPetrolCars(array $filters): array
    {
        return $this->findVehicles('Petrol', 'Car', $filters, ['v.space']);
    }

    public function findElectricScooters(array $filters): array
    {
        return $this->findVehicles('Electric', 'Scooter', $filters, []);
    }

    public function findPetrolScooters(array $filters): array
    {
        return $this->findVehicles('Petrol', 'Scooter', $filters, []);
    }
}
