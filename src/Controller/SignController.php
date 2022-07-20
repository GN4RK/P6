<?php

namespace App\Controller;

use App\Form\Type\SignUpType;
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
    #[Route('/signin', name: 'sign_in')]
    public function signIn(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('sign/signin.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/signup', name: 'sign_up')]
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

            dump($user);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


   
        }





        return $this->render('sign/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'log_out')]
    public function logOut(): Response {
        return new Response();
    }

}