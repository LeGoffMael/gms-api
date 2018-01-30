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
     * All Categories of the Image
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Get("/images/{id}/categories")
     * @param Request $request
     * @return mixed
     */
    public function getImagesCategoriesAction(Request $request) {
        $image = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->find($request->get('id'));
        /* @var $image Image */

        if (empty($image)) {
            return $this->imageNotFound();
        }

        $categories = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->createQueryBuilder('c')
                ->join('c.images', 'i')
                ->where('i.id = :id_img')
                ->setParameter('id_img', $request->get('id'))
                ->getQuery()->getResult();
        /* @var $categories Category[] */

        return $categories;
    }

    /**
     * Insert new Image
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"image"})
     * @Rest\Post("/images")
     * @param Request $request
     */
    public function postImageAction(Request $request) {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        //Add Categories
        if($request->get('categories') != null) {
            $idCategories = explode(',',$request->get('categories'));
            foreach($idCategories as $idCategory) {
                $category = $this->get('doctrine.orm.entity_manager')
                                 ->getRepository('AppBundle:Category')
                                 ->find($idCategory);

                if (empty($category)) {
                    return \FOS\RestBundle\View\View::create(['message' => 'Category '.$idCategory.' not found'], Response::HTTP_NOT_FOUND);
                }

                $image->addCategory($category);
            }
        }

        //Add Tags
        if($request->get('tags') != null) {
            $idTags = explode(',',$request->get('tags'));
            foreach($idTags as $idTag) {
                $tag = $this->get('doctrine.orm.entity_manager')
                                 ->getRepository('AppBundle:Tag')
                                 ->find($idTag);

                if (empty($tag)) {
                    return \FOS\RestBundle\View\View::create(['message' => 'Tag '.$idTag.' not found'], Response::HTTP_NOT_FOUND);
                }

                $image->addTag($tag);
            }
        }

        //Custom the default time zone in php.ini
        $today = new \DateTime();
        $data = array(
            'url' => $request->get('url'),
            'date' => $today->format('Y-m-d H:i:s'),
            'score' => 0,
            'description' => $request->get('description'),
        );

        $form->submit($data); // Validation des données

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($image);
            $em->flush();
            return $image;
        } else {
            return $form;
        }
    }

    /**
     * Full Update Image with the specified id
     * @Rest\View(serializerGroups={"image"})
     * @Rest\Put("/images/{id}")
     */
    public function updateImageAction(Request $request) {
        return $this->updateImage($request, true);
    }

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

        //TODO
        /* generate the receive datas, normaly just with $request->request->all() but always empty */
        $data = array();
        if($request->get('url') != null) {
            $data['url'] = $request->get('url');
        }
        if($request->get('score') != null) {
            if($request->get('score') == 1) {
                $data['score'] = $image->getScore() + 1;
            } elseif($request->get('score') == 0) {
                $data['score'] = $image->getScore() - 1;
            }
        }
        if($request->get('description') != null) {
            $data['description'] = $request->get('description');
        }
        //Update categories
        if($request->get('categories') != null) {

            foreach ($image->getCategories() as $category) {
                $image->removeCategory($category);
            }
            $idCategories = explode(',',$request->get('categories'));
            foreach($idCategories as $idCategory) {
                $category = $this->get('doctrine.orm.entity_manager')
                                 ->getRepository('AppBundle:Category')
                                 ->find($idCategory);

                if (empty($category)) {
                    return \FOS\RestBundle\View\View::create(['message' => 'Category '.$idCategory.' not found'], Response::HTTP_NOT_FOUND);
                }

                $image->addCategory($category);
            }
        }
        //Update tags
        if($request->get('tags') != null) {
            foreach ($image->getTags() as $tag) {
                $image->removeTag($tag);
            }
            $idTags = explode(',',$request->get('tags'));
            foreach($idTags as $idTag) {
                $tag = $this->get('doctrine.orm.entity_manager')
                                 ->getRepository('AppBundle:Tag')
                                 ->find($idTag);

                if (empty($tag)) {
                    return \FOS\RestBundle\View\View::create(['message' => 'Tag '.$idTag.' not found'], Response::HTTP_NOT_FOUND);
                }

                $image->addTag($tag);
            }
        }
        if(!empty($data)) {
            $today = new \DateTime();
            $data['date'] = $today->format('Y-m-d H:i:s');
        }

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

    /**
     * Delete Image with the specified id
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"image"})
     * @Rest\Delete("/images/{id}")
     * @param Request $request
     */
    public function removeImageAction(Request $request)
    {
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