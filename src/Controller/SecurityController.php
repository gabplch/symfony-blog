<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route('/register', name: 'security_registration')]
    public function registration(Request $request): Response
    {
        $user = $this->getUser();

        if ($user) {
            return $this->redirectToRoute('blog_index');
        }

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $this->userRepository->save($user, true);
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
