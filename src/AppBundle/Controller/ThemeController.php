<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use AppBundle\Form\Type\ThemeType;
use AppBundle\Entity\Theme;

/**
 * ThemeController short summary.
 *
 * ThemeController description.
 *
 * @version 1.0
 * @author Maël Le Goff
 */
class ThemeController extends Controller {

    /**
     * All Themes
     * @Rest\View()
     * @Rest\Get("/themes")
     * @param Request $request
     * @return mixed
     */
    public function getThemesAction(Request $request) {
        $themes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Theme')
                ->findAll();
        /* @var $themes Theme[] */

        return $themes;
    }

    /**
     * Theme with the specified id
     * @Rest\View()
     * @Rest\Get("/themes/{id}")
     * @param Request $request
     * @return mixed
     */
    public function getThemeAction(Request $request) {
        $theme = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Theme')
                ->find($request->get('id'));
        /* @var $theme Theme */

        if (empty($theme)) {
            return $this->themeNotFound();
        }

        return $theme;
    }

    /**
     * Insert new Theme
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/themes")
     * @param Request $request
     * @return \AppBundle\Entity\Theme|\AppBundle\Form\Type\ThemeType
     */
    public function postThemeAction(Request $request) {
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);

        $form->submit($request->request->all()); // Data validation

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($theme);
            $em->flush();
            return $theme;
        } else {
            return $form;
        }
    }

    /**
     * Full Update Theme with the specified id
     * @Rest\View()
     * @Rest\Put("/themes/{id}")
     */
    public function putThemeAction(Request $request) {
        return $this->updateTheme($request, true);
    }

    /**
     * Partial Update Theme with the specified id
     * @Rest\View()
     * @Rest\Patch("/themes/{id}")
     * @param Request $request
     * @return mixed
     */
    public function patchThemeAction(Request $request) {
        return $this->updateTheme($request, false);
    }

    /**
     * Complete or Partial Update Theme with the specified id
     * @param Request $request
     * @param mixed $clearMissing complete or partial
     * @return mixed
     */
    private function updateTheme(Request $request, $clearMissing) {
        $theme = $this->get('doctrine.orm.entity_manager')
        ->getRepository('AppBundle:Theme')
        ->find($request->get('id'));
        /* @var $theme Theme */

        if (empty($theme)) {
            return $this->themeNotFound();
        }

        $form = $this->createForm(ThemeType::class, $theme);

        // The false parameter tells Symfony
        // to keep the values in our entity
        // if the user does not supply one in a query
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($theme);
            $em->flush();
            return $theme;
        } else {
            return $form;
        }
    }

    /**
     * Delete $theme with the specified id
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/themes/{id}")
     * @param Request $request
     */
    public function removeThemeAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $theme = $em->getRepository('AppBundle:Theme')
        ->find($request->get('id'));
        /* @var $theme Theme */

        if($theme) {
            $em->remove($theme);
            $em->flush();
        }
    }

    /**
     * Message 404 Theme not found
     * @return View
     */
    private function themeNotFound() {
        return \FOS\RestBundle\View\View::create(['message' => 'Theme not found'], Response::HTTP_NOT_FOUND);
    }
}