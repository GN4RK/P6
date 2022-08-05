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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SignController extends AbstractController
{
    #[Route('/sign/in', name: 'sign_in')]
    public function signIn(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('sign/sign_in.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/sign/up', name: 'sign_up')]
    public function signUp(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine, Request $request, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(SignUpType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $user->setStatus("pending" . uniqid (rand(1000000,9999999), true));
            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            ));

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // sending validation email
            $email = (new Email())
            ->from('yoann.leonard@gmail.com')
            ->to($user->getEmail())
            ->subject('SnowTricks - Please validate your email adress')
            ->html(
                '<p>Welcome in Snowtricks !</p>' .
                '<p>Please click on the link below to validate your email adress</p>'.
                '<a href="' . $request->getSchemeAndHttpHost() .
                '/validate/' . $user->getUsername() .
                '/' . $user->getStatus().
                '">LINK</a>'
            );

            $mailer->send($email);
            $this->addFlash('success', "Please check your email to validate your user account.");
            
        }

        return $this->render('sign/sign_up.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sign/forgot_password', name: 'forgot_password')]
    public function forgotPassword(Request $request, ManagerRegistry $doctrine, MailerInterface $mailer): Response {

        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = new User();
            $username = $form->getData()['username'];
            $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);

            if ($user) {
                $user->setNewToken();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // sending mail with token to reset password
                $email = (new Email())
                ->from('yoann.leonard@gmail.com')
                ->to($user->getEmail())
                ->subject('SnowTricks - Forgot Password')
                ->html(
                    '<p>To reset your password, please click on the link below</p>'.
                    '<a href="' . $request->getSchemeAndHttpHost() .
                    '/reset/' . $user->getToken().
                    '">LINK</a>'
                );
    
                $mailer->send($email);
                $this->addFlash('success', "Please check your email to reset your password.");
            }
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