<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use AppBundle\Form\Type\SettingsType;
use AppBundle\Entity\Settings;

/**
 * SettingsController short summary.
 *
 * SettingsController description.
 *
 * @version 1.0
 * @author Maël Le Goff
 */
class SettingsController extends Controller {
    /**
     * Settings
     * @Rest\View(serializerGroups={"settings"})
     * @Rest\Get("/settings")
     * @param Request $request
     * @return mixed
     */
    public function getSettingsAction(Request $request) {
        $settings = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Role')
                ->find(1);
        /* @var $settings Settings */

        if (empty($settings)) {
            return $this->settingsNotFound();
        }

        return $settings;
    }

    /**
     * Insert new Settings
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"settings"})
     * @Rest\Post("/settings")
     * @param Request $request
     * @return \AppBundle\Entity\Settings|\AppBundle\Form\Type\SettingsType
     */
    public function postRoleAction(Request $request) {
        $settings = new Settings();
        $form = $this->createForm(SettingsType::class, $settings);

        $form->submit($request->request->all()); // Data validation

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($settings);
            $em->flush();
            return $settings;
        } else {
            return $form;
        }
    }

    /**
     * Full Update Settings
     * @Rest\View(serializerGroups={"settings"})
     * @Rest\Put("/settings")
     */
    public function putSettingsAction(Request $request) {
        return $this->updateSettings($request, true);
    }

    /**
     * Partial Update Settings
     * @Rest\View(serializerGroups={"settings"})
     * @Rest\Patch("/settings")
     * @param Request $request
     * @return mixed
     */
    public function patchSettingsAction(Request $request) {
        return $this->updateSettings($request, false);
    }

    /**
     * Complete or Partial Update Settings
     * @param Request $request
     * @param mixed $clearMissing complete or partial
     * @return mixed
     */
    private function updateSettings(Request $request, $clearMissing) {
        $settings = $this->get('doctrine.orm.entity_manager')
        ->getRepository('AppBundle:Settings')
        ->find(1);
        /* @var $settings Settings */

        if (empty($settings)) {
            return $this->settingsNotFound();
        }

        $form = $this->createForm(SettingsType::class, $settings);

        // The false parameter tells Symfony
        // to keep the values in our entity
        // if the user does not supply one in a query
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($settings);
            $em->flush();
            return $settings;
        } else {
            return $form;
        }
    }

    /**
     * Delete Settings
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"settings"})
     * @Rest\Delete("/settings")
     * @param Request $request
     */
    public function removeRoleAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $settings = $em->getRepository('AppBundle:Settings')
        ->find(1);
        /* @var $settings Settings */

        if($settings) {
            $em->remove($settings);
            $em->flush();
        }
    }

    /**
     * Message 404 Settings not found
     * @return View
     */
    private function settingsNotFound() {
        return \FOS\RestBundle\View\View::create(['message' => 'Settings not found'], Response::HTTP_NOT_FOUND);
    }
}