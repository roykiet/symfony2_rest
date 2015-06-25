<?php
namespace Acme\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
    public function findPostsByTagIds($tagIds, $offset = 0, $limit = 5)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT p, t FROM AcmeBlogBundle:Post p
            JOIN p.tags t
            WHERE t.id in (:ids)'
            )->setParameter('ids', $tagIds)
            ->setMaxResults($limit)
            ->setFirstResult($offset);


        try {
            return $query->getArrayResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function countPostsByTagIds($tagIds)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT p, t FROM AcmeBlogBundle:Post p
            JOIN p.tags t
            WHERE t.id in (:ids)'
            )->setParameter('ids', $tagIds);

        try {
            $posts = $query->getArrayResult();
            return count($posts);
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}