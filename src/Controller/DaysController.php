<?php

namespace App\Controller;

use App\Entity\Days;
use App\Form\DaysType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Days controller.
 *
 * @Route("/days")
 */
class DaysController extends AbstractController
{

    /**
     * Lists all Days entities.
     *
     * @Route("/", name="days_index", methods={"GET"})
     */
    public function indexAction(EntityManagerInterface $entityManager
    ): Response {
        $em = $entityManager;

        $days = $em->getRepository(Days::class)->getActiveCards();

        return $this->render('days/index.html.twig', [
          'days' => $days,
        ]);
    }

    /**
     * Lists all Archived Days entities.
     *
     * @Route("/archive", name="days_archive", methods={"GET"})
     */
    public function archiveAction(EntityManagerInterface $entityManager
    ): Response {
        $em = $entityManager;

        $days = $em->getRepository(Days::class)->getArchiveCards();

        return $this->render('days/index.html.twig', [
          'days' => $days,
        ]);
    }

    /**
     * Creates a new Days entity.
     *
     * @Route("/new", name="days_new", methods={"GET", "POST"})
     */
    public function newAction(
      Request $request,
      EntityManagerInterface $entityManager
    ) {
        $day = new Days();
        $form = $this->createForm(DaysType::class, $day);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->persist($day);
            $em->flush();

            return $this->redirectToRoute('days_index');
        }

        return $this->render('days/new.html.twig', [
          'day' => $day,
          'day_form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Days entity.
     *
     * @Route("/{id}/edit", name="days_edit", methods={"GET", "POST"})
     */
    public function editAction(
      Request $request,
      Days $day,
      EntityManagerInterface $entityManager
    ) {
        $deleteForm = $this->createDeleteForm($day);
        $editForm = $this->createForm(DaysType::class, $day);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $entityManager;
            $em->persist($day);
            $em->flush();

            return $this->redirectToRoute('days_index');
        }

        return $this->render('days/edit.html.twig', [
          'day' => $day,
          'day_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Days entity.
     *
     * @Route("/{id}", name="days_delete", methods={"DELETE"})
     */
    public function deleteAction(
      Request $request,
      Days $day,
      EntityManagerInterface $entityManager
    ): RedirectResponse {
        $form = $this->createDeleteForm($day);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
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
     * @return FormInterface The form
     */
    private function createDeleteForm(Days $day): FormInterface
    {
        return $this->createFormBuilder()
          ->setAction(
            $this->generateUrl('days_delete', ['id' => $day->getId()])
          )
          ->setMethod('DELETE')
          ->getForm();
    }

}
