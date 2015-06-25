<?php

namespace Acme\BlogBundle\Tests\Fixtures\Entity;

use Acme\BlogBundle\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadPostData implements FixtureInterface
{
    static public $posts = array();

    public function load(ObjectManager $manager)
    {
        $post = new Post();
        $post->setTitle('title');
        $post->setBody('body');

        $arrayTags = array();
        $post->getTags($arrayTags);

        $manager->persist($post);
        $manager->flush();

        self::$posts[] = $post;
    }
}
