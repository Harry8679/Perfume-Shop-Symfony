<?php

namespace App\Controller;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    #[Route(['en' => '/account', 'fr' => '/compte'], name: 'app_account')]
    public function index(): Response
    {
        
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }
        
        return $this->render('account/index.html.twig');
    }

    #[Route(['en' => '/account/update-password', 'fr' => '/compte/modifier-votre-mot-de-passe'], name: 'app_account_update_password')]
    public function update_password(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());
            $em->flush();
        }

        return $this->render('account/update_password.html.twig', [
            'formUpdatePassword' => $form->createView(),
        ]);
    }
}
