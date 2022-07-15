<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SignController extends AbstractController
{
    #[Route('/signin', name: 'sign_in')]
    public function signIn(): Response
    {
        return $this->render('sign/signin.html.twig');
    }

    #[Route('/signup', name: 'sign_up')]
    public function signUp(): Response
    {
        return $this->render('sign/signup.html.twig');
    }
}