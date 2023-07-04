<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use AppBundle\Form\TaskListsType;
use AppBundle\Form\TasksMassEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TaskLists controller.
 *
 * @Route("/tasklists")
 */
class TaskListsController extends AbstractController
{
    /**
     * Lists all TaskLists entities.
     *
     * @Route("/", name="tasklists_index", methods={"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $taskLists = $em->getRepository(TaskLists::class)->findAll();

        $taskTemplate = new Tasks();
        $form = $this->createForm(TasksMassEditType::class, $taskTemplate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tasks = $em->getRepository(Tasks::class)->findBy(['taskList' => $taskTemplate->getTaskList()]);
            foreach ($tasks as $task) {
                $task->setPriority($taskTemplate->getPriority());
                $task->setUrgency($taskTemplate->getUrgency());
            }
            $em->flush();

            return $this->redirectToRoute('tasklists_index');
        }

        return $this->render('AppBundle:tasklists:index.html.twig', [
            'taskLists' => $taskLists,
            'tasksMassEdit_form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new TaskLists entity.
     *
     * @Route("/new", name="tasklists_new", methods={"GET","POST"})
     */
    public function newAction(Request $request)
    {
        $taskList = new TaskLists();
        $form = $this->createForm(TaskListsType::class, $taskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($taskList);
            $em->flush();

            return $this->redirectToRoute('tasklists_index');
        }

        return $this->render('AppBundle:tasklists:new.html.twig', [
            'taskList' => $taskList,
            'tasklist_form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a TaskLists entity.
     *
     * @Route("/{id}", name="tasklists_show", methods={"GET"})
     */
    public function showAction(TaskLists $taskList)
    {
        $deleteForm = $this->createDeleteForm($taskList);

        return $this->render('AppBundle:tasklists:show.html.twig', [
            'taskList' => $taskList,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing TaskLists entity.
     *
     * @Route("/{id}/edit", name="tasklists_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, TaskLists $taskList)
    {
        $deleteForm = $this->createDeleteForm($taskList);
        $editForm = $this->createForm(TaskListsType::class, $taskList);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($taskList);
            $em->flush();

            return $this->redirectToRoute('tasklists_index');
        }

        return $this->render('AppBundle:tasklists:edit.html.twig', [
            'taskList' => $taskList,
            'tasklist_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing TaskLists entity.
     *
     * @Route("/{id}/archive", name="tasklists_archive", methods={"GET", "POST"})
     */
    public function archiveAction(Request $request, TaskLists $taskList)
    {
        $taskList->setStatus('archive');

        $em = $this->getDoctrine()->getManager();
        $em->persist($taskList);
        $em->flush();

        $this->addFlash('success', 'TaskList ' . $taskList->getName() . ' Archived');

        return $this->redirectToRoute('tasklists_index');
    }

    /**
     * Displays a form to edit an existing TaskLists entity.
     *
     * @Route("/{id}/unarchive", name="tasklists_unarchive", methods={"GET", "POST"})
     */
    public function unarchiveAction(Request $request, TaskLists $taskList)
    {
        $taskList->setStatus('start');

        $em = $this->getDoctrine()->getManager();
        $em->persist($taskList);
        $em->flush();

        $this->addFlash('success', 'TaskList ' . $taskList->getName() . ' UnArchived');

        return $this->redirectToRoute('tasklists_index');
    }

    /**
     * Deletes a TaskLists entity.
     *
     * @Route("/{id}", name="tasklists_delete", methods={"DELETE"})
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
     * @return \Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(TaskLists $taskList)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tasklists_delete', ['id' => $taskList->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
