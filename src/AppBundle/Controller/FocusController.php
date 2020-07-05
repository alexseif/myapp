<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Tasks;
use AppBundle\Entity\TaskLists;
use AppBundle\Form\TasksType;
use AppBundle\Model\ActionItem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FocusController extends Controller
{

  /**
   * 
   * @Route("/focus", name="focus")
   */
  public function focusAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $focus = [];
    $focus['todayHours'] = \AppBundle\Util\WorkWeek::getDayHours(date('l'));
    $focus['completed'] = $em->getRepository('AppBundle:Tasks')->getCompletedToday();
    if ($request->query->has('client')) {
      $focus['client'] = $em->getRepository('AppBundle:Client')->find($request->get('client'));
      if (empty($focus['client'])) {
        throw new NotFoundHttpException("Client not found");
      }
      $focus['tasks'] = $em->getRepository('AppBundle:Tasks')->focusByClient($focus['client']);
      $contract = $em->getRepository('AppBundle:Contract')->findOneBy(["client" => $focus['client']]);
      if ($contract) {
        $focus['contract'] = $contract;
        $focus['todayHours'] = $focus['contract']->getHoursPerDay();
      }
    } else {
      $focus['tasks'] = $em->getRepository('AppBundle:Tasks')->focusList();
    }

    return $this->render("AppBundle:focus:index.html.twig", $focus);
  }

  /**
   * 
   * @Route("/focus/{name}", name="focus_tasklist")
   */
  public function focusByTaskListAction(TaskLists $taskList)
  {
    $em = $this->getDoctrine()->getManager();
    $todayHours = \AppBundle\Util\WorkWeek::getDayHours(date('l'));

    $tasks = $em->getRepository('AppBundle:Tasks')->focusByTasklist($taskList);
    $completedToday = $em->getRepository('AppBundle:Tasks')->getCompletedToday();

    return $this->render("AppBundle:focus:index.html.twig", array(
          'todayHours' => $todayHours,
          'taskList' => $taskList,
          'tasks' => $tasks,
          'completed' => $completedToday,
    ));
  }

  /**
   * 
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
        'order' => 'ASC'
          ), 10
      );
      $tasks = array_merge($tasks, $reorderTasks);
    }
    return $this->render("AppBundle:focus:singleTask.html.twig", array(
          'tasks' => $tasks,
    ));
  }

  /**
   * 
   * @Route("/beta", name="beta")
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
      $actionItems[] = new ActionItem($task->getId(), 'task', $task->getTask(), $task->getDuration() . "m", $task->getTaskList()->getName(), $task->getPriority(), $task->getUrgency());
    }

    return $this->render("AppBundle:focus:beta.html.twig", array(
          'actionItems' => $actionItems,
          'tasks' => $tasks,
          'days' => $days,
          'today' => $today,
    ));
  }

}
