<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="dashboard")
     */
    public function dashboardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $taskLists = $em->getRepository('AppBundle:TaskLists')->findAll();
        $tasks = $em->getRepository('AppBundle:Tasks')->findUnlisted();
        $days = $em->getRepository('AppBundle:Days')->getActiveCards();
        $accounts = $em->getRepository('AppBundle:Accounts')->findAll();
        $projects = $em->getRepository('AppBundle:Projects')->findAll();

        return $this->render('default/dashboard.html.twig', array(
                    'taskLists' => $taskLists,
                    'tasks' => $tasks,
                    'days' => $days,
                    'accounts' => $accounts,
                    'projects' => $projects
        ));
    }

}
