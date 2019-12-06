<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Focus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Focus controller.
 *
 * @Route("focus-crud")
 */
class FocusController extends Controller
{
    /**
     * Lists all focus entities.
     *
     * @Route("/", name="focus_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $foci = $em->getRepository('AppBundle:Focus')->findAll();

        return $this->render('AppBundle:focus:index.html.twig', array(
            'foci' => $foci,
        ));
    }

    /**
     * Creates a new focus entity.
     *
     * @Route("/new", name="focus_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $focus = new Focus();
        $form = $this->createForm('AppBundle\Form\FocusType', $focus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($focus);
            $em->flush();

            return $this->redirectToRoute('focus_show', array('id' => $focus->getId()));
        }

        return $this->render('AppBundle:focus:new.html.twig', array(
            'focus' => $focus,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a focus entity.
     *
     * @Route("/{id}", name="focus_show")
     * @Method("GET")
     */
    public function showAction(Focus $focus)
    {
        $deleteForm = $this->createDeleteForm($focus);

        return $this->render('AppBundle:focus:show.html.twig', array(
            'focus' => $focus,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing focus entity.
     *
     * @Route("/{id}/edit", name="focus_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Focus $focus)
    {
        $deleteForm = $this->createDeleteForm($focus);
        $editForm = $this->createForm('AppBundle\Form\FocusType', $focus);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('focus_edit', array('id' => $focus->getId()));
        }

        return $this->render('AppBundle:focus:edit.html.twig', array(
            'focus' => $focus,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a focus entity.
     *
     * @Route("/{id}", name="focus_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Focus $focus)
    {
        $form = $this->createDeleteForm($focus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($focus);
            $em->flush();
        }

        return $this->redirectToRoute('focus_index');
    }

    /**
     * Creates a form to delete a focus entity.
     *
     * @param Focus $focus The focus entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Focus $focus)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('focus_delete', array('id' => $focus->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
