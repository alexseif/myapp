<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
   * @Route("/", name="tasks_index")
   * @Method("GET")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $tasks = $em->getRepository('AppBundle:Tasks')->findAll();

    return $this->render('tasks/index.html.twig', array(
          'tasks' => $tasks,
    ));
  }

  /**
   * Advanced Lists all Tasks entities.
   *
   * @Route("/advanced", name="tasks_advanced")
   * @Method({"GET", "POST"})
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
      "est" => "ASC"
    ));

    return $this->render('tasks/advanced.html.twig', array(
          'tasks' => $tasks,
    ));
  }

  /**
   * Creates a new Tasks entity.
   *
   * @Route("/new", name="tasks_new")
   * @Method({"GET", "POST"})
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
        $task->setCompletedAt(new \DateTime());
      } else {
        $task->setCompletedAt(null);
      }
      $em->persist($task);
      $em->flush();

      return $this->redirectToRoute('tasks_show', array('id' => $task->getId()));
//            return $this->redirectToRoute('focus');
    }

    return $this->render('tasks/new.html.twig', array(
          'task' => $task,
          'task_form' => $form->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing Tasks entity.
   *
   * @Route("/order", name="tasks_order")
   * @Method("POST")
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
   * @Route("/{id}", name="tasks_show")
   * @Method("GET")
   */
  public function showAction(Tasks $tasks)
  {
    $deleteForm = $this->createDeleteForm($tasks);

    return $this->render('tasks/show.html.twig', array(
          'task' => $tasks,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing Tasks entity.
   *
   * @Route("/{id}/edit", name="tasks_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, Tasks $task)
  {
    $em = $this->getDoctrine()->getManager();


    if ($request->isXMLHttpRequest()) {
      $task->setCompleted($request->get('completed'));
      if ($task->getCompleted()) {
        $task->setCompletedAt(new \DateTime());
      } else {
        $task->setCompletedAt(null);
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

    return $this->render('tasks/edit.html.twig', array(
          'task' => $task,
          'task_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Postpone a task eta to tomorrow
   *
   * @Route("/{id}/postpone", name="tasks_postpone")
   * @Method("GET")
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
   * @Route("/{id}", name="tasks_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, Tasks $task)
  {
    $form = $this->createDeleteForm($task);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
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
