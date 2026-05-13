<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Subject;
use App\Entity\User;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum_public', methods: ['GET'])]
    public function public(SubjectRepository $SR): Response
    {
        $subject = $SR->findBy(['private' => false]);

        return $this->render('forum/public/public.html.twig',
            [
                'subjects' => $subject,
            ]);
    }

    #[Route('/forum/new_subject', name: 'app_forum_public_new_subject', methods: ['GET', 'POST'])]
    public function newPublicSubject(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('new_subject', $request->request->get('_token'))) {

                $post_content = $request->request->get('title');
                $subject_content = $request->request->get('content');

                if (empty($post_content) || empty($subject_content)) {
                    $this->addFlash('error', 'Les champs ne peuvent être vide');
                    return $this->redirectToRoute('app_forum_public_new_subject');
                }

                $subject = new Subject();
                $subject->setTitle($request->request->get('title'));
                $subject->setUser($user);
                $subject->setCreatedAt(new \DateTimeImmutable());
                $subject->setPrivate(false);

                $firstPost = new Post();
                $firstPost->setContent($request->request->get('content'));
                $firstPost->setUser($user);
                $firstPost->setCreatedAt(new \DateTimeImmutable());

                $subject->addPost($firstPost);
                $em->persist($subject);
                $em->persist($firstPost);
                $em->flush();

                return $this->redirectToRoute('app_forum_public_subject', ['slug' => $subject->getSlug()]);
            }
        }

        return $this->render('forum/public/new_subject.html.twig');
    }

    #[Route('/forum/subject/{slug}', name: 'app_forum_public_subject', methods: ['GET', 'POST'])]
    public function publicSubject(EntityManagerInterface $em, Request $request, string $slug, SubjectRepository $SR): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $subject = $SR->findOneBy(['slug' => $slug]);
        if (!$subject) {
            $this->addFlash('error', "Ce sujet n'existe pas.");

            return $this->redirectToRoute('app_forum_public');
        }
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('new_post', $request->request->get('_token'))) {
               
                $post_content= $request->request->get('_post');
                if (empty($post_content)) {
                    $this->addFlash('error', 'Le message ne peut pas être vide');
                    return $this->redirectToRoute('app_forum_public_subject', ['slug' => $subject->getSlug()]);
                }
                $post = new Post();
                $post->setContent($request->request->get('_post'));
                $post->setUser($user);
                $post->setCreatedAt(new \DateTimeImmutable());

                $post->setSubject($subject);
                $em->persist($post);
                $em->flush();

                return $this->redirectToRoute('app_forum_public_subject', ['slug' => $subject->getSlug()]);
            }
        }

        return $this->render('forum/public/subject.html.twig', [
            'subject' => $subject,
        ]);
    }

    #[Route('/forum/private', name: 'app_forum_private')]
    public function private(SubjectRepository $SR): Response
    {
        $subjects = $SR->findBy(['private' => true]);
        return $this->render('forum/private/private.html.twig',[
            "subjects" => $subjects
        ]);
    }
        #[Route('/forum/private/{slug}', name: 'app_forum_private_subject', methods: ['GET', 'POST'])]
    public function privateSubject(EntityManagerInterface $em, Request $request, string $slug, SubjectRepository $SR): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $subject = $SR->findOneBy(['slug' => $slug]);

        if ($user != $subject->getUser() && !$subject->getReservation()->getUserInvites()->contains($user)) {
            $this->addFlash('error', "Vous n'êtes pas invité à cette réunion.");
            return $this->redirectToRoute('app_forum_private');
        }
        if (!$subject) {
            $this->addFlash('error', "Ce sujet n'existe pas.");

            return $this->redirectToRoute('app_forum_private');
        }
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('new_post', $request->request->get('_token'))) {
               
                $post_content= $request->request->get('_post');
                if (empty($post_content)) {
                    $this->addFlash('error', 'Le message ne peut pas être vide');
                    return $this->redirectToRoute('app_forum_private_subject', ['slug' => $subject->getSlug()]);
                }
                $post = new Post();
                $post->setContent($request->request->get('_post'));
                $post->setUser($user);
                $post->setCreatedAt(new \DateTimeImmutable());

                $post->setSubject($subject);
                $em->persist($post);
                $em->flush();

                return $this->redirectToRoute('app_forum_private_subject', ['slug' => $subject->getSlug()]);
            }
        }

        $userInvites = $subject->getReservation()->getUserInvites();

        return $this->render('forum/private/private_subject.html.twig', [
            'subject' => $subject,
            'userInvites' => $userInvites
        ]);
    }

    #[Route('/forum/announcements', name: 'app_forum_announcements')]
    public function announcements(): Response
    {
        return $this->render('forum/announcements.html.twig');
    }
}
