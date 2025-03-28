<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Enum\EtatCommande;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    // adds a product to the cart
    #[Route('/commande/add/{id}/{quantite}', name: 'app_commande', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*', 'quantite' => '[0-9]*'])]
    public function add(EntityManagerInterface $entityManager, int $id, int $quantite = 1): Response
    {



        $produits = $entityManager->getRepository(Produit::class)->findBy(['id' => $id, 'estActif' => true]);
        if (!$produits)
            return new JsonResponse(['ok' => false]);
        $produit = $produits[0];
        $commandes = $entityManager->getRepository(Commande::class)->findBy(['etat' => EtatCommande::EN_COURS]);
        if (!$commandes && !$quantite)
            return new JsonResponse(['ok' => false]);
        if (!$commandes) {
            $commande = new Commande();
            $commande->setAcheteur($this->getUser());
            $commande->setEtat(EtatCommande::EN_COURS);
            $commande->setDateCommande(new DateTime());
            $entityManager->persist($commande);
            $entityManager->flush();
        } else {
            $commande = $commandes[0];
        }
        $lignes = $entityManager->getRepository(LigneCommande::class)->findBy(['commande' => $commande->getId(), 'produit' => $produit]);
        if (!$lignes && !$quantite)
            return new JsonResponse(['ok' => false]);
        if ($lignes) {
            $ligne = $lignes[0];
            if (!$quantite) {
                $entityManager->remove($ligne);
                $entityManager->flush();
            } else {
                $ligne->getQuantite($ligne->getQuantite() + $quantite);
                $entityManager->persist($ligne);
                $entityManager->flush();
            }
        } else {
            $ligne = new LigneCommande();
            $ligne->setCommande($commande);
            $ligne->setProduit($produit);
            $ligne->setQuantite($quantite);
            $ligne->setPrix($produit->getPrix());
            $entityManager->persist($ligne);
            $entityManager->flush();
        }
        $commande->setNumero(date('Y-m-d') . '-' . $commande->getId());
        $commande->setTotal((int)$entityManager->getRepository(Commande::class)->findTotal()['total']);
        $entityManager->persist($commande);
        $entityManager->flush();

        return new JsonResponse(['ok' => true]);
    }
    // route that valides the commande
    #[Route('/commande/checkout', name: 'commande_checkout', methods: ['GET'])]
    public function checkout(EntityManagerInterface $entityManager): Response
    {
        $commandes = $entityManager->getRepository(Commande::class)->findBy(['etat' => EtatCommande::EN_COURS]);
        if (!$commandes)
            return new JsonResponse(['ok' => false]);
        $commande = $commandes[0]->setEtat(EtatCommande::EN_INSTANCE)->setDateValidation(new DateTime());
        $entityManager->persist($commande);
        $entityManager->flush();
        return new JsonResponse(['ok' => true]);
    }
    //route that defines the preparation of the commnde by logisticien
    // tout logisticient  peut manager toute commande
    #[Route('/commande/start/{id}', name: 'commande_start', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function start(EntityManagerInterface $entityManager, $id): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        if (!$commande || $commande->getEtat() !== EtatCommande::EN_INSTANCE)
            return new JsonResponse(['ok' => false]);
        $commande->setEtat(EtatCommande::EN_PREPA)->setDateDebutPreparation(new DateTime());
        $entityManager->persist($commande);
        $entityManager->flush();
        return new JsonResponse(['ok' => true]);
    }
    #[Route('/commande/end/{id}', name: 'commande_end', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function end(EntityManagerInterface $entityManager, $id): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        if (!$commande || $commande->getEtat() !== EtatCommande::EN_PREPA)
            return new JsonResponse(['ok' => false]);
        $commande->setEtat(EtatCommande::EN_ATTENTE_EXPEDITION)->setDateFinPreparation(new DateTime());
        $entityManager->persist($commande);
        $entityManager->flush();
        return new JsonResponse(['ok' => true]);
    }
    #[Route('/commande/deliver/{id}', name: 'commande_deliver', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function deliver(EntityManagerInterface $entityManager, $id): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        if (!$commande || $commande->getEtat() !== EtatCommande::EN_ATTENTE_EXPEDITION)
            return new JsonResponse(['ok' => false]);
        $commande->setEtat(EtatCommande::EXPEDIEE)->setDateExpedition(new DateTime());
        $entityManager->persist($commande);
        $entityManager->flush();
        return new JsonResponse(['ok' => true]);
    }
    // route qui affiche la facture
    #[Route('/commande/invoice/{id}', name: 'commande_invoice', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function invoice(EntityManagerInterface $entityManager, $id): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        if (!$commande || $commande->getEtat() !== EtatCommande::EXPEDIEE)
            return $this->render('test/commande/commandeIndisponible.html.twig');
        if ($this->getUser()->getRoles()[0] === 'ROLE_ACHETEUR' && $commande->getAcheteur() !== $this->getUser());
        return $this->render('test/commande/invoice.html.twig', ['commande' => $commande]);
    }

    #[Route('/commande/invoicePDF/{id}', name: 'commande_invoicePDF', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function invoicePDF(EntityManagerInterface $entityManager, $id): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        if (!$commande || $commande->getEtat() !== EtatCommande::EXPEDIEE)
            return $this->render('test/commande/commandeIndisponible.html.twig');
        if ($this->getUser()->getRoles()[0] === 'ROLE_ACHETEUR' && $commande->getAcheteur() !== $this->getUser());
        return $this->render('test/commande/invoice.html.twig', ['commande' => $commande]);

        $html2pdf = new Html2Pdf();
        $response = $this->render('test/commande/invoice.html.twig', ['commande' => $commande]);
        $html2pdf->writeHTML($response->getContent());

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'Cmd_#' . $commande->getNumero() . '_facture.pdf'
        );

        return new Response($html2pdf->output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition
        ]);
    }
}
