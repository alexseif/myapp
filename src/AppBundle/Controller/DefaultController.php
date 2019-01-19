<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Tasks;
use AppBundle\Entity\TaskLists;
use AppBundle\Form\TasksType;
use AppBundle\Model\ActionItem;

class DefaultController extends Controller
{

  /**
   * 
   * @Route("/", name="dashboard")
   * @Template("default/dashboard.html.twig")
   */
  public function dashboardAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $taskLists = $em->getRepository('AppBundle:TaskLists')->findAllWithActiveTasks();
    $unlistedTasks = $em->getRepository('AppBundle:Tasks')->findUnlisted();
    $randomTasks = $em->getRepository('AppBundle:Tasks')->randomTasks();
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

    $countByUrgenctAndPriority = $em->getRepository('AppBundle:Tasks')->countByUrgenctAndPriority();
    $piechart = [];
    $piechart['Urgent & Important'] = 0;
    $piechart['Urgent'] = 0;
    $piechart['Important'] = 0;
    $piechart['Normal'] = 0;
    $piechart['Low'] = 0;

    foreach ($countByUrgenctAndPriority as $key => $row) {
      $row['est'] = (int) $row['est'];
      if ($row['urgency']) {
        if ($row['priority']) {
          $piechart['Urgent & Important'] = $row['est'];
        } else {
          $piechart['Urgent'] = $row['est'];
        }
      } elseif ($row['priority'] > 0) {
        $piechart['Important'] = $row['est'];
      } elseif ($row['priority'] < 0) {
        $piechart['Low'] = $row['est'];
      } else {
        $piechart['Normal'] = $row['est'];
      }
    }

    $tskCnt = array();
    foreach ($tsksCntDay as $t) {
      $tskCnt[$t['day_name']] = $t['cnt'];
    }
    $issued = 0;
    foreach ($issuedThisMonth as $tm) {
      $issued += abs($tm->getAmount());
    }

    return array(
      'taskLists' => $taskLists,
      'randomTasks' => $randomTasks,
      'unlistedTasks' => $unlistedTasks,
      'days' => $days,
      'accounts' => $accounts,
      'issued' => $issued,
      'tskCnt' => $tskCnt,
      'interval' => $interval,
      'costOfLife' => $costOfLife,
      'piechart' => $piechart,
    );
  }

  /**
   * 
   * @Route("/workarea", name="workarea")
   * @Template("default/workarea.html.twig")
   */
  public function workareaAction()
  {
    $em = $this->getDoctrine()->getManager();
    $focusTasks = $em->getRepository('AppBundle:Tasks')->focusLimitList();
    $days = $em->getRepository('AppBundle:Days')->getImportantCards();
    $accounts = $em->getRepository('AppBundle:Accounts')->findBy(array('conceal' => false));
    /** Cost Of Life * */
    $currencies = $em->getRepository('AppBundle:Currency')->findAll();
    $cost = $em->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"];

    $costOfLife = new \AppBundle\Logic\CostOfLifeLogic($cost, $currencies);

    $issuedThisMonth = $em->getRepository('AppBundle:AccountTransactions')->issuedThisMonth();
    $issued = 0;
    foreach ($issuedThisMonth as $tm) {
      $issued += abs($tm->getAmount());
    }
    return array(
      'focus' => $focusTasks,
      'days' => $days,
      'accounts' => $accounts,
      'costOfLife' => $costOfLife,
      'issued' => $issued,
    );
  }

  /**
   * 
   * @Route("/beta", name="beta")
   * @Template("default/beta.html.twig")
   */
  public function betaAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $tasksRepo = $em->getRepository('AppBundle:Tasks');
    $tasks = $tasksRepo->getIncopmleteTasks();
//    $tasksCompletedToday = $tasksRepo->getCompletedToday();
    $days = $em->getRepository('AppBundle:Days')->getImportantCards();

    $today = new \DateTime();
    $actionItems = array();
    foreach ($days as $day) {
      $actionItems[] = new ActionItem($day->getId(), 'day', $day->getName(), $day->getDeadline()->diff($today)->format('%R%a days'));
    }
    foreach ($tasks as $task) {
      $actionItems[] = new ActionItem($task->getId(), 'task', $task->getTask(), $task->getEst() . "m", $task->getTaskList()->getName(), $task->getPriority(), $task->getUrgency());
    }

    return array(
      'actionItems' => $actionItems,
      'tasks' => $tasks,
      'days' => $days,
      'today' => $today,
    );
  }

  /**
   * 
   * @Route("/focus", name="focus")
   * @Template("default/focus.html.twig")
   */
  public function focusAction()
  {
    $em = $this->getDoctrine()->getManager();

    $tasks = $em->getRepository('AppBundle:Tasks')->focusList();
    $completedToday = $em->getRepository('AppBundle:Tasks')->getCompletedToday();
    $task = new Tasks();

    return array(
      'tasks' => $tasks,
      'completed' => $completedToday,
    );
  }

  /**
   * 
   * @Route("/singleTask", name="singleTask")
   * @Template("default/singleTask.html.twig")
   */
  public function singleTaskAction()
  {
    $em = $this->getDoctrine()->getManager();
    $tasksRepo = $em->getRepository('AppBundle:Tasks');
    $weightedList = $tasksRepo->weightedList();
    $taskListsOrder = [];
    foreach ($weightedList as $key => $row) {
      if (!in_array($row['id'], $taskListsOrder)) {
        $taskListsOrder[] = $row['id'];
      }
    }
    $tasks = [];
    foreach ($taskListsOrder as $taskListId) {
      $reorderTasks = $tasksRepo->findBy(
          array(
        'taskList' => $taskListId,
        'completed' => false
          ), array(
        'urgency' => 'DESC',
        'priority' => 'DESC',
        'est' => 'ASC',
        'order' => 'ASC'
          ), 10
      );
      $tasks = array_merge($tasks, $reorderTasks);
    }
    return array(
      'tasks' => $tasks,
    );
  }

  /**
   * 
   * @Route("/focus/{name}", name="focus_tasklist")
   * @Template("default/focus.html.twig")
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
    return array(
      'taskList' => $taskList,
      'tasks' => $tasks,
      'completed' => $completedToday,
      'task_form' => $form->createView(),
    );
  }

  /**
   * 
   * @Route("/lists", name="lists_view")
   * @Template("default/lists.html.twig")
   */
  public function listsAction()
  {
    $em = $this->getDoctrine()->getManager();
    $today = new \DateTime();

    $lists = $em->getRepository('AppBundle:TaskLists')->findBy(array('status' => 'start'));
    return array(
      'today' => $today,
      'lists' => $lists,
    );
  }

  /**
   * 
   * @Route("/lists/{id}/modal", name="list_show_modal", methods={"GET"})
   * @Template("tasks/show_modal.html.twig")
   */
  public function listModalAction(TaskLists $taskList)
  {
    $tasks = $taskList->getTasks(false);
    $random = rand(0, $tasks->count() - 1);
    return array(
      'task' => $tasks->get($random),
    );
  }

}
