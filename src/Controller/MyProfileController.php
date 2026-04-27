<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_my_profile')]
    public function index(): Response
    {
        return $this->render('my_profile/index.html.twig', [
            'controller_name' => 'MyProfileController',
        ]);
    }
}
