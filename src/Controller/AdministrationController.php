<?php

namespace App\Controller;

use App\Enum\ReservationType;
use App\Form\Admin\EmployeFormType;
use App\Repository\ReservationRoomRepository;
use App\Repository\RoomsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/administration')]
final class AdministrationController extends AbstractController
{
    #[Route('/', name: 'app_administration', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_employes');
    }

    #[Route('/employes', name: 'app_employes', methods: ['GET'])]
    public function employes(UserRepository $UR): Response
    {
        $users = $UR->findAll();

        return $this->render('/administration/employes/employes.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/employes/delete/{id}', name: 'app_employes_delete', methods: ['POST'])]
    public function deleteEmploye(int $id, Request $request, UserRepository $UR, EntityManagerInterface $EM): Response
    {
        $user = $UR->find($id);
        if (!$user) {
            throw $this->createNotFoundException();
        }
        if (!$this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            return $this->redirectToRoute('app_employes');
        }
        foreach ($user->getReservationRooms() as $reservationsRoom) {
            $EM->remove($reservationsRoom);
        }
        foreach ($user->getSubjects() as $subject) {
            $EM->remove($subject);
        }
        foreach ($user->getPosts() as $post) {
            $EM->remove($post);
        }
        foreach ($user->getReservationRoomsInvites() as $getReservationRoomsInvites) {
            $EM->remove($getReservationRoomsInvites);
        }

        $EM->remove($user);
        $EM->flush();
        $this->addFlash('success', 'Employé supprimé avec succès.');

        return $this->redirectToRoute('app_employes');
    }

    #[Route('/employes/edit/{id}', name: 'app_employes_edit', methods: ['POST', 'GET'])]
    public function editEmploye(int $id, Request $request, UserRepository $UR, EntityManagerInterface $EM): Response
    {
        $user = $UR->find($id);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(EmployeFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles($form->get('roles')->getData());
            $EM->flush();

            return $this->redirectToRoute('app_employes');
        }

        return $this->render('/administration/employes/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/salles', name: 'app_salles', methods: ['GET'])]
    public function salles(): Response
    {
        return $this->render('/administration/salles.html.twig');
    }

    #[Route('/reservations', name: 'app_reservations', methods: ['GET'])]
    public function reservations(UserRepository $UR, ReservationRoomRepository $RRR, RoomsRepository $RR): Response
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

        $reservationsRooms = $RRR->findAll();
        $reservationRoomsData = array_map(function ($reservationsRoom) {
            return [
                'id' => $reservationsRoom->getId(),
                'room' => $reservationsRoom->getRoom()->getId(),
                'date' => $reservationsRoom->getReservedFor()->format('Y-m-d'),
                'timeStart' => $reservationsRoom->getTimeStart()->format('H:i'),
                'timeEnd' => $reservationsRoom->getTimeEnd()->format('H:i'),
                'user' => $reservationsRoom->getUser()->getId(),
            ];
        }, $reservationsRooms);
        $reservationsType = array_map(fn ($type) => [
            'value' => $type->value,
            'label' => $type->name,
        ], ReservationType::cases());

        return $this->render('/administration/reservations.html.twig', [
            'users' => $usersData,
            'rooms' => $roomsData,
            'reservations' => $reservationRoomsData,
            'reservationsType' => $reservationsType,
            'controller_name' => 'ReserveController',
        ]);
    }

    #[Route('/forum', name: 'app_forum', methods: ['GET'])]
    public function forum(): Response
    {
        return $this->render('/administration/forum.html.twig');
    }

    #[Route('/equipement', name: 'app_equipement', methods: ['GET'])]
    public function equipement(): Response
    {
        return $this->render('/administration/equipement.html.twig');
    }
}
