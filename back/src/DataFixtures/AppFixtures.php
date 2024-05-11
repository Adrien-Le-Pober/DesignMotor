<?php

namespace App\DataFixtures;

use App\Entity\Type;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\Model;
use App\Entity\Motorization;
use App\Entity\Vehicle;
use App\Service\AssetsService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function __construct(private AssetsService $assetsService)
    { }

    public function load(ObjectManager $manager): void
    {
        $assetsPath = $this->assetsService;

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
                    $color = (new Color())->setName('red');
                    $manager->persist($color);
                    $type = (new Type())->setName('Car');
                    $manager->persist($type);
                    $motorization = (new Motorization())->setName('Electric');
                    $manager->persist($motorization);
                    $vehicle
                        ->setSpace(mt_rand(1, 10))
                        ->addColor($color)
                        ->setMotorization($motorization)
                        ->setType($type)
                        ->setImagePath($assetsPath->getImagePath() . 'renault_megan_E-Tech.jpg')
                        ->setVideoPath($assetsPath->getVideoPath() . 'Renault-Megane-eTech.mp4');
                    break;
                case 'Classe S':
                    $color = (new Color())->setName('grey');
                    $manager->persist($color);
                    $type = (new Type())->setName('Car');
                    $manager->persist($type);
                    $motorization = (new Motorization())->setName('Petrol');
                    $manager->persist($motorization);
                    $vehicle
                        ->setSpace(mt_rand(1, 10))
                        ->addColor($color)
                        ->setMotorization($motorization)
                        ->setType($type)
                        ->setImagePath($assetsPath->getImagePath() . 'mercedes_classe_s.jpg')
                        ->setVideoPath($assetsPath->getVideoPath() . 'Mercedes-Classe-S.mp4');
                    break;
                case 'Polo':
                    $color = (new Color())->setName('blue');
                    $manager->persist($color);
                    $type = (new Type())->setName('Car');
                    $manager->persist($type);
                    $motorization = (new Motorization())->setName('Petrol');
                    $manager->persist($motorization);
                    $vehicle
                        ->setSpace(mt_rand(1, 10))
                        ->addColor($color)
                        ->setMotorization($motorization)
                        ->setType($type)
                        ->setImagePath($assetsPath->getImagePath() . 'volkswagen_polo.jpg')
                        ->setVideoPath($assetsPath->getVideoPath() . 'Volkswagen-Polo.mp4');
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
                        ->setType($type)
                        ->setImagePath($assetsPath->getImagePath() . 'Peugeot_Django_classic_50.jpg')
                        ->setVideoPath($assetsPath->getVideoPath() . 'Peugeot-Django-50.mp4');
                    break;
                default:
                    echo 'error';
            }

            $manager->persist($vehicle);
        }

        $manager->flush();
    }
}
