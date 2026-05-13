<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\Rooms;
use App\Form\Admin\EmployeFormType;
use App\Form\AnnonceFormType;
use App\Form\CreateRoomFormType;
use App\Repository\AnnouncementRepository;
use App\Repository\ReservationRoomRepository;
use App\Repository\RoomsRepository;
use App\Repository\SubjectRepository;
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
            $this->addFlash('error', 'Employé introuvable.');
            return $this->redirectToRoute('app_employes');
        }
        if (!$this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            return $this->redirectToRoute('app_employes');
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
    public function salles(RoomsRepository $RR): Response
    {
        $rooms = $RR->findAll();
        return $this->render('/administration/salles/salles.html.twig',[
            "rooms" => $rooms
        ]);
    }
    #[Route('/salles/edit{id}', name: 'app_edit_salles', methods: ['GET','POST'])]
    public function editSalles(int $id,Request $request, EntityManagerInterface $em): Response
    {
        $room = $em->getRepository(Rooms::class)->find($id);
        $form = $this->createForm(CreateRoomFormType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($room);
            $em->flush();
            $this->addFlash('success', 'Salle modifié avec succès.');
            return $this->redirectToRoute('app_salles');
        }
        return $this->render('/administration/salles/edit_salles.html.twig',[
            "form" => $form->createView(),
        ]);
    }
    #[Route('/salles/create', name: 'app_new_salles', methods: ['GET','POST'])]
    public function newSalles(Request $request, EntityManagerInterface $em): Response
    {
        $room = new Rooms();
        $form = $this->createForm(CreateRoomFormType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($room);
            $em->flush();
            $this->addFlash('success', 'Salle ajoutée avec succès.');
            return $this->redirectToRoute('app_salles');
        }
        return $this->render('/administration/salles/new_salles.html.twig',[
            "form" => $form->createView(),
        ]);
    }
    #[Route('/salles/delete{id}', name: 'app_delete_salles', methods: ['GET','POST'])]
    public function deleteSalles(int $id, Request $request, RoomsRepository $RR, EntityManagerInterface $em): Response
    {
        $rooms = $RR->find($id);
        if (!$rooms) {
            $this->addFlash('error', 'Cette salle n\'existe pas.');
            return $this->redirectToRoute('app_salles');
        }
        if (!$this->isCsrfTokenValid("_token".$id, $request->request->get("danger"))) {

          
        $this->addFlash('error', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_salles');
        }

        $em->remove($rooms);
        $em->flush();
        $this->addFlash('success', 'Salle supprimée avec succès.');
        return $this->redirectToRoute('app_salles');
            
        
    }


    #[Route('/reservations', name: 'app_reservations', methods: ['GET'])]
    public function reservations( ReservationRoomRepository $RRR): Response
    {
        $reservations = $RRR->findAll();
        
        return $this->render('/administration/reservations/reservations.html.twig',[
            "reservations" => $reservations
        ]);
    }
      #[Route('/reservations/delete{id}', name: 'app_delete_reservations', methods: ['GET','POST'])]
    public function deleteReservation(int $id, Request $request, ReservationRoomRepository $RRR, EntityManagerInterface $em): Response
    {
        $reservations = $RRR->find($id);
        if (!$reservations) {
            $this->addFlash('error', 'Cette réservation n\'existe pas.');
            return $this->redirectToRoute('app_reservations');
        }
        if (!$this->isCsrfTokenValid("reservation".$id, $request->request->get("_token"))) {

          
        $this->addFlash('error', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_reservations');
        }

        $em->remove($reservations);
        $em->flush();
        $this->addFlash('success', 'Réservation supprimée avec succès.');
        return $this->redirectToRoute('app_reservations');
            
        
    }

    #[Route('/forum', name: 'app_forum', methods: ['GET'])]
    public function forum(SubjectRepository $SR, AnnouncementRepository $AR): Response
    {
        $annonces = $AR->findAll();
        $privateSubjects = $SR->findBy(['private' => true]);
        $publicSubjects = $SR->findBy(['private' => false]);
        return $this->render('/administration/forum/forum.html.twig',[
            "annonces" => $annonces,
            "publicSubjects" => $publicSubjects,
            "privateSubjects" => $privateSubjects]);
    }
    #[Route('/forum/subject', name: 'app_admin_forum_subject', methods: ['GET'])]
    public function forumSubject(SubjectRepository $SR): Response
    {
        $publicSubjects = $SR->findBy(['private' => false]);
        return $this->render('/administration/forum/subject.html.twig',[
            "publicSubjects" => $publicSubjects
        ]);
    }

    #[Route('/forum/private', name: 'app_admin_forum_private', methods: ['GET'])]
    public function forumPrivate(SubjectRepository $SR): Response
    {
        $privateSubjects = $SR->findBy(['private' => true]);
        return $this->render('/administration/forum/private.html.twig',[
            "privateSubjects" => $privateSubjects
        ]);
    }

    #[Route('/forum/annonce', name: 'app_admin_forum_annonce', methods: ['GET'])]
    public function forumAnnonce(AnnouncementRepository $AR): Response
    {
        $annonces = $AR->findAll();
        return $this->render('/administration/forum/annonce.html.twig',[
            "annonces" => $annonces
        ]);
    }
    #[Route('/forum/annonce/create', name: 'app_admin_forum_new_annonce', methods: ['GET', 'POST'])]
    public function forumNewAnnonce(Request $request, EntityManagerInterface $em): Response
    {
        $annonce = new Announcement();
        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setCreatedAt(new \DateTimeImmutable());
            $annonce->setUser($this->getUser());
            $em->persist($annonce);
            $em->flush();
            $this->addFlash('success', 'Annonce ajoutée avec succès.');
            return $this->redirectToRoute('app_admin_forum_annonce');
        }
        return $this->render('/administration/forum/new_annonce.html.twig',[
            "form" => $form->createView()
        ]);
    }
    #[Route('/equipement', name: 'app_equipement', methods: ['GET'])]
    public function equipement(): Response
    {
        return $this->render('/administration/equipement.html.twig');
    }
}
