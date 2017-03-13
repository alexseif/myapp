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
    $accounts = $em->getRepository('AppBundle:Accounts')->findBy(array('conceal' => false));
    $issuedThisMonth = $em->getRepository('AppBundle:AccountTransactions')->issuedThisMonth();
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

    $tasksUrgencyAndPriority = $em->getRepository('AppBundle:Tasks')->countByUrgenctAndPriority();
    $tasksUrgencyAndPriority = $em->getRepository('AppBundle:Tasks')->sumByUrgenctAndPriority();


    $tskCnt = array();
    foreach ($tsksCntDay as $t) {
      $tskCnt[$t['day_name']] = $t['cnt'];
    }
    $issued = 0;
    foreach ($issuedThisMonth as $tm) {
      $issued += abs($tm->getAmount());
    }

    return $this->render('default/dashboard.html.twig', array(
          'taskLists' => $taskLists,
          'tasks' => $tasks,
          'days' => $days,
          'accounts' => $accounts,
          'issued' => $issued,
          'tskCnt' => $tskCnt,
          'interval' => $interval,
          'costOfLife' => $costOfLife,
          'tasksUrgencyAndPriority' => $tasksUrgencyAndPriority
    ));
  }

  /**
   * @Route("/beta", name="beta")
   */
  public function betaAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $today = new \DateTime();

    return $this->render('default/beta.html.twig', array(
          'today' => $today,
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

    return $this->render('default/focus.html.twig', array(
          'tasks' => $tasks,
          'completed' => $completedToday,
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
   * @ROUTE("/roadmap", name="roadmap")
   */
  public function roadmapAction()
  {
    $roadmap = new \AppBundle\Logic\Roadmap();

    $em = $this->getDoctrine()->getManager();
    $today = new \DateTime();

    $tasks = $em->getRepository('AppBundle:Tasks')->findBy(
        array('completed' => false), array(
      'urgency' => 'desc',
      'priority' => 'desc',
      'order' => 'asc'
        )
    );
    $days = $em->getRepository('AppBundle:Days')->getActiveCards();

    $roadmap->setDays($days);
    $roadmap->setTasks($tasks);
    $roadmap->populateDots();

    return $this->render('default/roadmap.html.twig', array(
          'roadmap' => $roadmap,
    ));
  }

}
