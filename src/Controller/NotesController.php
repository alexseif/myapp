<?php

namespace App\Controller;

use App\Entity\Notes;
use App\Form\NotesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Notes controller.
 *
 * @Route("/notes")
 */
class NotesController extends AbstractController
{
    /**
     * Lists all Notes entities.
     *
     * @Route("/", name="notes_index", methods={"GET"})
     */
    public function indexAction( EntityManagerInterface $entityManager)
    {
        $em = $entityManager;

        $notes = $em->getRepository(Notes::class)->findAll();

        return $this->render('notes/index.html.twig', [
            'notes' => $notes,
        ]);
    }

    /**
     * Creates a new Notes entity.
     *
     * @Route("/new", name="notes_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, EntityManagerInterface $entityManager)
    {
        $note = new Notes();
        $form = $this->createForm(NotesType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('notes_show', ['id' => $note->getId()]);
        }

        return $this->render('notes/new.html.twig', [
            'note' => $note,
            'notes_form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Notes entity.
     *
     * @Route("/{id}", name="notes_show", methods={"GET"})
     */
    public function showAction(Notes $note)
    {
        $deleteForm = $this->createDeleteForm($note);

        return $this->render('notes/show.html.twig', [
            'note' => $note,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Notes entity.
     *
     * @Route("/{id}/edit", name="notes_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Notes $note, EntityManagerInterface $entityManager)
    {
        $deleteForm = $this->createDeleteForm($note);
        $editForm = $this->createForm(NotesType::class, $note);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $entityManager;
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('notes_show', ['id' => $note->getId()]);
        }

        return $this->render('notes/edit.html.twig', [
            'note' => $note,
            'notes_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Notes entity.
     *
     * @Route("/{id}", name="notes_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Notes $note, EntityManagerInterface $entityManager)
    {
        $form = $this->createDeleteForm($note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
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
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Notes $note)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('notes_delete', ['id' => $note->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
