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

    $taskLists = $em->getRepository('AppBundle:TaskLists')->findAllWithActiveTasks();
    $tasks = $em->getRepository('AppBundle:Tasks')->findUnlisted();
    $days = $em->getRepository('AppBundle:Days')->getActiveCards();
    $accounts = $em->getRepository('AppBundle:Accounts')->findAll();
    $paidThisMonth = $em->getRepository('AppBundle:AccountTransactions')->paidThisMonth();
    $projects = $em->getRepository('AppBundle:Projects')->findAll();
    $txnDate = $em->getRepository('AppBundle:Transactions')->getFirstDate();
    $tsksCntDay = $em->getRepository('AppBundle:Tasks')->findTasksCountByDay();
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
          'projects' => $projects,
          'tskCnt' => $tskCnt,
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
    $form = $this->createForm(TasksType::class, $task, array(
      'action' => $this->generateUrl('tasks_new')
    ));
    return $this->render('default/focus.html.twig', array(
          'tasks' => $tasks,
          'task_form' => $form->createView(),
    ));
  }

  /**
   * 
   * @ROUTE("/focus/{tasklist}", name="focus_tasklist")
   */
  public function focusByTaskListAction($tasklist)
  {
    $em = $this->getDoctrine()->getManager();

    $tasks = $em->getRepository('AppBundle:Tasks')->focusByTasklist($tasklist);
    $task = new Tasks();
    $form = $this->createForm(TasksType::class, $task, array(
      'action' => $this->generateUrl('tasks_new')
    ));
    return $this->render('default/focus.html.twig', array(
          'tasks' => $tasks,
          'task_form' => $form->createView(),
    ));
  }

}
