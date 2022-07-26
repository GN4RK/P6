<?php

namespace App\Controller;

use App\Form\Type\SignUpType;
use App\Form\Type\ForgotPasswordType;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignController extends AbstractController
{
    #[Route('/sign/in', name: 'sign_in')]
    public function signIn(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('sign/signin.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/sign/up', name: 'sign_up')]
    public function signUp(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(SignUpType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $user->setStatus("pending");
            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            ));

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('sign/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sign/forgot_password', name: 'forgot_password')]
    public function forgotPassword(Request $request): Response {

        $user = new User();
        $form = $this->createForm(ForgotPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


        }






        return $this->render('sign/forgot_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/sign/logout', name: 'log_out')]
    public function logOut(): Response {
        return new Response();
    }

}