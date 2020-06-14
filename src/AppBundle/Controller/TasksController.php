<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Tasks;
use AppBundle\Form\TasksType;

/**
 * Tasks controller.
 *
 * @Route("/tasks")
 */
class TasksController extends Controller
{

  /**
   * Lists all Tasks entities.
   *
   * @Route("/", name="tasks_index", methods={"GET"})
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $tasks = $em->getRepository('AppBundle:Tasks')->findAllWithJoins();

    return $this->render("AppBundle:tasks:index.html.twig", array(
          'tasks' => $tasks,
    ));
  }

  /**
   * Reorders Tasks entity.
   *
   * @Route("/reorder", name="tasks_reorder", methods={"GET"})
   */
  public function reorderAction(Request $request)
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
    $tasks = new \Doctrine\Common\Collections\ArrayCollection();
    foreach ($taskListsOrder as $taskListId) {
      $reorderTasks = $tasksRepo->findBy(
          array(
        'taskList' => $taskListId
          ), array(
        'urgency' => 'DESC',
        'priority' => 'DESC',
        'order' => 'ASC',
        'est' => 'ASC'
          )
      );

      $tasks->add($reorderTasks);
    }

    return new \Symfony\Component\HttpFoundation\JsonResponse($tasks);
  }

  /**
   * Search all Tasks entities.
   *
   * @Route("/progressByDate", name="tasks_progress_by_date", methods={"GET"})
   */
  public function progressByDateAction(Request $request)
  {
    $formData = new \stdClass();
    $formData->date = new \DateTime();
    $formData->date->modify('-1 day');

    $form = $this->createFormBuilder($formData)
        ->setMethod('GET')
        ->add('date', \Symfony\Component\Form\Extension\Core\Type\DateType::class, [
          'widget' => 'single_text',
          'label' => false])
        ->add('Get', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class)
        ->getForm();

    $form->handleRequest($request);

    $em = $this->getDoctrine()->getManager();
    $tasksRepo = $em->getRepository('AppBundle:Tasks');

    $completedYesterday = $tasksRepo->getCompletedByDate($formData->date);
    $createdYesterday = $tasksRepo->getCreatedByDate($formData->date);
    $updatedYesterday = $tasksRepo->getUpdatedByDate($formData->date);
    // Starting by yesterday

    return $this->render("AppBundle:tasks:progressByDate.html.twig", [
          'form' => $form->createView(),
          'date' => $formData->date,
          'completedYesterday' => $completedYesterday,
          'createdYesterday' => $createdYesterday,
          'updatedYesterday' => $updatedYesterday
    ]);
  }

  /**
   * Search all Tasks entities.
   *
   * @Route("/search", name="tasks_search", methods={"GET"})
   */
  public function searchAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $form = $this->createForm(\AppBundle\Form\TasksFilterType::class, $request->get('tasks_filter'), array(
      'method' => 'GET',
    ));
    $filters = $tasks = array();
    if ($request->query->has($form->getName())) {
      $filters = $request->get('tasks_filter');
      $tasksQuery = $em->getRepository('AppBundle:Tasks')->createQueryBuilder('t')
          ->select('t, tl, acc, c, w')
          ->join('t.taskList', 'tl')
          ->leftJoin('tl.account', 'acc')
          ->leftJoin('acc.client', 'c')
          ->leftJoin('t.workLog', 'w');

      $firstWhere = true;
      foreach ($filters as $key => $value) {
        if ($firstWhere) {
          $firstWhere = false;
          $tasksQuery->where($tasksQuery->expr()->in('t.' . $key, ':' . $key));
        } else {
          $tasksQuery->andWhere($tasksQuery->expr()->in('t.' . $key, ':' . $key));
        }
        $tasksQuery->setParameter($key, $value);
      }

      $tasks = $tasksQuery->getQuery()->getResult();
    }

    return $this->render("AppBundle:tasks:search.html.twig", array(
          'filters' => $filters,
          'tasks' => $tasks,
          'task_filter_form' => $form->createView(),
    ));
  }

  /**
   * Advanced Lists all Tasks entities.
   *
   * @Route("/advanced", name="tasks_advanced", methods={"GET", "POST"})
   */
  public function advancedAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $tasks = $em->getRepository('AppBundle:Tasks')->findBy(array(
      "completed" => false
        ), array(
      "priority" => "DESC",
      "urgency" => "DESC",
      "order" => "ASC",
      "duration" => "ASC"
    ));

    return $this->render("AppBundle:tasks:advanced.html.twig", array(
          'tasks' => $tasks,
    ));
  }

  /**
   * Creates a new Tasks entity.
   *
   * @Route("/new", name="tasks_new", methods={"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $task = new Tasks();
    if (!empty($request->get("tasklist"))) {
      $taskList = $em->getRepository('AppBundle:TaskLists')->find($request->get("tasklist"));
      $task->setTaskList($taskList);
    }
    $form = $this->createForm(TasksType::class, $task);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      if ($task->getCompleted()) {
        if (is_null($task->getCompletedAt()))
          $task->setCompletedAt(new \DateTime());
      } else {
        $task->setCompletedAt(null);
      }
      $em->persist($task);
      $em->flush();

      return $this->redirectToRoute('tasks_show', array('id' => $task->getId()));
//            return $this->redirectToRoute('focus');
    }

    return $this->render("AppBundle:tasks:new.html.twig", array(
          'task' => $task,
          'task_form' => $form->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing Tasks entity.
   *
   * @Route("/order", name="tasks_order", methods={"POST"})
   */
  public function orderAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    if ($request->isXMLHttpRequest()) {
      $tasks = $request->get('tasks');
      foreach ($tasks as $order => $taskId) {
        $task = $em->find(Tasks::class, $taskId);
        if ($task) {
          $task->setOrder($order);
        }
      }
      $em->flush();
      return new \Symfony\Component\HttpFoundation\JsonResponse();
    }
  }

  /**
   * Finds and displays a Tasks entity.
   *
   * @Route("/{id}", name="tasks_show", methods={"GET"})
   */
  public function showAction(Tasks $tasks)
  {
    $deleteForm = $this->createDeleteForm($tasks);

    return $this->render("AppBundle:tasks:show.html.twig", array(
          'task' => $tasks,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Finds and displays a Tasks entity.
   *
   * @Route("/{id}/modal", name="task_show_modal", methods={"GET"})
   */
  public function showModalAction(Tasks $tasks)
  {
    return $this->render("AppBundle:tasks:show_modal.html.twig", array(
          'task' => $tasks,
    ));
  }

  /**
   * Displays a form to edit an existing Tasks entity.
   *
   * @Route("/{id}/edit", name="tasks_edit", methods={"GET", "POST"})
   */
  public function editAction(Request $request, Tasks $task)
  {
    $em = $this->getDoctrine()->getManager();


    if ($request->isXMLHttpRequest()) {

      if (!is_null($request->get('postpone'))) {
        $postpone = $request->get('postpone');
        $eta = new \DateTime($request->get('postpone'));
        if ("tomorrow" == $postpone) {
          $eta->setTime(8, 0, 0);
        }
        $task->setEta($eta);
      }

      if (!is_null($request->get('undo'))) {
        $task->setEta(null);
      }
      if (!is_null($request->get('completed'))) {
        $task->setCompleted($request->get('completed'));
        if ($task->getCompleted()) {
          $task->setCompletedAt(new \DateTime());
        } else {
          $task->setCompletedAt(null);
        }
      }
      $em->flush();
      return new \Symfony\Component\HttpFoundation\JsonResponse();
    }

    $deleteForm = $this->createDeleteForm($task);
    $editForm = $this->createForm(TasksType::class, $task);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em = $this->getDoctrine()->getManager();
      if ("archive" == $request->get('completedAt')) {
        $task->setCompleted(true);
        $lastMonth = new \DateTime();
        $lastMonth->sub(new \DateInterval('P1M'));
        $task->setCompletedAt($lastMonth);
      } else {
        if ($task->getCompleted()) {
          if (null == $task->getCompletedAt()) {
            $task->setCompletedAt(new \DateTime());
          }
        } else {
          $task->setCompletedAt(null);
        }
      }
      $em->persist($task);
      $em->flush();
      return $this->redirectToRoute('tasks_show', array('id' => $task->getId()));
    }

    return $this->render("AppBundle:tasks:edit.html.twig", array(
          'task' => $task,
          'task_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Postpone a task eta to tomorrow
   *
   * @Route("/{id}/postpone", name="tasks_postpone", methods={"GET"})
   */
  public function postponeAction(Request $request, Tasks $task)
  {
    $em = $this->getDoctrine()->getManager();
    $tomorrow = new \DateTime();
    $tomorrow->add(\DateInterval::createFromDateString("+8 hours"));
    $task->setEta($tomorrow);
    $em->persist($task);
    $em->flush();
    $referer = $request->headers->get('referer');
    if (is_null($referer) || strpos($request->headers->get('referer'), "postpone")) {
      return $this->redirectToRoute('focus');
    }
    return $this->redirect($referer);
  }

  /**
   * Deletes a Tasks entity.
   *
   * @Route("/{id}", name="tasks_delete", methods={"DELETE"})
   */
  public function deleteAction(Request $request, Tasks $task)
  {
    $form = $this->createDeleteForm($task);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $workLog = $task->getWorkLog();
      if ($workLog) {
        $em->remove($workLog);
      }
      $em->remove($task);
      $em->flush();
    }

    return $this->redirectToRoute('tasks_index');
  }

  /**
   * Creates a form to delete a Tasks entity.
   *
   * @param Tasks $task The Tasks entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Tasks $task)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('tasks_delete', array('id' => $task->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}
