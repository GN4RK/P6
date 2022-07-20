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
    #[Route('/{start}', name: 'home')]
    public function home(ManagerRegistry $doctrine, Request $request, int $start = 0): Response
    {
        $tricksRepository = $doctrine->getRepository(Trick::class);

        // first 5 tricks loaded
        $tricks = $tricksRepository->findBy([], ['date' => 'DESC'], 5, $start);
        // $tricks = $tricksRepository->findAll();

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
          
            $response = array();
            foreach($tricks as $trick) {
                $response[] = array (
                    'name' => $trick->getName(),
                    'slug' => $trick->getSlug(),
                );

            }

            dump($tricks);
            dump($response);
            return new JsonResponse($response);
        }

       

        return $this->render('home/home.html.twig', ['tricks' => $tricks]);
    }
}