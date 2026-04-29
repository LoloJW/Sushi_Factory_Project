<?php

namespace App\Controller;

use App\Entity\User;
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

#[Route('/profile/change-password', name: 'app_change_password', methods: ['GET'])]
public function changePasswordForm(): Response
{
    return $this->render('my_profile/passwordChange.html.twig');
}
#[Route('/profile/change-password/submit', name: 'app_change_password_submit', methods: ['POST'])]
public function changePassword(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em ): Response
{
    $user = $this->getUser();
    if (!$user instanceof User) {
        throw $this->createAccessDeniedException();
    }
    $oldPassword = $request->request->get('old_password');
    $newPassword = $request->request->get('new_password');
    $confirmPassword = $request->request->get('confirm_password');

    if (!$hasher->isPasswordValid($user, $oldPassword)) {
        $this->addFlash('error_password', 'Ancien mot de passe incorrect.');
        return $this->redirectToRoute('app_change_password');
    }

    if ($newPassword !== $confirmPassword) {
        $this->addFlash('error_password', 'Les mots de passe ne correspondent pas.');
        return $this->redirectToRoute('app_change_password');
    }

    $user->setPassword($hasher->hashPassword($user, $newPassword));
    $em->flush();

    $this->addFlash('success_password', 'Mot de passe modifié avec succès.');
    return $this->redirectToRoute('app_my_profile');
}

#[Route('/profile/change-email', name: 'app_change_email', methods: ['POST', 'GET'])]
public function changeEmail(Request $request, EntityManagerInterface $em, UserRepository $UR): Response
{
    $user = $this->getUser();
    if (!$user instanceof User) {
        throw $this->createAccessDeniedException();
    }
    $newEmail = $request->request->get('email');

    $existing = $UR->findOneBy(['email' => $newEmail]);
    if ($existing) {
        $this->addFlash('error_email', 'Cet email est déjà utilisé.');
        return $this->redirectToRoute('app_my_profile');
    }

    $user->setEmail($newEmail);
    $em->flush();

    $this->addFlash('success_email', 'Email modifié avec succès.');
    return $this->redirectToRoute('app_my_profile');
}
}
