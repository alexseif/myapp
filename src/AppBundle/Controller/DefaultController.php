<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Tasks;
use AppBundle\Form\TasksType;

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

    /**
     * @Route("/focus", name="focus")
     */
    public function focusAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository('AppBundle:Tasks')->focusList();
        $task = new Tasks();
        $form = $this->createForm(TasksType::class, $task);
        return $this->render('default/focus.html.twig', array(
                    'tasks' => $tasks,
                    'task_form' => $form->createView(),
        ));
    }

}
