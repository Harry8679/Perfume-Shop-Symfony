<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    #[Route('/{_locale}', name: 'app_homepage', requirements: ['_locale' => 'en|fr'])]
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig');
    }
}
