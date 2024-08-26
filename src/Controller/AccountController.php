<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    #[Route(['en' => '/account', 'fr' => '/mon-compte'], name: 'app_account')]
    // #[Route(['en' => '/register', 'fr' => '/inscription'], name: 'app_register')]
    public function index(): Response
    {
        
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }
        
        return $this->render('account/index.html.twig');
    }
}
