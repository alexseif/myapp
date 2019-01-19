<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Days;
use AppBundle\Form\DaysType;

/**
 * Days controller.
 *
 * @Route("/days")
 */
class DaysController extends Controller
{

  /**
   * Lists all Days entities.
   *
   * @Route("/", name="days_index")
   * @Method("GET")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $days = $em->getRepository('AppBundle:Days')->getActiveCards();

    return $this->render('days/index.html.twig', array(
          'days' => $days,
    ));
  }

  /**
   * Lists all Archived Days entities.
   *
   * @Route("/archive", name="days_archive")
   * @Method("GET")
   */
  public function archiveAction()
  {
    $em = $this->getDoctrine()->getManager();

    $days = $em->getRepository('AppBundle:Days')->getArchiveCards();

    return $this->render('days/index.html.twig', array(
          'days' => $days,
    ));
  }

  /**
   * Creates a new Days entity.
   *
   * @Route("/new", name="days_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $day = new Days();
    $form = $this->createForm('AppBundle\Form\DaysType', $day);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($day);
      $em->flush();

      return $this->redirectToRoute('days_index');
    }

    return $this->render('days/new.html.twig', array(
          'day' => $day,
          'day_form' => $form->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing Days entity.
   *
   * @Route("/{id}/edit", name="days_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, Days $day)
  {
    $deleteForm = $this->createDeleteForm($day);
    $editForm = $this->createForm('AppBundle\Form\DaysType', $day);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($day);
      $em->flush();

      return $this->redirectToRoute('days_index');
    }

    return $this->render('days/edit.html.twig', array(
          'day' => $day,
          'day_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a Days entity.
   *
   * @Route("/{id}", name="days_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, Days $day)
  {
    $form = $this->createDeleteForm($day);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($day);
      $em->flush();
    }

    return $this->redirectToRoute('days_index');
  }

  /**
   * Creates a form to delete a Days entity.
   *
   * @param Days $day The Days entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Days $day)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('days_delete', array('id' => $day->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}
