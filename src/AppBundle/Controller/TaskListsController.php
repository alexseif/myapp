<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use AppBundle\Form\TaskListsType;
use AppBundle\Repository\TaskListsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tasklists")
 */
class TaskListsController extends Controller
{
    /**
     * @Route("/", name="tasklists_index", methods={"GET"})
     */
    public function index(TaskListsRepository $taskListsRepository, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $taskTemplate = new Tasks();
        $form = $this->createForm('AppBundle\Form\TasksMassEditType', $taskTemplate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tasks = $em->getRepository('AppBundle:Tasks')->findBy(array("taskList" => $taskTemplate->getTaskList()));
            foreach ($tasks as $task) {
                $task->setPriority($taskTemplate->getPriority());
                $task->setUrgency($taskTemplate->getUrgency());
            }
            $em->flush();

            return $this->redirectToRoute('tasklists_index');
        }
        return $this->render('tasklists/index.html.twig', [
            'tasklists' => $taskListsRepository->findAll(),
            'tasksMassEdit_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="tasklists_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $taskList = new TaskLists();
        $form = $this->createForm(TaskListsType::class, $taskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($taskList);
            $entityManager->flush();

            return $this->redirectToRoute('tasklists_index');
        }

        return $this->render('tasklists/new.html.twig', [
            'tasklist' => $taskList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tasklists_show", methods={"GET"})
     */
    public function show(TaskLists $taskList): Response
    {
        return $this->render('tasklists/show.html.twig', [
            'tasklist' => $taskList,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tasklists_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TaskLists $taskList): Response
    {
        $form = $this->createForm(TaskListsType::class, $taskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tasklists_index');
        }

        return $this->render('tasklists/edit.html.twig', [
            'tasklist' => $taskList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing TaskLists entity.
     *
     * @Route("/{id}/archive", name="tasklists_archive", methods={"GET", "POST"})
     * @param TaskLists $taskList
     * @return RedirectResponse
     */
    public function archiveAction(TaskLists $taskList)
    {
        $taskList->setStatus("archive");

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
     * @param TaskLists $taskList
     * @return RedirectResponse
     */
    public function unarchiveAction(TaskLists $taskList)
    {
        $taskList->setStatus("start");

        $em = $this->getDoctrine()->getManager();
        $em->persist($taskList);
        $em->flush();

        $this->addFlash('success', 'TaskList ' . $taskList->getName() . ' UnArchived');

        return $this->redirectToRoute('tasklists_index');
    }


    /**
     * @Route("/{id}", name="tasklists_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TaskLists $taskList): Response
    {
        if ($this->isCsrfTokenValid('delete' . $taskList->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($taskList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tasklists_index');
    }
}
