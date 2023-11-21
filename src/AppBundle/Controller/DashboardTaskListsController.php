<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DashboardTaskLists;
use AppBundle\Entity\TaskLists;
use AppBundle\Form\DashboardTaskListsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Dashboardtasklist controller.
 *
 * @Route("dashboardtasklists")
 */
class DashboardTaskListsController extends Controller
{
    /**
     * Lists all dashboardTaskList entities.
     *
     * @Route("/", name="dashboardtasklists_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dashboardTaskLists = $em->getRepository(DashboardTaskLists::class)->findAllTaskLists();

        return $this->render('@App/Settings/Dashboard/Tasklists/index.html.twig', [
            'dashboardTaskLists' => $dashboardTaskLists,
        ]);
    }

    /**
     * Creates a new dashboardTaskList entity.
     *
     * @Route("/new", name="dashboardtasklists_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $dashboardTaskList = new DashboardTaskLists();
        $form = $this->createForm(DashboardTaskListsType::class, $dashboardTaskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dashboardTaskList);
            $em->flush();

            return $this->redirectToRoute('dashboardtasklists_index');
        }

        return $this->render('@App/Settings/Dashboard/Tasklists/new.html.twig', [
            'dashboardTaskList' => $dashboardTaskList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing dashboardTaskList entity.
     *
     * @Route("/{id}/edit", name="dashboardtasklists_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, DashboardTaskLists $dashboardTaskList)
    {
        $deleteForm = $this->createDeleteForm($dashboardTaskList);
        $editForm = $this->createForm(DashboardTaskListsType::class, $dashboardTaskList);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dashboardtasklists_edit', ['id' => $dashboardTaskList->getId()]);
        }

        return $this->render('@App/Settings/Dashboard/Tasklists/edit.html.twig', [
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
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = $request->get('data');
        $taskList = $em->getRepository(TaskLists::class)->find($data['id']);
        if ($taskList) {
            $dashboardTaskList = new DashboardTaskLists();
            $dashboardTaskList->setTaskList($taskList);
            $em->persist($dashboardTaskList);
            $em->flush();
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        return new \Symfony\Component\HttpFoundation\Response();
    }

    /**
     * Creates a new dashboardTaskList entity.
     *
     * @Route("/remove", name="dashboardtasklists_remove", methods={"POST"})
     */
    public function removeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = $request->get('data');
        $dashboardTaskList = $em->getRepository(DashboardTaskLists::class)->findOneBy(['taskList' => $data['id']]);
        if ($dashboardTaskList) {
            $em->remove($dashboardTaskList);
            $em->flush();
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        return new \Symfony\Component\HttpFoundation\Response();
    }

    /**
     * Deletes a dashboardTaskList entity.
     *
     * @Route("/{id}", name="dashboardtasklists_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, DashboardTaskLists $dashboardTaskList)
    {
        $form = $this->createDeleteForm($dashboardTaskList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
    private function createDeleteForm(DashboardTaskLists $dashboardTaskList)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dashboardtasklists_delete', ['id' => $dashboardTaskList->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
