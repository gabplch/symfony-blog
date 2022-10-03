<?php

namespace App\Repository;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 *
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function save(Like $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Like $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    function getUserMark(Post $post, User $user): ?Like
    {
        $qb = $this->createQueryBuilder('_like');

        $qb
            ->innerJoin('_like.post', 'post')
            ->innerJoin('_like.user', 'user')
            ->where('post.id = :post_id')
            ->andWhere('user.id = :user_id')
            ->setParameter('post_id', $post->getId())
            ->setParameter('user_id', $user->getId())
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
