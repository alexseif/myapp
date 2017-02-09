<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Cost controller.
 *
 * @Route("cost")
 */
class CostController extends Controller
{
    /**
     * Lists all cost entities.
     *
     * @Route("/", name="cost_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $costs = $em->getRepository('AppBundle:Cost')->findAll();

        return $this->render('cost/index.html.twig', array(
            'costs' => $costs,
        ));
    }

    /**
     * Creates a new cost entity.
     *
     * @Route("/new", name="cost_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cost = new Cost();
        $form = $this->createForm('AppBundle\Form\CostType', $cost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cost);
            $em->flush($cost);

            return $this->redirectToRoute('cost_show', array('id' => $cost->getId()));
        }

        return $this->render('cost/new.html.twig', array(
            'cost' => $cost,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cost entity.
     *
     * @Route("/{id}", name="cost_show")
     * @Method("GET")
     */
    public function showAction(Cost $cost)
    {
        $deleteForm = $this->createDeleteForm($cost);

        return $this->render('cost/show.html.twig', array(
            'cost' => $cost,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cost entity.
     *
     * @Route("/{id}/edit", name="cost_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cost $cost)
    {
        $deleteForm = $this->createDeleteForm($cost);
        $editForm = $this->createForm('AppBundle\Form\CostType', $cost);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cost_edit', array('id' => $cost->getId()));
        }

        return $this->render('cost/edit.html.twig', array(
            'cost' => $cost,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a cost entity.
     *
     * @Route("/{id}", name="cost_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Cost $cost)
    {
        $form = $this->createDeleteForm($cost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cost);
            $em->flush($cost);
        }

        return $this->redirectToRoute('cost_index');
    }

    /**
     * Creates a form to delete a cost entity.
     *
     * @param Cost $cost The cost entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cost $cost)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cost_delete', array('id' => $cost->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
