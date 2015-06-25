<?php

namespace Acme\BlogBundle\Handler;

use Acme\BlogBundle\Model\TagInterface;

interface TagHandlerInterface
{
    /**
     * Get a Tag given the identifier
     *
     * @api
     *
     * @param mixed $id
     *
     * @return TagInterface
     */
    public function get($id);

    /**
     * Get a list of Tags.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Post Tag, creates a new Tag.
     *
     * @api
     *
     * @param array $parameters
     *
     * @return TagInterface
     */
    public function post(array $parameters);

    /**
     * Edit a Tag.
     *
     * @api
     *
     * @param TagInterface   $tag
     * @param array           $parameters
     *
     * @return TagInterface
     */
    public function put(TagInterface $tag, array $parameters);

    /**
     * Partially update a Tag.
     *
     * @api
     *
     * @param TagInterface   $tag
     * @param array           $parameters
     *
     * @return TagInterface
     */
    public function patch(TagInterface $tag, array $parameters);

    /**
     * Delete a Tag.
     *
     * @param TagInterface $tag
     *
     * @return TagInterface
     */
    public function delete(TagInterface $tag);
}