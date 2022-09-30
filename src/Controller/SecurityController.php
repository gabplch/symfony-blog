<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Security\SecurityAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route('/register', name: 'security_registration')]
    public function registration(Request $request, UserAuthenticatorInterface $authenticator, SecurityAuthenticator $formAuthenticator, UserPasswordHasherInterface $hasher): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('blog_index');
        }

        $form = $this->createForm(RegistrationType::class, new User());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->addRole('ROLE_USER');
            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));

            $this->userRepository->save($user, true);

            $authenticator->authenticateUser($user, $formAuthenticator, $request);
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'security_login')]
    public function login(AuthenticationUtils $authHelper): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('blog_index');
        }

        return $this->render('security/login.html.twig', [
            'username' => $authHelper->getLastUsername(),
            'error' => $authHelper->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'security_logout')]
    public function logout(): void
    {
    }
}
