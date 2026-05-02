<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class MyProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_my_profile')]
    public function index(): Response
    {
        
        return $this->render('my_profile/index.html.twig');
    }

    #[Route('/profile/change-password', name: 'app_change_password', methods: ['GET', 'POST'])]
    public function changePasswordForm( Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            if (!$hasher->isPasswordValid($user,$oldPassword)) {
                $this->addflash('error', 'Mot de passe entré incorrect.');
                return $this->redirectToRoute('app_change_password');
            }
            $user->setPassword($hasher->hashPassword($user, $form->get('password')->getData()));
            $em->flush();
            $this->addflash('success_password', 'Mot de passe modifié avec succès.');
            return $this->redirectToRoute('app_my_profile');
        }
        return $this->render('my_profile/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/change-email', name: 'app_change_email', methods: ['POST', 'GET'])]
    public function changeEmail(Request $request, EntityManagerInterface $em, UserRepository $UR): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        
        if($request->isMethod("POST")){
            $newMail = $request->request->get('email');
            $existingMail = $UR->findOneBy(['email' => $newMail]);
            if ($existingMail) {
                $this->addFlash('error_email', 'Cet email est deja utilisé.');
                return $this->redirectToRoute('app_change_email');
            }
            $user->setEmail($newMail);
            $em->flush();
    
            $this->addFlash('success_email', 'Email modifié avec succès.');
            return $this->redirectToRoute('app_my_profile');
        }

        return $this->render('my_profile/change_email.html.twig');
    }
}
