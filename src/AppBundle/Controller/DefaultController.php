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

    return $this->render('default/dashboard.html.twig', array(
          'taskLists' => $taskLists,
          'randomTasks' => $randomTasks,
          'tasks' => $tasks,
          'days' => $days,
          'accounts' => $accounts,
          'issued' => $issued,
          'tskCnt' => $tskCnt,
          'interval' => $interval,
          'costOfLife' => $costOfLife,
          'piechart' => $piechart,
    ));
  }

  /**
   * @Route("/workarea", name="workarea")
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
    return $this->render('default/workarea.html.twig', array(
          'focus' => $focusTasks,
          'days' => $days,
          'accounts' => $accounts,
          'costOfLife' => $costOfLife,
          'issued' => $issued,
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
   * @Route("/singleTask", name="singleTask")
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
    return $this->render('default/singleTask.html.twig', array(
          'tasks' => $tasks,
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
   * @ROUTE("/completedTasks", name="completed_tasks")
   */
  public function completedTasksAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $tasksRepo = $em->getRepository('AppBundle:Tasks');

    $taskListName = $request->get('taskList');
    $accountName = $request->get('account');
    $clientName = $request->get('client');

    $criteria = array('completed' => TRUE);
    $orderBy = array('completedAt' => 'DESC');

    if ($taskListName) {
      $taskList = $em->getRepository('AppBundle:TaskLists')->findBy(array('name' => $taskListName));
      $criteria['taskList'] = $taskList;
      $tasks = $tasksRepo->findBy($criteria, $orderBy);
    } elseif ($accountName) {
      $tasks = $tasksRepo->createQueryBuilder('t')
          ->select('t')
          ->leftJoin('t.taskList', 'tl')
          ->leftJoin('tl.account', 'a')
          ->where('t.completed = TRUE')
          ->andWhere('a.name = :account')
          ->setParameter('account', $accountName)
          ->orderBy("t.completedAt", "DESC")
          ->getQuery()
          ->getResult();
    } elseif ($clientName) {
      $tasks = $tasksRepo->createQueryBuilder('t')
          ->select('t')
          ->leftJoin('t.taskList', 'tl')
          ->leftJoin('tl.account', 'a')
          ->leftJoin('a.client', 'c')
          ->where('t.completed = TRUE')
          ->andWhere('c.name = :client')
          ->setParameter('client', $clientName)
          ->orderBy("t.completedAt", "DESC")
          ->getQuery()
          ->getResult();
    } else {
      $tasks = $tasksRepo->findBy($criteria, $orderBy);
    }



    return $this->render('default/completedTasks.html.twig', array(
          'tasks' => $tasks,
    ));
  }

  /**
   * 
   * @ROUTE("/lists", name="lists_view")
   */
  public function listsAction()
  {
    $em = $this->getDoctrine()->getManager();
    $today = new \DateTime();

    $lists = $em->getRepository('AppBundle:TaskLists')->findBy(array('status' => 'start'));
    return $this->render('default/lists.html.twig', array(
          'today' => $today,
          'lists' => $lists,
    ));
  }

}
