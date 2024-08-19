<?php
// src/Controller/RegisterController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route(['en' => '/register', 'fr' => '/inscription'], name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerService $mailerService): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Configurer la langue en fonction de l'utilisateur
            $locale = $request->getLocale();
            $request->getSession()->set('_locale', $locale);

            // Créer un token de confirmation
            $user->setConfirmationToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));

            // Persist et flush l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoyer l'email de validation
            $mailerService->sendValidationEmail($user);

            // Ajouter un message flash
            $this->addFlash('success', 'Un email de confirmation a été envoyé. Veuillez vérifier votre boîte de réception.');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('register/register.html.twig', [
            'formRegister' => $form->createView()
        ]);
    }

    #[Route('/confirm/{token}', name: 'app_confirm_email')]
    public function confirmEmail(string $token, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Token invalide.');
        }

        // Supprimer le token et activer le compte utilisateur
        $user->setConfirmationToken(null);
        $user->setIsActive(true); // Assurez-vous que le champ `isActive` existe dans votre entité User
        $entityManager->flush();

        $this->addFlash('success', 'Votre email a été confirmé avec succès.');

        return $this->redirectToRoute('app_homepage');
    }
}
