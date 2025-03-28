<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController{
    #[Route('/produit/inactive/{id}', name: 'produit_inactive', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function inactive(EntityManagerInterface $entityManager, int $id): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        $produit->setEstActif(false);
        $entityManager->persist($produit);
        $entityManager->flush();

        return $this->redirectToRoute('test9');
    }
}
