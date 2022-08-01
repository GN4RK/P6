<?php

namespace App\Controller;

use App\Entity\Trick;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;


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
}