<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository('AppBundle:Tasks')->findAll();
        $days = $em->getRepository('AppBundle:Days')->getActiveCards();
        $accounts = $em->getRepository('AppBundle:Accounts')->findAll();

        return $this->render('default/index.html.twig', array(
                    'tasks' => $tasks,
                    'days' => $days,
                    'accounts' => $accounts,
        ));
    }

}
