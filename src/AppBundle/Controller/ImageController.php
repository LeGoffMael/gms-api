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
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Category not found');
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
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Tag not found');
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

    /**
     * All Images of the User
     * @Rest\View(serializerGroups={"image"})
     * @Rest\Get("/users/{id}/images")
     * @param Request $request
     * @return mixed
     */
    public function getUserImagesAction(Request $request) {
        $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User not found');
        }

        $images = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->createQueryBuilder('i')
                ->where('i.user = :id_user')
                ->setParameter('id_user', $request->get('id'))
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
        $today = new \DateTime();
        $data['date'] = $today->format('Y-m-d H:i:s');
        // Add Categories
        if(array_key_exists('categories', $data)) {
            foreach($data['categories'] as $idCategory) {
                $category = $this->get('doctrine.orm.entity_manager')
                                 ->getRepository('AppBundle:Category')
                                 ->find($idCategory);
                if (empty($category)) {
                    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Category '.$idCategory.' not found');
                }
                $image->addCategory($category); //Remove to validation
            }
            unset($data['categories']);
        }
        // Add Tags
        if(array_key_exists('tags', $data)) {
            foreach($data['tags'] as $idTag) {
                $tag = $this->get('doctrine.orm.entity_manager')
                                 ->getRepository('AppBundle:Tag')
                                 ->find($idTag);
                if (empty($tag)) {
                    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Tag '.$idTag.' not found');
                }
                $image->addTag($tag);
            }
            unset($data['tags']); //Remove to validation
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

        $data = $request->request->all();
        //Value by default
        unset($data['date']);
        // Update Categories
        if(array_key_exists('categories', $data)) {
            // Remove all Categories
            foreach ($image->getCategories() as $category) {
                $image->removeCategory($category);
            }
            // Add all Categories
            foreach($data['categories'] as $idCategory) {
                $category = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->find($idCategory);
                if (empty($category)) {
                    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Category '.$idCategory.' not found');
                }
                $image->addCategory($category); //Remove to validation
            }
            unset($data['categories']);
        }
        // Update Tags
        if(array_key_exists('tags', $data)) {
            // Remove all Tags
            foreach ($image->getTags() as $tag) {
                $image->removeTag($tag);
            }
            // Add all Tags
            foreach($data['tags'] as $idTag) {
                $tag = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Tag')
                ->find($idTag);
                if (empty($tag)) {
                    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Tag '.$idTag.' not found');
                }
                $image->addTag($tag);
            }
            unset($data['tags']); //Remove to validation
        }

        // The false parameter tells Symfony
        // to keep the values in our entity
        // if the user does not supply one in a query
        $form->submit($data, $clearMissing);

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
            foreach ($image->getTags() as $tag) {
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
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Image not found');
    }
}