<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use AppBundle\Form\Type\CategoryType;
use AppBundle\Entity\Category;

/**
 * CategoryController short summary.
 *
 * CategoryController description.
 *
 * @version 1.0
 * @author Ma�l Le Goff
 */
class CategoryController extends Controller {

    /**
     * All Categories
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Get("/categories")
     * @param Request $request
     * @return mixed
     */
    public function getCategoriesAction(Request $request) {
        $categories = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->findAll();
        /* @var $categories Category[] */

        return $categories;
    }

    /**
     * Category with the specified id
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Get("/categories/{id}")
     * @param Request $request
     * @return mixed
     */
    public function getCategoryAction(Request $request) {
        $category = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->find($request->get('id'));
        /* @var $category Category */

        if (empty($category)) {
            return $this->categoryNotFound();
        }

        return $category;
    }

    /**
     * All Images of the Category
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Get("/categories/{id}/parent")
     * @param Request $request 
     * @return mixed
     */
    public function getParentAction(Request $request) {
        $category = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->find($request->get('id')); // L'identifiant en tant que param�tre n'est plus n�cessaire
        /* @var $category Category */

        if (empty($category)) {
            return $this->categoryNotFound();
        }

        return $category->getParent();
    }

    /**
     * All the childrens of the categories
     * @Rest\View(serializerGroups={"children"})
     * @Rest\Get("/categories/{id}/childrens")
     * @param Request $request 
     * @return mixed
     */
    public function getChildrensAction(Request $request) {
        $category = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->find($request->get('id'));
        /* @var $category Category */

        if (empty($category)) {
            return $this->categoryNotFound();
        }

        return $category->getChildrens();
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
     * Insert new Category
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"category"})
     * @Rest\Post("/categories")
     * @param Request $request
     * @return \AppBundle\Entity\Category|\AppBundle\Form\Type\CategoryType
     */
    public function postCategoryAction(Request $request) {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $data = array();
        $data['name'] = $request->get('name');
        $data['urlImage'] = $request->get('urlImage');
        if($request->get('parent') != null) {
            $data['parent'] = $request->get('parent');
        }

        $form->submit($data); // Validation des donn�es

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($category);
            $em->flush();
            return $category;
        } else {
            return $form;
        }
    }

    /**
     * Full Update Category with the specified id
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Put("/categories/{id}")
     */
    public function updateCategoryAction(Request $request) {
        return $this->updateCategory($request, true);
    }

    /**
     * Partial Update Category with the specified id
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Patch("/categories/{id}")
     * @param Request $request
     * @return mixed
     */
    public function patchCategoryAction(Request $request) {
        return $this->updateCategory($request, false);
    }

    /**
     * Complete or Partial Update Category with the specified id
     * @param Request $request
     * @param mixed $clearMissing complete or partial
     * @return mixed
     */
    private function updateCategory(Request $request, $clearMissing) {
        $category = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->find($request->get('id'));
        /* @var $category Category */

        if (empty($category)) {
            return $this->categoryNotFound();
        }

        $form = $this->createForm(CategoryType::class, $category);

        //TODO
        /* generate the receive datas, normaly just with $request->request->all() but always empty */
        $data = array();
        if($request->get('name') != null)
            $data['name'] = $request->get('name');
        if($request->get('urlImage') != null)
            $data['urlImage'] = $request->get('urlImage');
        if($request->get('parent') != null) {
            $data['parent'] = $request->get('parent');
        }
        $form->submit($data, $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($category);
            $em->flush();
            return $category;
        } else {
            return $form;
        }
    }

    /**
     * Delete Category with the specified id
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"category"})
     * @Rest\Delete("/categories/{id}")
     * @param Request $request
     */
    public function removeCategoryAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $category = $em->getRepository('AppBundle:Category')
                    ->find($request->get('id'));
        /* @var $category Category */

        if (!$category) {
            return;
        }

        //Remove all the childrens
        foreach($category->getChildrens() as $children) {
            $em->remove($children);
        }
        $em->remove($category);
        $em->flush();
    }

    /**
     * Message 404 Category not found
     * @return View
     */
    private function categoryNotFound() {
        return \FOS\RestBundle\View\View::create(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
    }
}