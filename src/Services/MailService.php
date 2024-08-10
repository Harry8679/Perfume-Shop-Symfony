<?php

// src/Service/MailerService.php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendValidationEmail($user)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@votre-domaine.com', 'Votre Nom'))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->subject('Confirmez votre adresse email')
            ->htmlTemplate('emails/registration_confirmation.html.twig')
            ->context([
                'user' => $user,
                'confirmationUrl' => sprintf('https://votre-domaine.com/confirm/%s', $user->getConfirmationToken())
            ]);

        $this->mailer->send($email);
    }
}
