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
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
     * @QueryParam(name="offset", requirements="\d+", default="", description="Index of beginning of pagination")
     * @QueryParam(name="limit", requirements="\d+", default="", description="Number of items to display")
     * @QueryParam(name="page", requirements="\d+", default="", description="The page to display")
     * @QueryParam(name="top", requirements="[a-z]+", default="", description="If order by score")
     * @QueryParam(name="search", requirements="[a-z]+", default="", description="Search term")
     * @param Request $request
     * @return mixed
     */
    public function getImagesAction(Request $request, ParamFetcher $paramFetcher) {
        $settings = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Settings')
                ->find(1);

        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $page = $paramFetcher->get('page');
        $top = $paramFetcher->get('top');
        $search = $paramFetcher->get('search');

        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('i')
           ->from('AppBundle:Image', 'i');

        //If page is set use default limit
        if($page != "") {
            $offset = $page * $settings->getLimitGallery() - $settings->getLimitGallery();
            $limit = $settings->getLimitGallery();
        }
        if ($offset != "") {
            $qb->setFirstResult($offset);
        }
        if ($limit != "") {
            $qb->setMaxResults($limit);
        }

        //Order by score
        if ($top == "true") {
            $qb->addSelect('SUM(COALESCE(v.value,0)) AS HIDDEN score_image')
                ->leftJoin('i.votes', 'v')
                ->groupBy('i.id')
                ->addOrderBy('score_image', 'DESC');
        }

        //Search
        if ($search != "") {
            $qb->leftJoin('i.categories', 'c')
               ->leftJoin('i.tags', 't')
               ->where('i.description LIKE :search')
               ->orWhere('c.name LIKE :search')
               ->orWhere('t.name LIKE :search')
               ->setParameter('search', '%'.$search.'%');
        }

        $qb->addOrderBy('i.createdAt', 'DESC');
        $qb->addOrderBy('i.id', 'DESC');

        $images = $qb->getQuery()->getResult();

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
     * @QueryParam(name="offset", requirements="\d+", default="", description="Index of beginning of pagination")
     * @QueryParam(name="limit", requirements="\d+", default="", description="Number of items to display")
     * @QueryParam(name="page", requirements="\d+", default="", description="The page to display")
     * @QueryParam(name="top", requirements="[a-z]+", default="", description="If order by score")
     * @param Request $request
     * @return mixed
     */
    public function getCategoriesImagesAction(Request $request, ParamFetcher $paramFetcher) {
        $category = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->find($request->get('id'));
        /* @var $category Category */

        if (empty($category)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Category not found');
        }

        $settings = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Settings')
                ->find(1);

        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $page = $paramFetcher->get('page');
        $top = $paramFetcher->get('top');

        $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->createQueryBuilder('i')
                ->join('i.categories', 'c')
                ->where('c.id = :id_categ')
                ->setParameter('id_categ', $request->get('id'));

        //If page is set use default limit
        if($page != "") {
            $offset = $page * $settings->getLimitGallery() - $settings->getLimitGallery();
            $limit = $settings->getLimitGallery();
        }
        if ($offset != "") {
            $qb->setFirstResult($offset);
        }
        if ($limit != "") {
            $qb->setMaxResults($limit);
        }

        //Order by score
        if ($top == "true") {
            $qb->addSelect('SUM(COALESCE(v.value,0)) AS HIDDEN score_image')
                ->leftJoin('i.votes', 'v')
                ->groupBy('i.id')
                ->addOrderBy('score_image', 'DESC');
        }

        $qb->addOrderBy('i.createdAt', 'DESC');
        $qb->addOrderBy('i.id', 'DESC');

        $images = $qb->getQuery()->getResult();

        return $images;
    }

    /**
     * All Images of the Tag
     * @Rest\View(serializerGroups={"image"})
     * @Rest\Get("/tags/{id}/images")
     * @QueryParam(name="offset", requirements="\d+", default="", description="Index of beginning of pagination")
     * @QueryParam(name="limit", requirements="\d+", default="", description="Number of items to display")
     * @QueryParam(name="page", requirements="\d+", default="", description="The page to display")
     * @QueryParam(name="top", requirements="[a-z]+", default="", description="If order by score")
     * @param Request $request
     * @return mixed
     */
    public function getTagImagesAction(Request $request, ParamFetcher $paramFetcher) {
        $tag = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Tag')
                ->find($request->get('id'));
        /* @var $tag Tag */

        if (empty($tag)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Tag not found');
        }

        $settings = $this->get('doctrine.orm.entity_manager')
         ->getRepository('AppBundle:Settings')
         ->find(1);

        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $page = $paramFetcher->get('page');
        $top = $paramFetcher->get('top');

        $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->createQueryBuilder('i')
                ->join('i.tags', 't')
                ->where('t.id = :id_tag')
                ->setParameter('id_tag', $request->get('id'));

        //If page is set use default limit
        if($page != "") {
            $offset = $page * $settings->getLimitGallery() - $settings->getLimitGallery();
            $limit = $settings->getLimitGallery();
        }
        if ($offset != "") {
            $qb->setFirstResult($offset);
        }
        if ($limit != "") {
            $qb->setMaxResults($limit);
        }

        //Order by score
        if ($top == "true") {
            $qb->addSelect('SUM(COALESCE(v.value,0)) AS HIDDEN score_image')
                ->leftJoin('i.votes', 'v')
                ->groupBy('i.id')
                ->addOrderBy('score_image', 'DESC');
        }

        $qb->addOrderBy('i.createdAt', 'DESC');
        $qb->addOrderBy('i.id', 'DESC');

        $images = $qb->getQuery()->getResult();

        return $images;
    }

    /**
     * All Images of the User
     * @Rest\View(serializerGroups={"image"})
     * @Rest\Get("/users/{id}/images")
     * @QueryParam(name="offset", requirements="\d+", default="", description="Index of beginning of pagination")
     * @QueryParam(name="limit", requirements="\d+", default="", description="Number of items to display")
     * @QueryParam(name="page", requirements="\d+", default="", description="The page to display")
     * @QueryParam(name="top", requirements="[a-z]+", default="", description="If order by score")
     * @param Request $request
     * @return mixed
     */
    public function getUserImagesAction(Request $request, ParamFetcher $paramFetcher) {
        $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User not found');
        }

        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $page = $paramFetcher->get('page');
        $top = $paramFetcher->get('top');

        $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->createQueryBuilder('i')
                ->where('i.creator = :id_user')
                ->setParameter('id_user', $request->get('id'));

        //If page is set use default limit
        if($page != "") {
            $offset = $page * $settings->getLimitGallery() - $settings->getLimitGallery();
            $limit = $settings->getLimitGallery();
        }
        if ($offset != "") {
            $qb->setFirstResult($offset);
        }
        if ($limit != "") {
            $qb->setMaxResults($limit);
        }

        //Order by score
        if ($top == "true") {
            $qb->addSelect('SUM(COALESCE(v.value,0)) AS HIDDEN score_image')
                ->leftJoin('i.votes', 'v')
                ->groupBy('i.id')
                ->addOrderBy('score_image', 'DESC');
        }

        $qb->addOrderBy('i.createdAt', 'DESC');
        $qb->addOrderBy('i.id', 'DESC');

        $images = $qb->getQuery()->getResult();

        return $images;
    }

    /*********************** POST ***********************/
    /**
     * Insert new Image
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"image"})
     * @Rest\Post("/images")
     * @Security("has_role('ROLE_WRITER')")
     * @param Request $request
     */
    public function postImageAction(Request $request) {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        
        $connectedUser = $this->get('security.token_storage')->getToken()->getUser();

        $data = $request->request->all();
        //Value by default
        $today = new \DateTime();
        $data['createdAt'] = $today->format('Y-m-d H:i:s');
        $data['creator'] = $connectedUser->getId();;
        unset($data['updatedAt']);
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
     * @param Request $request
     * @return mixed|\AppBundle\Entity\Image|\FOS\RestBundle\View\View|\Symfony\Component\Form\Form
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
     * @return mixed|\AppBundle\Entity\Image|\FOS\RestBundle\View\View|\Symfony\Component\Form\Form
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
        $today = new \DateTime();
        $data['createdAt'] = $image->getCreatedAt()->format('Y-m-d H:i:s');
        $data['updatedAt'] = $today->format('Y-m-d H:i:s');
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        
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