<?php

namespace App\Controller;

use App\Entity\DashboardTaskLists;
use App\Entity\TaskLists;
use App\Form\DashboardTaskListsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Dashboardtasklist controller.
 *
 * @Route("dashboardtasklists")
 */
class DashboardTaskListsController extends AbstractController
{
    /**
     * Lists all dashboardTaskList entities.
     *
     * @Route("/", name="dashboardtasklists_index", methods={"GET"})
     */
    public function indexAction(EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {
        $em = $entityManager;

        $dashboardTaskLists = $em->getRepository(DashboardTaskLists::class)->findAllTaskLists();

        return $this->render('Settings/Dashboard/Tasklists/index.html.twig', [
            'dashboardTaskLists' => $dashboardTaskLists,
        ]);
    }

    /**
     * Creates a new dashboardTaskList entity.
     *
     * @Route("/new", name="dashboardtasklists_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, EntityManagerInterface $entityManager)
    {
        $dashboardTaskList = new DashboardTaskLists();
        $form = $this->createForm(DashboardTaskListsType::class, $dashboardTaskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->persist($dashboardTaskList);
            $em->flush();

            return $this->redirectToRoute('dashboardtasklists_index');
        }

        return $this->render('Settings/Dashboard/Tasklists/new.html.twig', [
            'dashboardTaskList' => $dashboardTaskList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing dashboardTaskList entity.
     *
     * @Route("/{id}/edit", name="dashboardtasklists_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, DashboardTaskLists $dashboardTaskList, EntityManagerInterface $entityManager)
    {
        $deleteForm = $this->createDeleteForm($dashboardTaskList);
        $editForm = $this->createForm(DashboardTaskListsType::class, $dashboardTaskList);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('dashboardtasklists_edit', ['id' => $dashboardTaskList->getId()]);
        }

        return $this->render('Settings/Dashboard/Tasklists/edit.html.twig', [
            'dashboardTaskList' => $dashboardTaskList,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Creates a new dashboardTaskList entity.
     *
     * @Route("/add", name="dashboardtasklists_add", methods={"POST"})
     */
    public function addAction(Request $request, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {
        $em = $entityManager;

        $data = $request->get('data');
        $taskList = $em->getRepository(TaskLists::class)->find($data['id']);
        if ($taskList) {
            $dashboardTaskList = new DashboardTaskLists();
            $dashboardTaskList->setTaskList($taskList);
            $em->persist($dashboardTaskList);
            $em->flush();
        } else {
            throw new NotFoundHttpException();
        }

        return new Response();
    }

    /**
     * Creates a new dashboardTaskList entity.
     *
     * @Route("/remove", name="dashboardtasklists_remove", methods={"POST"})
     */
    public function removeAction(Request $request, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {
        $em = $entityManager;

        $data = $request->get('data');
        $dashboardTaskList = $em->getRepository(DashboardTaskLists::class)->findOneBy(['taskList' => $data['id']]);
        if ($dashboardTaskList) {
            $em->remove($dashboardTaskList);
            $em->flush();
        } else {
            throw new NotFoundHttpException();
        }

        return new Response();
    }

    /**
     * Deletes a dashboardTaskList entity.
     *
     * @Route("/{id}", name="dashboardtasklists_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, DashboardTaskLists $dashboardTaskList, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $form = $this->createDeleteForm($dashboardTaskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->remove($dashboardTaskList);
            $em->flush();
        }

        return $this->redirectToRoute('dashboardtasklists_index');
    }

    /**
     * Creates a form to delete a dashboardTaskList entity.
     *
     * @param DashboardTaskLists $dashboardTaskList The dashboardTaskList entity
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(DashboardTaskLists $dashboardTaskList): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dashboardtasklists_delete', ['id' => $dashboardTaskList->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
