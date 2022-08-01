<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Media;
use App\Form\Type\ImageType;
use App\Form\Type\YoutubeType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;

class MediaController extends AbstractController
{
    #[Route('/trick/edit/{slug}/addimage', name: 'trick_add_image')]
    public function addImage(string $slug, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        if (empty($user)) {
            $this->addFlash('error', 'You have to be logged in to add a media to a trick');
            return $this->redirectToRoute('home');
        }
        
        $trick = $doctrine->getRepository(Trick::class)->findOneBy(['slug' => $slug]);

        $media = new Media();

        $form = $this->createForm(ImageType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $directory = "upload/img";

            $file = $form['content']->getData();

            $extension = $file->guessExtension();
            if (!$extension) {
                // extension cannot be guessed
                $extension = 'bin';
            }

            $newFileName = uniqid (rand(1000000,9999999), true).'.'.$extension;
            $file->move($directory, $newFileName);

            $media->setTrick($trick);
            $media->setType("image");
            $media->setContent($newFileName);
            $media->setDate(new \DateTime());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($media);
            $entityManager->flush();

            $this->addFlash('success', 'Media successfully created.');

            return $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/add_image.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);


    }

    #[Route('/trick/edit/{slug}/addyoutube', name: 'trick_add_youtube')]
    public function addYoutube(string $slug, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        if (empty($user)) {
            $this->addFlash('error', 'You have to be logged in to add a media to a trick');
            return $this->redirectToRoute('home');
        }
        
        $trick = $doctrine->getRepository(Trick::class)->findOneBy(['slug' => $slug]);

        $media = new Media();

        $form = $this->createForm(YoutubeType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $media = $form->getData();

            if (!str_contains($media->getContent(), "https://www.youtube.com/watch?v=")) {
                $this->addFlash('error', 'Bad format.');
                return $this->render('trick/add_youtube.html.twig', [
                    'trick' => $trick,
                    'form' => $form->createView(),
                ]);
            }



            $media->setTrick($trick);
            $media->setType("youtube");
            $media->setDate(new \DateTime());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($media);
            $entityManager->flush();

            $this->addFlash('success', 'Media successfully created.');

            return $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/add_youtube.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/media/delete/{id}', name: 'delete_media')]
    public function deleteMedia(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        if (empty($user)) {
            $this->addFlash('error', 'You have to be logged in to delete a trick');
            return $this->redirectToRoute('home');
        }
        
        $media = $doctrine->getRepository(Media::class)->findOneBy(['id' => $id]);
        $trick = $media->getTrick();

        // delete file if the media is an image
        if ($media->getType() == "image") {
            $filesystem = new Filesystem();
            $filesystem->remove(['upload/img/'. $media->getContent()]);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($media);
        $entityManager->flush();
        
        $this->addFlash('warning', 'Media deleted.');

        return $this->redirectToRoute('trick_edit', ["slug" => $trick->getSlug()]);

    }

}