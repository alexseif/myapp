<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Days;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/", name="days_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $days = $em->getRepository('AppBundle:Days')->getActiveCards();

        return $this->render('AppBundle:days:index.html.twig', [
            'days' => $days,
        ]);
    }

    /**
     * Lists all Archived Days entities.
     *
     * @Route("/archive", name="days_archive", methods={"GET"})
     */
    public function archiveAction()
    {
        $em = $this->getDoctrine()->getManager();

        $days = $em->getRepository('AppBundle:Days')->getArchiveCards();

        return $this->render('AppBundle:days:index.html.twig', [
            'days' => $days,
        ]);
    }

    /**
     * Creates a new Days entity.
     *
     * @Route("/new", name="days_new", methods={"GET", "POST"})
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

        return $this->render('AppBundle:days:new.html.twig', [
            'day' => $day,
            'day_form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Days entity.
     *
     * @Route("/{id}/edit", name="days_edit", methods={"GET", "POST"})
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

        return $this->render('AppBundle:days:edit.html.twig', [
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
     * @return FormInterface The form
     */
    private function createDeleteForm(Days $day)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('days_delete', ['id' => $day->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
