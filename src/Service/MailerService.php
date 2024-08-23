<?php

// src/Service/MailerService.php
namespace App\Service;

use SendGrid;
use SendGrid\Mail\Mail;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\RequestStack;

class MailerService
{
    private $sendGrid;
    private $twig;
    private $router;
    private $requestStack;

    // Inject RequestStack dans le service pour obtenir la langue actuelle
    public function __construct(string $sendGridApiKey, Environment $twig, UrlGeneratorInterface $router, RequestStack $requestStack)
    {
        $this->sendGrid = new SendGrid($sendGridApiKey);
        $this->twig = $twig;
        $this->router = $router;
        $this->requestStack = $requestStack;  // Ajoutez ceci
    }

    public function sendValidationEmail(User $user): void
    {
        $email = new Mail();
        $email->setFrom("emarh.harry.code@gmail.com", "Emarh Perfume");

        // Récupérer la langue actuelle
        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        // Générer l'URL de confirmation avec la locale
        $confirmationUrl = $this->router->generate(
            'app_confirm_email',
            ['token' => $user->getConfirmationToken(), '_locale' => $locale],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $emailContent = $this->twig->render('emails/confirmation_email.html.twig', [
            'user' => $user,
            'confirmationUrl' => $confirmationUrl
        ]);

        $subject = $this->twig->getExtension(\Symfony\Bridge\Twig\Extension\TranslationExtension::class)
                            ->trans('email.subject', [], null, $locale);

        $email->setSubject($subject);
        $email->addTo($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName());
        $email->addContent("text/html", $emailContent);

        try {
            $response = $this->sendGrid->send($email);
            if ($response->statusCode() >= 400) {
                throw new \Exception('Failed to send email: ' . $response->body());
            }
        } catch (\Exception $e) {
            error_log('Email sending failed: ' . $e->getMessage());
        }
    }
}
