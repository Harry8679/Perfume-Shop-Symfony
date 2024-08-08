<?php 

namespace App\Controller;

use App\Form\RegisterUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route(['en' => '/register', 'fr' => '/inscription'], name: 'app_register')]
    public function index(): Response
    {
        $form = $this->createForm(RegisterUserType::class);
        return $this->render('register/register.html.twig', [
            'formRegister' => $form->createView()
        ]);
    }
}
