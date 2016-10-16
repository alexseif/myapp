<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Goals;
use AppBundle\Form\GoalsType;

/**
 * Goals controller.
 *
 * @Route("/goals")
 */
class GoalsController extends Controller
{
    /**
     * Lists all Goals entities.
     *
     * @Route("/", name="goals_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $goals = $em->getRepository('AppBundle:Goals')->findAll();

        return $this->render('goals/index.html.twig', array(
            'goals' => $goals,
        ));
    }

    /**
     * Creates a new Goals entity.
     *
     * @Route("/new", name="goals_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $goal = new Goals();
        $form = $this->createForm('AppBundle\Form\GoalsType', $goal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($goal);
            $em->flush();

            return $this->redirectToRoute('goals_show', array('id' => $goal->getId()));
        }

        return $this->render('goals/new.html.twig', array(
            'goal' => $goal,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Goals entity.
     *
     * @Route("/{id}", name="goals_show")
     * @Method("GET")
     */
    public function showAction(Goals $goal)
    {
        $deleteForm = $this->createDeleteForm($goal);

        return $this->render('goals/show.html.twig', array(
            'goal' => $goal,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Goals entity.
     *
     * @Route("/{id}/edit", name="goals_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Goals $goal)
    {
        $deleteForm = $this->createDeleteForm($goal);
        $editForm = $this->createForm('AppBundle\Form\GoalsType', $goal);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($goal);
            $em->flush();

            return $this->redirectToRoute('goals_edit', array('id' => $goal->getId()));
        }

        return $this->render('goals/edit.html.twig', array(
            'goal' => $goal,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Goals entity.
     *
     * @Route("/{id}", name="goals_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Goals $goal)
    {
        $form = $this->createDeleteForm($goal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($goal);
            $em->flush();
        }

        return $this->redirectToRoute('goals_index');
    }

    /**
     * Creates a form to delete a Goals entity.
     *
     * @param Goals $goal The Goals entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Goals $goal)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('goals_delete', array('id' => $goal->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
