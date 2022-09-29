<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
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
