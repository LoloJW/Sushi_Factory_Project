<?php

namespace App\Controller;

use App\Enum\ReservationType;
use App\Repository\ReservationRoomRepository;
use App\Repository\RoomsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReserveController extends AbstractController
{
    #[Route('/reservation', name: 'app_reserve')]
    public function index(UserRepository $UR , ReservationRoomRepository $RRR, RoomsRepository $RR): Response
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

        return $this->render('reserve/index.html.twig', [
            "users" => $usersData,
            "rooms" => $roomsData,
            "reservationsRooms" => $reservationRoomsData,
            "reservationsTypes" => $reservationsType
        ]);
    }
}
