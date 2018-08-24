<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tasklist controller.
 *
 * @Route("tasklist")
 */
class TaskListController extends Controller
{

  /**
   * Lists all taskList entities.
   *
   * @Route("/", name="tasklist_index")
   * @Method("GET")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $taskLists = $em->getRepository('AppBundle:TaskList')->findAll();

    return $this->render('tasklist/index.html.twig', array(
          'taskLists' => $taskLists,
    ));
  }

  /**
   * Creates a new taskList entity.
   *
   * @Route("/new", name="tasklist_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $taskList = new Tasklist();
    $form = $this->createForm('AppBundle\Form\TaskListType', $taskList);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($taskList);
      $em->flush();

      return $this->redirectToRoute('tasklist_show', array('id' => $taskList->getId()));
    }

    return $this->render('tasklist/new.html.twig', array(
          'taskList' => $taskList,
          'form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a taskList entity.
   *
   * @Route("/{id}", name="tasklist_show")
   * @Method("GET")
   */
  public function showAction(TaskList $taskList)
  {
    $deleteForm = $this->createDeleteForm($taskList);

    return $this->render('tasklist/show.html.twig', array(
          'taskList' => $taskList,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing taskList entity.
   *
   * @Route("/{id}/edit", name="tasklist_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, TaskList $taskList)
  {
    $deleteForm = $this->createDeleteForm($taskList);
    $editForm = $this->createForm('AppBundle\Form\TaskListType', $taskList);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('tasklist_edit', array('id' => $taskList->getId()));
    }

    return $this->render('tasklist/edit.html.twig', array(
          'taskList' => $taskList,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a taskList entity.
   *
   * @Route("/{id}", name="tasklist_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, TaskList $taskList)
  {
    $form = $this->createDeleteForm($taskList);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($taskList);
      $em->flush();
    }

    return $this->redirectToRoute('tasklist_index');
  }

  /**
   * Creates a form to delete a taskList entity.
   *
   * @param TaskList $taskList The taskList entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(TaskList $taskList)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('tasklist_delete', array('id' => $taskList->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}
