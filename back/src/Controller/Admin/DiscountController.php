<?php

namespace App\Controller\Admin;

use App\Entity\Discount;
use App\Repository\VehicleRepository;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DiscountController extends AbstractController
{
    #[Route('/discounts', name: 'app_discount')]
    public function discountIndex(DiscountRepository $discountRepository)
    {
        $serializer = new Serializer([new ObjectNormalizer()]);
        $discounts = $discountRepository->findAll();
        $data = [];

        foreach($discounts as $discount) {
            $data[] = $serializer->normalize(
                $discount,
                null,
                [AbstractNormalizer::ATTRIBUTES => ['id', 'storageDuration', 'rate']]
            );
        }
        

        return $this->json($data);
    }

    #[Route('/new-discount', name: 'app_new_discount')]
    public function newDiscount(
        VehicleRepository $vehicleRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $data = json_decode($request->getContent(), true);

        if (isset($data['storageDuration']) && isset($data['rate'])) {
            $discount = (new Discount())
                ->setStorageDuration($data['storageDuration'])
                ->setRate($data['rate']);

            $discount->run($vehicleRepository->findAll());

            $entityManager->persist($discount);
            $entityManager->flush();

            return $this->json("La nouvelle réduction vient d'être appliquée");
        } else {
            return $this->json(['error' => 'Les données entrées sont invalides'], 400);
        }

    }

    #[Route('/cancel-discount/{discount}', name: 'app_cancel_discount')]
    public function cancelDiscount(Discount $discount, EntityManagerInterface $entityManager)
    {
        $discount->cancel();
        
        $entityManager->remove($discount);
        $entityManager->flush();

        return $this->json('La réduction à bien été retirée');
    }
}
