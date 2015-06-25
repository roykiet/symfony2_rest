<?php

namespace Acme\BlogBundle\Tests\Handler;

use Acme\BlogBundle\Handler\TagHandler;
use Acme\BlogBundle\Model\TagInterface;
use Acme\BlogBundle\Entity\Tag;

class TagHandlerTest extends \PHPUnit_Framework_TestCase
{
    const TAG_CLASS = 'Acme\BlogBundle\Tests\Handler\DummyTag';

    /** @var TagHandler */
    protected $tagHandler;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }
        
        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::TAG_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::TAG_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::TAG_CLASS));
    }


    public function testGet()
    {
        $id = 1;
        $tag = $this->getTag();
        $this->repository->expects($this->once())->method('find')
            ->with($this->equalTo($id))
            ->will($this->returnValue($tag));

        $this->tagHandler = $this->createTagHandler($this->om, static::TAG_CLASS,  $this->formFactory);

        $this->tagHandler->get($id);
    }

    public function testAll()
    {
        $offset = 1;
        $limit = 2;

        $tags = $this->getTags(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with(array(), null, $limit, $offset)
            ->will($this->returnValue($tags));

        $this->tagHandler = $this->createTagHandler($this->om, static::TAG_CLASS,  $this->formFactory);

        $all = $this->tagHandler->all($limit, $offset);

        $this->assertEquals($tags, $all);
    }

    public function testPost()
    {
        $name = 'name1';

        $parameters = array('name' => $name);

        $tag = $this->getTag();
        $tag->setName($name);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($tag));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->tagHandler = $this->createTagHandler($this->om, static::TAG_CLASS,  $this->formFactory);
        $tagObject = $this->tagHandler->post($parameters);

        $this->assertEquals($tagObject, $tag);
    }

    /**
     * @expectedException Acme\BlogBundle\Exception\InvalidFormException
     */
    public function testPostShouldRaiseException()
    {
        $name = 'name1';

        $parameters = array('name' => $name);

        $tag = $this->getTag();
        $tag->setName($name);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->tagHandler = $this->createTagHandler($this->om, static::TAG_CLASS,  $this->formFactory);
        $this->tagHandler->post($parameters);
    }

    public function testPut()
    {
        $name = 'name1';

        $parameters = array('name' => $name);

        $tag = $this->getTag();
        $tag->setName($name);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($tag));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->tagHandler = $this->createTagHandler($this->om, static::TAG_CLASS,  $this->formFactory);
        $tagObject = $this->tagHandler->put($tag, $parameters);

        $this->assertEquals($tagObject, $tag);
    }

    public function testPatch()
    {
        $name = 'name1';

        $parameters = array('name' => $name);

        $tag = $this->getTag();
        $tag->setName($name);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($tag));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->tagHandler = $this->createTagHandler($this->om, static::TAG_CLASS,  $this->formFactory);
        $tagObject = $this->tagHandler->patch($tag, $parameters);

        $this->assertEquals($tagObject, $tag);
    }


    protected function createTagHandler($objectManager, $tagClass, $formFactory)
    {
        return new TagHandler($objectManager, $tagClass, $formFactory);
    }

    protected function getTag()
    {
        $tagClass = static::TAG_CLASS;

        return new $tagClass();
    }

    protected function getTags($maxTags = 5)
    {
        $tags = array();
        for($i = 0; $i < $maxTags; $i++) {
            $tags[] = $this->getTag();
        }

        return $tags;
    }
}

class DummyTag extends Tag
{
}
