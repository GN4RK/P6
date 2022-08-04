<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Media;
use App\Form\Type\TrickType;
use App\Form\Type\CommentType;
use App\Form\Type\FeaturedType;
use App\Form\Type\NewTrickType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Filesystem\Filesystem;

class TrickController extends AbstractController
{
    #[Route('/trick/details/{slug}', name: 'trick_details')]
    public function details(string $slug, ManagerRegistry $doctrine, Request $request): Response
    {
       
        $trick = $doctrine->getRepository(Trick::class)->findOneBy(['slug' => $slug]);
        $user = $this->getUser();
        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $comment->setUser($user);
            $comment->setTrick($trick);
            $comment->setDate(new \DateTime());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

        }
        
        return $this->render('trick/details.html.twig', [
            'trick' => $trick,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/trick/create', name: 'trick_create')]
    public function create(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): Response
    {
        // current user
        $user = $this->getUser();
        if (empty($user)) {
            $this->addFlash('error', 'You have to be logged in to view this page');
            return $this->redirectToRoute('sign_in');
        }

        $entityManager = $doctrine->getManager();

        $trick = new Trick();
        $form = $this->createForm(NewTrickType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setUser($user);
            $trick->setName($form->getData()['name']);
            $trick->setDate(new \DateTime());
            $trick->setCategory($form->getData()['category']);
            $trick->setDescription($form->getData()['description']);
            $slugger = new AsciiSlugger();
            $trick->setSlug(strtolower($slugger->slug($trick->getName())));

            // validating inputs
            $violations = $validator->validate($trick);
            if (count($violations) > 0) {
                $violation = $violations[0];
                $this->addFlash('error', $violation->getMessage());
                return $this->render('trick/create.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            // handling youtube link
            if (isset($form->getData()['youtube_link'])) {
                $youtube = new Media();
                $youtube->setContent($form->getData()['youtube_link']);

                if (!str_contains($youtube->getContent(), "https://www.youtube.com/watch?v=")) {
                    $this->addFlash('error', 'Bad format for youtube link. Please use a link like this : https://www.youtube.com/watch?v=xxxxxxxxxxx');
                    return $this->render('trick/create.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }

                $youtube->setTrick($trick);
                $youtube->setType("youtube");
                $youtube->setDate(new \DateTime());

                $entityManager->persist($youtube);
                $this->addFlash('success', 'New youtube video added.');

            }

            // handling image file
            if (isset($form->getData()['image'])) {
                $image = new Media();
                $directory = "upload/img";
                $file = $form['image']->getData();
                $extension = $file->guessExtension();
                if (!$extension) {
                    // extension cannot be guessed
                    $extension = 'bin';
                }
                $violations = $validator->validate(
                    $file, 
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => ['image/*']
                    ])
                );
                if ($violations->count() > 0) {
                    $violation = $violations[0];
                    $this->addFlash('error', $violation->getMessage());
                    return $this->render('trick/create.html.twig', [
                        'form' => $form->createView()
                    ]);
                }

                $newFileName = uniqid (rand(1000000,9999999), true).'.'.$extension;
                $file->move($directory, $newFileName);
                $image->setTrick($trick);
                $image->setType("image");
                $image->setContent($newFileName);
                $image->setDate(new \DateTime());
                $entityManager->persist($image);
                $trick->setFeaturedImage($image);
                $this->addFlash('success', 'New image added.');

            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();
            $this->addFlash('success', 'Trick successfully created.');

            return $this->redirectToRoute('trick_details', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/trick/edit/{slug}', name: 'trick_edit')]
    public function edit(string $slug, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        if (empty($user)) {
            $this->addFlash('error', 'You have to be logged in to edit a trick');
            return $this->redirectToRoute('home');
        }

        $trick = $doctrine->getRepository(Trick::class)->findOneBy(['slug' => $slug]);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $trick = $form->getData();

            $tricksRepository = $doctrine->getRepository(Trick::class);
            $trickAlreadyInDB = $tricksRepository->findOneBy(['name' => $trick->getName()]);
            if ($trickAlreadyInDB && $trickAlreadyInDB->getId() != $trick->getId()) {
                $this->addFlash('error', 'Name already used');
                return $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
            }
            $slugger = new AsciiSlugger();
            $trick->setSlug(strtolower($slugger->slug($trick->getName())));
            
            $trick->setLastUpdate(new \DateTime());

            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Trick successfully edited.');

            return $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
        }
        
        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/trick/delete/{slug}', name: 'trick_delete')]
    public function delete(string $slug, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        if (empty($user)) {
            $this->addFlash('error', 'You have to be logged in to delete a trick');
            return $this->redirectToRoute('home');
        }
        
        $trick = $doctrine->getRepository(Trick::class)->findOneBy(['slug' => $slug]);

        $medias = $trick->getMedia();
        foreach($medias as $media) {
            // delete file if the media linked is an image
            if ($media->getType() == "image") {
                $filesystem = new Filesystem();
                $filesystem->remove(['upload/img/'. $media->getContent()]);
            }
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($trick);
        $entityManager->flush();
        
        $this->addFlash('warning', 'Trick ['. $trick->getName(). '] deleted.');

        return $this->redirectToRoute('home');
    }

    #[Route('/trick/edit/featured/{slug}', name: 'trick_edit_featured')]
    public function editFeatured(string $slug, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        if (empty($user)) {
            $this->addFlash('error', 'You have to be logged in to delete a trick');
            return $this->redirectToRoute('home');
        }
        
        $trick = $doctrine->getRepository(Trick::class)->findOneBy(['slug' => $slug]);
        $media_list = $doctrine->getRepository(Media::class)->findBy([
            'trick' => $trick,
            'type' => 'image',
        ]);
        $form = $this->createForm(FeaturedType::class, $trick, ['data' => $media_list]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $featuredImage = $form->getData()["featuredImage"];            
            $trick->setLastUpdate(new \DateTime());
            $trick->setFeaturedImage($featuredImage);

            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Trick successfully edited.');

            return $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
        }
        
        return $this->render('trick/editFeatured.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/trick/edit/unlinkfeatured/{slug}', name: 'trick_unlink_featured')]
    public function unlinkFeatured(string $slug, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        if (empty($user)) {
            $this->addFlash('error', 'You have to be logged in to edit a trick');
            return $this->redirectToRoute('home');
        }
        
        $trick = $doctrine->getRepository(Trick::class)->findOneBy(['slug' => $slug]);
        $trick->setFeaturedImage(null);

        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        $this->addFlash('success', 'Featured image unlinked successfully.');

        return $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
    }

    #[Route('/trick/details/{slug}/start/{start}', name: 'comment_ajax')]
    public function moreCommentsAjax(string $slug, ManagerRegistry $doctrine, Request $request, int $start = 10): Response
    {
        $trick = $doctrine->getRepository(Trick::class)->findOneBy(['slug' => $slug]);
        $comments = $trick->getComments();
        $end = $start + 10;

        if ($end > count($comments)) $end = count($comments);

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $response = array();

            for ($i = $start; $i < $end; $i++) {
                $user = $comments[$i]->getUser();
                $response[] = array (
                    'photo' => $user->getPhoto(),
                    'username' => $user->getUsername(),
                    'date' => $comments[$i]->getDate(),
                    'content' => $comments[$i]->getContent()
                );
            }

            return new JsonResponse($response);
        }

        return $this->redirectToRoute('home');
    }

}