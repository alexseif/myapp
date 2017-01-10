<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Goal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Goal controller.
 *
 * @Route("goal")
 */
class GoalController extends Controller
{
    /**
     * Lists all goal entities.
     *
     * @Route("/", name="goal_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $goals = $em->getRepository('AppBundle:Goal')->findAll();

        return $this->render('goal/index.html.twig', array(
            'goals' => $goals,
        ));
    }

    /**
     * Creates a new goal entity.
     *
     * @Route("/new", name="goal_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $goal = new Goal();
        $form = $this->createForm('AppBundle\Form\GoalType', $goal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($goal);
            $em->flush($goal);

            return $this->redirectToRoute('goal_show', array('id' => $goal->getId()));
        }

        return $this->render('goal/new.html.twig', array(
            'goal' => $goal,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a goal entity.
     *
     * @Route("/{id}", name="goal_show")
     * @Method("GET")
     */
    public function showAction(Goal $goal)
    {
        $deleteForm = $this->createDeleteForm($goal);

        return $this->render('goal/show.html.twig', array(
            'goal' => $goal,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing goal entity.
     *
     * @Route("/{id}/edit", name="goal_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Goal $goal)
    {
        $deleteForm = $this->createDeleteForm($goal);
        $editForm = $this->createForm('AppBundle\Form\GoalType', $goal);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('goal_edit', array('id' => $goal->getId()));
        }

        return $this->render('goal/edit.html.twig', array(
            'goal' => $goal,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a goal entity.
     *
     * @Route("/{id}", name="goal_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Goal $goal)
    {
        $form = $this->createDeleteForm($goal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($goal);
            $em->flush($goal);
        }

        return $this->redirectToRoute('goal_index');
    }

    /**
     * Creates a form to delete a goal entity.
     *
     * @param Goal $goal The goal entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Goal $goal)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('goal_delete', array('id' => $goal->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
