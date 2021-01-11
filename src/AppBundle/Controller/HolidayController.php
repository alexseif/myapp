<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\HolidayType;
use AppBundle\Entity\Holiday;

/**
 * Holiday controller.
 *
 * @Route("/holiday")
 */
class HolidayController extends Controller
{

    /**
     * Lists all Holiday entities.
     *
     * @Route("/", name="holiday_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $holidays = $em->getRepository('AppBundle:Holiday')->findAll();
        return $this->render("AppBundle:holiday:index.html.twig", array(
            'holidays' => $holidays
        ));
    }

    /**
     * Creates a new Holiday entity.
     *
     * @Route("/fetch", name="holiday_fetch", methods={"GET", "POST"})
     */
    public function testAction()
    {
        $this->get('myapp.workingdays')->updateHolidays();
        $this->addFlash('Success', 'Holidays Updated');
        return $this->redirect($this->generateUrl('holiday_index'));
    }

    /**
     * Creates a new Holiday entity.
     *
     * @Route("/new", name="holiday_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $note = new Holiday();
        $form = $this->createForm('AppBundle\Form\HolidayType', $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('holiday_show', array('id' => $note->getId()));
        }

        return $this->render("AppBundle:holiday:new.html.twig", array(
            'note' => $note,
            'holiday_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Holiday entity.
     *
     * @Route("/{id}", name="holiday_show", methods={"GET"})
     */
    public function showAction(\AppBundle\Entity\Holiday $holiday)
    {
        $deleteForm = $this->createDeleteForm($holiday);

        return $this->render("AppBundle:holiday:show.html.twig", array(
            'holiday' => $holiday,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Holiday entity.
     *
     * @Route("/{id}/edit", name="holiday_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Holiday $holiday)
    {
        $deleteForm = $this->createDeleteForm($holiday);
        $editForm = $this->createForm('AppBundle\Form\HolidayType', $holiday);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($holiday);
            $em->flush();

            return $this->redirectToRoute('holiday_show', array('id' => $holiday->getId()));
        }

        return $this->render("AppBundle:holiday:edit.html.twig", array(
            'holiday' => $holiday,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Holiday entity.
     *
     * @Route("/{id}", name="holiday_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Holiday $note)
    {
        $form = $this->createDeleteForm($note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Holiday $note)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('holiday_delete', array('id' => $note->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}
