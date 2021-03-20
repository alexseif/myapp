<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use AppBundle\Form\TasklistSizingType;
use AppBundle\Form\TaskListsType;
use AppBundle\Form\TaskSizingType;
use AppBundle\Repository\AccountsRepository;
use AppBundle\Repository\TaskListsRepository;
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
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $tasklists = $this->get(TaskListsRepository::class)->findActive();
        if ($request->get('account')) {
            $account = $request->get('account');
            $tasklists = $this->get(TaskListsRepository::class)->findBy([
                'account' => $account
            ]);
        }
        return $this->render('sizing/index.html.twig', [
            'tasklists' => $tasklists,
            'query' => $request->query->all()
        ]);
    }


    /**
     * @param TaskLists $tasklist
     * @return Response|null
     * @Route ("/{id}", name="_by_tasklist")
     */
    public function sizingByTasklist(TaskLists $tasklist, Request $request): ?Response
    {
        $request->query->add(['id' => $tasklist->getId()]);
        $query = $request->query->all();
        $tasks = $tasklist->getTasks(false);
        $form = $this->createForm(TasklistSizingType::class, $tasklist, [
            'action' => $this->generateUrl('sizing_by_tasklist', $query),
            'tasks' => $tasks
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Sizing saved');
            return $this->redirect($this->generateUrl('sizing_by_tasklist', $query));
        }
        return $this->render('sizing/tasklist.html.twig', [
            'tasklist' => $tasklist,
            'form' => $form->createView(),
            'query' => $query
        ]);
    }

    /**
     * @Route("/{id}/new_task", name="_new_task", methods={"GET","POST"})
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function newTask(TaskLists $tasklist, Request $request)
    {
        $request->query->add(['id' => $tasklist->getId()]);
        $query = $request->query->all();

        $task = new Tasks();
        $task->setTaskList($tasklist);

        $form = $this->createForm(TaskSizingType::class, $task, [
            'action' => $this->generateUrl('sizing_new_task', $query)
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            $this->addFlash('success', 'Task saved');
            return $this->redirect($this->generateUrl('sizing_by_tasklist', $query));
        }

        return JsonResponse::create($this->renderView('tasks/_new_sizing.html.twig', [
            'form' => $form->createView(),
            'query' => $query
        ]));
    }


    /**
     * @Route("/new/tasklist", name="_new_tasklist", methods={"GET","POST"})
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function newTasklist(Request $request)
    {
        $query = $request->query->all();

        $tasklist = new TaskLists();
        if (array_key_exists('account', $query)) {
            $tasklist->setAccount($this->get(AccountsRepository::class)->find($query['account']));
        }
        $form = $this->createForm(TaskListsType::class, $tasklist, [
            'action' => $this->generateUrl('sizing_new_tasklist', $query)
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tasklist);
            $entityManager->flush();
            $this->addFlash('success', 'New Task List saved');
            return $this->redirect($this->generateUrl('sizing_by_tasklist', array_merge(['id' => $tasklist->getId()], $query)));
        }

        return JsonResponse::create($this->renderView('@App/tasklists/_new_modal.html.twig', [
            'form' => $form->createView(),
            'query' => $query
        ]));
    }
}
