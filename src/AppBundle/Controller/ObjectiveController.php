<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Objective;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Objective controller.
 *
 * @Route("objective")
 */
class ObjectiveController extends Controller
{

    /**
     * Lists all objective entities.
     *
     * @Route("/", name="objective_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $objectives = $em->getRepository('AppBundle:Objective')->findAll();

        return $this->render('AppBundle:objective:index.html.twig', array(
            'objectives' => $objectives,
        ));
    }

    /**
     * Creates a new objective entity.
     *
     * @Route("/new", name="objective_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $objective = new Objective();
        $form = $this->createForm('AppBundle\Form\ObjectiveType', $objective);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($objective);
            $em->flush();

            return $this->redirectToRoute('objective_show', array('id' => $objective->getId()));
        }

        return $this->render('AppBundle:objective:new.html.twig', array(
            'objective' => $objective,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a objective entity.
     *
     * @Route("/{id}", name="objective_show", methods={"GET"})
     */
    public function showAction(Objective $objective)
    {
        $deleteForm = $this->createDeleteForm($objective);

        return $this->render('AppBundle:objective:show.html.twig', array(
            'objective' => $objective,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing objective entity.
     *
     * @Route("/{id}/edit", name="objective_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Objective $objective)
    {
        $deleteForm = $this->createDeleteForm($objective);
        $editForm = $this->createForm('AppBundle\Form\ObjectiveType', $objective);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('objective_edit', array('id' => $objective->getId()));
        }

        return $this->render('AppBundle:objective:edit.html.twig', array(
            'objective' => $objective,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a objective entity.
     *
     * @Route("/{id}", name="objective_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Objective $objective)
    {
        $form = $this->createDeleteForm($objective);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($objective);
            $em->flush();
        }

        return $this->redirectToRoute('objective_index');
    }

    /**
     * Creates a form to delete a objective entity.
     *
     * @param Objective $objective The objective entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Objective $objective)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('objective_delete', array('id' => $objective->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}
