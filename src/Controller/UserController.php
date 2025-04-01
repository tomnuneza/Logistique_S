<?php

namespace App\Controller;

use App\Entity\PointDeVente;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class UserController extends AbstractController
{

    // Route qui va lister tous les utilisateurs
    #[Route('/userlist', name: 'userlist', methods: ['GET'])]
    public function userlist(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findBy([], ['nom' => 'ASC']);
        $poinDeVentes = $entityManager->getRepository(PointDeVente::class)->findBy([], ['nom' => 'ASC']);
        return $this->render('user/userlist.html.twig', [
            'users' => $users,
            'pointDeVentes' => $poinDeVentes,
        ]);
    }

    // Route pour afficher le formulaire global des utilisateurs
    #[Route('/create/{id}', name: 'create', methods: ['GET', 'POST'], requirements: ['id' => '[1-9][0-9]*'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, int $id = 0): Response
    {
        $user = $id ? $entityManager->getRepository(User::class)->find($id) : new User();
        $pointDeVentes = $entityManager->getRepository(PointDeVente::class)->findBy([], ['nom' => 'ASC']);
        $form = $this->createForm(UserType::class, $user, ['pointDeVentes' => $pointDeVentes]); // 
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $user->setEmail();
            $plainPassword = $form->get('password')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            // $user->setNom();
            // $user->setPrenom();
            // $user->setTelephone();
            // $user->setRoles();
            // $user->setEstActif();
            // $user->setPointDeVente();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('userlist');
        }
        return $this->render('user/create.html.twig', ['form' => $form]);
    }
}
