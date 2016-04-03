<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Tasks;
use AppBundle\Form\TasksType;

/**
 * Tasks controller.
 *
 * @Route("/tasks")
 */
class TasksController extends Controller
{

    /**
     * Lists all Tasks entities.
     *
     * @Route("/", name="tasks_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository('AppBundle:Tasks')->findAll();

        return $this->render('tasks/index.html.twig', array(
                    'tasks' => $tasks,
        ));
    }

    /**
     * Creates a new Tasks entity.
     *
     * @Route("/new", name="tasks_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $task = new Tasks();
        if (!empty($request->get("tasklist"))) {
            $taskList = $em->getRepository('AppBundle:TaskLists')->find($request->get("tasklist"));
            $task->setTaskList($taskList);
        }
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

//            return $this->redirectToRoute('tasks_show', array('id' => $task ->getId()));
            return $this->redirectToRoute('tasks_index');
        }

        return $this->render('tasks/new.html.twig', array(
                    'task' => $task,
                    'task_form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tasks entity.
     *
     * @Route("/{id}/edit", name="tasks_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Tasks $task)
    {
        $deleteForm = $this->createDeleteForm($task);
        $editForm = $this->createForm(TasksType::class, $task);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('tasks_edit', array('id' => $task->getId()));
        }

        return $this->render('tasks/edit.html.twig', array(
                    'task' => $task,
                    'task_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Tasks entity.
     *
     * @Route("/{id}", name="tasks_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Tasks $task)
    {
        $form = $this->createDeleteForm($task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();
        }

        return $this->redirectToRoute('tasks_index');
    }

    /**
     * Creates a form to delete a Tasks entity.
     *
     * @param Tasks $task The Tasks entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Tasks $task)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('tasks_delete', array('id' => $task->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
