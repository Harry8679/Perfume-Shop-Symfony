<?php

// src/Service/MailerService.php
namespace App\Service;

use SendGrid;
use SendGrid\Mail\Mail;
use SendGrid\Mail\From; // Importation nécessaire
use App\Entity\User;

class MailerService
{
    private $sendGrid;

    public function __construct(string $sendGridApiKey)
    {
        $this->sendGrid = new SendGrid($sendGridApiKey);
    }

    public function sendValidationEmail(User $user): void
    {
        $email = new Mail();
        
        // Utilisation de l'objet SendGrid\Mail\From
        // $email->setFrom(new From("no-reply@emarh_perfume.fr", "Emarh Perfume"));
        $email->setFrom(new From("emarhdureal@gmail.com", "Emarh Perfume"));

        $email->setSubject("Please Confirm Your Email");
        $email->addTo($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName());
        $email->addContent(
            "text/html",
            "<p>Thank you for registering! Please confirm your email by clicking the following link:</p>" .
            "<p><a href='http://yourdomain.com/confirm/{$user->getConfirmationToken()}'>Confirm Email</a></p>"
        );

        try {
            $response = $this->sendGrid->send($email);
            if ($response->statusCode() >= 400) {
                throw new \Exception('Failed to send email: ' . $response->body());
            }
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            error_log('Email sending failed: ' . $e->getMessage());
        }
    }
}
