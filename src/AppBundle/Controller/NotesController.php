<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Notes;
use AppBundle\Form\NotesType;

/**
 * Notes controller.
 *
 * @Route("/notes")
 */
class NotesController extends Controller
{

  /**
   * Lists all Notes entities.
   *
   * @Route("/", name="notes_index")
   * @Method("GET")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $notes = $em->getRepository('AppBundle:Notes')->findAll();

    return $this->render('notes/index.html.twig', array(
          'notes' => $notes,
    ));
  }

  /**
   * Creates a new Notes entity.
   *
   * @Route("/new", name="notes_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $note = new Notes();
    $form = $this->createForm('AppBundle\Form\NotesType', $note);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($note);
      $em->flush();

      return $this->redirectToRoute('notes_show', array('id' => $note->getId()));
    }

    return $this->render('notes/new.html.twig', array(
          'note' => $note,
          'notes_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a Notes entity.
   *
   * @Route("/{id}", name="notes_show")
   * @Method("GET")
   */
  public function showAction(Notes $note)
  {
    $deleteForm = $this->createDeleteForm($note);

    return $this->render('notes/show.html.twig', array(
          'note' => $note,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing Notes entity.
   *
   * @Route("/{id}/edit", name="notes_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, Notes $note)
  {
    $deleteForm = $this->createDeleteForm($note);
    $editForm = $this->createForm('AppBundle\Form\NotesType', $note);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($note);
      $em->flush();

      return $this->redirectToRoute('notes_show', array('id' => $note->getId()));
    }

    return $this->render('notes/edit.html.twig', array(
          'note' => $note,
          'notes_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a Notes entity.
   *
   * @Route("/{id}", name="notes_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, Notes $note)
  {
    $form = $this->createDeleteForm($note);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($note);
      $em->flush();
    }

    return $this->redirectToRoute('notes_index');
  }

  /**
   * Creates a form to delete a Notes entity.
   *
   * @param Notes $note The Notes entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Notes $note)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('notes_delete', array('id' => $note->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}
