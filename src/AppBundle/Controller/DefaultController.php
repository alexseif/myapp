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
   * @Route("/beta", name="beta")
   * @Template("AppBundle:default:beta.html.twig")
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
   * @Template("AppBundle:default:focus.html.twig")
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
   * @Template("AppBundle:default:singleTask.html.twig")
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
    return array(
      'tasks' => $tasks,
    );
  }

  /**
   * 
   * @Route("/focus/{name}", name="focus_tasklist")
   * @Template("AppBundle:default:focus.html.twig")
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
   * @Template("AppBundle:default:lists.html.twig")
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
