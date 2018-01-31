<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use AppBundle\Form\Type\TagType;
use AppBundle\Entity\Tag;

/**
 * TagController short summary.
 *
 * TagController description.
 *
 * @version 1.0
 * @author Maël Le Goff
 */
class TagController extends Controller {

    /**
     * All Tags
     * @Rest\View(serializerGroups={"tag"})
     * @Rest\Get("/tags")
     * @param Request $request
     * @return mixed
     */
    public function getTagsAction(Request $request) {
        $tags = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Tag')
                ->findAll();
        /* @var $tags Tag[] */

        return $tags;
    }

    /**
     * Tag with the specified id
     * @Rest\View(serializerGroups={"tag"})
     * @Rest\Get("/tags/{id}")
     * @param Request $request
     * @return mixed
     */
    public function getTagAction(Request $request) {
        $tag = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Tag')
                ->find($request->get('id'));
        /* @var $tag Tag */

        if (empty($tag)) {
            return $this->tagNotFound();
        }

        return $tag;
    }

    /**
     * All Tags of the Image
     * @Rest\View(serializerGroups={"tag"})
     * @Rest\Get("/images/{id}/tags")
     * @param Request $request
     * @return mixed
     */
    public function getImagesTagsAction(Request $request) {
        $image = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->find($request->get('id'));
        /* @var $image Image */

        if (empty($image)) {
            return $this->imageNotFound();
        }

        $tags = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Tag')
                ->createQueryBuilder('t')
                ->join('t.images', 'i')
                ->where('i.id = :id_img')
                ->setParameter('id_img', $request->get('id'))
                ->getQuery()->getResult();
        /* @var $tags Tag[] */

        return $tags;
    }

    /**
     * Insert new Tag
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"tag"})
     * @Rest\Post("/tags")
     * @param Request $request
     * @return \AppBundle\Entity\Tag|\AppBundle\Form\Type\TagType
     */
    public function postTagAction(Request $request) {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);

        $form->submit($request->request->all()); // Data validation

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($tag);
            $em->flush();
            return $tag;
        } else {
            return $form;
        }
    }

    /**
     * Full Update Tag with the specified id
     * @Rest\View(serializerGroups={"tag"})
     * @Rest\Put("/tags/{id}")
     */
    public function putTagAction(Request $request) {
        return $this->updateTag($request, true);
    }

    /**
     * Partial Update Tag with the specified id
     * @Rest\View(serializerGroups={"tag"})
     * @Rest\Patch("/tags/{id}")
     * @param Request $request
     * @return mixed
     */
    public function patchTagAction(Request $request) {
        return $this->updateTag($request, false);
    }

    /**
     * Complete or Partial Update Tag with the specified id
     * @param Request $request
     * @param mixed $clearMissing complete or partial
     * @return mixed
     */
    private function updateTag(Request $request, $clearMissing) {
        $tag = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Tag')
                ->find($request->get('id'));
        /* @var $tag Tag */

        if (empty($tag)) {
            return $this->tagNotFound();
        }

        $form = $this->createForm(TagType::class, $tag);

        // The false parameter tells Symfony
        // to keep the values in our entity
        // if the user does not supply one in a query
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($tag);
            $em->flush();
            return $tag;
        } else {
            return $form;
        }
    }

    /**
     * Delete Tag with the specified id
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"tag"})
     * @Rest\Delete("/tags/{id}")
     * @param Request $request
     */
    public function removeTagAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $tag = $em->getRepository('AppBundle:Tag')
                  ->find($request->get('id'));
        /* @var $tag Tag */

        if($tag) {
            $em->remove($tag);
            $em->flush();
        }
    }

    /**
     * Message 404 Tag not found
     * @return View
     */
    private function tagNotFound() {
        return \FOS\RestBundle\View\View::create(['message' => 'Tag not found'], Response::HTTP_NOT_FOUND);
    }
}