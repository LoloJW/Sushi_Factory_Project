<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\ReservationRoomRepository;
use App\Repository\RoomsRepository;
use App\Enum\ReservationType;


#[Route('/administration')]
final class AdministrationController extends AbstractController
{   
    #[Route('/', name: 'app_administration',methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_employes');
    }
    #[Route('/employes', name: 'app_employes',methods: ['GET'])]
    public function employes(): Response
    {
        return $this->render('/administration/employes.html.twig');
    }
    #[Route('/salles', name: 'app_salles',methods: ['GET'])]
    public function salles(): Response
    {
        return $this->render('/administration/salles.html.twig');
    }
    #[Route('/reservations', name: 'app_reservations',methods: ['GET'])]
    public function reservations(UserRepository $UR , ReservationRoomRepository $RRR, RoomsRepository $RR): Response
    {
        $users = $UR->findAll();
        $usersData = array_map(function($user){
            return[
                "id" => $user->getId(),
                "firstName" => $user->getFirstName(),
                "lastName" => $user->getLastName(),
                "avatar" => $user->getImgProfile(),
            ];
        }, $users);

        $rooms = $RR->findAll();
        $roomsData = array_map(function($room){
            return[
                "id" => $room->getId(),
                "roomNumber" => $room->getRoomNumber(),
                "capacity" => $room->getCapacity(),
                "projector" => $room->isProjector(),
                "whiteboard" => $room->isWhiteboard(),
            ];
        }, $rooms);

        $reservationsRooms = $RRR->findAll();
        $reservationRoomsData = array_map(function($reservationsRoom)
        {
            return[
                "id" => $reservationsRoom->getId(),
                "room" => $reservationsRoom->getRoom()->getId(),
                "date" => $reservationsRoom->getReservedFor()->format("Y-m-d"),
                "timeStart" => $reservationsRoom->getTimeStart()->format("H:i"),
                "timeEnd" => $reservationsRoom->getTimeEnd()->format("H:i"),
                "user" => $reservationsRoom->getUser()->getId(),
            ];
        }, $reservationsRooms);
        $reservationsType = array_map(fn($type) => [
            'value' => $type->value,
            'label' => $type->name,
            ], ReservationType::cases());

        return $this->render('/administration/reservations.html.twig', [
            "users" => $usersData,
            "rooms" => $roomsData,
            "reservations" => $reservationRoomsData,
            "reservationsType" => $reservationsType,
            'controller_name' => 'ReserveController',
        ]);
    }
    #[Route('/forum', name: 'app_forum',methods: ['GET'])]
    public function forum(): Response
    {
        return $this->render('/administration/forum.html.twig');
    }
    #[Route('/equipement', name: 'app_equipement',methods: ['GET'])]
    public function equipement(): Response
    {
        return $this->render('/administration/equipement.html.twig');
    }
}
