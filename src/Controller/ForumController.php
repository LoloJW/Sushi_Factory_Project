<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum_public')]
    public function public(): Response
    {
        return $this->render('forum/public.html.twig', [
            'controller_name' => 'ForumController',
        ]);
    }

    #[Route('/forum/private', name: 'app_forum_private')]
    public function private(): Response
    {
        return $this->render('forum/private.html.twig', [
            'controller_name' => 'ForumController',
        ]);
    }

    #[Route('/forum/announcements', name: 'app_forum_announcements')]
    public function announcements(): Response
    {
        return $this->render('forum/announcements.html.twig', [
            'controller_name' => 'ForumController',
        ]);
    }
}
