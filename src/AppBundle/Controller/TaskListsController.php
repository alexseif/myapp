<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\TaskLists;
use AppBundle\Form\TaskListsType;

/**
 * TaskLists controller.
 *
 * @Route("/tasklists")
 */
class TaskListsController extends Controller
{

    /**
     * Lists all TaskLists entities.
     *
     * @Route("/", name="tasklists_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $taskLists = $em->getRepository('AppBundle:TaskLists')->findAll();

        return $this->render('tasklists/index.html.twig', array(
                    'taskLists' => $taskLists,
        ));
    }

    /**
     * Creates a new TaskLists entity.
     *
     * @Route("/new", name="tasklists_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $taskList = new TaskLists();
        $form = $this->createForm('AppBundle\Form\TaskListsType', $taskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($taskList);
            $em->flush();

            return $this->redirectToRoute('tasklists_index');
        }

        return $this->render('tasklists/new.html.twig', array(
                    'taskList' => $taskList,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TaskLists entity.
     *
     * @Route("/{id}", name="tasklists_show")
     * @Method("GET")
     */
    public function showAction(TaskLists $taskList)
    {
        $deleteForm = $this->createDeleteForm($taskList);

        return $this->render('tasklists/show.html.twig', array(
                    'taskList' => $taskList,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TaskLists entity.
     *
     * @Route("/{id}/edit", name="tasklists_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, TaskLists $taskList)
    {
        $deleteForm = $this->createDeleteForm($taskList);
        $editForm = $this->createForm('AppBundle\Form\TaskListsType', $taskList);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($taskList);
            $em->flush();

            return $this->redirectToRoute('tasklists_index');
        }

        return $this->render('tasklists/edit.html.twig', array(
                    'taskList' => $taskList,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a TaskLists entity.
     *
     * @Route("/{id}", name="tasklists_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, TaskLists $taskList)
    {
        $form = $this->createDeleteForm($taskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($taskList);
            $em->flush();
        }

        return $this->redirectToRoute('tasklists_index');
    }

    /**
     * Creates a form to delete a TaskLists entity.
     *
     * @param TaskLists $taskList The TaskLists entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TaskLists $taskList)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('tasklists_delete', array('id' => $taskList->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
