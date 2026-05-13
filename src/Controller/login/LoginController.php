<?php

namespace App\Controller\login;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/auth')]
final class LoginController extends AbstractController
{
    #[Route('/', name: 'app_redirect_login')]
    public function redirect_login(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $UA): Response
    {       // AuthenticationUtils c'est ça qui récupère en session les informations de la dernière tentative de connexion/authentification
        // En l'occurence il récupère le dernier email entré et le dernier message d'erreur
        if ($this->getUser()) {
            return $this->redirectToRoute('app_welcome');
        }

        return $this->render('login/index.html.twig', [
            'error' => $UA->getLastAuthenticationError(),
            'last_email' => $UA->getLastUsername(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Si ce message est vu il y a un problème');
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        EntityManagerInterface $em,
        Request $request,
        UserPasswordHasherInterface $hasher,
        EmailVerifier $emailVerifier): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_welcome');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Comme le mot de passe est un RepeatedType, on fait un autre get pour accéder au premier input mdp.
            $password = $form->get('password')->get('first')->getData();

            $user->setPassword($hasher->hashPassword($user, $password));
            $user->setjoinedAt(new \DateTimeImmutable());
            $user->setIsVerified(false);
            $user->setRoles(['ROLE_USER']);

            $em->persist($user);
            $em->flush();

            $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('SushiFactory@SF.com', 'MrSakana'))
                    ->to($user->getEmail())
                    ->subject('Veuillez verifier votre email')
                    ->htmlTemplate('login/confirmation_email.html.twig')
            );
            $this->addFlash('info', 'Un lien vous a été envoyé sur votre email.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, EmailVerifier $emailVerifier, UserRepository $UR): Response
    {
        $id = $request->query->get('id');
        if (!$id) {
            return $this->redirectToRoute('app_login'); // Ce sont des if de protection, ils sont là pour vérifier que l'id et le user existent.
        }
        $user = $UR->find($id);
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Si quelqu'un essaye d'y accéder sans ID ou avec un ID qui n'est lié à aucun user ça renvoit sur la login page sans erreur
        }

        try {
            $emailVerifier->handleEmailConfirmation($request, $user); // en temps normal $this->getUser() suffit si l'utilisateur est connecté, mais comme l'utilisateur n'est pas connecté on cherche le user par son ID
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('success', 'Votre email a été confirmé.');

        return $this->redirectToRoute('app_login');
    }
}
