<?php

namespace App\Controller;

use App\Entity\ReservationRoom;
use App\Entity\Subject;
use App\Entity\User;
use App\Enum\ReservationType;
use App\Repository\ReservationRoomRepository;
use App\Repository\RoomsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReserveController extends AbstractController
{
    #[Route('/reservation', name: 'app_reserve')]
    public function index(UserRepository $UR, ReservationRoomRepository $RRR, RoomsRepository $RR): Response
    {
        $users = $UR->findAll();
        $usersData = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'avatar' => $user->getImgProfile(),
            ];
        }, $users);

        $rooms = $RR->findAll();
        $roomsData = array_map(function ($room) {
            return [
                'id' => $room->getId(),
                'roomNumber' => $room->getRoomNumber(),
                'capacity' => $room->getCapacity(),
                'projector' => $room->isProjector(),
                'whiteboard' => $room->isWhiteboard(),
            ];
        }, $rooms);

        $today = new \DateTime('today', new \DateTimeZone('Europe/Paris'));
        $reservationsRooms = $RRR->findByDate($today);

        $reservationRoomsData = array_map(function ($reservationsRoom) {
            return [
                'id' => $reservationsRoom->getId(),
                'roomId' => $reservationsRoom->getRoom()->getId(),
                'date' => $reservationsRoom->getReservedFor()->format('Y-m-d'),
                'timeStart' => $reservationsRoom->getTimeStart()->format('H:i'),
                'timeEnd' => $reservationsRoom->getTimeEnd()->format('H:i'),
                'user' => $reservationsRoom->getUser()->getId(),
                'type' => $reservationsRoom->getType()->value,
                'name' => $reservationsRoom->getName(),   
            ];
        }, $reservationsRooms);

        $reservationsType = array_map(fn ($type) => [
            'value' => $type->value,
            'label' => $type->name,
        ], ReservationType::cases());

        return $this->render('reserve/index.html.twig', [
            'users' => $usersData,
            'rooms' => $roomsData,
            'reservationsRooms' => $reservationRoomsData,
            'reservationsTypes' => $reservationsType,
        ]);
    }

    #[Route('/reservation/create', name: 'app_reservation_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, RoomsRepository $RR, ReservationRoomRepository $RRR): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$this->isCsrfTokenValid('reservation', $request->headers->get('CSRF-Token'))) {
            return $this->json(['error' => 'Invalid CSRF token'], 403);
        }

        $room = $RR->find($data['roomId']);
        $timeStart = new \DateTime($data['timeStart']);
        $timeEnd = new \DateTime($data['timeEnd']);
        $date = new \DateTime('today', new \DateTimeZone('Europe/Paris'));
        $invitedUsers = $data['invitedUsers'];

        $conflict = $RRR->findConflict($room, $timeStart, $timeEnd, $date);
        if ($conflict) {
            return $this->json(['error' => 'Ce créneau est déjà réservé'], 409);
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $reservation = new ReservationRoom();
        $reservation->setUser($user);
        $reservation->setRoom($RR->find($data['roomId']));
        $reservation->setReservedFor($date);
        $reservation->setTimeStart(new \DateTime($data['timeStart']));
        $reservation->setName($data['name']);
        $reservation->setTimeEnd(new \DateTime($data['timeEnd']));
        $reservation->setType(ReservationType::from($data['type']));
        $reservation->setCreatedAt(new \DateTimeImmutable());

        foreach ($invitedUsers as $invitedUser) {
            $guest = $em->getRepository(User::class)->find($invitedUser);
            $reservation->addUserInvite($guest);
        }

        $reunion = new Subject();
        $reunion->setPrivate(true);
        $reunion->setTitle($data['name']);
        $reunion->setReservation($reservation);
        $reunion->setCreatedAt(new \DateTimeImmutable());
        $reunion->setUser($user);
        
        $em->persist($reservation);
        $em->persist($reunion);

        $em->flush();

        return $this->json(['success' => true]);
    }
}
