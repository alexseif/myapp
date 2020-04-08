<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RecurrEntity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Recurr controller.
 *
 * @Route("recurr")
 */
class RecurrController extends Controller
{

  /**
   * Lists all recurr entities.
   *
   * @Route("/", name="recurr_index", methods={"GET"})
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $recurrs = $em->getRepository('AppBundle:RecurrEntity')->findAll();

    return $this->render('AppBundle:recurr:index.html.twig', array(
          'recurrs' => $recurrs,
    ));
  }

  /**
   * Creates a new recurr entity.
   *
   * @Route("/new", name="recurr_new", methods={"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $recurr = new RecurrEntity();
    $form = $this->createForm('AppBundle\Form\RecurrType', $recurr);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($recurr);
      $em->flush();

      return $this->redirectToRoute('recurr_show', array('id' => $recurr->getId()));
    }

    return $this->render('AppBundle:recurr:new.html.twig', array(
          'recurr' => $recurr,
          'form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a recurr entity.
   *
   * @Route("/{id}", name="recurr_show", methods={"GET"})
   */
  public function showAction(RecurrEntity $recurr)
  {
    $deleteForm = $this->createDeleteForm($recurr);
    $recurr->getRecurrenceCollection();
    return $this->render('AppBundle:recurr:show.html.twig', array(
          'recurr' => $recurr,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing recurr entity.
   *
   * @Route("/{id}/edit", name="recurr_edit", methods={"GET", "POST"})
   */
  public function editAction(Request $request, RecurrEntity $recurr)
  {
    $deleteForm = $this->createDeleteForm($recurr);
    $editForm = $this->createForm('AppBundle\Form\RecurrType', $recurr);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('recurr_edit', array('id' => $recurr->getId()));
    }

    return $this->render('AppBundle:recurr:edit.html.twig', array(
          'recurr' => $recurr,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a recurr entity.
   *
   * @Route("/{id}", name="recurr_delete", methods={"DELETE"})
   */
  public function deleteAction(Request $request, RecurrEntity $recurr)
  {
    $form = $this->createDeleteForm($recurr);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($recurr);
      $em->flush();
    }

    return $this->redirectToRoute('recurr_index');
  }

  /**
   * Creates a form to delete a recurr entity.
   *
   * @param Recurr $recurr The recurr entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(RecurrEntity $recurr)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('recurr_delete', array('id' => $recurr->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}
