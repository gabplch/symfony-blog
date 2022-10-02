<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'blog_admin')]
    public function index(PostRepository $postRepository, UserRepository $userRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'posts' => $postRepository->findAll(),
            'users' => $userRepository->findAll(),
        ]);
    }
}
