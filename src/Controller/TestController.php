<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Fournisseur;
use App\Entity\Produit;
use App\Enum\EtatCommande;
use App\Form\ProduitType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{

    // Route sans paramètre.
    #[Route('/test1', name: 'test1', methods: ['GET'])]
    public function test1(): Response
    {
        return new Response("<h1>Hello !</h1>");
    }

    // Route avec paramètre obligatoire.
    #[Route('/test2/{id}', name: 'test2', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function test2(int $id): Response
    {
        return new Response("<h1>{$id}</h1>");
    }

    // Route avec paramètre facultatif.
    #[Route('/test3/{id}', name: 'test3', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function test3(int $id = 0): Response
    {
        return new Response("<h1>{$id}</h1>");
    }

    // Route avec priorité.
    #[Route('/test4/{id}', name: 'test4', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function test4(int $id = 0): Response
    {
        return $this->render('test/test4.html.twig', ['id' => $id]);
    }

    // Route avec vue comportant un asset.
    #[Route('/test5', name: 'test5', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function test5(): Response
    {
        return $this->render('test/test5.html.twig');
    }

    // Route avec vue comportant un lien.
    #[Route('/test6', name: 'test6', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function test6(): Response
    {
        return $this->render('test/test6.html.twig');
    }

    // Route pour créer un produit.
    #[Route('/test7', name: 'test7', methods: ['GET'])]
    public function test7(EntityManagerInterface $entity_manager): Response
    {
        $produit = new Produit();
        $produit->setNom('Machin');
        $produit->setReference('MCH');
        $produit->setTypeConditionnement('Sachet');
        $produit->setQuantite(50);
        $produit->setEmplacement('H123');
        $produit->setPrix(12);
        $produit->setQuota(10);
        $produit->setStock(80);
        $produit->setEstActif(true);
        $produit->setDateCreation(new DateTime());
        $produit->setDateMaj(new DateTime());
        $entity_manager->persist($produit);
        $entity_manager->flush();
        return $this->render('test/test7.html.twig', ['produit' => $produit]);
    }

    // Route qui multiplie par deux le prix du produit 1
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

    // Route qui supprime un produit
    #[Route('/test8/{id}', name: 'test8', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function test8(EntityManagerInterface $entityManager, int $id): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        $entityManager->remove($produit);
        $entityManager->flush();
        return $this->redirectToRoute('test9');
    }

    // Route qui va lister tous les produits
    #[Route('/test9', name: 'test9', methods: ['GET'])]
    public function test9(EntityManagerInterface $em): Response
    {
        $produits = $em->getRepository(Produit::class)->findBy([], ['nom' => 'ASC']);
    
        $commande = $em->getRepository(Commande::class)->findOneBy([
            'etat' => EtatCommande::EN_COURS,
            'acheteur' => $this->getUser(),
        ]);
    
        return $this->render('test/test9.html.twig', [
            'produits' => $produits,
            'commande' => $commande,
        ]);
    }
        
    

    // Route pour afficher le formulaire global des produits
    #[Route('/test10/{id}', name: 'test10', methods: ['GET', 'POST'], requirements: ['id' => '[1-9][0-9]*'])]
    public function test10(Request $request, EntityManagerInterface $entityManager, int $id = 0): Response
    {
        $produit = $id ? $entityManager->getRepository(Produit::class)->find($id) : new Produit();
        $categories = $entityManager->getRepository(Categorie::class)->findBy([], ['nom' => 'ASC']);
        $fournisseurs = $entityManager->getRepository(Fournisseur::class)->findBy([], ['nom' => 'ASC']);
        $form = $this->createForm(ProduitType::class, $produit, ['categories' => $categories,'fournisseurs' => $fournisseurs]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $produit->setDateCreation(new DateTime());
            $produit->setDateMaj(new DateTime());
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute('test9');
        }
        return $this->render('test/test10.html.twig', ['form' => $form]);
    }

        // Route qui va sotcker une variable en session
        #[Route('/test11', name: 'test11', methods: ['GET'])]
        public function test11(Request $request): Response
        {
            $session = $request->getSession();
            $session->set('pi', 3.14);
            return new Response("<h1>Session créée !</h1><h3>PI : {$session->get('pi', 0)}</h3>");
        }

        // Route qui génère un pdf
        #[Route('/test12', name: 'test12', methods: ['GET'])]
        public function test12(): Response
        {
            $produit = new Produit();
            $produit->setNom('Machin');
            $produit->setReference('MCH');
            $produit->setTypeConditionnement('Sachet');
            $produit->setQuantite(50);
            $produit->setEmplacement('H123');
            $produit->setPrix(12);
            $produit->setQuota(10);
            $produit->setStock(80);
            $produit->setEstActif(true);
    
        $html2pdf = new Html2Pdf();
        // $html2pdf->writeHTML('<h1>HelloWorld</h1>This is my first test');
        $response = $this->render('test/test7.html.twig', ['produit' => $produit]);
        $html2pdf->writeHTML($response->getContent());
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT, 
            'facture.pdf'
        );
        // $response->headers->set('Content-Disposition', $disposition);
        return new Response($html2pdf->output("", 'S'), 200, ['content-type' => 'application/pdf', 'Content-Disposition' => $disposition]);
        // return new Response($html2pdf->output("facture.pdf", 'S'), 200, ['content-type' => 'application/pdf', 'charset=UTF-8]']);
        }

        // Route qui bascule entre deux types d'affichages
        #[Route('/test13/{display}', name: 'test13', methods: ['GET'])]
        public function test13($display = 'TILES'): Response
        {
            return $this->render('test/test13.html.twig', ['display' => $display]);
        }

    // Route pour afficher le formulaire détaillé des produits
    //   #[Route('/test102', name: 'test102', methods: ['GET'])]
    //   public function test102(): Response
    //   {
    //     $produit = new Produit();
    //     $form = $this->createForm(ProduitType::class, $produit);
    //     return $this->render('test/test102.html.twig', ['form' => $form]);
    //   }
}