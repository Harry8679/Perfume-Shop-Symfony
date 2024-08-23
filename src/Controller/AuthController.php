<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\MailerService;

class AuthController extends AbstractController
{
    #[Route(['en' => '/login-register', 'fr' => '/connexion-inscription'], name: 'app_login_register')]
    public function LoginRegister(Request $request, EntityManagerInterface $entityManager, MailerService $mailerService, AuthenticationUtils $authenticationUtils): Response 
    {
        // Redirection si l'utilisateur est déjà authentifié
        if ($this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        // Gestion de l'inscription
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));

            $entityManager->persist($user);
            $entityManager->flush();

            $mailerService->sendValidationEmail($user);

            $this->addFlash('success', 'Un email de confirmation a été envoyé. Veuillez vérifier votre boîte de réception.');

            return $this->redirectToRoute('app_homepage');
        }

        // Gestion de la connexion
        $lastUsername = $authenticationUtils->getLastUsername() ?? ''; // Assurez-vous que lastUsername est toujours une chaîne de caractères
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('auth/login_register.html.twig', [
            'formRegister' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(['en' => '/login', 'fr' => '/connexion'], name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirection si l'utilisateur est déjà authentifié
        if ($this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername ?? '',
            'error' => $error,
        ]);
    }

    #[Route(['en' => '/register', 'fr' => '/inscription'], name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, MailerService $mailerService): Response
    {
        // Redirection si l'utilisateur est déjà authentifié
        if ($this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));

            $entityManager->persist($user);
            $entityManager->flush();

            $mailerService->sendValidationEmail($user);

            $this->addFlash('success', 'Un email de confirmation a été envoyé. Veuillez vérifier votre boîte de réception.');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Le code ici ne sera jamais exécuté
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
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
