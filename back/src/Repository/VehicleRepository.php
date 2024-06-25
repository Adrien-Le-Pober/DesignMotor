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
            'v.imagePath',
            'v.price',
            'v.soldedPrice',
            'b.name AS brandName',
            'mo.name AS modelName',
            'm.name AS motorizationName',
            't.name AS typeName'
        ];

        $selectFields = array_unique(array_merge($defaultSelectFields, $specialFields));

        $qb->select($selectFields)
            ->leftJoin('v.brand', 'b')
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
        return $this->findVehicles('Electrique', 'Voiture', $filters, []);
    }

    public function findPetrolCars(array $filters): array
    {
        return $this->findVehicles('Essence', 'Voiture', $filters, []);
    }

    public function findElectricScooters(array $filters): array
    {
        return $this->findVehicles('Electrique', 'Scooter', $filters, []);
    }

    public function findPetrolScooters(array $filters): array
    {
        return $this->findVehicles('Essence', 'Scooter', $filters, []);
    }

    public function findDetailsById(int $id): array
    {
        return $this->createQueryBuilder('v')
            ->select(
                'v.id',
                'v.price',
                'v.soldedPrice',
                'v.power',
                'v.space',
                'v.imagePath AS image',
                'v.description',
                'b.name AS brand',
                'b.description AS brandDescription',
                'mo.name AS model',
                'mo.description AS modelDescription',
                'm.name AS motorization',
                'c.name AS color'
            )
            ->andWhere('v.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('v.brand', 'b')
            ->leftJoin('v.model', 'mo')
            ->leftJoin('v.motorization', 'm')
            ->leftJoin('v.color', 'c')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByModelOrBrand(string $query, int $limit = 5)
    {
        return $this->createQueryBuilder('v')
            ->select('v.id', 'b.name AS brand', 'mo.name AS model')
            ->leftJoin('v.brand', 'b')
            ->leftJoin('v.model', 'mo')
            ->where('mo.name LIKE :query')
            ->orWhere('b.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
