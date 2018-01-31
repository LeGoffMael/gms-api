<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use AppBundle\Form\Type\ImageType;
use AppBundle\Entity\Image;

/**
 * ImageController short summary.
 *
 * ImageController description.
 *
 * @version 1.0
 * @author Maël Le Goff
 */
class ImageController extends Controller {

    /*********************** GET ***********************/
    /**
     * All Images
     * @Rest\View(serializerGroups={"image"})
     * @Rest\Get("/images")
     * @param Request $request
     * @return mixed
     */
    public function getImagesAction(Request $request) {
        $images = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->findAll();
        /* @var $images Image[] */

        return $images;
    }

    /**
     * Image with the specified id
     * @Rest\View(serializerGroups={"image"})
     * @Rest\Get("/images/{id}")
     * @param Request $request
     * @return mixed
     */
    public function getImageAction(Request $request) {
        $image = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->find($request->get('id'));
        /* @var $image Image */

        if (empty($image)) {
            return $this->imageNotFound();
        }

        return $image;
    }

    /**
     * All Images of the Category
     * @Rest\View(serializerGroups={"image"})
     * @Rest\Get("/categories/{id}/images")
     * @param Request $request
     * @return mixed
     */
    public function getCategoriesImagesAction(Request $request) {
        $category = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->find($request->get('id'));
        /* @var $category Category */

        if (empty($category)) {
            return $this->categoryNotFound();
        }

        $images = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->createQueryBuilder('i')
                ->join('i.categories', 'c')
                ->where('c.id = :id_categ')
                ->setParameter('id_categ', $request->get('id'))
                ->getQuery()->getResult();
        /* @var $images Image[] */

        return $images;
    }

    /**
     * All Images of the Tag
     * @Rest\View(serializerGroups={"image"})
     * @Rest\Get("/tags/{id}/images")
     * @param Request $request
     * @return mixed
     */
    public function getTagImagesAction(Request $request) {
        $tag = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Tag')
                ->find($request->get('id'));
        /* @var $tag Tag */

        if (empty($tag)) {
            return $this->tagNotFound();
        }

        $images = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->createQueryBuilder('i')
                ->join('i.tags', 't')
                ->where('t.id = :id_tag')
                ->setParameter('id_tag', $request->get('id'))
                ->getQuery()->getResult();
        /* @var $images Image[] */

        return $images;
    }

    /*********************** POST ***********************/
    /**
     * Insert new Image
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"image"})
     * @Rest\Post("/images")
     * @param Request $request
     */
    public function postImageAction(Request $request) {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        $data = $request->request->all();
        //Value by default
        $data['score'] = 0;
        $today = new \DateTime();
        $data['date'] = $today->format('Y-m-d H:i:s');
        //TODO Add categories
        if(array_key_exists('categories', $data)) {
            $idCategories = explode(',',$data['categories']);
            foreach($idCategories as $idCategory) {
                $category = $this->get('doctrine.orm.entity_manager')
                                 ->getRepository('AppBundle:Category')
                                 ->find($idCategory);
                if (empty($category)) {
                    return \FOS\RestBundle\View\View::create(['message' => 'Category '.$idCategory.' not found'], Response::HTTP_NOT_FOUND);
                }
                $image->addCategory($category);
            }
            unset($data['categories']);
        }
        //TODO Add tags
        if(array_key_exists('tags', $data)) {
            $idTags = explode(',',$data['tags']);
            foreach($idTags as $idTag) {
                $tag = $this->get('doctrine.orm.entity_manager')
                                 ->getRepository('AppBundle:Tag')
                                 ->find($idTag);
                if (empty($tag)) {
                    return \FOS\RestBundle\View\View::create(['message' => 'Tag '.$idTag.' not found'], Response::HTTP_NOT_FOUND);
                }
                $image->addTag($tag);
            }
            unset($data['tags']);
        }
        $form->submit($data); // Data validation

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($image);
            $em->flush();
            return $image;
        } else {
            return $form;
        }
    }

    /*********************** PUT ***********************/
    /**
     * Full Update Image with the specified id
     * @Rest\View(serializerGroups={"image"})
     * @Rest\Put("/images/{id}")
     */
    public function putImageAction(Request $request) {
        return $this->updateImage($request, true);
    }

    /*********************** PATCH ***********************/
    /**
     * Partial Update Image with the specified id
     * @Rest\View(serializerGroups={"image"})
     * @Rest\Patch("/images/{id}")
     * @param Request $request
     * @return mixed
     */
    public function patchImageAction(Request $request) {
        return $this->updateImage($request, false);
    }

    /**
     * Complete or Partial Update Image with the specified id
     * @param Request $request
     * @param mixed $clearMissing complete or partial
     * @return mixed
     */
    private function updateImage(Request $request, $clearMissing) {
        $image = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->find($request->get('id'));
        /* @var $image Image */

        if (empty($image)) {
            return $this->imageNotFound();
        }

        $form = $this->createForm(ImageType::class, $image);

        // The false parameter tells Symfony
        // to keep the values in our entity
        // if the user does not supply one in a query
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($image);
            $em->flush();
            return $image;
        } else {
            return $form;
        }
    }

    /*********************** DELETE ***********************/
    /**
     * Delete Image with the specified id
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"image"})
     * @Rest\Delete("/images/{id}")
     * @param Request $request
     */
    public function removeImageAction(Request $request) {
        $em = $this->get('doctrine.orm.entity_manager');
        $image = $em->getRepository('AppBundle:Image')
                    ->find($request->get('id'));
        /* @var $image Image */

        if($image) {
            foreach ($image->getCategories() as $category) {
                $image->removeCategory($category);
            }
            foreach ($image->getCategories() as $tag) {
                $image->removeTag($tag);
            }
            $em->remove($image);
            $em->flush();
        }
    }

    /**
     * Message 404 Image not found
     * @return View
     */
    private function imageNotFound() {
        return \FOS\RestBundle\View\View::create(['message' => 'Image not found'], Response::HTTP_NOT_FOUND);
    }
}