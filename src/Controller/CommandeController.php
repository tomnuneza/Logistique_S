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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{

    #[Route('/panier', name: 'panier', methods: ['GET'])]
public function panier(EntityManagerInterface $entityManager): Response
{
    $commande = $entityManager->getRepository(Commande::class)->findOneBy([
        'etat' => EtatCommande::EN_COURS,
        'acheteur' => $this->getUser(),
    ]);

    return $this->render('/commande/panier.html.twig', [
        'commande' => $commande,
    ]);
}


    #[Route('/commande/update/{id}', name: 'commande_update', methods: ['GET'])]
public function update(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $quantite = (int) $request->query->get('quantite', 1);

    $produit = $entityManager->getRepository(Produit::class)->find($id);
    $commande = $entityManager->getRepository(Commande::class)->findOneBy([
        'etat' => EtatCommande::EN_COURS,
        'acheteur' => $this->getUser(),
    ]);

    if (!$produit || !$commande) {
        return $this->redirectToRoute('test9');
    }

    $ligne = $entityManager->getRepository(LigneCommande::class)->findOneBy([
        'commande' => $commande,
        'produit' => $produit,
    ]);

    if ($ligne) {
        if ($quantite === 0) {
            $entityManager->remove($ligne);
        } else {
            $ligne->setQuantite($quantite);
            $entityManager->persist($ligne);
        }

        // Update total
        $commande->setTotal((int) $entityManager->getRepository(Commande::class)->findTotal()['total']);
        $entityManager->persist($commande);
        $entityManager->flush();
    }

    return $this->redirectToRoute('panier');
}

    #[Route('/commande/remove/{id}', name: 'commande_remove', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function remove(EntityManagerInterface $entityManager, int $id): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        $commande = $entityManager->getRepository(Commande::class)->findOneBy([
            'etat' => EtatCommande::EN_COURS,
            'acheteur' => $this->getUser(),
        ]);

        $ligne = $entityManager->getRepository(LigneCommande::class)->findOneBy([
            'commande' => $commande,
            'produit' => $produit,
        ]);

        // Decrease quantity or remove line
        if ($ligne->getQuantite() > 1) {
            $ligne->setQuantite($ligne->getQuantite() - 1);
            $entityManager->persist($ligne);
        } else {
            $entityManager->remove($ligne);
        }

        // Update total if needed
        // $commande->setTotal((int) $entityManager->getRepository(Commande::class)->findTotal()['total']);
        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->redirectToRoute('panier'); // or return JsonResponse if using fetch
    }


    // Route qui ajoute un produit au panier dans une certaine quantité (ou le supprime si quantité nulle).
    #[Route('/commande/add/{id}', name: 'commande_add', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function add(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $quantite = (int) $request->query->get('quantite', 1);
        // Stocker l'e
        // Tenter de récupérer le produit.
        $produits = $entityManager->getRepository(Produit::class)->findBy(['id' => $id, 'estActif' => true]);
        // Si produit introuvable, erreur.
        if (!$produits)
            return new JsonResponse(['ok' => false]);
        $produit = $produits[0];
        // $total = $produit->getPrix() * $quantite;
        // Tenter de récupérer la commande en cours de l’utilisateur logué.
        $commandes = $entityManager->getRepository(Commande::class)->findBy(['etat' => EtatCommande::EN_COURS]);
        // Si pas de commande en cours et quantité nulle, erreur.
        if (!$commandes && !$quantite)
            return new JsonResponse(['ok' => false]);
        // Sinon, si pas de commande en cours, en créer une.
        if (!$commandes) {
            $commande = new Commande();
            $commande->setAcheteur($this->getUser());
            $commande->setEtat(EtatCommande::EN_COURS);
            $commande->setDateCommande(new DateTime());
            $entityManager->persist($commande);
            $entityManager->flush();
        }
        // Sinon récupérer la commande
        else {
            $commande = $commandes[0];
        }
        // Récupérer une éventuelle ligne de commande avec le produit.
        $lignes = $entityManager->getRepository(LigneCommande::class)->findBy(['commande' => $commande, 'produit' => $produit]);
        // Si pas de ligne et quantité nulle, erreur.
        if (!$lignes && !$quantite)
            return new JsonResponse(['ok' => false]);
        // Si déjà une ligne, modifier la quantité.
        if ($lignes) {
            $ligne = $lignes[0];
            // Si quantité nulle, supprimer la ligne.
            if (!$quantite) {
                $entityManager->remove($ligne);
                $entityManager->flush();
            } else {
                // Sinon ajouter la nouvelle quantite et persister (hors numéro et total).
                $ligne->setQuantite($ligne->getQuantite() + $quantite);
                $entityManager->persist($ligne);
                $entityManager->flush();
            }
        }
        // Sinon, créer une nouvelle ligne et persister.
        else {
            $ligne = new LigneCommande();
            $ligne->setCommande($commande);
            $ligne->setProduit($produit);
            $ligne->setQuantite($quantite);
            $ligne->setPrix($produit->getPrix());
            $entityManager->persist($ligne);
            $entityManager->flush();
        }
        // Définir le numéro et le total de la commande et persister.
        $commande->setNumero(date('Y-m-d') . '-' . $commande->getId());
        $commande->setTotal((int)$entityManager->getRepository(Commande::class)->findTotal()['total']);
        $entityManager->persist($commande);
        $entityManager->flush();

        // Retourner une Response JSON (idéalement avec le contenu du panier simplifié).
        return $this->redirectToRoute('test9');
        return new JsonResponse(['ok' => true]);
        
    }

    // Route qui valide la commmande
    #[Route('/commande/checkout', name: 'commande_checkout', methods: ['GET'])]
    public function checkout(EntityManagerInterface $entityManager): Response
    // Récupérer l’unique commande en cours de l’utilisateur logué.
    {
        $commandes = $entityManager->getRepository(Commande::class)->findBy(['etat' => EtatCommande::EN_COURS]);
        // Si pas de commandes en cours
        if (!$commandes)
            return new JsonResponse(['ok' => false]);
        // Définir dateValidation et passer l’état à EN_INSTANCE.
        $commande = $commandes[0]->setEtat(EtatCommande::EN_INSTANCE)->setDateValidation(new DateTime());
        // Pour chaîner, il faut savoir ce que ça retourne d'abord
        // Persister
        $entityManager->persist($commande);
        $entityManager->flush();

        return new JsonResponse(['ok' => true]);
    }

    // Route qui acte le début de préparation de la commmande par le logisticien.
    // Note : Tout logisticien peut manager toute commande.
    #[Route('/commande/begin/{id}', name: 'commande_begin', methods: ['GET'], requirements: ['id' => '[1-9][0-9]*'])]
    public function begin(EntityManagerInterface $entityManager, int $id): Response
    // Tenter de récupérer la commande.
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        // Si pas de commande ou commande pas en instance, erreur.
        if (!$commande || $commande->getEtat() !== EtatCommande::EN_INSTANCE)
            return new JsonResponse(['ok' => false]);
        // Modifier l’état et persister.
        $commande->setEtat(EtatCommande::EN_PREPA);
        $commande->setDateDebutPrepa(new DateTime());
        // Persister
        $entityManager->persist($commande);
        $entityManager->flush();

        return new JsonResponse(['ok' => true]);
    }

    // Route qui acte la fin de la préparation.
    #[Route('/commande/end/{id}', name: 'commande_end', methods: ['GET'])]
    public function end(EntityManagerInterface $entityManager, int $id): Response
    // Récupérer l’unique commande en fin de préparation de l’utilisateur logué.
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        // Si pas de commandes en cours
        if (!$commande || $commande->getEtat() !== EtatCommande::EN_PREPA)
            return new JsonResponse(['ok' => false]);
        // Définir dateFinPrepa et passer l’état à EN_ATTENTE_EXPEDITION.
        $commande->setEtat(EtatCommande::EN_ATTENTE_EXPEDITION)->setDateFinPrepa(new DateTime());
        // Persister
        $entityManager->persist($commande);
        $entityManager->flush();

        return new JsonResponse(['ok' => true]);
    }

    // Route qui acte la date d'expédition de la commmande et génère une facture
    #[Route('/commande/expedition/{id}', name: 'commande_expedition', methods: ['GET'])]
    public function expedition(EntityManagerInterface $entityManager, $id): Response
    // Récupérer l’unique commande en attente d'expédition de l’utilisateur logué.
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        // Si pas de commandes en cours
        if (!$commande || $commande->getEtat() !== EtatCommande::EN_ATTENTE_EXPEDITION)
            return new JsonResponse(['ok' => false]);
        // Modifier l’état et persister.
        // $commande = $commandes[0];
        $commande->setEtat(EtatCommande::EXPEDIEE);
        $commande->setDateExpedition(new DateTime());
        // Persister
        $entityManager->persist($commande);
        $entityManager->flush();
        return new JsonResponse(['ok' => true]);
        // Récupérer les infos de la commande
        // $etat = EtatCommande::EXPEDIEE->value;
        // $commande->getAcheteur($this->getUser());
        // $commande->getNumero();
        // $commande->getEtat($etat);
        // $commande->getDateExpedition();
        // $lignes = $entityManager->getRepository(LigneCommande::class)->findBy(['commande' => $commande]);
        // $produits = $entityManager->getRepository(Produit::class)->findBy(['id' => $id]);

        // foreach ($lignes as $ligne) {
        //     $ligne->getProduit($id);
        //     $ligne->getQuantite();
        //     $ligne->getPrix();
        // }
        // $commande->setTotal((int)$entityManager->getRepository(Commande::class)->findTotal()['total']);
        // $html2pdf = new Html2Pdf();
        // $response = $this->render('test/expedition.html.twig', ['commande' => $commande, 'lignes' => $lignes]);
        // $html2pdf->writeHTML($response->getContent());
        // $disposition = HeaderUtils::makeDisposition(
        //     HeaderUtils::DISPOSITION_ATTACHMENT,
        //     'facture.pdf'
        // );



        // return new Response($html2pdf->output("", 'S'), 200, ['content-type' => 'application/pdf', 'Content-Disposition' => $disposition]);
    }

    // Route qui affiche la facture (au moement de l'expédition).
    #[Route('/commande/invoice/{id}', name: 'commande_invoice', methods: ['GET'])]
    public function invoice(EntityManagerInterface $entityManager, $id): Response
    // Récupérer l’unique commande en attente d'expédition de l’utilisateur logué.
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        // Si pas de commandes en cours
        if (!$commande || $commande->getEtat() !== EtatCommande::EXPEDIEE)
            return $this->render('commande/commandeIndisponible.html.twig');
        // Si utilisateur ACHETEUR et PAS propriétaire de la commande, alors erreur.
        if ($this->getUser()->getRoles()[0] === 'ROLE_ACHETEUR' && $commande->getAcheteur() !== $this->getUser())
            return $this->render('commande/commandeIndisponible.html.twig');
        return $this->render('commande/invoice.html.twig', ['commande' => $commande]);
    }
    // Route qui download la facture pdf
    #[Route('/commande/invoicePDF/{id}', name: 'commande_invoicePDF', methods: ['GET'])]
    public function invoicePDF(EntityManagerInterface $entityManager, $id): Response
    // Récupérer l’unique commande en attente d'expédition de l’utilisateur logué.
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        // Si pas de commandes en cours
        if (!$commande || $commande->getEtat() !== EtatCommande::EXPEDIEE)
            return $this->render('commande/commandeIndisponible.html.twig');
        // Si utilisateur ACHETEUR et PAS propriétaire de la commande, alors erreur.
        if ($this->getUser()->getRoles()[0] === 'ROLE_ACHETEUR' && $commande->getAcheteur() !== $this->getUser())
            return $this->render('commande/commandeIndisponible.html.twig');

        // Construire et downloader le PDF.
        $html2pdf = new Html2Pdf();
        $response = $this->render('commande/invoice.html.twig', ['commande' => $commande]);
        $html2pdf->writeHTML($response->getContent());
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'Cmd_#' . $commande->getNumero() . '_facture.pdf'
        );
        return new Response($html2pdf->output("", 'S'), 200, ['content-type' => 'application/pdf', 'Content-Disposition' => $disposition]);
    }
}
