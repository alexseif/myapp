<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskLists;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/lists", name="lists_view")
     */
    public function listsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $today = new \DateTime();

        $lists = $em->getRepository('AppBundle:TaskLists')->findBy(['status' => 'start']);

        return $this->render('AppBundle:default:lists.html.twig', [
            'today' => $today,
            'lists' => $lists,
        ]);
    }

    /**
     * @Route("/lists/{id}/modal", name="list_show_modal", methods={"GET"})
     */
    public function listModalAction(TaskLists $taskList)
    {
        $tasks = $taskList->getTasks(false);
        $random = random_int(0, $tasks->count() - 1);

        return $this->render('AppBundle:tasks:show_modal.html.twig', [
            'task' => $tasks->get($random),
        ]);
    }

    /**
     * @Route("/getBottomBarDetails", name="get_bottom_bar_details", methods={"GET"})
     */
    public function getBottomBarDetails()
    {
        return $this->render('::bottom-bar-details.html.twig');
    }
}
