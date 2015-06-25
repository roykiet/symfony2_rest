<?php

namespace Acme\BlogBundle\Tests\Handler;

use Acme\BlogBundle\Handler\PostHandler;
use Acme\BlogBundle\Model\PostInterface;
use Acme\BlogBundle\Entity\Post;

class PostHandlerTest extends \PHPUnit_Framework_TestCase
{
    const POST_CLASS = 'Acme\BlogBundle\Tests\Handler\DummyPost';

    /** @var PostHandler */
    protected $postHandler;
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
            ->with($this->equalTo(static::POST_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::POST_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::POST_CLASS));
    }


    public function testGet()
    {
        $id = 1;
        $post = $this->getPost();
        $this->repository->expects($this->once())->method('find')
            ->with($this->equalTo($id))
            ->will($this->returnValue($post));

        $this->postHandler = $this->createPostHandler($this->om, static::POST_CLASS,  $this->formFactory);

        $this->postHandler->get($id);
    }

    public function testAll()
    {
        $offset = 1;
        $limit = 2;

        $posts = $this->getPosts(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with(array(), null, $limit, $offset)
            ->will($this->returnValue($posts));

        $this->postHandler = $this->createPostHandler($this->om, static::POST_CLASS,  $this->formFactory);

        $all = $this->postHandler->all($limit, $offset);

        $this->assertEquals($posts, $all);
    }

    public function testPost()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $post = $this->getPost();
        $post->setTitle($title);
        $post->setBody($body);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($post));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->postHandler = $this->createPostHandler($this->om, static::POST_CLASS,  $this->formFactory);
        $postObject = $this->postHandler->post($parameters);

        $this->assertEquals($postObject, $post);
    }

    /**
     * @expectedException Acme\BlogBundle\Exception\InvalidFormException
     */
    public function testPostShouldRaiseException()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $post = $this->getPost();
        $post->setTitle($title);
        $post->setBody($body);

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

        $this->postHandler = $this->createPostHandler($this->om, static::POST_CLASS,  $this->formFactory);
        $this->postHandler->post($parameters);
    }

    public function testPut()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $post = $this->getPost();
        $post->setTitle($title);
        $post->setBody($body);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($post));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->postHandler = $this->createPostHandler($this->om, static::POST_CLASS,  $this->formFactory);
        $postObject = $this->postHandler->put($post, $parameters);

        $this->assertEquals($postObject, $post);
    }

    public function testPatch()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('body' => $body);

        $post = $this->getPost();
        $post->setTitle($title);
        $post->setBody($body);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($post));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->postHandler = $this->createPostHandler($this->om, static::POST_CLASS,  $this->formFactory);
        $postObject = $this->postHandler->patch($post, $parameters);

        $this->assertEquals($postObject, $post);
    }


    protected function createPostHandler($objectManager, $postClass, $formFactory)
    {
        return new PostHandler($objectManager, $postClass, $formFactory);
    }

    protected function getPost()
    {
        $postClass = static::POST_CLASS;

        return new $postClass();
    }

    protected function getPosts($maxPosts = 5)
    {
        $posts = array();
        for($i = 0; $i < $maxPosts; $i++) {
            $posts[] = $this->getPost();
        }

        return $posts;
    }
}

class DummyPost extends Post
{
}
