<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\Type\TrickType;
use App\Form\Type\CommentType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function PHPUnit\Framework\isEmpty;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick/details/{slug}", name="trick_details")
     */
    public function details(string $slug, ManagerRegistry $doctrine, Request $request): Response
    {
       
        $trick = $doctrine->getRepository(Trick::class)->findOneBy(['slug' => $slug]);
        dump($trick);

        // TODO get current logged user
        $user = $doctrine->getRepository(User::class)->find(1);

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        $commentForm->createView();
        
        return $this->render('trick/details.html.twig', [
            'trick' => $trick,
            'user' => $user,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    /**
     * @Route("/trick/create", name="trick_create")
     */
    public function create(ManagerRegistry $doctrine, Request $request): Response
    {
        // TODO get current logged user
        $user = $doctrine->getRepository(User::class)->find(1);
        if (empty($user)) return $this->redirectToRoute('error_you_have_to_be_logged_in');

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick = $form->getData();
            $trick->setUser($user);
            $trick->setDate(new \DateTime());
            $slugger = new AsciiSlugger();
            $trick->setSlug($slugger->slug($trick->getName()));
            
            $tricksRepository = $doctrine->getRepository(Trick::class);
            $trickAlreadyInDB = $tricksRepository->findOneBy(['name' => $trick->getName()]);

            if ($trickAlreadyInDB) {
                return $this->redirectToRoute('error_name_already_used');
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_details', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}