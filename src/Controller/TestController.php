<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// final class TestController extends AbstractController{
//     #[Route('/test', name: 'test', methods:['GET'])]
//     public function test(): Response
//     {
//         return $this->render('test/index.html.twig', [
//             'controller_name' => 'TestController',
//         ]);
//     }
// }
final class TestController extends AbstractController
{
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): Response
    {
        return new Response("<h1>HEY</h1>");
    }

    #[Route('/test2/{id}', name: 'test2', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function test2(int $id): Response
    {
        return new Response("<h1>{$id}</h1>");
    }

    #[Route('/test3/{id}', name: 'test3', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function test3(int $id = 0): Response
    {
        return new Response("<h1>{$id}</h1>");
    }
    #[Route('/test3/3', name: 'test32', methods: ['GET'], priority: 1)]
    public function test32(): Response
    {
        return new Response("<h1> only 3</h1>");
    }

    #[Route('/test4/{id}', name: 'test4', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function test4(int $id = 0): Response
    {
        return $this->render('test/test4.html.twig', ['id' => $id]);
    }

    #[Route('/test5', name: 'test5', methods: ['GET'])]
    public function test5(): Response
    {
        return $this->render('test/test5.html.twig');
    }

    #[Route('/test6', name: 'test6', methods: ['GET'])]
    public function test6(): Response
    {
        return $this->render('test/test6.html.twig');
    }

    #[Route('/test7', name: 'test7', methods: ['GET'])]
    public function test7(EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $produit->setNom('product1');
        $produit->setReference('pro1');
        $produit->setTypeConditionnement('Sachet');
        $produit->setQuantite(50);
        $produit->setEmplacement('H123');
        $produit->setPrix(12);
        $produit->setQuota(10);
        $produit->setStock(100);
        $produit->setEstActif(true);
        $produit->setDateCreation(new DateTime());
        $produit->setDateMaj(new DateTime());

        $entityManager->persist($produit);
        $entityManager->flush();

        return $this->render('test/test7.html.twig', ['produit' => $produit]);
    }

    #[Route('/test72', name: 'test72', methods: ['GET'])]
    public function test72(EntityManagerInterface $entityManager): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find(1);
        $produit->setPrix($produit->getPrix() * 2);
        $produit->setDateMaj(new DateTime());

        $entityManager->persist($produit);
        $entityManager->flush();

        return $this->render('test/test7.html.twig', ['produit' => $produit]);
    }

    #[Route('/test8', name: 'test8', methods: ['GET'])]
    public function test8(EntityManagerInterface $entityManager): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find(19);
        $entityManager->remove($produit);
        $entityManager->flush();

        return new Response("<h1>Produit 1 supprimé !</h1>");
    }

// route lister les produits
    #[Route('/test9', name: 'test9', methods: ['GET'])]
    public function test9(EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager->getRepository(Produit::class)->findBy([], ['nom' => 'ASC']);

        return $this->render('test/test9.html.twig', ['produits' => $produits]);
    }

    // Route pour afficher le formulaire des produits.
#[Route('/test10', name: 'test10', methods: ['GET'])]
public function test10(): Response
{
    $produit = new Produit();
    $form = $this->createForm(ProduitType::class, $produit);
    
    return $this->render('test/test10.html.twig', ['form' => $form]);
}
// Route pour afficher le formulaire detailles des produits.
#[Route('/test102', name: 'test102', methods: ['GET'])]
public function test102(): Response
{
    $produit = new Produit();
    $form = $this->createForm(ProduitType::class, $produit);
    
    return $this->render('test/test102.html.twig', ['form' => $form]);
}

}
