<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
   * @Route("/", name="notes_index", methods={"GET"})
   * @Template("AppBundle:notes:index.html.twig")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $notes = $em->getRepository('AppBundle:Notes')->findAll();

    return array(
      'notes' => $notes,
    );
  }

  /**
   * Creates a new Notes entity.
   *
   * @Route("/new", name="notes_new", methods={"GET", "POST"})
   * @Template("AppBundle:notes:new.html.twig")
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

    return array(
      'note' => $note,
      'notes_form' => $form->createView(),
    );
  }

  /**
   * Finds and displays a Notes entity.
   *
   * @Route("/{id}", name="notes_show", methods={"GET"})
   * @Template("AppBundle:notes:show.html.twig")
   */
  public function showAction(Notes $note)
  {
    $deleteForm = $this->createDeleteForm($note);

    return array(
      'note' => $note,
      'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Displays a form to edit an existing Notes entity.
   *
   * @Route("/{id}/edit", name="notes_edit", methods={"GET", "POST"})
   * @Template("AppBundle:notes:edit.html.twig")
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

    return array(
      'note' => $note,
      'notes_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Deletes a Notes entity.
   *
   * @Route("/{id}", name="notes_delete", methods={"DELETE"})
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
