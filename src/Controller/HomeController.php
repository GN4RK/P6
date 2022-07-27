<?php

namespace App\Controller;

use App\Entity\Trick;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ManagerRegistry $doctrine): Response
    {
        $tricksRepository = $doctrine->getRepository(Trick::class);
        // first 5 tricks loaded
        $tricks = $tricksRepository->findBy([], ['date' => 'DESC'], 5, 5);

        return $this->render('home/home.html.twig', ['tricks' => $tricks]);
    }

    #[Route('/start/{start}', name: 'home_ajax')]
    public function homeAjax(ManagerRegistry $doctrine, Request $request, mixed $start = 0): Response
    {
        $tricksRepository = $doctrine->getRepository(Trick::class);
        $tricks = $tricksRepository->findBy([], ['date' => 'DESC'], 5, $start);

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $response = array();
            foreach($tricks as $trick) {
                $response[] = array (
                    'name' => $trick->getName(),
                    'slug' => $trick->getSlug(),
                );
            }

            return new JsonResponse($response);
        }

        return $this->redirectToRoute('home');
    }
}