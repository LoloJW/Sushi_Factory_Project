<?php

namespace App\Controller;

use App\Repository\AnnouncementRepository;
use App\Repository\ReservationRoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WelcomeController extends AbstractController
{
    #[Route('/', name: 'app_redirect_login_welcome')] // La page welcome par défaut sera app_login, et ensuite en fonction de si l'utilisateur est connecté
    public function redirectLoginWelcome(): Response // ou non, il redirigera vers la page welcome
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/welcome', name: 'app_welcome')]
    public function index(AnnouncementRepository $AR,ReservationRoomRepository $RR): Response
    {
        $user = $this->getUser();
        $reservations = $RR->findReservationsWhereUserIsInvited($user);
        $annonces = $AR->findBy([],["createdAt" => "DESC"], 2);

        return $this->render('welcome/index.html.twig',[
            "annonces" => $annonces,
            "reservations" => $reservations
        ]);
    }
}
