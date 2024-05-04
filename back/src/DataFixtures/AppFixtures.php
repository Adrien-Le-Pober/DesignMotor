<?php

namespace App\DataFixtures;

use App\Entity\Type;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\Model;
use App\Entity\Motorization;
use App\Entity\Vehicle;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brandsData = [
            'Renault' => 'Megane E-Tech',
            'Mercedes' => 'Classe S',
            'Volkswagen' => 'Polo',
            'Peugeot' => 'DJANGO CLASSIC 50'
        ];

        foreach ($brandsData as $brandName => $modelName) {
            $brand = (new Brand())
                ->setName($brandName);
            $manager->persist($brand);

            $model = (new Model())
                ->setName($modelName)
                ->setBrand($brand);
            $manager->persist($model);

            $vehicle = (new Vehicle())
                ->setPower(mt_rand(1, 100))
                ->setBrand($brand)
                ->setModel($model);

            switch($modelName) {
                case 'Megane E-Tech':
                    $color = (new Color())->setName('blue');
                    $manager->persist($color);
                    $type = (new Type())->setName('Car');
                    $manager->persist($type);
                    $motorization = (new Motorization())->setName('Electric');
                    $manager->persist($motorization);
                    $vehicle
                        ->setSpace(mt_rand(1, 10))
                        ->addColor($color)
                        ->setMotorization($motorization)
                        ->setType($type);
                    break;
                case 'Classe S':
                    $color = (new Color())->setName('black');
                    $manager->persist($color);
                    $type = (new Type())->setName('Car');
                    $manager->persist($type);
                    $motorization = (new Motorization())->setName('Petrol');
                    $manager->persist($motorization);
                    $vehicle
                        ->setSpace(mt_rand(1, 10))
                        ->addColor($color)
                        ->setMotorization($motorization)
                        ->setType($type);
                    break;
                case 'Polo':
                    $color = (new Color())->setName('yellow');
                    $manager->persist($color);
                    $type = (new Type())->setName('Car');
                    $manager->persist($type);
                    $motorization = (new Motorization())->setName('Petrol');
                    $manager->persist($motorization);
                    $vehicle
                        ->setSpace(mt_rand(1, 10))
                        ->addColor($color)
                        ->setMotorization($motorization)
                        ->setType($type);
                    break;
                case 'DJANGO CLASSIC 50':
                    $color = (new Color())->setName('red');
                    $manager->persist($color);
                    $type = (new Type())->setName('Scooter');
                    $manager->persist($type);
                    $motorization = (new Motorization())->setName('Petrol');
                    $manager->persist($motorization);
                    $vehicle
                        ->addColor($color)
                        ->setMotorization($motorization)
                        ->setType($type);
                    break;
                default:
                    echo 'error';
            }

            $manager->persist($vehicle);
        }

        $manager->flush();
    }
}
