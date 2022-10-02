<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\Role;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/user/{user_id}/prove_to_admin', name: 'user_prove')]
    #[Entity('user', expr: 'repository.find(user_id)')]
    public function prove(User $user): Response
    {
        $user->addRole(Role::ADMIN);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('blog_admin');
    }

    #[Route('/user/{user_id}/refuse_to_user', name: 'user_refuse')]
    #[Entity('user', expr: 'repository.find(user_id)')]
    public function refuse(User $user): Response
    {
        $roles = array_flip($user->getRoles());
        unset($roles[Role::ADMIN]);
        $user->setRoles(array_flip($roles));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('blog_admin');
    }

    #[Route('/user/{user_id}/delete', name: 'user_delete')]
    #[Entity('user', expr: 'repository.find(user_id)')]
    public function delete(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('blog_admin');
    }
}
