<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_my_profile')]
    public function index(UserRepository $UR): Response
    {
        $this->getUser();
        
        return $this->render('my_profile/index.html.twig');
    }
}
