<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\UserLike;
use App\Repository\AnnouncementRepository;
use App\Repository\PostRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserLikeRepository;
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
        $subject = $SR->findBy(['private' => false], ['createdAt' => 'DESC']);

        return $this->render('forum/public/public.html.twig',
            [
                'subjects' => $subject,
            ]);
    }
    #[Route('/forum/post/likes/{id}', name: 'app_forum_post_likes', methods: ['POST'])]
    public function postLikes(
        Request $request, 
        int $id, 
        PostRepository $PR, 
        UserLikeRepository $ULR,
        EntityManagerInterface $em): Response
    {
        $post = $PR->findOneBy(['id' => $id]);
        $user=$this->getUser();
        $existing = $ULR->findOneBy(["user" => $user, "post" => $post]);

        if (!$this->isCsrfTokenValid("like_post", $request->request->get('_token'))) {
            $this->addFlash('error', "Une erreur est survenue.");
            return $this->redirectToRoute('app_forum_public');
        }

        if ($existing) {
            $em->remove($existing);
            }
        else{
            $like = new UserLike();
            $like->setUser($user);
            $like->setPost($post);
            $like->setCreatedAt(new \DateTimeImmutable());
            $em->persist($like);
        }
        $em->flush();
        
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_forum_public'));
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
        if (!$subject || $subject->isPrivate() === true) {
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
    #[Route('/forum/subject/likes/{slug}', name: 'app_forum_subject_likes', methods: ['POST'])]
    public function subjectLikes(
        Request $request, 
        string $slug, 
        SubjectRepository $SR, 
        UserLikeRepository $ULR,
        EntityManagerInterface $em): Response
    {
        $subject = $SR->findOneBy(['slug' => $slug]);
        $user=$this->getUser();
        $existing = $ULR->findOneBy(["user" => $user, "subject" => $subject]);

        if (!$this->isCsrfTokenValid("like_subject", $request->request->get('_token'))) {
            $this->addFlash('error', "Une erreur est survenue.");
            return $this->redirectToRoute('app_forum_public');
        }

        if ($existing) {
            $em->remove($existing);
            }
        else{
            $like = new UserLike();
            $like->setUser($user);
            $like->setSubject($subject);
            $like->setCreatedAt(new \DateTimeImmutable());
            $em->persist($like);
        }
        $em->flush();
        
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_forum_public'));
    }

    #[Route('/forum/private', name: 'app_forum_private')]
    public function private(SubjectRepository $SR): Response
    {

        $subjects = $SR->findBy(['private' => true], ['createdAt' => 'DESC']);
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
        if (!$subject || $subject->isPrivate() === false) {
            $this->addFlash('error', "Ce sujet n'existe pas.");

            return $this->redirectToRoute('app_forum_private');
        }
        
        if ($user !== $subject->getUser() && !$subject->getReservation()->getUserInvites()->contains($user)) {
            $this->addFlash('error', "Vous n'êtes pas invité à cette réunion.");
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
    #[Route('/forum/announcements', name: 'app_forum_announcements', methods: ['GET'])]
    public function announcements(AnnouncementRepository $AR): Response
    {
        $annonces = $AR->findBy([],["createdAt" => "DESC"]);

        return $this->render('forum/annonce/announcements.html.twig',[
            "annonces" => $annonces
        ]);
    }
    #[Route('/forum/announcements/likes/{slug}', name: 'app_forum_announcements_likes', methods: ['POST'])]
    public function announcementsLikes(
        Request $request, 
        string $slug, 
        AnnouncementRepository $AR, 
        UserLikeRepository $ULR,
        EntityManagerInterface $em): Response
    {
        $annonce = $AR->findOneBy(['slug' => $slug]);
        $user=$this->getUser();
        $existing = $ULR->findOneBy(["user" => $user, "announcement" => $annonce]);

        if (!$this->isCsrfTokenValid("like_announcement", $request->request->get('_token'))) {
            $this->addFlash('error', "Une erreur est survenue.");
            return $this->redirectToRoute('app_forum_announcements');
        }

        if ($existing) {
            $em->remove($existing);
            }
        else{
            $like = new UserLike();
            $like->setUser($user);
            $like->setAnnouncement($annonce);
            $like->setCreatedAt(new \DateTimeImmutable());
            $em->persist($like);
        }
        $em->flush();
        
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_forum_announcements'));
    }
    #[Route('/forum/annonces/{slug}', name: 'app_forum_annonces_subject', methods: ['GET', 'POST'])]
    public function annoncesSubject( string $slug, AnnouncementRepository $AR): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $annonce = $AR->findOneBy(['slug' => $slug]);

        if (!$annonce) {
            $this->addFlash('error', "Cette annonce n'existe pas.");

            return $this->redirectToRoute('app_forum_announcements');
        }

        return $this->render('forum/annonce/annonce_subject.html.twig', [
            'annonce' => $annonce
        ]);
    }
    #[Route('/forum/test-likes', name: 'app_test_likes')]
    public function testLikes(AnnouncementRepository $AR): Response
    {
        $annonces = $AR->findAll();
        
        return $this->render('test/likes.html.twig', [
            'annonces' => $annonces,
        ]);
    }
}
