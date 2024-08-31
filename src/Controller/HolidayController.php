<?php

namespace App\Controller;

use App\Entity\Holiday;
use App\Form\HolidayType;
use App\Service\WorkingDays;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Holiday controller.
 *
 * @Route("/holiday")
 */
class HolidayController extends AbstractController
{

    /**
     * Lists all Holiday entities.
     *
     * @Route("/", name="holiday_index", methods={"GET"})
     */
    public function indexAction(EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {
        $em = $entityManager;

        $holidays = $em->getRepository(Holiday::class)->findAll();

        return $this->render('holiday/index.html.twig', [
          'holidays' => $holidays,
        ]);
    }

    /**
     * Creates a new Holiday entity.
     *
     * @Route("/fetch", name="holiday_fetch", methods={"GET", "POST"})
     */
    public function testAction(WorkingDays $workingDays): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $workingDays->updateHolidays();
        $this->addFlash('Success', 'Holidays Updated');

        return $this->redirect($this->generateUrl('holiday_index'));
    }

    /**
     * Creates a new Holiday entity.
     *
     * @Route("/new", name="holiday_new", methods={"GET", "POST"})
     */
    public function newAction(
      Request $request,
      EntityManagerInterface $entityManager
    ) {
        $note = new Holiday();
        $form = $this->createForm(HolidayType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute(
              'holiday_show',
              ['id' => $note->getId()]
            );
        }

        return $this->render('holiday/new.html.twig', [
          'note' => $note,
          'holiday_form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Holiday entity.
     *
     * @Route("/{id}", name="holiday_show", methods={"GET"})
     */
    public function showAction(Holiday $holiday): \Symfony\Component\HttpFoundation\Response
    {
        $deleteForm = $this->createDeleteForm($holiday);

        return $this->render('holiday/show.html.twig', [
          'holiday' => $holiday,
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Holiday entity.
     *
     * @Route("/{id}/edit", name="holiday_edit", methods={"GET", "POST"})
     */
    public function editAction(
      Request $request,
      Holiday $holiday,
      EntityManagerInterface $entityManager
    ) {
        $deleteForm = $this->createDeleteForm($holiday);
        $editForm = $this->createForm(HolidayType::class, $holiday);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $entityManager;
            $em->persist($holiday);
            $em->flush();

            return $this->redirectToRoute(
              'holiday_show',
              ['id' => $holiday->getId()]
            );
        }

        return $this->render('holiday/edit.html.twig', [
          'holiday' => $holiday,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Holiday entity.
     *
     * @Route("/{id}", name="holiday_delete", methods={"DELETE"})
     */
    public function deleteAction(
      Request $request,
      Holiday $note,
      EntityManagerInterface $entityManager
    ): \Symfony\Component\HttpFoundation\RedirectResponse {
        $form = $this->createDeleteForm($note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->remove($note);
            $em->flush();
        }

        return $this->redirectToRoute('holiday_index');
    }

    /**
     * Creates a form to delete a Holiday entity.
     *
     * @param Holiday $note The Holiday entity
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Holiday $note)
    {
        return $this->createFormBuilder()
          ->setAction(
            $this->generateUrl('holiday_delete', ['id' => $note->getId()])
          )
          ->setMethod('DELETE')
          ->getForm();
    }

}
