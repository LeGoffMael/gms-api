<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use AppBundle\Form\Type\RoleType;
use AppBundle\Entity\Role;

/**
 * RoleController short summary.
 *
 * RoleController description.
 *
 * @version 1.0
 * @author Maël Le Goff
 */
class RoleController extends Controller {

    /**
     * All Roles
     * @Rest\View(serializerGroups={"role"})
     * @Rest\Get("/roles")
     * @param Request $request
     * @return mixed
     */
    public function getRolesAction(Request $request) {
        $roles = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Role')
                ->findAll();
        /* @var $roles Role[] */

        return $roles;
    }

    /**
     * Role with the specified id
     * @Rest\View(serializerGroups={"role"})
     * @Rest\Get("/roles/{id}")
     * @param Request $request
     * @return mixed
     */
    public function getRoleAction(Request $request) {
        $role = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Role')
                ->find($request->get('id'));
        /* @var $role Role */

        if (empty($role)) {
            return $this->roleNotFound();
        }

        return $role;
    }
    
    /**
     * Insert new Role
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"role"})
     * @Rest\Post("/roles")
     * @param Request $request
     * @return \AppBundle\Entity\Role|\AppBundle\Form\Type\RoleType
     */
    public function postRoleAction(Request $request) {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        
        $data = array();
        $data['name'] = $request->get('name');
        
        $form->submit($data); // Validation des donn�es
        
        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($role);
            $em->flush();
            return $role;
        } else {
            return $form;
        }
    }
    
    /**
     * Full Update Role with the specified id
     * @Rest\View(serializerGroups={"role"})
     * @Rest\Put("/roles/{id}")
     */
    public function updateRoleAction(Request $request) {
        return $this->updateRole($request, true);
    }
    
    /**
     * Partial Update Role with the specified id
     * @Rest\View(serializerGroups={"role"})
     * @Rest\Patch("/roles/{id}")
     * @param Request $request
     * @return mixed
     */
    public function patchRoleAction(Request $request) {
        return $this->updateRole($request, false);
    }
    
    /**
     * Complete or Partial Update Role with the specified id
     * @param Request $request
     * @param mixed $clearMissing complete or partial
     * @return mixed
     */
    private function updateRole(Request $request, $clearMissing) {
        $role = $this->get('doctrine.orm.entity_manager')
        ->getRepository('AppBundle:Role')
        ->find($request->get('id'));
        /* @var $role Role */
        
        if (empty($role)) {
            return $this->roleNotFound();
        }
        
        $form = $this->createForm(RoleType::class, $role);
        
        //TODO
        /* generate the receive datas, normaly just with $request->request->all() but always empty */
        $data = array();
        if($request->get('name') != null)
            $data['name'] = $request->get('name');
            $form->submit($data, $clearMissing);
            
            if ($form->isValid()) {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($role);
                $em->flush();
                return $role;
            } else {
                return $form;
            }
    }
    
    /**
     * Delete Role with the specified id
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"role"})
     * @Rest\Delete("/roles/{id}")
     * @param Request $request
     */
    public function removeRoleAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $role = $em->getRepository('AppBundle:Role')
        ->find($request->get('id'));
        /* @var $role Role */
        
        if($role) {
            $em->remove($role);
            $em->flush();
        }
    }
    
    /**
     * Message 404 Role not found
     * @return View
     */
    private function roleNotFound() {
        return \FOS\RestBundle\View\View::create(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
    }
}