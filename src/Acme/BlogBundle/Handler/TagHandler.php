<?php

namespace Acme\BlogBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Acme\BlogBundle\Model\TagInterface;
use Acme\BlogBundle\Form\TagType;
use Acme\BlogBundle\Exception\InvalidFormException;

class TagHandler implements TagHandlerInterface
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
     * Get a Tag.
     *
     * @param mixed $id
     *
     * @return TagInterface
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get a list of Tags.
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
     * Create a new Tag.
     *
     * @param array $parameters
     *
     * @return TagInterface
     */
    public function post(array $parameters)
    {
        $tag = $this->createTag();

        return $this->processForm($tag, $parameters, 'POST');
    }

    /**
     * Edit a Tag.
     *
     * @param TagInterface $tag
     * @param array         $parameters
     *
     * @return TagInterface
     */
    public function put(TagInterface $tag, array $parameters)
    {
        return $this->processForm($tag, $parameters, 'PUT');
    }

    /**
     * Delete a Tag.
     *
     * @param TagInterface $tag
     *
     * @return TagInterface
     */
    public function delete(TagInterface $tag)
    {
        try {
            $this->om->remove($tag);
            $this->om->flush($tag);
            return true;
        }catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * Partially update a Tag.
     *
     * @param TagInterface $tag
     * @param array         $parameters
     *
     * @return TagInterface
     */
    public function patch(TagInterface $tag, array $parameters)
    {
        return $this->processForm($tag, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param TagInterface $tag
     * @param array         $parameters
     * @param String        $method
     *
     * @return TagInterface
     *
     * @throws \Acme\BlogBundle\Exception\InvalidFormException
     */
    private function processForm(TagInterface $tag, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new TagType(), $tag, array('method' => $method,'csrf_protection' => false));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {

            $tag = $form->getData();
            $this->om->persist($tag);
            $this->om->flush($tag);

            return $tag;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createTag()
    {
        return new $this->entityClass();
    }

}