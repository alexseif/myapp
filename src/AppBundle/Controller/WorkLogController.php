<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WorkLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Worklog controller.
 *
 * @Route("worklog")
 */
class WorkLogController extends Controller
{

  /**
   * Lists all workLog entities.
   *
   * @Route("/", name="worklog_index")
   * @Method("GET")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $workLogs = $em->getRepository('AppBundle:WorkLog')->orderByTaskList();

    return $this->render('worklog/index.html.twig', array(
          'workLogs' => $workLogs,
    ));
  }

  /**
   * Lists all workLog entities.
   *
   * @Route("/tasklist/{tasklist}", name="worklog_tasklist")
   * @Method("GET")
   */
  public function tasklistAction(\AppBundle\Entity\TaskLists $tasklist)
  {
    $em = $this->getDoctrine()->getManager();

    $workLogs = $em->getRepository('AppBundle:WorkLog')->getByTaskList($tasklist);

    return $this->render('worklog/tasklist.html.twig', array(
          'workLogs' => $workLogs,
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
    $log = (is_null($request->get('unlog'))) ? TRUE : FALSE;

    $criteria = array('completed' => TRUE, 'workLoggable' => $log);
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
          ->andWhere('t.workLoggable = :log')
          ->andWhere('a.name = :account')
          ->setParameter('log', $log)
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
          ->andWhere('t.workLoggable = :log')
          ->andWhere('c.name = :client')
          ->setParameter('log', $log)
          ->setParameter('client', $clientName)
          ->orderBy("t.completedAt", "DESC")
          ->getQuery()
          ->getResult();
    } else {
      $tasks = $tasksRepo->findBy($criteria, $orderBy);
    }

    return $this->render('worklog/completedTasks.html.twig', array(
          'unlog' => $log,
          'tasks' => $tasks,
    ));
  }

  /**
   * Creates a new workLog entity.
   *
   * @Route("/new", name="worklog_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();


    /** Cost Of Life * */
    $currencies = $em->getRepository('AppBundle:Currency')->findAll();
    $cost = $em->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"];

    $costOfLife = new \AppBundle\Logic\CostOfLifeLogic($cost, $currencies);


    $workLog = new Worklog();
    $workLog->setPricePerUnit($costOfLife->getHourly());

    if ($request->get('task')) {
      $task = $em->getRepository('AppBundle:Tasks')->find($request->get('task'));
      if (!$task) {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
      }
      $workLog->setTask($task);
      $workLog->setName($task->getTask());
      $workLog->setDuration($task->getEst());
      $workLog->setTotal($workLog->getPricePerUnit() / 60 * $workLog->getDuration());
    }
    $form = $this->createForm('AppBundle\Form\WorkLogType', $workLog);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $task = $workLog->getTask();
      $AccountTransaction = new \AppBundle\Entity\AccountTransactions();
      $AccountTransaction->setNote($workLog->getTask());
      $AccountTransaction->setAmount($workLog->getTotal() * -1);
      $AccountTransaction->setAccount($task->getAccount());
      $em->persist($workLog);
      $em->persist($AccountTransaction);
      $em->flush();

      return $this->redirectToRoute('worklog_show', array('id' => $workLog->getId()));
    }

    return $this->render('worklog/new.html.twig', array(
          'workLog' => $workLog,
          'costOfLife' => $costOfLife,
          'form' => $form->createView(),
    ));
  }

  /**
   * Auto Creates a new workLog entity.
   *
   * @Route("/autolog", name="worklog_autolog")
   * @Method({"GET", "POST"})
   */
  public function autologAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    /** Cost Of Life * */
    $currencies = $em->getRepository('AppBundle:Currency')->findAll();
    $cost = $em->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"];

    $costOfLife = new \AppBundle\Logic\CostOfLifeLogic($cost, $currencies);

    $taskIds = $request->get('task_ids');
    foreach ($taskIds as $taskId) {
      $task = $em->getRepository('AppBundle:Tasks')->find($taskId);
      if (!$task) {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
      }
      if ($task->getEst() > 0) {
        if (is_null($task->getWorkLog())) {
          $task->setWorkLog(new WorkLog());
        }
        $task->setWorkLoggable(true);
        $task->getWorklog()->setTask($task);
        $task->getWorklog()->setPricePerUnit($costOfLife->getHourly());
        $task->getWorklog()->setName($task->getTask());
        $task->getWorklog()->setDuration($task->getEst());
        $task->getWorklog()->setTotal($task->getWorklog()->getPricePerUnit() / 60 * $task->getWorklog()->getDuration());
        $em->persist($task->getWorklog());
      } else {
        $this->addFlash('warning_raw', 'Task  <a href="' . $this->generateUrl('tasks_show', array("id" => $taskId)) . '" target="_new">' . $taskId . '</a> has 0 est');
      }
      $em->flush();
    }
    return new \Symfony\Component\HttpFoundation\Response();
  }

  /**
   * Marks tasks as unloggale
   *
   * @Route("/unloggable", name="worklog_unloggable")
   * @Method({"GET", "POST"})
   */
  public function unloggableAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $taskIds = $request->get('task_ids');
    foreach ($taskIds as $taskId) {
      $task = $em->getRepository('AppBundle:Tasks')->find($taskId);
      if (!$task) {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
      }

      if ($task->getWorklog()) {
        $worklog = $task->getWorklog();
        $em->remove($worklog);
      }

      $task->setWorklog(NULL);
      $task->setWorkLoggable(FALSE);
      $em->flush();
    }
    return new \Symfony\Component\HttpFoundation\Response();
  }

  /**
   * Deletes a workLog entity.
   *
   * @Route("/autodelete", name="worklog_autodelete")
   * @Method({"GET", "POST"})
   */
  public function autodeleteAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $taskIds = $request->get('task_ids');
    foreach ($taskIds as $taskId) {
      $task = $em->getRepository('AppBundle:Tasks')->find($taskId);
      if (!$task) {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
      }
      if ($task->getWorklog()) {
        $em->remove($task->getWorklog());
      }
      $em->flush($task);
    }
    return new \Symfony\Component\HttpFoundation\Response();
  }

  /**
   * Finds and displays a workLog entity.
   *
   * @Route("/{id}", name="worklog_show")
   * @Method("GET")
   */
  public function showAction(WorkLog $workLog)
  {
    $deleteForm = $this->createDeleteForm($workLog);

    return $this->render('worklog/show.html.twig', array(
          'workLog' => $workLog,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing workLog entity.
   *
   * @Route("/{id}/edit", name="worklog_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, WorkLog $workLog)
  {
    $deleteForm = $this->createDeleteForm($workLog);
    $editForm = $this->createForm('AppBundle\Form\WorkLogType', $workLog);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('worklog_edit', array('id' => $workLog->getId()));
    }

    return $this->render('worklog/edit.html.twig', array(
          'workLog' => $workLog,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a workLog entity.
   *
   * @Route("/{id}", name="worklog_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, WorkLog $workLog)
  {
    $form = $this->createDeleteForm($workLog);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($workLog);
      $em->flush($workLog);
    }

    return $this->redirectToRoute('worklog_index');
  }

  /**
   * Creates a form to delete a workLog entity.
   *
   * @param WorkLog $workLog The workLog entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(WorkLog $workLog)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('worklog_delete', array('id' => $workLog->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}
