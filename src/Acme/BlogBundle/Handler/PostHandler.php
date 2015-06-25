<?php

namespace Acme\BlogBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Acme\BlogBundle\Model\PostInterface;
use Acme\BlogBundle\Form\PostType;
use Acme\BlogBundle\Exception\InvalidFormException;

class PostHandler implements PostHandlerInterface
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
    }

    /**
     * Get a Post.
     *
     * @param mixed $id
     *
     * @return PostInterface
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get a list of Posts.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * Create a new Post.
     *
     * @param array $parameters
     *
     * @return PostInterface
     */
    public function post(array $parameters)
    {
        $post = $this->createPost();

        return $this->processForm($post, $parameters, 'POST');
    }

    /**
     * Edit a Post.
     *
     * @param PostInterface $post
     * @param array         $parameters
     *
     * @return PostInterface
     */
    public function put(PostInterface $post, array $parameters)
    {
        return $this->processForm($post, $parameters, 'PUT');
    }

    /**
     * Delete a Post.
     *
     * @param PostInterface $post
     *
     * @return PostInterface
     */
    public function delete(PostInterface $post)
    {
        try {
            $this->om->remove($post);
            $this->om->flush($post);
            return true;
        }catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * Partially update a Post.
     *
     * @param PostInterface $post
     * @param array         $parameters
     *
     * @return PostInterface
     */
    public function patch(PostInterface $post, array $parameters)
    {
        return $this->processForm($post, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param PostInterface $post
     * @param array         $parameters
     * @param String        $method
     *
     * @return PostInterface
     *
     * @throws \Acme\BlogBundle\Exception\InvalidFormException
     */
    private function processForm(PostInterface $post, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new PostType(), $post, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {

            $post = $form->getData();
            $this->om->persist($post);
            $this->om->flush($post);

            return $post;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createPost()
    {
        return new $this->entityClass();
    }

}