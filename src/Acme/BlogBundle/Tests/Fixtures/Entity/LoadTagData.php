<?php

namespace Acme\BlogBundle\Tests\Fixtures\Entity;

use Acme\BlogBundle\Entity\Tag;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadTagData implements FixtureInterface
{
    static public $tags = array();

    public function load(ObjectManager $manager)
    {
        $tag = new Tag();
        $tag->setName('name');

        $manager->persist($tag);
        $manager->flush();

        self::$tags[] = $tag;
    }
}
