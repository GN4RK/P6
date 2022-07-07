<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick/details/{slug}", name="trick_details")
     */
    public function details(): Response
    {
        return $this->render('trick/details.html.twig');
    }

    /**
     * @Route("/trick/create", name="trick_create")
     */
    public function create(): Response
    {
        return $this->render('trick/create.html.twig');
    }

}