<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CostOfLife;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Costoflife controller.
 *
 * @Route("costoflife")
 */
class CostOfLifeController extends Controller
{

    /**
     * Lists all costOfLife entities.
     *
     * @Route("/", name="costoflife_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $costOfLives = $em->getRepository('AppBundle:CostOfLife')->findAll();

        return $this->render("AppBundle:costoflife:index.html.twig", array(
            'costOfLives' => $costOfLives,
        ));
    }

    /**
     * Creates a new costOfLife entity.
     *
     * @Route("/new", name="costoflife_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $costOfLife = new Costoflife();
        $form = $this->createForm('AppBundle\Form\CostOfLifeType', $costOfLife);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($costOfLife);
            $em->flush($costOfLife);

            return $this->redirectToRoute('costoflife_show', array('id' => $costOfLife->getId()));
        }

        return $this->render("AppBundle:costoflife:new.html.twig", array(
            'costOfLife' => $costOfLife,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a costOfLife entity.
     *
     * @Route("/{id}", name="costoflife_show", methods={"GET"})
     */
    public function showAction(CostOfLife $costOfLife)
    {
        $deleteForm = $this->createDeleteForm($costOfLife);

        return $this->render("AppBundle:costoflife:show.html.twig", array(
            'costOfLife' => $costOfLife,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing costOfLife entity.
     *
     * @Route("/{id}/edit", name="costoflife_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, CostOfLife $costOfLife)
    {
        $deleteForm = $this->createDeleteForm($costOfLife);
        $editForm = $this->createForm('AppBundle\Form\CostOfLifeType', $costOfLife);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('costoflife_edit', array('id' => $costOfLife->getId()));
        }

        return $this->render("AppBundle:costoflife:edit.html.twig", array(
            'costOfLife' => $costOfLife,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a costOfLife entity.
     *
     * @Route("/{id}", name="costoflife_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, CostOfLife $costOfLife)
    {
        $form = $this->createDeleteForm($costOfLife);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($costOfLife);
            $em->flush($costOfLife);
        }

        return $this->redirectToRoute('costoflife_index');
    }

    /**
     * Creates a form to delete a costOfLife entity.
     *
     * @param CostOfLife $costOfLife The costOfLife entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CostOfLife $costOfLife)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('costoflife_delete', array('id' => $costOfLife->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}
