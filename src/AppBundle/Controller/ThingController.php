<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Thing;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Thing controller.
 *
 * @Route("thing")
 */
class ThingController extends Controller
{

    /**
     * Lists all thing entities.
     *
     * @Route("/", name="thing_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $things = $em->getRepository('AppBundle:Thing')->findAll();

        return $this->render('AppBundle:thing:index.html.twig', array(
            'things' => $things,
        ));
    }

    /**
     * Creates a new thing entity.
     *
     * @Route("/new", name="thing_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $thing = new Thing();
        $form = $this->createForm('AppBundle\Form\ThingType', $thing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($thing);
            $em->flush();

            return $this->redirectToRoute('thing_show', array('id' => $thing->getId()));
        }

        return $this->render('AppBundle:thing:new.html.twig', array(
            'thing' => $thing,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a thing entity.
     *
     * @Route("/{id}", name="thing_show", methods={"GET"})
     */
    public function showAction(Thing $thing)
    {
        $deleteForm = $this->createDeleteForm($thing);

        return $this->render('AppBundle:thing:show.html.twig', array(
            'thing' => $thing,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing thing entity.
     *
     * @Route("/{id}/edit", name="thing_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Thing $thing)
    {
        $deleteForm = $this->createDeleteForm($thing);
        $editForm = $this->createForm('AppBundle\Form\ThingType', $thing);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('thing_edit', array('id' => $thing->getId()));
        }

        return $this->render('AppBundle:thing:edit.html.twig', array(
            'thing' => $thing,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a thing entity.
     *
     * @Route("/{id}", name="thing_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Thing $thing)
    {
        $form = $this->createDeleteForm($thing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($thing);
            $em->flush();
        }

        return $this->redirectToRoute('thing_index');
    }

    /**
     * Creates a form to delete a thing entity.
     *
     * @param Thing $thing The thing entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Thing $thing)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('thing_delete', array('id' => $thing->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}
