<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use AppBundle\Form\Type\VoteType;
use AppBundle\Entity\Vote;

/**
 * VoteController short summary.
 *
 * VoteController description.
 *
 * @version 1.0
 * @author Ma�l Le Goff
 */
class VoteController extends Controller {

    /**
     * All Votes
     * @Rest\View(serializerGroups={"vote"})
     * @Rest\Get("/votes")
     * @param Request $request
     * @return mixed
     */
    public function getVotesAction(Request $request) {
        $votes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Vote')
                ->findAll();
        /* @var $votes Vote[] */

        return $votes;
    }

    /**
     * Vote with the specified ids
     * @Rest\View(serializerGroups={"vote"})
     * @Rest\Get("/votes/{ip}_{user}_{image}")
     * @param Request $request
     * @return mixed
     */
    public function getVoteAction(Request $request) {
        $vote = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Vote')
                ->findBy(array(
                    'ip' => $request->get('ip'),
                    'user' => $request->get('user'),
                    'image' => $request->get('image')
                ));
        /* @var $vote Vote */

        if (empty($vote)) {
            return $this->voteNotFound();
        }

        return $vote;
    }

    /**
     * All Votes of the User
     * @Rest\View(serializerGroups={"vote"})
     * @Rest\Get("/users/{id}/votes")
     * @param Request $request
     * @return mixed
     */
    public function getUserVotesAction(Request $request) {
        $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User not found');
        }

        $votes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Vote')
                ->createQueryBuilder('v')
                ->where('v.user = :id_user')
                ->setParameter('id_user', $request->get('id'))
                ->getQuery()->getResult();
        /* @var $votes Vote[] */

        return $votes;
    }

    /**
     * All Votes of the Image
     * @Rest\View(serializerGroups={"vote"})
     * @Rest\Get("/images/{id}/votes")
     * @param Request $request
     * @return mixed
     */
    public function getImageVotesAction(Request $request) {
        $image = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Image')
                ->find($request->get('id'));
        /* @var $image Image */

        if (empty($image)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Image not found');
        }

        $votes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Vote')
                ->createQueryBuilder('v')
                ->where('v.image = :id_image')
                ->setParameter('id_image', $request->get('id'))
                ->getQuery()->getResult();
        /* @var $votes Vote[] */

        return $votes;
    }

    /**
     * Insert or Full Update Vote with the specified ids
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"vote"})
     * @Rest\Put("/images/{id}/votes")
     * @param Request $request
     * @return \AppBundle\Entity\Vote|\AppBundle\Form\Type\VoteType
     */
    public function putVoteAction(Request $request) {
        $vote = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Vote')
                ->findBy(array(
                    'ip' => $request->get('ip'),
                    'user' => $request->get('user'),
                    'image' => $request->get('id')
                ));
        /* @var $vote Vote */

        $data = $request->request->all();
        //Default value    
        if (!(true === $authChecker->isGranted('ROLE_ADMIN') && array_key_exists($data['ip']) && array_key_exists($data['user']))) {
            $connectedUser = $this->get('security.token_storage')->getToken()->getUser();
            $data['ip'] = $request->getClientIp();
            $data['user'] = $connectedUser->getId();
        }
        $data['image'] = $request->get('image');
        $today = new \DateTime();
        $data['date'] = $today->format('Y-m-d H:i:s');
        
        //If don't exist yet
        if (empty($vote)) {
            $vote = new Vote();
            $form = $this->createForm(VoteType::class, $vote);
            $form->submit($data); // Data validation

            if ($form->isValid()) {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($vote);
                $em->flush();
                return $vote;
            } else {
                return $form;
            }
        }

        $form = $this->createForm(VoteType::class, $vote[0]);

        // The false parameter tells Symfony
        // to keep the values in our entity
        // if the user does not supply one in a query
        $form->submit($data, true);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($vote[0]);
            $em->flush();
            return $vote;
        } else {
            return $form;
        }
    }

    /**
     * Delete Vote with the specified ids
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"vote"})
     * @Rest\Delete("/images/{id}/votes")
     * @param Request $request
     */
    public function removeVoteAction(Request $request) {
        $em = $this->get('doctrine.orm.entity_manager');
        $vote = $em->getRepository('AppBundle:Vote')
                ->findBy(array(
                    'ip' => $request->get('ip'),
                    'user' => $request->get('user'),
                    'image' => $request->get('id')
                ));
        /* @var $vote Vote */

        if($vote) {
            $em->remove($vote[0]);
            $em->flush();
        }
    }

    /**
     * Message 404 Vote not found
     * @return View
     */
    private function voteNotFound() {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Vote not found');
    }
}