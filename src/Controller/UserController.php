<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Form\Type\ResetPasswordType;


class UserController extends AbstractController
{
    #[Route('/deleteuser/{id}', name: 'delete_user')]
    public function deleteUser(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['id' => $id]);
        if (empty($user)) {
            $this->addFlash('error', 'User not found');
            return $this->redirectToRoute('home');
        }
        
        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        
        $this->addFlash('warning', 'User deleted.');

        return $this->redirectToRoute('home');
    }

    #[Route('/validate/{username}/{token}', name: 'validate')]
    public function validateUser(string $username, string $token, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);

        if ($user->getStatus() == $token) {
            $user->setStatus("validated");
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', "User validated ! You can now sign in.");
            return $this->redirectToRoute('home');
        }

        $this->addFlash('error', "invalid token");
        return $this->redirectToRoute('home');
    }

    #[Route('/reset/{token}', name: 'reset_password')]
    public function resetPassword(UserPasswordHasherInterface $passwordHasher, string $token, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            $this->addFlash('error', "Wrong token.");
            return $this->redirectToRoute('sign_in');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->getData()['username'] != $user->getUsername()) {
                $this->addFlash('error', "Wrong username.");
                return $this->render('sign/reset_password.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            $pass1 = $form->getData()['new_password'];
            $pass2 = $form->getData()['confirm'];

            if ($pass1 != $pass2) {
                $this->addFlash('error', "Passwords are not the same.");
                return $this->render('sign/reset_password.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $pass1
            ));

            $user->setToken(null);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', "Password reset ! You can now sign in.");

            return $this->redirectToRoute('sign_in');
        }

        return $this->render('sign/reset_password.html.twig', [
            'form' => $form->createView()
        ]);

    }
}