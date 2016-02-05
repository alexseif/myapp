<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        //TODO: sort by date
        $em = $this->getDoctrine()->getManager();

        $days = $em->getRepository('AppBundle:Days')->findAll();

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
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Days entity.
     *
     * @Route("/{id}", name="days_show")
     * @Method("GET")
     */
    public function showAction(Days $day)
    {
        $deleteForm = $this->createDeleteForm($day);

        return $this->render('days/show.html.twig', array(
            'day' => $day,
            'delete_form' => $deleteForm->createView(),
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

            return $this->redirectToRoute('days_edit', array('id' => $day->getId()));
        }

        return $this->render('days/edit.html.twig', array(
            'day' => $day,
            'edit_form' => $editForm->createView(),
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
