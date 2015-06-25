<?php

namespace Acme\BlogBundle\Controller;

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
use Acme\BlogBundle\Form\TagType;
use Acme\BlogBundle\Model\TagInterface;


class TagController extends FOSRestController
{
    /**
     * List all tags.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing tags.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many tags to return.")
     *
     * @Annotations\View(
     *  templateVar="tags"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getTagsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('acme_blog.tag.handler')->all($limit, $offset);
    }

    /**
     * Get single Tag.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Tag for a given id",
     *   output = "Acme\BlogBundle\Entity\Tag",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the tag is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="tag")
     *
     * @param int     $id      the tag id
     *
     * @return array
     *
     * @throws NotFoundHttpException when tag not exist
     */
    public function getTagAction($id)
    {
        $tag = $this->getOr404($id);

        return $tag;
    }

    /**
     * Presents the form to use to create a new tag.
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
    public function newTagAction()
    {
        return $this->createForm(new TagType());
    }

    /**
     * Create a Tag from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new tag from the submitted data.",
     *   input = "Acme\BlogBundle\Form\TagType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AcmeBlogBundle:Tag:newTag.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postTagAction(Request $request)
    {
        try {
            $newTag = $this->container->get('acme_blog.tag.handler')->post(
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $newTag->getId(),
                '_format' => $request->get('_format')
            );

            $this->container->get('acme_blog.mail.handler')->send();

            return $this->routeRedirectView('api_1_get_tag', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing tag from the submitted data or create a new tag at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Acme\DemoBundle\Form\TagType",
     *   statusCodes = {
     *     201 = "Returned when the Tag is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AcmeBlogBundle:Tag:editTag.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the tag id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when tag not exist
     */
    public function putTagAction(Request $request, $id)
    {
        try {
            if (!($tag = $this->container->get('acme_blog.tag.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $tag = $this->container->get('acme_blog.tag.handler')->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $tag = $this->container->get('acme_blog.tag.handler')->put(
                    $tag,
                    $request->request->all()
                );
            }

            $routeOptions = array(
                'id' => $tag->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_tag', $routeOptions, $statusCode);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing tag from the submitted data or create a new tag at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Acme\DemoBundle\Form\TagType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AcmeBlogBundle:Tag:editTag.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the tag id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when tag not exist
     */
    public function patchTagAction(Request $request, $id)
    {
        try {
            $tag = $this->container->get('acme_blog.tag.handler')->patch(
                $this->getOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $tag->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_tag', $routeOptions, Codes::HTTP_NO_CONTENT);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Fetch a Tag or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return TagInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($tag = $this->container->get('acme_blog.tag.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $tag;
    }

    /**
     * Delete a Tag
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "delete a tag.",
     *   input = "Acme\BlogBundle\Form\TagType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AcmeBlogBundle:Tag:deleteTag.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param mixed $id
     *
     * @return FormTypeInterface|View
     */
    public function deleteTagAction($id)
    {
        if (!($tag = $this->container->get('acme_blog.tag.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }
        $result = $this->container->get('acme_blog.tag.handler')->delete($tag);
        if($result)
        {
            return ['message' => 'success'];
        }else{
            return ['message' => 'cannot delete'];
        }
    }
}
