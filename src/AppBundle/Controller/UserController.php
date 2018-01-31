<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use AppBundle\Form\Type\UserType;
use AppBundle\Entity\User;

/**
 * UserController short summary.
 *
 * UserController description.
 *
 * @version 1.0
 * @author Ma�l Le Goff
 */
class UserController extends Controller {

    /**
     * All Users
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Get("/users")
     * @param Request $request
     * @return mixed
     */
    public function getUsersAction(Request $request) {
        $users = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->findAll();
        /* @var $users User[] */

        return $users;
    }

    /**
     * User with the specified id
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Get("/users/{id}")
     * @param Request $request
     * @return mixed
     */
    public function getUserAction(Request $request) {
        $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            return $this->userNotFound();
        }

        return $user;
    }

    /**
     * Insert new User
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post("/users")
     * @param Request $request
     * @return \AppBundle\Entity\User|\AppBundle\Form\Type\UserType
     */
    public function postUserAction(Request $request) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        //TODO

        $form->submit($data); // Validation des donn�es

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * Full Update User with the specified id
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Put("/users/{id}")
     */
    public function updateUserAction(Request $request) {
        return $this->updateUser($request, true);
    }

    /**
     * Partial Update User with the specified id
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Patch("/users/{id}")
     * @param Request $request
     * @return mixed
     */
    public function patchUserAction(Request $request) {
        return $this->updateUser($request, false);
    }

    /**
     * Complete or Partial Update User with the specified id
     * @param Request $request
     * @param mixed $clearMissing complete or partial
     * @return mixed
     */
    private function updateUser(Request $request, $clearMissing) {
       $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            return $this->userNotFound();
        }

        $form = $this->createForm(UserType::class, $user);

        //TODO

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * Delete User with the specified id
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"user"})
     * @Rest\Delete("/users/{id}")
     * @param Request $request
     */
    public function removeUserAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')
                  ->find($request->get('id'));
        /* @var $user User */

       if($user) {
            $em->remove($user);
            $em->flush();
        }
    }

    /**
     * Message 404 User not found
     * @return View
     */
    private function userNotFound() {
        return \FOS\RestBundle\View\View::create(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
    }
}