<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Tasks;
use AppBundle\Entity\TaskLists;
use AppBundle\Form\TasksType;

class DefaultController extends Controller
{

  /**
   * @Route("/", name="dashboard")
   */
  public function dashboardAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $taskLists = $em->getRepository('AppBundle:TaskLists')->findAllWithActiveTasks();
    $tasks = $em->getRepository('AppBundle:Tasks')->findUnlisted();
    $days = $em->getRepository('AppBundle:Days')->getActiveCards();
    $accounts = $em->getRepository('AppBundle:Accounts')->findAll();
    $paidThisMonth = $em->getRepository('AppBundle:AccountTransactions')->paidThisMonth();
    $tsksCntDay = $em->getRepository('AppBundle:Tasks')->findTasksCountByDay();
    $estSum = $em->getRepository('AppBundle:Tasks')->sumEst()["est"];
    $today = new \DateTime();
    $endDay = new \DateTime();
    $endDay->add(\DateInterval::createFromDateString($estSum . " minutes"));
    $interval = $endDay->diff($today, true);

    /** Cost Of Life * */
    $currencies = $em->getRepository('AppBundle:Currency')->findAll();
    $cost = $em->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"];

    $costOfLife = new \AppBundle\Logic\CostOfLifeLogic($cost, $currencies);



    $tskCnt = array();
    foreach ($tsksCntDay as $t) {
      $tskCnt[$t['day_name']] = $t['cnt'];
    }
    $paid = 0;
    foreach ($paidThisMonth as $tm) {
      $paid += abs($tm->getAmount());
    }


    return $this->render('default/dashboard.html.twig', array(
          'taskLists' => $taskLists,
          'tasks' => $tasks,
          'days' => $days,
          'accounts' => $accounts,
          'paid' => $paid,
          'tskCnt' => $tskCnt,
          'interval' => $interval,
          'costOfLife' => $costOfLife,
    ));
  }

  /**
   * @Route("/focus", name="focus")
   */
  public function focusAction()
  {
    $em = $this->getDoctrine()->getManager();

    $tasks = $em->getRepository('AppBundle:Tasks')->focusList();
    $completedToday = $em->getRepository('AppBundle:Tasks')->getCompletedToday();
    $task = new Tasks();
    $form = $this->createForm(TasksType::class, $task, array(
      'action' => $this->generateUrl('tasks_new')
    ));
    return $this->render('default/focus.html.twig', array(
          'tasks' => $tasks,
          'completed' => $completedToday,
          'task_form' => $form->createView(),
    ));
  }

  /**
   * 
   * @ROUTE("/focus/{name}", name="focus_tasklist")
   */
  public function focusByTaskListAction(TaskLists $taskList)
  {
    $em = $this->getDoctrine()->getManager();

    $tasks = $em->getRepository('AppBundle:Tasks')->focusByTasklist($taskList);
    $completedToday = $em->getRepository('AppBundle:Tasks')->getCompletedToday();

    $task = new Tasks();
    $task->setTaskList($taskList);
    $form = $this->createForm(TasksType::class, $task, array(
      'action' => $this->generateUrl('tasks_new')
    ));
    return $this->render('default/focus.html.twig', array(
          'taskList' => $taskList,
          'tasks' => $tasks,
          'completed' => $completedToday,
          'task_form' => $form->createView(),
    ));
  }

  /**
   * 
   * @ROUTE("/debug", name="debug")
   */
  public function debug()
  {
    $availableRoutes = $this->get('router')->getRouteCollection()->all();
    $routes = array();
    foreach ($availableRoutes as $name => $route) {
      if (strpos($name, "_") !== 0) {
        $div = explode('_', $name);
        $first = $div[0];
        unset($div[0]);

        $r['name'] = implode('_', $div);
        $r['path'] = $route->getPath();
        $r['methods'] = $route->getMethods();
        $routes[$first][] = $r;
      }
    }
    $entities = array();
    $em = $this->getDoctrine()->getManager();
    $meta = $em->getMetadataFactory()->getAllMetadata();
    foreach ($meta as $m) {
      $e['entity'] = $m->getName();
      $e['name'] = explode('\\', $e['entity']);
      $e['name'] = end($e['name']);

      $entities[] = $e;
    }

    return $this->render('default/debug.html.twig', array(
          'routes' => $routes,
          'entities' => $entities
    ));
  }

}
