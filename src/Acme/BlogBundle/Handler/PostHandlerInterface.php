<?php

namespace Acme\BlogBundle\Handler;

use Acme\BlogBundle\Model\PostInterface;

interface PostHandlerInterface
{
    /**
     * Get a Post given the identifier
     *
     * @api
     *
     * @param mixed $id
     *
     * @return PostInterface
     */
    public function get($id);

    /**
     * Get a list of Posts.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Post Post, creates a new Post.
     *
     * @api
     *
     * @param array $parameters
     *
     * @return PostInterface
     */
    public function post(array $parameters);

    /**
     * Edit a Post.
     *
     * @api
     *
     * @param PostInterface   $post
     * @param array           $parameters
     *
     * @return PostInterface
     */
    public function put(PostInterface $post, array $parameters);

    /**
     * Partially update a Post.
     *
     * @api
     *
     * @param PostInterface   $post
     * @param array           $parameters
     *
     * @return PostInterface
     */
    public function patch(PostInterface $post, array $parameters);

    /**
     * Delete a Post.
     *
     * @param PostInterface $post
     *
     * @return PostInterface
     */
    public function delete(PostInterface $post);
}