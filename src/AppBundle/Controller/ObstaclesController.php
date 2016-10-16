<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Obstacles;
use AppBundle\Form\ObstaclesType;

/**
 * Obstacles controller.
 *
 * @Route("/obstacles")
 */
class ObstaclesController extends Controller
{
    /**
     * Lists all Obstacles entities.
     *
     * @Route("/", name="obstacles_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $obstacles = $em->getRepository('AppBundle:Obstacles')->findAll();

        return $this->render('obstacles/index.html.twig', array(
            'obstacles' => $obstacles,
        ));
    }

    /**
     * Creates a new Obstacles entity.
     *
     * @Route("/new", name="obstacles_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $obstacle = new Obstacles();
        $form = $this->createForm('AppBundle\Form\ObstaclesType', $obstacle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($obstacle);
            $em->flush();

            return $this->redirectToRoute('obstacles_show', array('id' => $obstacle->getId()));
        }

        return $this->render('obstacles/new.html.twig', array(
            'obstacle' => $obstacle,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Obstacles entity.
     *
     * @Route("/{id}", name="obstacles_show")
     * @Method("GET")
     */
    public function showAction(Obstacles $obstacle)
    {
        $deleteForm = $this->createDeleteForm($obstacle);

        return $this->render('obstacles/show.html.twig', array(
            'obstacle' => $obstacle,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Obstacles entity.
     *
     * @Route("/{id}/edit", name="obstacles_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Obstacles $obstacle)
    {
        $deleteForm = $this->createDeleteForm($obstacle);
        $editForm = $this->createForm('AppBundle\Form\ObstaclesType', $obstacle);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($obstacle);
            $em->flush();

            return $this->redirectToRoute('obstacles_edit', array('id' => $obstacle->getId()));
        }

        return $this->render('obstacles/edit.html.twig', array(
            'obstacle' => $obstacle,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Obstacles entity.
     *
     * @Route("/{id}", name="obstacles_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Obstacles $obstacle)
    {
        $form = $this->createDeleteForm($obstacle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($obstacle);
            $em->flush();
        }

        return $this->redirectToRoute('obstacles_index');
    }

    /**
     * Creates a form to delete a Obstacles entity.
     *
     * @param Obstacles $obstacle The Obstacles entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Obstacles $obstacle)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('obstacles_delete', array('id' => $obstacle->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
