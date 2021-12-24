<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use AppBundle\Service\FocusService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FocusController extends Controller
{
    /**
     * @Route("/focus", name="focus")
     */
    public function focusAction(Request $request, FocusService $focusService)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->query->has('client')) {
            $client = $em->getRepository('AppBundle:Client')->find($request->get('client'));
            if (empty($client)) {
                throw new NotFoundHttpException('Client not found');
            }
            $focusService->setTasks($client);
        } else {
            $focusService->setTasks();
        }

        return $this->render('AppBundle:focus:index.html.twig', $focusService->get());
    }

    /**
     * @Route("/focus/{name}", name="focus_tasklist")
     */
    public function focusByTaskListAction(TaskLists $tasklist, FocusService $focusService): ?Response
    {
        $focusService->setTasksByTaskList($tasklist);

        return $this->render('AppBundle:focus:index.html.twig', $focusService->get());
    }

    /**
     * @Route("/singleTask", name="singleTask")
     */
    public function singleTaskAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tasksRepo = $em->getRepository(Tasks::class);
        $weightedList = $tasksRepo->weightedList();
        $taskListsOrder = [];
        foreach ($weightedList as $row) {
            if (!in_array($row['id'], $taskListsOrder)) {
                $taskListsOrder[] = $row['id'];
            }
        }
        $tasks = [];
        foreach ($taskListsOrder as $taskListId) {
            $reorderTasks = $tasksRepo->findBy([
                'taskList' => $taskListId,
                'completed' => false,
            ], [
                'urgency' => 'DESC',
                'priority' => 'DESC',
                'order' => 'ASC',
            ], 10
            );
            $tasks = array_merge($tasks, $reorderTasks);
        }

        return $this->render('AppBundle:focus:singleTask.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
