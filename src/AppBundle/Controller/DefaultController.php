<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Tasks;
use AppBundle\Entity\TaskLists;
use AppBundle\Form\TasksType;
use AppBundle\Model\ActionItem;

class DefaultController extends Controller
{

  /**
   * 
   * @Route("/lists", name="lists_view")
   */
  public function listsAction()
  {
    $em = $this->getDoctrine()->getManager();
    $today = new \DateTime();

    $lists = $em->getRepository('AppBundle:TaskLists')->findBy(array('status' => 'start'));
    return $this->render("AppBundle:default:lists.html.twig", array(
          'today' => $today,
          'lists' => $lists,
    ));
  }

  /**
   * 
   * @Route("/lists/{id}/modal", name="list_show_modal", methods={"GET"})
   */
  public function listModalAction(TaskLists $taskList)
  {
    $tasks = $taskList->getTasks(false);
    $random = rand(0, $tasks->count() - 1);
    return $this->render("AppBundle:tasks:show_modal.html.twig", array(
          'task' => $tasks->get($random),
    ));
  }

  /**
   * 
   * @Route("/getBottomBarDetails", name="get_bottom_bar_details", methods={"GET"})
   */
  public function getBottomBarDetails()
  {
    return $this->render("::bottom-bar-details.html.twig");
  }

}
