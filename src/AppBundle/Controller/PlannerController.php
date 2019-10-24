<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/planner")
 */
class PlannerController extends Controller
{

  /**
   * @Route("/", name="planner_list")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $taskLists = $em->getRepository('AppBundle:TaskLists')->findBy(["status" => "start"]);
    $planners = [];
    $planners[] = new \DateTime();
    $planners[] = strtotime("+1 days");
    $planners[] = strtotime("+2 days");
    return $this->render('AppBundle:Planner:index.html.twig', array(
          "planners" => $planners,
          "taskLists" => $taskLists
    ));
  }

  /**
   * @Route("/update", name="planner_update")
   */
  public function updateAction(Request $request)
  {
    $planners = $request->get('planners');
    $em = $this->getDoctrine()->getManager();
    foreach ($planners as $key => $planner) {
      foreach ($planner as $sortOrder => $taskId) {
        $planners[$key][$sortOrder] = $em->getRepository("AppBundle:Tasks")->find($taskId);
      }
    }
    $session = $request->getSession();
    $session->set("planners", $planners);
    return new JsonResponse();
  }

  /**
   * @Route("/planner", name="planner")
   */
  public function plannerAction(Request $request)
  {
    $session = $request->getSession();
    $planners = $session->get("planners", []);

    return $this->render('AppBundle:Planner:planner.html.twig', array(
          "planners" => $planners,
    ));
  }

}
