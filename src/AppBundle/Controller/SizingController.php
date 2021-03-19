<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use AppBundle\Form\TasklistSizingType;
use AppBundle\Form\TaskSizingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sizing", name="sizing")
 */
class SizingController extends Controller
{
    /**
     * @Route("/", name="_index")
     * @return Response
     */
    public function index(): Response
    {
        $tasklists = $this->getDoctrine()->getRepository(TaskLists::class)->findActive();
        return $this->render('sizing/index.html.twig', [
            'tasklists' => $tasklists
        ]);
    }

    /**
     * @param TaskLists $tasklist
     * @return Response|null
     * @Route ("/{id}/", name="_tasklist")
     */
    public function sizingTasklist(TaskLists $tasklist): ?Response
    {
        $form = $this->createForm(TasklistSizingType::class, $tasklist, [
            'action' => $this->generateUrl('sizing_new_task', ['id' => $tasklist->getId()]),
        ]);
        return $this->render('sizing/tasklist.html.twig', [
            'tasklist' => $tasklist,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new/ajax/{id}", name="_new_task", methods={"GET","POST"})
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function newAjax(TaskLists $tasklist, Request $request)
    {

        $task = new Tasks();
        $task->setTaskList($tasklist);

        $form = $this->createForm(TaskSizingType::class, $task, [
            'action' => $this->generateUrl('sizing_new_task', ['id' => $tasklist->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('sizing_tasklist', ['id' => $tasklist->getId()]));
        }

        return JsonResponse::create($this->renderView('tasks/_new_sizing.html.twig', ['form' => $form->createView()]));
    }
}
