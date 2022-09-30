<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/comment/{postSlug}/new', name: 'comment_new', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[ParamConverter('post', options: ['mapping' => ['postSlug' => 'slug']])]
    public function commentNew(Request $request, Post $post, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $post->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('blog_post', ['slug' => $post->getSlug()]);
        }

        return $this->render('blog/post/comment/comment_form_error.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}
