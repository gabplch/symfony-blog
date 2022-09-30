<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'blog_index', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route('/page/{page<[1-9]\d*>}', name: 'blog_index_paginated', methods: ['GET'])]
    public function index(int $page, PostRepository $posts): Response
    {
        $latestPosts = $posts->findLatest($page);

        return $this->render('blog/index.html.twig', [
            'paginator' => $latestPosts,
        ]);
    }

    #[Route('/posts/{slug}', name: 'blog_post', methods: ['GET'])]
    public function postShow(Post $post): Response
    {
        return $this->render('blog/post/show.html.twig', ['post' => $post]);
    }
}
