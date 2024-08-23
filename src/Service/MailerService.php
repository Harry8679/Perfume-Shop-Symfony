<?php

// src/Service/MailerService.php
namespace App\Service;

use SendGrid;
use SendGrid\Mail\Mail;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class MailerService
{
    private $sendGrid;
    private $twig;
    private $router;

    public function __construct(string $sendGridApiKey, Environment $twig, UrlGeneratorInterface $router)
    {
        $this->sendGrid = new SendGrid($sendGridApiKey);
        $this->twig = $twig;
        $this->router = $router;
    }

    public function sendValidationEmail(User $user): void
{
    $email = new Mail();

    $email->setFrom("emarh.harry.code@gmail.com", "Emarh Perfume");

    // Générer l'URL de confirmation avec le token
    $confirmationUrl = $this->router->generate('app_confirm_email', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

    // Charger le template Twig
    $emailContent = $this->twig->render('emails/confirmation_email.html.twig', [
        'user' => $user,
        'confirmationUrl' => $confirmationUrl
    ]);

    // Définir l'objet du mail ici
    $subject = $this->twig->getExtension(\Symfony\Bridge\Twig\Extension\TranslationExtension::class)
                         ->trans('email.subject');

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
