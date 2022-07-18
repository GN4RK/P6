<?php

namespace App\Controller;

use App\Entity\Trick;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ManagerRegistry $doctrine): Response
    {
        $tricksRepository = $doctrine->getRepository(Trick::class);
        $tricks = $tricksRepository->findAll();

        return $this->render('home/home.html.twig', ['tricks' => $tricks]);
    }
}