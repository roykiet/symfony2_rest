<?php

namespace Acme\BlogBundle\Controller;

use Acme\BlogBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;

use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Acme\BlogBundle\Exception\InvalidFormException;
use Acme\BlogBundle\Form\PostType;
use Acme\BlogBundle\Model\PostInterface;


class PostController extends FOSRestController
{
    /**
     * List all posts.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing posts.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many posts to return.")
     *
     * @Annotations\View(
     *  templateVar="posts"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getPostsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('acme_blog.post.handler')->all($limit, $offset);
    }

    /**
     * Get single Post.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Post for a given id",
     *   output = "Acme\BlogBundle\Entity\Post",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the post is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="post")
     *
     * @param int     $id      the post id
     *
     * @return array
     *
     * @throws NotFoundHttpException when post not exist
     */
    public function getPostAction($id)
    {
        $post = $this->getOr404($id);

        return $post;
    }

    /**
     * Presents the form to use to create a new post.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  templateVar = "form"
     * )
     *
     * @return FormTypeInterface
     */
    public function newPostAction()
    {
        return $this->createForm(new PostType());
    }

    /**
     * Create a Post from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new post from the submitted data.",
     *   input = "Acme\BlogBundle\Form\PostType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AcmeBlogBundle:Post:newPost.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postPostAction(Request $request)
    {
        try {
            $newPost = $this->container->get('acme_blog.post.handler')->post(
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $newPost->getId(),
                '_format' => $request->get('_format')
            );
            $this->container->get('acme_blog.mail.handler')->send();

            return $this->routeRedirectView('api_1_get_post', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing post from the submitted data or create a new post at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Acme\DemoBundle\Form\PostType",
     *   statusCodes = {
     *     201 = "Returned when the Post is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AcmeBlogBundle:Post:editPost.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the post id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when post not exist
     */
    public function putPostAction(Request $request, $id)
    {
        try {
            if (!($post = $this->container->get('acme_blog.post.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $post = $this->container->get('acme_blog.post.handler')->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $post = $this->container->get('acme_blog.post.handler')->put(
                    $post,
                    $request->request->all()
                );
            }

            $routeOptions = array(
                'id' => $post->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_post', $routeOptions, $statusCode);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing post from the submitted data or create a new post at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Acme\DemoBundle\Form\PostType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AcmeBlogBundle:Post:editPost.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the post id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when post not exist
     */
    public function patchPostAction(Request $request, $id)
    {
        try {
            $post = $this->container->get('acme_blog.post.handler')->patch(
                $this->getOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $post->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_post', $routeOptions, Codes::HTTP_NO_CONTENT);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Fetch a Post or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return PostInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($post = $this->container->get('acme_blog.post.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $post;
    }

    /**
     * Delete a Post
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "delete a post.",
     *   input = "Acme\BlogBundle\Form\PostType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AcmeBlogBundle:Post:deletePost.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param mixed $id
     *
     * @return FormTypeInterface|View
     */
    public function deletePostAction($id)
    {
        if (!($post = $this->container->get('acme_blog.post.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }
        $result = $this->container->get('acme_blog.post.handler')->delete($post);
        if($result)
        {
            $this->container->get('acme_blog.logger.handler')->info('The post with id '. $id . ' is deleted successfully');
            return ['message' => 'success'];
        }else{
            return ['message' => 'cannot delete'];
        }
    }
    /*
    * @Annotations\View(
    *  template = "AcmeBlogBundle:Post:getPosts.html.twig",
    *  statusCode = Codes::HTTP_BAD_REQUEST,
    *  templateVar = "form"
    * )
    *
    * @param Request $request the request object
    * @param Tag $tag the request object
    *
    * @return FormTypeInterface|View
    */
    public function getTagPostsAction($tagId)
    {
        
    }
}
