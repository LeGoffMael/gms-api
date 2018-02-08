<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
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
 * @author Maël Le Goff
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
        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('c')
           ->from('AppBundle:Category', 'c');
        $qb->orderBy('c.name', 'ASC');

        $categories = $qb->getQuery()->getResult();

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
     * Parent of the Category
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Get("/categories/{id}/parent")
     * @param Request $request
     * @return mixed
     */
    public function getParentAction(Request $request) {
        $category = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->find($request->get('id'));
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
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Image not found');
        }

        $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->createQueryBuilder('c')
                ->join('c.images', 'i')
                ->where('i.id = :id_img')
                ->setParameter('id_img', $request->get('id'));

        $qb->orderBy('c.name', 'ASC');

        $categories = $qb->getQuery()->getResult();

        return $categories;
    }

    /**
     * All Categories of the User
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Get("/users/{id}/categories")
     * @param Request $request
     * @return mixed
     */
    public function getUserCategoriesAction(Request $request) {
        $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User not found');
        }

        $qb = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Category')
                ->createQueryBuilder('c')
                ->where('c.user = :id_user')
                ->setParameter('id_user', $request->get('id'));

        $qb->orderBy('c.name', 'ASC');

        $categories = $qb->getQuery()->getResult();

        return $categories;
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

        $form->submit($request->request->all()); // Data validation

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
    public function putCategoryAction(Request $request) {
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

        // The false parameter tells Symfony
        // to keep the values in our entity
        // if the user does not supply one in a query
        $form->submit($request->request->all(), $clearMissing);

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
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Category not found');
    }
}