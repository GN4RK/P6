<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;

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
    public function create(ManagerRegistry $doctrine, Request $request): Response
    {
        $trick = new Trick();
        
        $form = $this->createFormBuilder($trick)
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'jump' => 'jump',
                    'rotation' => 'rotation',
                    'press' => 'press',
                    'grab' => 'grab',
                    'rail' => 'rail',
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Trick'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $trick = $form->getData();

            $user = $doctrine->getRepository(User::class)->find(1);
            $tricksRepository = $doctrine->getRepository(Trick::class);

            $trick->setUser($user);
            $trick->setDate(new \DateTime());

            $trickAlreadyInDB = $tricksRepository->findOneBy(['name' => $trick->getName()]);

            if ($trickAlreadyInDB) {
                return $this->redirectToRoute('error_name_already_used');
            }


            $entityManager = $doctrine->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_details', ['slug' => $trick->getName()]);
        }

        return $this->render('trick/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}