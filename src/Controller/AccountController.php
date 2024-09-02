<?php

namespace App\Controller;

use App\Form\PasswordUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    public function update_password(): Response
    {
        $form = $this->createForm(PasswordUserType::class);

        return $this->render('account/update_password.html.twig', [
            'formUpdatePassword' => $form->createView()
        ]);
    }
}
