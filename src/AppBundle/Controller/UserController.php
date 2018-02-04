<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
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
 * @author Maël Le Goff
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
        $form = $this->createForm(UserType::class, $user, ['validation_groups'=>['Default', 'New']]);

        $data = $request->request->all();
        //Value by default
        $today = new \DateTime();
        $data['createdAt'] = $today->format('Y-m-d H:i:s');
        $data['updatedAt'] = $today->format('Y-m-d H:i:s');
        $data['updatedAt'] = $today->format('Y-m-d H:i:s');
        $data['role'] = 3;
        $form->submit($data); // Data validation

        if ($form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            // the password in clear is encoded before the backup
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);

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
    public function putUserAction(Request $request) {
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

        if ($clearMissing) { // If a full update, the password must be validated
            $options = ['validation_groups'=>['Default', 'FullUpdate']];
        } else {
            $options = []; // Symfony's default validation group is Default
        }

        $form = $this->createForm(UserType::class, $user, $options);

        // The false parameter tells Symfony
        // to keep the values in our entity
        // if the user does not supply one in a query
        $data = $request->request->all();
        //Value by default
        $today = new \DateTime();
        $data['createdAt'] = $user->getCreatedAt()->format('Y-m-d H:i:s');
        $data['updatedAt'] = $today->format('Y-m-d H:i:s');
        $data['role'] = 3;
        $form->submit($data, $clearMissing);

        if ($form->isValid()) {
            // If the user wants to change his password
            if (!empty($user->getPlainPassword())) {
                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($encoded);
            }
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